<?php

namespace App\Filament\Resources\BlogPosts\RelationManagers;

use App\Enums\BlogCommentStatus;
use App\Models\BlogComment;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'نظرات این مطلب';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('author_name')->label('نام')->required(),
            TextInput::make('author_email')->label('ایمیل')->email(),
            Textarea::make('body')->label('متن')->required()->rows(4)->columnSpanFull(),
            Select::make('status')
                ->label('وضعیت')
                ->options(collect(BlogCommentStatus::cases())->mapWithKeys(fn (BlogCommentStatus $s) => [$s->value => $s->label()]))
                ->required()
                ->native(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author_name')->label('نویسنده'),
                TextColumn::make('body')->label('متن')->limit(60)->wrap(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (BlogCommentStatus $state): string => $state->label()),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->label('تأیید')
                    ->icon('heroicon-o-check')
                    ->visible(fn (BlogComment $record) => $record->status !== BlogCommentStatus::Approved)
                    ->action(fn (BlogComment $record) => $record->update(['status' => BlogCommentStatus::Approved])),
                Action::make('spam')
                    ->label('اسپم')
                    ->color('danger')
                    ->visible(fn (BlogComment $record) => $record->status !== BlogCommentStatus::Spam)
                    ->action(fn (BlogComment $record) => $record->update(['status' => BlogCommentStatus::Spam])),
                DeleteAction::make(),
            ]);
    }
}