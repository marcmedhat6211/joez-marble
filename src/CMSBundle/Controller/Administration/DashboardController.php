<?php

namespace App\CMSBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('cms/admin/dashboard/index.html.twig');
    }
}