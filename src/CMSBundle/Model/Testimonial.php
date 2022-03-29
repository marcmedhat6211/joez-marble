<?php

namespace App\CMSBundle\Model;

use Doctrine\ORM\Mapping as ORM;

abstract class Testimonial
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /**
     * @ORM\Column(name="client", type="string", nullable=true)
     */
    protected string $client;

    /**
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    protected ?string $message;

    /**
     * @var bool
     *
     * @ORM\Column(name="publish", type="boolean")
     */
    protected bool $publish = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(bool $publish): self
    {
        $this->publish = $publish;

        return $this;
    }
}