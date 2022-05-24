<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Services\CartService;
use App\SeoBundle\Repository\SeoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/add-item-ajax/{slug}", name="fe_add_item_to_cart_ajax", methods={"GET", "POST"})
     */
    public function create(
        Request               $request,
        TranslatorInterface   $translator,
        SeoRepository         $seoRepository,
        ProductRepository     $productRepository,
        CartService           $cartService,
        UrlGeneratorInterface $generator,
        Packages              $assets,
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
        $cartItemObj = $cartService->getCartItemInArray($cartItem);

        return $this->json([
            "error" => false,
            "message" => $translator->trans("item_added_to_cart_success_msg"),
            "totalCartQuantity" => $cartItem->getCart()->getTotalQuantity(),
            "cartItem" => $cartItemObj,
        ]);
    }
}