<?php

namespace App\Services\Admin;

use App\Support\PersistentBackupStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class BackupService
{
    public const TYPE_DATABASE = 'database';

    public const TYPE_FILES = 'files';

    /**
     * @return array{ok: bool, message: string, path?: string}
     */
    public function backupDatabase(): array
    {
        $ready = PersistentBackupStorage::ensureBackupsDirectory();
        if (! $ready['ok']) {
            return $ready;
        }

        $stamp = now()->format('Y-m-d-His');
        $filename = "db-{$stamp}.sql.gz";
        $path = PersistentBackupStorage::typeDirectory(self::TYPE_DATABASE).DIRECTORY_SEPARATOR.$filename;

        try {
            $sql = $this->exportDatabaseSql();
            File::put($path, gzencode($sql, 9) ?: $sql);
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'بک‌آپ دیتابیس ناموفق: '.$e->getMessage()];
        }

        $this->prune(self::TYPE_DATABASE);

        return [
            'ok' => true,
            'message' => 'بک‌آپ دیتابیس ذخیره شد.',
            'path' => $path,
        ];
    }

    /**
     * @return array{ok: bool, message: string, path?: string}
     */
    public function backupFiles(): array
    {
        $ready = PersistentBackupStorage::ensureBackupsDirectory();
        if (! $ready['ok']) {
            return $ready;
        }

        $source = $this->publicFilesRoot();
        if ($source === '' || ! is_dir($source)) {
            return ['ok' => false, 'message' => 'پوشهٔ فایل‌های آپلود پیدا نشد.'];
        }

        $stamp = now()->format('Y-m-d-His');
        $filename = "files-{$stamp}.zip";
        $path = PersistentBackupStorage::typeDirectory(self::TYPE_FILES).DIRECTORY_SEPARATOR.$filename;

        try {
            $this->zipDirectory($source, $path);
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'بک‌آپ فایل‌ها ناموفق: '.$e->getMessage()];
        }

        $this->prune(self::TYPE_FILES);

        return [
            'ok' => true,
            'message' => 'بک‌آپ فایل‌ها ذخیره شد.',
            'path' => $path,
        ];
    }

    public function prune(?string $type = null): int
    {
        $deleted = 0;
        $types = $type ? [$type] : [self::TYPE_DATABASE, self::TYPE_FILES];
        $keep = max(1, (int) config('backup.keep', 4));
        $retainSeconds = max(1, (int) config('backup.retain_days', 2)) * 86400;
        $cutoff = time() - $retainSeconds;

        foreach ($types as $currentType) {
            $items = $this->list($currentType);
            foreach ($items as $index => $item) {
                $tooOld = $item['mtime'] < $cutoff;
                $tooMany = $index >= $keep;
                if (! $tooOld && ! $tooMany) {
                    continue;
                }

                if (@unlink($item['path'])) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    /**
     * @return list<array{name: string, path: string, size: int, mtime: int, type: string}>
     */
    public function list(string $type): array
    {
        $directory = PersistentBackupStorage::typeDirectory($type);
        if (! is_dir($directory)) {
            return [];
        }

        $items = [];
        foreach (File::files($directory) as $file) {
            $name = $file->getFilename();
            if (! preg_match('/^(db|files)-\d{4}-\d{2}-\d{2}-\d{6}\.(sql\.gz|zip)$/', $name)) {
                continue;
            }

            $items[] = [
                'name' => $name,
                'path' => $file->getPathname(),
                'size' => $file->getSize(),
                'mtime' => $file->getMTime(),
                'type' => $type,
            ];
        }

        usort($items, fn (array $a, array $b): int => $b['mtime'] <=> $a['mtime']);

        return $items;
    }

    public function resolveDownloadPath(string $type, string $name): ?string
    {
        if (! in_array($type, [self::TYPE_DATABASE, self::TYPE_FILES], true)) {
            return null;
        }

        if (! preg_match('/^(db|files)-\d{4}-\d{2}-\d{2}-\d{6}\.(sql\.gz|zip)$/', $name)) {
            return null;
        }

        $path = PersistentBackupStorage::typeDirectory($type).DIRECTORY_SEPARATOR.$name;
        $real = realpath($path);
        $root = realpath(PersistentBackupStorage::typeDirectory($type));

        if ($real === false || $root === false || ! str_starts_with($real, $root)) {
            return null;
        }

        return is_file($real) ? $real : null;
    }

    public function delete(string $type, string $name): bool
    {
        $path = $this->resolveDownloadPath($type, $name);

        return $path !== null && @unlink($path);
    }

    public function formattedSize(int $bytes): string
    {
        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 1).' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    protected function publicFilesRoot(): string
    {
        return rtrim(Storage::disk('public')->path(''), '/\\');
    }

    protected function exportDatabaseSql(): string
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $dumped = $this->dumpMysqlWithBinary();
            if ($dumped !== null) {
                return $dumped;
            }
        }

        return $this->dumpWithPhp();
    }

    protected function dumpMysqlWithBinary(): ?string
    {
        $mysqldump = $this->findBinary('mysqldump');
        if ($mysqldump === null) {
            return null;
        }

        $config = config('database.connections.'.config('database.default'));
        $database = (string) ($config['database'] ?? '');
        if ($database === '') {
            return null;
        }

        $cnf = tempnam(sys_get_temp_dir(), 'baharfc-mycnf-');
        if ($cnf === false) {
            return null;
        }

        $contents = "[client]\n"
            .'user='.($config['username'] ?? '')."\n"
            .'password='.($config['password'] ?? '')."\n"
            .'host='.($config['host'] ?? '127.0.0.1')."\n"
            .'port='.($config['port'] ?? 3306)."\n";

        try {
            File::put($cnf, $contents);
            chmod($cnf, 0600);

            $result = Process::timeout(180)->run([
                $mysqldump,
                '--defaults-extra-file='.$cnf,
                '--single-transaction',
                '--quick',
                '--skip-lock-tables',
                $database,
            ]);

            if (! $result->successful()) {
                return null;
            }

            return $result->output();
        } finally {
            @unlink($cnf);
        }
    }

    protected function dumpWithPhp(): string
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $pdo = $connection->getPdo();
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $sql = "-- baharfc backup\nSET FOREIGN_KEY_CHECKS=0;\n";

        if ($driver === 'sqlite') {
            $sql = "-- baharfc sqlite backup\nPRAGMA foreign_keys=OFF;\nBEGIN;\n";
            $tables = $pdo->query("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll();
            foreach ($tables as $table) {
                $name = (string) $table['name'];
                $create = trim((string) ($table['sql'] ?? ''));
                if ($create !== '') {
                    $sql .= $create.";\n";
                }
                $sql .= $this->insertStatements($pdo, $name);
            }
            $sql .= "COMMIT;\nPRAGMA foreign_keys=ON;\n";

            return $sql;
        }

        $tables = $this->listConnectionTables($connection);
        sort($tables);

        foreach ($tables as $table) {
            $quoted = $this->quoteIdentifier($driver, $table);
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $create = $pdo->query('SHOW CREATE TABLE '.$quoted)->fetch();
                $createSql = $create['Create Table'] ?? $create['Create View'] ?? null;
                if (is_string($createSql)) {
                    $sql .= 'DROP TABLE IF EXISTS '.$quoted.";\n".$createSql.";\n";
                }
            }
            $sql .= $this->insertStatements($pdo, $table, $quoted);
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    protected function insertStatements(\PDO $pdo, string $table, ?string $quotedTable = null): string
    {
        $quotedTable ??= $this->quoteIdentifier(DB::connection()->getDriverName(), $table);
        $sql = '';
        $rows = $pdo->query('SELECT * FROM '.$quotedTable);
        if ($rows === false) {
            return '';
        }

        foreach ($rows as $row) {
            $columns = [];
            $values = [];
            foreach ($row as $column => $value) {
                $columns[] = $this->quoteIdentifier(DB::connection()->getDriverName(), (string) $column);
                $values[] = $value === null ? 'NULL' : $pdo->quote((string) $value);
            }
            $sql .= 'INSERT INTO '.$quotedTable.' ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).");\n";
        }

        return $sql;
    }

    /**
     * Laravel's getTableListing() defaults to schema-qualified names like
     * "bahardbxhl_db.addresses". Quoting that as one identifier makes MySQL
     * look for table bahardbxhl_db.addresses inside database bahardbxhl_db.
     *
     * @return list<string>
     */
    protected function listConnectionTables(\Illuminate\Database\Connection $connection): array
    {
        $builder = $connection->getSchemaBuilder();
        $tables = $builder->getTableListing(null, false);

        $names = [];
        foreach ($tables as $table) {
            $name = $this->unqualifiedTableName((string) $table);
            if ($name === '' || str_starts_with($name, 'sqlite_')) {
                continue;
            }
            $names[] = $name;
        }

        return array_values(array_unique($names));
    }

    public function unqualifiedTableName(string $name): string
    {
        $name = str_replace(['`', '"'], '', $name);

        if (str_contains($name, '.')) {
            return Str::afterLast($name, '.');
        }

        return $name;
    }

    protected function quoteIdentifier(string $driver, string $name): string
    {
        $name = $this->unqualifiedTableName($name);

        if ($driver === 'sqlite' || $driver === 'pgsql') {
            return '"'.str_replace('"', '""', $name).'"';
        }

        return '`'.str_replace('`', '``', $name).'`';
    }

    protected function zipDirectory(string $source, string $destination): void
    {
        $zip = new ZipArchive;
        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('ساخت فایل zip ممکن نیست.');
        }

        $source = realpath($source) ?: $source;
        $files = File::allFiles($source);
        if ($files === []) {
            $zip->addFromString('README.txt', "empty public disk\n");
        }

        foreach ($files as $file) {
            $full = $file->getPathname();
            $relative = ltrim(Str::after($full, $source), '/\\');
            if ($relative === '') {
                $relative = $file->getFilename();
            }
            $zip->addFile($full, str_replace('\\', '/', $relative));
        }

        $zip->close();
    }

    protected function findBinary(string $name): ?string
    {
        $result = Process::run(['which', $name]);
        if (! $result->successful()) {
            return null;
        }

        $path = trim($result->output());

        return $path !== '' ? $path : null;
    }
}
