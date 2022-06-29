<?php

namespace App\PageBundle\Controller;

use App\CMSBundle\Entity\Banner;
use App\CMSBundle\Entity\UserFeedback;
use App\CMSBundle\Repository\BannerRepository;
use App\CMSBundle\Repository\TestimonialRepository;
use App\CMSBundle\Repository\UserFeedbackRepository;
use App\ECommerceBundle\Repository\CartRepository;
use App\ECommerceBundle\Repository\CategoryRepository;
use App\ECommerceBundle\Repository\CurrencyRepository;
use App\ECommerceBundle\Repository\ProductFavouriteRepository;
use App\ServiceBundle\Utils\Validate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="fe_home", methods={"GET"})
     */
    public function index(BannerRepository $bannerRepository, TestimonialRepository $testimonialRepository): Response
    {
        
        return $this->render('page/home/index.html.twig', [
            "mainBanners" => $this->getMainBanners($bannerRepository),
            "collectionBannerOne" => $this->getSingleBanner($bannerRepository, 2),
            "collectionBannerTwo" => $this->getSingleBanner($bannerRepository, 3),
            "collectionBannerThree" => $this->getSingleBanner($bannerRepository, 4),
            "midPageBigBanner" => $this->getSingleBanner($bannerRepository, 5),
            "midPageSmallBannerOne" => $this->getSingleBanner($bannerRepository, 6),
            "midPageSmallBannerTwo" => $this->getSingleBanner($bannerRepository, 7),
            "midPageSmallBannerThree" => $this->getSingleBanner($bannerRepository, 8),
            "midPageLeftBanner" => $this->getSingleBanner($bannerRepository, 9),
            "midPageRightSmallBannerOne" => $this->getSingleBanner($bannerRepository, 10),
            "midPageRightSmallBannerTwo" => $this->getSingleBanner($bannerRepository, 11),
            "midPageLivingBanner" => $this->getSingleBanner($bannerRepository, 12),
            "categoryBannerOne" => $this->getSingleBanner($bannerRepository, 13),
            "categoryBannerTwo" => $this->getSingleBanner($bannerRepository, 14),
            "testimonials" => $this->getTestimonials($testimonialRepository),
        ]);
    }

    public function menu(
        Request $request,
        CategoryRepository $categoryRepository,
        CartRepository $cartRepository,
        ProductFavouriteRepository $productFavouriteRepository,
    ): Response
    {
        $user = $this->getUser();
        $categories = $this->getCategories($categoryRepository);
        $cart = $cartRepository->findOneBy(["user" => $user]);
        $wishlistCount = 0;
        if ($user) {
            $wishlistCount = $productFavouriteRepository->getFavouriteProductsCountByUser($user);
        }

        return $this->render('fe/_desktop-menu.html.twig', [
            "request" => $request,
            "categories" => $categories,
            "cart" => $cart ?: null,
            "wishlistCount" => $wishlistCount,
        ]);
    }

    public function mobileMenu(Request $request, CategoryRepository $categoryRepository): Response
    {
        $categories = $this->getCategories($categoryRepository);

        return $this->render('fe/_mobile-menu.html.twig', [
            "request" => $request,
            "categories" => $categories
        ]);
    }

    public function footer(Request $request): Response
    {

        return $this->render('fe/_footer.html.twig', [
            "request" => $request,
        ]);
    }

    /**
     * @Route("/edit-profile", name="fe_edit_profile", methods={"GET", "POST"})
     */
    public function editProfile(Request $request): Response
    {
        return $this->render('fe/_edit-profile-modal.html.twig', [
            "request" => $request,
        ]);
    }

    /**
     * @Route("/shop-and-ship", name="fe_shop_and_ship", methods={"GET", "POST"})
     */
    public function shopAndShip(
        Request $request,
        CurrencyRepository $currencyRepository,
        TranslatorInterface $translator
    ): Response
    {
        $currencies = $currencyRepository->findAll();

        if ($request->getMethod() == "POST") {
            $backRoute = $request->headers->get("referer");
            $currencyId = $request->get("currency");
            if (!$currencyId || $currencyId == "" || !Validate::not_null($currencyId)) {
                $this->addFlash("error", $translator->trans("choose_currency_msg"));
                return $this->redirect($backRoute);
            }

            $currency = $currencyRepository->find($currencyId);
            $currencyInfo = [
                "code" => $currency->getCode(),
                "egpEquivalence" => $currency->getEgpEquivalence()
            ];
            setcookie("currencyInfo", $currencyInfo);

            $this->addFlash("success", $translator->trans("currency_saved_success_msg"));
            return $this->redirect($backRoute);
        }

        return $this->render('fe/_shop-&-ship-modal.html.twig', [
            "request" => $request,
            "currencies" => $currencies,
        ]);
    }

    public function privacyPolicy(Request $request): Response
    {
        return $this->render('fe/_privacy-policy-modal.html.twig', [
            "request" => $request,
        ]);
    }

    /**
     * @Route("/user-feedback-ajax", name="fe_user_feedback_ajax", methods={"POST"})
     */
    public function userFeedback(
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $em,
        UserFeedbackRepository $userFeedbackRepository
    ): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                "error" => true,
                "message" =>  $translator->trans("login_to_feedback_msg"),
            ]);
        }

        $rating = $request->request->get("rate");
        $category = $request->request->get("category");

        if (!$rating || !$category) {
            return $this->json([
                "error" => true,
                "message" =>  $translator->trans("all_fields_required_error_msg"),
            ]);
        }

        $latestUserFeedback = $userFeedbackRepository->getLatestUserFeedback($user);
        if ($latestUserFeedback) {
            $latestUserFeedbackCreatedDate = $latestUserFeedback->getCreated();
            $now = new \DateTime();
            $diffInHrs = $now->diff($latestUserFeedbackCreatedDate)->format("%h");
            if ($diffInHrs < 1) {
                return $this->json([
                    "error" => true,
                    "message" =>  $translator->trans("just_added_feedback_msg"),
                ]);
            }
        }

        $userFeedback = new UserFeedback();
        $userFeedback->setUser($user);
        $userFeedback->setRating($rating);
        $userFeedback->setCategory($category);
        $em->persist($userFeedback);
        $em->flush();

        return $this->json([
           "error" => false,
           "message" =>  $translator->trans("thanks_for_feedback_msg"),
        ]);
    }

    //====================================================================================PRIVATE METHODS============================================================================

    private function getCategories(CategoryRepository $categoryRepository): array
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $categoryRepository->filter($search, false, false, 6);
    }

    private function getMainBanners(BannerRepository $bannerRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->publish = 1;
        $search->placement = 1;

        return $bannerRepository->filter($search, false, false, 6);
    }

    private function getSingleBanner(BannerRepository $bannerRepository, int $placement): ?Banner
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->publish = 1;
        $search->placement = $placement;
        $banner = $bannerRepository->filter($search, false, false, 1);

        return count($banner) > 0 ? $banner[0] : null;
    }

    private function getTestimonials(TestimonialRepository $testimonialRepository)
    {
        $search = new \stdClass();
        $search->publish = 1;

        return $testimonialRepository->filter($search);
    }
}