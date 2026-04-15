<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup diário do banco MySQL, mantendo apenas os 2 últimos backups';

    private const MAX_BACKUPS = 2;
    private const BACKUP_DIR = 'backups';

    public function handle(): int
    {
        $database = config('database.connections.mysql.database');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $filename = sprintf('backup_%s_%s.sql.gz', $database, now()->format('Y-m-d_His'));
        $backupPath = storage_path(self::BACKUP_DIR);

        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $fullPath = $backupPath . '/' . $filename;

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s | gzip > %s',
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($fullPath)
        );

        $this->info("Iniciando backup de '{$database}'...");

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($fullPath) || filesize($fullPath) === 0) {
            $this->error('Falha ao criar o backup.');
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            return Command::FAILURE;
        }

        $sizeMb = round(filesize($fullPath) / 1024 / 1024, 2);
        $this->info("Backup criado: {$filename} ({$sizeMb} MB)");

        $this->pruneOldBackups($backupPath);

        return Command::SUCCESS;
    }

    private function pruneOldBackups(string $backupPath): void
    {
        $files = glob($backupPath . '/backup_*.sql.gz');

        if ($files === false || count($files) <= self::MAX_BACKUPS) {
            return;
        }

        usort($files, fn(string $a, string $b) => filemtime($b) <=> filemtime($a));

        $toDelete = array_slice($files, self::MAX_BACKUPS);

        foreach ($toDelete as $file) {
            unlink($file);
            $this->info('Backup antigo removido: ' . basename($file));
        }
    }
}
