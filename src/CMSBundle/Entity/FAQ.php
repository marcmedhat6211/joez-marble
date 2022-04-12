<?php

namespace App\CMSBundle\Entity;

use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="faq")
 * @ORM\Entity(repositoryClass="App\CMSBundle\Repository\FAQRepository")
 */
class FAQ implements DateTimeInterface
{
    use DateTimeTrait, VirtualDeleteTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /**
     * @ORM\Column(name="question", type="text")
     */
    protected ?string $question;

    /**
     * @ORM\Column(name="answer", type="text")
     */
    protected ?string $answer;

    /**
     * @ORM\Column(name="sort_no", type="integer", nullable=true)
     */
    protected ?int $sortNo;

    /**
     * @ORM\Column(name="publish", type="boolean")
     */
    protected bool $publish = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\CMSBundle\Entity\FAQCategory", inversedBy="faqs", cascade={"persist"})
     */
    private ?FAQCategory $faqCategory;

    public function __toString(): string
    {
        return $this->question;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

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

    public function getPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(bool $publish): self
    {
        $this->publish = $publish;

        return $this;
    }

    public function getFaqCategory(): ?FAQCategory
    {
        return $this->faqCategory;
    }

    public function setFaqCategory(?FAQCategory $faqCategory): self
    {
        $this->faqCategory = $faqCategory;

        return $this;
    }
}