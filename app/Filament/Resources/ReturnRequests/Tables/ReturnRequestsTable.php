<?php

namespace App\Filament\Resources\ReturnRequests\Tables;

use App\Enums\ReturnRequestStatus;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReturnRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.number')->label('سفارش')->searchable(),
                TextColumn::make('user.name')->label('مشتری')->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (ReturnRequestStatus $state): string => $state->label()),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(collect(ReturnRequestStatus::cases())->mapWithKeys(fn (ReturnRequestStatus $s) => [$s->value => $s->label()])),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
