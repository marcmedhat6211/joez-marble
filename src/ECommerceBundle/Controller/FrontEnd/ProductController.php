<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Repository\CategoryRepository;
use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Repository\SubcategoryRepository;
use App\SeoBundle\Repository\SeoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/filter", name="fe_filter_product", methods={"GET"})
     * @Route("/filter/on-sale", name="fe_filter_product_sale", methods={"GET"})
     * @Route("/filter/new-arrival", name="fe_filter_product_new_arrival", methods={"GET"})
     * @Route("/filter/subcategory/{slug}", name="fe_filter_subcategory", methods={"GET"})
     * @Route("/filter/category/{slug}", name="fe_filter_category", methods={"GET"})
     */
    public function filter(
        Request               $request,
        SeoRepository         $seoRepository,
        CategoryRepository    $categoryRepository,
        SubcategoryRepository $subcategoryRepository,
        ProductRepository     $productRepository,
                              $slug = null
    ): Response
    {
        $currentRoute = $request->get("_route");
        $firstCategory = $firstSubcategory = $filteredSubcategory = $filteredCategory = null;

        $search = new \stdClass();
        $search->deleted = 0;
        $search->publish = 1;
        $search->living = 0;

        $noCategoriesRoutes = [
            "fe_filter_product",
            "fe_filter_product_sale",
            "fe_filter_product_new_arrival"
        ];
        if (in_array($currentRoute, $noCategoriesRoutes)) {
            $firstCategory = $categoryRepository->findOneBy(["deleted" => NULL]);
            if ($firstCategory) {
                $firstSubcategory = $subcategoryRepository->findOneBy(["category" => $firstCategory, "deleted" => NULL]);
            }
        }

        if ($currentRoute == "fe_filter_product_sale") {
            $search->onSale = 1;
        } elseif ($currentRoute == "fe_filter_product_new_arrival") {
            $search->newArrival = 1;
        } elseif ($currentRoute == "fe_filter_subcategory") {
            $seo = $seoRepository->findOneBy(["slug" => $slug]);
            if (!$seo) {
                throw new NotFoundHttpException("This Subcategory is not available anymore");
            }
            $subcategory = $subcategoryRepository->findOneBy(["seo" => $seo]);
            if (!$subcategory) {
                throw new NotFoundHttpException("This Subcategory is not available anymore");
            }
            $filteredSubcategory = $subcategory;
            $search->subcategory = $subcategory;
        } elseif ($currentRoute == "fe_filter_category") {
            $seo = $seoRepository->findOneBy(["slug" => $slug]);
            if (!$seo) {
                throw new NotFoundHttpException("This Category is not available anymore");
            }
            $category = $categoryRepository->findOneBy(["seo" => $seo]);
            if (!$category) {
                throw new NotFoundHttpException("This Category is not available anymore");
            }
            $filteredCategory = $category;
            $search->category = $category;
        }

        $products = $productRepository->filter($search, false, true, 16, $request);
        return $this->render('ecommerce/frontEnd/product/filter.html.twig', [
            "firstCategory" => $firstCategory,
            "firstSubcategory" => $firstSubcategory,
            "filteredSubcategory" => $filteredSubcategory,
            "filteredCategory" => $filteredCategory,
            "products" => $products,
            "currentRoute" => $currentRoute
        ]);
    }
}