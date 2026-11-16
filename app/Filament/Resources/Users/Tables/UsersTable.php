<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\AdminRole;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('username')
                    ->label('نام کاربری')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('موبایل')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('is_admin')
                    ->label('مدیر کل')
                    ->boolean(),
                TextColumn::make('adminRole.name')
                    ->label('نقش پنل')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('عضویت')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_admin')->label('مدیر کل'),
                SelectFilter::make('admin_role_id')
                    ->label('نقش کارمند')
                    ->options(fn () => AdminRole::query()->orderBy('name')->pluck('name', 'id')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
