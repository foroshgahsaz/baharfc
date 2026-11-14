<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                حالت فعلی: <strong>{{ $mode === 'live' ? 'واقعی (live)' : 'سندباکس (sandbox)' }}</strong>
                — متغیر <code class="text-xs">PAYMENT_MODE</code> در پنل هاست
            </p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                کلیدهای درگاه در دیتابیس ذخیره نمی‌شوند؛ از متغیرهای محیطی (پنل) تنظیم می‌شوند. تراکنش‌ها در منوی «پرداخت‌ها» قابل مشاهده‌اند.
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <table class="w-full text-start text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">درگاه</th>
                        <th class="px-4 py-3 font-medium">وضعیت پیکربندی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gateways as $key => $label)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-3">{{ $label }}</td>
                            <td class="px-4 py-3">
                                @if ($configured[$key] ?? false)
                                    <span class="text-emerald-600 dark:text-emerald-400">متغیرهای env تنظیم شده</span>
                                @else
                                    <span class="text-amber-600 dark:text-amber-400">نیاز به تنظیم در پنل (سندباکس تا آن موقع)</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <pre class="overflow-x-auto rounded-xl bg-gray-900 p-4 text-xs text-gray-100"><code>MELLAT_TERMINAL_ID, MELLAT_USERNAME, MELLAT_PASSWORD
PARSIAN_PIN
DIGIPAY_CLIENT_ID, DIGIPAY_CLIENT_SECRET
SNAPPPAY_MERCHANT_ID, SNAPPPAY_SECRET</code></pre>
    </div>
</x-filament-panels::page>