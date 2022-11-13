<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\ECommerceBundle\Entity\Category;
use App\ECommerceBundle\Entity\Subcategory;
use App\ECommerceBundle\Form\SubcategoryType;
use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Repository\SubcategoryRepository;
use App\MediaBundle\Model\Image as BaseImage;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subcategory")
 */
class SubcategoryController extends AbstractController
{
    /**
     * @Route("/", name="subcategory_index", methods={"GET"})
     */
    public function index(Request $request, SubcategoryRepository $subcategoryRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getSubcategories($request, $subcategoryRepository);

        return $this->render('ecommerce/admin/subcategory/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="subcategory_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $subcategory = new Subcategory();
        $form = $this->createForm(SubcategoryType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subcategory);
            $em->flush();

            $coverPhoto = $this->uploadImage($form, $uploadFileService, $subcategory, UploadFileService::ACTION_ADD, 'coverPhoto', 1850, 350);
            if (!$coverPhoto) {
                return $this->redirectToRoute("subcategory_edit", ["id" => $subcategory->getId()]);
            }

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("subcategory_index");
        }

        return $this->render('ecommerce/admin/subcategory/new.html.twig', [
            'form' => $form->createView(),
            'subcategory' => new Subcategory()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="subcategory_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Subcategory $subcategory, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(SubcategoryType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subcategory);
            $em->flush();

            $coverPhoto = $this->uploadImage($form, $uploadFileService, $subcategory, UploadFileService::ACTION_EDIT, 'coverPhoto', 1850, 350);
            if (!$coverPhoto) {
                return $this->redirectToRoute("subcategory_edit", ["id" => $subcategory->getId()]);
            }

            $this->addFlash("success", "Subcategory updated successfully");

            return $this->redirectToRoute("subcategory_index");
        }

        return $this->render('ecommerce/admin/subcategory/edit.html.twig', [
            'form' => $form->createView(),
            'subcategory' => $subcategory,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="subcategory_delete", methods={"GET", "POST"})
     */
    public function delete(Subcategory $subcategory, EntityManagerInterface $em, ProductRepository $productRepository): Response
    {
        $relatedProductsCount = $this->getRelatedProductsCount($productRepository, $subcategory);
        if ($relatedProductsCount > 0) {
            $this->addFlash("error", "You can't delete this subcategory because it has $relatedProductsCount products related to it");
            return $this->redirectToRoute("subcategory_index");
        }

        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $subcategory->setDeleted(new \DateTime());
        $subcategory->setDeletedBy($this->getUser()->getFullName());
        $em->persist($subcategory);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("subcategory_index");
    }

    //=================================================================PRIVATE METHODS==================================================================

    private function getSubcategories(Request $request, SubcategoryRepository $subcategoryRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $subcategoryRepository->filter($search, false, true, 10, $request);
    }

    private function getRelatedProductsCount(ProductRepository $productRepository, Subcategory $subcategory): int
    {
        $search = new \stdClass();
        $search->deleted= 0;
        $search->subcategory = $subcategory->getId();

        return $productRepository->filter($search, true);
    }

    private function uploadImage(
        FormInterface $form,
        UploadFileService $uploadFileService,
        Subcategory $category,
        string $actionType,
        string $fieldName,
        int $maxWidth,
        int $maxHeight
    ): bool
    {
        if ($form->get($fieldName)->getData()) {
            $isImageUploaded = $uploadFileService->uploadImage(
                $form,
                Category::class,
                $category,
                $actionType,
                $fieldName,
                BaseImage::IMAGE_TYPE_MAIN,
                $maxWidth,
                $maxHeight
            );
            if (!$isImageUploaded["valid"]) {
                foreach ($isImageUploaded["errors"] as $error) {
                    $this->addFlash("error", $error);
                }

                return false;
            }
        }

        return true;
    }
}