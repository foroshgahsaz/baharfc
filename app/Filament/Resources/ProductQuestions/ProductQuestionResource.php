<?php

namespace App\Filament\Resources\ProductQuestions;

use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\ProductQuestions\Pages\EditProductQuestion;
use App\Filament\Resources\ProductQuestions\Pages\ListProductQuestions;
use App\Models\ProductQuestion;
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

class ProductQuestionResource extends Resource
{
    use ChecksAdminPermissions;

    protected static function adminPermissionPrefix(): string
    {
        return 'product_questions';
    }

    protected static function requiredDatabaseTable(): ?string
    {
        return 'product_questions';
    }

    protected static ?string $model = ProductQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $modelLabel = 'پرسش محصول';

    protected static ?string $pluralModelLabel = 'پرسش‌های محصول';

    protected static ?string $navigationLabel = 'پرسش‌های محصول';

    protected static string|UnitEnum|null $navigationGroup = 'فروشگاه';

    protected static ?int $navigationSort = 46;

    public static function getNavigationBadge(): ?string
    {
        $count = SchemaGuard::whenTable('product_questions', fn () => ProductQuestion::query()
            ->where('status', 'pending')
            ->count(), 0);

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('author_name')->label('نام')->required(),
            Textarea::make('body')->label('پرسش')->required()->rows(4)->columnSpanFull(),
            Select::make('status')->label('وضعیت')->options([
                'pending' => 'در انتظار',
                'approved' => 'تأیید شده',
                'rejected' => 'رد شده',
            ])->required()->native(false),
            Textarea::make('answer_body')
                ->label('پاسخ فروشگاه')
                ->rows(4)
                ->columnSpanFull()
                ->dehydrated(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('محصول')->searchable(),
                TextColumn::make('author_name')->label('نام'),
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
                    ->visible(fn (ProductQuestion $record) => $record->status === 'pending')
                    ->action(fn (ProductQuestion $record) => $record->update(['status' => 'approved'])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductQuestions::route('/'),
            'edit' => EditProductQuestion::route('/{record}/edit'),
        ];
    }
}
