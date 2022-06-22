<?php

namespace App\ECommerceBundle\Services;

use App\ECommerceBundle\Entity\Cart;
use App\ECommerceBundle\Entity\CartItem;
use App\ECommerceBundle\Entity\Order;
use App\ECommerceBundle\Entity\OrderItem;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Repository\CartItemRepository;
use App\ECommerceBundle\Repository\CartRepository;
use App\Kernel;
use App\ServiceBundle\Service\SendEmailService;
use App\UserBundle\Entity\ShippingInformation;
use App\UserBundle\Entity\User;
use App\UserBundle\Repository\ShippingInformationRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderService
{
    private EntityManagerInterface $em;
    private CartService $cartService;
    private SendEmailService $sendEmailService;

    public function __construct(
        EntityManagerInterface $em,
        CartService            $cartService,
        SendEmailService       $sendEmailService
    )
    {
        $this->em = $em;
        $this->cartService = $cartService;
        $this->sendEmailService = $sendEmailService;
    }

    public function createOrder(Cart $cart): void
    {
        $order = new Order();
        $order->setStatus(Order::STATUS_PENDING);
        $order->setUser($cart->getUser());
        $order->setTotalPrice($cart->getTotalPrice());
        $order->setTotalQuantity($cart->getTotalQuantity());
        foreach ($cart->getCartItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setItemTotalPrice($cartItem->getItemTotalPrice());
            $order->addOrderItem($orderItem);
            $this->em->persist($orderItem);
        }

        $this->em->persist($order);
        $this->em->flush();

        $this->cartService->clearCart($cart);
        $this->sendOrderEmail($order);
    }

    public function getOrderGrandTotal(Cart $cart): float
    {
        $shippingFee = 30;
        $taxes = 140;
        $cartTotal = $cart->getTotalPrice();
        $couponDiscount = 50;

        return (($shippingFee + $taxes + $cartTotal) - $couponDiscount);
    }

    private function sendOrderEmail(Order $order)
    {
        $email = (new TemplatedEmail())
            ->from(new Address(Kernel::FROM_EMAIL, Kernel::WEBSITE_TITLE))
            ->to(new Address(Kernel::ADMIN_EMAIL))
            ->subject(Kernel::WEBSITE_TITLE . $order->getUser()->getFullName() . ' Created a new Order')
            ->htmlTemplate('ecommerce/frontEnd/order/_admin-email.html.twig')
            ->context([
                'order' => $order,
            ]);
        $this->sendEmailService->send($email);
    }
}