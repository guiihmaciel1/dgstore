<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\CRM\Models\Deal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueDealsCommand extends Command
{
    protected $signature = 'crm:check-overdue-deals';
    protected $description = 'Loga deals do CRM que estão com prazo de fechamento vencido';

    public function handle(): int
    {
        $overdueDeals = Deal::open()
            ->whereNotNull('expected_close_date')
            ->where('expected_close_date', '<', today())
            ->with(['user', 'stage', 'customer'])
            ->get();

        if ($overdueDeals->isEmpty()) {
            $this->info('Nenhum deal atrasado encontrado.');
            return self::SUCCESS;
        }

        $this->info("{$overdueDeals->count()} deal(s) atrasado(s):");

        foreach ($overdueDeals as $deal) {
            $daysOverdue = $deal->expected_close_date->diffInDays(today());
            $seller = $deal->user?->name ?? 'Sem vendedor';
            $customer = $deal->customer?->name ?? 'Sem cliente';

            $this->warn("  [{$seller}] {$deal->title} — cliente: {$customer} — {$daysOverdue} dia(s) de atraso");
        }

        Log::info("CRM: {$overdueDeals->count()} deal(s) atrasado(s) detectado(s).", [
            'deals' => $overdueDeals->map(fn (Deal $d) => [
                'id' => $d->id,
                'title' => $d->title,
                'seller' => $d->user?->name,
                'days_overdue' => $d->expected_close_date->diffInDays(today()),
            ])->toArray(),
        ]);

        return self::SUCCESS;
    }
}
