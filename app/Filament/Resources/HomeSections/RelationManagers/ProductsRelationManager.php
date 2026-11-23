<?php

namespace App\Filament\Resources\HomeSections\RelationManagers;

use App\Enums\HomeProductSource;
use App\Models\Product;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'محصولات انتخاب‌شده';

    public function isVisible(): bool
    {
        return $this->getOwnerRecord()->source === HomeProductSource::Manual;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sort_order')
                ->label('ترتیب')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('محصول')->searchable(),
                TextColumn::make('price')->label('قیمت')->numeric(),
                TextColumn::make('pivot.sort_order')->label('ترتیب')->sortable(),
            ])
            ->defaultSort('home_section_product.sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordTitle(fn (Product $record): string => self::productSelectLabel($record, html: true))
                    ->recordSelectSearchColumns(['name', 'slug'])
                    ->recordSelectOptionsQuery(fn ($query) => $query->where('is_active', true))
                    ->recordSelect(fn (Select $select) => $select->allowHtml())
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('محصول'),
                        TextInput::make('sort_order')
                            ->label('ترتیب')
                            ->numeric()
                            ->default(0),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ]);
    }

    private static function productSelectLabel(Product $record, bool $html = false): string
    {
        $name = $record->name;
        $price = number_format($record->sellablePrice()).' تومان';

        if (! $html) {
            return "{$name} — {$price}";
        }

        $image = $record->imageUrl();
        $safeName = e($name);
        $safePrice = e($price);

        if (blank($image)) {
            return "<span class=\"fi-product-opt\"><strong>{$safeName}</strong> — <span class=\"text-gray-500\">{$safePrice}</span></span>";
        }

        $safeImage = e($image);

        return '<span class="fi-product-opt" style="display:flex;align-items:center;gap:8px;">'
            .'<img src="'.$safeImage.'" alt="" style="width:36px;height:36px;border-radius:8px;object-fit:cover;flex-shrink:0;" />'
            .'<span><strong>'.$safeName.'</strong><br /><span style="font-size:12px;color:#6b7280;">'.$safePrice.'</span></span>'
            .'</span>';
    }
}