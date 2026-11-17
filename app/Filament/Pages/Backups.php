<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ChecksAdminPageAccess;
use App\Services\Admin\BackupService;
use App\Support\PersistentBackupStorage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class Backups extends Page
{
    use ChecksAdminPageAccess;

    protected static function adminPagePermission(): string
    {
        return 'settings.backups';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $navigationLabel = 'بک‌آپ';

    protected static ?string $title = 'بک‌آپ دیتابیس و فایل‌ها';

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 48;

    protected static ?string $slug = 'backups';

    protected string $view = 'filament.pages.backups';

    public function createDatabaseBackup(): void
    {
        @set_time_limit(180);
        $result = app(BackupService::class)->backupDatabase();
        $this->notifyResult($result);
    }

    public function createFilesBackup(): void
    {
        @set_time_limit(180);
        $result = app(BackupService::class)->backupFiles();
        $this->notifyResult($result);
    }

    public function downloadBackup(string $type, string $name): BinaryFileResponse
    {
        $path = app(BackupService::class)->resolveDownloadPath($type, $name);
        abort_unless(is_string($path), 404);

        return response()->download($path, $name);
    }

    public function deleteBackup(string $type, string $name): void
    {
        $ok = app(BackupService::class)->delete($type, $name);

        $notification = Notification::make()
            ->title($ok ? 'آرشیو حذف شد' : 'حذف ناموفق بود');

        if ($ok) {
            $notification->success()->send();

            return;
        }

        $notification->danger()->send();
    }

    /**
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        $service = app(BackupService::class);
        PersistentBackupStorage::ensureBackupsDirectory();

        return [
            'databaseBackups' => $service->list(BackupService::TYPE_DATABASE),
            'fileBackups' => $service->list(BackupService::TYPE_FILES),
            'backupsRoot' => PersistentBackupStorage::backupsRoot(),
            'keep' => (int) config('backup.keep', 4),
            'retainDays' => (int) config('backup.retain_days', 2),
            'service' => $service,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backupDatabase')
                ->label('بک‌آپ دستی دیتابیس')
                ->icon(Heroicon::OutlinedCircleStack)
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('بک‌آپ دستی دیتابیس')
                ->modalDescription('یک نسخه از پایگاه داده روی دیسک پایدار ذخیره می‌شود. حداکثر ۴ نسخه و تا ۲ روز نگه داشته می‌شود.')
                ->action(fn () => $this->createDatabaseBackup()),
            Action::make('backupFiles')
                ->label('بک‌آپ دستی فایل‌ها')
                ->icon(Heroicon::OutlinedArchiveBox)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('بک‌آپ دستی فایل‌ها')
                ->modalDescription('تصاویر و فایل‌های آپلود روی دیسک پایدار زیپ می‌شوند. حداکثر ۴ نسخه و تا ۲ روز نگه داشته می‌شود.')
                ->action(fn () => $this->createFilesBackup()),
        ];
    }

    /**
     * @param  array{ok: bool, message: string, path?: string}  $result
     */
    protected function notifyResult(array $result): void
    {
        $notification = Notification::make()->title($result['message']);

        if ($result['ok']) {
            $notification->success()->send();

            return;
        }

        $notification->danger()->send();
    }
}
