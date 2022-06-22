<?php

namespace App\ECommerceBundle\Controller\Administration;

use App\ECommerceBundle\Entity\Order;
use App\ECommerceBundle\Form\OrderType;
use App\ECommerceBundle\Repository\OrderRepository;
use App\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="order_index", methods={"GET"})
     */
    public function index(Request $request, OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getOrders($request, $orderRepository);

        return $this->render('ecommerce/admin/order/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/{id}/edit", name="order_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Order $order, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($order);
            $em->flush();
            $this->addFlash("success", "Order updated successfully");

            return $this->redirectToRoute("order_index");
        }

        return $this->render('ecommerce/admin/order/edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    //============================================================PRIVATE METHODS=================================================================

    private function getOrders(Request $request, OrderRepository $orderRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;

        return $orderRepository->filter($search, false, true, 10, $request);
    }
}