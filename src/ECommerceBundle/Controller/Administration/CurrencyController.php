<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\CMSBundle\Entity\Banner;
use App\ECommerceBundle\Entity\Currency;
use App\ECommerceBundle\Form\CurrencyType;
use App\ECommerceBundle\Repository\CurrencyRepository;
use App\MediaBundle\Model\Image as BaseImage;
use App\MediaBundle\Services\UploadFileService;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/currency")
 */
class CurrencyController extends AbstractController
{
    /**
     * @Route("/", name="currency_index", methods={"GET"})
     */
    public function index(Request $request, CurrencyRepository $currencyRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getCurrencies($request, $currencyRepository);

        return $this->render('ecommerce/admin/currency/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="currency_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $currency = new Currency();
        $form = $this->createForm(CurrencyType::class, $currency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($currency);
            $em->flush();

            if ($form->get("flag")->getData()) {
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Currency::class,
                    $currency,
                    UploadFileService::ACTION_ADD,
                    "flag",
                    BaseImage::IMAGE_TYPE_MAIN,
                    20,
                    12
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("currency_edit", ["id" => $currency->getId()]);
                    }
                }
            }

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("currency_index");
        }

        return $this->render('ecommerce/admin/currency/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="currency_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Currency $currency, EntityManagerInterface $em, UploadFileService $uploadFileService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(CurrencyType::class, $currency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($currency);
            $em->flush();
            if ($form->get("flag")->getData()) {
                $isImageUploaded = $uploadFileService->uploadImage(
                    $form,
                    Currency::class,
                    $currency,
                    UploadFileService::ACTION_EDIT,
                    "flag",
                    BaseImage::IMAGE_TYPE_MAIN,
                    20,
                    12
                );
                if (!$isImageUploaded["valid"]) {
                    foreach ($isImageUploaded["errors"] as $error) {
                        $this->addFlash("error", $error);
                        return $this->redirectToRoute("currency_edit", ["id" => $currency->getId()]);
                    }
                }
            }

            $this->addFlash("success", "Currency updated successfully");

            return $this->redirectToRoute("currency_index");
        }

        return $this->render('ecommerce/admin/currency/edit.html.twig', [
            'form' => $form->createView(),
            'currency' => $currency,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="currency_delete", methods={"GET", "POST"})
     */
    public function delete(Currency $currency, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $em->remove($currency);
        $em->flush();
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("currency_index");
    }

    private function getCurrencies(Request $request, CurrencyRepository $currencyRepository)
    {
        $search = new \stdClass();

        return $currencyRepository->filter($search, false, true, 10, $request);
    }
}