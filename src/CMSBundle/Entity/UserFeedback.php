<?php

namespace App\CMSBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use App\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_feedback")
 * @ORM\Entity(repositoryClass="App\CMSBundle\Repository\UserFeedbackRepository")
 */
class UserFeedback implements DateTimeInterface
{
    use DateTimeTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="feedbacks", cascade={"persist"})
     */
    private ?User $user;

    /**
     * @ORM\Column(name="rating", type="integer")
     */
    private int $rating;

    /**
     * @ORM\Column(name="category", type="string")
     */
    private string $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

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
}