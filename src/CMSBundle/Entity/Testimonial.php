<?php

namespace App\CMSBundle\Entity;

use App\MediaBundle\Entity\Image;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use App\CMSBundle\Model\Testimonial as BaseTestimonial;

/**
 * @ORM\Table(name="testimonial")
 * @ORM\Entity(repositoryClass="App\CMSBundle\Repository\TestimonialRepository")
 */
class Testimonial extends BaseTestimonial
{
    /**
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected ?string $url;

    /**
     * @ORM\Column(name="sort_no", type="integer", nullable=true)
     */
    protected ?int $sortNo;

    /**
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="testimonial", cascade={"persist", "remove" })
     * @JoinColumn(name="image_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Image $image;

    public function __toString(): string
    {
        return $this->client;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSortNo(): ?int
    {
        return $this->sortNo;
    }

    public function setSortNo(?int $sortNo): self
    {
        $this->sortNo = $sortNo;

        return $this;
    }
}