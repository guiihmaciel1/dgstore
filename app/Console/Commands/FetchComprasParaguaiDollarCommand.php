<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\System\Models\DollarCpRate;
use App\Domain\System\Models\SystemSetting;
use App\Domain\System\Services\ComprasParaguaiDollarService;
use Illuminate\Console\Command;

class FetchComprasParaguaiDollarCommand extends Command
{
    protected $signature = 'dollar:fetch-cp';

    protected $description = 'Busca a cotação do dólar no Compras Paraguai e sincroniza com o sistema';

    public function handle(ComprasParaguaiDollarService $service): int
    {
        $rate = $service->fetchRate();

        if ($rate === null) {
            $this->warn('Não foi possível obter a cotação do Compras Paraguai.');

            return self::FAILURE;
        }

        $latest = DollarCpRate::latestRate();

        if ($latest && (float) $latest->rate === $rate) {
            $latest->update(['fetched_at' => now()]);
            $this->info("Cotação inalterada: R$ {$rate}. Horário atualizado.");
        } else {
            DollarCpRate::create([
                'rate'       => $rate,
                'fetched_at' => now(),
            ]);

            $this->info("Nova cotação salva: R$ {$rate}");
        }

        $this->syncDollarRate($rate);

        $history = $service->fetchHistory();

        if ($history !== null) {
            $this->info('Histórico atualizado (' . count($history['history'] ?? []) . ' registros).');
        } else {
            $this->warn('Não foi possível buscar o histórico de cotações.');
        }

        return self::SUCCESS;
    }

    private function syncDollarRate(float $rate): void
    {
        $source = SystemSetting::get('dollar_rate_source');

        if ($source === 'manual') {
            $this->line('Cotação DG definida manualmente hoje — não sobrescrita.');

            return;
        }

        SystemSetting::set('dollar_rate', (string) $rate);
        SystemSetting::set('dollar_rate_updated_at', now()->toIso8601String());
        SystemSetting::set('dollar_rate_source', 'compras_paraguai');

        $this->info("Cotação DG sincronizada: R$ {$rate} (automático via CP)");
    }
}
