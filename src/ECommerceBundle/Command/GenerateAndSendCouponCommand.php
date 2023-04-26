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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Address;

class GenerateAndSendCouponCommand extends Command
{
    protected static $defaultName = 'app:coupon:generate:send';
    private CouponRepository $couponRepository;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private SendEmailService $emailService;

    public function __construct(
        CouponRepository       $couponRepository,
        EntityManagerInterface $em,
        UserRepository         $userRepository,
        SendEmailService       $emailService,
        string                 $name = null
    )
    {
        parent::__construct($name);
        $this->couponRepository = $couponRepository;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate a coupon and send it to users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        // generate the coupon code
        $couponCode = $this->generateRandomString(5);
        while (true) {
            $existingCoupon = $this->couponRepository->findOneBy(["couponCode" => $couponCode]);
            if ($existingCoupon) {
                $couponCode = $this->generateRandomString(5);
            } else {
                break;
            }
        }

        // save the coupon code to db
        $coupon = $this->createNewCouponInDB($couponCode);

        // send the coupon code to users
        $users = $this->userRepository->getMostInteractiveUsers(Coupon::USERS_TO_SEND_COUPON_COUNT);
        foreach ($users as $user) {
            $this->sendCouponCodeByEmail($user, $coupon);
        }

        $this->removeExpiredCouponsFromDb();

        $symfonyStyle->success("Process Completed!");
        return 0;
    }

    private function generateRandomString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    private function createNewCouponInDB(string $couponCode): Coupon
    {
        $coupon = new Coupon();
        $coupon->setCouponCode($couponCode);
        $coupon->setExpirationDate($this->generateExpirationDate());

        $this->em->persist($coupon);
        $this->em->flush();

        return $coupon;
    }

    private function generateExpirationDate(): \DateTime
    {
        $now = new \DateTime();

        return $now->modify("+" . Coupon::EXPIRATION_TIME_IN_DAYS . " days");
    }

    private function sendCouponCodeByEmail(User $user, Coupon $coupon)
    {
        $email = (new TemplatedEmail())
            ->from(new Address(Kernel::FROM_EMAIL, Kernel::WEBSITE_TITLE))
            ->to(new Address($user->getEmail()))
            ->subject(Kernel::WEBSITE_TITLE . ' - New Coupon Code')
            ->htmlTemplate('ecommerce/frontEnd/coupon/couponEmail.html.twig')
            ->context([
                'user' => $user,
                'coupon' => $coupon,
            ]);
        $this->emailService->send($email);
    }

    private function removeExpiredCouponsFromDb()
    {
        $coupons = $this->couponRepository->getExpiredCoupons();

        if (count($coupons) > 0) {
            foreach ($coupons as $coupon) {
                $couponExpirationDate = $coupon->getExpirationDate();
                $now = new \DateTime();

                if ($couponExpirationDate <= $now) {
                    $this->em->remove($coupon);
                    $this->em->flush();
                }
            }
        }
    }
}