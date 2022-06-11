<?php

namespace App\ECommerceBundle\Services;

use App\ECommerceBundle\Entity\Cart;
use App\ECommerceBundle\Entity\CartItem;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Repository\CartItemRepository;
use App\ECommerceBundle\Repository\CartRepository;
use App\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartService
{
    private EntityManagerInterface $em;
    private CartRepository $cartRepository;
    private CartItemRepository $cartItemRepository;
    private Packages $assets;
    private Request $request;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityManagerInterface $em,
        CartRepository         $cartRepository,
        CartItemRepository     $cartItemRepository,
        Packages               $assets,
        RequestStack           $requestStack,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->em = $em;
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->assets = $assets;
        $this->request = $requestStack->getCurrentRequest();
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * This method adds an item to the cart
     * @param Product $product
     * @param User $user
     * @return CartItem
     */
    public function addItem(Product $product, User $user): CartItem
    {
        $productPrice = $product->getPrice();
        $existingCartItem = $this->cartItemRepository->getCartItemByProductAndUser($product, $user);
        if ($existingCartItem) {
            $newQuantity = $existingCartItem->getQuantity() + 1;
            $newItemTotalPrice = $existingCartItem->getItemTotalPrice() + $productPrice;

            $existingCartItem->setQuantity($newQuantity);
            $existingCartItem->setItemTotalPrice($newItemTotalPrice);

            $existingCart = $existingCartItem->getCart();
            $newCartTotalPrice = $existingCart->getTotalPrice() + $productPrice;
            $newCartTotalQuantity = $existingCart->getTotalQuantity() + 1;
            $existingCart->setTotalPrice($newCartTotalPrice);
            $existingCart->setTotalQuantity($newCartTotalQuantity);

            $this->em->persist($existingCart);
            $this->em->persist($existingCartItem);
            $returnedCartItem = $existingCartItem;
        } else {
            $cart = $this->cartRepository->findOneBy(["user" => $user]);
            if (!$cart) {
                $cart = new Cart();
                $cart->setUser($user);
                $cart->setTotalPrice($productPrice);
                $cart->setTotalQuantity(1);
            } else {
                $cart->setTotalPrice($cart->getTotalPrice() + $productPrice);
                $cart->setTotalQuantity($cart->getTotalQuantity() + 1);
            }
            $this->em->persist($cart);

            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity(1);
            $cartItem->setItemTotalPrice($productPrice);
            $this->em->persist($cartItem);
            $returnedCartItem = $cartItem;
        }
        $this->em->flush();

        return $returnedCartItem;
    }

    /**
     * This method removes the whole item from the cart
     * @param CartItem $cartItem
     */
    public function removeTheWholeItem(CartItem $cartItem): void
    {
        $cart = $cartItem->getCart();
        $itemQty = $cartItem->getQuantity();
        $itemTotalPrice = $cartItem->getItemTotalPrice();

        $cart->setTotalQuantity($cart->getTotalQuantity() - $itemQty);
        $cart->setTotalPrice($cart->getTotalPrice() - $itemTotalPrice);

        $this->em->remove($cartItem);
        if ($cart->getTotalQuantity() == 0) {
            $this->em->remove($cart);
        } else {
            $this->em->persist($cart);
        }

        $this->em->flush();
    }

    /**
     * This method removes only one item from the cart
     * @param CartItem $cartItem
     */
    public function removeOneItemFromCart(CartItem $cartItem)
    {
        $cart = $cartItem->getCart();
        $cartTotalPrice = $cart->getTotalPrice();
        $cartTotalQty = $cart->getTotalQuantity();
        $itemQty = $cartItem->getQuantity();
        $itemTotalPrice = $cartItem->getItemTotalPrice();
        $product = $cartItem->getProduct();
        $productPrice = $product->getPrice();

        $cart->setTotalPrice($cartTotalPrice - $productPrice);
        $cart->setTotalQuantity($cartTotalQty - 1);
        $cartItem->setQuantity($itemQty - 1);
        $cartItem->setItemTotalPrice($itemTotalPrice - $productPrice);

        if ($cartItem->getQuantity() == 0) {
            $this->em->remove($cartItem);
        } else {
            $this->em->persist($cartItem);
        }

        if ($cart->getTotalQuantity() == 0) {
            $this->em->remove($cart);
        } else {
            $this->em->persist($cart);
        }

        $this->em->flush();
    }

    /**
     * This method returns the cart item in an array shape
     * @param CartItem $cartItem
     * @return array
     */
    #[ArrayShape(["itemId" => "int|null", "itemImageUrl" => "string", "itemTitle" => "null|string", "itemQty" => "int|null", "itemPrice" => "float|null", "itemLink" => "string", "removeWholeItemUrl" => "string"])] public function getCartItemInArray(CartItem $cartItem): array
    {
        $product = $cartItem->getProduct();
        $productImageUrl = $this->request->getSchemeAndHttpHost() . "/images/placeholders/placeholder-md.jpg";
        if ($product->getMainImage()) {
            $productImageUrl = $this->request->getSchemeAndHttpHost() . $this->assets->getUrl($product->getMainImage()->getAbsolutePath());
        }
        $removeWholeItemUrl = $this->urlGenerator->generate("fe_remove_whole_item_from_cart_ajax", ["id" => $cartItem->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return [
            "itemId" => $cartItem->getId(),
            "itemImageUrl" => $productImageUrl,
            "itemTitle" => $product->getTitle(),
            "itemQty" => $cartItem->getQuantity(),
            "itemPrice" => $product->getPrice(),
            "itemLink" => "#", //@todo: add product absolute link
            "removeWholeItemUrl" => $removeWholeItemUrl,
        ];
    }

    /**
     * This method gets the cart total after adding taxes and shipping fees
     * @param Cart $cart
     * @return float
     */
    public function getCartTotal(Cart $cart): float
    {
        //@todo: add right taxes and shipping
        $taxes = 140;
        $shipping = 30;

        return $cart->getTotalPrice() + $taxes + $shipping;
    }
}