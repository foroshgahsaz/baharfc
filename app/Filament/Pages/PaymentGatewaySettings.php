<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PaymentGatewaySettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'درگاه‌های پرداخت';

    protected static ?string $title = 'تنظیمات درگاه‌های پرداخت';

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.payment-gateway-settings';

    public function getViewData(): array
    {
        $gateways = [
            'mellat' => 'بانک ملت (به‌پرداخت)',
            'parsian' => 'بانک پارسیان',
            'digipay' => 'دیجی‌پی (اقساط)',
            'snapppay' => 'اسنپ‌پی (اقساط)',
        ];

        $configured = [];

        foreach (array_keys($gateways) as $key) {
            $cfg = config("payment.gateways.{$key}", []);
            $configured[$key] = collect($cfg)->filter(fn ($v) => filled($v))->isNotEmpty();
        }

        return [
            'mode' => config('payment.mode'),
            'gateways' => $gateways,
            'configured' => $configured,
        ];
    }
}
