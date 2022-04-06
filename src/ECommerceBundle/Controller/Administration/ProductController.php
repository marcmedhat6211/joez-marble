<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Form\ProductType;
use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Repository\ProductSpecRepository;
use App\ECommerceBundle\Services\ProductService;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getCategories($request, $productRepository);

        return $this->render('ecommerce/admin/product/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, ProductService $productService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("product_index");
        }

        return $this->render('ecommerce/admin/product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            $this->addFlash("success", "Product updated successfully");

            return $this->redirectToRoute("product_index");
        }

        return $this->render('ecommerce/admin/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product_delete", methods={"GET", "POST"})
     */
    public function delete(Product $product, EntityManagerInterface $em, ProductSpecRepository $productSpecRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        $productSpecsCount = $this->getRelatedProductSpecsCount($product, $productSpecRepository);
        if ($productSpecsCount > 0) {
            $this->addFlash("error", "You can't delete this product because it has $productSpecsCount specs related to it");
            return $this->redirectToRoute("product_index");
        }

        $product->setDeleted(new \DateTime());
        $product->setDeletedBy($this->getUser()->getFullName());
        $em->persist($product);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("product_index");
    }

    //=========================================================================PRIVATE METHODS=======================================================================

    private function getCategories(Request $request, ProductRepository $productRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $productRepository->filter($search, false, true, 10, $request);
    }

    private function getRelatedProductSpecsCount(Product $product, ProductSpecRepository $productSpecRepository): int
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->product = $product->getId();

        return $productSpecRepository->filter($search, true);
    }
}