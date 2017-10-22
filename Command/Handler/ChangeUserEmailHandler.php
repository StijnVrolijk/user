<?php

declare(strict_types=1);

namespace MsgPhp\User\Command\Handler;

use MsgPhp\Domain\EventBusInterface;
use MsgPhp\User\Command\ChangeUserEmailCommand;
use MsgPhp\User\Event\UserEmailChangedEvent;
use MsgPhp\User\Repository\UserRepositoryInterface;
use MsgPhp\User\Repository\UserSecondaryEmailRepositoryInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class ChangeUserEmailHandler
{
    private $repository;
    private $eventBus;

    public function __construct(UserRepositoryInterface $repository, EventBusInterface $eventBus)
    {
        $this->repository = $repository;
        $this->eventBus = $eventBus;
    }

    public function handle(ChangeUserEmailCommand $command): void
    {
        $user = $this->repository->find($command->userId);

        if ($command->email === $oldEmail = $user->getEmail()) {
            return;
        }

        $user->changeEmail($command->email);

        $this->repository->save($user);
        $this->eventBus->handle(new UserEmailChangedEvent($user, $oldEmail));
    }
}
