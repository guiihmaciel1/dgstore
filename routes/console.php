<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Avaliação de iPhones Seminovos
|--------------------------------------------------------------------------
| Scraping do OLX às 06:00 e cálculo de médias às 06:15, diariamente.
| Certifique-se de que o cron do servidor esteja configurado:
| * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
*/

Schedule::command('valuation:scrape')->dailyAt('06:00');
Schedule::command('valuation:calculate-averages')->dailyAt('06:15');
