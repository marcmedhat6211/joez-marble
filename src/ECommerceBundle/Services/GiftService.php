<?php

namespace App\ECommerceBundle\Services;

use App\ECommerceBundle\Entity\Gift;
use App\ECommerceBundle\Entity\Order;
use App\Kernel;
use App\MediaBundle\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;

class GiftService
{
    const GIFTS_PATH = "/gift";

    private ContainerInterface $container;
    private Security $security;
    private EntityManagerInterface $em;
    private MailerInterface $mailer;

    public function __construct(
        ContainerInterface     $container,
        Security               $security,
        EntityManagerInterface $em,
        MailerInterface $mailer,
    )
    {
        $this->container = $container;
        $this->security = $security;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function createGift(UploadedFile $image)
    {
        $imagePath = $this->getImagePath($image);
        $this->saveImageLocally($image, $imagePath);

        $gift = new Gift();
        $gift->setUser($this->security->getUser());
        $gift->setStatus(Order::STATUS_PENDING);
        $gift->setImage($this->createGiftImageObject($image, $imagePath));
        $this->em->persist($gift);
        $this->em->flush();

        $this->sendEmailAfterCreatingGift($gift);
        $this->sendEmailAfterCreatingGift($gift, true);
    }

    //============================================== PRIVATE METHODS ===========================================

    private function saveImageLocally(UploadedFile $image, string $imagePath): void
    {
        $uploadsDir = $this->container->getParameter("uploads_dir");
        $image->move("$uploadsDir/$imagePath", $image->getClientOriginalName());
    }

    #[Pure]
    private function getImagePath(UploadedFile $image): string
    {
        $fileUploadedDate = $this->getImageUploadedDay($image);
        $fileUploadedDateArr = explode("/", $fileUploadedDate);
        $year = $fileUploadedDateArr[0];
        $month = $fileUploadedDateArr[1];
        $day = $fileUploadedDateArr[2];

        return $year . "/" . $month . "/" . $day . self::GIFTS_PATH;
    }

    private function getImageUploadedDay(UploadedFile $file): string
    {
        return date("Y/m/d", filemtime($file));
    }

    private function createGiftImageObject(UploadedFile $image, string $imagePath): Image
    {
        $uploadsDir = $this->container->getParameter("uploads_dir");
        $imageSize = getimagesize("$uploadsDir/$imagePath/{$image->getClientOriginalName()}");

        $imageObj = new Image();
        $imageObj->setPath($imagePath);
        $imageObj->setName($image->getClientOriginalName());
        list($width, $height) = $imageSize;
        $imageObj->setWidth($width);
        $imageObj->setHeight($height);
        $imageObj->setSize($width * $height);
        $imageObj->setType(Image::IMAGE_TYPE_MAIN);
        $this->em->persist($imageObj);

        return $imageObj;
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendEmailAfterCreatingGift(Gift $gift, bool $isReceiverAdmin = false)
    {
        $user = $this->security->getUser();

        if ($isReceiverAdmin) {
            $to = Kernel::ADMIN_EMAIL;
            $subject = Kernel::WEBSITE_TITLE . "| {$user->getEmail()} has submitted a gift order";
        } else {
            $to = $user->getEmail();
            $subject = Kernel::WEBSITE_TITLE . "| Your gift order has been submitted successfully!";
        }

        $email = (new TemplatedEmail())
            ->from(new Address(Kernel::FROM_EMAIL, Kernel::WEBSITE_TITLE))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate('ecommerce/frontEnd/gift/email.html.twig')
            ->context([
                'user' => $user,
                'gift' => $gift,
                'isReceiverAdmin' => $isReceiverAdmin
            ]);

        $this->mailer->send($email);
    }
}