<?php

namespace App\Filament\Pages;

use App\Services\Admin\ApplicationErrorLogReader;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ErrorLogArchive extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'آرشیو خطاها';

    protected static ?string $title = 'خطاهای سیستم (۵ روز اخیر)';

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'error-logs';

    protected string $view = 'filament.pages.error-log-archive';

    public string $selectedDay = '';

    public function mount(ApplicationErrorLogReader $reader): void
    {
        $days = $reader->selectableDays();
        $this->selectedDay = $days[0] ?? now()->toDateString();
    }

    public function selectDay(string $day): void
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
            return;
        }

        $this->selectedDay = $day;
    }

    public function clearSelectedDay(ApplicationErrorLogReader $reader): void
    {
        $reader->clearDay($this->selectedDay);

        Notification::make()
            ->title('خطاهای روز انتخاب‌شده پاک شد')
            ->body('از این به بعد فقط خطاهای جدید برای این تاریخ ثبت می‌شوند.')
            ->success()
            ->send();
    }

  protected function getHeaderActions(): array
    {
        return [
            Action::make('clearDay')
                ->label('پاک کردن خطاهای این روز')
                ->color('danger')
                ->icon(Heroicon::OutlinedTrash)
                ->requiresConfirmation()
                ->modalHeading('پاک کردن خطاهای '.$this->selectedDay)
                ->modalDescription('تمام ورودی‌های خطا برای این روز از فایل لاگ حذف می‌شوند. این عمل قابل بازگشت نیست.')
                ->action(fn () => $this->clearSelectedDay(app(ApplicationErrorLogReader::class))),
        ];
    }

    public function getViewData(): array
    {
        $reader = app(ApplicationErrorLogReader::class);

        return [
            'days' => $reader->selectableDays(),
            'dayCounts' => collect($reader->selectableDays())
                ->mapWithKeys(fn (string $day) => [$day => $reader->errorCountForDay($day)])
                ->all(),
            'entries' => $reader->errorsForDay($this->selectedDay),
        ];
    }
}