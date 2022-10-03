<?php

declare(strict_types=1);

namespace Pheature\Community\Symfony\Twig;

use Pheature\Core\Toggle\Exception\FeatureNotFoundException;
use Pheature\Core\Toggle\Read\ConsumerIdentity;
use Pheature\Core\Toggle\Read\Toggle;
use Pheature\Model\Toggle\Identity;
use Twig\Extension\RuntimeExtensionInterface;

final class PheatureFlagsRuntime implements RuntimeExtensionInterface
{
    private Toggle $toggle;

    public function __construct(Toggle $toggle)
    {
        $this->toggle = $toggle;
    }

    /**
     * @throws FeatureNotFoundException
     */
    public function isEnabled(string $feature, ConsumerIdentity $consumerIdentity = null): bool
    {
        return $this->toggle->isEnabled($feature, $consumerIdentity);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws FeatureNotFoundException
     */
    public function isFeatureEnabled(string $feature, ?string $identity = null, array $payload = []): bool
    {
        $consumerIdentity = is_string($identity) ? new Identity($identity, $payload) : null;

        return $this->isEnabled($feature, $consumerIdentity);
    }
}
