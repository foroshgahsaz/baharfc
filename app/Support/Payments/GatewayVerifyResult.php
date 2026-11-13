<?php

namespace App\Support\Payments;

readonly class GatewayVerifyResult
{
    public function __construct(
        public bool $successful,
        public ?string $trackingCode = null,
        public ?string $failureReason = null,
        public array $meta = [],
    ) {}
}
