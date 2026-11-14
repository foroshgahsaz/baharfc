<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'تنوع‌ها (واریانت)';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('عنوان')->required(),
            TextInput::make('sku')->label('SKU'),
            TextInput::make('price')->label('قیمت (تومان)')->numeric()->required(),
            TextInput::make('compare_at_price')->label('قیمت خط‌خورده')->numeric(),
            TextInput::make('stock')->label('موجودی')->numeric()->default(0),
            TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
            KeyValue::make('options')->label('ویژگی‌ها'),
            Toggle::make('is_active')->label('فعال')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')->label('عنوان'),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('price')->label('قیمت')->numeric()->suffix(' تومان'),
                TextColumn::make('stock')->label('موجودی'),
            ])
            ->headerActions([CreateAction::make()->label('افزودن واریانت')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
