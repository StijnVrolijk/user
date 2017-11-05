<?php

declare(strict_types=1);

namespace MsgPhp\User\Infra\Doctrine\Repository;

use MsgPhp\Domain\Entity\EntityCollectionInterface;
use MsgPhp\Domain\Infra\Doctrine\DomainEntityRepositoryTrait;
use MsgPhp\Eav\{AttributeIdInterface, AttributeValueIdInterface};
use MsgPhp\User\Entity\UserAttributeValue;
use MsgPhp\User\Repository\UserAttributeValueRepositoryInterface;
use MsgPhp\User\UserIdInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class UserAttributeValueRepository implements UserAttributeValueRepositoryInterface
{
    use DomainEntityRepositoryTrait;

    private $alias = 'user_attribute_value';
    private $idFields = ['user', 'attributeValue'];

    /**
     * @return EntityCollectionInterface|UserAttributeValue[]
     */
    public function findAllByAttributeId(AttributeIdInterface $attributeId, int $offset = null, int $limit = null): EntityCollectionInterface
    {
        $qb = $this->createQueryBuilder($offset, $limit);
        $this->addAttributeCriteria($qb, $attributeId);

        return $this->createResultSet($qb->getQuery());
    }

    /**
     * @return EntityCollectionInterface|UserAttributeValue[]
     */
    public function findAllByAttributeIdAndValue(AttributeIdInterface $attributeId, $value, int $offset = null, int $limit = null): EntityCollectionInterface
    {
        $qb = $this->createQueryBuilder($offset, $limit);
        $this->addAttributeCriteria($qb, $attributeId, $value);

        return $this->createResultSet($qb->getQuery());
    }

    /**
     * @return EntityCollectionInterface|UserAttributeValue[]
     */
    public function findAllByUserId(UserIdInterface $userId, int $offset = null, int $limit = null): EntityCollectionInterface
    {
        $qb = $this->createQueryBuilder($offset, $limit);
        $this->addFieldCriteria($qb, ['user' => $userId]);

        return $this->createResultSet($qb->getQuery());
    }

    /**
     * @return EntityCollectionInterface|UserAttributeValue[]
     */
    public function findAllByUserIdAndAttributeId(UserIdInterface $userId, AttributeIdInterface $attributeId, int $offset = null, int $limit = null): EntityCollectionInterface
    {
        $qb = $this->createQueryBuilder($offset, $limit);
        $this->addFieldCriteria($qb, ['user' => $userId]);
        $this->addAttributeCriteria($qb, $attributeId);

        return $this->createResultSet($qb->getQuery());
    }

    public function find(UserIdInterface $userId, AttributeValueIdInterface $attributeValueId): UserAttributeValue
    {
        return $this->doFind(...func_get_args());
    }

    public function exists(UserIdInterface $userId, AttributeValueIdInterface $attributeValueId): bool
    {
        return $this->doExists(...func_get_args());
    }

    /**
     * @internal
     */
    public function save(UserAttributeValue $userAttributeValue): void
    {
        $this->doSave($userAttributeValue);
    }

    /**
     * @internal
     */
    public function delete(UserAttributeValue $userAttributeValue): void
    {
        $this->doDelete($userAttributeValue);
    }

    private function addAttributeCriteria(QueryBuilder $qb, AttributeIdInterface $attributeId, $value = null): void
    {
        if (3 === func_num_args()) {
            $qb->join($this->alias.'.attributeValue', 'attribute_value', Join::WITH, 'attribute_value.checksum = :attribute_value');
            $qb->setParameter('attribute_value', md5(serialize($value)));
        } else {
            $qb->join($this->alias.'.attributeValue', 'attribute_value');
        }

        $qb->join('attribute_value.attribute', 'attribute', Join::WITH, 'attribute.id = :attribute');
        $qb->setParameter('attribute', $attributeId);
    }
}
