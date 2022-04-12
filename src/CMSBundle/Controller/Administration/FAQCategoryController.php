<?php

namespace App\CMSBundle\Controller\Administration;

use App\CMSBundle\Entity\FAQCategory;
use App\CMSBundle\Form\FAQCategoryType;
use App\CMSBundle\Repository\FAQCategoryRepository;
use App\CMSBundle\Repository\FAQRepository;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/faq-category")
 */
class FAQCategoryController extends AbstractController
{
    /**
     * @Route("/", name="faq_category_index", methods={"GET"})
     */
    public function index(Request $request, FAQCategoryRepository $faqCategoryRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getFAQCategories($request, $faqCategoryRepository);

        return $this->render('cms/admin/faqCategory/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="faq_category_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $faqCategory = new FAQCategory();
        $form = $this->createForm(FAQCategoryType::class, $faqCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($faqCategory);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("faq_category_index");
        }

        return $this->render('cms/admin/faqCategory/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="faq_category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FAQCategory $faqCategory, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(FAQCategoryType::class, $faqCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($faqCategory);
            $em->flush();
            $this->addFlash("success", "FAQ Category updated successfully");

            return $this->redirectToRoute("faq_category_index");
        }

        return $this->render('cms/admin/faqCategory/edit.html.twig', [
            'form' => $form->createView(),
            'faqCategory' => $faqCategory,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="faq_category_delete", methods={"GET", "POST"})
     */
    public function delete(FAQCategory $faqCategory, EntityManagerInterface $em, FAQRepository $FAQRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        $relatedFaqsCount = $this->getRelatedFaqsCount($faqCategory, $FAQRepository);
        if ($relatedFaqsCount > 0) {
            $this->addFlash("error", "You can't delete this faq category because it has FAQs related to it");
            return $this->redirectToRoute("faq_category_index");
        }

        $faqCategory->setDeleted(new \DateTime());
        $faqCategory->setDeletedBy($this->getUser()->getFullName());
        $em->persist($faqCategory);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("faq_category_index");
    }

    private function getFAQCategories(Request $request, FAQCategoryRepository $faqCategoryRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $faqCategoryRepository->filter($search, false, true, 10, $request);
    }

    private function getRelatedFaqsCount(FAQCategory $faqCategory, FAQRepository $FAQRepository): int
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->faqCategory = $faqCategory->getId();

        return $FAQRepository->filter($search, true);
    }
}