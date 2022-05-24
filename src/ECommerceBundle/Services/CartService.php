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

class CartService
{
    private EntityManagerInterface $em;
    private CartRepository $cartRepository;
    private CartItemRepository $cartItemRepository;
    private Packages $assets;
    private Request $request;

    public function __construct(
        EntityManagerInterface $em,
        CartRepository         $cartRepository,
        CartItemRepository     $cartItemRepository,
        Packages               $assets,
        RequestStack           $requestStack
    )
    {
        $this->em = $em;
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->assets = $assets;
        $this->request = $requestStack->getCurrentRequest();
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
     * This method removes an item from the cart
     * @param Product $product
     * @param User $user
     */
    public function removeItem(Product $product, User $user)
    {
    }

    /**
     * This method returns the cart item in an array shape
     * @param CartItem $cartItem
     * @return array
     */
    #[ArrayShape(["itemId" => "int|null", "itemImageUrl" => "string", "itemTitle" => "null|string", "itemQty" => "int|null", "itemPrice" => "float|null"])] public function getCartItemInArray(CartItem $cartItem): array
    {
        $product = $cartItem->getProduct();
        $productImageUrl = $this->request->getSchemeAndHttpHost() . $this->assets->getUrl($product->getMainImage()->getAbsolutePath());

        //@todo: add product absolute link
        return [
            "itemId" => $cartItem->getId(),
            "itemImageUrl" => $productImageUrl,
            "itemTitle" => $product->getTitle(),
            "itemQty" => $cartItem->getQuantity(),
            "itemPrice" => $product->getPrice(),
        ];
    }
}