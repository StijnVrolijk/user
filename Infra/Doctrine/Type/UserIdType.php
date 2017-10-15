<?php

declare(strict_types=1);

namespace MsgPhp\User\Infra\Doctrine\Type;

use MsgPhp\Domain\Infra\Doctrine\DomainUuidType;
use MsgPhp\Domain\Infra\Uuid\DomainId;
use MsgPhp\User\Infra\Uuid\UserId;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class UserIdType extends DomainUuidType
{
    protected function convertToDomainId(string $value): DomainId
    {
        return new UserId($value);
    }
}
