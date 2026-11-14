<?php

namespace App\Filament\Resources\ShippingMethods;

use App\Filament\Resources\ShippingMethods\Pages\CreateShippingMethod;
use App\Filament\Resources\ShippingMethods\Pages\EditShippingMethod;
use App\Filament\Resources\ShippingMethods\Pages\ListShippingMethods;
use App\Models\ShippingMethod;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ShippingMethodResource extends Resource
{
    protected static ?string $model = ShippingMethod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $modelLabel = 'روش ارسال';

    protected static ?string $pluralModelLabel = 'روش‌های ارسال';

    protected static ?string $navigationLabel = 'حمل و نقل';

    protected static string|UnitEnum|null $navigationGroup = 'فروشگاه';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('نام')->required()->maxLength(255),
            TextInput::make('slug')
                ->label('شناسه')
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true)
                ->helperText('مثال: post-pishtaz'),
            TextInput::make('price')->label('هزینه (تومان)')->required()->numeric()->minValue(0),
            TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
            Toggle::make('is_active')->label('فعال')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('slug')->label('شناسه'),
                TextColumn::make('price')->label('هزینه')->numeric()->suffix(' تومان'),
                TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShippingMethods::route('/'),
            'create' => CreateShippingMethod::route('/create'),
            'edit' => EditShippingMethod::route('/{record}/edit'),
        ];
    }
}
