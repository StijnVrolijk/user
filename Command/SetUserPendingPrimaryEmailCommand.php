<?php

declare(strict_types=1);

namespace MsgPhp\User\Command;

use MsgPhp\User\UserIdInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class SetUserPendingPrimaryEmailCommand
{
    /** @var UserIdInterface */
    public $userId;

    /** @var string|null */
    public $email;

    public function __construct(UserIdInterface $userId, ?string $email)
    {
        $this->userId = $userId;
        $this->email = $email;
    }
}
