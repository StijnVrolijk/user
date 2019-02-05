<?php

declare(strict_types=1);

/*
 * This file is part of the MsgPHP package.
 *
 * (c) Roland Franssen <franssen.roland@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MsgPhp\User\Infra\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use MsgPhp\Domain\DomainCollectionInterface;
use MsgPhp\Domain\Exception\EntityNotFoundException;
use MsgPhp\Domain\Infra\Doctrine\DomainEntityRepositoryTrait;
use MsgPhp\User\Entity\User;
use MsgPhp\User\Repository\UserRepositoryInterface;
use MsgPhp\User\UserIdInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class UserRepository implements UserRepositoryInterface
{
    use DomainEntityRepositoryTrait;

    /**
     * @var string|null
     */
    private $usernameField;

    /**
     * @var string|null
     */
    private $usernameClass;

    /**
     * @psalm-param class-string $class
     */
    public function __construct(string $class, EntityManagerInterface $em, ?string $usernameField, ?string $usernameClass)
    {
        $this->class = $class;
        $this->em = $em;
        $this->usernameField = $usernameField;
        $this->usernameClass = $usernameClass;
    }

    /**
     * @return DomainCollectionInterface|User[]
     */
    public function findAll(int $offset = 0, int $limit = 0): DomainCollectionInterface
    {
        return $this->doFindAll($offset, $limit);
    }

    public function find(UserIdInterface $id): User
    {
        return $this->doFind($id);
    }

    public function findByUsername(string $username): User
    {
        $qb = $this->createQueryBuilderForUsername($username);

        if (null !== $qb) {
            try {
                return $qb->getQuery()->getSingleResult();
            } catch (NoResultException $e) {
                throw EntityNotFoundException::createForFields($this->class, ['username' => $username]);
            }
        }

        return $this->doFindByFields($this->getUsernameCriteria($username));
    }

    public function exists(UserIdInterface $id): bool
    {
        return $this->doExists($id);
    }

    public function usernameExists(string $username): bool
    {
        $qb = $this->createQueryBuilderForUsername($username);

        if (null !== $qb) {
            $qb->select('1');

            return (bool) $qb->getQuery()->getScalarResult();
        }

        return $this->doExistsByFields($this->getUsernameCriteria($username));
    }

    public function save(User $user): void
    {
        $this->doSave($user);
    }

    public function delete(User $user): void
    {
        $this->doDelete($user);
    }

    private function getUsernameCriteria(string $username): array
    {
        if (null === $this->usernameField) {
            throw new \LogicException(sprintf('No username field available for entity "%s".', $this->class));
        }

        return [$this->usernameField => $username];
    }

    private function createQueryBuilderForUsername(string $username): ?QueryBuilder
    {
        if (null === $this->usernameClass) {
            return null;
        }

        $qb = $this->createQueryBuilder();
        $qb->join($this->usernameClass, 'username', Join::WITH, 'username.user = '.$this->getAlias());
        $qb->where($qb->expr()->eq('username.username', ':username'));
        $qb->setMaxResults(1);
        $qb->setParameter('username', $username);

        return $qb;
    }
}
