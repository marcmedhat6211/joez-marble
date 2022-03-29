<?php

namespace App\ECommerceBundle\Entity;

use App\MediaBundle\Entity\Image;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Table(name="currency")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Repository\CurrencyRepository")
 */
class Currency
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="code", type="string", length=50)
     */
    private ?string $code;

    /**
     * @ORM\Column(name="egp_equivalence", type="float")
     */
    private ?float $egpEquivalence;

    /**
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="currency", cascade={"persist", "remove"})
     * @JoinColumn(name="flag_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Image $flag;

    public function __toString(): string
    {
        return $this->code;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getEgpEquivalence(): ?float
    {
        return $this->egpEquivalence;
    }

    public function setEgpEquivalence(float $egpEquivalence): self
    {
        $this->egpEquivalence = $egpEquivalence;

        return $this;
    }

    public function getFlag(): ?Image
    {
        return $this->flag;
    }

    public function setFlag(?Image $flag): self
    {
        $this->flag = $flag;

        return $this;
    }
}