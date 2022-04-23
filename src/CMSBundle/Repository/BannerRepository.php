<?php

namespace App\CMSBundle\Repository;

use App\CMSBundle\Entity\Banner;
use App\ServiceBundle\Utils\Validate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Banner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banner[]    findAll()
 * @method Banner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannerRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Banner::class);
        $this->paginator = $paginator;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Banner $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Banner $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    private function getStatement()
    {
        return $this->createQueryBuilder('b');
    }

    private function filterWhereClause(QueryBuilder $statement, \stdClass $search)
    {
        if (isset($search->string) and Validate::not_null($search->string)) {
            $statement->andWhere('b.id LIKE :searchTerm '
                . 'OR b.title LIKE :searchTerm '
            );
            $statement->setParameter('searchTerm', '%' . trim($search->string) . '%');
        }

        if (isset($search->placement) and $search->placement != "") {
            $statement->andWhere('b.placement = :placement');
            $statement->setParameter('placement', $search->placement);
        }

        if (isset($search->publish) and $search->publish != "") {
            $statement->andWhere('b.publish = :publish');
            $statement->setParameter('publish', $search->publish);
        }

        if (isset($search->deleted) and in_array($search->deleted, array(0, 1))) {
            if ($search->deleted == 1) {
                $statement->andWhere('b.deleted IS NOT NULL');
            } else {
                $statement->andWhere('b.deleted IS NULL');
            }
        }
    }

    private function filterOrder(QueryBuilder $statement, \stdClass $search)
    {
        $sortSQL = [
            'b.id',
            'b.sortNo',
            'b.title',
            'b.placement',
            'b.publish',
            'b.created',
        ];

        if (isset($search->ordr) and Validate::not_null($search->ordr)) {
            $dir = $search->ordr['dir'];
            $columnNumber = $search->ordr['column'];
            if (isset($columnNumber) and array_key_exists($columnNumber, $sortSQL)) {
                $statement->orderBy($sortSQL[$columnNumber], $dir);
            }
        } else {
            $statement->orderBy($sortSQL[0], "DESC");
        }
    }

    private function filterCount(QueryBuilder $statement)
    {
        $statement->select("COUNT(DISTINCT b.id)");
        $statement->setMaxResults(1);

        $count = $statement->getQuery()->getOneOrNullResult();
        if (is_array($count) and count($count) > 0) {
            return (int)reset($count);
        }

        return 0;
    }

    public function filter($search, $count = false, $isPagination = false, $pageLimit = null, Request $request = null)
    {
        $statement = $this->getStatement();
        $this->filterWhereClause($statement, $search);

        if ($count == true) {
            return $this->filterCount($statement);
        }
        $statement->groupBy('b.id');
        $this->filterOrder($statement, $search);

        if ($isPagination) {
            return $this->paginator->paginate($statement->getQuery(), $request->query->getInt('page', 1), $pageLimit);
        }

        return $statement->getQuery()->execute();
    }
}
