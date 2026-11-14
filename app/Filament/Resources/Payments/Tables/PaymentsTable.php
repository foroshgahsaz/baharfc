<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->label('ارجاع')->searchable()->copyable(),
                TextColumn::make('order.number')->label('سفارش')->searchable(),
                TextColumn::make('gateway')->label('درگاه'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->label()),
                TextColumn::make('amount')->label('مبلغ')->numeric()->suffix(' تومان'),
                TextColumn::make('tracking_code')->label('پیگیری')->toggleable(),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn (PaymentStatus $s) => [$s->value => $s->label()])),
            ]);
    }
}
