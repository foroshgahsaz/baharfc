<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class SchemaGuard
{
    public static function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }

    public static function hasColumn(string $table, string $column): bool
    {
        if (! self::hasTable($table)) {
            return false;
        }

        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T|null
     */
    public static function whenTable(string $table, callable $callback, mixed $default = null): mixed
    {
        if (! self::hasTable($table)) {
            return $default;
        }

        try {
            return $callback();
        } catch (\Throwable) {
            return $default;
        }
    }
}