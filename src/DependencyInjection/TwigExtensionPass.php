<?php

declare(strict_types=1);

namespace Pheature\Community\Symfony\DependencyInjection;

use Pheature\Community\Symfony\Twig\PheatureFlagsExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TwigExtensionPass implements CompilerPassInterface
{
    /** @psalm-suppress ReservedWord */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $container->register(PheatureFlagsExtension::class, PheatureFlagsExtension::class);

        $container->findDefinition('twig')
            ->addMethodCall('addExtension', [new Reference(PheatureFlagsExtension::class)])
        ;
    }
}
