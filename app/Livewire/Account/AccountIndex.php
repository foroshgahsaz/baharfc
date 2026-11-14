<?php

namespace App\Livewire\Account;

use App\Actions\Account\SetDefaultAddress;
use App\Http\Requests\Account\StoreAddressRequest;
use App\Http\Requests\Account\UpdateAddressRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Models\Address;
use App\Support\IranLocations;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.shop')]
class AccountIndex extends Component
{
    use AuthorizesRequests;

    public string $activeTab = 'profile';

    public string $name = '';

    public ?string $email = null;

    public ?string $phone = null;

    public bool $showAddressForm = false;

    public ?int $editingAddressId = null;

    public string $addressTitle = '';

    public string $recipientName = '';

    public string $addressPhone = '';

    public string $province = '';

    public string $city = '';

    public string $addressLine = '';

    public ?string $postalCode = null;

    public bool $isDefault = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['profile', 'addresses'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function saveProfile(): void
    {
        $messages = (new UpdateProfileRequest)->messages();
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore(Auth::id())],
        ], $messages);

        Auth::user()->update($validated);

        session()->flash('account_status', 'پروفایل با موفقیت ذخیره شد.');
    }

    public function openCreateAddress(): void
    {
        $this->resetAddressForm();
        $this->showAddressForm = true;
        $this->editingAddressId = null;
        $this->recipientName = Auth::user()->name;
        $this->addressPhone = Auth::user()->phone ?? '';
    }

    public function openEditAddress(int $addressId): void
    {
        $address = Address::query()->findOrFail($addressId);
        $this->authorize('update', $address);

        $this->editingAddressId = $address->id;
        $this->addressTitle = $address->title;
        $this->recipientName = $address->recipient_name;
        $this->addressPhone = $address->phone;
        $this->province = $address->province;
        $this->city = $address->city;
        $this->addressLine = $address->address_line;
        $this->postalCode = $address->postal_code;
        $this->isDefault = $address->is_default;
        $this->showAddressForm = true;
    }

    public function updatedProvince(): void
    {
        $cities = IranLocations::citiesForProvince($this->province);
        if ($this->city !== '' && ! in_array($this->city, $cities, true)) {
            $this->city = '';
        }
    }

    public function saveAddress(): void
    {
        $rules = $this->editingAddressId
            ? (new UpdateAddressRequest)->rules()
            : (new StoreAddressRequest)->rules();
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
        ], [
            'addressTitle' => $messages['title.required'] ?? 'عنوان آدرس را وارد کنید.',
            'recipientName.required' => $messages['recipient_name.required'],
            'addressPhone.required' => $messages['phone.required'],
            'province.required' => $messages['province.required'],
            'city.required' => $messages['city.required'],
            'addressLine.required' => $messages['address_line.required'],
            'postalCode.regex' => $messages['postal_code.regex'] ?? '',
        ]);

        $payload = [
            'title' => $validated['addressTitle'],
            'recipient_name' => $validated['recipientName'],
            'phone' => $validated['addressPhone'],
            'province' => $validated['province'],
            'city' => $validated['city'],
            'address_line' => $validated['addressLine'],
            'postal_code' => $validated['postalCode'],
            'is_default' => $validated['isDefault'] ?? false,
        ];

        $user = Auth::user();

        if ($this->editingAddressId) {
            $address = Address::query()->findOrFail($this->editingAddressId);
            $this->authorize('update', $address);
            $address->update($payload);
        } else {
            $this->authorize('create', Address::class);
            $address = $user->addresses()->create($payload);
        }

        if ($payload['is_default']) {
            app(SetDefaultAddress::class)->execute($user, $address);
        }

        $this->showAddressForm = false;
        $this->resetAddressForm();
        session()->flash('account_status', 'آدرس ذخیره شد.');
    }

    public function deleteAddress(int $addressId): void
    {
        $address = Address::query()->findOrFail($addressId);
        $this->authorize('delete', $address);
        $address->delete();
        session()->flash('account_status', 'آدرس حذف شد.');
    }

    public function makeDefault(int $addressId, SetDefaultAddress $action): void
    {
        $address = Address::query()->findOrFail($addressId);
        $this->authorize('update', $address);
        $action->execute(Auth::user(), $address);
        session()->flash('account_status', 'آدرس پیش‌فرض تنظیم شد.');
    }

    public function cancelAddressForm(): void
    {
        $this->showAddressForm = false;
        $this->resetAddressForm();
    }

    protected function resetAddressForm(): void
    {
        $this->reset([
            'editingAddressId',
            'addressTitle',
            'recipientName',
            'addressPhone',
            'province',
            'city',
            'addressLine',
            'postalCode',
            'isDefault',
        ]);
    }

    public function render()
    {
        return view('livewire.account.account-index', [
            'addresses' => Auth::user()->addresses()->latest()->get(),
            'provinces' => IranLocations::provinces(),
            'cities' => IranLocations::citiesForProvince($this->province),
        ]);
    }
}
