<?php

namespace App\ECommerceBundle\Repository;

use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Services\ProductService;
use App\ServiceBundle\Utils\Validate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;
    private ProductService $productService;

    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator,
        ProductService $productService
    )
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
        $this->productService = $productService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Product $entity, bool $flush = true): void
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
    public function remove(Product $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    private function getStatement()
    {
        return $this->
        createQueryBuilder('p')
            ->leftJoin("p.subcategory", "sc")
            ->leftJoin("sc.category", "c");
    }

    private function filterWhereClause(QueryBuilder $statement, \stdClass $search)
    {
        if (isset($search->string) and Validate::not_null($search->string)) {
            $statement->andWhere('p.id LIKE :searchTerm '
                . 'OR p.title LIKE :searchTerm '
            );
            $statement->setParameter('searchTerm', '%' . trim($search->string) . '%');
        }

        if (isset($search->price) and Validate::not_null($search->price)) {
            $statement->andWhere('p.price = :price');
            $statement->setParameter('price', $search->price);
        }

        if (isset($search->notId) and Validate::not_null($search->notId)) {
            $statement->andWhere('p.id <> :notId');
            $statement->setParameter('notId', $search->notId);
        }

        if (isset($search->category) and $search->category != "") {
            $statement->andWhere('sc.category = :category');
            $statement->setParameter('category', $search->category);
        }

        if (isset($search->title) and $search->title != "") {
            $statement->andWhere('sc.title = :title');
            $statement->setParameter('title', $search->title);
        }

        if (isset($search->subcategory) and $search->subcategory != "") {
            $statement->andWhere('p.subcategory = :subcategory');
            $statement->setParameter('subcategory', $search->subcategory);
        }

        if (isset($search->subcategories) and $search->subcategories != "") {
            $statement->andWhere('p.subcategory IN (:subcategories)');
            $statement->setParameter('subcategories', $search->subcategories);
        }

        if (isset($search->publish) and $search->publish != "") {
            $statement->andWhere('p.publish = :publish');
            $statement->setParameter('publish', $search->publish);
        }

        if (isset($search->featured) and $search->featured != "") {
            $statement->andWhere('p.featured = :featured');
            $statement->setParameter('featured', $search->featured);
        }

        if (isset($search->newArrival) and $search->newArrival != "") {
            $statement->andWhere('p.newArrival = :newArrival');
            $statement->setParameter('newArrival', $search->newArrival);
        }

        if (isset($search->bestSeller) and $search->bestSeller != "") {
            $statement->andWhere('p.bestSeller = :bestSeller');
            $statement->setParameter('bestSeller', $search->bestSeller);
        }

        if (isset($search->onSale) and $search->onSale != "") {
            $statement->andWhere('p.onSale = :onSale');
            $statement->setParameter('onSale', $search->onSale);
        }

        if (isset($search->living) and $search->living != "") {
            $statement->andWhere('c.living = :living');
            $statement->setParameter('living', $search->living);
        }

        if (isset($search->deleted) and in_array($search->deleted, array(0, 1))) {
            if ($search->deleted == 1) {
                $statement->andWhere('p.deleted IS NOT NULL');
            } else {
                $statement->andWhere('p.deleted IS NULL');
            }
        }
    }

    private function filterOrder(QueryBuilder $statement, \stdClass $search)
    {
        $sortSQL = [
            'p.id',
            'p.sku',
            'p.title',
            'p.price',
            'sc.category',
            'p.subcategory',
            'p.publish',
            'p.featured',
            'p.newArrival',
            'p.created',
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

    private function filterCount(QueryBuilder $statement): ?int
    {
        $statement->select("COUNT(DISTINCT p.id)");
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
        $statement->groupBy('p.id');
        $this->filterOrder($statement, $search);

        if ($isPagination) {
            $paginationResults = $this->paginator->paginate($statement->getQuery(), $request->query->getInt('page', 1), $pageLimit);
            $this->productService->addFavouriteStatusToProductsObjects((array)$paginationResults->getItems());
            return $paginationResults;
        }

        if ($pageLimit !== null) {
            $statement->setMaxResults($pageLimit);
        }

        $rows = $statement->getQuery()->execute();
        $this->productService->addFavouriteStatusToProductsObjects($rows);
        return $rows;
    }
}
