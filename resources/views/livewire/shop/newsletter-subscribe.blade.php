<div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
    <h3 class="text-base font-bold text-slate-900">خبرنامه</h3>
    <p class="mt-1 text-sm text-slate-600">از تخفیف‌ها و مقالات جدید باخبر شوید.</p>
    @if ($statusMessage)
        <p class="mt-3 text-sm text-brand-700">{{ $statusMessage }}</p>
    @endif
    <form wire:submit="subscribe" class="mt-4 flex flex-col gap-2 sm:flex-row">
        <input type="email" wire:model="email" placeholder="email@example.com" dir="ltr" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
        <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">عضویت</button>
    </form>
    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>
