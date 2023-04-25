<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Entity\Gift;
use App\ECommerceBundle\Services\GiftService;
use App\ServiceBundle\Utils\Validate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

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
        Request     $request,
        GiftService $giftService
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!Validate::not_null($user->getPhone())) return $this->json([
            "success" => false,
            "message" => "Please provide your phone number in your profile information so that the administrator can contact you, you can do that by clicking on the user icon above > edit profile, or by clicking on 'My Account' button below in the footer"
        ]);

        /** @var UploadedFile $giftImage */
        $giftImage = $request->files->get("gift");
        if (!$giftImage instanceof UploadedFile) return $this->json([
            "success" => false,
            "message" => "A server error occurred! Please provide an image to submit to the server"
        ]);

        try {
            $giftService->createGift($giftImage);
        } catch (TransportExceptionInterface $exception) {
            return $this->json([
                "success" => false,
                "message" => "A server error occurred! {$exception->getMessage()}"
            ]);
        }

        return $this->json(["success" => true, "message" => "Your gift has been submitted successfully, Please check your email, and the administrator will contact you shortly"]);
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