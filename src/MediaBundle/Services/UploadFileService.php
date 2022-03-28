<?php

namespace App\MediaBundle\Services;

use App\MediaBundle\Entity\Image;
use App\MediaBundle\Model\Image as BaseImage;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function MongoDB\Driver\Monitoring\removeSubscriber;

class UploadFileService
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;

    const MAX_FILE_SIZE = 2097152; // in bytes
    const ACTION_ADD = "add";
    const ACTION_EDIT = "edit";
    public static array $availableImageMimeTypes = ["image/gif", "image/jpeg", "image/jpg", "image/png"];

    public function __construct(
        ContainerInterface     $container,
        EntityManagerInterface $em
    )
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function uploadImage(
        FormInterface $form,
        string        $fullEntityPath,
        object        $entityObject = null,
        string        $actionType = self::ACTION_ADD,
        string        $filename = "image",
        string        $imageType = BaseImage::IMAGE_TYPE_MAIN,
        float         $maxWidth = 0,
        float         $maxHeight = 0,
    ): array
    {
        if (!$form->get($filename)->getData()) { // no image uploaded
            return [
                "valid" => true,
                "errors" => []
            ];
        }

        $file = $form->get($filename)->getData();
        $errors = $this->validateFile($file, $maxWidth, $maxHeight);
        if (count($errors) > 0) {
            return [
                "valid" => false,
                "errors" => $errors
            ];
        }

        if ($actionType == self::ACTION_EDIT) {
            $methodName = "get" . ucfirst($filename);
            $oldImage = $entityObject->{$methodName}();
            if ($oldImage) {
                $this->removeImage($oldImage);
            }
        }

        $fileDBName = md5(uniqid()) . '.' . $file->guessClientExtension();
        $entityName = $this->getEntityName($fullEntityPath);
        $filePath = $this->generateFilePath($file, $entityName);

        list($width, $height) = getimagesize($file);
        $this->createNewImage($fileDBName, $imageType, $filePath, $file->getSize(), $width, $height, $entityObject);
        $imageFullPath = $this->container->getParameter("uploads_dir") . "/" . $filePath;

        $file->move(
            $imageFullPath,
            $fileDBName
        );

        return [
            "valid" => true,
            "errors" => []
        ];
    }

    /**
     * This method removes the image from the database and deletes it from the uploads folder
     * @param Image $image
     */
    public function removeImage(Image $image): void
    {
        $this->removeImageFromItsDirectory($image);

        $this->em->remove($image);
        $this->em->flush();
    }

    //======================================================================PRIVATE METHODS======================================================================================

    /**
     * This method returns the entity name
     * @param string $fullEntityPath
     * @return string
     */
    private function getEntityName(string $fullEntityPath): string
    {
        $entityPathArr = explode("\\", $fullEntityPath);

        return strtolower(end($entityPathArr));
    }

    /**
     * This method validates the given file
     * @param UploadedFile $file
     * @param float $maxWidth
     * @param float $maxHeight
     * @return array
     */
    private function validateFile(UploadedFile $file, float $maxWidth, float $maxHeight): array
    {
        $errors = [];
        list($width, $height) = getimagesize($file);
        $fileSize = $file->getSize();
        $fileMimeType = $file->getMimeType();

        if ($fileSize > self::MAX_FILE_SIZE) {
            $errors[] = "File size exceeds limit (Max Size is 2 MB)";
        }

        if ($maxWidth > 0 && $maxHeight > 0) {
            if ($width > $maxWidth || $height > $maxHeight) {
                $errors[] = "Please upload a file with the right dimensions";
            }
        }

        if (!in_array($fileMimeType, self::$availableImageMimeTypes)) {
            $errors[] = "File type is not valid";
        }

        return $errors;
    }

    /**
     * This method generates the file path
     * @param UploadedFile $file
     * @param string $entityName
     * @return string
     */
    #[Pure] private function generateFilePath(UploadedFile $file, string $entityName): string
    {
        $fileUploadedDate = $this->getFileUploadedDay($file);
        $fileUploadedDateArr = explode("/", $fileUploadedDate);
        $year = $fileUploadedDateArr[0];
        $month = $fileUploadedDateArr[1];
        $day = $fileUploadedDateArr[2];

        return $year . "/" . $month . "/" . $day . "/" . $entityName;
    }

    /**
     * This method gets the file's uploaded day
     * @param $file
     * @return string
     */
    private function getFileUploadedDay($file): string
    {
        return date("Y/m/d", filemtime($file));
    }

    /**
     * This method creates a new row in the image table
     * @param string $fileDBName
     * @param string $imageType
     * @param string $path
     * @param float $size
     * @param float $width
     * @param float $height
     * @param object $entityObject
     */
    private function createNewImage(
        string $fileDBName,
        string $imageType,
        string $path,
        float  $size,
        float  $width,
        float  $height,
        object $entityObject
    ): void
    {
        $image = new Image();
        $image->setName($fileDBName);
        $image->setType($imageType);
        $image->setPath($path);
        $image->setSize($size);
        $image->setWidth($width);
        $image->setHeight($height);
        if ($entityObject->getTitle()) {
            $image->setAlt($entityObject->getTitle());
        }
        $entityObject->setImage($image);

        $this->em->persist($image);
        $this->em->persist($entityObject);
        $this->em->flush();
    }

    private function removeImageFromItsDirectory(Image $image): void
    {
        $imageFullPath = $this->getImageFullPath($image);

        if (file_exists($imageFullPath)) {
            unlink($imageFullPath);
        }
    }

    /**
     * This method returns the full path of an image
     * @param Image $image
     * @return string
     */
    private function getImageFullPath(Image $image): string {
        $uploadsDir = $this->container->getParameter("uploads_dir");
        $imagePath = $image->getPath();
        $imageName = $image->getName();

        return $uploadsDir . "/" . $imagePath . "/" . $imageName;
    }
}