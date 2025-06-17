<?php declare(strict_types=1);

namespace Shopware\Core\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\App\Privileges\Privileges;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Service\ServiceRegistryClient\SaveConsentRequest;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @internal
 */
#[Package('framework')]
class Manager
{
    public function __construct(
        private readonly Privileges $privileges,
        private readonly Connection $connection,
        private readonly SystemConfigService $systemConfigService,
        private readonly ServiceRegistryClient $serviceRegistryClient,
    ) {
    }

    public function enable(PermissionsConsent $consent, Context $context): void
    {
        $this->privileges->acceptAllForApps($this->getAllServices(), $context);
        $this->serviceRegistryClient->saveConsent(
            new SaveConsentRequest(
                identifier: $consent->identifier,
                shopUserId: $context->getSource()->getUserId(),
                shopIdentifier: $this->systemConfigService->getString('core.app.shopId'),
                consentDate: $consent->grantedAt->format('Y-m-d H:i:s'),
                consentRevision: $consent->revision,
            )
        );
    }

    public function disable(PermissionsConsent $consent, Context $context): void
    {
        $this->privileges->revokeAllForApps($this->getAllServices(), $context);

        $this->serviceRegistryClient->revokeConsent($consent->identifier);
    }

    /**
     * @return list<string>
     */
    private function getAllServices(): array
    {
        return $this->connection->fetchFirstColumn(
            'SELECT LOWER(HEX(id)) FROM app WHERE self_managed = 1'
        );
    }
}
