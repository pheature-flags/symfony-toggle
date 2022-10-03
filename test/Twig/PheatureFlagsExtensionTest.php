<?php

declare(strict_types=1);

namespace Pheature\Test\Community\Symfony\DependencyInjection;

use Pheature\Community\Symfony\Twig\PheatureFlagsExtension;
use Pheature\Community\Symfony\Twig\PheatureFlagsRuntime;
use Pheature\Core\Toggle\Read\ConsumerIdentity;
use Pheature\Core\Toggle\Read\Feature;
use Pheature\Core\Toggle\Read\FeatureFinder;
use PHPUnit\Framework\TestCase;
use Pheature\Core\Toggle\Read\Toggle;
use Pheature\Core\Toggle\Read\ToggleStrategies;
use Pheature\Core\Toggle\Read\ToggleStrategy;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

final class PheatureFlagsExtensionTest extends TestCase
{
    public function testItShouldExposeTwoTests(): void
    {
        $extension = new PheatureFlagsExtension(new Toggle($this->createStub(FeatureFinder::class)));
        $this->assertCount(2, $extension->getTests());
    }

    public function testItShouldExposeOneFunction(): void
    {
        $extension = new PheatureFlagsExtension(new Toggle($this->createStub(FeatureFinder::class)));
        $this->assertCount(1, $extension->getFunctions());
    }

    /**
     * @dataProvider getTemplates
     */
    public function testRendering($template, $variables)
    {
        $loader = new ArrayLoader(['index' => $template]);
        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension(new PheatureFlagsExtension());
        $toggle = $this->createAllFeaturesEnabledToggle();
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([PheatureFlagsRuntime::class => fn() => new PheatureFlagsRuntime($toggle)]));

        $this->assertSame('yes', $twig->load('index')->render($variables));
    }

    public function getTemplates()
    {
        return [
            ['{% if "foo" is enabled %}yes{% endif %}', []],
            ['{% if "foo" is enabled_for(i) %}yes{% endif %}', ['i' => $this->createStub(ConsumerIdentity::class)]],
            ['{% if is_feature_enabled("foo") %}yes{% endif %}', []],
        ];
    }

    private function createAllFeaturesEnabledToggle(): Toggle
    {
        $strategyStub = $this->createStub(ToggleStrategy::class);
        $strategyStub
            ->method('isSatisfiedBy')
            ->willReturn(true)
        ;

        $featureStub = $this->createStub(Feature::class);
        $featureStub
            ->method('isEnabled')
            ->willReturn(true)
        ;
        $featureStub
            ->method('strategies')
            ->willReturn(new ToggleStrategies($strategyStub))
        ;

        $featureFinderStub = $this->createStub(FeatureFinder::class);
        $featureFinderStub
            ->method('get')
            ->willReturn($featureStub)
        ;

        return new Toggle($featureFinderStub);
    }
}
