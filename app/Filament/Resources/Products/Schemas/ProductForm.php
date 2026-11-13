<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('نام محصول')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($set, ?string $state): void {
                        if (filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('نامک (URL)')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('price')
                    ->label('قیمت (تومان)')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                TextInput::make('compare_at_price')
                    ->label('قیمت قبل از تخفیف')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('sort_order')
                    ->label('ترتیب نمایش')
                    ->numeric()
                    ->default(0),
                FileUpload::make('image_path')
                    ->label('تصویر')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                Toggle::make('is_featured')
                    ->label('نمایش در صفحه اصلی'),
            ]);
    }
}
