<?php

declare(strict_types=1);

namespace Pheature\Test\Community\Symfony\DependencyInjection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Pheature\Community\Symfony\DependencyInjection\FeatureFinderFactoryPass;
use Pheature\Community\Symfony\DependencyInjection\SegmentFactoryPass;
use Pheature\Community\Symfony\DependencyInjection\ToggleStrategyFactoryPass;
use Pheature\Core\Toggle\Read\FeatureFinder;
use Pheature\Crud\Psr11\Toggle\ToggleConfig;
use PHPUnit\Framework\TestCase;

final class FeatureFinderFactoryPassTest extends TestCase
{
    public function testItShouldRegisterChainFeatureFinderInContainer(): void
    {
        touch('test.sqlite');
        $compilerPass = new FeatureFinderFactoryPass();
        $container = TestContainerFactory::create($compilerPass, 'chain');
        $segmentFactoryPass = new SegmentFactoryPass();
        $segmentFactoryPass->process($container);
        $strategyFactoryPass = new ToggleStrategyFactoryPass();
        $strategyFactoryPass->process($container);
        $container->register(ToggleConfig::class, ToggleConfig::class)->addArgument([
            'driver' => 'chain',
            'driver_options' => ['inmemory', 'dbal'],
            'api_enabled' => false,
            'api_prefix' => '',
        ]);
        $container->register(Connection::class, Connection::class)
            ->setFactory([DriverManager::class, 'getConnection'])
            ->addArgument(['url' => 'sqlite:///test.sqlite']);

        $featureFinderFactoryDefinition = $container->getDefinition(FeatureFinder::class);
        self::assertFalse($featureFinderFactoryDefinition->isAutowired());
        self::assertTrue($featureFinderFactoryDefinition->isLazy());

        $featureFinderFactory = $container->get(FeatureFinder::class);
        self::assertInstanceOf(FeatureFinder::class, $featureFinderFactory);
        unlink('test.sqlite');
    }

    public function testItShouldRegisterChainWithoutDbalFeatureFinderInContainer(): void
    {
        $compilerPass = new FeatureFinderFactoryPass();
        $container = TestContainerFactory::create($compilerPass, 'chain', [
            'driver' => 'chain',
            'driver_options' => ['inmemory'],
            'segment_types' => [],
            'strategy_types' => [],
        ]);
        $segmentFactoryPass = new SegmentFactoryPass();
        $segmentFactoryPass->process($container);
        $strategyFactoryPass = new ToggleStrategyFactoryPass();
        $strategyFactoryPass->process($container);
        $container->register(ToggleConfig::class, ToggleConfig::class)->addArgument([
            'driver' => 'chain',
            'driver_options' => ['inmemory'],
            'api_enabled' => false,
            'api_prefix' => '',
        ]);

        $featureFinderFactoryDefinition = $container->getDefinition(FeatureFinder::class);
        self::assertFalse($featureFinderFactoryDefinition->isAutowired());
        self::assertTrue($featureFinderFactoryDefinition->isLazy());

        $featureFinderFactory = $container->get(FeatureFinder::class);
        self::assertInstanceOf(FeatureFinder::class, $featureFinderFactory);
    }
}
