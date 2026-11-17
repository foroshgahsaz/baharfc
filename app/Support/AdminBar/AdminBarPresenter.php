<?php

namespace App\Support\AdminBar;

use App\Models\User;

readonly class AdminBarPresenter
{
    /**
     * @param  list<AdminBarItem>  $links
     */
    public function __construct(
        public User $user,
        public string $siteName,
        public string $panelHomeUrl,
        public string $shopHomeUrl,
        public string $logoutUrl,
        public array $links,
        public string $context,
    ) {}

    public function isVisible(): bool
    {
        return true;
    }
}