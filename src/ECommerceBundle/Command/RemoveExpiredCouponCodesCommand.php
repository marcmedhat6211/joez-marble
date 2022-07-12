<?php

namespace App\ECommerceBundle\Command;

use App\ECommerceBundle\Entity\Coupon;
use App\ECommerceBundle\Repository\CouponRepository;
use App\Kernel;
use App\ServiceBundle\Service\SendEmailService;
use App\UserBundle\Entity\User;
use App\UserBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Address;

class RemoveExpiredCouponCodesCommand extends Command
{
    protected static $defaultName = 'app:coupon:expired:remove';
    private CouponRepository $couponRepository;
    private EntityManagerInterface $em;

    public function __construct(
        CouponRepository       $couponRepository,
        EntityManagerInterface $em,
        string                 $name = null
    )
    {
        parent::__construct($name);
        $this->couponRepository = $couponRepository;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate a coupon and send it to users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $coupons = $this->couponRepository->findAll();
        foreach ($coupons as $coupon) {
            $couponExpirationDate = $coupon->getExpirationDate();
            $now = new \DateTime();

            if ($couponExpirationDate <= $now) {
                $this->em->remove($coupon);
                $this->em->flush();
            }
        }
        return 0;
    }
}