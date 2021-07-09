<?php

declare(strict_types=1);

namespace WebsProjects\PayGatePlugin\Payum;

final class SyliusApi
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $paygateId;

    /** @var string */
    private $reference;

    public function __construct(string $apiKey, string $paygateId, string $reference)
    {
        $this->apiKey = $apiKey;
        $this->paygateId = $paygateId;
        $this->reference = $reference;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getPaygateId(): string
    {
        return $this->paygateId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

}
