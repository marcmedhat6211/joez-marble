<?php

namespace App\UserBundle\Repository;

use App\UserBundle\Entity\ShippingInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShippingInformation>
 *
 * @method ShippingInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShippingInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShippingInformation[]    findAll()
 * @method ShippingInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippingInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingInformation::class);
    }

    public function add(ShippingInformation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShippingInformation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
