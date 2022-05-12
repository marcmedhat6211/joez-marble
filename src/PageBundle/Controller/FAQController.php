<?php

namespace App\PageBundle\Controller;

use App\CMSBundle\Entity\FAQCategory;
use App\CMSBundle\Repository\FAQCategoryRepository;
use App\CMSBundle\Repository\FAQRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FAQController extends AbstractController
{
    /**
     * @Route("/faqs", name="fe_faqs", methods={"GET"})
     */
    public function index(FAQCategoryRepository $FAQCategoryRepository, FAQRepository $FAQRepository): Response
    {
        $faqCategories = $this->getFaqCategories($FAQCategoryRepository);
        foreach ($faqCategories as $faqCategory) {
            $faqCategory->publishedFAQs = $this->getFaqsByCategory($FAQRepository, $faqCategory);
        }

        dump($faqCategories);

        return $this->render('page/faq/index.html.twig', [
            "faqCategories" => $faqCategories
        ]);
    }

    //====================================================================================PRIVATE METHODS============================================================================

    private function getFaqCategories(FAQCategoryRepository $FAQCategoryRepository): array
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->ordr = ["column" => 1, "dir" => "ASC"];

        return $FAQCategoryRepository->filter($search);
    }

    private function getFaqsByCategory(FAQRepository $FAQRepository, FAQCategory $faqCategory): array
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->publish = 1;
        $search->faqCategory = $faqCategory->getId();
        $search->ordr = ["column" => 1, "dir" => "ASC"];

        return $FAQRepository->filter($search);
    }
}