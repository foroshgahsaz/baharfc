<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'گالری تصاویر';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')
                ->label('تصویر')
                ->image()
                ->disk('public')
                ->directory('products/gallery')
                ->required(),
            TextInput::make('alt')->label('متن جایگزین'),
            TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('path')->label('پیش‌نمایش')->disk('public')->square(),
                TextColumn::make('alt')->label('Alt'),
                TextColumn::make('sort_order')->label('ترتیب'),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن تصویر'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
