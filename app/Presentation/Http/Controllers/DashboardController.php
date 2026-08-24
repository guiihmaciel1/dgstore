<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\GenerateReportUseCase;
use App\Domain\CRM\Models\Deal;
use App\Domain\CRM\Models\PipelineStage;
use App\Domain\CRM\Models\ProductInterest;
use App\Domain\Customer\Models\Customer;
use App\Domain\Finance\Models\FinancialTransaction;
use App\Domain\Import\Models\ImportOrder;
use App\Domain\Import\Services\ImportOrderService;
use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Marketing\Models\MarketingUsedListing;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\Reservation\Services\ReservationService;
use App\Domain\Commission\Models\Commission;
use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Enums\SaleType;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Models\SaleItem;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\Schedule\Enums\AppointmentStatus;
use App\Domain\Schedule\Models\Appointment;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Domain\Warranty\Services\WarrantyService;
use App\Domain\News\Services\AppleNewsService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GenerateReportUseCase $reportUseCase,
        private readonly WarrantyService $warrantyService,
        private readonly ImportOrderService $importService,
        private readonly ReservationService $reservationService,
        private readonly AppleNewsService $appleNewsService
    ) {}

    public function index(Request $request): View
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $referenceDate = Carbon::createFromDate($year, $month, 1);
        $isCurrentMonth = $referenceDate->isSameMonth(now());

        $data = $this->reportUseCase->dashboardData($referenceDate);

        $systemNotifications = $this->getSystemNotifications();
        $newLeadsWaiting = $this->getNewLeadsWaiting();
        $birthdayCustomers = $this->getBirthdayCustomers();
        $todayAppointments = $this->getTodayAppointments();
        $nextAppointment = $this->getNextAppointment();
        $todayPayables = $this->getTodayPayables();

        [$monthSummary, $salesAnalytics] = $this->getMonthSummaryAndAnalytics($referenceDate);
        $wholesaleData = $this->buildWholesaleIntelligence($referenceDate);
        $internStats = $this->buildInternStats($referenceDate);
        $followupSales = $this->getFollowupSales();
        $appleNews = $this->appleNewsService->getCached();
        $stockItems = $this->getStockCatalog();

        $executiveSummary = null;
        if (auth()->user()->isAdminGeral()) {
            $executiveSummary = app(ExecutiveSummaryController::class)->buildExecutiveData('month');
        }

        return view('dashboard', [
            'todayTotal' => $data['today']['total'],
            'todayCount' => $data['today']['count'],
            'monthTotal' => $data['month']['total'],
            'monthCount' => $data['month']['count'],
            'topProducts' => $data['top_products'],
            'salesChart' => $data['sales_chart'],
            'profit' => $data['profit'],
            'prevMonthProfit' => $data['prev_month_profit'],
            'systemNotifications' => $systemNotifications,
            'newLeadsWaiting' => $newLeadsWaiting,
            'birthdayCustomers' => $birthdayCustomers,
            'todayAppointments' => $todayAppointments,
            'nextAppointment' => $nextAppointment,
            'todayPayables' => $todayPayables,
            'monthSummary' => $monthSummary,
            'salesAnalytics' => $salesAnalytics,
            'wholesaleData' => $wholesaleData,
            'internStats' => $internStats,
            'followupSales' => $followupSales,
            'appleNews' => $appleNews,
            'stockItems' => $stockItems,
            'referenceDate' => $referenceDate,
            'isCurrentMonth' => $isCurrentMonth,
            'executiveSummary' => $executiveSummary,
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
            ->whereRaw('DAY(birth_date) >= ?', [now()->day])
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

        $interestsMatched = ProductInterest::pending()
            ->whereHas('deal', fn ($q) => $q->open())
            ->get()
            ->filter(fn (ProductInterest $pi) => $pi->hasMatchInStock())
            ->count();
        if ($interestsMatched > 0) {
            $notifications[] = [
                'type' => 'success', 'icon' => 'crm', 'count' => $interestsMatched,
                'label' => 'Interesses com estoque',
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

        $followupCount = Sale::whereNotNull('customer_id')
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->whereNotNull('sold_at')
            ->where('sold_at', '<=', now()->subDays(7))
            ->where('sold_at', '>=', now()->subDays(30))
            ->whereDoesntHave('followups')
            ->count();
        if ($followupCount > 0) {
            $notifications[] = [
                'type' => 'info', 'icon' => 'followup', 'count' => $followupCount,
                'label' => 'Follow-ups pendentes',
                'route' => '#followup-modal',
            ];
        }

        return $notifications;
    }

    private function getFollowupSales(): array
    {
        $sales = Sale::with(['customer', 'items'])
            ->whereNotNull('customer_id')
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->whereNotNull('sold_at')
            ->where('sold_at', '<=', now()->subDays(7))
            ->where('sold_at', '>=', now()->subDays(30))
            ->whereDoesntHave('followups')
            ->orderBy('sold_at')
            ->limit(20)
            ->get();

        return $sales->map(function (Sale $sale) {
            $daysSince = (int) $sale->sold_at->diffInDays(now());
            $productNames = $sale->items->pluck('product_name')->implode(', ') ?: 'Sem produtos';
            $customerName = $sale->customer?->name ?? 'Sem cliente';
            $phone = $sale->customer?->phone ?? '';
            $cleanPhone = preg_replace('/\D/', '', $phone);
            $hasPhone = strlen($cleanPhone) >= 8;
            if ($hasPhone && strlen($cleanPhone) <= 11) {
                $cleanPhone = '55' . $cleanPhone;
            }

            $message = "Olá {$customerName}! Aqui é a DG Store.\n"
                . "Faz {$daysSince} dias que você adquiriu o {$productNames} conosco.\n"
                . "Está tudo certo com o aparelho? Precisa de algo?\n"
                . "Agradecemos pela preferência!";

            return [
                'id' => $sale->id,
                'customer_name' => $customerName,
                'customer_phone' => $phone,
                'has_phone' => $hasPhone,
                'product_names' => $productNames,
                'sale_number' => $sale->sale_number,
                'sold_at_formatted' => $sale->sold_at->format('d/m/Y'),
                'days_since' => $daysSince,
                'whatsapp_url' => $hasPhone
                    ? 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($message)
                    : null,
            ];
        })->values()->toArray();
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

    private function getTodayPayables(): \Illuminate\Support\Collection
    {
        $systemCategoryNames = ['Trade-in', 'Custo de Mercadoria', 'Compra Fornecedor'];
        $systemCategoryIds = \App\Domain\Finance\Models\FinancialCategory::whereIn('name', $systemCategoryNames)->pluck('id');

        return FinancialTransaction::with(['category'])
            ->where('type', 'expense')
            ->whereIn('status', ['pending', 'overdue'])
            ->where('due_date', today())
            ->when($systemCategoryIds->isNotEmpty(), fn ($q) => $q->whereNotIn('category_id', $systemCategoryIds))
            ->orderBy('amount', 'desc')
            ->get();
    }

    private function getStockCatalog(): array
    {
        $products = Product::active()
            ->inStock()
            ->where('category', 'smartphone')
            ->get();

        $usedListings = MarketingUsedListing::all()
            ->keyBy(fn ($l) => $l->listable_type.'_'.$l->listable_id);

        $grouped = $products->groupBy(fn ($p) => $p->name . '|' . ($p->storage ?? '') . '|' . ($p->color ?? '') . '|' . $p->condition->value);

        $items = $grouped->map(function ($group) use ($usedListings) {
            $first = $group->first();
            $listingKey = Product::class.'_'.$first->id;
            $listing = $usedListings->get($listingKey);

            return [
                'name' => $first->name,
                'storage' => $first->storage,
                'color' => $first->color,
                'condition' => $first->condition->value,
                'qty' => $group->sum('stock_quantity'),
                'price' => (float) $first->sale_price,
                'battery' => $listing?->battery_health ?? $first->battery_health,
                'has_box' => (bool) ($listing?->has_box ?? $first->has_box),
                'has_cable' => (bool) ($listing?->has_cable ?? $first->has_cable),
                'notes' => $listing?->notes ?? '',
                'sort_gen' => $this->extractIphoneGeneration($first->name),
                'sort_model' => $this->extractModelTier($first->name),
            ];
        })->values();

        $marketingPrices = MarketingPrice::active()->ordered()->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'storage' => $p->storage,
                'color' => $p->color,
                'condition' => 'new',
                'qty' => 1,
                'price' => (float) $p->price,
                'battery' => null,
                'has_box' => true,
                'has_cable' => true,
                'sort_gen' => $this->extractIphoneGeneration($p->name),
                'sort_model' => $this->extractModelTier($p->name),
            ]);

        $all = $items->concat($marketingPrices)
            ->sortBy([['sort_gen', 'asc'], ['sort_model', 'desc'], ['storage', 'asc'], ['name', 'asc']])
            ->values();

        $used = $all->filter(fn ($i) => in_array($i['condition'], ['used', 'refurbished']))->values();
        $new = $all->filter(fn ($i) => $i['condition'] === 'new')->values();

        return [
            'used' => $used->toArray(),
            'new' => $new->toArray(),
            'usedCount' => $used->sum('qty'),
            'newCount' => $new->count(),
        ];
    }

    private function extractIphoneGeneration(string $name): int
    {
        if (preg_match('/iphone\s*(\d+)/i', $name, $m)) {
            return (int) $m[1];
        }
        return 999;
    }

    private function extractModelTier(string $name): int
    {
        $lower = strtolower($name);
        if (str_contains($lower, 'pro max')) return 4;
        if (str_contains($lower, 'pro')) return 3;
        if (str_contains($lower, 'plus')) return 2;
        return 1;
    }

    /**
     * Retorna metas configuradas para as vendedoras.
     */
    private function getInternGoals(): array
    {
        return [
            'monthly' => [
                ['target' => 30, 'reward' => 'Colônia Victoria Secrets para cada'],
                ['target' => 40, 'reward' => 'Kit Colônia + Creme Victoria Secrets para cada'],
                ['target' => 50, 'reward' => 'Perfume Árabe para cada'],
            ],
            'weekly' => [
                'target' => 10,
                'deadline_day' => Carbon::THURSDAY,
                'reward' => 'Almoço por conta da empresa na Sexta-feira',
            ],
        ];
    }

    /**
     * Computa estatísticas das vendedoras para o dashboard.
     */
    private function buildInternStats(Carbon $referenceDate): array
    {
        $start = $referenceDate->copy()->startOfMonth();
        $end = $referenceDate->copy()->endOfMonth();

        $interns = User::where('role', UserRole::Intern)
            ->where('active', true)
            ->get();

        if ($interns->isEmpty()) {
            return ['interns' => [], 'combined' => [], 'goals' => [], 'daily_chart' => []];
        }

        $internIds = $interns->pluck('id');

        $internSales = Sale::with(['items.product', 'customer', 'commissions'])
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->whereIn('seller_id', $internIds)
            ->get();

        $salesByIntern = $internSales->groupBy('seller_id');

        $weekStart = now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = now()->endOfWeek(Carbon::SUNDAY);
        $isCurrentMonth = $referenceDate->isSameMonth(now());

        $weekSales = $isCurrentMonth
            ? $internSales->filter(fn (Sale $s) => $s->sold_at->between($weekStart, $weekEnd))
            : collect();

        $weekSalesByIntern = $weekSales->groupBy('seller_id');

        $internCommissions = Commission::whereBetween('created_at', [$start, $end])
            ->whereIn('user_id', $internIds)
            ->get()
            ->groupBy('user_id');

        $internsData = $interns->map(function (User $intern) use ($salesByIntern, $weekSalesByIntern, $internCommissions) {
            $sales = $salesByIntern->get($intern->id, collect());
            $weekSalesForIntern = $weekSalesByIntern->get($intern->id, collect());
            $commissions = $internCommissions->get($intern->id, collect());

            $totalRevenue = (float) $sales->sum('total');
            $totalProfit = (float) $sales->flatMap->items->sum(fn ($item) => $item->item_profit);
            $salesCount = $sales->count();

            return [
                'id' => $intern->id,
                'name' => $intern->name,
                'sales_count' => $salesCount,
                'total_revenue' => $totalRevenue,
                'total_profit' => $totalProfit,
                'commission_earned' => (float) $commissions->sum('commission_amount'),
                'sales_this_week' => $weekSalesForIntern->count(),
                'avg_ticket' => $salesCount > 0 ? $totalRevenue / $salesCount : 0,
            ];
        })->sortByDesc('sales_count')->values()->toArray();

        $combinedSales = array_sum(array_column($internsData, 'sales_count'));
        $combinedRevenue = array_sum(array_column($internsData, 'total_revenue'));
        $combinedProfit = array_sum(array_column($internsData, 'total_profit'));
        $combinedCommission = array_sum(array_column($internsData, 'commission_earned'));
        $combinedWeekSales = array_sum(array_column($internsData, 'sales_this_week'));

        $goals = $this->getInternGoals();
        $monthlyGoals = collect($goals['monthly'])->map(fn ($goal) => [
            ...$goal,
            'reached' => $combinedSales >= $goal['target'],
            'progress' => min(100, round(($combinedSales / $goal['target']) * 100)),
        ])->toArray();

        $weeklyGoal = $goals['weekly'];
        $today = now();
        $deadlineDay = $weeklyGoal['deadline_day'];
        $deadlineDate = $weekStart->copy()->addDays($deadlineDay - 1)->endOfDay();
        $pastDeadline = $isCurrentMonth && $today->greaterThan($deadlineDate);

        $weeklyGoalData = [
            'target' => $weeklyGoal['target'],
            'current' => $combinedWeekSales,
            'reward' => $weeklyGoal['reward'],
            'reached' => $combinedWeekSales >= $weeklyGoal['target'],
            'past_deadline' => $pastDeadline,
            'deadline_label' => 'Quinta-feira',
            'progress' => min(100, round(($combinedWeekSales / $weeklyGoal['target']) * 100)),
            'remaining' => max(0, $weeklyGoal['target'] - $combinedWeekSales),
        ];

        $dailyChart = $this->buildInternDailyChart($internSales, $start, $end, $referenceDate);

        $salesDetail = $internSales->sortByDesc('sold_at')->map(function (Sale $sale) use ($interns) {
            $seller = $interns->firstWhere('id', $sale->seller_id);
            $productNames = $sale->items->map(fn ($item) => $item->product_snapshot['name'] ?? 'Produto')->implode(', ');

            $totalCost = (float) $sale->items->sum(fn ($item) => ($item->total_cost_value ?? 0) * $item->quantity);
            $grossProfit = (float) $sale->profit;
            $commission = (float) $sale->commissions->sum('commission_amount');
            $netProfit = $grossProfit - $commission;

            $conditions = $sale->items->map(function ($item) {
                $rawCond = $item->product?->condition ?? ($item->product_snapshot['condition'] ?? null);
                if ($rawCond instanceof \App\Domain\Product\Enums\ProductCondition) {
                    return $rawCond->value;
                }
                return is_string($rawCond) ? $rawCond : null;
            })->filter()->unique()->values()->toArray();

            $conditionLabel = 'Outro';
            if (in_array('new', $conditions) && count($conditions) === 1) {
                $conditionLabel = 'Novo';
            } elseif (count(array_intersect(['used', 'refurbished'], $conditions)) > 0 && !in_array('new', $conditions)) {
                $conditionLabel = 'Seminovo';
            } elseif (in_array('new', $conditions) && count(array_intersect(['used', 'refurbished'], $conditions)) > 0) {
                $conditionLabel = 'Misto';
            }

            return [
                'sale_number' => $sale->sale_number,
                'seller_name' => $seller?->name ?? $sale->seller_name,
                'customer_name' => $sale->customer?->name ?? 'Sem cliente',
                'products' => $productNames,
                'total' => (float) $sale->total,
                'cost' => $totalCost,
                'gross_profit' => $grossProfit,
                'commission' => $commission,
                'net_profit' => $netProfit,
                'condition' => $conditionLabel,
                'sold_at' => $sale->sold_at->format('d/m H:i'),
            ];
        })->values()->toArray();

        return [
            'interns' => $internsData,
            'combined' => [
                'total_sales' => $combinedSales,
                'total_revenue' => $combinedRevenue,
                'total_profit' => $combinedProfit,
                'total_commission' => $combinedCommission,
                'sales_this_week' => $combinedWeekSales,
            ],
            'goals' => [
                'monthly' => $monthlyGoals,
                'weekly' => $weeklyGoalData,
            ],
            'daily_chart' => $dailyChart,
            'sales_detail' => $salesDetail,
        ];
    }

    /**
     * Monta dados diários para o chart de evolução das vendedoras.
     */
    private function buildInternDailyChart($internSales, Carbon $start, Carbon $end, Carbon $referenceDate): array
    {
        $isCurrentMonth = $referenceDate->isSameMonth(now());
        $lastDay = $isCurrentMonth ? now()->day : $end->day;

        $dailyData = [];
        for ($day = 1; $day <= $lastDay; $day++) {
            $dailyData[$day] = 0;
        }

        foreach ($internSales as $sale) {
            $day = $sale->sold_at->day;
            if (isset($dailyData[$day])) {
                $dailyData[$day]++;
            }
        }

        return [
            'labels' => array_map(fn ($d) => str_pad((string) $d, 2, '0', STR_PAD_LEFT), array_keys($dailyData)),
            'data' => array_values($dailyData),
        ];
    }

    /**
     * Retorna [monthSummary, salesAnalytics] numa unica query para evitar duplicacao.
     * @return array{0: array, 1: array}
     */
    private function getMonthSummaryAndAnalytics(?Carbon $referenceDate = null): array
    {
        $ref = $referenceDate ?? now();
        $start = $ref->copy()->startOfMonth();
        $end = $ref->copy()->endOfMonth();

        $sales = Sale::with(['items.product', 'tradeIns', 'customer'])
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $allItems = $sales->flatMap->items;

        $customerBySale = $sales->keyBy('id')->map(fn (Sale $s) => $s->customer?->name ?? 'Sem cliente');

        $monthSummary = $this->buildMonthSummary($ref, $sales, $allItems, $start, $end);
        $salesAnalytics = $this->buildSalesAnalytics($sales, $allItems, $customerBySale);

        return [$monthSummary, $salesAnalytics];
    }

    private function buildMonthSummary(Carbon $ref, $sales, $allItems, Carbon $start, Carbon $end): array
    {
        $totalSales = $sales->count();
        $totalRevenue = (float) $sales->sum('total');
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        $totalItems = (int) $allItems->sum('quantity');
        
        // Totais por tipo de venda
        $repasseTotal = (float) $sales->where('sale_type', \App\Domain\Sale\Enums\SaleType::Repasse)->sum('total');
        $clienteFinalTotal = (float) $sales->where('sale_type', \App\Domain\Sale\Enums\SaleType::ClienteFinal)->sum('total');

        $accessoryCategories = ['charger', 'cable', 'case', 'accessory'];
        $otherAppleCategories = ['tablet', 'notebook', 'smartwatch', 'headphone', 'speaker'];

        $iphoneNew = 0;
        $iphoneUsed = 0;
        $iphoneCfNew = 0;
        $iphoneCfUsed = 0;
        $iphoneRepasseNew = 0;
        $iphoneRepasseUsed = 0;
        $accessories = 0;
        $otherApple = 0;

        $salesById = $sales->keyBy('id');

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
            $saleType = $salesById[$item->sale_id]?->sale_type ?? null;

            if ($category === 'smartphone') {
                $isUsed = in_array($condition, ['used', 'refurbished']);

                if ($isUsed) {
                    $iphoneUsed += $qty;
                } else {
                    $iphoneNew += $qty;
                }

                if ($saleType === \App\Domain\Sale\Enums\SaleType::ClienteFinal) {
                    if ($isUsed) {
                        $iphoneCfUsed += $qty;
                    } else {
                        $iphoneCfNew += $qty;
                    }
                } else {
                    if ($isUsed) {
                        $iphoneRepasseUsed += $qty;
                    } else {
                        $iphoneRepasseNew += $qty;
                    }
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
            'month_label' => $ref->translatedFormat('F/Y'),
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'average_ticket' => $averageTicket,
            'total_items' => $totalItems,
            'iphone_total' => $iphoneNew + $iphoneUsed,
            'iphone_new' => $iphoneNew,
            'iphone_used' => $iphoneUsed,
            'iphone_cf_total' => $iphoneCfNew + $iphoneCfUsed,
            'iphone_cf_new' => $iphoneCfNew,
            'iphone_cf_used' => $iphoneCfUsed,
            'iphone_repasse_total' => $iphoneRepasseNew + $iphoneRepasseUsed,
            'iphone_repasse_new' => $iphoneRepasseNew,
            'iphone_repasse_used' => $iphoneRepasseUsed,
            'accessories' => $accessories,
            'other_apple' => $otherApple,
            'trade_ins_received' => $tradeInsReceived,
            'repasse_total' => $repasseTotal,
            'cliente_final_total' => $clienteFinalTotal,
        ];
    }

    private function buildSalesAnalytics($sales, $allItems, $customerBySale): array
    {
        $paymentMethods = $sales
            ->groupBy(fn (Sale $s) => $s->payment_method->value)
            ->map(function ($group, $methodValue) {
                $method = PaymentMethod::tryFrom($methodValue);
                return [
                    'method' => $methodValue,
                    'label' => $method?->label() ?? ucfirst($methodValue),
                    'count' => $group->count(),
                    'total' => (float) $group->sum('total'),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();

        $pixCount = $sales->filter(fn (Sale $s) => $s->payment_method === PaymentMethod::Pix)->count();
        $pixTotal = (float) $sales->filter(fn (Sale $s) => $s->payment_method === PaymentMethod::Pix)->sum('total');

        $installmentSales = $sales->filter(fn (Sale $s) => $s->payment_method === PaymentMethod::Installment || $s->payment_method === PaymentMethod::CreditCard);
        $installmentCount = $installmentSales->count();
        $installmentTotal = (float) $installmentSales->sum('total');
        $avgInstallments = $installmentCount > 0
            ? round($installmentSales->avg('installments'), 1)
            : 0;

        $zeroMarginItems = [];
        $highMarginItems = [];
        $marginBuckets = ['negative' => 0, 'zero' => 0, 'low' => 0, 'medium' => 0, 'high' => 0, 'premium' => 0];
        $bucketSamples = ['negative' => [], 'zero' => [], 'low' => [], 'medium' => [], 'high' => [], 'premium' => []];

        foreach ($allItems as $item) {
            $profit = $item->item_profit;
            $snapshot = $item->product_snapshot ?? [];
            $name = $item->product?->name ?? $snapshot['name'] ?? 'Produto removido';
            $customer = $customerBySale->get($item->sale_id, 'Sem cliente');

            $itemData = [
                'name' => $name,
                'customer' => $customer,
                'profit' => $profit,
                'unit_price' => (float) $item->unit_price,
                'cost' => $item->total_cost_value,
                'quantity' => $item->quantity,
            ];

            if ($profit <= 0) {
                $zeroMarginItems[] = $itemData;
            }

            if ($profit >= 500) {
                $highMarginItems[] = $itemData;
            }

            if ($profit < 0) {
                $marginBuckets['negative']++;
                if (count($bucketSamples['negative']) < 3) {
                    $bucketSamples['negative'][] = $itemData;
                }
            } elseif ($profit == 0) {
                $marginBuckets['zero']++;
                if (count($bucketSamples['zero']) < 3) {
                    $bucketSamples['zero'][] = $itemData;
                }
            } elseif ($profit < 100) {
                $marginBuckets['low']++;
                if (count($bucketSamples['low']) < 3) {
                    $bucketSamples['low'][] = $itemData;
                }
            } elseif ($profit < 500) {
                $marginBuckets['medium']++;
                if (count($bucketSamples['medium']) < 3) {
                    $bucketSamples['medium'][] = $itemData;
                }
            } elseif ($profit < 1000) {
                $marginBuckets['high']++;
                if (count($bucketSamples['high']) < 3) {
                    $bucketSamples['high'][] = $itemData;
                }
            } else {
                $marginBuckets['premium']++;
                if (count($bucketSamples['premium']) < 3) {
                    $bucketSamples['premium'][] = $itemData;
                }
            }
        }

        usort($highMarginItems, fn ($a, $b) => $b['profit'] <=> $a['profit']);

        $topModelColors = $this->buildTopModelColorRanking($allItems);
        [$topNewModels, $topUsedModels] = $this->buildConditionRankings($allItems);

        return [
            'payment_methods' => $paymentMethods,
            'pix' => ['count' => $pixCount, 'total' => $pixTotal],
            'installment' => [
                'count' => $installmentCount,
                'total' => $installmentTotal,
                'avg_installments' => $avgInstallments,
            ],
            'margin_alerts' => [
                'zero_margin_count' => count($zeroMarginItems),
                'zero_margin_items' => array_slice($zeroMarginItems, 0, 5),
                'high_margin_count' => count($highMarginItems),
                'high_margin_items' => array_slice($highMarginItems, 0, 5),
            ],
            'margin_buckets' => $marginBuckets,
            'bucket_samples' => $bucketSamples,
            'top_model_colors' => $topModelColors,
            'top_new_models' => $topNewModels,
            'top_used_models' => $topUsedModels,
        ];
    }

    /**
     * Top 5 smartphones novos e top 5 seminovos vendidos no mes.
     * @return array{0: array, 1: array}
     */
    private function buildConditionRankings($allItems): array
    {
        $smartphoneItems = $allItems->filter(function ($item) {
            $rawCat = $item->product?->category ?? ($item->product_snapshot['category'] ?? null);
            $cat = $rawCat instanceof \App\Domain\Product\Enums\ProductCategory ? $rawCat->value : $rawCat;
            return $cat === 'smartphone';
        });

        $buildRanking = function ($items) {
            return $items
                ->groupBy(function ($item) {
                    $name = $item->product?->name ?? $item->product_snapshot['name'] ?? 'Produto removido';
                    return $name;
                })
                ->map(function ($group, $name) {
                    $color = null;
                    $storage = null;
                    $first = $group->first();
                    $color = $first->product?->color ?? $first->product_snapshot['color'] ?? null;
                    $storage = $first->product?->storage ?? $first->product_snapshot['storage'] ?? null;

                    return [
                        'name' => $name,
                        'color' => $color,
                        'storage' => $storage,
                        'quantity' => $group->sum('quantity'),
                    ];
                })
                ->sortByDesc('quantity')
                ->take(5)
                ->values()
                ->toArray();
        };

        $newItems = $smartphoneItems->filter(function ($item) {
            $rawCondition = $item->product?->condition ?? ($item->product_snapshot['condition'] ?? null);
            $condition = $rawCondition instanceof ProductCondition ? $rawCondition->value : $rawCondition;
            return $condition === 'new';
        });

        $usedItems = $smartphoneItems->filter(function ($item) {
            $rawCondition = $item->product?->condition ?? ($item->product_snapshot['condition'] ?? null);
            $condition = $rawCondition instanceof ProductCondition ? $rawCondition->value : $rawCondition;
            return in_array($condition, ['used', 'refurbished']);
        });

        return [$buildRanking($newItems), $buildRanking($usedItems)];
    }

    /**
     * Retorna ranking de cores por modelo de iPhone vendido no mes.
     * Agrupa modelos (Pro Max, Pro, base) e unifica cores sinonimas.
     * @return array<int, array{model: string, total: int, colors: array}>
     */
    private function buildTopModelColorRanking($allItems): array
    {
        $smartphoneItems = $allItems->filter(function ($item) {
            $rawCat = $item->product?->category ?? ($item->product_snapshot['category'] ?? null);
            $cat = $rawCat instanceof \App\Domain\Product\Enums\ProductCategory ? $rawCat->value : $rawCat;
            return $cat === 'smartphone';
        });

        if ($smartphoneItems->isEmpty()) {
            return [];
        }

        $byModel = $smartphoneItems->groupBy(function ($item) {
            $name = $item->product?->name ?? $item->product_snapshot['name'] ?? '';
            return $this->normalizeModelName($name);
        });

        $models = $byModel
            ->map(function ($items, $model) {
                $colors = $items
                    ->groupBy(function ($item) {
                        $color = $item->product?->color ?? $item->product_snapshot['color'] ?? null;
                        return $this->normalizeColorName($color);
                    })
                    ->map(fn ($colorItems, $color) => [
                        'color' => $color,
                        'quantity' => $colorItems->sum('quantity'),
                    ])
                    ->sortByDesc('quantity')
                    ->values()
                    ->toArray();

                return [
                    'model' => $model,
                    'total' => $items->sum('quantity'),
                    'colors' => $colors,
                ];
            })
            ->filter(fn ($m) => $m['total'] > 0)
            ->sortByDesc('total')
            ->values()
            ->toArray();

        return array_slice($models, 0, 5);
    }

    private function normalizeModelName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+\d+\s*GB/i', '', $name);
        $name = preg_replace('/\s+(Preto|Branco|Azul|Dourado|Prata|Cinza|Verde|Roxo|Rosa|Vermelho|Black|White|Blue|Gold|Silver|Gray|Green|Purple|Pink|Red|Natural|Desert|Teal|Titânio|Titanium|Orange|Laranja|Deep Blue|Ultramarine)\b.*/i', '', $name);
        return trim($name);
    }

    private function normalizeColorName(?string $color): string
    {
        if (! $color || trim($color) === '') {
            return 'Não informada';
        }

        $color = trim($color);
        $lower = mb_strtolower($color);

        $map = [
            'silver' => 'Prata',
            'prata' => 'Prata',
            'white' => 'Branco',
            'branco' => 'Branco',
            'black' => 'Preto',
            'preto' => 'Preto',
            'preto espacial' => 'Preto',
            'space black' => 'Preto',
            'meia-noite' => 'Preto',
            'midnight' => 'Preto',
            'blue' => 'Azul',
            'azul' => 'Azul',
            'deep blue' => 'Azul',
            'azul ultramarino' => 'Azul',
            'ultramarine' => 'Azul',
            'ocean blue' => 'Azul',
            'orange' => 'Laranja',
            'laranja' => 'Laranja',
            'gold' => 'Dourado',
            'dourado' => 'Dourado',
            'green' => 'Verde',
            'verde' => 'Verde',
            'teal' => 'Verde',
            'red' => 'Vermelho',
            'vermelho' => 'Vermelho',
            'purple' => 'Roxo',
            'roxo' => 'Roxo',
            'pink' => 'Rosa',
            'rosa' => 'Rosa',
            'natural' => 'Natural',
            'titânio natural' => 'Natural',
            'natural titanium' => 'Natural',
            'titânio preto' => 'Preto',
            'black titanium' => 'Preto',
            'titânio branco' => 'Branco',
            'white titanium' => 'Branco',
            'titânio deserto' => 'Deserto',
            'desert' => 'Deserto',
            'desert titanium' => 'Deserto',
            'gray' => 'Cinza',
            'cinza' => 'Cinza',
            'estelar' => 'Branco',
            'starlight' => 'Branco',
        ];

        return $map[$lower] ?? ucfirst($color);
    }

    /**
     * Inteligencia Atacado x Cliente Final: KPIs de repasse vs cliente final.
     */
    private function buildWholesaleIntelligence(Carbon $referenceDate): array
    {
        $start = $referenceDate->copy()->startOfMonth();
        $end = $referenceDate->copy()->endOfMonth();

        $sales = Sale::with(['items.product', 'customer'])
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $repasseSales = $sales->where('sale_type', SaleType::Repasse);
        $cfSales = $sales->where('sale_type', SaleType::ClienteFinal);

        $channelSummary = $this->buildChannelSummary($repasseSales, $cfSales);
        $topRepasseClients = $this->buildTopRepasseClients($repasseSales);
        $accumulated = $this->buildAccumulatedRanking($referenceDate);
        $monthlyEvolution = $this->buildMonthlyEvolution($referenceDate);

        return [
            'channel_summary' => $channelSummary,
            'top_repasse_clients' => $topRepasseClients,
            'accumulated_ranking' => $accumulated,
            'monthly_evolution' => $monthlyEvolution,
        ];
    }

    private function buildChannelSummary($repasseSales, $cfSales): array
    {
        $build = function ($sales, string $label) {
            $count = $sales->count();
            $revenue = (float) $sales->sum('total');
            $profit = (float) $sales->sum(fn (Sale $s) => $s->profit);
            $items = (int) $sales->flatMap->items->sum('quantity');
            $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
            $ticket = $count > 0 ? $revenue / $count : 0;

            return compact('label', 'count', 'revenue', 'profit', 'items', 'margin', 'ticket');
        };

        $repasse = $build($repasseSales, 'Repasse');
        $cf = $build($cfSales, 'Cliente Final');

        $totalRevenue = $repasse['revenue'] + $cf['revenue'];
        $totalProfit = $repasse['profit'] + $cf['profit'];
        $repasseRevenuePct = $totalRevenue > 0 ? ($repasse['revenue'] / $totalRevenue) * 100 : 0;
        $repasseProfitPct = $totalProfit > 0 ? ($repasse['profit'] / $totalProfit) * 100 : 0;

        return [
            'repasse' => $repasse,
            'cliente_final' => $cf,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'repasse_revenue_pct' => $repasseRevenuePct,
            'repasse_profit_pct' => $repasseProfitPct,
        ];
    }

    private function buildTopRepasseClients($repasseSales): array
    {
        return $repasseSales
            ->filter(fn (Sale $s) => $s->customer_id !== null)
            ->groupBy('customer_id')
            ->map(function ($sales) {
                $customer = $sales->first()->customer;
                $count = $sales->count();
                $revenue = (float) $sales->sum('total');
                $profit = (float) $sales->sum(fn (Sale $s) => $s->profit);
                $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

                return [
                    'customer_id' => $customer?->id,
                    'name' => $customer?->name ?? 'Sem cliente',
                    'phone' => $customer?->phone,
                    'count' => $count,
                    'revenue' => $revenue,
                    'profit' => $profit,
                    'margin' => $margin,
                ];
            })
            ->sortByDesc('revenue')
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Ranking acumulado de clientes de repasse nos ultimos 6 meses.
     */
    private function buildAccumulatedRanking(Carbon $referenceDate): array
    {
        $endMonth = $referenceDate->copy()->endOfMonth();
        $startMonth = $referenceDate->copy()->subMonths(5)->startOfMonth();

        $sales = Sale::with('customer')
            ->where('sale_type', SaleType::Repasse)
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->whereNotNull('customer_id')
            ->whereBetween('sold_at', [$startMonth, $endMonth])
            ->get();

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $referenceDate->copy()->subMonths($i);
            $months[] = $m->format('Y-m');
        }

        $byCustomer = $sales->groupBy('customer_id');

        $ranking = $byCustomer->map(function ($customerSales) use ($months) {
            $customer = $customerSales->first()->customer;
            $totalRevenue = (float) $customerSales->sum('total');
            $totalProfit = (float) $customerSales->sum(fn (Sale $s) => $s->profit);
            $totalCount = $customerSales->count();

            $perMonth = [];
            foreach ($months as $monthKey) {
                $monthSales = $customerSales->filter(
                    fn (Sale $s) => $s->sold_at->format('Y-m') === $monthKey
                );
                $perMonth[$monthKey] = [
                    'revenue' => (float) $monthSales->sum('total'),
                    'count' => $monthSales->count(),
                ];
            }

            $monthValues = array_column($perMonth, 'revenue');
            $firstHalf = array_sum(array_slice($monthValues, 0, 3));
            $secondHalf = array_sum(array_slice($monthValues, 3, 3));
            $trend = $firstHalf > 0
                ? round((($secondHalf - $firstHalf) / $firstHalf) * 100, 1)
                : ($secondHalf > 0 ? 100 : 0);

            return [
                'customer_id' => $customer?->id,
                'name' => $customer?->name ?? 'Sem cliente',
                'total_revenue' => $totalRevenue,
                'total_profit' => $totalProfit,
                'total_count' => $totalCount,
                'per_month' => $perMonth,
                'trend' => $trend,
            ];
        })
            ->sortByDesc('total_revenue')
            ->take(5)
            ->values()
            ->toArray();

        return [
            'months' => $months,
            'month_labels' => array_map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M'), $months),
            'clients' => $ranking,
        ];
    }

    /**
     * Evolucao mensal: lucro repasse vs cliente final nos ultimos 6 meses.
     */
    private function buildMonthlyEvolution(Carbon $referenceDate): array
    {
        $endMonth = $referenceDate->copy()->endOfMonth();
        $startMonth = $referenceDate->copy()->subMonths(5)->startOfMonth();

        $sales = Sale::with('items')
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->whereBetween('sold_at', [$startMonth, $endMonth])
            ->get();

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $referenceDate->copy()->subMonths($i);
            $months[] = $m->format('Y-m');
        }

        $repasseData = [];
        $cfData = [];
        $labels = [];

        foreach ($months as $monthKey) {
            $monthSales = $sales->filter(fn (Sale $s) => $s->sold_at->format('Y-m') === $monthKey);
            $labels[] = Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('M/y');

            $repasseMonthSales = $monthSales->where('sale_type', SaleType::Repasse);
            $cfMonthSales = $monthSales->where('sale_type', SaleType::ClienteFinal);

            $repasseData[] = (float) $repasseMonthSales->sum(fn (Sale $s) => $s->profit);
            $cfData[] = (float) $cfMonthSales->sum(fn (Sale $s) => $s->profit);
        }

        return [
            'labels' => $labels,
            'repasse' => $repasseData,
            'cliente_final' => $cfData,
        ];
    }
}
