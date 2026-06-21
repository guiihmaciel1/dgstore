<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\CRM\Models\Deal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueDealsCommand extends Command
{
    protected $signature = 'crm:check-overdue-deals';
    protected $description = 'Verifica deals atrasados, sem interação e com follow-ups vencidos';

    public function handle(): int
    {
        $issues = [];

        $this->checkOverdueDeals($issues);
        $this->checkStaleDeals($issues);
        $this->checkOverdueFollowups($issues);

        if (empty($issues)) {
            $this->info('Nenhum alerta de CRM no momento.');
            return self::SUCCESS;
        }

        Log::info('CRM: ' . count($issues) . ' alerta(s) detectado(s).', ['alerts' => $issues]);

        return self::SUCCESS;
    }

    private function checkOverdueDeals(array &$issues): void
    {
        $overdueDeals = Deal::open()
            ->whereNotNull('expected_close_date')
            ->where('expected_close_date', '<', today())
            ->with(['user', 'customer'])
            ->get();

        if ($overdueDeals->isEmpty()) {
            return;
        }

        $this->warn("{$overdueDeals->count()} deal(s) com prazo vencido:");

        foreach ($overdueDeals as $deal) {
            $daysOverdue = $deal->expected_close_date->diffInDays(today());
            $seller = $deal->user?->name ?? 'Sem vendedor';

            $this->line("  [{$seller}] {$deal->title} — {$daysOverdue}d de atraso");

            $issues[] = [
                'type' => 'overdue',
                'deal_id' => $deal->id,
                'title' => $deal->title,
                'seller' => $seller,
                'days_overdue' => $daysOverdue,
            ];
        }
    }

    private function checkStaleDeals(array &$issues): void
    {
        $staleDeals = Deal::stale(4)
            ->with(['user', 'customer'])
            ->get();

        if ($staleDeals->isEmpty()) {
            return;
        }

        $this->warn("{$staleDeals->count()} deal(s) sem interação +4h:");

        foreach ($staleDeals as $deal) {
            $hours = $deal->waiting_hours;
            $seller = $deal->user?->name ?? 'Sem vendedor';
            $label = $hours >= 24 ? ((int) floor($hours / 24)) . 'd' : ((int) $hours) . 'h';

            $this->line("  [{$seller}] {$deal->title} — {$label} sem resposta");

            $issues[] = [
                'type' => 'stale',
                'deal_id' => $deal->id,
                'title' => $deal->title,
                'seller' => $seller,
                'hours_waiting' => $hours,
            ];
        }
    }

    private function checkOverdueFollowups(array &$issues): void
    {
        $followupDeals = Deal::needsFollowup()
            ->with(['user', 'customer'])
            ->get();

        if ($followupDeals->isEmpty()) {
            return;
        }

        $this->warn("{$followupDeals->count()} follow-up(s) atrasado(s):");

        foreach ($followupDeals as $deal) {
            $seller = $deal->user?->name ?? 'Sem vendedor';
            $action = $deal->next_action ?? 'Ação não definida';

            $this->line("  [{$seller}] {$deal->title} — {$action} (era p/ {$deal->next_action_at->format('d/m H:i')})");

            $issues[] = [
                'type' => 'followup_overdue',
                'deal_id' => $deal->id,
                'title' => $deal->title,
                'seller' => $seller,
                'next_action' => $action,
                'was_due' => $deal->next_action_at->toIso8601String(),
            ];
        }
    }
}
