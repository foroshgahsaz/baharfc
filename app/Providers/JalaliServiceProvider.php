<?php

namespace App\Providers;

use App\Support\JalaliDate;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class JalaliServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::directive('jalali', function (string $expression) {
            return "<?php echo \\App\\Support\\JalaliDate::format($expression) ?? '—'; ?>";
        });

        Blade::directive('jalaliDate', function (string $expression) {
            return "<?php echo \\App\\Support\\JalaliDate::date($expression) ?? '—'; ?>";
        });

        TextColumn::configureUsing(function (TextColumn $column): void {
            $name = (string) $column->getName();
            if (! $this->isDateAttribute($name)) {
                return;
            }

            $column
                ->formatStateUsing(fn ($state) => JalaliDate::dateTime($state) ?? '—')
                ->sortable();
        });

        TextEntry::configureUsing(function (TextEntry $entry): void {
            $name = (string) $entry->getName();
            if (! $this->isDateAttribute($name)) {
                return;
            }

            $entry->formatStateUsing(fn ($state) => JalaliDate::dateTime($state) ?? '—');
        });

        DateTimePicker::configureUsing(function (DateTimePicker $picker): void {
            $picker->helperText('ذخیره میلادی؛ نمایش در لیست و گزارش‌ها شمسی است.');
        });
    }

    protected function isDateAttribute(string $name): bool
    {
        return str_ends_with($name, '_at')
            || in_array($name, ['starts_at', 'ends_at', 'published_at', 'paid_at'], true);
    }
}