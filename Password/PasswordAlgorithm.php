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

namespace MsgPhp\User\Password;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class PasswordAlgorithm
{
    public const DEFAULT_LEGACY = 'sha512';

    /**
     * @var int|string
     */
    public $type;

    /**
     * @var array|null
     */
    public $options;

    /**
     * @var bool
     */
    public $legacy;

    /**
     * @var bool|null
     */
    public $encodeBase64;

    /**
     * @var PasswordSalt|null
     */
    public $salt;

    /**
     * @see https://secure.php.net/manual/en/function.password-hash.php
     */
    public static function create(int $type = \PASSWORD_DEFAULT, array $options = []): self
    {
        $instance = new self($type);
        $instance->options = $options;

        return $instance;
    }

    /**
     * @see https://secure.php.net/manual/en/function.hash.php
     */
    public static function createLegacy(string $type = self::DEFAULT_LEGACY): self
    {
        $instance = new self($type, true);
        $instance->encodeBase64 = false;

        return $instance;
    }

    public static function createLegacyBase64Encoded(string $type = self::DEFAULT_LEGACY): self
    {
        $instance = self::createLegacy($type);
        $instance->encodeBase64 = true;

        return $instance;
    }

    public static function createLegacySalted(PasswordSalt $salt, bool $encodeBase64 = true, string $type = self::DEFAULT_LEGACY): self
    {
        $instance = $encodeBase64 ? self::createLegacyBase64Encoded($type) : self::createLegacy($type);
        $instance->salt = $salt;

        return $instance;
    }

    /**
     * @param int|string $type
     */
    private function __construct($type, bool $legacy = false)
    {
        $this->type = $type;
        $this->legacy = $legacy;
    }
}
