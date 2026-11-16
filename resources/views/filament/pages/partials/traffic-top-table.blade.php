<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
    <table class="mt-3 w-full text-sm">
        <thead>
            <tr class="text-right text-xs text-gray-500">
                <th class="pb-2 font-medium">عنوان</th>
                <th class="pb-2 font-medium">بازدید</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <td class="py-2 pe-2">{{ $row->label }}</td>
                    <td class="py-2 font-mono text-xs">{{ number_format($row->total_views) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="py-4 text-center text-gray-400">داده‌ای در این بازه نیست.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>