<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Models\MenuItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'آیتم‌های منو';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('label')
                    ->label('عنوان نمایشی')
                    ->required()
                    ->maxLength(120)
                    ->columnSpanFull(),
                Select::make('item_type')
                    ->label('نوع لینک')
                    ->options(MenuItem::itemTypeOptions())
                    ->required()
                    ->native(false)
                    ->live()
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get): bool => $get('item_type') === 'category')
                    ->required(fn (Get $get): bool => $get('item_type') === 'category'),
                Select::make('product_id')
                    ->label('محصول')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get): bool => $get('item_type') === 'product')
                    ->required(fn (Get $get): bool => $get('item_type') === 'product'),
                Select::make('blog_post_id')
                    ->label('مقاله بلاگ')
                    ->relationship('blogPost', 'title')
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get): bool => $get('item_type') === 'blog_post')
                    ->required(fn (Get $get): bool => $get('item_type') === 'blog_post'),
                Select::make('page_key')
                    ->label('صفحه')
                    ->options(MenuItem::pageKeyOptions())
                    ->native(false)
                    ->visible(fn (Get $get): bool => $get('item_type') === 'page')
                    ->required(fn (Get $get): bool => $get('item_type') === 'page'),
                KeyValue::make('query_params')
                    ->label('پارامترهای آدرس (اختیاری)')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->visible(fn (Get $get): bool => $get('item_type') === 'page' && $get('page_key') === 'products')
                    ->columnSpanFull(),
                TextInput::make('custom_url')
                    ->label('آدرس کامل')
                    ->url()
                    ->maxLength(2048)
                    ->visible(fn (Get $get): bool => $get('item_type') === 'custom_url')
                    ->required(fn (Get $get): bool => $get('item_type') === 'custom_url')
                    ->columnSpanFull(),
                Select::make('parent_id')
                    ->label('زیرمجموعهٔ')
                    ->options(function (): array {
                        $menuId = $this->getOwnerRecord()->getKey();

                        return MenuItem::query()
                            ->where('menu_id', $menuId)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->pluck('label', 'id')
                            ->all();
                    })
                    ->native(false)
                    ->helperText('زیرمنو یا ستون مگامنو — برای مگامنو، والد را خالی بگذارید و «مگامنو» را روی آیتم سطح اول فعال کنید؛ فرزندان مستقیم ستون‌ها و نوه‌ها لینک‌های هر ستون هستند.'),
                Toggle::make('is_mega_menu')
                    ->label('مگامنو (چندستونه، مثل وردپرس)')
                    ->helperText('فقط برای آیتم‌های سطح اول منوی header_main.')
                    ->visible(fn (Get $get): bool => ! $get('parent_id')),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')->label('فعال')->default(true),
                Toggle::make('show_divider_before')->label('جداکننده قبل از لینک'),
                Toggle::make('open_in_new_tab')->label('باز شدن در تب جدید'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('label')->label('عنوان'),
                TextColumn::make('item_type')
                    ->label('نوع')
                    ->formatStateUsing(fn (string $state): string => MenuItem::itemTypeOptions()[$state] ?? $state),
                TextColumn::make('resolved_url')
                    ->label('آدرس')
                    ->state(fn (MenuItem $record): string => $record->resolvedUrl())
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('sort_order')->label('ترتیب'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن آیتم'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}