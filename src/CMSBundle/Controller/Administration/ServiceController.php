<?php

namespace App\CMSBundle\Controller\Administration;

use App\CMSBundle\Entity\Service;
use App\CMSBundle\Form\ServiceType;
use App\CMSBundle\Repository\ServiceRepository;
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
 * @Route("/service")
 */
class ServiceController extends AbstractController
{
    /**
     * @Route("/", name="service_index", methods={"GET"})
     */
    public function index(Request $request, ServiceRepository $serviceRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getServices($request, $serviceRepository);

        return $this->render('cms/admin/service/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="service_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($service);
            $em->flush();

            if ($form->get("image")->getData()) {
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Service::class,
                    $service,
                    UploadFileService::ACTION_ADD,
                    "image",
                    BaseImage::IMAGE_TYPE_MAIN,
                    165,
                    165
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("service_edit", ["id" => $service->getId()]);
                    }
                }
            }

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("service_index");
        }

        return $this->render('cms/admin/service/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="service_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Service $service, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($service);
            $em->flush();

            if ($form->get("image")->getData()) {
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Service::class,
                    $service,
                    UploadFileService::ACTION_EDIT,
                    "image",
                    BaseImage::IMAGE_TYPE_MAIN,
                    165,
                    165
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("service_edit", ["id" => $service->getId()]);
                    }
                }
            }

            $this->addFlash("success", "Service updated successfully");

            return $this->redirectToRoute("service_index");
        }

        return $this->render('cms/admin/service/edit.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="service_delete", methods={"GET", "POST"})
     */
    public function delete(Service $service, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $em->remove($service);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("service_index");
    }

    private function getServices(Request $request, ServiceRepository $serviceRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $serviceRepository->filter($search, false, true, 10, $request);
    }
}