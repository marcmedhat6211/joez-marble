<?php

namespace App\SeoBundle\Entity;

use App\ECommerceBundle\Entity\Product;
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
    protected ?Product $product;
}