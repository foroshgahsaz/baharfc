<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-slate-900">حساب کاربری</h1>

    @if (session('account_status'))
        <div class="mt-4 rounded-2xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
            {{ session('account_status') }}
        </div>
    @endif

    <div class="mt-6 flex gap-2 border-b border-slate-200 pb-2">
        <button
            type="button"
            wire:click="setTab('profile')"
            class="rounded-xl px-4 py-2 text-sm font-medium transition {{ $activeTab === 'profile' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            پروفایل
        </button>
        <button
            type="button"
            wire:click="setTab('addresses')"
            class="rounded-xl px-4 py-2 text-sm font-medium transition {{ $activeTab === 'addresses' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            آدرس‌ها
        </button>
    </div>

    @if ($activeTab === 'profile')
        <form wire:submit="saveProfile" class="mt-8 space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">نام</label>
                <input id="name" type="text" wire:model="name" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20" />
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">ایمیل</label>
                <input id="email" type="email" wire:model="email" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20" dir="ltr" />
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700">موبایل</label>
                <input id="phone" type="text" wire:model="phone" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20" dir="ltr" />
                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="rounded-2xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
                ذخیره تغییرات
            </button>
        </form>
    @else
        <div class="mt-8">
            @unless ($showAddressForm)
                <button
                    type="button"
                    wire:click="openCreateAddress"
                    class="mb-6 rounded-2xl border border-dashed border-brand-300 px-4 py-3 text-sm font-medium text-brand-700 transition hover:bg-brand-50"
                >
                    + افزودن آدرس جدید
                </button>
            @endunless

            @if ($showAddressForm)
                <form wire:submit="saveAddress" class="mb-8 space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $editingAddressId ? 'ویرایش آدرس' : 'آدرس جدید' }}</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">عنوان</label>
                            <input type="text" wire:model="addressTitle" placeholder="مثلاً منزل" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                            @error('addressTitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">نام گیرنده</label>
                            <input type="text" wire:model="recipientName" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                            @error('recipientName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">تلفن</label>
                            <input type="text" wire:model="addressPhone" dir="ltr" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                            @error('addressPhone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">استان</label>
                            <select wire:model.live="province" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                <option value="">انتخاب استان</option>
                                @foreach (array_keys($provinces) as $provinceName)
                                    <option value="{{ $provinceName }}">{{ $provinceName }}</option>
                                @endforeach
                            </select>
                            @error('province') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">شهر</label>
                            <select wire:model="city" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" @disabled(blank($province))>
                                <option value="">انتخاب شهر</option>
                                @foreach ($cities as $cityName)
                                    <option value="{{ $cityName }}">{{ $cityName }}</option>
                                @endforeach
                            </select>
                            @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">آدرس</label>
                            <textarea wire:model="addressLine" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                            @error('addressLine') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">کد پستی</label>
                            <input type="text" wire:model="postalCode" dir="ltr" maxlength="10" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                            @error('postalCode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" wire:model="isDefault" class="rounded border-slate-300 text-brand-600" />
                            آدرس پیش‌فرض
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">ذخیره</button>
                        <button type="button" wire:click="cancelAddressForm" class="rounded-xl border border-slate-200 px-5 py-2 text-sm text-slate-600 hover:bg-slate-50">انصراف</button>
                    </div>
                </form>
            @endif

            <ul class="space-y-4">
                @forelse ($addresses as $address)
                    <li class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">
                                    {{ $address->title }}
                                    @if ($address->is_default)
                                        <span class="ms-2 rounded-lg bg-brand-100 px-2 py-0.5 text-xs text-brand-800">پیش‌فرض</span>
                                    @endif
                                </p>
                                <p class="mt-1 text-sm text-slate-600">{{ $address->recipient_name }} — {{ $address->phone }}</p>
                                <p class="mt-2 text-sm text-slate-700">{{ $address->fullLocation() }}</p>
                                <p class="text-sm text-slate-600">{{ $address->address_line }}</p>
                                @if ($address->postal_code)
                                    <p class="mt-1 text-xs text-slate-500" dir="ltr">کد پستی: {{ $address->postal_code }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @unless ($address->is_default)
                                    <button type="button" wire:click="makeDefault({{ $address->id }})" class="text-xs text-brand-700 hover:underline">پیش‌فرض</button>
                                @endunless
                                <button type="button" wire:click="openEditAddress({{ $address->id }})" class="text-xs text-slate-600 hover:underline">ویرایش</button>
                                <button type="button" wire:click="deleteAddress({{ $address->id }})" wire:confirm="این آدرس حذف شود؟" class="text-xs text-red-600 hover:underline">حذف</button>
                            </div>
                        </div>
                    </li>
                @empty
                    @unless ($showAddressForm)
                        <li class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                            هنوز آدرسی ثبت نشده است.
                        </li>
                    @endunless
                @endforelse
            </ul>
        </div>
    @endif
</div>
