<?php

namespace App\CMSBundle\Entity;

use App\MediaBundle\Entity\Image;
use App\ServiceBundle\Model\DateTimeInterface;
use App\ServiceBundle\Model\DateTimeTrait;
use App\ServiceBundle\Model\VirtualDeleteTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="banner")
 * @ORM\Entity(repositoryClass="App\CMSBundle\Repository\BannerRepository")
 */
class Banner implements DateTimeInterface
{
    use DateTimeTrait, VirtualDeleteTrait;

    public static array $placements = [
        'Home Page slider (1920px * 975px)' => 1,
    ];
    public static array $placementDimensions = [
        1 => ["width" => 1920, "height" => 975],
    ];

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

    /**
     * @ORM\Column(name="placement", type="integer")
     */
    protected int $placement;

    /**
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected ?string $url;

    /**
     * @ORM\Column(name="text", type="text", length=255, nullable=true)
     */
    protected ?string $text;

    /**
     * @ORM\Column(name="action_button_text", type="string", length=20, nullable=true)
     */
    protected ?string $actionButtonText = 'View';

    /**
     * @ORM\Column(name="sort_no", type="integer", nullable=true)
     */
    protected ?int $sortNo;

    /**
     * @ORM\Column(name="publish", type="boolean")
     */
    private bool $publish = true;

    /**
     * @ORM\Column(name="open_new_tab", type="boolean")
     */
    private bool $openNewTab = false;

    /**
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", inversedBy="banner", cascade={"persist", "remove" })
     * @JoinColumn(name="image_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Image $image;

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPlacement(): ?int
    {
        return $this->placement;
    }

    #[Pure] public function getPlacementName(): ?string
    {
        $placement = $this->getPlacement();
        return array_search($placement, self::$placements);
    }

    public function setPlacement(int $placement): self
    {
        $this->placement = $placement;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getActionButtonText(): ?string
    {
        return $this->actionButtonText;
    }

    public function setActionButtonText(?string $actionButtonText): self
    {
        $this->actionButtonText = $actionButtonText;

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

    public function getOpenNewTab(): ?bool
    {
        return $this->openNewTab;
    }

    public function setOpenNewTab(bool $openNewTab): self
    {
        $this->openNewTab = $openNewTab;

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