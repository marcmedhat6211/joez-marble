<?php

namespace App\ECommerceBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\ProductRepository")
 */
class Product implements DateTimeInterface
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
     * @ORM\Column(name="sku", type="string", length=50)
     */
    private ?string $sku;

    /**
     * @ORM\Column(name="brief", type="text")
     */
    private ?string $brief;

    /**
     * @ORM\Column(name="description", type="text")
     */
    private ?string $description;

    /**
     * @ORM\Column(name="publish", type="boolean")
     */
    private bool $publish = true;

    /**
     * @ORM\Column(name="featured", type="boolean")
     */
    private bool $featured = false;

    /**
     * @ORM\Column(name="new_arrival", type="boolean")
     */
    private bool $newArrival = false;

    /**
     * @ORM\ManyToOne(targetEntity="Subcategory", inversedBy="products", cascade={"persist", "remove"})
     */
    private ?Subcategory $subcategory;

    /**
     * @ORM\OneToMany(targetEntity="ProductSpec", mappedBy="product", orphanRemoval=true)
     */
    private mixed $productSpecs;

    #[Pure] public function __construct()
    {
        $this->productSpecs = new ArrayCollection();
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

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getBrief(): ?string
    {
        return $this->brief;
    }

    public function setBrief(string $brief): self
    {
        $this->brief = $brief;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(bool $publish): self
    {
        $this->publish = $publish;

        return $this;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getNewArrival(): ?bool
    {
        return $this->newArrival;
    }

    public function setNewArrival(bool $newArrival): self
    {
        $this->newArrival = $newArrival;

        return $this;
    }

    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    public function setSubcategory(?Subcategory $subcategory): self
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    /**
     * @return Collection<int, ProductSpec>
     */
    public function getProductSpecs(): Collection
    {
        return $this->productSpecs;
    }

    public function addProductSpec(ProductSpec $productSpec): self
    {
        if (!$this->productSpecs->contains($productSpec)) {
            $this->productSpecs[] = $productSpec;
            $productSpec->setProduct($this);
        }

        return $this;
    }

    public function removeProductSpec(ProductSpec $productSpec): self
    {
        if ($this->productSpecs->removeElement($productSpec)) {
            // set the owning side to null (unless already changed)
            if ($productSpec->getProduct() === $this) {
                $productSpec->setProduct(null);
            }
        }

        return $this;
    }
}