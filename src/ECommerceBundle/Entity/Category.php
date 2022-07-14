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
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\CategoryRepository")
 */
class Category implements DateTimeInterface, SeoInterface
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
     * @ORM\Column(name="living", type="boolean")
     */
    private bool $living = false;

    /**
     * @ORM\OneToOne(targetEntity="App\SeoBundle\Entity\Seo", inversedBy="category", cascade={"persist", "remove" })
     */
    private ? Seo $seo;

    /**
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="categoryHeaderImageOne", cascade={"persist", "remove"})
     * @JoinColumn(name="header_image_one_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Image $headerImageOne;

    /**
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="categoryHeaderImageTwo", cascade={"persist", "remove"})
     * @JoinColumn(name="header_image_two_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Image $headerImageTwo;

    /**
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="categoryCoverPhoto", cascade={"persist", "remove"})
     * @JoinColumn(name="cover_photo_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Image $coverPhoto;

    /**
     * @ORM\OneToMany(targetEntity="Subcategory", mappedBy="category")
     */
    private mixed $subcategories;

    #[Pure] public function __construct()
    {
        $this->subcategories = new ArrayCollection();
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

    public function getLiving(): ?bool
    {
        return $this->living;
    }

    public function setLiving(bool $living): self
    {
        $this->living = $living;

        return $this;
    }

    /**
     * @return Collection<int, Subcategory>
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }

    public function addSubcategory(Subcategory $subcategory): self
    {
        if (!$this->subcategories->contains($subcategory)) {
            $this->subcategories[] = $subcategory;
            $subcategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubcategory(Subcategory $subcategory): self
    {
        if ($this->subcategories->removeElement($subcategory)) {
            // set the owning side to null (unless already changed)
            if ($subcategory->getCategory() === $this) {
                $subcategory->setCategory(null);
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

    public function isLiving(): ?bool
    {
        return $this->living;
    }

    public function getHeaderImageOne(): ?Image
    {
        return $this->headerImageOne;
    }

    public function setHeaderImageOne(?Image $headerImageOne): self
    {
        $this->headerImageOne = $headerImageOne;

        return $this;
    }

    public function getHeaderImageTwo(): ?Image
    {
        return $this->headerImageTwo;
    }

    public function setHeaderImageTwo(?Image $headerImageTwo): self
    {
        $this->headerImageTwo = $headerImageTwo;

        return $this;
    }

    public function getCoverPhoto(): ?Image
    {
        return $this->coverPhoto;
    }

    public function setCoverPhoto(?Image $coverPhoto): self
    {
        $this->coverPhoto = $coverPhoto;

        return $this;
    }
}