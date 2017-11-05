<?php

declare(strict_types=1);

namespace MsgPhp\User\Entity;

use MsgPhp\Domain\Entity\CreatedAtFieldTrait;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
class UserSecondaryEmail
{
    use CreatedAtFieldTrait;
    use UserFieldTrait;

    private $user;
    private $email;
    private $token;
    private $pendingPrimary = false;
    private $confirmedAt;

    /**
     * @internal
     */
    public function __construct(User $user, string $email)
    {
        if ($email === $user->getEmail()) {
            throw new \LogicException('Secondary e-mail cannot be the same as the current primary e-mail.');
        }

        $this->user = $user;
        $this->email = $email;
        $this->token = bin2hex(random_bytes(32));
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function isPendingPrimary(): bool
    {
        return $this->pendingPrimary;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    /**
     * @internal
     */
    public function confirm(): void
    {
        $this->token = null;
        $this->confirmedAt = new \DateTimeImmutable();
    }

    /**
     * @internal
     */
    public function markPendingPrimary(bool $flag = true): void
    {
        if ($flag && $this->confirmedAt) {
            throw new \LogicException('Cannot mark user secondary e-mail a pending primary as it\'s already confirmed.');
        }

        $this->pendingPrimary = $flag;
    }
}
