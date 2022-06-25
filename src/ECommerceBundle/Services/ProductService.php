<?php

namespace App\ECommerceBundle\Services;

use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Repository\ProductFavouriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProductService
{
    private EntityManagerInterface $em;
    private Security $security;
    private ProductFavouriteRepository $productFavouriteRepository;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        ProductFavouriteRepository $productFavouriteRepository
    )
    {
        $this->em = $em;
        $this->security = $security;
        $this->productFavouriteRepository = $productFavouriteRepository;
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
                $productFavourite = $this->productFavouriteRepository->findOneBy(["user" => $user, "product" => $product]);
                if ($productFavourite) {
                    $product->hasFavourite = true;
                } else {
                    $product->hasFavourite = false;
                }
            }
        }
    }
}