<?php

namespace App\ServiceBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService
{

    protected TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }

        return $token->getUser();
    }

    public function getUserName(): string
    {
        $user = $this->getUser();
        if ($user === null) {
            return 'none';
        } elseif ('cli' === PHP_SAPI) {
            return "System-CLI";
        } elseif (method_exists($user, 'getFullName') == true) {
            return $user->getFullName();
        } elseif (method_exists($user, 'getName') == true) {
            return $user->getFullName();
        } elseif (method_exists($user, 'getUserName') == true) {
            return $user->getUserName();
        }

        return "none";
    }

}
