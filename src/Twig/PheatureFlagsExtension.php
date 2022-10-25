<?php

declare(strict_types=1);

namespace Pheature\Community\Symfony\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

final class PheatureFlagsExtension extends AbstractExtension
{
    private PheatureFlagsRuntime $runtime;

    public function __construct(PheatureFlagsRuntime $runtime)
    {
        $this->runtime = $runtime;
    }

    /**
     * @return TwigTest[]
     */
    public function getTests(): array
    {
        return [
            new TwigTest('enabled', [$this->runtime, 'isEnabled']),
            new TwigTest('enabled_for', [$this->runtime, 'isEnabled'], ['one_mandatory_argument' => true]),
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_feature_enabled', [$this->runtime, 'isFeatureEnabled']),
        ];
    }
}
