<?php

declare(strict_types=1);

namespace Pheature\Test\Community\Symfony\DependencyInjection;

use Pheature\Community\Symfony\DependencyInjection\TwigExtensionPass;
use Pheature\Community\Symfony\Twig\PheatureFlagsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class TwigExtensionPassTest extends TestCase
{
    public function testItShouldNotRegisterTwigExtensionIfTwigServiceIsNotPresent(): void
    {
        $compilerPass = new TwigExtensionPass();
        $container = $containerBuilder = new ContainerBuilder();
        $container->addCompilerPass($compilerPass);
        $container->compile();

        $removedIds = $container->getRemovedIds();
        $this->assertArrayNotHasKey(PheatureFlagsExtension::class, $removedIds);
    }

    public function testItShouldRegisterTwigExtension(): void
    {
        $compilerPass = new TwigExtensionPass();
        $container = $containerBuilder = new ContainerBuilder();
        $container->register('twig', Environment::class)
            ->addArgument(new Definition(ArrayLoader::class))
        ;
        $container->addCompilerPass($compilerPass);
        $container->compile();

        $removedIds = $container->getRemovedIds();
        $this->assertArrayHasKey(PheatureFlagsExtension::class, $removedIds);
    }
}
