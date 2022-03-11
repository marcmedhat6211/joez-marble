<?php

namespace App\ServiceBundle\EventListener;

use App\ServiceBundle\Model\DateTimeInterface;
use App\UserBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DateTimeSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof DateTimeInterface) {
            $username = $this->getUserName();
            $entity->setModified(new \DateTime(date('Y-m-d H:i:s')));
            $entity->setModifiedBy($username);

        }
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof DateTimeInterface) {
            $username = $this->getUserName();

            $entity->setModified(new \DateTime(date('Y-m-d H:i:s')));
            $entity->setModifiedBy($username);

            if ($entity->getCreated() == null) {
                $entity->setCreated(new \DateTime(date('Y-m-d H:i:s')));
            }
            if ($entity->getCreator() == null) {
                $entity->setCreator($username);
            }
        }
    }

    public function getUser()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }

        return $token->getUser();
    }

    private function getUserName(): string
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return 'none';
        }
        if (method_exists($user, 'getName') == true) {
            $userName = $user->getName();
        } elseif (method_exists($user, 'getFullName') == true) {
            $userName = $user->getFullName();
        } else {
            $userName = $user->getUserName();
        }

        return $userName;
    }

}