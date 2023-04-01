<?php

namespace App\ECommerceBundle\Controller\FrontEnd;

use App\ECommerceBundle\Repository\CartItemRepository;
use App\ECommerceBundle\Repository\CartRepository;
use App\ECommerceBundle\Repository\ProductRepository;
use App\ECommerceBundle\Services\CartService;
use App\ECommerceBundle\Services\CurrencyService;
use App\MediaBundle\Services\UploadFileService;
use App\SeoBundle\Repository\SeoRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        return $this->render('ecommerce/frontEnd/cart/index.html.twig');
    }

    /**
     * @Route("", name="fe_gift_create_ajax", methods={"GET", "POST"})
     */
    public function createAjax(
        Request $request,
        UploadFileService $uploadFileService
    ): JsonResponse
    {
        return $this->json([]);
    }
}