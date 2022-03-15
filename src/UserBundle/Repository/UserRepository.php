<?php

namespace App\UserBundle\Repository;

use App\ServiceBundle\Utils\Validate;
use App\UserBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param string $role
     *
     * @return array
     */
    public function findByRole(string $role): array
    {
        $statement = $this->createQueryBuilder("u")
            ->select('u')
            ->where('u.roles LIKE :roles')
            ->andWhere('u.deleted IS NULL')
            ->orderBy('u.id', 'DESC')
            ->setParameter('roles', '%"'.$role.'"%');
        return $statement->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findAllUsers(): array
    {
        $statement = $this->createQueryBuilder("u")
            ->where('u.deleted IS NULL')
            ->orderBy('u.id', 'DESC');
        return $statement->getQuery()->getResult();
    }


    private function getStatement()
    {
        return $this->createQueryBuilder('u');
    }

    private function filterWhereClause(QueryBuilder $statement, \stdClass $search)
    {
        if (isset($search->string) AND Validate::not_null($search->string)) {
            $statement->andWhere('u.id LIKE :searchTerm '
                .'OR u.fullName LIKE :searchTerm '
                .'OR u.email LIKE :searchTerm '
                .'OR u.phone LIKE :searchTerm '
            );
            $statement->setParameter('searchTerm', '%'.trim($search->string).'%');
        }

        if (isset($search->role) AND Validate::not_null($search->role)) {
            if ($search->role == User::ROLE_DEFAULT) {
                $statement->andWhere("u.roles = :role");
                $statement->setParameter("role", "[]");
            } else {
                $roles = (!is_array($search->role)) ? [$search->role] : $search->role;
                $roleClause = null;
                $i = 0;
                foreach ($roles as $value) {
                    if ($i > 0) {
                        $roleClause .= " OR ";
                    }
                    $roleClause .= " u.roles LIKE :role".$i;
                    $statement->setParameter('role'.$i, '%'.trim($value).'%');
                    $i++;
                }
                $statement->andWhere($roleClause);
            }

        }
        if (isset($search->ids) AND is_array($search->ids) and count($search->ids) > 0) {
            $statement->andWhere('u.id in (:ids)');
            $statement->setParameter('ids', $search->ids);
        }

        if (isset($search->enabled) AND (is_bool($search->enabled) or in_array($search->enabled, [0, 1]))) {
            $statement->andWhere('u.enabled = :enabled');
            $statement->setParameter('enabled', $search->enabled);
        }
        if (isset($search->subscriptionNewsletter) AND (is_bool($search->subscriptionNewsletter) or in_array($search->subscriptionNewsletter, [0, 1]))) {
            $statement->andWhere('u.subscriptionNewsletter = :subscriptionNewsletter');
            $statement->setParameter('subscriptionNewsletter', $search->subscriptionNewsletter);
        }

        if (isset($search->deleted) AND in_array($search->deleted, array(0, 1))) {
            if ($search->deleted == 1) {
                $statement->andWhere('u.deleted IS NOT NULL');
            } else {
                $statement->andWhere('u.deleted IS NULL');
            }
        }
    }

    private function filterOrder(QueryBuilder $statement, \stdClass $search)
    {
        $sortSQL = [
            'u.id',
            'u.fullName',
            'u.email',
            'u.phone',
            'u.lastLogin',
            'u.created',
            'u.enabled',
        ];

        if (isset($search->ordr) AND Validate::not_null($search->ordr)) {
            $dir = $search->ordr['dir'];
            $columnNumber = $search->ordr['column'];
            if (isset($columnNumber) AND array_key_exists($columnNumber, $sortSQL)) {
                $statement->orderBy($sortSQL[$columnNumber], $dir);
            }
        } else {
            $statement->orderBy($sortSQL[0], "DESC");
        }
    }

    private function filterPagination(QueryBuilder $statement, $startLimit = null, $endLimit = null)
    {
        if ($startLimit === null OR $endLimit == null) {
            return false;
        }
        $statement->setFirstResult($startLimit)
            ->setMaxResults($endLimit);
    }

    private function filterCount(QueryBuilder $statement)
    {
        $statement->select("COUNT(DISTINCT u.id)");
        $statement->setMaxResults(1);

        $count = $statement->getQuery()->getOneOrNullResult();
        if (is_array($count) and count($count) > 0) {
            return (int)reset($count);
        }

        return 0;
    }

    public function filter($search, $count = false, $startLimit = null, $endLimit = null)
    {
        $statement = $this->getStatement();
        $this->filterWhereClause($statement, $search);

        if ($count == true) {
            return $this->filterCount($statement);
        }
        $statement->groupBy('u.id');
        $this->filterPagination($statement, $startLimit, $endLimit);
        $this->filterOrder($statement, $search);

        return $statement->getQuery()->execute();
    }
}
