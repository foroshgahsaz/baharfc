<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Pages;

use App\Filament\Resources\PaymentGatewaySettings\PaymentGatewaySettingResource;
use App\Models\PaymentGatewaySetting;
use App\Services\Payment\PaymentConfiguration;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditPaymentGatewaySetting extends EditRecord
{
    protected static string $resource = PaymentGatewaySettingResource::class;

    public function mount(int | string $record): void
    {
        if (! app(PaymentConfiguration::class)->schemaReady()) {
            $this->redirect(PaymentGatewaySettingResource::getUrl('index'));

            return;
        }

        parent::mount($record);
    }

    public function form(Schema $schema): Schema
    {
        $slug = $this->getRecord()->slug;

        $credentialFields = match ($slug) {
            'mellat' => [
                TextInput::make('terminal_id')->label('Terminal ID')->required()->numeric(),
                TextInput::make('username')->label('نام کاربری')->required(),
                TextInput::make('password')->label('رمز عبور')->required()->password()->revealable(),
            ],
            'parsian' => [
                TextInput::make('pin')->label('PIN درگاه')->required()->password()->revealable(),
            ],
            'digipay' => [
                TextInput::make('client_id')->label('Client ID')->required(),
                TextInput::make('client_secret')->label('Client Secret')->required()->password()->revealable(),
            ],
            'snapppay' => [
                TextInput::make('merchant_id')->label('Merchant ID')->required(),
                TextInput::make('secret')->label('Secret')->required()->password()->revealable(),
            ],
            default => [],
        };

        return $schema->components([
            Toggle::make('is_enabled')->label('فعال در صفحه پرداخت فروشگاه')->default(true),
            ...$credentialFields,
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var PaymentGatewaySetting $record */
        $record = $this->getRecord();
        $credentials = $record->credentials ?? [];

        return array_merge($data, $credentials);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var PaymentGatewaySetting $record */
        $record = $this->getRecord();
        $keys = match ($record->slug) {
            'mellat' => ['terminal_id', 'username', 'password'],
            'parsian' => ['pin'],
            'digipay' => ['client_id', 'client_secret'],
            'snapppay' => ['merchant_id', 'secret'],
            default => [],
        };

        $credentials = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && filled($data[$key])) {
                $credentials[$key] = $data[$key];
            }

            unset($data[$key]);
        }

        $data['credentials'] = $credentials;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
