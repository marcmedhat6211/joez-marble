<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\ECommerceBundle\Entity\Subcategory;
use App\ECommerceBundle\Form\SubcategoryType;
use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Repository\SubcategoryRepository;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $subcategory = new Subcategory();
        $form = $this->createForm(SubcategoryType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subcategory);
            $em->flush();
            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("subcategory_index");
        }

        return $this->render('ecommerce/admin/subcategory/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="subcategory_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Subcategory $subcategory, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(SubcategoryType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subcategory);
            $em->flush();
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

        return $subcategoryRepository->filter($search, false, true, 10, $request);
    }

    private function getRelatedProductsCount(ProductRepository $productRepository, Subcategory $subcategory): int
    {
        $search = new \stdClass();
        $search->deleted= 0;
        $search->subcategory = $subcategory->getId();

        return $productRepository->filter($search, true);
    }
}