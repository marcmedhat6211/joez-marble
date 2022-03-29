<?php

namespace App\CMSBundle\Controller\Administration;

use App\CMSBundle\Entity\Testimonial;
use App\CMSBundle\Form\TestimonialType;
use App\CMSBundle\Repository\TestimonialRepository;
use App\MediaBundle\Entity\Image;
use App\MediaBundle\Model\Image as BaseImage;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/testimonial")
 */
class TestimonialController extends AbstractController
{
    /**
     * @Route("/", name="testimonial_index", methods={"GET"})
     */
    public function index(Request $request, TestimonialRepository $testimonialRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getTestimonials($request, $testimonialRepository);

        return $this->render('cms/admin/testimonial/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="testimonial_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $testimonial = new Testimonial();
        $form = $this->createForm(TestimonialType::class, $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($testimonial);
            $em->flush();

            if ($form->get("image")->getData()) {
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Testimonial::class,
                    $testimonial,
                    UploadFileService::ACTION_ADD,
                    "image",
                    BaseImage::IMAGE_TYPE_MAIN,
                    165,
                    165
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("testimonial_edit", ["id" => $testimonial->getId()]);
                    }
                }
            }

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("testimonial_index");
        }

        return $this->render('cms/admin/testimonial/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="testimonial_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Testimonial $testimonial, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(TestimonialType::class, $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($testimonial);
            $em->flush();

            if ($form->get("image")->getData()) {
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Testimonial::class,
                    $testimonial,
                    UploadFileService::ACTION_EDIT,
                    "image",
                    BaseImage::IMAGE_TYPE_MAIN,
                    165,
                    165
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("testimonial_edit", ["id" => $testimonial->getId()]);
                    }
                }
            }

            $this->addFlash("success", "Testimonial updated successfully");

            return $this->redirectToRoute("testimonial_index");
        }

        return $this->render('cms/admin/testimonial/edit.html.twig', [
            'form' => $form->createView(),
            'testimonial' => $testimonial,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="testimonial_delete", methods={"GET", "POST"})
     */
    public function delete(Testimonial $testimonial, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $em->remove($testimonial);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("testimonial_index");
    }

    private function getTestimonials(Request $request, TestimonialRepository $testimonialRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $testimonialRepository->filter($search, false, true, 10, $request);
    }
}