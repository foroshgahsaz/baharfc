<?php

namespace App\Support\AdminBar;

readonly class AdminBarItem
{
    public function __construct(
        public string $label,
        public string $url,
        public bool $highlight = false,
        public bool $openInNewTab = false,
    ) {}
}