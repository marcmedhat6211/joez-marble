<?php

namespace App\UserBundle\Entity;

use App\CMSBundle\Entity\UserFeedback;
use App\ECommerceBundle\Entity\Cart;
use App\ECommerceBundle\Entity\Order;
use App\ECommerceBundle\Entity\ProductFavourite;
use App\UserBundle\Model\BaseUser;
use App\UserBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User extends BaseUser
{
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="full_name", type="string", length=255)
     */
    private ?string $fullName;

    /**
     * @ORM\Column(name="gender", type="string", length=20, nullable=true)
     */
    private ?string $gender = null;

    /**
     * @ORM\Column(name="birthdate", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $birthdate;

    /**
     * @Assert\Regex("/^[0-9\(\)\/\+ \-]+$/i")
     *
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private ?string $phone;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    private ?string $facebookId;

    /**
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    private ?string $googleId;

    /**
     * @ORM\OneToOne(targetEntity="App\ECommerceBundle\Entity\Cart", mappedBy="user", cascade={"persist"})
     */
    private ?Cart $cart;

    /**
     * @ORM\OneToMany(targetEntity="App\ECommerceBundle\Entity\Order", mappedBy="user")
     */
    private mixed $orders;

    /**
     * @ORM\OneToMany(targetEntity="App\CMSBundle\Entity\UserFeedback", mappedBy="user")
     */
    private mixed $feedbacks;

    /**
     * @ORM\OneToMany(targetEntity="App\ECommerceBundle\Entity\ProductFavourite", mappedBy="user")
     */
    private mixed $productFavourites;

    /**
     * @ORM\OneToOne(targetEntity="App\UserBundle\Entity\ShippingInformation", mappedBy="user")
     */
    private ?ShippingInformation $shippingInformation;

    #[Pure] public function __construct()
    {
        $this->feedbacks = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->productFavourites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }


    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        // unset the owning side of the relation if necessary
        if ($cart === null && $this->cart !== null) {
            $this->cart->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($cart !== null && $cart->getUser() !== $this) {
            $cart->setUser($this);
        }

        $this->cart = $cart;

        return $this;
    }

    /**
     * @return Collection<int, UserFeedback>
     */
    public function getFeedbacks(): Collection
    {
        return $this->feedbacks;
    }

    public function addFeedback(UserFeedback $feedback): self
    {
        if (!$this->feedbacks->contains($feedback)) {
            $this->feedbacks[] = $feedback;
            $feedback->setUser($this);
        }

        return $this;
    }

    public function removeFeedback(UserFeedback $feedback): self
    {
        if ($this->feedbacks->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getUser() === $this) {
                $feedback->setUser(null);
            }
        }

        return $this;
    }

    public function getShippingInformation(): ?ShippingInformation
    {
        return $this->shippingInformation;
    }

    public function setShippingInformation(?ShippingInformation $shippingInformation): self
    {
        // unset the owning side of the relation if necessary
        if ($shippingInformation === null && $this->shippingInformation !== null) {
            $this->shippingInformation->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($shippingInformation !== null && $shippingInformation->getUser() !== $this) {
            $shippingInformation->setUser($this);
        }

        $this->shippingInformation = $shippingInformation;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductFavourite>
     */
    public function getProductFavourites(): Collection
    {
        return $this->productFavourites;
    }

    public function addProductFavourite(ProductFavourite $productFavourite): self
    {
        if (!$this->productFavourites->contains($productFavourite)) {
            $this->productFavourites[] = $productFavourite;
            $productFavourite->setUser($this);
        }

        return $this;
    }

    public function removeProductFavourite(ProductFavourite $productFavourite): self
    {
        if ($this->productFavourites->removeElement($productFavourite)) {
            // set the owning side to null (unless already changed)
            if ($productFavourite->getUser() === $this) {
                $productFavourite->setUser(null);
            }
        }

        return $this;
    }
}
