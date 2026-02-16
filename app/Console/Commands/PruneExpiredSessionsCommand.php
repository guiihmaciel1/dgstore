<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneExpiredSessionsCommand extends Command
{
    protected $signature = 'sessions:prune';
    protected $description = 'Remove sessões expiradas da tabela sessions';

    public function handle(): int
    {
        $lifetimeInMinutes = (int) config('session.lifetime', 120);
        $expiredBefore = now()->subMinutes($lifetimeInMinutes)->getTimestamp();

        $deleted = DB::table(config('session.table', 'sessions'))
            ->where('last_activity', '<', $expiredBefore)
            ->delete();

        if ($deleted > 0) {
            $this->info("{$deleted} sessão(ões) expirada(s) removida(s).");
        } else {
            $this->info('Nenhuma sessão expirada encontrada.');
        }

        return self::SUCCESS;
    }
}
