<?php

namespace App\ECommerceBundle\Services;

use App\ECommerceBundle\Entity\Material;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Repository\ProductFavouriteRepository;
use App\ECommerceBundle\Repository\ProductMaterialImageRepository;
use App\MediaBundle\Repository\ImageRepository;
use App\MediaBundle\Services\FileService;
use App\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProductService
{
    private EntityManagerInterface $em;
    private Security $security;
    private ProductFavouriteRepository $productFavouriteRepository;
    private ProductMaterialImageRepository $productMaterialImageRepository;
    private FileService $fileService;
    private ImageRepository $imageRepository;

    public function __construct(
        EntityManagerInterface         $em,
        Security                       $security,
        ProductFavouriteRepository     $productFavouriteRepository,
        ProductMaterialImageRepository $productMaterialImageRepository,
        FileService                    $fileService,
        ImageRepository                $imageRepository
    )
    {
        $this->em = $em;
        $this->security = $security;
        $this->productFavouriteRepository = $productFavouriteRepository;
        $this->productMaterialImageRepository = $productMaterialImageRepository;
        $this->fileService = $fileService;
        $this->imageRepository = $imageRepository;
    }

    //@TODO: make sure this works well
    public function isSkuValid(string $sku, Product $product = null): bool
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->sku = $sku;
        if ($product) {
            $search->notId = $product->getId();
        }

        $productsCount = $this->em->getRepository(Product::class)->filter($search, true);
        if ($productsCount > 0) {
            return false;
        }

        return true;
    }

    public function addFavouriteStatusToProductsObjects(array $products): void
    {
        $user = $this->security->getUser();
        if ($user) {
            foreach ($products as $product) {
                $this->addFavouriteStatusToProductObject($product, $user);
            }
        }
    }

    public function addFavouriteStatusToProductObject(Product $product, User $user)
    {
        $productFavourite = $this->productFavouriteRepository->findOneBy(["user" => $user, "product" => $product]);
        if ($productFavourite) {
            $product->hasFavourite = true;
        } else {
            $product->hasFavourite = false;
        }
    }

    public function getProductImagesPathsByProductAndMaterial(Product $product, Material $material = null): array
    {
        $imagesPaths = [];
        foreach ($product->getGalleryImages() as $galleryImage) {
            $imagesPaths[] = $this->fileService->getFileFullAbsolutePath($galleryImage->getAbsolutePath());
        }

        if (!$material) {
            return $imagesPaths;
        }

        $materialImagesIds = $this->productMaterialImageRepository->getMaterialImagesByProductAndMaterial($product, $material);
        if (count($materialImagesIds) == 0) {
            return $imagesPaths;
        }

        $materialImages = $this->imageRepository->getImagesByIds($materialImagesIds);
        $imagesPaths = [];
        foreach ($materialImages as $materialImage) {
            $imagesPaths[] = $this->fileService->getFileFullAbsolutePath('uploads/' . $materialImage["path"] . "/" . $materialImage["name"]);
        }

        return $imagesPaths;
    }
}