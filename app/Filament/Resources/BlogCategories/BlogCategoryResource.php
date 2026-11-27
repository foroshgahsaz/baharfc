<?php

namespace App\Filament\Resources\BlogCategories;

use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\BlogCategories\Pages\CreateBlogCategory;
use App\Filament\Resources\BlogCategories\Pages\EditBlogCategory;
use App\Filament\Resources\BlogCategories\Pages\ListBlogCategories;
use App\Models\BlogCategory;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Support\SchemaGuard;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BlogCategoryResource extends Resource
{
    use ChecksAdminPermissions;

    protected static function adminPermissionPrefix(): string
    {
        return 'blog_categories';
    }

    protected static ?string $model = BlogCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?string $modelLabel = 'دسته بلاگ';

    protected static ?string $pluralModelLabel = 'دسته‌های بلاگ';

    protected static string|UnitEnum|null $navigationGroup = 'بازاریابی';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('نام')->required(),
            TextInput::make('slug')->label('نامک')->required()->unique(ignoreRecord: true),
            Select::make('parent_id')
                ->label('دسته والد')
                ->relationship('parent', 'name')
                ->searchable()
                ->preload()
                ->visible(fn (): bool => SchemaGuard::hasColumn('blog_categories', 'parent_id')),
            Textarea::make('description')
                ->label('توضیح (صفحه آرشیو)')
                ->rows(3)
                ->columnSpanFull()
                ->visible(fn (): bool => SchemaGuard::hasColumn('blog_categories', 'description')),
            TextInput::make('meta_title')
                ->label('عنوان SEO')
                ->visible(fn (): bool => SchemaGuard::hasColumn('blog_categories', 'meta_title')),
            Textarea::make('meta_description')
                ->label('توضیح SEO')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull()
                ->visible(fn (): bool => SchemaGuard::hasColumn('blog_categories', 'meta_description')),
            Toggle::make('is_active')->label('فعال')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('نام')->searchable(),
            TextColumn::make('slug')->label('نامک'),
            IconColumn::make('is_active')->label('فعال')->boolean(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogCategories::route('/'),
            'create' => CreateBlogCategory::route('/create'),
            'edit' => EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
