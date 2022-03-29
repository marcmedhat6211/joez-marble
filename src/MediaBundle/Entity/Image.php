<?php

namespace App\MediaBundle\Entity;

use App\CMSBundle\Entity\Banner;
use App\CMSBundle\Entity\Testimonial;
use App\ECommerceBundle\Entity\Currency;
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
}