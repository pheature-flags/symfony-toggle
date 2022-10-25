<?php

declare(strict_types=1);

namespace Pheature\Test\Community\Symfony\DependencyInjection;

use Pheature\Community\Symfony\DependencyInjection\SegmentFactoryPass;
use Pheature\Core\Toggle\Read\ChainSegmentFactory;
use Pheature\Core\Toggle\Read\SegmentFactory;
use Pheature\Model\Toggle\IdentitySegment;
use Pheature\Model\Toggle\StrictMatchingSegment;
use PHPUnit\Framework\TestCase;

final class SegmentFactoryPassTest extends TestCase
{
    public function testItShouldRegisterToggleStrategyFactoryInContainer(): void
    {
        $compilerPass = new SegmentFactoryPass();
        $container = TestContainerFactory::create($compilerPass);

        $segmentFactoryDefinition = $container->getDefinition(SegmentFactory::class);
        $this->assertFalse($segmentFactoryDefinition->isAutowired());
        $this->assertFalse($segmentFactoryDefinition->isLazy());
        $this->assertCount(2, $segmentFactoryDefinition->getArguments());

        $segment1 = $container->getDefinition(IdentitySegment::NAME);
        $this->assertFalse($segment1->isAutowired());
        $this->assertFalse($segment1->isLazy());
        $segment2 = $container->getDefinition(StrictMatchingSegment::NAME);
        $this->assertFalse($segment2->isAutowired());
        $this->assertFalse($segment2->isLazy());

        $segmentFactory = $container->get(SegmentFactory::class);
        $this->assertInstanceOf(ChainSegmentFactory::class, $segmentFactory);
    }
}
