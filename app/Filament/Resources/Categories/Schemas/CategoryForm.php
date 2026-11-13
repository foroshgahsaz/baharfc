<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('نام')->required(),
                TextInput::make('slug')->label('نامک')->required()->unique(ignoreRecord: true),
                Select::make('parent_id')
                    ->label('دسته والد')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
                Textarea::make('description')->label('توضیحات')->columnSpanFull(),
                TextInput::make('meta_title')->label('عنوان SEO'),
                Textarea::make('meta_description')->label('توضیح SEO')->rows(2),
                FileUpload::make('image_path')->label('تصویر')->image()->disk('public')->directory('categories')->columnSpanFull(),
                Toggle::make('is_active')->label('فعال')->default(true),
            ]);
    }
}
