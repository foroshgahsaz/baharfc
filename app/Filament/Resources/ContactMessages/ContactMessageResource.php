<?php

namespace App\Filament\Resources\ContactMessages;

use App\Enums\ContactMessageStatus;
use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\ContactMessages\Pages\EditContactMessage;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Models\ContactMessage;
use App\Support\SchemaGuard;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class ContactMessageResource extends Resource
{
    use ChecksAdminPermissions;

    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $modelLabel = 'پیام تماس';

    protected static ?string $pluralModelLabel = 'پیام‌های تماس';

    protected static ?string $navigationLabel = 'صندوق تماس';

    protected static string|UnitEnum|null $navigationGroup = 'فروشگاه';

    protected static ?int $navigationSort = 7;

    protected static function adminPermissionPrefix(): string
    {
        return 'contact_messages';
    }

    protected static function requiredDatabaseTable(): ?string
    {
        return 'contact_messages';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = SchemaGuard::whenTable('contact_messages', fn () => ContactMessage::query()
            ->where('status', ContactMessageStatus::New)
            ->count(), 0);

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('نام')->disabled(),
                TextInput::make('email')->label('ایمیل')->disabled(),
                TextInput::make('phone')->label('تلفن')->disabled(),
                TextInput::make('subject')->label('موضوع')->disabled(),
                Textarea::make('message')->label('پیام')->disabled()->rows(6)->columnSpanFull(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(collect(ContactMessageStatus::cases())->mapWithKeys(fn (ContactMessageStatus $s) => [$s->value => $s->label()]))
                    ->required()
                    ->native(false),
                Textarea::make('admin_note')
                    ->label('یادداشت داخلی مدیر')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('email')->label('ایمیل')->searchable()->copyable(),
                TextColumn::make('subject')->label('موضوع')->limit(40)->placeholder('—'),
                TextColumn::make('message')->label('خلاصه')->limit(50)->wrap(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (ContactMessageStatus $state): string => $state->label()),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(collect(ContactMessageStatus::cases())->mapWithKeys(fn (ContactMessageStatus $s) => [$s->value => $s->label()]))
                    ->default(ContactMessageStatus::New->value),
            ])
            ->recordActions([
                Action::make('markRead')
                    ->label('خوانده شد')
                    ->icon('heroicon-o-envelope-open')
                    ->visible(fn (ContactMessage $record) => $record->status === ContactMessageStatus::New)
                    ->action(function (ContactMessage $record): void {
                        $record->markAsRead();
                    }),
                Action::make('archive')
                    ->label('بایگانی')
                    ->action(fn (ContactMessage $record) => $record->update(['status' => ContactMessageStatus::Archived])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markReadBulk')
                        ->label('علامت خوانده‌شده')
                        ->action(fn (Collection $records) => $records->each->markAsRead()),
                    BulkAction::make('archiveBulk')
                        ->label('بایگانی')
                        ->action(fn (Collection $records) => $records->each(
                            fn (ContactMessage $m) => $m->update(['status' => ContactMessageStatus::Archived])
                        )),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'edit' => EditContactMessage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}