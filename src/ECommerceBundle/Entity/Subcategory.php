<?php

namespace App\ECommerceBundle\Entity;

use App\SeoBundle\Entity\Seo;
use App\SeoBundle\Model\SeoInterface;
use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="subcategory")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\SubcategoryRepository")
 */
class Subcategory implements DateTimeInterface, SeoInterface
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
     * @ORM\OneToOne(targetEntity="App\SeoBundle\Entity\Seo", inversedBy="subcategory", cascade={"persist", "remove" })
     */
    private ? Seo $seo;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="subcategories", cascade={"persist"})
     */
    private ?Category $category;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="subcategory")
     */
    private mixed $products;

    #[Pure] public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
            $product->setSubcategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSubcategory() === $this) {
                $product->setSubcategory(null);
            }
        }

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
}