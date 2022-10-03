<?php

declare(strict_types=1);

namespace Pheature\Test\Community\Symfony\DependencyInjection;

use Pheature\Community\Symfony\Twig\PheatureFlagsRuntime;
use Pheature\Core\Toggle\Read\ConsumerIdentity;
use Pheature\Core\Toggle\Read\Feature;
use Pheature\Core\Toggle\Read\FeatureFinder;
use PHPUnit\Framework\TestCase;
use Pheature\Core\Toggle\Read\Toggle;
use Pheature\Core\Toggle\Read\ToggleStrategies;
use Pheature\Core\Toggle\Read\ToggleStrategy;

final class PheatureFlagsRuntimeTest extends TestCase
{
    public function testIsEnabledMethodDelegatesToToggle(): void
    {
        $featureMock = $this->createMock(Feature::class);
        $featureMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false)
        ;

        $featureFinderMock = $this->createMock(FeatureFinder::class);
        $featureFinderMock
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('foobar'))
            ->willReturn($featureMock)
        ;

        $extension = new PheatureFlagsRuntime(new Toggle($featureFinderMock));
        self::assertFalse($extension->isEnabled('foobar'));
    }

    public function testIsFeatureEnabledMethodDelegatesToToggle(): void
    {
        $featureMock = $this->createMock(Feature::class);
        $featureMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false)
        ;

        $featureFinderMock = $this->createMock(FeatureFinder::class);
        $featureFinderMock
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('foobar'))
            ->willReturn($featureMock)
        ;

        $extension = new PheatureFlagsRuntime(new Toggle($featureFinderMock));
        self::assertFalse($extension->isFeatureEnabled('foobar'));
    }

    public function testIsFeatureEnabledMethodPassesAConsumerIdentityToStrategies(): void
    {
        $strategyMock = $this->createMock(ToggleStrategy::class);
        $strategyMock
            ->expects($this->once())
            ->method('isSatisfiedBy')
            ->with($this->callback(fn(ConsumerIdentity $identity) => $identity->id() === 'foo' && $identity->payload() === ['foo' => 'bar', 'baz' => 'qux']))
            ->willReturn(true)
        ;

        $featureMock = $this->createMock(Feature::class);
        $featureMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true)
        ;
        $featureMock
            ->expects($this->once())
            ->method('strategies')
            ->willReturn(new ToggleStrategies($strategyMock))
        ;

        $featureFinderMock = $this->createMock(FeatureFinder::class);
        $featureFinderMock
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('foobar'))
            ->willReturn($featureMock)
        ;

        $extension = new PheatureFlagsRuntime(new Toggle($featureFinderMock));
        self::assertTrue($extension->isFeatureEnabled('foobar', 'foo', ['foo' => 'bar', 'baz' => 'qux']));
    }
}
