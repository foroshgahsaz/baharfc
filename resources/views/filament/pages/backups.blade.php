<x-filament-panels::page>
    @php
        $databaseBackups = $databaseBackups ?? [];
        $fileBackups = $fileBackups ?? [];
        $backupsRoot = $backupsRoot ?? '';
        $keep = $keep ?? 4;
        $retainDays = $retainDays ?? 2;
        $service = $service ?? app(\App\Services\Admin\BackupService::class);
    @endphp

    <div class="space-y-6" dir="rtl">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <p class="text-sm leading-7 text-gray-600 dark:text-gray-300">
                بک‌آپ‌ها روی دیسک پایدار ذخیره می‌شوند تا بعد از دیپلوی پاک نشوند.
                برای هر نوع (دیتابیس / فایل) حداکثر <strong>{{ $keep }}</strong> نسخه و تا <strong>{{ $retainDays }} روز</strong> نگه داشته می‌شود؛ نسخه‌های قدیمی‌تر خودکار حذف می‌گردند.
            </p>
            <p class="mt-2 font-mono text-xs text-gray-500" dir="ltr">{{ $backupsRoot }}</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ([
                ['title' => 'دیتابیس', 'items' => $databaseBackups, 'type' => 'database', 'empty' => 'هنوز بک‌آپ دیتابیس ندارید. از دکمه «بک‌آپ دستی دیتابیس» استفاده کنید.'],
                ['title' => 'فایل‌ها و تصاویر', 'items' => $fileBackups, 'type' => 'files', 'empty' => 'هنوز بک‌آپ فایل ندارید. از دکمه «بک‌آپ دستی فایل‌ها» استفاده کنید.'],
            ] as $section)
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                        <h3 class="font-semibold text-gray-900 dark:text-white">آرشیو {{ $section['title'] }}</h3>
                        <p class="text-xs text-gray-500">{{ count($section['items']) }} از {{ $keep }} نسخه</p>
                    </div>
                    @if (count($section['items']) === 0)
                        <p class="p-6 text-center text-sm text-gray-500">{{ $section['empty'] }}</p>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($section['items'] as $item)
                                <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                                    <div>
                                        <p class="font-mono text-xs text-gray-500" dir="ltr">{{ $item['name'] }}</p>
                                        <p class="text-sm text-gray-800 dark:text-gray-200">
                                            {{ \App\Support\JalaliDate::dateTime(date('c', $item['mtime'])) ?? '—' }}
                                            · {{ $service->formattedSize($item['size']) }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            wire:click="downloadBackup('{{ $section['type'] }}', '{{ $item['name'] }}')"
                                        >
                                            دانلود
                                        </x-filament::button>
                                        <x-filament::button
                                            size="sm"
                                            color="danger"
                                            wire:click="deleteBackup('{{ $section['type'] }}', '{{ $item['name'] }}')"
                                            wire:confirm="این آرشیو حذف شود؟"
                                        >
                                            حذف
                                        </x-filament::button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
