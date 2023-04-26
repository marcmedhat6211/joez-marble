<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Entity\Coupon;
use App\ECommerceBundle\Entity\Order;
use App\ECommerceBundle\Repository\CouponRepository;
use App\ECommerceBundle\Repository\OrderRepository;
use App\ECommerceBundle\Services\CurrencyService;
use App\ECommerceBundle\Services\OrderService;
use App\ServiceBundle\Utils\Validate;
use App\UserBundle\Entity\ShippingInformation;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\ShippingInformationType;
use App\UserBundle\Repository\ShippingInformationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("", name="fe_order_index", methods={"GET"})
     */
    public function index(
        TranslatorInterface $translator,
        OrderRepository     $orderRepository,
        Request             $request
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash("error", $translator->trans("login_to_access_orders_history_msg"));
            return $this->redirectToRoute("fe_home");
        }

        $orders = $this->getOrders($request, $user, $orderRepository);

        return $this->render('ecommerce/frontEnd/order/index.html.twig', [
            "orders" => $orders,
        ]);
    }

    /**
     * @Route("/create", name="fe_order_create", methods={"GET", "POST"})
     */
    public function create(
        TranslatorInterface           $translator,
        OrderService                  $orderService,
        ShippingInformationRepository $shippingInformationRepository,
        EntityManagerInterface        $em,
        Request                       $request
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash("error", $translator->trans("you_have_to_login_msg"));
            return $this->redirectToRoute("fe_home");
        }

        $userCart = $user->getCart();
        if (!$userCart) {
            $this->addFlash("error", $translator->trans("cart_is_empty_error_msg"));
            return $this->redirectToRoute("fe_home");
        }


        $shippingInformation = $shippingInformationRepository->findOneBy(["user" => $user]);
        if (!$shippingInformation) {
            $shippingInformation = new ShippingInformation();
            $shippingInformation->setUser($user);
        }
        $shippingInformationForm = $this->createForm(ShippingInformationType::class, $shippingInformation);
        $shippingInformationForm->handleRequest($request);

        $orderGrandTotal = $orderService->getOrderGrandTotal($userCart);

        if ($shippingInformationForm->isSubmitted() && $shippingInformationForm->isValid()) {
            $em->persist($shippingInformation);
            $em->flush();

            $orderService->createOrder($userCart);
            $this->addFlash("success", "Order Created Successfully, The administrator has been notified of your order and will contact you shortly");
            return $this->redirectToRoute("fe_order_index");
        }

        return $this->render('ecommerce/frontEnd/order/create.html.twig', [
            "cart" => $userCart,
            "form" => $shippingInformationForm->createView(),
            "shippingFee" => Order::SHIPPING_FEE,
            "taxes" => Order::TAXES,
            "orderGrandTotal" => $orderGrandTotal,
        ]);
    }

    /**
     * @Route("/add-coupon-ajax", name="fe_order_add_coupon_ajax", methods={"GET", "POST"})
     * @throws \Exception
     */
    public function addCoupon(
        Request                $request,
        TranslatorInterface    $translator,
        CouponRepository       $couponRepository,
        EntityManagerInterface $em,
        OrderService           $orderService,
        CurrencyService        $currencyService
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("login_to_continue_order_msg")
            ]);
        }

        $couponCode = $request->get("couponCode");
        if (!Validate::not_null($couponCode)) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("enter_coupon_code_msg")
            ]);
        }

        $couponObj = $couponRepository->findOneBy(["couponCode" => $couponCode]);
        if (!$couponObj) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("coupon_not_valid_msg")
            ]);
        }

        $isCouponStillActive = $couponRepository->checkIfCouponIsStillActive($couponObj);
        if (!$isCouponStillActive) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("coupon_expired_msg")
            ]);
        }

        $userCart = $user->getCart();
        $userCart->setCouponDiscount(Coupon::DISCOUNT);
        $em->persist($userCart);
        $em->flush();

        $newOrderTotal = $currencyService->getPriceWithCurrentCurrency($orderService->getOrderGrandTotal($userCart));

        return $this->json([
            "error" => false,
            "message" => $translator->trans("coupon_applied_success_msg"),
            "couponDiscount" => $currencyService->getPriceWithCurrentCurrency(Coupon::DISCOUNT),
            "newOrderTotal" => $newOrderTotal
        ]);
    }

    //=====================================================PRIVATE METHODS=======================================

    private function getOrders(Request $request, User $user, OrderRepository $orderRepository)
    {
        $search = new \stdClass();
        $search->user = $user->getId();
        $search->deleted = 0;

        return $orderRepository->filter($search, false, true, 10, $request);
    }
}