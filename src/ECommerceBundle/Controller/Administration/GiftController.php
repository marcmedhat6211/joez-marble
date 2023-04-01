<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\ECommerceBundle\Entity\Gift;
use App\ECommerceBundle\Form\GiftType;
use App\ECommerceBundle\Repository\GiftRepository;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gift")
 */
class GiftController extends AbstractController
{
    /**
     * @Route("/", name="gift_index", methods={"GET"})
     */
    public function index(Request $request, GiftRepository $giftRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $giftRepository->filter(new \stdClass(), false, true, 10, $request);

        return $this->render('ecommerce/admin/gift/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/{id}/edit", name="gift_edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        Gift $gift
    ): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(GiftType::class, $gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($gift);
            $em->flush();
            $this->addFlash("success", "Gift order status updated successfully");

            return $this->redirectToRoute("gift_edit", ["id" => $gift->getId()]);
        }

        return $this->render('ecommerce/admin/gift/edit.html.twig', [
            'form' => $form->createView(),
            'gift' => $gift,
        ]);
    }
}