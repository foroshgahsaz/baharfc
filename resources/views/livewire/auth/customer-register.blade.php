<div class="mx-auto flex min-h-[calc(100vh-8rem)] max-w-lg flex-col justify-center px-4 py-12">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50">
        <div class="border-b border-slate-100 px-6 py-8">
            <h1 class="text-2xl font-bold text-slate-900">ثبت‌نام با نام کاربری</h1>
            <p class="mt-2 text-sm text-slate-500">برای ورود با رمز عبور، ابتدا حساب بسازید.</p>
        </div>
        <form wire:submit="register" class="space-y-4 p-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">نام و نام خانوادگی</label>
                <input wire:model="name" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/30" />
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">نام کاربری</label>
                <input wire:model="username" type="text" dir="ltr" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/30" />
                @error('username') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">موبایل (اختیاری)</label>
                <input wire:model="phone" type="tel" dir="ltr" placeholder="09123456789" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/30" />
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">رمز عبور</label>
                <input wire:model="password" type="password" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/30" />
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">تکرار رمز عبور</label>
                <input wire:model="password_confirmation" type="password" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/30" />
            </div>
            <button type="submit" class="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                ایجاد حساب
            </button>
            <p class="text-center text-sm text-slate-500">
                حساب دارید؟
                <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline">ورود</a>
            </p>
        </form>
    </div>
</div>
