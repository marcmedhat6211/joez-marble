<?php

namespace App\ECommerceBundle\Entity;

use App\MediaBundle\Entity\Image;
use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="material")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\MaterialRepository")
 */
class Material implements DateTimeInterface
{
    use DateTimeTrait, VirtualDeleteTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="title", type="string", length=50)
     */
    private ?string $title;

    /**
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="material", cascade={"persist", "remove" })
     * @JoinColumn(name="image_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Image $mainImage;

    /**
     * @ORM\OneToMany(targetEntity="App\ECommerceBundle\Entity\ProductMaterialImage", mappedBy="material")
     */
    private mixed $productMaterialImages;

    #[Pure] public function __construct()
    {
        $this->productMaterialImages = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    public function setMainImage(?Image $mainImage): self
    {
        $this->mainImage = $mainImage;

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
            $productMaterialImage->setMaterial($this);
        }

        return $this;
    }

    public function removeProductMaterialImage(ProductMaterialImage $productMaterialImage): self
    {
        if ($this->productMaterialImages->removeElement($productMaterialImage)) {
            // set the owning side to null (unless already changed)
            if ($productMaterialImage->getMaterial() === $this) {
                $productMaterialImage->setMaterial(null);
            }
        }

        return $this;
    }
}