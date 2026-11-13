<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

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
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('نامک (URL)')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('برای فارسی نامک را دستی وارد کنید'),
                Select::make('category_id')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('brand_id')
                    ->label('برند')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('price')
                    ->label('قیمت (تومان)')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                TextInput::make('compare_at_price')
                    ->label('قیمت قبل از تخفیف')
                    ->numeric()
                    ->minValue(0),
                Textarea::make('short_description')
                    ->label('توضیح کوتاه')
                    ->rows(2)
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
                TextInput::make('meta_title')
                    ->label('عنوان SEO'),
                Textarea::make('meta_description')
                    ->label('توضیح SEO')
                    ->rows(2),
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
