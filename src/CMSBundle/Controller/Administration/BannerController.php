<?php

namespace App\CMSBundle\Controller\Administration;

use App\CMSBundle\Entity\Banner;
use App\CMSBundle\Form\BannerType;
use App\CMSBundle\Repository\BannerRepository;
use App\MediaBundle\Model\Image as ImageAlias;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/banner")
 */
class BannerController extends AbstractController
{
    /**
     * @Route("/", name="banner_index", methods={"GET"})
     */
    public function index(Request $request, BannerRepository $bannerRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getBanners($request, $bannerRepository);

        return $this->render('cms/admin/banner/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="banner_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $banner = new Banner();
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($banner);
            $em->flush();

            if ($form->get("image")->getData()) {
                $sizesArrayKey = Banner::$placementDimensions[$banner->getPlacement()];
                $width = $sizesArrayKey["width"];
                $height = $sizesArrayKey["height"];
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Banner::class,
                    $banner,
                    UploadFileService::ACTION_ADD,
                    "image",
                    ImageAlias::IMAGE_TYPE_MAIN,
                    $width,
                    $height
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("banner_edit", ["id" => $banner->getId()]);
                    }
                }
            }

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("banner_index");
        }

        return $this->render('cms/admin/banner/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="banner_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Banner $banner, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($banner);
            $em->flush();

            if ($form->get("image")->getData()) {
                $sizesArrayKey = Banner::$placementDimensions[$banner->getPlacement()];
                $width = $sizesArrayKey["width"];
                $height = $sizesArrayKey["height"];
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Banner::class,
                    $banner,
                    UploadFileService::ACTION_EDIT,
                    "image",
                    ImageAlias::IMAGE_TYPE_MAIN,
                    $width,
                    $height
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("banner_edit", ["id" => $banner->getId()]);
                    }
                }
            }

            $this->addFlash("success", "Banner updated successfully");

            return $this->redirectToRoute("banner_index");
        }

        return $this->render('cms/admin/banner/edit.html.twig', [
            'form' => $form->createView(),
            'banner' => $banner,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="banner_delete", methods={"GET", "POST"})
     */
    public function delete(Banner $banner, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        if ($banner->getImage()) {
            $uploadFileService->removeImage($banner->getImage());
        }
        $banner->setDeleted(new \DateTime());
        $banner->setDeletedBy($this->getUser()->getFullName());
        $em->persist($banner);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("banner_index");
    }

    private function getBanners(Request $request, BannerRepository $bannerRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $bannerRepository->filter($search, false, true, 10, $request);
    }
}