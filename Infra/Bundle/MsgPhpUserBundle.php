<?php

declare(strict_types=1);

namespace MsgPhp\User\Infra\Bundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class MsgPhpUserBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new Extension();
    }
}
