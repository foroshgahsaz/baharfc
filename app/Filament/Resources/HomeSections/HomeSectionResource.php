<?php

namespace App\Filament\Resources\HomeSections;

use App\Enums\HomeCarouselStyle;
use App\Enums\HomeProductSource;
use App\Enums\HomeSectionAnchor;
use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\HomeSections\Pages\CreateHomeSection;
use App\Filament\Resources\HomeSections\Pages\EditHomeSection;
use App\Filament\Resources\HomeSections\Pages\ListHomeSections;
use App\Filament\Resources\HomeSections\RelationManagers\ProductsRelationManager;
use App\Models\HomeSection;
use App\Support\SchemaGuard;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class HomeSectionResource extends Resource
{
    use ChecksAdminPermissions;

    protected static ?string $model = HomeSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?string $modelLabel = 'کاروسل صفحه اصلی';

    protected static ?string $pluralModelLabel = 'کاروسل‌های صفحه اصلی';

    protected static ?string $navigationLabel = 'کاروسل صفحه اصلی';

    protected static string|UnitEnum|null $navigationGroup = 'بازاریابی';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    protected static function adminPermissionPrefix(): string
    {
        return 'home_sections';
    }

    public static function canViewAny(): bool
    {
        return SchemaGuard::hasTable('home_sections') && parent::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label('عنوان کاروسل')
                    ->required()
                    ->maxLength(160)
                    ->columnSpanFull(),
                TextInput::make('subtitle')
                    ->label('زیرعنوان / هایلایت (اختیاری)')
                    ->maxLength(240)
                    ->columnSpanFull(),
                Select::make('style')
                    ->label('استایل نمایش')
                    ->options(HomeCarouselStyle::options())
                    ->required()
                    ->default(HomeCarouselStyle::Style1->value)
                    ->native(false),
                Select::make('position_after')
                    ->label('جایگاه در صفحه اصلی')
                    ->options(HomeSectionAnchor::options())
                    ->required()
                    ->default(HomeSectionAnchor::SpecialDeals->value)
                    ->native(false)
                    ->helperText('مثلاً «بعد از پیشنهاد شگفت‌انگیز»'),
                Select::make('source')
                    ->label('منبع محصولات')
                    ->options(HomeProductSource::options())
                    ->required()
                    ->default(HomeProductSource::Category->value)
                    ->live()
                    ->native(false),
                Select::make('category_id')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get): bool => $get('source') === HomeProductSource::Category->value)
                    ->required(fn (Get $get): bool => $get('source') === HomeProductSource::Category->value),
                TextInput::make('product_limit')
                    ->label('تعداد نمایش')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(24)
                    ->default(8)
                    ->visible(fn (Get $get): bool => $get('source') === HomeProductSource::Category->value),
                TextInput::make('more_label')
                    ->label('متن دکمه بیشتر')
                    ->default('محصولات بیشتر')
                    ->maxLength(80),
                TextInput::make('more_url')
                    ->label('لینک دکمه بیشتر (اختیاری)')
                    ->url()
                    ->maxLength(2048)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('ترتیب (بین کاروسل‌های هم‌جایگاه)')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان')->searchable(),
                TextColumn::make('style')
                    ->label('استایل')
                    ->formatStateUsing(fn ($state) => $state instanceof HomeCarouselStyle ? $state->label() : (string) $state),
                TextColumn::make('position_after')
                    ->label('جایگاه')
                    ->formatStateUsing(fn ($state) => $state instanceof HomeSectionAnchor ? $state->label() : (string) $state),
                TextColumn::make('source')
                    ->label('منبع')
                    ->formatStateUsing(fn ($state) => $state instanceof HomeProductSource ? $state->label() : (string) $state),
                TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomeSections::route('/'),
            'create' => CreateHomeSection::route('/create'),
            'edit' => EditHomeSection::route('/{record}/edit'),
        ];
    }
}