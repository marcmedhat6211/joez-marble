<?php

namespace App\ECommerceBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="product_spec")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\ProductSpecRepository")
 */
class ProductSpec implements DateTimeInterface
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
     * @ORM\Column(name="value", type="string", length=120)
     */
    private ?string $value;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productSpecs", cascade={"persist"})
     */
    private ?Product $product;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
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
}