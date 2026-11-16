<?php

namespace App\Livewire\Shop;

use App\Actions\Account\SaveUserAddress;
use App\Http\Requests\Account\StoreAddressRequest;
use App\Support\IranLocations;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CheckoutAddressModal extends Component
{
    use AuthorizesRequests;

    public bool $show = false;

    public string $addressTitle = 'منزل';

    public string $recipientName = '';

    public string $addressPhone = '';

    public string $province = '';

    public string $city = '';

    public string $addressLine = '';

    public ?string $postalCode = null;

    public bool $isDefault = true;

    public ?float $latitude = null;

    public ?float $longitude = null;

    #[On('open-checkout-address')]
    public function open(): void
    {
        $user = Auth::user();
        $this->resetForm();
        $this->recipientName = $user->name;
        $this->addressPhone = $user->phone ?? '';
        $this->show = true;
        $this->dispatch('checkout-map-init');
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function updatedProvince(): void
    {
        $cities = IranLocations::citiesForProvince($this->province);
        if ($this->city !== '' && ! in_array($this->city, $cities, true)) {
            $this->city = '';
        }
    }

    public function save(SaveUserAddress $action): void
    {
        $rules = (new StoreAddressRequest)->rules();
        $messages = (new StoreAddressRequest)->messages();

        $validated = $this->validate([
            'addressTitle' => $rules['title'],
            'recipientName' => $rules['recipient_name'],
            'addressPhone' => $rules['phone'],
            'province' => $rules['province'],
            'city' => $rules['city'],
            'addressLine' => $rules['address_line'],
            'postalCode' => $rules['postal_code'],
            'isDefault' => $rules['is_default'] ?? ['sometimes', 'boolean'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'addressTitle.required' => $messages['title.required'] ?? 'عنوان آدرس را وارد کنید.',
            'recipientName.required' => $messages['recipient_name.required'],
            'addressPhone.required' => $messages['phone.required'],
            'province.required' => $messages['province.required'],
            'city.required' => $messages['city.required'],
            'addressLine.required' => $messages['address_line.required'],
            'postalCode.regex' => $messages['postal_code.regex'] ?? '',
        ]);

        $this->authorize('create', \App\Models\Address::class);

        $address = $action->execute(Auth::user(), [
            'title' => $validated['addressTitle'],
            'recipient_name' => $validated['recipientName'],
            'phone' => $validated['addressPhone'],
            'province' => $validated['province'],
            'city' => $validated['city'],
            'address_line' => $validated['addressLine'],
            'postal_code' => $validated['postalCode'],
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_default' => $validated['isDefault'] ?? true,
        ]);

        $this->show = false;
        $this->dispatch('checkout-address-saved', addressId: $address->id);
    }

    protected function resetForm(): void
    {
        $this->reset([
            'addressTitle',
            'recipientName',
            'addressPhone',
            'province',
            'city',
            'addressLine',
            'postalCode',
            'isDefault',
            'latitude',
            'longitude',
        ]);
        $this->addressTitle = 'منزل';
        $this->isDefault = true;
    }

    public function render()
    {
        return view('livewire.shop.checkout-address-modal', [
            'provinces' => IranLocations::provinces(),
            'cities' => IranLocations::citiesForProvince($this->province),
        ]);
    }
}