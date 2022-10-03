<?php

declare(strict_types=1);

namespace Pheature\Community\Symfony\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

final class PheatureFlagsExtension extends AbstractExtension
{
    /**
     * @return TwigTest[]
     */
    public function getTests(): array
    {
        return [
            new TwigTest('enabled', [PheatureFlagsRuntime::class, 'isEnabled']),
            new TwigTest('enabled_for', [PheatureFlagsRuntime::class, 'isEnabled'], ['one_mandatory_argument' => true]),
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_feature_enabled', [PheatureFlagsRuntime::class, 'isFeatureEnabled']),
        ];
    }
}
