<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Services\OrderService;
use App\UserBundle\Entity\ShippingInformation;
use App\UserBundle\Form\ShippingInformationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/create", name="fe_order_create", methods={"GET", "POST"})
     */
    public function create(
        TranslatorInterface $translator,
        OrderService $orderService,
        Request $request
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

        $shippingInformation = new ShippingInformation();
        $shippingInformationForm = $this->createForm(ShippingInformationType::class, $shippingInformation);
        $shippingInformationForm->handleRequest($request);

        $shippingFee = 30;
        $taxes = 140;
        $orderGrandTotal = $orderService->getOrderGrandTotal($userCart);

        if ($shippingInformationForm->isSubmitted() && $shippingInformationForm->isValid()) {

        }

        return $this->render('ecommerce/frontEnd/order/create.html.twig', [
            "cart" => $userCart,
            "form" => $shippingInformationForm->createView(),
            "shippingFee" => $shippingFee,
            "taxes" => $taxes,
            "orderGrandTotal" => $orderGrandTotal,
        ]);
    }
}