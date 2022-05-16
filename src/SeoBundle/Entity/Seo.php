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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        // unset the owning side of the relation if necessary
        if ($product === null && $this->product !== null) {
            $this->product->setSeo(null);
        }

        // set the owning side of the relation if necessary
        if ($product !== null && $product->getSeo() !== $this) {
            $product->setSeo($this);
        }

        $this->product = $product;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        // unset the owning side of the relation if necessary
        if ($category === null && $this->category !== null) {
            $this->category->setSeo(null);
        }

        // set the owning side of the relation if necessary
        if ($category !== null && $category->getSeo() !== $this) {
            $category->setSeo($this);
        }

        $this->category = $category;

        return $this;
    }

    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    public function setSubcategory(?Subcategory $subcategory): self
    {
        // unset the owning side of the relation if necessary
        if ($subcategory === null && $this->subcategory !== null) {
            $this->subcategory->setSeo(null);
        }

        // set the owning side of the relation if necessary
        if ($subcategory !== null && $subcategory->getSeo() !== $this) {
            $subcategory->setSeo($this);
        }

        $this->subcategory = $subcategory;

        return $this;
    }
}