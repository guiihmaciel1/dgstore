<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\GenerateReportUseCase;
use App\Domain\CRM\Models\Deal;
use App\Domain\Finance\Models\FinancialTransaction;
use App\Domain\Import\Models\ImportOrder;
use App\Domain\Import\Services\ImportOrderService;
use App\Domain\Reservation\Services\ReservationService;
use App\Domain\Warranty\Services\WarrantyService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GenerateReportUseCase $reportUseCase,
        private readonly WarrantyService $warrantyService,
        private readonly ImportOrderService $importService,
        private readonly ReservationService $reservationService
    ) {}

    public function index(): View
    {
        $data = $this->reportUseCase->dashboardData();

        // Alertas dos módulos
        $alerts = [
            'warranties_expiring' => $this->warrantyService->countExpiringSoon(30),
            'open_claims' => $this->warrantyService->countOpenClaims(),
            'imports_in_transit' => $this->importService->countInTransit(),
            'reservations_active' => $this->reservationService->countActive(),
            'reservations_expiring' => $this->reservationService->countExpiringSoon(3),
            'reservations_overdue' => $this->reservationService->countOverdue(),
            'deals_open' => Deal::where('user_id', auth()->id())->open()->count(),
            'deals_overdue' => Deal::where('user_id', auth()->id())->open()
                ->whereNotNull('expected_close_date')
                ->where('expected_close_date', '<', today())
                ->count(),
        ];

        // Notificações do sistema (dados monitorados pelos cron jobs)
        $systemNotifications = $this->getSystemNotifications();

        return view('dashboard', [
            'todayTotal' => $data['today']['total'],
            'todayCount' => $data['today']['count'],
            'monthTotal' => $data['month']['total'],
            'monthCount' => $data['month']['count'],
            'lowStockCount' => $data['low_stock']['count'],
            'lowStockProducts' => $data['low_stock']['products'],
            'topProducts' => $data['top_products'],
            'salesChart' => $data['sales_chart'],
            'alerts' => $alerts,
            'systemNotifications' => $systemNotifications,
        ]);
    }

    private function getSystemNotifications(): array
    {
        $notifications = [];

        // Transações financeiras vencidas
        $overdueTransactions = FinancialTransaction::where('status', 'overdue')->count();
        if ($overdueTransactions > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'finance',
                'message' => "{$overdueTransactions} transação(ões) financeira(s) vencida(s)",
                'route' => route('finance.payables'),
            ];
        }

        // Transações vencendo nos próximos 3 dias
        $dueSoon = FinancialTransaction::where('status', 'pending')
            ->whereBetween('due_date', [today(), today()->addDays(3)])
            ->count();
        if ($dueSoon > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'finance',
                'message' => "{$dueSoon} transação(ões) vencendo nos próximos 3 dias",
                'route' => route('finance.index'),
            ];
        }

        // Deals atrasados no CRM
        $overdueDeals = Deal::where('user_id', auth()->id())->open()
            ->whereNotNull('expected_close_date')
            ->where('expected_close_date', '<', today())
            ->count();
        if ($overdueDeals > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'crm',
                'message' => "{$overdueDeals} negócio(s) com prazo de fechamento vencido",
                'route' => route('crm.board'),
            ];
        }

        // Deals sem atividade há mais de 5 dias
        $staleDeals = Deal::where('user_id', auth()->id())->open()
            ->where('updated_at', '<', now()->subDays(5))
            ->count();
        if ($staleDeals > 0) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'crm',
                'message' => "{$staleDeals} negócio(s) sem atividade há mais de 5 dias",
                'route' => route('crm.board'),
            ];
        }

        // Importações atrasadas
        $delayedImports = ImportOrder::active()
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', today())
            ->count();
        if ($delayedImports > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'import',
                'message' => "{$delayedImports} pedido(s) de importação com chegada atrasada",
                'route' => route('imports.index'),
            ];
        }

        return $notifications;
    }
}
