<?php

namespace App\Filament\Resources\ProductReviews;

use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\ProductReviews\Pages\EditProductReview;
use App\Filament\Resources\ProductReviews\Pages\ListProductReviews;
use App\Models\ProductReview;
use App\Support\SchemaGuard;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ProductReviewResource extends Resource
{
    use ChecksAdminPermissions;

    protected static function adminPermissionPrefix(): string
    {
        return 'product_reviews';
    }

    protected static function requiredDatabaseTable(): ?string
    {
        return 'product_reviews';
    }

    protected static ?string $model = ProductReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $modelLabel = 'دیدگاه محصول';

    protected static ?string $pluralModelLabel = 'دیدگاه‌های محصول';

    protected static ?string $navigationLabel = 'دیدگاه‌های محصول';

    protected static string|UnitEnum|null $navigationGroup = 'فروشگاه';

    protected static ?int $navigationSort = 45;

    public static function getNavigationBadge(): ?string
    {
        $count = SchemaGuard::whenTable('product_reviews', fn () => ProductReview::query()
            ->where('status', 'pending')
            ->count(), 0);

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('author_name')->label('نام')->required(),
            TextInput::make('rating')->label('امتیاز')->numeric()->minValue(1)->maxValue(5),
            Textarea::make('body')->label('متن')->required()->rows(5)->columnSpanFull(),
            Select::make('status')->label('وضعیت')->options([
                'pending' => 'در انتظار',
                'approved' => 'تأیید شده',
                'rejected' => 'رد شده',
            ])->required()->native(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('محصول')->searchable(),
                TextColumn::make('author_name')->label('نام'),
                TextColumn::make('rating')->label('امتیاز'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('created_at')->label('تاریخ')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')->label('وضعیت')->options([
                    'pending' => 'در انتظار',
                    'approved' => 'تأیید شده',
                    'rejected' => 'رد شده',
                ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('تأیید')
                    ->visible(fn (ProductReview $record) => $record->status === 'pending')
                    ->action(fn (ProductReview $record) => $record->update(['status' => 'approved'])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductReviews::route('/'),
            'edit' => EditProductReview::route('/{record}/edit'),
        ];
    }
}
