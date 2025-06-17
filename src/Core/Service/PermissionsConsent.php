<?php

namespace Shopware\Core\Service;

use Shopware\Core\Framework\Context;

class PermissionsConsent
{
    public function __construct(
        public string $identifier,
        public string $revision,
        public \DateTimeInterface $grantedAt,
    ) {
    }
}
