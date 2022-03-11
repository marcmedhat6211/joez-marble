<?php

namespace App\ServiceBundle\Model;

use Doctrine\ORM\Mapping as ORM;

trait DateTimeTrait {

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(name="creator", type="string", length=255)
     */
    protected $creator;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $modified;

    /**
     * @ORM\Column(name="modified_by", type="string", length=255)
     */
    protected $modifiedBy;


    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setCreator($creator) {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator() {
        return $this->creator;
    }

    public function setModified($modified) {
        $this->modified = $modified;

        return $this;
    }

    public function getModified() {
        return $this->modified;
    }

    public function setModifiedBy($modifiedBy) {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getModifiedBy() {
        return $this->modifiedBy;
    }

}
