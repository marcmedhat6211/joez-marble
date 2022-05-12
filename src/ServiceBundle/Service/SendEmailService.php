<?php

namespace App\ServiceBundle\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SendEmailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(RawMessage $email): void
    {
        $this->mailer->send($email);
    }
}