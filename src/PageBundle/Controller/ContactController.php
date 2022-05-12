<?php

namespace App\PageBundle\Controller;

use App\Kernel;
use App\PageBundle\Form\ContactType;
use App\ServiceBundle\Service\SendEmailService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact-us", name="fe_contact", methods={"GET", "POST"})
     */
    public function index(
        Request             $request,
        TranslatorInterface $translator,
        SendEmailService    $emailService
    ): Response
    {
        $form = $this->createForm(ContactType::class, null, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->sendEmail($emailService, $data);
            $this->addFlash('success', $translator->trans('contact_message_sent_success_msg'));

            return $this->redirectToRoute('fe_contact');
        }

        return $this->render('page/contact/index.html.twig', [
            'contact_form' => $form->createView(),
        ]);
    }

    //====================================================================================PRIVATE METHODS============================================================================

    private function sendEmail(SendEmailService $emailService, array $data)
    {
        $email = (new TemplatedEmail())
            ->from(new Address(Kernel::FROM_EMAIL, Kernel::WEBSITE_TITLE))
            ->to(new Address(Kernel::ADMIN_EMAIL))
            ->subject(Kernel::WEBSITE_TITLE . ' - contact us from ' . $data['name'])
            ->htmlTemplate('page/contact/email.html.twig')
            ->context([
                'data' => $data,
            ]);
        $emailService->send($email);
    }
}