<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\System\Models\SystemSetting;
use Illuminate\Console\Command;

class ResetDollarRateCommand extends Command
{
    protected $signature = 'dollar:reset';

    protected $description = 'Remove a cotação do dólar para forçar novo preenchimento diário';

    public function handle(): int
    {
        SystemSetting::remove('dollar_rate');
        SystemSetting::remove('dollar_rate_updated_at');

        $this->info('Cotação do dólar removida. Será solicitado novo preenchimento.');

        return self::SUCCESS;
    }
}
