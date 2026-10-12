<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\System\Models\DollarCpRate;
use App\Domain\System\Models\SystemSetting;
use Illuminate\Console\Command;

class ResetDollarRateCommand extends Command
{
    protected $signature = 'dollar:reset';

    protected $description = 'Reseta a cotação do dólar e aplica automaticamente a última do Compras Paraguai';

    public function handle(): int
    {
        $cpLatest = DollarCpRate::latestRate();

        if ($cpLatest) {
            SystemSetting::set('dollar_rate', (string) $cpLatest->rate);
            SystemSetting::set('dollar_rate_updated_at', now()->toIso8601String());
            SystemSetting::set('dollar_rate_source', 'compras_paraguai');

            $this->info("Cotação atualizada automaticamente: R$ {$cpLatest->rate} (Compras Paraguai)");
        } else {
            SystemSetting::remove('dollar_rate');
            SystemSetting::remove('dollar_rate_updated_at');
            SystemSetting::remove('dollar_rate_source');

            $this->warn('Nenhuma cotação do Compras Paraguai disponível. Cotação removida.');
        }

        return self::SUCCESS;
    }
}
