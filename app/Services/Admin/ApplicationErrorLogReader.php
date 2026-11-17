<?php

namespace App\Services\Admin;

use App\Support\PersistentLogStorage;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApplicationErrorLogReader
{
    public const ARCHIVE_DAYS = 5;

    /**
     * @return list<string> Y-m-d dates, newest first (up to 5 days with content or last 5 calendar days)
     */
    public function selectableDays(): array
    {
        $daysWithErrors = [];
        foreach ($this->logPaths() as $path) {
            foreach ($this->extractDayKeysFromFile($path) as $day) {
                $daysWithErrors[$day] = true;
            }
        }

        $calendar = collect();
        for ($i = 0; $i < self::ARCHIVE_DAYS; $i++) {
            $calendar->push(Carbon::today()->subDays($i)->toDateString());
        }

        $merged = $calendar
            ->merge(array_keys($daysWithErrors))
            ->unique()
            ->sortDesc()
            ->values()
            ->take(self::ARCHIVE_DAYS)
            ->all();

        return $merged;
    }

    /**
     * @return list<array{at: string, level: string, message: string, preview: string}>
     */
    public function errorsForDay(string $day): array
    {
        $entries = [];
        foreach ($this->logPaths() as $path) {
            $entries = array_merge($entries, $this->parseFile($path, $day, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']));
        }

        usort($entries, fn (array $a, array $b) => strcmp($b['at'], $a['at']));

        return $entries;
    }

    public function errorCountForDay(string $day): int
    {
        return count($this->errorsForDay($day));
    }

    public function clearDay(string $day): void
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
            return;
        }

        foreach ($this->logPaths() as $path) {
            if (Str::endsWith($path, "laravel-{$day}.log")) {
                File::put($path, '');

                continue;
            }

            if (Str::endsWith($path, 'laravel.log')) {
                $this->removeDayFromSingleLog($path, $day);
            }
        }

        $this->pruneOlderThanArchiveWindow();
    }

    /**
     * @return list<string>
     */
    protected function logPaths(): array
    {
        $base = PersistentLogStorage::logsRoot();
        $paths = [];

        $single = $base.'/laravel.log';
        if (is_file($single)) {
            $paths[] = $single;
        }

        $dated = glob($base.'/laravel-*.log') ?: [];
        rsort($dated);
        foreach ($dated as $file) {
            if (is_file($file)) {
                $paths[] = $file;
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @param  list<string>  $levels
     * @return list<array{at: string, level: string, message: string, preview: string}>
     */
    protected function parseFile(string $path, string $day, array $levels): array
    {
        if (! is_readable($path) || filesize($path) === 0) {
            return [];
        }

        $content = $this->readTail($path, 2_000_000);
        $blocks = preg_split('/(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/', $content, -1, PREG_SPLIT_NO_EMPTY);

        if ($blocks === false) {
            return [];
        }

        $entries = [];
        $levelPattern = implode('|', $levels);

        foreach ($blocks as $block) {
            if (! preg_match('/^\[(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})\] [^\s]+\.('.$levelPattern.'): (.*)$/s', trim($block), $matches)) {
                continue;
            }

            if ($matches[1] !== $day) {
                continue;
            }

            $messageLine = trim(Str::before($matches[4], "\n"));
            $preview = Str::limit(preg_replace('/\s+/', ' ', $block), 2000);

            $entries[] = [
                'at' => "{$matches[1]} {$matches[2]}",
                'level' => $matches[3],
                'message' => $messageLine,
                'preview' => $preview,
            ];
        }

        return $entries;
    }

    /**
     * @return list<string>
     */
    protected function extractDayKeysFromFile(string $path): array
    {
        if (! is_readable($path)) {
            return [];
        }

        if (preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log$/', $path, $m)) {
            return [$m[1]];
        }

        $content = $this->readTail($path, 512_000);
        preg_match_all('/\[(\d{4}-\d{2}-\d{2}) /', $content, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    protected function removeDayFromSingleLog(string $path, string $day): void
    {
        if (! is_writable($path)) {
            return;
        }

        $content = File::get($path);
        $blocks = preg_split('/(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/', $content, -1, PREG_SPLIT_NO_EMPTY);

        if ($blocks === false) {
            return;
        }

        $kept = [];
        foreach ($blocks as $block) {
            if (str_starts_with($block, "[{$day} ")) {
                continue;
            }
            $kept[] = $block;
        }

        File::put($path, implode('', $kept));
    }

    protected function pruneOlderThanArchiveWindow(): void
    {
        $cutoff = Carbon::today()->subDays(self::ARCHIVE_DAYS)->toDateString();
        foreach (glob(PersistentLogStorage::logsRoot().'/laravel-*.log') ?: [] as $file) {
            if (! preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log$/', $file, $m)) {
                continue;
            }
            if ($m[1] < $cutoff) {
                @unlink($file);
            }
        }
    }

    protected function readTail(string $path, int $maxBytes): string
    {
        $size = filesize($path);
        if ($size === false || $size <= $maxBytes) {
            return (string) file_get_contents($path);
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return '';
        }

        fseek($handle, -$maxBytes, SEEK_END);
        $data = fread($handle, $maxBytes);
        fclose($handle);

        return $data === false ? '' : $data;
    }
}