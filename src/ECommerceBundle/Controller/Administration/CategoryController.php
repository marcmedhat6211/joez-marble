<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\ECommerceBundle\Entity\Category;
use App\ECommerceBundle\Form\CategoryType;
use App\ECommerceBundle\Repository\CategoryRepository;
use App\ECommerceBundle\Repository\SubcategoryRepository;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getCategories($request, $categoryRepository);

        return $this->render('ecommerce/admin/category/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="category_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("category_index");
        }

        return $this->render('ecommerce/admin/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash("success", "Category updated successfully");

            return $this->redirectToRoute("category_index");
        }

        return $this->render('ecommerce/admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="category_delete", methods={"GET", "POST"})
     */
    public function delete(Category $category, EntityManagerInterface $em, SubcategoryRepository $subcategoryRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        $relatedSubcategoriesCount = $this->getRelatedSubcategoriesCount($subcategoryRepository, $category);
        if ($relatedSubcategoriesCount > 0) {
            $this->addFlash("error", "You can't delete this category because it has $relatedSubcategoriesCount subcategories that are related to it");
            return $this->redirectToRoute("category_index");
        }
        $category->setDeleted(new \DateTime());
        $category->setDeletedBy($this->getUser()->getFullName());
        $em->persist($category);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("category_index");
    }

    //============================================================PRIVATE METHODS=================================================================

    private function getCategories(Request $request, CategoryRepository $categoryRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $categoryRepository->filter($search, false, true, 10, $request);
    }

    private function getRelatedSubcategoriesCount(SubcategoryRepository $subcategoryRepository, Category $category): int
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->category = $category->getId();

        return $subcategoryRepository->filter($search, true);
    }
}