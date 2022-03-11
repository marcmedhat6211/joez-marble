<?php

namespace App\UserBundle\Security\HWI;

use App\UserBundle\Entity\User;
use App\UserBundle\Event\RegistrationEvent;
use App\UserBundle\Repository\UserRepository;
use App\UserBundle\UserEvents;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use PN\ServiceBundle\Utils\Validate;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthUserProvider implements OAuthAwareUserProviderInterface
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private EventDispatcherInterface $eventDispatcher;
    private array $properties;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        EventDispatcherInterface $eventDispatcher,
        ParameterBagInterface $parameterBag
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->properties = $parameterBag->get("hwi_oauth.resource_owners");
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.",
                $resourceOwnerName));
        }

        $userEmail = $response->getEmail();
        $username = $response->getUsername();

        $user = $this->userRepository->findOneBy([$this->getProperty($response) => $username]);

        if (Validate::not_null($userEmail) and !$user instanceof User) {
            $user = $this->userRepository->findOneBy(["email" => $userEmail]);
        }

        // if null just create new user and set it properties
        if (null === $user) {
            //throw new \Symfony\Component\Security\Core\Exception\UserNotFoundException(sprintf("User '%s' not found.", $username));
            return $this->createUserByOAuthUserResponse($response);
        } else {
            return $this->updateUserByOAuthUserResponse($user, $response);
        }
    }

    private function createUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $user = new User();
        $this->updateUserByOAuthUserResponse($user, $response);

        // set default values taken from OAuth sign-in provider account
        if (null !== $email = $response->getEmail()) {
            $user->setEmail($email);
        } else {
            $resourceOwnerName = $response->getResourceOwner()->getName();
            $user->setEmail($response->getUsername().'@'.$resourceOwnerName.'.com');
        }

        $user->setEmail($user->getEmail());
        $user->setFullName($response->getNickname());
        $user->setEnabled(true);
        $this->em->persist($user);
        $this->em->flush();

        $event = new RegistrationEvent($user, new Request());
        $this->eventDispatcher->dispatch($event, UserEvents::REGISTRATION_COMPLETED);

        return $user;
    }


    private function updateUserByOAuthUserResponse(User $user, UserResponseInterface $response): UserInterface
    {
        $providerName = $response->getResourceOwner()->getName();
        $providerNameSetter = 'set'.ucfirst($providerName).'Id';
        $user->$providerNameSetter($response->getUsername());

        if (!$user->getPassword()) {
            $secret = md5(uniqid(rand(), true));
            $user->setPlainPassword($secret);
        }

        return $user;
    }

    private function getProperty(UserResponseInterface $response): string
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();
        if (!isset($this->properties[$resourceOwnerName])) {
            throw new RuntimeException(sprintf("No property defined for entity for resource owner '%s'.",
                $resourceOwnerName));
        }

        return $resourceOwnerName."Id";
    }
}