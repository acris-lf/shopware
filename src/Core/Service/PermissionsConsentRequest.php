<?php

namespace Shopware\Core\Service;

class PermissionsConsentRequest
{
    public function __construct(
        public string $consentId,
        public string $userId,
        public string $shopId,
        public string $consentDate,
        public string $consentRevision,
        public ?string $licenseHost = null
    ) {
    }
}
