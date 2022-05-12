<?php

namespace App\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    /**
     * @Route("/about-us", name="fe_about", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('page/about/index.html.twig');
    }

    //====================================================================================PRIVATE METHODS============================================================================
}