<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\ECommerceBundle\Entity\ProductSpec;
use App\ECommerceBundle\Form\ProductSpecType;
use App\ECommerceBundle\Repository\ProductSpecRepository;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product-spec")
 */
class ProductSpecController extends AbstractController
{
    /**
     * @Route("/", name="product_spec_index", methods={"GET"})
     */
    public function index(Request $request, ProductSpecRepository $productSpecRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getCategories($request, $productSpecRepository);

        return $this->render('ecommerce/admin/productSpec/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="product_spec_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $productSpec = new ProductSpec();
        $form = $this->createForm(ProductSpecType::class, $productSpec);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($productSpec);
            $em->flush();
            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("product_spec_index");
        }

        return $this->render('ecommerce/admin/productSpec/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_spec_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ProductSpec $productSpec, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(ProductSpecType::class, $productSpec);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($productSpec);
            $em->flush();
            $this->addFlash("success", "Product Spec updated successfully");

            return $this->redirectToRoute("product_spec_index");
        }

        return $this->render('ecommerce/admin/productSpec/edit.html.twig', [
            'form' => $form->createView(),
            'productSpec' => $productSpec,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product_spec_delete", methods={"GET", "POST"})
     */
    public function delete(ProductSpec $productSpec, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        $productSpec->setDeleted(new \DateTime());
        $productSpec->setDeletedBy($this->getUser()->getFullName());
        $em->persist($productSpec);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("product_spec_index");
    }

    private function getCategories(Request $request, ProductSpecRepository $productSpecRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $productSpecRepository->filter($search, false, true, 10, $request);
    }
}