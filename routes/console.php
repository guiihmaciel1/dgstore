<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Tarefas Agendadas ────────────────────────────────
Schedule::command('reservations:process-expired')->dailyAt('00:05');
Schedule::command('finance:mark-overdue')->dailyAt('00:10');
Schedule::command('sessions:prune')->dailyAt('01:00');
Schedule::command('crm:check-overdue-deals')->dailyAt('08:00');
Schedule::command('imports:check-delayed')->dailyAt('08:05');
