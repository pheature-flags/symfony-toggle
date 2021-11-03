<?php

declare(strict_types=1);

namespace Pheature\Community\Symfony\DependencyInjection;

use Pheature\Core\Toggle\Read\ChainToggleStrategyFactory;
use Pheature\Core\Toggle\Read\SegmentFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class ToggleStrategyFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var array<array<mixed>> $pheatureFlagsConfig */
        $pheatureFlagsConfig = $container->getExtensionConfig('pheature_flags');
        $mergedConfig = array_merge(...$pheatureFlagsConfig);

        $toggleStrategyFactory = $container->register(
            ChainToggleStrategyFactory::class,
            ChainToggleStrategyFactory::class
        )
            ->setAutowired(false)
            ->setLazy(true)
            ->addArgument(new Reference(SegmentFactory::class));

        Assert::keyExists($mergedConfig, 'strategy_types');
        Assert::isArray($mergedConfig['strategy_types']);

        /** @var array<string, string> $strategyDefinition */
        foreach ($mergedConfig['strategy_types'] as $strategyDefinition) {
            Assert::keyExists($strategyDefinition, 'type');
            Assert::string($strategyDefinition['type']);
            Assert::keyExists($strategyDefinition, 'factory_id');
            Assert::string($strategyDefinition['factory_id']);
            $container->register($strategyDefinition['type'], $strategyDefinition['factory_id'])
                ->setAutowired(false)
                ->setLazy(true);

            $toggleStrategyFactory->addArgument(new Reference($strategyDefinition['type']));
        }
    }
}
