<?php

namespace App\CMSBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="faq_category")
 * @ORM\Entity(repositoryClass="App\CMSBundle\Repository\FAQCategoryRepository")
 */
class FAQCategory implements DateTimeInterface
{
    use DateTimeTrait, VirtualDeleteTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="title", type="string")
     */
    private ?string $title;

    /**
     * @ORM\Column(name="sort_no", type="integer", nullable=true)
     */
    private ?int $sortNo;

    /**
     * @ORM\OneToMany(targetEntity="App\CMSBundle\Entity\FAQ", mappedBy="faqCategory")
     */
    private mixed $faqs;

    public function __construct()
    {
        $this->faqs = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

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

    public function getSortNo(): ?int
    {
        return $this->sortNo;
    }

    public function setSortNo(?int $sortNo): self
    {
        $this->sortNo = $sortNo;

        return $this;
    }

    /**
     * @return Collection<int, FAQ>
     */
    public function getFaqs(): Collection
    {
        return $this->faqs;
    }

    public function addFaq(FAQ $faq): self
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs[] = $faq;
            $faq->setFaqCategory($this);
        }

        return $this;
    }

    public function removeFaq(FAQ $faq): self
    {
        if ($this->faqs->removeElement($faq)) {
            // set the owning side to null (unless already changed)
            if ($faq->getFaqCategory() === $this) {
                $faq->setFaqCategory(null);
            }
        }

        return $this;
    }
}