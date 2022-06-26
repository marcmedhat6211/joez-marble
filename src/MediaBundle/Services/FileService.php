<?php

namespace App\MediaBundle\Services;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FileService
{
    private Packages $assets;
    private Request $request;

    public function __construct(
        Packages     $assets,
        RequestStack $requestStack,
    )
    {
        $this->assets = $assets;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getFileFullAbsolutePath(string $fileAbsolutePath): string
    {
        return $this->request->getSchemeAndHttpHost() . $this->assets->getUrl($fileAbsolutePath);
    }
}