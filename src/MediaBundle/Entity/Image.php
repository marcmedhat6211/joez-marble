<?php

namespace App\MediaBundle\Entity;

use App\CMSBundle\Entity\Banner;
use App\CMSBundle\Entity\Testimonial;
use App\ECommerceBundle\Entity\Currency;
use App\ECommerceBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\MediaBundle\Model\Image as BaseImage;

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
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\Product", mappedBy="mainImage")
     */
    private ?Product $product;

    /**
     * @ORM\ManyToMany(targetEntity="App\ECommerceBundle\Entity\Product", mappedBy="galleryImages")
     */
    private mixed $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        // unset the owning side of the relation if necessary
        if ($product === null && $this->product !== null) {
            $this->product->setMainImage(null);
        }

        // set the owning side of the relation if necessary
        if ($product !== null && $product->getMainImage() !== $this) {
            $product->setMainImage($this);
        }

        $this->product = $product;

        return $this;
    }
}