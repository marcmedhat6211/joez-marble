<?php

namespace App\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="App\MediaBundle\Repository\ImageRepository")
 */
class Image
{
    const IMAGE_TYPE_MAIN = "main";
    const IMAGE_TYPE_GALLERY = "gallery";

    public static array $imageTypes = [
        1 => self::IMAGE_TYPE_MAIN,
        2 => self::IMAGE_TYPE_GALLERY
    ];

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=220)
     */
    private string $name;

    /**
     * @ORM\Column(name="path", type="string")
     */
    private string $path;

    /**
     * @ORM\Column(name="alt", type="string", nullable=true)
     */
    private string $alt;

    /**
     * @ORM\Column(name="width", type="float")
     */
    private float $width;

    /**
     * @ORM\Column(name="height", type="float")
     */
    private float $height;

    /**
     * @ORM\Column(name="size", type="float")
     */
    private float $size;

    /**
     * @ORM\Column(name="type", type="string")
     */
    private string $type = self::IMAGE_TYPE_MAIN;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(float $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}