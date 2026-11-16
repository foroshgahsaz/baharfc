<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\CreateBanner;
use App\Filament\Resources\Banners\Pages\EditBanner;
use App\Filament\Resources\Banners\Pages\ListBanners;
use App\Models\Banner;
use BackedEnum;
use App\Filament\Forms\Components\MediaPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $modelLabel = 'اسلاید صفحه اصلی';

    protected static ?string $pluralModelLabel = 'اسلایدر صفحه اصلی';

    protected static ?string $navigationLabel = 'اسلایدر صفحه اصلی';

    protected static string|UnitEnum|null $navigationGroup = 'بازاریابی';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('عنوان')->required(),
            TextInput::make('subtitle')->label('زیرعنوان')->columnSpanFull(),
            MediaPicker::make('image_path')
                ->label('تصویر اسلاید')
                ->imagesOnly()
                ->helperText('ابعاد پیشنهادی: ۱۳۸۸×۴۶۳ — انتخاب از کتابخانه رسانه')
                ->columnSpanFull(),
            TextInput::make('image_url')
                ->label('یا آدرس تصویر (اختیاری)')
                ->url()
                ->columnSpanFull(),
            TextInput::make('link_url')->label('لینک کلیک')->url(),
            TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
            Toggle::make('is_active')->label('فعال')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان')->searchable(),
                TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }
}
