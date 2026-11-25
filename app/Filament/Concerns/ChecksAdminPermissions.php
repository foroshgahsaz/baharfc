<?php

namespace App\Filament\Concerns;

use App\Support\SchemaGuard;

trait ChecksAdminPermissions
{
    abstract protected static function adminPermissionPrefix(): string;

    protected static function requiredDatabaseTable(): ?string
    {
        return null;
    }

    protected static function databaseTableReady(): bool
    {
        $table = static::requiredDatabaseTable();

        return $table === null || SchemaGuard::hasTable($table);
    }

    protected static function userCanAdmin(string $suffix): bool
    {
        return auth()->user()?->canAdmin(static::adminPermissionPrefix().'.'.$suffix) ?? false;
    }

    public static function canViewAny(): bool
    {
        if (! static::databaseTableReady()) {
            return false;
        }

        $moderatePrefixes = ['blog_comments', 'product_reviews', 'product_questions'];

        return static::userCanAdmin('view')
            || (in_array(static::adminPermissionPrefix(), $moderatePrefixes, true) && static::userCanAdmin('moderate'));
    }

    public static function canCreate(): bool
    {
        if (! static::databaseTableReady()) {
            return false;
        }

        return static::userCanAdmin('create');
    }

    public static function canEdit($record): bool
    {
        if (! static::databaseTableReady()) {
            return false;
        }

        $moderatePrefixes = ['blog_comments', 'product_reviews', 'product_questions'];

        return static::userCanAdmin('update')
            || (in_array(static::adminPermissionPrefix(), $moderatePrefixes, true) && static::userCanAdmin('moderate'));
    }

    public static function canDelete($record): bool
    {
        if (! static::databaseTableReady()) {
            return false;
        }

        $moderatePrefixes = ['blog_comments', 'product_reviews', 'product_questions'];

        return static::userCanAdmin('delete')
            || (in_array(static::adminPermissionPrefix(), $moderatePrefixes, true) && static::userCanAdmin('moderate'));
    }
}