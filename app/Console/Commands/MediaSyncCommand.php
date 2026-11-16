<?php

namespace App\Console\Commands;

use App\Services\Admin\MediaLibraryService;
use Illuminate\Console\Command;

class MediaSyncCommand extends Command
{
    protected $signature = 'media:sync';

    protected $description = 'فایل‌های موجود روی دیسک public را در کتابخانهٔ رسانه ثبت می‌کند';

    public function handle(MediaLibraryService $service): int
    {
        $count = $service->syncExistingFilesOnDisk();
        $this->components->info("{$count} فایل جدید به کتابخانه اضافه شد.");

        return self::SUCCESS;
    }
}