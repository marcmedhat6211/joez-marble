<?php

namespace App\UserBundle\Security;

use App\UserBundle\Entity\User;
use App\UserBundle\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class MyEntityUserProvider extends EntityUserProvider implements AccountConnectorInterface
{
    private UserRepository $userRepository;

    public function __construct(ManagerRegistry $registry, $class, array $properties, UserRepository $userRepository, $managerName = null)
    {
        parent::__construct($registry, $class, $properties, $managerName);
        $this->userRepository = $userRepository;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): User|UserInterface
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        $serviceName = $response->getResourceOwner()->getName();
        $setterId = "set" . ucfirst($serviceName) . "Id";
        $setterAccessToken = "set" . ucfirst($serviceName) . "AccessToken";

        $email = $response->getEmail();
        if (null === $user = $this->findUser([$this->properties[$resourceOwnerName] => $email])) {
            $user = new User();
            $user->setEmail($response->getEmail());;
            $user->$setterId($response->getEmail());
            $user->$setterAccessToken($response->getAccessToken());
            $this->setFullName($user, $response);
            $this->em->persist($user);
            $this->em->flush();

            return $user;
        }

        $user->$setterAccessToken($response->getAccessToken());

        return $user;
    }

    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of App\Model\User, but got "%s".', get_class($user)));
        }

        $property = $this->getProperty($response);
        $email = $response->getEmail();

        if (null !== $previousUser = $this->userRepository->findOneBy([$property => $email])) {
            $this->disconnect($previousUser, $response);
        }

        $serviceName = $response->getResourceOwner()->getName();
        $setter = "set" . ucfirst($serviceName) . "AccessToken";
        $user->$setter($response->getAccessToken());

        $this->updateUser($user, $response);
    }

    protected function getProperty(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf('No property defined for entity for resource owner name "%s"', $resourceOwnerName));
        }

        return $this->properties[$resourceOwnerName];
    }

    public function disconnect(UserInterface $user, UserResponseInterface $response): void
    {
        $property = $this->getProperty($response);
        $accessor = PropertyAccess::createPropertyAccessor();

        $accessor->setValue($user, $property, null);

        $this->updateUser($user, $response);
    }

    private function updateUser(UserInterface $user, UserResponseInterface $response): void
    {
        $user->setEmail($response->getEmail());
        $this->setFullName($user, $response);

        $this->em->persist($user);
        $this->em->flush();
    }

    private function setFullName(UserInterface $user, UserResponseInterface $response): void
    {
        $user->setFullName($response->getFirstName() . " " . $response->getLastName());
    }
}