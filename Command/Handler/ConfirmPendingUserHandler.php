<?php

declare(strict_types=1);

namespace MsgPhp\User\Command\Handler;

use MsgPhp\Domain\{CommandBusInterface, EventBusInterface};
use MsgPhp\User\Command\{ConfirmPendingUserCommand, CreateUserCommand};
use MsgPhp\User\Event\PendingUserConfirmedEvent;
use MsgPhp\User\Repository\PendingUserRepositoryInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class ConfirmPendingUserHandler
{
    private $repository;
    private $commandBus;
    private $eventBus;

    public function __construct(PendingUserRepositoryInterface $repository, CommandBusInterface $commandBus, EventBusInterface $eventBus = null)
    {
        $this->repository = $repository;
        $this->commandBus = $commandBus;
        $this->eventBus = $eventBus;
    }

    public function handle(ConfirmPendingUserCommand $command): void
    {
        $pendingUser = $this->repository->findByToken($command->token);

        $this->commandBus->handle(new CreateUserCommand($command->userId, $pendingUser->getEmail(), $pendingUser->getPassword(), $command->enableUser, false));
        $this->repository->delete($pendingUser);

        if (null !== $this->eventBus) {
            $this->eventBus->handle(new PendingUserConfirmedEvent($pendingUser, $command->userId));
        }
    }
}
