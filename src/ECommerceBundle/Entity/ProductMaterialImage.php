<?php

namespace App\ECommerceBundle\Entity;

use App\MediaBundle\Entity\Image;
use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="product_material_image")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\ProductMaterialImageRepository")
 */
class ProductMaterialImage
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\ECommerceBundle\Entity\Product", inversedBy="productMaterialImages", cascade={"persist"})
     */
    private ?Product $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="productMaterialImages", cascade={"persist"})
     */
    private ?Image $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\ECommerceBundle\Entity\Material", inversedBy="productMaterialImages", cascade={"persist"})
     */
    private ?Material $material;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }
}