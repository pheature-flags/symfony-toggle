<?php

declare(strict_types=1);

namespace Pheature\Community\Symfony\DependencyInjection;

use Doctrine\DBAL\Connection;
use Pheature\Core\Toggle\Read\ChainToggleStrategyFactory;
use Pheature\Core\Toggle\Read\FeatureFinder;
use Pheature\Core\Toggle\Read\Toggle;
use Pheature\Crud\Psr11\Toggle\FeatureFinderFactory;
use Pheature\Crud\Psr11\Toggle\ToggleConfig;
use Pheature\InMemory\Toggle\InMemoryFeatureFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class PheatureFlagsExtension extends ConfigurableExtension
{
    /**
     * @param array<mixed> $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $container->register(ToggleConfig::class, ToggleConfig::class)
            ->setAutowired(false)
            ->setLazy(false)
            ->addArgument($mergedConfig);

        $container->register(Toggle::class, Toggle::class)
            ->setAutowired(false)
            ->setLazy(false)
            ->addArgument(
                new Reference(FeatureFinder::class)
            )
        ;
    }
}
