<?php

namespace App\SeoBundle\Entity;

use App\ECommerceBundle\Entity\Category;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Entity\Subcategory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\SeoBundle\Model\Seo as BaseSeo;

/**
 * @ORM\Table(name="seo")
 * @ORM\Entity(repositoryClass="App\SeoBundle\Repository\SeoRepository")
 * @UniqueEntity("slug")
 */
class Seo extends BaseSeo
{
    /**
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\Product", mappedBy="seo")
     */
    private ?Product $product;

    /**
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\Category", mappedBy="seo")
     */
    private ?Category $category;

    /**
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\Subcategory", mappedBy="seo")
     */
    private ?Subcategory $subcategory;
}