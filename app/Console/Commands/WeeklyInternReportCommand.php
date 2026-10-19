<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\TimeClock\Models\IdleLog;
use App\Domain\TimeClock\Models\TimeClockEntry;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Services\UazapiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class WeeklyInternReportCommand extends Command
{
    protected $signature = 'timeclock:weekly-report';
    protected $description = 'Envia relatório semanal de vendedoras via WhatsApp para o admin';

    private const ADMIN_WHATSAPP = '5517992010606';
    private const EXPECTED_HOURS_PER_DAY = 6;

    public function handle(UazapiService $uazapi): int
    {
        $lastMonday = now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $lastSunday = now()->subWeek()->endOfWeek(Carbon::SUNDAY);

        $interns = User::where('role', UserRole::Intern)
            ->where('active', true)
            ->get();

        if ($interns->isEmpty()) {
            $this->info('Nenhuma vendedora ativa.');
            return self::SUCCESS;
        }

        $lines = [];
        $lines[] = "📊 *Relatório Semanal — Vendedoras*";
        $lines[] = "_{$lastMonday->format('d/m')} a {$lastSunday->format('d/m')}_";
        $lines[] = "";

        foreach ($interns as $intern) {
            $report = $this->buildInternReport($intern, $lastMonday, $lastSunday);
            $lines[] = "━━━━━━━━━━━━━━━";
            $lines[] = "👤 *{$intern->name}*";
            $lines[] = "";
            $lines[] = "📱 Vendas celulares: *{$report['phone_sales']}*";
            $lines[] = "🛒 Vendas totais: *{$report['total_sales']}*";
            $lines[] = "";
            $lines[] = "⏰ Horas trabalhadas: *{$report['worked']}*";
            $lines[] = "📋 Esperado: *{$report['expected']}*";
            $lines[] = "📈 Saldo: *{$report['balance']}*";
            $lines[] = "";
            $lines[] = "⏸ Tempo inativo: *{$report['idle_minutes']}min* ({$report['idle_count']}×)";
            $lines[] = "📊 Média/dia: *{$report['avg_per_day']}*";
            $lines[] = "";
        }

        $lines[] = "━━━━━━━━━━━━━━━";
        $lines[] = "_Gerado automaticamente pelo sistema DG Store_";

        $message = implode("\n", $lines);

        try {
            $uazapi->sendText(self::ADMIN_WHATSAPP, $message);
            $this->info('Relatório semanal enviado via WhatsApp.');
        } catch (\Throwable $e) {
            $this->error("Falha ao enviar: {$e->getMessage()}");
            report($e);
        }

        return self::SUCCESS;
    }

    private function buildInternReport(User $intern, Carbon $start, Carbon $end): array
    {
        $entries = TimeClockEntry::forUser($intern->id)
            ->whereBetween('punched_at', [$start, $end])
            ->orderBy('punched_at')
            ->get();

        $byDate = $entries->groupBy(fn ($e) => $e->punched_at->format('Y-m-d'));

        $totalWorked = 0;
        $daysWorked = 0;

        foreach ($byDate as $dayEntries) {
            $sorted = $dayEntries->sortBy('punched_at');
            $minutes = $this->calcWorkedMinutes($sorted);
            if ($minutes > 0) {
                $totalWorked += $minutes;
                $daysWorked++;
            }
        }

        $expectedMinutes = $daysWorked * self::EXPECTED_HOURS_PER_DAY * 60;
        $balance = $totalWorked - $expectedMinutes;

        $idleLogs = IdleLog::forUser($intern->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $totalSales = Sale::where('seller_id', $intern->id)
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->count();

        $phoneSales = Sale::with('items.product')
            ->where('seller_id', $intern->id)
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get()
            ->filter(fn (Sale $sale) => $sale->items->contains(
                fn ($item) => in_array($item->product?->category, [ProductCategory::Smartphone, ProductCategory::Tablet], true)
            ))
            ->count();

        return [
            'total_sales' => $totalSales,
            'phone_sales' => $phoneSales,
            'worked' => $this->formatMinutes($totalWorked),
            'expected' => $this->formatMinutes($expectedMinutes),
            'balance' => ($balance >= 0 ? '+' : '-') . $this->formatMinutes(abs($balance)),
            'avg_per_day' => $daysWorked > 0 ? $this->formatMinutes((int) round($totalWorked / $daysWorked)) : '0h00',
            'idle_minutes' => (int) $idleLogs->sum('idle_minutes'),
            'idle_count' => $idleLogs->count(),
        ];
    }

    private function calcWorkedMinutes(\Illuminate\Support\Collection $entries): int
    {
        $clockIn = $entries->firstWhere('type', TimeClockEntry::TYPE_CLOCK_IN);
        $clockOut = $entries->firstWhere('type', TimeClockEntry::TYPE_CLOCK_OUT);

        if (!$clockIn || !$clockOut) {
            return 0;
        }

        $total = (int) $clockIn->punched_at->diffInMinutes($clockOut->punched_at);

        $lunchOut = $entries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_OUT);
        $lunchIn = $entries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_IN);

        if ($lunchOut && $lunchIn) {
            $total -= (int) $lunchOut->punched_at->diffInMinutes($lunchIn->punched_at);
        }

        return max(0, $total);
    }

    private function formatMinutes(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return "{$h}h" . str_pad((string) $m, 2, '0', STR_PAD_LEFT);
    }
}
