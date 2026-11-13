<?php

namespace App\Support\Payments;

readonly class GatewayInitResult
{
    public function __construct(
        public string $redirectUrl,
        public ?string $trackingCode = null,
        public array $meta = [],
    ) {}
}
