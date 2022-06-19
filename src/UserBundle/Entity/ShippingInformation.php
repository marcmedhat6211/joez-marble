<?php

namespace App\UserBundle\Entity;

use App\ServiceBundle\Model\DateTimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="`shipping_information`")
 * @ORM\Entity(repositoryClass="App\UserBundle\Repository\ShippingInformationRepository")
 */
class ShippingInformation
{
    use DateTimeTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="company_name", type="string", length=120, nullable=true)
     */
    private ?string $companyName;

    /**
     * @ORM\Column(name="first_name", type="string", length=120)
     */
    private ?string $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=120)
     */
    private ?string $lastName;

    /**
     * @ORM\Column(name="phone", type="string", length=120)
     */
    private ?string $phone;

    /**
     * @ORM\Column(name="address", type="string", length=120)
     */
    private ?string $address;

    /**
     * @ORM\Column(name="district", type="string", length=120)
     */
    private ?string $district;

    /**
     * @ORM\Column(name="address_name", type="string", length=120)
     */
    private ?string $addressName;

    /**
     * @ORM\OneToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="shippingInformation", cascade={"persist"})
     */
    private ?User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(string $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getAddressName(): ?string
    {
        return $this->addressName;
    }

    public function setAddressName(string $addressName): self
    {
        $this->addressName = $addressName;

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
