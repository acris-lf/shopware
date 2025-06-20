<?php declare(strict_types=1);

namespace Shopware\Core\Service;

use Shopware\Core\Framework\App\AppCollection;
use Shopware\Core\Framework\App\AppStateService;
use Shopware\Core\Framework\App\Privileges\Privileges;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @internal
 */
#[Package('framework')]
class Manager
{
    public const CONFIG_KEY_SERVICES_DISABLED = 'core.services.disabled';

    /**
     * @param EntityRepository<AppCollection> $repository
     */
    public function __construct(
        private readonly Privileges $privileges,
        private readonly SystemConfigService $systemConfigService,
        private readonly EntityRepository $repository,
        private readonly AppStateService $appStateService,
    ) {
    }

    public function grantPermissions(Context $context): void
    {
        /** @var list<string> $serviceIds */
        $serviceIds = $this->getAllServices($context)->getIds();

        $this->privileges->acceptAllForApps($serviceIds, $context);
    }

    public function revokePermissions(Context $context): void
    {
        /** @var list<string> $serviceIds */
        $serviceIds = $this->getAllServices($context)->getIds();

        $this->privileges->revokeAllForApps($serviceIds, $context);
    }

    public function enable(Context $context): void
    {
        $this->systemConfigService->delete(self::CONFIG_KEY_SERVICES_DISABLED);

        foreach ($this->getAllServices($context) as $service) {
            $this->appStateService->activateApp($service->getId(), $context);
        }
    }

    public function disable(Context $context): void
    {
        $services = $this->getAllServices($context);
        /** @var list<string> $serviceIds */
        $serviceIds = $services->getIds();

        $this->privileges->revokeAllForApps($serviceIds, $context);
        $this->systemConfigService->set(self::CONFIG_KEY_SERVICES_DISABLED, true);

        foreach ($services as $service) {
            $this->appStateService->deactivateApp($service->getId(), $context);
        }
    }

    private function getAllServices(Context $context): AppCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('selfManaged', true));

        return $this->repository->search($criteria, $context)->getEntities();
    }
}
