<?php

namespace App\ServiceBundle\Model;

interface DateTimeInterface {
    public function setCreated($created);
    public function getCreated();
    public function setCreator($creator);
    public function getCreator();
    public function setModified($modified);
    public function getModified();
    public function setModifiedBy($modifiedBy);
    public function getModifiedBy();
}