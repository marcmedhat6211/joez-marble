<?php

namespace App\MediaBundle\Services;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFileService
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function uploadFile(UploadedFile $file): string {
        $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();
        $file->move(
            $this->container->getParameter('uploads_dir'),
            $fileName
        );

        return $fileName;
    }
}