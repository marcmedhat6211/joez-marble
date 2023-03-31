<?php

namespace App\ECommerceBundle\Repository;

use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Entity\ProductFavourite;
use App\ServiceBundle\Utils\Validate;
use App\UserBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<ProductFavourite>
 *
 * @method ProductFavourite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductFavourite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductFavourite[]    findAll()
 * @method ProductFavourite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductFavouriteRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, ProductFavourite::class);
        $this->paginator = $paginator;
    }

    public function add(ProductFavourite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductFavourite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeProductFavouriteByUserAndProduct(User $user, Product $product): void
    {
        $this->createQueryBuilder("pf")
            ->delete()
            ->andWhere("pf.user = :userId")
            ->andWhere("pf.product = :productId")
            ->setParameter("userId", $user->getId())
            ->setParameter("productId", $product->getId())
            ->getQuery()
            ->execute()
        ;
    }

    private function getStatement()
    {
        return $this->createQueryBuilder('pf');
    }

    private function filterWhereClause(QueryBuilder $statement, \stdClass $search)
    {
        if (isset($search->string) and Validate::not_null($search->string)) {
            $statement->andWhere('pf.id LIKE :searchTerm ');
            $statement->setParameter('searchTerm', '%' . trim($search->string) . '%');
        }

        if (isset($search->user) and $search->user != "") {
            $statement->andWhere('pf.user = :user');
            $statement->setParameter('user', $search->user);
        }

        if (isset($search->product) and $search->product != "") {
            $statement->andWhere('pf.product = :product');
            $statement->setParameter('product', $search->product);
        }
    }

    private function filterOrder(QueryBuilder $statement, \stdClass $search)
    {
        $sortSQL = [
            'pf.id',
            'pf.status',
            'pf.created',
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
        $statement->select("COUNT(DISTINCT pf.id)");
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
        $statement->groupBy('pf.id');
        $this->filterOrder($statement, $search);

        if ($isPagination) {
            return $this->paginator->paginate($statement->getQuery(), $request->query->getInt('page', 1), $pageLimit);
        }

        if ($pageLimit !== null) {
            $statement->setMaxResults($pageLimit);
        }

        return $statement->getQuery()->execute();
    }
}
