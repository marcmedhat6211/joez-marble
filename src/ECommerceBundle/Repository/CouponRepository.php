<?php

namespace App\ECommerceBundle\Repository;

use App\ECommerceBundle\Entity\Coupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coupon>
 *
 * @method Coupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coupon[]    findAll()
 * @method Coupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function add(Coupon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Coupon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws \Exception
     */
    public function checkIfCouponIsStillActive(Coupon $couponObj): bool
    {
        return $this->createQueryBuilder("c")
            ->select("COUNT(c.id)")
            ->where("c.id = :couponId")
            ->andWhere(":today <= c.expirationDate")
            ->setParameters([
                "couponId" => $couponObj->getId(),
                "today" => new \DateTime()
            ])
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function getExpiredCoupons()
    {
        return $this->createQueryBuilder("c")
            ->where("c.expirationDate < :today")
            ->setParameter("today", new \DateTime())
            ->getQuery()
            ->getResult()
            ;
    }
}
