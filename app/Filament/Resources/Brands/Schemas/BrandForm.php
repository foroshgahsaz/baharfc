<?php

namespace App\Filament\Resources\Brands\Schemas;

use App\Filament\Forms\Components\MediaPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('نام برند')->required(),
            TextInput::make('slug')->label('نامک')->required()->unique(ignoreRecord: true),
            Textarea::make('description')->label('توضیحات'),
            MediaPicker::make('logo_path')->label('لوگو')->imagesOnly(),
            Toggle::make('is_active')->label('فعال')->default(true),
        ]);
    }
}
