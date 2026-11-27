<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('products_count')
                    ->label('تعداد محصول')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label('والد')
                    ->toggleable(),
                TextColumn::make('slug')
                    ->label('نامک')
                    ->toggleable(),
                IconColumn::make('show_on_home')
                    ->label('صفحه اصلی')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
            ])
            ->recordActions([
                Action::make('viewStorefront')
                    ->label('مشاهده در فروشگاه')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Category $record): string => route('categories.show', $record->slug))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}