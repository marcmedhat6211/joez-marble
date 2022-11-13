<?php

namespace App\CMSBundle\Controller\Administration;

use App\CMSBundle\Entity\FAQ;
use App\CMSBundle\Form\FAQType;
use App\CMSBundle\Repository\FAQRepository;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/faq")
 */
class FAQController extends AbstractController
{
    /**
     * @Route("/", name="faq_index", methods={"GET"})
     */
    public function index(Request $request, FAQRepository $faqRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getFAQs($request, $faqRepository);

        return $this->render('cms/admin/faq/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="faq_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $faq = new FAQ();
        $form = $this->createForm(FAQType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($faq);
            $em->flush();
            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("faq_index");
        }

        return $this->render('cms/admin/faq/new.html.twig', [
            'form' => $form->createView(),
            'faq' => new FAQ()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="faq_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FAQ $faq, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(FAQType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($faq);
            $em->flush();
            $this->addFlash("success", "FAQ updated successfully");

            return $this->redirectToRoute("faq_index");
        }

        return $this->render('cms/admin/faq/edit.html.twig', [
            'form' => $form->createView(),
            'faq' => $faq,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="faq_delete", methods={"GET", "POST"})
     */
    public function delete(FAQ $faq, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $faq->setDeleted(new \DateTime());
        $faq->setDeletedBy($this->getUser()->getFullName());
        $em->persist($faq);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("faq_index");
    }

    private function getFAQs(Request $request, FAQRepository $faqRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $faqRepository->filter($search, false, true, 10, $request);
    }
}