<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('تصویر')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('قیمت')
                    ->numeric()
                    ->sortable()
                    ->suffix(' تومان'),
                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->label('ویژه')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('به‌روزرسانی')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('فعال'),
                TernaryFilter::make('is_featured')->label('ویژه'),
            ])
            ->recordActions([
                Action::make('viewStorefront')
                    ->label('نمایش در فروشگاه')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Product $record): string => route('products.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Product $record): bool => $record->is_active),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
