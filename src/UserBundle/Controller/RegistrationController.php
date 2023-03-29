<?php

namespace App\UserBundle\Controller;

use App\UserBundle\Entity\User;
use App\UserBundle\Event\RegistrationEvent;
use App\UserBundle\Form\RegistrationType;
use App\UserBundle\Repository\UserRepository;
use App\UserBundle\Security\CustomAuthenticator;
use App\UserBundle\UserEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\FirewallMapInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class RegistrationController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @Route("/register", name="app_user_registration")
     */
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        EventDispatcherInterface $eventDispatcher,
        UserAuthenticatorInterface $userAuthenticator,
        CustomAuthenticator $authenticator,
        FirewallMapInterface $firewallMap

    ): Response {
        //if user is already logged in just redirect him to home and tell him that he needs to log out first
        if ($this->getUser()) {
            $this->addFlash('warning',
                'You are already logged in as a user, please logout if you want to create another account with different credentials');

            return $this->redirectToRoute('fe_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // persisting and adding the user to the database
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Welcome to Joez Marble {$user->getFullName()}!, You Signed up successfully");

            $event = new RegistrationEvent($user, $request);
            $eventDispatcher->dispatch($event, UserEvents::REGISTRATION_COMPLETED);

            $userAuthenticator->authenticateUser($user, $authenticator, $request);

            return $this->onAuthenticationSuccess($request, $firewallMap);
        }

        return $this->render('user/registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    private function onAuthenticationSuccess(Request $request, FirewallMapInterface $firewallMap): ?Response
    {
        $firewallConfig = $firewallMap->getFirewallConfig($request);

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallConfig->getName())) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->generateUrl('fe_home'));
    }

}
