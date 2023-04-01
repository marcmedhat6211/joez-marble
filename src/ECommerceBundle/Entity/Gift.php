<?php

namespace App\ECommerceBundle\Entity;

use App\MediaBundle\Entity\Image;
use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="gift")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\GiftRepository")
 */
class Gift implements DateTimeInterface
{
    use DateTimeTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="status", type="string")
     */
    private string $status = Order::STATUS_PENDING;

    /**
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     */
    private ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\MediaBundle\Entity\Image")
     */
    private ?Image $image = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }
}