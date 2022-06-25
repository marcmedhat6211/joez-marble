<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Entity\Category;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Entity\ProductFavourite;
use App\ECommerceBundle\Entity\Subcategory;
use App\ECommerceBundle\Repository\CategoryRepository;
use App\ECommerceBundle\Repository\ProductFavouriteRepository;
use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Repository\SubcategoryRepository;
use App\SeoBundle\Repository\SeoRepository;
use App\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     */
    public function filterLiving(Request $request, CategoryRepository $categoryRepository): Response
    {
        $categories = $this->getValidLivingCategories($categoryRepository, $request);
        foreach ($categories as $category) {
            $category->products = [];
            $subcategories = $category->getSubcategories();
            foreach ($subcategories as $subcategory) {
                $products = $subcategory->getProducts();
                foreach ($products as $index => $product) {
                    if ($index <= 10) {
                        $category->products[] = $product;
                    } else {
                        break;
                    }
                }
            }
        }

        return $this->render('ecommerce/frontEnd/product/joez-living/filter.html.twig', [
            "categories" => $categories,
        ]);
    }

    /**
     * @Route("joez-living/filter/{slug}", name="fe_filter_living_show", methods={"GET"})
     */
    public function showLiving(
        Request               $request,
        SeoRepository         $seoRepository,
        SubcategoryRepository $subcategoryRepository,
        ProductRepository     $productRepository,
                              $slug = null,
    ): Response
    {
        if ($slug) {
            $seo = $seoRepository->findOneBy(["slug" => $slug]);
            if ($seo) {
                $subcategory = $subcategoryRepository->findOneBy(["seo" => $seo]);
            } else {
                throw new NotFoundHttpException("This subcategory is either deleted or is not available anymore");
            }
        } else {
            throw new NotFoundHttpException("This subcategory is either deleted or is not available anymore");
        }

        $products = null;
        if ($subcategory) {
            $products = $this->getProductsBySubcategory($subcategory, $productRepository, $request);
        }

        return $this->render('ecommerce/frontEnd/product/joez-living/show.html.twig', [
            "subcategory" => $subcategory,
            "products" => $products,
        ]);
    }

    /**
     * @Route("product/{slug}/show", name="fe_product_show", methods={"GET"})
     */
    public function show(
        SeoRepository     $seoRepository,
        ProductRepository $productRepository,
                          $slug = null
    ): Response
    {
        if (!$slug) {
            throw new NotFoundHttpException("Product Not Found");
        }

        $seo = $seoRepository->findOneBy(["slug" => $slug]);
        if (!$seo) {
            throw new NotFoundHttpException("Product Not Found");
        }

        $product = $productRepository->findOneBy(["seo" => $seo]);
        if (!$product) {
            throw new NotFoundHttpException("Product Not Found");
        }

        $relatedProducts = $this->getRelatedProducts($product, $productRepository);

        return $this->render('ecommerce/frontEnd/product/show.html.twig', [
            "product" => $product,
            "pageTitle" => $seo->getTitle(),
            "relatedProducts" => $relatedProducts,
        ]);
    }

    //=============================================PRIVATE METHODS====================================================

    private function getValidLivingCategories(CategoryRepository $categoryRepository, Request $request)
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->living = 1;

        return $categoryRepository->filter($search, false, true, 2, $request);
    }

    private function getProductsBySubcategory(Subcategory $subcategory, ProductRepository $productRepository, Request $request)
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->publish = 1;
        $search->subcategory = $subcategory->getId();

        return $productRepository->filter($search, false, true, 20, $request);
    }

    private function getRelatedProducts(Product $product, ProductRepository $productRepository)
    {
        $search = new \stdClass();
        $search->publish = 1;
        $search->deleted = 0;
        $search->notId = $product->getId();

        return $productRepository->filter($search, false, false, 8);
    }
}