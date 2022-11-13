<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\ECommerceBundle\Entity\Material;
use App\ECommerceBundle\Entity\ProductMaterialImage;
use App\ECommerceBundle\Form\MaterialGalleryType;
use App\ECommerceBundle\Form\MaterialType;
use App\ECommerceBundle\Repository\MaterialRepository;
use App\ECommerceBundle\Repository\ProductMaterialImageRepository;
use App\ECommerceBundle\Repository\ProductRepository;
use App\MediaBundle\Model\Image as BaseImage;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/material")
 */
class MaterialController extends AbstractController
{
    /**
     * @Route("/", name="material_index", methods={"GET"})
     */
    public function index(Request $request, MaterialRepository $materialRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getMaterials($request, $materialRepository);

        return $this->render('ecommerce/admin/material/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="material_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($material);
            $em->flush();

            if ($form->get("mainImage")->getData()) {
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Material::class,
                    $material,
                    UploadFileService::ACTION_ADD,
                    "mainImage",
                    BaseImage::IMAGE_TYPE_MAIN,
                    65,
                    65
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("material_edit", ["id" => $material->getId()]);
                    }
                }
            }

            $this->addFlash('success', 'Successfully saved');
            return $this->redirectToRoute("material_index");
        }

        return $this->render('ecommerce/admin/material/new.html.twig', [
            'form' => $form->createView(),
            'material' => new Material()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="material_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Material $material, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($material);
            $em->flush();

            if ($form->get("mainImage")->getData()) {
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Material::class,
                    $material,
                    UploadFileService::ACTION_EDIT,
                    "mainImage",
                    BaseImage::IMAGE_TYPE_MAIN,
                    65,
                    65
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                    }
                    return $this->redirectToRoute("material_edit", ["id" => $material->getId()]);
                }
            }
            $this->addFlash("success", "Material updated successfully");

            return $this->redirectToRoute("material_index");
        }

        return $this->render('ecommerce/admin/material/edit.html.twig', [
            'form' => $form->createView(),
            'material' => $material,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="material_delete", methods={"GET", "POST"})
     */
    public function delete(
        Material $material,
        EntityManagerInterface $em,
        ProductMaterialImageRepository $productMaterialImageRepository,
        UploadFileService $uploadFileService
    ): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        $materialUsedCount = $this->getMaterialsUsedCount($material, $productMaterialImageRepository);
        if ($materialUsedCount > 0) {
            $this->addFlash("error", "You can't delete this material because it is connected to products and images, delete all images that uses this material and try again!");
            return $this->redirectToRoute("material_index");
        }

        if ($material->getMainImage()) {
            $uploadFileService->removeImage($material->getMainImage());
        }

        $material->setDeleted(new \DateTime());
        $material->setDeletedBy($this->getUser()->getFullName());
        $em->persist($material);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("material_index");
    }

    //=========================================================================PRIVATE METHODS=======================================================================

    private function getMaterials(Request $request, MaterialRepository $materialRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $materialRepository->filter($search, false, true, 10, $request);
    }

    private function getMaterialsUsedCount(Material $material, ProductMaterialImageRepository $productMaterialImageRepository): int
    {
        $search = new \stdClass();
        $search->material = $material->getId();

        return $productMaterialImageRepository->filter($search, true);
    }
}