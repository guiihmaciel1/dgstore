<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Import\Models\ImportOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDelayedImportsCommand extends Command
{
    protected $signature = 'imports:check-delayed';
    protected $description = 'Loga pedidos de importação com chegada atrasada';

    public function handle(): int
    {
        $delayedOrders = ImportOrder::query()
            ->active()
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', today())
            ->with(['supplier', 'user'])
            ->get();

        if ($delayedOrders->isEmpty()) {
            $this->info('Nenhum pedido de importação atrasado.');
            return self::SUCCESS;
        }

        $this->info("{$delayedOrders->count()} pedido(s) de importação atrasado(s):");

        foreach ($delayedOrders as $order) {
            $daysLate = $order->estimated_arrival->diffInDays(today());
            $supplier = $order->supplier?->name ?? 'Sem fornecedor';

            $this->warn("  #{$order->order_number} — fornecedor: {$supplier} — status: {$order->status->label()} — {$daysLate} dia(s) de atraso");
        }

        Log::info("Importações: {$delayedOrders->count()} pedido(s) com chegada atrasada.", [
            'orders' => $delayedOrders->map(fn (ImportOrder $o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'supplier' => $o->supplier?->name,
                'status' => $o->status->value,
                'estimated_arrival' => $o->estimated_arrival->toDateString(),
                'days_late' => $o->estimated_arrival->diffInDays(today()),
            ])->toArray(),
        ]);

        return self::SUCCESS;
    }
}
