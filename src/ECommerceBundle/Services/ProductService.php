<?php

namespace App\ECommerceBundle\Services;

use App\ECommerceBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
}