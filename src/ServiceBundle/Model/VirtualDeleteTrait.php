<?php

namespace App\ServiceBundle\Model;

use Doctrine\ORM\Mapping as ORM;

trait VirtualDeleteTrait {

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted = null;

    /**
     * @ORM\Column(name="deleted_by", type="string", length=30, nullable=true)
     */
    protected $deletedBy = NULL;

    /**
     * @param bool|null $deleted
     * @return $this
     */
    public function setDeleted(?bool $deleted): self {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDeleted(): ?bool {
        return $this->deleted;
    }

    /**
     * @param string|null $deletedBy
     * @return $this
     */
    public function setDeletedBy(?string $deletedBy): self {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDeletedBy(): ?string {
        return $this->deletedBy;
    }

}
