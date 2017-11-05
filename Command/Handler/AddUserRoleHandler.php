<?php

declare(strict_types=1);

namespace MsgPhp\User\Command\Handler;

use MsgPhp\Domain\Entity\EntityFactoryInterface;
use MsgPhp\Domain\EventBusInterface;
use MsgPhp\User\Command\AddUserRoleCommand;
use MsgPhp\User\Entity\UserRole;
use MsgPhp\User\Event\UserRoleAddedEvent;
use MsgPhp\User\Repository\UserRepositoryInterface;
use MsgPhp\User\Repository\UserRoleRepositoryInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class AddUserRoleHandler
{
    private $userRepository;
    private $userRoleRepository;
    private $factory;
    private $eventBus;

    public function __construct(UserRepositoryInterface $userRepository, UserRoleRepositoryInterface $userRoleRepository, EntityFactoryInterface $factory, EventBusInterface $eventBus)
    {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->factory = $factory;
        $this->eventBus = $eventBus;
    }

    public function handle(AddUserRoleCommand $command): void
    {
        $userRole = $this->factory->create(UserRole::class, [
            'user' => $this->userRepository->find($command->userId),
            'role' => $command->role,
        ]);

        $this->userRoleRepository->save($userRole);
        $this->eventBus->handle(new UserRoleAddedEvent($userRole));
    }
}
