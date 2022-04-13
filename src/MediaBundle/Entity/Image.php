<?php

namespace App\MediaBundle\Entity;

use App\CMSBundle\Entity\Banner;
use App\CMSBundle\Entity\Testimonial;
use App\ECommerceBundle\Entity\Currency;
use App\ECommerceBundle\Entity\Material;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Entity\ProductMaterialImage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\MediaBundle\Model\Image as BaseImage;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="App\MediaBundle\Repository\ImageRepository")
 */
class Image extends BaseImage
{
    /**
     * @ORM\OneToOne(targetEntity="App\CMSBundle\Entity\Banner", mappedBy="image")
     */
    private ?Banner $banner;

    /**
     * @ORM\OneToOne(targetEntity="App\CMSBundle\Entity\Testimonial", mappedBy="image")
     */
    private ?Testimonial $testimonial;

    /**
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\Currency", mappedBy="flag")
     */
    private ?Currency $currency;

    /**
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\Material", mappedBy="mainImage")
     */
    private ?Material $material;

    /**
     * @ORM\OneToMany(targetEntity="App\ECommerceBundle\Entity\ProductMaterialImage", mappedBy="image")
     */
    private mixed $productMaterialImages;

    /**
     * @ORM\ManyToMany(targetEntity="App\ECommerceBundle\Entity\Product", mappedBy="galleryImages")
     */
    private mixed $products;

    #[Pure] public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productMaterialImages = new ArrayCollection();
    }

    public function getBanner(): ?Banner
    {
        return $this->banner;
    }

    public function setBanner(?Banner $banner): self
    {
        // unset the owning side of the relation if necessary
        if ($banner === null && $this->banner !== null) {
            $this->banner->setImage(null);
        }

        // set the owning side of the relation if necessary
        if ($banner !== null && $banner->getImage() !== $this) {
            $banner->setImage($this);
        }

        $this->banner = $banner;

        return $this;
    }

    public function getTestimonial(): ?Testimonial
    {
        return $this->testimonial;
    }

    public function setTestimonial(?Testimonial $testimonial): self
    {
        // unset the owning side of the relation if necessary
        if ($testimonial === null && $this->testimonial !== null) {
            $this->testimonial->setImage(null);
        }

        // set the owning side of the relation if necessary
        if ($testimonial !== null && $testimonial->getImage() !== $this) {
            $testimonial->setImage($this);
        }

        $this->testimonial = $testimonial;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        // unset the owning side of the relation if necessary
        if ($currency === null && $this->currency !== null) {
            $this->currency->setFlag(null);
        }

        // set the owning side of the relation if necessary
        if ($currency !== null && $currency->getFlag() !== $this) {
            $currency->setFlag($this);
        }

        $this->currency = $currency;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        // unset the owning side of the relation if necessary
        if ($material === null && $this->material !== null) {
            $this->material->setMainImage(null);
        }

        // set the owning side of the relation if necessary
        if ($material !== null && $material->getMainImage() !== $this) {
            $material->setMainImage($this);
        }

        $this->material = $material;

        return $this;
    }

    /**
     * @return Collection<int, ProductMaterialImage>
     */
    public function getProductMaterialImages(): Collection
    {
        return $this->productMaterialImages;
    }

    public function addProductMaterialImage(ProductMaterialImage $productMaterialImage): self
    {
        if (!$this->productMaterialImages->contains($productMaterialImage)) {
            $this->productMaterialImages[] = $productMaterialImage;
            $productMaterialImage->setImage($this);
        }

        return $this;
    }

    public function removeProductMaterialImage(ProductMaterialImage $productMaterialImage): self
    {
        if ($this->productMaterialImages->removeElement($productMaterialImage)) {
            // set the owning side to null (unless already changed)
            if ($productMaterialImage->getImage() === $this) {
                $productMaterialImage->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addGalleryImage($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeGalleryImage($this);
        }

        return $this;
    }
}