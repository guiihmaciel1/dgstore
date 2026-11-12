<?php

declare(strict_types=1);

namespace App\Domain\Finance\Services;

use App\Domain\Finance\Models\CashSnapshot;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\User\Models\User;
use Carbon\CarbonInterface;

/**
 * Caixa real da loja: banco + espécie (conferidos pela equipe) + estoque a custo.
 */
class StoreCapitalService
{
    /**
     * Capital parado em estoque próprio, a preço de custo (compra + assistência).
     * Não inclui consignado (não é da loja) nem serviços.
     *
     * @return array{total: float, units: int, groups: array<string, array{label: string, units: int, cost: float}>, missing_cost_units: int}
     */
    public function stockCapital(): array
    {
        $rows = Product::query()
            ->where('active', true)
            ->where('stock_quantity', '>', 0)
            ->where('category', '!=', ProductCategory::Service->value)
            ->selectRaw('category, `condition`')
            ->selectRaw('SUM(stock_quantity) as units')
            ->selectRaw('SUM(stock_quantity * (COALESCE(cost_price, 0) + COALESCE(repair_cost, 0))) as cost')
            ->selectRaw('SUM(CASE WHEN cost_price IS NULL OR cost_price <= 0 THEN stock_quantity ELSE 0 END) as missing_cost_units')
            ->groupBy('category', 'condition')
            ->toBase()
            ->get();

        $groups = [
            'iphone_new' => ['label' => 'iPhones novos', 'units' => 0, 'cost' => 0.0],
            'iphone_used' => ['label' => 'iPhones seminovos', 'units' => 0, 'cost' => 0.0],
            'other' => ['label' => 'Outros produtos', 'units' => 0, 'cost' => 0.0],
        ];
        $missing = 0;

        foreach ($rows as $row) {
            $key = match (true) {
                $row->category === ProductCategory::Smartphone->value && $row->condition === ProductCondition::New->value => 'iphone_new',
                $row->category === ProductCategory::Smartphone->value => 'iphone_used',
                default => 'other',
            };
            $groups[$key]['units'] += (int) $row->units;
            $groups[$key]['cost'] += (float) $row->cost;
            $missing += (int) $row->missing_cost_units;
        }

        return [
            'total' => array_sum(array_column($groups, 'cost')),
            'units' => array_sum(array_column($groups, 'units')),
            'groups' => $groups,
            'missing_cost_units' => $missing,
        ];
    }

    public function latestSnapshot(): ?CashSnapshot
    {
        return CashSnapshot::with('user:id,name')->latest('created_at')->first();
    }

    /**
     * Última conferência feita antes do início do mês da data (fechamento do mês anterior).
     */
    public function snapshotBefore(CarbonInterface $monthStart): ?CashSnapshot
    {
        return CashSnapshot::where('created_at', '<', $monthStart)->latest('created_at')->first();
    }

    /**
     * Registra uma conferência: banco e espécie informados + estoque a custo de agora.
     */
    public function record(float $bank, float $cash, ?User $user): CashSnapshot
    {
        $stock = $this->stockCapital();

        return CashSnapshot::create([
            'bank_amount' => $bank,
            'cash_amount' => $cash,
            'stock_new_cost' => $stock['groups']['iphone_new']['cost'],
            'stock_used_cost' => $stock['groups']['iphone_used']['cost'],
            'stock_other_cost' => $stock['groups']['other']['cost'],
            'user_id' => $user?->id,
        ]);
    }

    public function isBusinessHours(?CarbonInterface $at = null): bool
    {
        $at ??= now();
        $config = config('store.cash_check');

        return in_array($at->dayOfWeekIso, $config['days'], true)
            && $at->hour >= $config['start_hour']
            && $at->hour < $config['end_hour'];
    }

    /**
     * Em horário comercial e sem conferência nas últimas N horas (ou nunca conferido).
     */
    public function isCheckDue(?CashSnapshot $latest, ?CarbonInterface $at = null): bool
    {
        $at ??= now();

        if (! $this->isBusinessHours($at)) {
            return false;
        }

        return ! $latest || $latest->created_at->lte($at->copy()->subHours(config('store.cash_check.interval_hours')));
    }

    /**
     * Converte "12.345,67", "12345,67" ou "12345.67" em float. Retorna null se inválido.
     */
    public static function parseMoney(?string $value): ?float
    {
        $value = trim((string) $value);
        $value = preg_replace('/[^\d,.\-]/', '', $value);

        if ($value === '' || $value === '-') {
            return null;
        }

        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $value)) {
            // "5.000" ou "1.250.000": ponto como separador de milhar
            $value = str_replace('.', '', $value);
        }

        return is_numeric($value) ? round((float) $value, 2) : null;
    }
}
