<?php

namespace App\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="fe_home", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('page/home/index.html.twig');
    }
}