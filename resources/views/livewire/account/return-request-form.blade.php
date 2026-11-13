<div class="mt-8 space-y-8">
    @if (session('return_status'))
        <div class="rounded-2xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
            {{ session('return_status') }}
        </div>
    @endif

    <form wire:submit="submit" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold text-slate-900">درخواست مرجوعی (RMA)</h2>
        <div>
            <label class="block text-sm font-medium text-slate-700">سفارش</label>
            <select wire:model="orderId" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                <option value="">انتخاب کنید</option>
                @foreach ($orders as $order)
                    <option value="{{ $order->id }}">{{ $order->number }} — {{ $order->statusLabel() }}</option>
                @endforeach
            </select>
            @error('orderId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">دلیل مرجوعی</label>
            <textarea wire:model="reason" rows="4" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm"></textarea>
            @error('reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="rounded-2xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
            ثبت درخواست
        </button>
    </form>

    @if ($requests->isNotEmpty())
        <div>
            <h3 class="mb-4 text-base font-bold text-slate-900">درخواست‌های قبلی</h3>
            <ul class="space-y-3">
                @foreach ($requests as $request)
                    <li class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-medium">{{ $request->order->number }}</span>
                            <span class="rounded-lg bg-slate-100 px-2 py-1 text-xs">{{ $request->status->label() }}</span>
                        </div>
                        <p class="mt-2 text-slate-600">{{ $request->reason }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
