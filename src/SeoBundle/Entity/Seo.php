<?php

namespace App\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="seo")
 * @ORM\Entity(repositoryClass="App\SeoBundle\Repository\SeoRepository")
 */
class Seo
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="title", type="string", length=250)
     */
    private ?string $title;

    /**
     * @ORM\Column(name="slug", type="string", length=250)
     */
    private ?string $slug;

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

    public function getSlug(): ?string
    {
        return strtolower(trim($this->title));
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}