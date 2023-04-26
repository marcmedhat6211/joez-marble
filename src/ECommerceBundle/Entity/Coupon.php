<?php

namespace App\ECommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="coupon")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\CouponRepository")
 */
class Coupon
{
    const EXPIRATION_TIME_IN_DAYS = 3;
    const USERS_TO_SEND_COUPON_COUNT = 20;
    const DISCOUNT = 50;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="coupon_code", type="string", length=50)
     */
    private ?string $couponCode;

    /**
     * @ORM\Column(name="expiration_date", type="datetime")
     */
    protected $expirationDate;

    public function __toString(): string
    {
        return $this->couponCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function setCouponCode(string $couponCode): self
    {
        $this->couponCode = $couponCode;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }
}