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

namespace MsgPhp\User\Command\Handler;

use MsgPhp\Domain\Exception\EntityNotFoundException;
use MsgPhp\Domain\Factory\DomainObjectFactoryInterface;
use MsgPhp\Domain\Message\{DomainMessageBusInterface, MessageDispatchingTrait};
use MsgPhp\User\Command\DeleteUserRoleCommand;
use MsgPhp\User\Event\UserRoleDeletedEvent;
use MsgPhp\User\Repository\UserRoleRepositoryInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class DeleteUserRoleHandler
{
    use MessageDispatchingTrait;

    /**
     * @var UserRoleRepositoryInterface
     */
    private $repository;

    public function __construct(DomainObjectFactoryInterface $factory, DomainMessageBusInterface $bus, UserRoleRepositoryInterface $repository)
    {
        $this->factory = $factory;
        $this->bus = $bus;
        $this->repository = $repository;
    }

    public function __invoke(DeleteUserRoleCommand $command): void
    {
        try {
            $userRole = $this->repository->find($command->userId, $command->roleName);
        } catch (EntityNotFoundException $e) {
            return;
        }

        $this->repository->delete($userRole);
        $this->dispatch(UserRoleDeletedEvent::class, compact('userRole'));
    }
}
