<?php

namespace App\Filament\Resources\AdminRoles\Pages;

use App\Filament\Resources\AdminRoles\AdminRoleResource;
use App\Models\AdminRole;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminRole extends EditRecord
{
    protected static string $resource = AdminRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn (AdminRole $record) => $record->is_system)
                ->tooltip(fn (AdminRole $record) => $record->is_system ? 'نقش‌های سیستمی قابل حذف نیستند.' : null),
        ];
    }
}