<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Commission\Models\Commission;
use App\Domain\Commission\Models\CommissionWithdrawal;
use App\Domain\Customer\Models\Customer;
use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Marketing\Models\MarketingUsedListing;
use App\Domain\Product\Models\Product;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Schedule\Models\Appointment;
use App\Domain\TimeClock\Models\TimeClockEntry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $timeClockEntries = TimeClockEntry::getTodayEntries($user->id);
        $nextPunchType = TimeClockEntry::getNextExpectedType($user->id);

        $punchMessages = [
            TimeClockEntry::TYPE_CLOCK_IN => ['Bom dia, ' . $user->name . '!', 'Registre sua chegada'],
            TimeClockEntry::TYPE_LUNCH_OUT => ['Hora do almoço?', 'Registre sua saída para almoço'],
            TimeClockEntry::TYPE_LUNCH_IN => ['De volta!', 'Registre sua volta do almoço'],
            TimeClockEntry::TYPE_CLOCK_OUT => ['Encerrando o dia?', 'Registre sua saída'],
        ];

        $punchButtonLabels = [
            TimeClockEntry::TYPE_CLOCK_IN => 'Cheguei!',
            TimeClockEntry::TYPE_LUNCH_OUT => 'Saindo para almoço',
            TimeClockEntry::TYPE_LUNCH_IN => 'Voltei do almoço',
            TimeClockEntry::TYPE_CLOCK_OUT => 'Encerrando expediente',
        ];

        $punchButtonColors = [
            TimeClockEntry::TYPE_CLOCK_IN => '#059669',
            TimeClockEntry::TYPE_LUNCH_OUT => '#d97706',
            TimeClockEntry::TYPE_LUNCH_IN => '#2563eb',
            TimeClockEntry::TYPE_CLOCK_OUT => '#7c3aed',
        ];

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $mySales = Sale::where('user_id', $user->id)
            ->whereBetween('sold_at', [$monthStart, $monthEnd])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $mySalesCount = $mySales->count();

        $totalCommissions = (float) Commission::forUser($user->id)->approved()->sum('commission_amount');
        $totalWithdrawn = (float) CommissionWithdrawal::forUser($user->id)->approved()->sum('amount');
        $commissionBalance = $totalCommissions - $totalWithdrawn;

        $monthCommissions = (float) Commission::forUser($user->id)
            ->approved()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount');

        $birthdayCustomers = Customer::whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->whereRaw('DAY(birth_date) >= ?', [now()->day])
            ->orderByRaw('DAY(birth_date) ASC')
            ->get();

        $todayAppointments = Appointment::forDate(today()->format('Y-m-d'))
            ->active()
            ->orderBy('start_time')
            ->get();

        $nextAppointment = Appointment::forDate(today()->format('Y-m-d'))
            ->active()
            ->where('start_time', '>=', now()->format('H:i:s'))
            ->orderBy('start_time')
            ->first();

        $stockItems = $this->getStockCatalog();

        return view('intern.dashboard', compact(
            'user',
            'timeClockEntries',
            'nextPunchType',
            'punchMessages',
            'punchButtonLabels',
            'punchButtonColors',
            'mySalesCount',
            'totalCommissions',
            'totalWithdrawn',
            'commissionBalance',
            'monthCommissions',
            'birthdayCustomers',
            'todayAppointments',
            'nextAppointment',
            'stockItems',
        ));
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
            ->sortBy([
                ['sort_gen', 'asc'],
                ['sort_model', 'desc'],
                ['storage', 'asc'],
                ['name', 'asc'],
            ])
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
}
