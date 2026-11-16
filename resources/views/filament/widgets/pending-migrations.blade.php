<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            ⚠ مایگریشن اجرا نشده — خطای ۵۰۰ محتمل است
        </x-slot>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
            کد جدید روی سرور است اما {{ $count }} مایگریشن هنوز در دیتابیس اجرا نشده است. حتماً داخل پاد/سرور این دستور را بزنید:
        </p>

        <pre class="text-sm rounded-lg bg-gray-950 text-gray-100 p-3 overflow-x-auto" dir="ltr">php artisan baharfc:post-deploy --seed-menu</pre>

        @if (count($pending) <= 8)
            <ul class="mt-3 text-xs text-gray-500 list-disc ps-5">
                @foreach ($pending as $name)
                    <li dir="ltr">{{ $name }}</li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>