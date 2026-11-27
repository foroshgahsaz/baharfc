<?php

namespace App\Filament\Resources\BlogTags;

use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\BlogTags\Pages\CreateBlogTag;
use App\Filament\Resources\BlogTags\Pages\EditBlogTag;
use App\Filament\Resources\BlogTags\Pages\ListBlogTags;
use App\Models\BlogTag;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BlogTagResource extends Resource
{
    use ChecksAdminPermissions;

    protected static function adminPermissionPrefix(): string
    {
        return 'blog_tags';
    }

    protected static ?string $model = BlogTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $modelLabel = 'برچسب بلاگ';

    protected static ?string $pluralModelLabel = 'برچسب‌های بلاگ';

    protected static string|UnitEnum|null $navigationGroup = 'بازاریابی';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('نام')->required()->maxLength(120),
            TextInput::make('slug')->label('نامک')->required()->maxLength(120)->unique(ignoreRecord: true),
            Textarea::make('description')->label('توضیح (آرشیو برچسب)')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('slug')->label('نامک'),
                TextColumn::make('posts_count')->label('مقالات')->counts('posts'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogTags::route('/'),
            'create' => CreateBlogTag::route('/create'),
            'edit' => EditBlogTag::route('/{record}/edit'),
        ];
    }
}