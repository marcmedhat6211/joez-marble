<?php

namespace App\ECommerceBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use App\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="order_item")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\OrderItemRepository")
 */
class OrderItem implements DateTimeInterface
{
    use DateTimeTrait, VirtualDeleteTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

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
     * @ORM\ManyToOne(targetEntity="App\ECommerceBundle\Entity\Order", inversedBy="orderItems", cascade={"persist"})
     */
    private ?Order $order;

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

    public function getItemTotalPrice(): ?float
    {
        return $this->itemTotalPrice;
    }

    public function setItemTotalPrice(float $itemTotalPrice): self
    {
        $this->itemTotalPrice = $itemTotalPrice;

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

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}