<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Entity\Gift;
use App\ECommerceBundle\Repository\CartItemRepository;
use App\ECommerceBundle\Repository\CartRepository;
use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Services\CartService;
use App\ECommerceBundle\Services\CurrencyService;
use App\ECommerceBundle\Services\GiftService;
use App\MediaBundle\Entity\Image;
use App\MediaBundle\Services\UploadFileService;
use App\SeoBundle\Repository\SeoRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/gift")
 */
class GiftController extends AbstractController
{
    /**
     * @Route("", name="fe_gift_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('ecommerce/frontEnd/gift/index.html.twig');
    }

    /**
     * @Route("/create-ajax", name="fe_gift_create_ajax", methods={"GET", "POST"})
     */
    public function createAjax(
        Request $request,
        GiftService $giftService
    ): JsonResponse
    {
        /** @var UploadedFile $giftImage */
        $giftImage = $request->files->get("gift");
        if (!$giftImage instanceof UploadedFile) return $this->json([
           "success" => false,
           "message" => "A server error occurred! Please provide an image to submit to the server"
        ]);

        $giftService->createGift($giftImage);

        return $this->json(["success" => true]);
    }

    /**
     * @Route("/download/{gift}", name="gift_download")
     */
    public function download(
        Gift $gift
    ): Response
    {
        $giftPath = $gift->getImage()->getAbsolutePath();
        $response = new BinaryFileResponse($giftPath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'gift.png');

        return $response;
    }
}