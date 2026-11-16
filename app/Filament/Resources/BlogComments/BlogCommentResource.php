<?php

namespace App\Filament\Resources\BlogComments;

use App\Enums\BlogCommentStatus;
use App\Filament\Resources\BlogComments\Pages\EditBlogComment;
use App\Filament\Resources\BlogComments\Pages\ListBlogComments;
use App\Models\BlogComment;
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

class BlogCommentResource extends Resource
{
    protected static ?string $model = BlogComment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $modelLabel = 'نظر';

    protected static ?string $pluralModelLabel = 'نظرات';

    protected static ?string $navigationLabel = 'نظرات بلاگ';

    protected static string|UnitEnum|null $navigationGroup = 'بازاریابی';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        $count = BlogComment::query()->where('status', BlogCommentStatus::Pending)->count();

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
            TextInput::make('author_email')->label('ایمیل')->email(),
            Textarea::make('body')->label('متن')->required()->rows(5)->columnSpanFull(),
            Select::make('status')
                ->label('وضعیت')
                ->options(collect(BlogCommentStatus::cases())->mapWithKeys(fn (BlogCommentStatus $s) => [$s->value => $s->label()]))
                ->required()
                ->native(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('post.title')->label('مطلب')->searchable()->limit(40),
                TextColumn::make('author_name')->label('نویسنده')->searchable(),
                TextColumn::make('author_email')->label('ایمیل')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('body')->label('متن')->limit(50)->wrap()->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (BlogCommentStatus $state): string => $state->label()),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(collect(BlogCommentStatus::cases())->mapWithKeys(fn (BlogCommentStatus $s) => [$s->value => $s->label()])),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('تأیید')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (BlogComment $record) => $record->status !== BlogCommentStatus::Approved)
                    ->action(fn (BlogComment $record) => $record->update(['status' => BlogCommentStatus::Approved])),
                Action::make('unapprove')
                    ->label('رد')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (BlogComment $record) => $record->status === BlogCommentStatus::Approved)
                    ->action(fn (BlogComment $record) => $record->update(['status' => BlogCommentStatus::Pending])),
                Action::make('spam')
                    ->label('اسپم')
                    ->color('danger')
                    ->action(fn (BlogComment $record) => $record->update(['status' => BlogCommentStatus::Spam])),
                Action::make('trash')
                    ->label('زباله')
                    ->color('gray')
                    ->action(fn (BlogComment $record) => $record->update(['status' => BlogCommentStatus::Trash])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('تأیید انتخاب‌شده‌ها')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each(
                            fn (BlogComment $comment) => $comment->update(['status' => BlogCommentStatus::Approved])
                        )),
                    BulkAction::make('markSpam')
                        ->label('علامت به عنوان اسپم')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each(
                            fn (BlogComment $comment) => $comment->update(['status' => BlogCommentStatus::Spam])
                        )),
                    BulkAction::make('moveToTrash')
                        ->label('انتقال به زباله')
                        ->action(fn (Collection $records) => $records->each(
                            fn (BlogComment $comment) => $comment->update(['status' => BlogCommentStatus::Trash])
                        )),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogComments::route('/'),
            'edit' => EditBlogComment::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}