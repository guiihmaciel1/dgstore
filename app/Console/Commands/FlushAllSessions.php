<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlushAllSessions extends Command
{
    protected $signature = 'sessions:flush-all';

    protected $description = 'Remove todas as sessões ativas, deslogando todos os usuários';

    public function handle(): int
    {
        $deleted = DB::table('sessions')->delete();

        $this->info("✓ {$deleted} sessões removidas. Todos os usuários foram deslogados.");

        return self::SUCCESS;
    }
}
