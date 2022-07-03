<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Repository\CartItemRepository;
use App\ECommerceBundle\Repository\CartRepository;
use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Services\CartService;
use App\ECommerceBundle\Services\CurrencyService;
use App\SeoBundle\Repository\SeoRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("", name="fe_cart", methods={"GET"})
     */
    public function index(
        Request            $request,
        CartRepository     $cartRepository,
        CartService        $cartService,
        PaginatorInterface $paginator
    ): Response
    {
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user" => $user]);
        $cartItemsObjs = [];
        $cartTotal = 0;
        $taxes = 0;
        $shipping = 0;
        if ($cart) {
            $cartItemsObjs = $cart->getCartItems();
            $cartTotal = $cartService->getCartTotal($cart);
            //@todo: add right taxes and shipping fees
            $taxes = 140;
            $shipping = 30;
        }

        $cartItems = $paginator->paginate($cartItemsObjs, $request->query->getInt('page', 1), 5);

        return $this->render('ecommerce/frontEnd/cart/index.html.twig', [
            "cart" => $cart,
            "cartItems" => $cartItems,
            "cartTotal" => $cartTotal,
            "taxes" => $taxes,
            "shipping" => $shipping
        ]);
    }

    /**
     * @Route("/add-item-ajax/{slug}", name="fe_add_item_to_cart_ajax", methods={"GET", "POST"})
     */
    public function add(
        TranslatorInterface $translator,
        SeoRepository       $seoRepository,
        ProductRepository   $productRepository,
        CartService         $cartService,
        CurrencyService     $currencyService,
                            $slug = null
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("login_before_adding_to_cart_msg")
            ]);
        }

        $product = null;
        if ($slug) {
            $seo = $seoRepository->findOneBy(["slug" => $slug]);
            if ($seo) {
                $product = $productRepository->findOneBy(["seo" => $seo]);
            }
        }

        if (!$slug || !$seo || !$product) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("product_not_available_anymore_msg")
            ]);
        }

        $cartItem = $cartService->addItem($product, $user);
        $cart = $cartItem->getCart();
        $cartItemObj = $cartService->getCartItemInArray($cartItem);
        $cartGrandTotal = $cartService->getCartTotal($cart);

        return $this->json([
            "error" => false,
            "message" => $translator->trans("item_added_to_cart_success_msg"),
            "totalCartQuantity" => $cartItem->getCart()->getTotalQuantity(),
            "cartGrandTotal" => $currencyService->getPriceWithCurrentCurrency($cartGrandTotal),
            "cartTotalPrice" => $currencyService->getPriceWithCurrentCurrency($cart->getTotalPrice()),
            "cartItem" => $cartItemObj,
        ]);
    }

    /**
     * @Route("/remove-whole-item-ajax/{id}", name="fe_remove_whole_item_from_cart_ajax", methods={"GET", "POST"})
     */
    public function removeWholeItem(
        CartItemRepository  $cartItemRepository,
        TranslatorInterface $translator,
        CartService         $cartService,
        CurrencyService     $currencyService,
                            $id = null
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("login_before_removing_from_cart_msg")
            ]);
        }

        if (!$id) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("item_not_available_anymore")
            ]);
        }

        $cartItem = $cartItemRepository->findOneBy(["id" => $id]);
        $cart = $cartItem->getCart();
        $cartItemId = $cartItem->getId();
        $cartService->removeTheWholeItem($cartItem);

        $newCart = $user->getCart();
        if ($newCart) {
            $cartGrandTotal = $cartService->getCartTotal($cart);
            $newCartTotalQuantity = $cart->getTotalQuantity();
        } else {
            $cartGrandTotal = 0;
            $newCartTotalQuantity = 0;
        }

        return $this->json([
            "error" => false,
            "message" => $translator->trans("item_removed_from_cart_successfully"),
            "newCartTotalQuantity" => $newCartTotalQuantity,
            "cartGrandTotal" => $currencyService->getPriceWithCurrentCurrency($cartGrandTotal),
            "cartTotal" => $currencyService->getPriceWithCurrentCurrency($cart->getTotalPrice()),
            "cartItemId" => $cartItemId,
        ]);
    }

    /**
     * @Route("/remove-one-item-ajax/{id}", name="fe_remove_one_item_from_cart_ajax", methods={"GET", "POST"})
     */
    public function removeOneItem(
        CartItemRepository  $cartItemRepository,
        TranslatorInterface $translator,
        CartService         $cartService,
        CurrencyService     $currencyService,
                            $id = null
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("login_before_removing_from_cart_msg")
            ]);
        }

        if (!$id) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("item_not_available_anymore")
            ]);
        }

        $cartItem = $cartItemRepository->findOneBy(["id" => $id]);
        $cart = $cartItem->getCart();
        $cartService->removeOneItemFromCart($cartItem);
        $cartGrandTotal = $cartService->getCartTotal($cart);

        return $this->json([
            "error" => false,
            "message" => $translator->trans("item_removed_from_cart_successfully"),
            "cartTotalQty" => $cart->getTotalQuantity(),
            "cartTotalPrice" => $currencyService->getPriceWithCurrentCurrency($cart->getTotalPrice()),
            "itemQty" => $cartItem->getQuantity(),
            "itemId" => $cartItem->getId(),
            "cartGrandTotal" => $currencyService->getPriceWithCurrentCurrency($cartGrandTotal),
        ]);
    }
}