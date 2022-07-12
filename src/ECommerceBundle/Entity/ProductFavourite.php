<?php

namespace App\ECommerceBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="product_favourite")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\ProductFavouriteRepository")
 */
class ProductFavourite
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="productFavourites", cascade={"persist"})
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\ECommerceBundle\Entity\Product", inversedBy="favourites", cascade={"persist"})
     */
    private ?Product $product;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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