<?php

namespace App\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShippingAndPaymentController extends AbstractController
{
    /**
     * @Route("/shipping-and-payment", name="fe_shipping_payment", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('page/about/index.html.twig');
    }

    //====================================================================================PRIVATE METHODS============================================================================
}