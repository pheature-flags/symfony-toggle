<?php

declare(strict_types=1);

namespace Pheature\Community\Symfony\DependencyInjection;

use Pheature\Core\Toggle\Read\ChainSegmentFactory;
use Pheature\Core\Toggle\Read\SegmentFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class SegmentFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var array<array<mixed>> $pheatureFlagsConfig */
        $pheatureFlagsConfig = $container->getExtensionConfig('pheature_flags');
        $mergedConfig = array_merge(...$pheatureFlagsConfig);

        $segmentFactory = $container->register(SegmentFactory::class, SegmentFactory::class)
            ->setAutowired(false)
            ->setLazy(false)
            ->setClass(ChainSegmentFactory::class);

        Assert::keyExists($mergedConfig, 'segment_types');
        Assert::isArray($mergedConfig['segment_types']);

        foreach ($mergedConfig['segment_types'] as $segmentDefinition) {
            Assert::keyExists($segmentDefinition, 'type');
            Assert::string($segmentDefinition['type']);
            Assert::keyExists($segmentDefinition, 'factory_id');
            Assert::string($segmentDefinition['factory_id']);
            if (false === $container->has($segmentDefinition['type'])) {
                $container->register($segmentDefinition['type'], $segmentDefinition['factory_id'])
                    ->setAutowired(false)
                    ->setLazy(false);
            }

            $segmentFactory->addArgument(new Reference($segmentDefinition['type']));
        }
    }
}
