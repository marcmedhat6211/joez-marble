<?php

namespace App\ECommerceBundle\Repository;

use App\ECommerceBundle\Entity\ProductMaterialImage;
use App\ServiceBundle\Utils\Validate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method ProductMaterialImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductMaterialImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductMaterialImage[]    findAll()
 * @method ProductMaterialImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductMaterialImageRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, ProductMaterialImage::class);
        $this->paginator = $paginator;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ProductMaterialImage $entity, bool $flush = true): void
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
    public function remove(ProductMaterialImage $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    private function getStatement()
    {
        return $this->createQueryBuilder('pmi');
    }

    private function filterWhereClause(QueryBuilder $statement, \stdClass $search)
    {
        if (isset($search->string) and Validate::not_null($search->string)) {
            $statement->andWhere('pmi.id LIKE :searchTerm '
                . 'OR pmi.title LIKE :searchTerm '
            );
            $statement->setParameter('searchTerm', '%' . trim($search->string) . '%');
        }

        if (isset($search->material) and $search->material > 0) {
            $statement->andWhere('pmi.material = :material');
            $statement->setParameter('material', $search->material);
        }

        if (isset($search->product) and $search->product > 0) {
            $statement->andWhere('pmi.product = :product');
            $statement->setParameter('product', $search->product);
        }

        if (isset($search->image) and $search->image > 0) {
            $statement->andWhere('pmi.image = :image');
            $statement->setParameter('image', $search->image);
        }
    }

    private function filterOrder(QueryBuilder $statement, \stdClass $search)
    {
        $sortSQL = [
            'pmi.id',
            'pmi.product',
            'pmi.material',
            'pmi.image',
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
        $statement->select("COUNT(DISTINCT pmi.id)");
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
        $statement->groupBy('pmi.id');
        $this->filterOrder($statement, $search);

        if ($isPagination) {
            return $this->paginator->paginate($statement->getQuery(), $request->query->getInt('page', 1), $pageLimit);
        }

        return $statement->getQuery()->execute();
    }
}
