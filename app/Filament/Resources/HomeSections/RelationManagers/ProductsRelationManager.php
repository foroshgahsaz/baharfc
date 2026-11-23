<?php

namespace App\Filament\Resources\HomeSections\RelationManagers;

use App\Enums\HomeProductSource;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'محصولات انتخاب‌شده';

    public function isVisible(): bool
    {
        return $this->getOwnerRecord()->source === HomeProductSource::Manual;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sort_order')
                ->label('ترتیب')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('محصول')->searchable(),
                TextColumn::make('price')->label('قیمت')->numeric(),
                TextColumn::make('pivot.sort_order')->label('ترتیب')->sortable(),
            ])
            ->defaultSort('home_section_product.sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('sort_order')
                            ->label('ترتیب')
                            ->numeric()
                            ->default(0),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ]);
    }
}