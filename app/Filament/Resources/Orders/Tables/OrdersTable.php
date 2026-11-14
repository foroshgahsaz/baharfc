<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label('شماره')->searchable()->copyable(),
                TextColumn::make('user.name')->label('مشتری')->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label()),
                TextColumn::make('total')->label('مبلغ')->numeric()->suffix(' تومان')->sortable(),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $s) => [$s->value => $s->label()])),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
