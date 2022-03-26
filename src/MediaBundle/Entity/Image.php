<?php

namespace App\MediaBundle\Entity;

use App\CMSBundle\Entity\Banner;
use Doctrine\ORM\Mapping as ORM;
use App\MediaBundle\Model\Image as BaseImage;

/**
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="App\MediaBundle\Repository\ImageRepository")
 */
class Image extends BaseImage
{
    /**
     * @ORM\OneToOne(targetEntity="App\CMSBundle\Entity\Banner", mappedBy="image")
     */
    private ?Banner $banner;

    public function getBanner(): ?Banner
    {
        return $this->banner;
    }

    public function setBanner(?Banner $banner): self
    {
        // unset the owning side of the relation if necessary
        if ($banner === null && $this->banner !== null) {
            $this->banner->setImage(null);
        }

        // set the owning side of the relation if necessary
        if ($banner !== null && $banner->getImage() !== $this) {
            $banner->setImage($this);
        }

        $this->banner = $banner;

        return $this;
    }
}