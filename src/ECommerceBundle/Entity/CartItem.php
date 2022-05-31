<?php

namespace App\ECommerceBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cart_item")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\CartItemRepository")
 */
class CartItem implements DateTimeInterface
{
    use DateTimeTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="quantity", type="integer")
     */
    private ?int $quantity;

    /**
     * @ORM\Column(name="item_total_price", type="float")
     */
    private ?float $itemTotalPrice;

    /**
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\Product", inversedBy="cartItem", cascade={"persist"})
     */
    private ?Product $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\ECommerceBundle\Entity\Cart", inversedBy="cartItems", cascade={"persist"})
     */
    private ?Cart $cart;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getItemTotalPrice(): ?float
    {
        return $this->itemTotalPrice;
    }

    public function setItemTotalPrice(float $itemTotalPrice): self
    {
        $this->itemTotalPrice = $itemTotalPrice;

        return $this;
    }
}