<?php

namespace App\Filament\Concerns;

trait ChecksAdminPageAccess
{
    abstract protected static function adminPagePermission(): string;

    public static function canAccess(): bool
    {
        return auth()->user()?->canAdmin(static::adminPagePermission()) ?? false;
    }
}