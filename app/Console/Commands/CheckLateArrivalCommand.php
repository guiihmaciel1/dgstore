<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\TimeClock\Models\TimeClockEntry;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Services\UazapiService;
use Illuminate\Console\Command;

class CheckLateArrivalCommand extends Command
{
    protected $signature = 'timeclock:check-late';
    protected $description = 'Alerta via WhatsApp se vendedoras não registraram chegada até 9:15';

    private const ADMIN_WHATSAPP = '5517992010606';

    public function handle(UazapiService $uazapi): int
    {
        if (now()->isWeekend()) {
            $this->info('Fim de semana — ignorando.');
            return self::SUCCESS;
        }

        $interns = User::where('role', UserRole::Intern)
            ->where('active', true)
            ->get();

        $lateInterns = [];

        foreach ($interns as $intern) {
            $hasClockIn = TimeClockEntry::getTodayEntries($intern->id)
                ->where('type', TimeClockEntry::TYPE_CLOCK_IN)
                ->isNotEmpty();

            if (!$hasClockIn) {
                $lateInterns[] = $intern->name;
            }
        }

        if (empty($lateInterns)) {
            $this->info('Todas as vendedoras registraram chegada.');
            return self::SUCCESS;
        }

        $names = implode(', ', $lateInterns);
        $time = now()->format('H:i');
        $message = "⚠️ Alerta de atraso ({$time})\n\n"
                 . "Ainda sem registro de chegada:\n"
                 . "• " . implode("\n• ", $lateInterns) . "\n\n"
                 . "O sistema está bloqueado até registrarem o ponto.";

        try {
            $uazapi->sendText(self::ADMIN_WHATSAPP, $message);
            $this->info("WhatsApp enviado: {$names}");
        } catch (\Throwable $e) {
            $this->error("Falha ao enviar WhatsApp: {$e->getMessage()}");
            report($e);
        }

        return self::SUCCESS;
    }
}
