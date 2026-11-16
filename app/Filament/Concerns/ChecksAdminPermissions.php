<?php

namespace App\Filament\Concerns;

trait ChecksAdminPermissions
{
    abstract protected static function adminPermissionPrefix(): string;

    protected static function userCanAdmin(string $suffix): bool
    {
        return auth()->user()?->canAdmin(static::adminPermissionPrefix().'.'.$suffix) ?? false;
    }

    public static function canViewAny(): bool
    {
        return static::userCanAdmin('view')
            || (static::adminPermissionPrefix() === 'blog_comments' && static::userCanAdmin('moderate'));
    }

    public static function canCreate(): bool
    {
        return static::userCanAdmin('create');
    }

    public static function canEdit($record): bool
    {
        return static::userCanAdmin('update')
            || (static::adminPermissionPrefix() === 'blog_comments' && static::userCanAdmin('moderate'));
    }

    public static function canDelete($record): bool
    {
        return static::userCanAdmin('delete')
            || (static::adminPermissionPrefix() === 'blog_comments' && static::userCanAdmin('moderate'));
    }
}