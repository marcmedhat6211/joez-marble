<?php

namespace App\ECommerceBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\CategoryRepository")
 */
class Category implements DateTimeInterface
{
    use DateTimeTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="title", type="string", length=50)
     */
    private string $title;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}