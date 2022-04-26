<?php

namespace App\SeoBundle\Service;

class SeoService
{
    public function generateSeoSlug(string $slugIdentifier): string
    {
        return strtolower(str_replace(" ", "-", $slugIdentifier));
    }
}