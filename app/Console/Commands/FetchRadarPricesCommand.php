<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\System\Models\RadarProduct;
use App\Domain\System\Services\RadarPyService;
use Illuminate\Console\Command;

class FetchRadarPricesCommand extends Command
{
    protected $signature = 'radar:fetch-prices';

    protected $description = 'Busca preços dos produtos monitorados no Compras Paraguai via IA';

    public function handle(RadarPyService $service): int
    {
        $count = RadarProduct::active()->count();

        if ($count === 0) {
            $this->info('Nenhum produto ativo no Radar PY. Use "radar:add" para adicionar.');

            return self::SUCCESS;
        }

        $this->info("Buscando preços de {$count} produto(s) monitorado(s)...");

        $service->fetchAllProducts();

        $cached = $service->getCached();
        $total = 0;

        foreach ($cached as $data) {
            $offerCount = count($data['offers'] ?? []);
            $total += $offerCount;
            $this->info("  ✓ {$data['product_name']}: {$offerCount} ofertas encontradas");
        }

        $this->info("Concluído: {$total} ofertas totais cacheadas.");

        return self::SUCCESS;
    }
}
