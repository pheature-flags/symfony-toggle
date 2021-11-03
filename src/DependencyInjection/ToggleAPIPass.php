<?php

declare(strict_types=1);

namespace Pheature\Community\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webmozart\Assert\Assert;

final class ToggleAPIPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var array<array<mixed>> $pheatureFlagsConfig */
        $pheatureFlagsConfig = $container->getExtensionConfig('pheature_flags');

        $mergedConfig = array_merge(...$pheatureFlagsConfig);
        Assert::keyExists($mergedConfig, 'api_enabled');
        Assert::boolean($mergedConfig['api_enabled']);

        if (false === $mergedConfig['api_enabled']) {
            return;
        }

        Assert::keyExists($mergedConfig, 'api_prefix');
        Assert::string($mergedConfig['api_prefix']);
        $container->getParameterBag()->set('pheature_flags_prefix', $mergedConfig['api_prefix']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config/toggle_api')
        );
        $loader->load('services/controller_services.yaml');
        $loader->load('services/handler_services.yaml');
    }
}
