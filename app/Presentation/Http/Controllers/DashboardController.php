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
use App\Domain\Sale\Enums\PaymentMethod;
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

        [$monthSummary, $salesAnalytics] = $this->getMonthSummaryAndAnalytics($referenceDate);
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
            'prevMonthProfit' => $data['prev_month_profit'],
            'systemNotifications' => $systemNotifications,
            'newLeadsWaiting' => $newLeadsWaiting,
            'birthdayCustomers' => $birthdayCustomers,
            'todayAppointments' => $todayAppointments,
            'nextAppointment' => $nextAppointment,
            'todayPayables' => $todayPayables,
            'monthSummary' => $monthSummary,
            'salesAnalytics' => $salesAnalytics,
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
}
