<x-filament-panels::page>
    @php
        $days = $days ?? [];
        $dayCounts = $dayCounts ?? [];
        $entries = $entries ?? [];
    @endphp

    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                خطاهای <strong>ERROR</strong> و سطوح بالاتر تا <strong>۵ روز</strong> نگه داشته می‌شوند. روز را انتخاب کنید؛ برای شروع دوباره همان روز، از دکمهٔ «پاک کردن خطاهای این روز» استفاده کنید.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ($days as $day)
                @php
                    $isActive = $selectedDay === $day;
                    $count = $dayCounts[$day] ?? 0;
                    $isToday = $day === now()->toDateString();
                @endphp
                <button
                    type="button"
                    wire:click="selectDay('{{ $day }}')"
                    @class([
                        'rounded-xl border px-4 py-2 text-sm font-medium transition',
                        'border-primary-500 bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-300' => $isActive,
                        'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200' => ! $isActive,
                    ])
                >
                    {{ $isToday ? 'امروز' : (\App\Support\JalaliDate::date($day) ?? $day) }}
                    <span class="mr-1 text-xs opacity-80">({{ $count }})</span>
                </button>
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">
                    خطاهای @jalaliDate($selectedDay)
                </h3>
            </div>

            @if (count($entries) === 0)
                <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">برای این روز خطایی ثبت نشده است.</p>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($entries as $index => $entry)
                        <li class="p-4" wire:key="err-{{ $selectedDay }}-{{ $index }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-mono text-xs text-gray-500 dark:text-gray-400" dir="ltr">{{ \App\Support\JalaliDate::dateTime($entry['at']) ?? $entry['at'] }}</span>
                                <span class="rounded-md bg-danger-50 px-2 py-0.5 text-xs font-semibold text-danger-700 dark:bg-danger-500/10 dark:text-danger-300">
                                    {{ $entry['level'] }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $entry['message'] }}</p>
                            <details class="mt-2">
                                <summary class="cursor-pointer text-xs text-primary-600 dark:text-primary-400">جزئیات کامل</summary>
                                <pre class="mt-2 max-h-64 overflow-auto rounded-lg bg-gray-50 p-3 text-xs leading-relaxed text-gray-800 dark:bg-gray-950 dark:text-gray-200" dir="ltr">{{ $entry['preview'] }}</pre>
                            </details>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-filament-panels::page>