<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/service")
 */
class ServiceController extends AbstractController
{
    /**
     * @Route("", name="fe_service_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('ecommerce/frontEnd/service/index.html.twig');
    }
}