<?php

namespace App\Filament\Resources\ReturnRequests;

use App\Filament\Resources\ReturnRequests\Pages\EditReturnRequest;
use App\Filament\Resources\ReturnRequests\Pages\ListReturnRequests;
use App\Filament\Resources\ReturnRequests\Tables\ReturnRequestsTable;
use App\Models\ReturnRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ReturnRequestResource extends Resource
{
    protected static ?string $model = ReturnRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static ?string $modelLabel = 'مرجوعی';

    protected static ?string $pluralModelLabel = 'درخواست‌های مرجوعی';

    protected static ?string $navigationLabel = 'مرجوعی‌ها';

    protected static string|UnitEnum|null $navigationGroup = 'فروشگاه';

    protected static ?int $navigationSort = 7;

    public static function table(Table $table): Table
    {
        return ReturnRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReturnRequests::route('/'),
            'edit' => EditReturnRequest::route('/{record}/edit'),
        ];
    }
}
