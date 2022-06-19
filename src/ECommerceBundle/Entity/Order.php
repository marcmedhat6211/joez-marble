<?php

namespace App\ECommerceBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use App\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="order")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\OrderRepository")
 */
class Order implements DateTimeInterface
{
    use DateTimeTrait, VirtualDeleteTrait;

    const STATUS_PENDING = "pending";
    const STATUS_SHIPPED = "shipped";
    const STATUS_DELIVERED = "delivered";
    const STATUS_CANCELLED = "cancelled";

    public static array $orderStatuses = [
        "Pending" => self::STATUS_PENDING,
        "Shipped" => self::STATUS_SHIPPED,
        "Delivered" => self::STATUS_DELIVERED,
        "Cancelled" => self::STATUS_CANCELLED
    ];

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="total_price", type="float")
     */
    private float $totalPrice = 0;

    /**
     * @ORM\Column(name="status", type="string")
     */
    private string $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(name="total_quantity", type="integer")
     */
    private int $totalQuantity = 0;

    /**
     * @ORM\OneToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="cart", cascade={"persist"})
     */
    private ?User $user;

    /**
     * @ORM\OneToMany(targetEntity="App\ECommerceBundle\Entity\OrderItem", mappedBy="order")
     */
    private mixed $orderItems;

    #[Pure] public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}