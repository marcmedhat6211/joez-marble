<?php

namespace App\MediaBundle\Controller\Administration;

use App\MediaBundle\Entity\Image;
use App\MediaBundle\Services\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/{id}/delete/{entityObjectId}/{className}", name="image_delete", methods={"GET", "POST"})
     */
    public function delete(
        Image $image,
        string $entityObjectId,
        string $className,
        EntityManagerInterface $em,
        UploadFileService $uploadFileService
    ) :Response
    {
        $entityObject = $em->getRepository($className)->find((int)$entityObjectId);
        $entityObject->removeGalleryImage($image);
        $em->persist($entityObject);
        $em->flush();
        $uploadFileService->removeImage($image);

        $entityObjectName = $this->getEntityObjectName($className);
        $routeName = $entityObjectName."_gallery_images";

        $this->addFlash("success", "Image deleted successfully");
        return $this->redirectToRoute($routeName, ["id" => $entityObject->getId()]);
    }

    private function getEntityObjectName(string $className): string
    {
        $classNameArr = explode("\\", $className);

        return strtolower(end($classNameArr));
    }
}