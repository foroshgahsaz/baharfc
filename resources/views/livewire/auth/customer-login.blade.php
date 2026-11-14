<div class="mx-auto flex min-h-[calc(100vh-8rem)] max-w-lg flex-col justify-center px-4 py-12">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50">
        <div class="border-b border-slate-100 bg-gradient-to-l from-brand-600 to-brand-700 px-6 py-8 text-white">
            <h1 class="text-2xl font-bold">ورود به حساب کاربری</h1>
            <p class="mt-2 text-sm text-brand-100">با موبایل (OTP) یا نام کاربری و رمز عبور</p>
        </div>

        <div class="flex border-b border-slate-100">
            <button
                type="button"
                wire:click="switchMode('otp')"
                @class([
                    'flex-1 py-3 text-sm font-medium transition',
                    $mode === 'otp' ? 'bg-brand-50 text-brand-800' : 'text-slate-500 hover:bg-slate-50',
                ])
            >
                ورود با پیامک
            </button>
            <button
                type="button"
                wire:click="switchMode('password')"
                @class([
                    'flex-1 py-3 text-sm font-medium transition',
                    $mode === 'password' ? 'bg-brand-50 text-brand-800' : 'text-slate-500 hover:bg-slate-50',
                ])
            >
                نام کاربری و رمز
            </button>
        </div>

        <div class="space-y-4 p-6">
            @if (session('status'))
                <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($mode === 'otp')
                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">شماره موبایل</label>
                    <input
                        id="phone"
                        type="tel"
                        wire:model="phone"
                        dir="ltr"
                        placeholder="09123456789"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-left text-sm outline-none ring-brand-500/30 focus:border-brand-500 focus:ring-4"
                    />
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($otpSent)
                    <div>
                        <label for="code" class="mb-1 block text-sm font-medium text-slate-700">کد تأیید</label>
                        <input
                            id="code"
                            type="text"
                            inputmode="numeric"
                            wire:model="code"
                            dir="ltr"
                            maxlength="8"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-center text-lg tracking-[0.35em] outline-none ring-brand-500/30 focus:border-brand-500 focus:ring-4"
                        />
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="button"
                        wire:click="verifyOtp"
                        wire:loading.attr="disabled"
                        class="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="verifyOtp">تأیید و ورود</span>
                        <span wire:loading wire:target="verifyOtp">لطفاً صبر کنید…</span>
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="sendOtp"
                        wire:loading.attr="disabled"
                        class="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="sendOtp">دریافت کد ورود</span>
                        <span wire:loading wire:target="sendOtp">در حال ارسال…</span>
                    </button>
                @endif
            @else
                <div>
                    <label for="username" class="mb-1 block text-sm font-medium text-slate-700">نام کاربری</label>
                    <input
                        id="username"
                        type="text"
                        wire:model="username"
                        autocomplete="username"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none ring-brand-500/30 focus:border-brand-500 focus:ring-4"
                    />
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">رمز عبور</label>
                    <input
                        id="password"
                        type="password"
                        wire:model="password"
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none ring-brand-500/30 focus:border-brand-500 focus:ring-4"
                    />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="button"
                    wire:click="loginWithPassword"
                    wire:loading.attr="disabled"
                    class="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 disabled:opacity-60"
                >
                    ورود
                </button>
                <p class="text-center text-sm text-slate-500">
                    حساب ندارید؟
                    <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline">ثبت‌نام</a>
                </p>
            @endif

            <p class="text-center text-xs leading-relaxed text-slate-500">
                در محیط توسعه، کد OTP در فایل لاگ Laravel ذخیره می‌شود.
            </p>
        </div>
    </div>
</div>
