<?php

namespace App\ECommerceBundle\Services;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GiftService
{
    const GIFTS_PATH = "/gift";

    private ContainerInterface $container;

    public function __construct(
        ContainerInterface $container
    )
    {
        $this->container = $container;
    }

    public function createGift(UploadedFile $image)
    {
        $imagePath = $this->getImagePath($image);
        $this->saveImageLocally($image, $imagePath);
        //@TODO: create a path to save the gifts at
        //@TODO: save the gift locally and get back the file path to use it in image entity
        //@TODO: create a new gift entry in database
    }

    //============================================== PRIVATE METHODS ===========================================

    private function saveImageLocally(UploadedFile $image, string $imagePath): void
    {
        $uploadsDir = $this->container->getParameter("uploads_dir");
        $image->move("$uploadsDir/$imagePath", $image->getClientOriginalName());
    }

    private function getImagePath(UploadedFile $image): string
    {
        $fileUploadedDate = $this->getImageUploadedDay($image);
        $fileUploadedDateArr = explode("/", $fileUploadedDate);
        $year = $fileUploadedDateArr[0];
        $month = $fileUploadedDateArr[1];
        $day = $fileUploadedDateArr[2];

        return $year . "/" . $month . "/" . $day . self::GIFTS_PATH;
    }

    private function getImageUploadedDay(UploadedFile $file): string
    {
        return date("Y/m/d", filemtime($file));
    }
}