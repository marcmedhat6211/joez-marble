<?php

namespace App\PageBundle\Controller;

use App\CMSBundle\Entity\Banner;
use App\CMSBundle\Entity\UserFeedback;
use App\CMSBundle\Repository\BannerRepository;
use App\CMSBundle\Repository\TestimonialRepository;
use App\CMSBundle\Repository\UserFeedbackRepository;
use App\ECommerceBundle\Entity\Coupon;
use App\ECommerceBundle\Repository\CartRepository;
use App\ECommerceBundle\Repository\CategoryRepository;
use App\ECommerceBundle\Repository\CouponRepository;
use App\ECommerceBundle\Repository\CurrencyRepository;
use App\ECommerceBundle\Repository\ProductFavouriteRepository;
use App\ECommerceBundle\Repository\ProductRepository;
use App\Kernel;
use App\MediaBundle\Services\FileService;
use App\ServiceBundle\Service\SendEmailService;
use App\ServiceBundle\Utils\Validate;
use App\UserBundle\Entity\User;
use App\UserBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
        Request                    $request,
        CategoryRepository         $categoryRepository,
        CartRepository             $cartRepository,
        ProductFavouriteRepository $productFavouriteRepository,
        CurrencyRepository $currencyRepository,
    ): Response
    {
        $user = $this->getUser();
        $categories = $this->getCategories($categoryRepository);
        $cart = $cartRepository->findOneBy(["user" => $user]);

        $wishlistCount = 0;
        if ($user) {
            $wishlistCount = $productFavouriteRepository->getFavouriteProductsCountByUser($user);
        }

        $currencyCode = "EGP";
        if (isset($_COOKIE["currencyCode"])) {
            $currencyCode = $_COOKIE["currencyCode"];
        }
        $currency = $currencyRepository->findOneBy(["code" => $currencyCode]);

        return $this->render('fe/_desktop-menu.html.twig', [
            "request" => $request,
            "categories" => $categories,
            "cart" => $cart ?: null,
            "wishlistCount" => $wishlistCount,
            "currency" => $currency,
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

    public function editProfile(): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $user = $this->getUser();

        return $this->render('fe/_edit-profile-modal.html.twig', [
            "user" => $user
        ]);
    }

    /**
     * @Route("/edit-profile-ajax", name="fe_edit_profile_ajax", methods={"GET", "POST"})
     */
    public function editProfileAjax(Request $request, TranslatorInterface $translator, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $user = $this->getUser();

        $data = $this->collectEditProfileData($request);
        $errors = $this->validateEditProfileData($data, $translator);

        if (count($errors) > 0) {
            return $this->json([
                "error" => true,
                "messages" => $errors
            ]);
        }

        $user->setFullName($data->name);
        $user->setEmail($data->email);
        if (Validate::not_null($data->phone)) {
            $user->setPhone($data->phone);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            "error" => false,
            "message" => $translator->trans("profile_data_updated_success_msg")
        ]);
    }

    /**
     * @Route("/shop-and-ship", name="fe_shop_and_ship", methods={"GET", "POST"})
     */
    public function shopAndShip(
        Request             $request,
        CurrencyRepository  $currencyRepository,
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
            setcookie("currencyCode", (string)$currency->getCode());
            setcookie("egpEquivalence", (float)$currency->getEgpEquivalence());

            $this->addFlash("success", $translator->trans("currency_changed_success_msg"));
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
        Request                $request,
        TranslatorInterface    $translator,
        EntityManagerInterface $em,
        UserFeedbackRepository $userFeedbackRepository
    ): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("login_to_feedback_msg"),
            ]);
        }

        $rating = $request->request->get("rate");
        $category = $request->request->get("category");

        if (!$rating || !$category) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("all_fields_required_error_msg"),
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
                    "message" => $translator->trans("just_added_feedback_msg"),
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
            "message" => $translator->trans("thanks_for_feedback_msg"),
        ]);
    }

    /**
     * @Route("/search-ajax", name="fe_search_ajax", methods={"GET", "POST"})
     */
    public function search(
        Request               $request,
        ProductRepository     $productRepository,
        FileService           $fileService,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse
    {
        $searchKeyword = $request->get("searchKeyword");

        $search = new \stdClass();
        $search->deleted = 0;
        $search->publish = 1;
        $search->string = $searchKeyword;
        $search->ordr = ["column" => 0, "dir" => "DESC"];

        $products = $productRepository->filter($search);
        $resultsObj = [];
        foreach ($products as $product) {
            $title = $product->getTitle();
            $imageUrl = $fileService->getFileFullAbsolutePath("images/placeholders/placeholder-md.jpg");
            if ($product->getMainImage()) {
                $imageUrl = $fileService->getFileFullAbsolutePath($product->getMainImage()->getAbsolutePath());
            }
            $productUrl = $urlGenerator->generate("fe_product_show", ["slug" => $product->getSeo()->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

            $resultsObj[] = [
                "title" => $title,
                "imageUrl" => $imageUrl,
                "productUrl" => $productUrl
            ];
        }

        return $this->json([
            "error" => false,
            "results" => $resultsObj
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

    private function collectEditProfileData(Request $request): \stdClass
    {
        $data = new \stdClass();

        $data->name = $request->get("name");
        $data->email = $request->get("email");
        $data->phone = $request->get("phone");

        return $data;
    }

    private function validateEditProfileData(\stdClass $data, TranslatorInterface $translator): array
    {
        $errors = [];

        if (!Validate::not_null($data->name)) {
            $errors[] = $translator->trans("enter_name_msg");
        } elseif (strlen($data->name) < 2) {
            $errors[] = $translator->trans("min_name_two_chars_msg");
        }

        if (!Validate::not_null($data->email)) {
            $errors[] = $translator->trans("enter_email_msg");
        } elseif (!Validate::email($data->email)) {
            $errors[] = $translator->trans("enter_valid_email_msg");
        }

        if (Validate::not_null($data->phone)) {
            if (!Validate::isPhoneNumber($data->phone)) {
                $errors[] = $translator->trans("enter_valid_phone_number_msg");
            }
        }

        return $errors;
    }
}