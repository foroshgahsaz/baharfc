<div>
    @if ($show)
        <div class="checkout-modal-backdrop" wire:click="close"></div>
        <div
            class="checkout-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="checkout-address-title"
            data-checkout-map-root
        >
            <header class="checkout-modal-head">
                <h2 id="checkout-address-title">ثبت آدرس تحویل</h2>
                <button type="button" class="checkout-modal-close" wire:click="close" aria-label="بستن">×</button>
            </header>

            <form wire:submit="save" class="checkout-modal-body">
                <p class="checkout-modal-hint">آدرس را وارد کنید یا روی نقشه محل تحویل را مشخص کنید.</p>

                <div class="address-map-wrap" wire:ignore>
                    <div data-checkout-map class="address-map"></div>
                    <p class="address-map-note">برای انتخاب موقعیت روی نقشه کلیک کنید.</p>
                </div>

                <div class="checkout-form-grid">
                    <label class="shop-field">
                        <span>عنوان آدرس</span>
                        <input type="text" wire:model="addressTitle" class="shop-input">
                        @error('addressTitle') <span class="shop-field-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="shop-field">
                        <span>نام گیرنده</span>
                        <input type="text" wire:model="recipientName" class="shop-input">
                        @error('recipientName') <span class="shop-field-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="shop-field">
                        <span>موبایل</span>
                        <input type="text" wire:model="addressPhone" class="shop-input" dir="ltr">
                        @error('addressPhone') <span class="shop-field-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="shop-field">
                        <span>استان</span>
                        <select wire:model.live="province" class="shop-input">
                            <option value="">انتخاب کنید</option>
                            @foreach (array_keys($provinces) as $provinceName)
                                <option value="{{ $provinceName }}">{{ $provinceName }}</option>
                            @endforeach
                        </select>
                        @error('province') <span class="shop-field-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="shop-field">
                        <span>شهر</span>
                        <select wire:model="city" class="shop-input" @disabled($province === '')>
                            <option value="">انتخاب کنید</option>
                            @foreach ($cities as $cityName)
                                <option value="{{ $cityName }}">{{ $cityName }}</option>
                            @endforeach
                        </select>
                        @error('city') <span class="shop-field-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="shop-field shop-field-full">
                        <span>آدرس کامل</span>
                        <textarea wire:model="addressLine" rows="3" class="shop-input"></textarea>
                        @error('addressLine') <span class="shop-field-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="shop-field">
                        <span>کد پستی (اختیاری)</span>
                        <input type="text" wire:model="postalCode" class="shop-input" dir="ltr" maxlength="10">
                        @error('postalCode') <span class="shop-field-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="shop-check shop-field-full">
                        <input type="checkbox" wire:model="isDefault">
                        <span>آدرس پیش‌فرض</span>
                    </label>
                </div>

                <footer class="checkout-modal-foot">
                    <button type="button" class="shop-btn shop-btn-outline" wire:click="close">انصراف</button>
                    <button type="submit" class="shop-btn shop-btn-primary">ذخیره آدرس</button>
                </footer>
            </form>
        </div>
    @endif
</div>

@pushOnce('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endPushOnce