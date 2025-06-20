<?php declare(strict_types=1);

namespace Shopware\Tests\Unit\Core\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\App\AppCollection;
use Shopware\Core\Framework\App\AppEntity;
use Shopware\Core\Framework\App\AppStateService;
use Shopware\Core\Framework\App\Privileges\Privileges;
use Shopware\Core\Framework\Context;
use Shopware\Core\Service\Manager;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Test\Stub\DataAbstractionLayer\StaticEntityRepository;

/**
 * @internal
 */
#[CoversClass(Manager::class)]
class ManagerTest extends TestCase
{
    private Privileges&MockObject $privileges;

    private AppStateService&MockObject $appStateService;

    private SystemConfigService&MockObject $systemConfigService;

    private Context $context;

    protected function setUp(): void
    {
        $this->privileges = $this->createMock(Privileges::class);
        $this->appStateService = $this->createMock(AppStateService::class);
        $this->systemConfigService = $this->createMock(SystemConfigService::class);
        $this->context = Context::createDefaultContext();
    }

    public function testEnable(): void
    {
        $services = new AppCollection([
            (new AppEntity())->assign(['id' => 'service1']),
            (new AppEntity())->assign(['id' => 'service2']),
            (new AppEntity())->assign(['id' => 'service3']),
        ]);

        $this->systemConfigService->expects($this->once())
            ->method('delete')
            ->with(Manager::CONFIG_KEY_SERVICES_DISABLED);

        $this->appStateService->expects($this->exactly($services->count()))
            ->method('activateApp');

        $manager = new Manager($this->privileges, $this->systemConfigService, $this->createAppRepository($services), $this->appStateService);

        $manager->enable($this->context);
    }

    public function testDisable(): void
    {
        $services = new AppCollection([
            (new AppEntity())->assign(['id' => 'service1']),
            (new AppEntity())->assign(['id' => 'service2']),
            (new AppEntity())->assign(['id' => 'service3']),
        ]);

        $this->privileges
            ->expects($this->once())
            ->method('revokeAllForApps')
            ->with($services->getIds(), $this->context);

        $this->systemConfigService->expects($this->once())
            ->method('set')
            ->with(Manager::CONFIG_KEY_SERVICES_DISABLED, true);

        $this->appStateService->expects($this->exactly($services->count()))
            ->method('deactivateApp');

        $manager = new Manager($this->privileges, $this->systemConfigService, $this->createAppRepository($services), $this->appStateService);

        $manager->disable($this->context);
    }

    public function testEnableWithNoServices(): void
    {
        $services = new AppCollection([]);

        $this->privileges
            ->expects($this->never())
            ->method('acceptAllForApps');

        $this->appStateService->expects($this->never())
            ->method('activateApp');

        $this->systemConfigService->expects($this->once())
            ->method('delete')
            ->with(Manager::CONFIG_KEY_SERVICES_DISABLED);

        $manager = new Manager($this->privileges, $this->systemConfigService, $this->createAppRepository($services), $this->appStateService);

        $manager->enable($this->context);
    }

    public function testDisableWithNoServices(): void
    {
        $services = new AppCollection([]);

        $this->privileges
            ->expects($this->once())
            ->method('revokeAllForApps')
            ->with([], $this->context);

        $manager = new Manager($this->privileges, $this->systemConfigService, $this->createAppRepository($services), $this->appStateService);

        $manager->disable($this->context);
    }

    /**
     * @return StaticEntityRepository<AppCollection>
     */
    private function createAppRepository(AppCollection $apps = new AppCollection()): StaticEntityRepository
    {
        /** @var StaticEntityRepository<AppCollection> $appRepository */
        $appRepository = new StaticEntityRepository([
            $apps,
        ]);

        return $appRepository;
    }
}
