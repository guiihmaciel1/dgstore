<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\System\Models\DollarCpRate;
use App\Domain\System\Services\ComprasParaguaiDollarService;
use Illuminate\Console\Command;

class FetchComprasParaguaiDollarCommand extends Command
{
    protected $signature = 'dollar:fetch-cp';

    protected $description = 'Busca a cotação do dólar no Compras Paraguai e salva no histórico';

    public function handle(ComprasParaguaiDollarService $service): int
    {
        $rate = $service->fetchRate();

        if ($rate === null) {
            $this->warn('Não foi possível obter a cotação do Compras Paraguai.');

            return self::FAILURE;
        }

        $latest = DollarCpRate::latestRate();

        if ($latest && (float) $latest->rate === $rate) {
            $this->info("Cotação inalterada: R$ {$rate}. Não salvo duplicado.");

            return self::SUCCESS;
        }

        DollarCpRate::create([
            'rate'       => $rate,
            'fetched_at' => now(),
        ]);

        $this->info("Cotação salva: R$ {$rate}");

        return self::SUCCESS;
    }
}
