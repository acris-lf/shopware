<?php

namespace Shopware\Core\Service\ServiceRegistryClient;

class SaveConsentRequest implements \JsonSerializable
{
    public function __construct(
        public string $identifier,
        public string $shopUserId,
        public string $shopIdentifier,
        public string $consentDate,
        public string $consentRevision,
        public ?string $licenseHost = null,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'identifier' => $this->identifier,
            'shopUserId' => $this->shopUserId,
            'shopIdentifier' => $this->shopIdentifier,
            'consentDate' => $this->consentDate,
            'consentRevision' => $this->consentRevision,
            'licenseHost' => $this->licenseHost,
        ];
    }
}
