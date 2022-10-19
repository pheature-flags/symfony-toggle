<?php

declare(strict_types=1);

namespace Pheature\Test\Community\Symfony;

use Pheature\Community\Symfony\DependencyInjection\PheatureFlagsExtension;
use Pheature\Community\Symfony\PheatureFlagsBundle;
use Pheature\Crud\Psr11\Toggle\ToggleConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PheatureFlagsBundleTest extends TestCase
{
    private ContainerBuilder $containerBuilder;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder();
        $extension = new PheatureFlagsExtension();
        $this->containerBuilder->registerExtension($extension);
        $bundle = new PheatureFlagsBundle();

        $bundle->build($this->containerBuilder);
    }

    public function testItShouldLoadAllContainerCompilerPasses(): void
    {
        $this->containerBuilder->compile();

        $removedServices = $this->containerBuilder->getRemovedIds();
        $this->assertArrayHasKey("Pheature\Crud\Psr11\Toggle\ToggleConfig", $removedServices);
        $this->assertArrayHasKey("Pheature\Core\Toggle\Read\Toggle", $removedServices);
        $this->assertArrayHasKey("Pheature\Core\Toggle\Read\SegmentFactory", $removedServices);
        $this->assertArrayHasKey("identity_segment", $removedServices);
        $this->assertArrayHasKey("strict_matching_segment", $removedServices);
        $this->assertArrayHasKey("in_collection_matching_segment", $removedServices);
        $this->assertArrayHasKey("Pheature\Core\Toggle\Read\ChainToggleStrategyFactory", $removedServices);
        $this->assertArrayHasKey("enable_by_matching_segment", $removedServices);
        $this->assertArrayHasKey("enable_by_matching_identity_id", $removedServices);
        $this->assertArrayHasKey("Pheature\Core\Toggle\Write\FeatureRepository", $removedServices);
        $this->assertArrayHasKey("Pheature\Core\Toggle\Read\FeatureFinder", $removedServices);
        $this->assertArrayHasKey("Pheature\InMemory\Toggle\InMemoryFeatureFactory", $removedServices);
    }

    public function testItShouldLoadDefaultSegmentTypesConfig(): void
    {
        $service = $this->containerBuilder->setAlias(
            'config',
            ToggleConfig::class
        );
        $service->setPublic(true);

        $this->containerBuilder->compile();
        /** @var ToggleConfig $config */
        $config = $this->containerBuilder->get('config');

        $segmentTypes = $config->segmentTypes();
        $this->assertCount(3, $segmentTypes);
        $this->assertSame([
            [
                "type" => "identity_segment",
                "factory_id" => "Pheature\Model\Toggle\SegmentFactory"
            ],
            [
                "type" => "strict_matching_segment",
                "factory_id" => "Pheature\Model\Toggle\SegmentFactory"
            ],
            [
                "type" => "in_collection_matching_segment",
                "factory_id" => "Pheature\Model\Toggle\SegmentFactory"
            ]
        ], $segmentTypes);
    }

    public function testItShouldLoadDefaultStrategyTypesConfig(): void
    {
        $service = $this->containerBuilder->setAlias(
            'config',
            ToggleConfig::class
        );
        $service->setPublic(true);

        $this->containerBuilder->compile();
        /** @var ToggleConfig $config */
        $config = $this->containerBuilder->get('config');

        $strategyTypes = $config->strategyTypes();
        $this->assertCount(2, $strategyTypes);
        $this->assertSame([
            [
                "type" => "enable_by_matching_segment",
                "factory_id" => "Pheature\Model\Toggle\StrategyFactory"
            ],
            [
                "type" => "enable_by_matching_identity_id",
                "factory_id" => "Pheature\Model\Toggle\StrategyFactory"
            ]
        ], $strategyTypes);
    }

    public function testItShouldLoadDefaultConfig(): void
    {
        $service = $this->containerBuilder->setAlias(
            'config',
            ToggleConfig::class
        );
        $service->setPublic(true);

        $this->containerBuilder->compile();
        /** @var ToggleConfig $config */
        $config = $this->containerBuilder->get('config');

        $this->assertSame('inmemory', $config->driver());
        $this->assertSame([], $config->driverOptions());
        $this->assertSame(false, $config->apiEnabled());
        $this->assertSame('', $config->apiPrefix());
        $this->assertSame([], $config->toggles());
    }
}
