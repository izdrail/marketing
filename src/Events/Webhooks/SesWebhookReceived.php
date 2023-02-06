<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Events\Webhooks;

class SesWebhookReceived
{
    /** @var array */
    public array $payload;

    /** @var string */
    public string $payloadType;

    public function __construct(array $payload, string $payloadType)
    {
        $this->payload = $payload;
        $this->payloadType = $payloadType;
    }
}
