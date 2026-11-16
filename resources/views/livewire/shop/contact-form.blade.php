<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    @if ($statusMessage)
        <p class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ $statusMessage }}</p>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">نام و نام خانوادگی</label>
            <input type="text" wire:model="name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">ایمیل</label>
                <input type="email" wire:model="email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if ($settings->contact_form_show_phone)
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">تلفن</label>
                    <input type="tel" wire:model="phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        @if ($settings->contact_form_show_subject)
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">موضوع</label>
                <input type="text" wire:model="subject" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                @error('subject')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">پیام شما</label>
            <textarea wire:model="message" rows="5" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            @error('message')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-700"
            wire:loading.attr="disabled"
        >
            ارسال پیام
        </button>
    </form>
</div>