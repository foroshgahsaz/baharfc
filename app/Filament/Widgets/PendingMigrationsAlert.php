<?php

namespace App\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator;

class PendingMigrationsAlert extends Widget
{
    protected static ?int $sort = -10;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.pending-migrations';

    public static function canView(): bool
    {
        if (! Filament::auth()->check()) {
            return false;
        }

        return count(self::pendingMigrationNames()) > 0;
    }

    /**
     * @return list<string>
     */
    public static function pendingMigrationNames(): array
    {
        try {
            /** @var Migrator $migrator */
            $migrator = app('migrator');
            /** @var MigrationRepositoryInterface $repository */
            $repository = app(MigrationRepositoryInterface::class);

            if (! $repository->repositoryExists()) {
                return ['migrations_table'];
            }

            $files = $migrator->getMigrationFiles($migrator->paths());

            return array_values(array_diff(array_keys($files), $repository->getRan()));
        } catch (\Throwable) {
            return [];
        }
    }

    protected function getViewData(): array
    {
        $pending = self::pendingMigrationNames();

        return [
            'pending' => $pending,
            'count' => count($pending),
        ];
    }
}