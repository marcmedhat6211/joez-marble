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
        'Home Page slider (1850px * 355px)' => 1,
        'Collection banner one (1850px * 270px)' => 2,
        'Collection banner two (1850px * 270px)' => 3,
        'Collection banner three (1850px * 270px)' => 4,
        'Mid Page Big Banner (1290px * 510px)' => 5,
        'Mid Page small Banner One (410px * 375px)' => 6,
        'Mid Page small Banner Two (410px * 375px)' => 7,
        'Mid Page small Banner Three (410px * 375px)' => 8,
        'Mid Page Left Banner (575px * 510px)' => 9,
        'Mid Page Right Small Banner One (410px * 240px)' => 10,
        'Mid Page Right Small Banner Two (410px * 240px)' => 11,
        'Mid Page Living Banner (300px * 300px)' => 12,
        'Category Banner One (630px * 350px)' => 13,
        'Category Banner Two (630px * 350px)' => 14,
    ];
    public static array $placementDimensions = [
        1 => ["width" => 1850, "height" => 355],
        2 => ["width" => 1850, "height" => 270],
        3 => ["width" => 1850, "height" => 270],
        4 => ["width" => 1850, "height" => 270],
        5 => ["width" => 1290, "height" => 510],
        6 => ["width" => 410, "height" => 375],
        7 => ["width" => 410, "height" => 375],
        8 => ["width" => 410, "height" => 375],
        9 => ["width" => 575, "height" => 510],
        10 => ["width" => 410, "height" => 240],
        11 => ["width" => 410, "height" => 240],
        12 => ["width" => 300, "height" => 300],
        13 => ["width" => 630, "height" => 350],
        14 => ["width" => 630, "height" => 350],
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
    private ?Image $image = null;

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