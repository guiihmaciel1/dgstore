<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\GenerateReportUseCase;
use App\Domain\CRM\Models\Deal;
use App\Domain\CRM\Models\PipelineStage;
use App\Domain\Customer\Models\Customer;
use App\Domain\Finance\Models\FinancialTransaction;
use App\Domain\Import\Models\ImportOrder;
use App\Domain\Import\Services\ImportOrderService;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Reservation\Services\ReservationService;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Models\SaleItem;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\Schedule\Enums\AppointmentStatus;
use App\Domain\Schedule\Models\Appointment;
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

        // Notificações unificadas do sistema
        $systemNotifications = $this->getSystemNotifications();

        // Novos leads aguardando interação (no estágio "Novo Lead" sem atividade real)
        $newLeadsWaiting = $this->getNewLeadsWaiting();

        // Aniversariantes do mês
        $birthdayCustomers = $this->getBirthdayCustomers();

        // Agenda do dia
        $todayAppointments = $this->getTodayAppointments();
        $nextAppointment = $this->getNextAppointment();

        $monthSummary = $this->getMonthSummary();

        return view('dashboard', [
            'todayTotal' => $data['today']['total'],
            'todayCount' => $data['today']['count'],
            'monthTotal' => $data['month']['total'],
            'monthCount' => $data['month']['count'],
            'topProducts' => $data['top_products'],
            'salesChart' => $data['sales_chart'],
            'profit' => $data['profit'],
            'systemNotifications' => $systemNotifications,
            'newLeadsWaiting' => $newLeadsWaiting,
            'birthdayCustomers' => $birthdayCustomers,
            'todayAppointments' => $todayAppointments,
            'nextAppointment' => $nextAppointment,
            'monthSummary' => $monthSummary,
        ]);
    }

    private function getNewLeadsWaiting(): \Illuminate\Support\Collection
    {
        $defaultStage = PipelineStage::where('is_default', true)->first();

        if (! $defaultStage) {
            return collect();
        }

        return Deal::open()
            ->where('pipeline_stage_id', $defaultStage->id)
            ->whereDoesntHave('activities', function ($q) {
                $q->whereIn('type', ['note', 'whatsapp', 'call']);
            })
            ->with(['customer', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getBirthdayCustomers(): \Illuminate\Support\Collection
    {
        return Customer::whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->orderByRaw('DAY(birth_date) ASC')
            ->get();
    }

    private function getSystemNotifications(): array
    {
        $notifications = [];

        $salesPending = Sale::where('payment_status', PaymentStatus::Pending)->count();
        if ($salesPending > 0) {
            $notifications[] = [
                'type' => 'danger', 'icon' => 'sale', 'count' => $salesPending,
                'label' => 'Vendas pendentes',
                'route' => route('sales.index', ['payment_status' => 'pending']),
            ];
        }

        $salesPartial = Sale::where('payment_status', PaymentStatus::Partial)->count();
        if ($salesPartial > 0) {
            $notifications[] = [
                'type' => 'warning', 'icon' => 'sale', 'count' => $salesPartial,
                'label' => 'Pagamento parcial',
                'route' => route('sales.index', ['payment_status' => 'partial']),
            ];
        }

        $overdueTransactions = FinancialTransaction::where('status', 'overdue')->count();
        if ($overdueTransactions > 0) {
            $notifications[] = [
                'type' => 'danger', 'icon' => 'finance', 'count' => $overdueTransactions,
                'label' => 'Transações vencidas',
                'route' => route('finance.payables'),
            ];
        }

        $dueSoon = FinancialTransaction::where('status', 'pending')
            ->whereBetween('due_date', [today(), today()->addDays(3)])
            ->count();
        if ($dueSoon > 0) {
            $notifications[] = [
                'type' => 'warning', 'icon' => 'finance', 'count' => $dueSoon,
                'label' => 'Transações vencendo',
                'route' => route('finance.index'),
            ];
        }

        $overdueDeals = Deal::where('user_id', auth()->id())->open()
            ->whereNotNull('expected_close_date')
            ->where('expected_close_date', '<', today())
            ->count();
        if ($overdueDeals > 0) {
            $notifications[] = [
                'type' => 'danger', 'icon' => 'crm', 'count' => $overdueDeals,
                'label' => 'Negócios atrasados',
                'route' => route('crm.board'),
            ];
        }

        $staleDeals = Deal::where('user_id', auth()->id())->open()
            ->where('updated_at', '<', now()->subDays(5))
            ->count();
        if ($staleDeals > 0) {
            $notifications[] = [
                'type' => 'info', 'icon' => 'crm', 'count' => $staleDeals,
                'label' => 'Negócios parados',
                'route' => route('crm.board'),
            ];
        }

        $warrantiesExpiring = $this->warrantyService->countExpiringSoon(30);
        if ($warrantiesExpiring > 0) {
            $notifications[] = [
                'type' => 'warning', 'icon' => 'warranty', 'count' => $warrantiesExpiring,
                'label' => 'Garantias vencendo',
                'route' => route('warranties.index', ['status' => 'expiring']),
            ];
        }

        $openClaims = $this->warrantyService->countOpenClaims();
        if ($openClaims > 0) {
            $notifications[] = [
                'type' => 'danger', 'icon' => 'warranty', 'count' => $openClaims,
                'label' => 'Acionamentos abertos',
                'route' => route('warranties.index', ['status' => 'with_claims']),
            ];
        }

        $importsInTransit = $this->importService->countInTransit();
        if ($importsInTransit > 0) {
            $notifications[] = [
                'type' => 'info', 'icon' => 'import', 'count' => $importsInTransit,
                'label' => 'Importações em trânsito',
                'route' => route('imports.index'),
            ];
        }

        $delayedImports = ImportOrder::active()
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', today())
            ->count();
        if ($delayedImports > 0) {
            $notifications[] = [
                'type' => 'warning', 'icon' => 'import', 'count' => $delayedImports,
                'label' => 'Importações atrasadas',
                'route' => route('imports.index'),
            ];
        }

        $reservationsOverdue = $this->reservationService->countOverdue();
        if ($reservationsOverdue > 0) {
            $notifications[] = [
                'type' => 'danger', 'icon' => 'reservation', 'count' => $reservationsOverdue,
                'label' => 'Reservas vencidas',
                'route' => route('reservations.index', ['status' => 'active']),
            ];
        }

        $reservationsExpiring = $this->reservationService->countExpiringSoon(3);
        if ($reservationsExpiring > 0) {
            $notifications[] = [
                'type' => 'warning', 'icon' => 'reservation', 'count' => $reservationsExpiring,
                'label' => 'Reservas vencendo',
                'route' => route('reservations.index'),
            ];
        }

        return $notifications;
    }

    private function getTodayAppointments(): \Illuminate\Support\Collection
    {
        return Appointment::forDate(today()->format('Y-m-d'))
            ->active()
            ->orderBy('start_time')
            ->get();
    }

    private function getNextAppointment(): ?Appointment
    {
        return Appointment::forDate(today()->format('Y-m-d'))
            ->active()
            ->where('start_time', '>=', now()->format('H:i:s'))
            ->orderBy('start_time')
            ->first();
    }

    private function getMonthSummary(): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $sales = Sale::with(['items.product', 'tradeIns'])
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $totalSales = $sales->count();
        $totalRevenue = (float) $sales->sum('total');
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        $allItems = $sales->flatMap->items;
        $totalItems = (int) $allItems->sum('quantity');

        $accessoryCategories = ['charger', 'cable', 'case', 'accessory'];
        $otherAppleCategories = ['tablet', 'notebook', 'smartwatch', 'headphone', 'speaker'];

        $iphoneNew = 0;
        $iphoneUsed = 0;
        $accessories = 0;
        $otherApple = 0;

        foreach ($allItems as $item) {
            $rawCategory = $item->product?->category ?? ($item->product_snapshot['category'] ?? null);
            $category = $rawCategory instanceof \App\Domain\Product\Enums\ProductCategory
                ? $rawCategory->value
                : (is_string($rawCategory) ? $rawCategory : null);

            $rawCondition = $item->product?->condition ?? ($item->product_snapshot['condition'] ?? null);
            $condition = $rawCondition instanceof ProductCondition
                ? $rawCondition->value
                : (is_string($rawCondition) ? $rawCondition : null);

            $qty = $item->quantity;

            if ($category === 'smartphone') {
                if (in_array($condition, ['used', 'refurbished'])) {
                    $iphoneUsed += $qty;
                } else {
                    $iphoneNew += $qty;
                }
            } elseif (in_array($category, $accessoryCategories)) {
                $accessories += $qty;
            } elseif (in_array($category, $otherAppleCategories)) {
                $otherApple += $qty;
            } else {
                $otherApple += $qty;
            }
        }

        $tradeInsReceived = (int) TradeIn::whereHas('sale', fn ($q) => $q
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
        )->count();

        return [
            'month_label' => now()->translatedFormat('F/Y'),
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'average_ticket' => $averageTicket,
            'total_items' => $totalItems,
            'iphone_total' => $iphoneNew + $iphoneUsed,
            'iphone_new' => $iphoneNew,
            'iphone_used' => $iphoneUsed,
            'accessories' => $accessories,
            'other_apple' => $otherApple,
            'trade_ins_received' => $tradeInsReceived,
        ];
    }
}
