<?php

namespace App\SeoBundle\EventListener;


use App\SeoBundle\Entity\Seo;
use App\SeoBundle\Model\SeoInterface;
use App\SeoBundle\Repository\SeoRepository;
use App\SeoBundle\Service\SeoService;
use App\ServiceBundle\Utils\Validate;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityPersistenceListener implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private SeoService $seoService;
    private SeoRepository $seoRepository;

    public function __construct(EntityManagerInterface $em, SeoService $seoService, SeoRepository $seoRepository)
    {
        $this->em = $em;
        $this->seoService = $seoService;
        $this->seoRepository = $seoRepository;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof SeoInterface)
        {
            $entityIdentifier = $entity->__toString();
            if (!$entityIdentifier) {
                throw new \Exception("Slug Can't be generated");
            }
            $newSlug = $this->seoService->generateSeoSlug($entityIdentifier);
            $otherEntity = $this->em->getRepository(Seo::class)->findOneBy(["slug" => $newSlug]);
            if ($otherEntity) {
                throw new \Exception("Slug already exists");
            }

            $seo = new Seo();
            $seo->setTitle($entityIdentifier);
            $seo->setSlug($newSlug);
            $entity->setSeo($seo);
            $this->em->persist($seo);
            $this->em->flush();
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof SeoInterface)
        {
            if ($entity->getDeleted()) {
                return;
            }

            $entitySeo = $entity->getSeo();
            $entityIdentifier = $entity->__toString();
            $newSlug = $this->seoService->generateSeoSlug($entityIdentifier);
            $isSameSlug = $this->seoRepository->findOneBy(["slug" => $newSlug]);
            if ($isSameSlug) {
                return;
            }

            $entitySeo->setTitle($entityIdentifier);
            $entitySeo->setSlug($newSlug);
            $this->em->persist($entitySeo);
        }
    }
}