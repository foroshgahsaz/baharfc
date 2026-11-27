<?php

namespace App\Filament\Resources\BlogPosts;

use App\Enums\BlogPostStatus;
use App\Filament\Forms\Components\MediaPicker;
use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Filament\Resources\BlogPosts\RelationManagers\CommentsRelationManager;
use App\Models\BlogPost;
use App\Support\SchemaGuard;
use App\Models\User;
use BackedEnum;
use App\Filament\Forms\Components\JalaliDateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class BlogPostResource extends Resource
{
    use ChecksAdminPermissions;

    protected static function adminPermissionPrefix(): string
    {
        return 'blog_posts';
    }

    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $modelLabel = 'مقاله';

    protected static ?string $pluralModelLabel = 'مقالات';

    protected static string|UnitEnum|null $navigationGroup = 'بازاریابی';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->label('نامک (پیوند یکتا)')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('مثل وردپرس — در آدرس سایت استفاده می‌شود'),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(collect(BlogPostStatus::cases())->mapWithKeys(fn (BlogPostStatus $s) => [$s->value => $s->label()]))
                    ->default(BlogPostStatus::Draft->value)
                    ->required()
                    ->native(false),
                Select::make('blog_category_id')
                    ->label('دسته')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('author_id')
                    ->label('نویسنده')
                    ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                JalaliDateTimePicker::make('published_at')
                    ->label('تاریخ انتشار (شمسی)')
                    ->helperText('برای «زمان‌بندی شده» تاریخ آیندهٔ شمسی انتخاب کنید'),
                MediaPicker::make('featured_image_path')
                    ->label('تصویر شاخص')
                    ->imagesOnly()
                    ->columnSpanFull(),
                Textarea::make('excerpt')
                    ->label('خلاصه / چکیده')
                    ->rows(3)
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('محتوا')
                    ->required()
                    ->columnSpanFull(),
                Select::make('tags')
                    ->label('برچسب‌ها')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->label('نام')->required()->maxLength(120),
                        TextInput::make('slug')->label('نامک')->required()->maxLength(120),
                    ])
                    ->helperText('مدیریت برچسب‌ها مثل تاکنونومی وردپرس')
                    ->visible(fn (): bool => SchemaGuard::hasTable('blog_tags'))
                    ->columnSpanFull(),
                TextInput::make('meta_title')
                    ->label('عنوان SEO'),
                Textarea::make('meta_description')
                    ->label('توضیح SEO')
                    ->rows(2)
                    ->maxLength(500)
                    ->columnSpanFull(),
                Toggle::make('allow_comments')
                    ->label('امکان ثبت نظر')
                    ->default(true),
                Toggle::make('is_featured')
                    ->label('مطلب ویژه'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان')->searchable()->sortable(),
                TextColumn::make('category.name')->label('دسته'),
                TextColumn::make('author.name')->label('نویسنده')->toggleable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (BlogPostStatus $state): string => $state->label()),
                TextColumn::make('comments_count')
                    ->label('نظرات')
                    ->counts('comments')
                    ->sortable(),
                TextColumn::make('published_at')->label('انتشار')->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(collect(BlogPostStatus::cases())->mapWithKeys(fn (BlogPostStatus $s) => [$s->value => $s->label()])),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }
}