<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Pages;

use App\Filament\Resources\PaymentGatewaySettings\PaymentGatewaySettingResource;
use App\Models\PaymentSetting;
use App\Services\Payment\PaymentConfiguration;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;

class ListPaymentGatewaySettings extends ListRecords
{
    protected static string $resource = PaymentGatewaySettingResource::class;

    protected function paymentSchemaReady(): bool
    {
        return app(PaymentConfiguration::class)->schemaReady();
    }

    public function content(Schema $schema): Schema
    {
        if (! $this->paymentSchemaReady()) {
            return $schema->components([
                Callout::make('payment_tables_missing')
                    ->title('جداول تنظیم درگاه هنوز روی سرور ساخته نشده')
                    ->description("بعد از به‌روزرسانی کد (PR #24) این دستورات را اجرا کنید:\n\nphp artisan migrate --force\nphp artisan optimize:clear\nphp artisan filament:optimize-clear")
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning'),
            ]);
        }

        return parent::content($schema);
    }

    protected function getHeaderActions(): array
    {
        if (! $this->paymentSchemaReady()) {
            return [];
        }

        return [
            Action::make('paymentMode')
                ->label('حالت پرداخت')
                ->icon('heroicon-o-adjustments-horizontal')
                ->fillForm(fn (): array => [
                    'payment_mode' => PaymentSetting::current()->payment_mode,
                ])
                ->form([
                    Select::make('payment_mode')
                        ->label('حالت')
                        ->required()
                        ->options([
                            'sandbox' => 'سندباکس (تست)',
                            'live' => 'واقعی (production)',
                        ])
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    PaymentSetting::current()->update([
                        'payment_mode' => $data['payment_mode'],
                    ]);

                    Notification::make()
                        ->title('حالت پرداخت ذخیره شد')
                        ->success()
                        ->send();
                }),
        ];
    }
}
