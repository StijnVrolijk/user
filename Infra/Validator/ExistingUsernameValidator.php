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

namespace MsgPhp\User\Infra\Validator;

use MsgPhp\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class ExistingUsernameValidator extends ConstraintValidator
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingUsername) {
            throw new UnexpectedTypeException($constraint, ExistingUsername::class);
        }

        if (null === $value || '' === ($value = (string) $value)) {
            return;
        }

        if (!$this->repository->usernameExists($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->setInvalidValue($value)
                ->setCode(ExistingUsername::DOES_NOT_EXIST_ERROR)
                ->addViolation()
            ;
        }
    }
}
