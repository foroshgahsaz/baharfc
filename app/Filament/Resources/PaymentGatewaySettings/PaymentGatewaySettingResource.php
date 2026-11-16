<?php

namespace App\Filament\Resources\PaymentGatewaySettings;

use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\PaymentGatewaySettings\Pages\EditPaymentGatewaySetting;
use App\Filament\Resources\PaymentGatewaySettings\Pages\ListPaymentGatewaySettings;
use App\Models\PaymentGatewaySetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PaymentGatewaySettingResource extends Resource
{
    use ChecksAdminPermissions;

    protected static function adminPermissionPrefix(): string
    {
        return 'payment_gateways';
    }

    protected static ?string $model = PaymentGatewaySetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $modelLabel = 'درگاه پرداخت';

    protected static ?string $pluralModelLabel = 'درگاه‌های پرداخت';

    protected static ?string $navigationLabel = 'درگاه‌های پرداخت';

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('درگاه')->searchable(),
                TextColumn::make('slug')->label('شناسه')->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_enabled')->label('فعال در فروشگاه')->boolean(),
                IconColumn::make('is_configured')
                    ->label('کلیدها کامل')
                    ->boolean()
                    ->getStateUsing(fn (PaymentGatewaySetting $record): bool => $record->isConfigured()),
            ])
            ->defaultSort('slug')
            ->recordUrl(fn (PaymentGatewaySetting $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentGatewaySettings::route('/'),
            'edit' => EditPaymentGatewaySetting::route('/{record}/edit'),
        ];
    }
}
