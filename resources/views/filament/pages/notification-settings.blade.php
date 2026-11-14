<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">درایور SMS</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-white">
                        <code>{{ $smsDriver }}</code>
                        @if ($smsDriver === 'log')
                            <span class="mr-2 text-sm text-amber-600">(کد OTP در لاگ سرور ذخیره می‌شود)</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ایمیل (Mailer)</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-white"><code>{{ $mailMailer }}</code></dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">فرستنده ایمیل</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ $mailFrom }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 text-sm text-gray-600 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            <p class="font-medium text-gray-900 dark:text-white">رویدادهای خودکار</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                <li>پرداخت موفق سفارش → SMS + ایمیل (در صورت پیکربندی)</li>
                <li>ارسال سفارش → SMS + ایمیل</li>
            </ul>
            <p class="mt-4 text-gray-500 dark:text-gray-400">
                متغیرها: <code>SHOP_SMS_DRIVER</code>, <code>MAIL_MAILER</code>, <code>MAIL_FROM_ADDRESS</code> در پنل هاست
            </p>
        </div>
    </div>
</x-filament-panels::page>