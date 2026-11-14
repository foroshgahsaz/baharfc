<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Pages;

use App\Filament\Resources\PaymentGatewaySettings\PaymentGatewaySettingResource;
use App\Models\PaymentSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPaymentGatewaySettings extends ListRecords
{
    protected static string $resource = PaymentGatewaySettingResource::class;

    protected function getHeaderActions(): array
    {
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
