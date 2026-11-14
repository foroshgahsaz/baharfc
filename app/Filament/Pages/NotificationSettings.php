<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class NotificationSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static ?string $navigationLabel = 'اعلان‌ها';

    protected static ?string $title = 'تنظیمات اعلان (SMS / ایمیل)';

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.notification-settings';

    public function getViewData(): array
    {
        return [
            'smsDriver' => config('shop.sms.driver'),
            'mailMailer' => config('mail.default'),
            'mailFrom' => config('mail.from.address'),
        ];
    }
}
