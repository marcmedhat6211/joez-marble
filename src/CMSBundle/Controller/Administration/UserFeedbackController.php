<?php

namespace App\CMSBundle\Controller\Administration;

use App\CMSBundle\Entity\FAQCategory;
use App\CMSBundle\Form\FAQCategoryType;
use App\CMSBundle\Repository\FAQCategoryRepository;
use App\CMSBundle\Repository\FAQRepository;
use App\CMSBundle\Repository\UserFeedbackRepository;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user-feedback")
 */
class UserFeedbackController extends AbstractController
{
    /**
     * @Route("/", name="user_feedback_index", methods={"GET"})
     */
    public function index(Request $request, UserFeedbackRepository $userFeedbackRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getUsersFeedbacks($request, $userFeedbackRepository);

        return $this->render('cms/admin/userFeedback/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    private function getUsersFeedbacks(Request $request, UserFeedbackRepository $userFeedbackRepository)
    {
        $search = new \stdClass();
        $search->ordr = ["dir" => "DESC", "column" => 1];

        return $userFeedbackRepository->filter($search, false, true, 10, $request);
    }
}