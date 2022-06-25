<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Entity\ProductFavourite;
use App\ECommerceBundle\Repository\CartRepository;
use App\ECommerceBundle\Repository\ProductFavouriteRepository;
use App\ECommerceBundle\Services\CartService;
use App\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/wishlist")
 */
class WishlistController extends AbstractController
{
    /**
     * @Route("", name="fe_wishlist", methods={"GET"})
     */
    public function index(
        Request            $request,
        TranslatorInterface $translator,
        ProductFavouriteRepository $productFavouriteRepository
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash("error", $translator->trans("login_to_access_wishlist_msg"));
            return $this->redirectToRoute("fe_home");
        }

        $productFavourites = $this->getProductFavourites($request, $user, $productFavouriteRepository);

        return $this->render('ecommerce/frontEnd/wishlist/index.html.twig', [
            "productFavourites" => $productFavourites,
        ]);
    }

    /**
     * @Route("product/{id}/toggle-wishlist-ajax", name="fe_product_toggle_wishlist_ajax", methods={"POST"})
     */
    public function toggle(
        TranslatorInterface        $translator,
        EntityManagerInterface     $em,
        ProductFavouriteRepository $productFavouriteRepository,
        Product                    $product
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("login_to_add_to_fav_msg"),
            ]);
        }

        if (!$product) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("product_not_available_anymore_msg"),
            ]);
        }

        $existingProductFavourite = $productFavouriteRepository->findOneBy(["user" => $user, "product" => $product]);
        if ($existingProductFavourite) { // user has already this product in his wishlist
            $em->remove($existingProductFavourite);
            $em->flush();
            $productFavouritesCount = $productFavouriteRepository->getFavouriteProductsCountByUser($user);

            return $this->json([
                "error" => false,
                "message" => $translator->trans("product_removed_from_wishlist_msg"),
                "productsFavouritesCount" => $productFavouritesCount,
                "action" => "PRODUCT_REMOVED",
            ]);
        }

        $productFavourite = new ProductFavourite();
        $productFavourite->setUser($user);
        $productFavourite->setProduct($product);
        $em->persist($productFavourite);
        $em->flush();

        $productFavouritesCount = $productFavouriteRepository->getFavouriteProductsCountByUser($user);

        return $this->json([
            "error" => false,
            "message" => $translator->trans("product_added_to_wishlist_msg"),
            "productsFavouritesCount" => $productFavouritesCount,
            "action" => "PRODUCT_ADDED",
        ]);
    }

    private function getProductFavourites(Request $request, User $user, ProductFavouriteRepository $productFavouriteRepository)
    {
        $search = new \stdClass();
        $search->user = $user->getId();

        return $productFavouriteRepository->filter($search, false, true, 10, $request);
    }
}