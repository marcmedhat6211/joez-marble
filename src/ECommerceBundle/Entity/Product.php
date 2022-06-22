<?php

namespace App\ECommerceBundle\Entity;

use App\MediaBundle\Entity\Image;
use App\SeoBundle\Entity\Seo;
use App\SeoBundle\Model\SeoInterface;
use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\ProductRepository")
 */
class Product implements DateTimeInterface, SeoInterface
{
    use DateTimeTrait, VirtualDeleteTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id = 0;

    /**
     * @ORM\Column(name="title", type="string", length=50)
     */
    private ?string $title;

    /**
     * @ORM\Column(name="price", type="float")
     */
    private ?float $price;

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
     * @ORM\Column(name="best_seller", type="boolean")
     */
    private bool $bestSeller = false;

    /**
     * @ORM\Column(name="on_sale", type="boolean")
     */
    private bool $onSale = false;

    /**
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="product", cascade={"persist", "remove" })
     * @JoinColumn(name="main_image_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Image $mainImage;

    /**
     * @ORM\OneToOne(targetEntity="App\SeoBundle\Entity\Seo", inversedBy="product", cascade={"persist", "remove" })
     */
    private ? Seo $seo;

    /**
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\CartItem", mappedBy="product")
     */
    private ?CartItem $cartItem;

    /**
     * @ORM\OneToMany(targetEntity="App\ECommerceBundle\Entity\OrderItem", mappedBy="product")
     */
    private mixed $orderItems;

    /**
     * @ORM\ManyToOne(targetEntity="Subcategory", inversedBy="products", cascade={"persist"})
     */
    private ?Subcategory $subcategory;

    /**
     * @ORM\OneToMany(targetEntity="ProductSpec", mappedBy="product")
     */
    private mixed $productSpecs;

    /**
     * @ORM\OneToMany(targetEntity="App\ECommerceBundle\Entity\ProductMaterialImage", mappedBy="product")
     */
    private mixed $productMaterialImages;

    /**
     * @ORM\ManyToMany(targetEntity="App\MediaBundle\Entity\Image", inversedBy="products", cascade={"persist", "remove" })
     */
    private mixed $galleryImages;

    /**
     * @ORM\ManyToMany(targetEntity="App\ECommerceBundle\Entity\Material")
     * @ORM\JoinTable(name="product_material",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="material_id", referencedColumnName="id")}
     *      )
     */
    private mixed $materials;

    #[Pure] public function __construct()
    {
        $this->productSpecs = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->productMaterialImages = new ArrayCollection();
        $this->galleryImages = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
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

    public function getBestSeller(): ?bool
    {
        return $this->bestSeller;
    }

    public function setBestSeller(bool $bestSeller): self
    {
        $this->bestSeller = $bestSeller;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    /**
     * @return Collection<int, Material>
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(Material $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
        }

        return $this;
    }

    public function removeMaterial(Material $material): self
    {
        $this->materials->removeElement($material);

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
            $productMaterialImage->setProduct($this);
        }

        return $this;
    }

    public function removeProductMaterialImage(ProductMaterialImage $productMaterialImage): self
    {
        if ($this->productMaterialImages->removeElement($productMaterialImage)) {
            // set the owning side to null (unless already changed)
            if ($productMaterialImage->getProduct() === $this) {
                $productMaterialImage->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getGalleryImages(): Collection
    {
        return $this->galleryImages;
    }

    public function addGalleryImage(Image $galleryImage): self
    {
        if (!$this->galleryImages->contains($galleryImage)) {
            $this->galleryImages[] = $galleryImage;
        }

        return $this;
    }

    public function removeGalleryImage(Image $galleryImage): self
    {
        $this->galleryImages->removeElement($galleryImage);

        return $this;
    }

    public function getSeo(): ?Seo
    {
        return $this->seo;
    }

    public function setSeo($seo)
    {
        $this->seo = $seo;

        return $this;
    }

    public function getOnSale(): ?bool
    {
        return $this->onSale;
    }

    public function setOnSale(bool $onSale): self
    {
        $this->onSale = $onSale;

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

    public function getCartItem(): ?CartItem
    {
        return $this->cartItem;
    }

    public function setCartItem(?CartItem $cartItem): self
    {
        // unset the owning side of the relation if necessary
        if ($cartItem === null && $this->cartItem !== null) {
            $this->cartItem->setProduct(null);
        }

        // set the owning side of the relation if necessary
        if ($cartItem !== null && $cartItem->getProduct() !== $this) {
            $cartItem->setProduct($this);
        }

        $this->cartItem = $cartItem;

        return $this;
    }

    public function isPublish(): ?bool
    {
        return $this->publish;
    }

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function isNewArrival(): ?bool
    {
        return $this->newArrival;
    }

    public function isBestSeller(): ?bool
    {
        return $this->bestSeller;
    }

    public function isOnSale(): ?bool
    {
        return $this->onSale;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setProduct($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }

        return $this;
    }
}