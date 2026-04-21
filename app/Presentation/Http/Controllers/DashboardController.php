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
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Models\SaleItem;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\Schedule\Enums\AppointmentStatus;
use App\Domain\Schedule\Models\Appointment;
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

        $monthSummary = $this->getMonthSummary($referenceDate);
        $followupSales = $this->getFollowupSales();
        $appleNews = $this->appleNewsService->getCached();
        $stockItems = $this->getStockCatalog();

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
            'todayPayables' => $todayPayables,
            'monthSummary' => $monthSummary,
            'followupSales' => $followupSales,
            'appleNews' => $appleNews,
            'stockItems' => $stockItems,
            'referenceDate' => $referenceDate,
            'isCurrentMonth' => $isCurrentMonth,
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

    private function getMonthSummary(?Carbon $referenceDate = null): array
    {
        $ref = $referenceDate ?? now();
        $start = $ref->copy()->startOfMonth();
        $end = $ref->copy()->endOfMonth();

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
            'month_label' => $ref->translatedFormat('F/Y'),
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
