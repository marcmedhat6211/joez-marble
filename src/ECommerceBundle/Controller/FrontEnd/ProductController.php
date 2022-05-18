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
     * @Route("joez-designs/filter", name="fe_filter_designs_product", methods={"GET"})
     * @Route("joez-designs/filter/on-sale", name="fe_filter_designs_product_sale", methods={"GET"})
     * @Route("joez-designs/filter/new-arrival", name="fe_filter_designs_product_new_arrival", methods={"GET"})
     * @Route("joez-designs/filter/subcategory/{slug}", name="fe_filter_designs_subcategory", methods={"GET"})
     * @Route("joez-designs/filter/category/{slug}", name="fe_filter_designs_category", methods={"GET"})
     */
    public function filterDesigns(
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
            "fe_filter_designs_product",
            "fe_filter_designs_product_sale",
            "fe_filter_designs_product_new_arrival"
        ];
        if (in_array($currentRoute, $noCategoriesRoutes)) {
            $firstCategory = $categoryRepository->findOneBy(["deleted" => NULL, "living" => 0]);
            if ($firstCategory) {
                $firstSubcategory = $subcategoryRepository->findOneBy(["category" => $firstCategory, "deleted" => NULL]);
            }
        }

        if ($currentRoute == "fe_filter_designs_product_sale") {
            $search->onSale = 1;
        } elseif ($currentRoute == "fe_filter_designs_product_new_arrival") {
            $search->newArrival = 1;
        } elseif ($currentRoute == "fe_filter_designs_subcategory") {
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
        } elseif ($currentRoute == "fe_filter_designs_category") {
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
        return $this->render('ecommerce/frontEnd/product/joez-designs/filter.html.twig', [
            "firstCategory" => $firstCategory,
            "firstSubcategory" => $firstSubcategory,
            "filteredSubcategory" => $filteredSubcategory,
            "filteredCategory" => $filteredCategory,
            "products" => $products,
            "currentRoute" => $currentRoute
        ]);
    }

    /**
     * @Route("joez-living/filter", name="fe_filter_living_product", methods={"GET"})
     * @Route("joez-living/filter/on-sale", name="fe_filter_living_product_sale", methods={"GET"})
     * @Route("joez-living/filter/new-arrival", name="fe_filter_living_product_new_arrival", methods={"GET"})
     * @Route("joez-living/filter/subcategory/{slug}", name="fe_filter_living_subcategory", methods={"GET"})
     * @Route("joez-living/filter/category/{slug}", name="fe_filter_living_category", methods={"GET"})
     */
    public function filterLiving(
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
        $search->living = 1;

        $noCategoriesRoutes = [
            "fe_filter_living_product",
            "fe_filter_living_product_sale",
            "fe_filter_living_product_new_arrival"
        ];
        if (in_array($currentRoute, $noCategoriesRoutes)) {
            $firstCategory = $categoryRepository->findOneBy(["deleted" => NULL, "living" => 1]);
            if ($firstCategory) {
                $firstSubcategory = $subcategoryRepository->findOneBy(["category" => $firstCategory, "deleted" => NULL]);
            }
        }

        if ($currentRoute == "fe_filter_living_product_sale") {
            $search->onSale = 1;
        } elseif ($currentRoute == "fe_filter_living_product_new_arrival") {
            $search->newArrival = 1;
        } elseif ($currentRoute == "fe_filter_living_subcategory") {
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
        } elseif ($currentRoute == "fe_filter_living_category") {
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
        return $this->render('ecommerce/frontEnd/product/joez-living/filter.html.twig', [
            "firstCategory" => $firstCategory,
            "firstSubcategory" => $firstSubcategory,
            "filteredSubcategory" => $filteredSubcategory,
            "filteredCategory" => $filteredCategory,
            "products" => $products,
            "currentRoute" => $currentRoute
        ]);
    }
}