<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Entity\Material;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Repository\ProductMaterialImageRepository;
use App\MediaBundle\Entity\Image;
use App\MediaBundle\Repository\ImageRepository;
use App\MediaBundle\Services\FileService;
use App\SeoBundle\Repository\SeoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/material")
 */
class MaterialController extends AbstractController
{

    /**
     * @Route("/{id}/product/{slug}/filter-ajax", name="fe_material_filter_ajax", methods={"GET"})
     */
    public function filter(
        TranslatorInterface $translator,
        SeoRepository $seoRepository,
        ProductMaterialImageRepository $productMaterialImageRepository,
        ImageRepository $imageRepository,
        FileService $fileService,
        Material            $material,
                            $slug = null,
    ): JsonResponse
    {
        if (!$material) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("material_not_available_anymore_msg"),
            ]);
        }

        if (!$slug) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("product_not_available_anymore_msg"),
            ]);
        }

        $seo = $seoRepository->findOneBy(["slug" => $slug]);
        if (!$seo) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("product_not_available_anymore_msg"),
            ]);
        }

        $product = $seo->getProduct();
        if (!$product) {
            return $this->json([
                "error" => true,
                "message" => $translator->trans("product_not_available_anymore_msg"),
            ]);
        }

        $materialImagesIds = $productMaterialImageRepository->getMaterialImagesByProductAndMaterial($product, $material);
        $materialImages = $imageRepository->getImagesByIds($materialImagesIds);

        $imagesPaths = [];
        foreach ($materialImages as $materialImage) {
            $imagesPaths[] = $fileService->getFileFullAbsolutePath('uploads/'.$materialImage["path"]."/".$materialImage["name"]);
        }

        return $this->json([
            "error" => false,
            "productTitle", $product->getTitle(),
            "images" => $imagesPaths
        ]);
    }
}