<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Services;

use App\Domain\Finance\Models\FinancialCategory;
use App\Domain\Finance\Models\FinancialTransaction;
use App\Domain\Finance\Services\FinanceService;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductService;
use App\Domain\Purchase\Models\Purchase;
use App\Domain\Purchase\Models\PurchaseItem;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Compras para estoque: registra a compra (e a saída do dinheiro) na hora,
 * e cada item entra no estoque quando chega, com IMEI e demais dados.
 */
class PurchaseService
{
    public const FINANCE_CATEGORY = 'Compra Fornecedor';
    public const FINANCE_REFERENCE = 'Purchase';

    public function __construct(
        private readonly FinanceService $finance,
        private readonly ProductService $products,
    ) {}

    /**
     * @param  array{supplier_id?: ?string, supplier_name?: ?string, purchased_at: string, expected_at?: ?string, account_id?: ?string, notes?: ?string, register_expense?: bool}  $data
     * @param  array<int, array{category: string, condition: string, model: string, storage?: ?string, color?: ?string, unit_cost: float|string, quantity: int|string}>  $lines
     */
    public function create(array $data, array $lines, User $user): Purchase
    {
        if (empty($lines)) {
            throw new InvalidArgumentException('Informe ao menos um item.');
        }

        return DB::transaction(function () use ($data, $lines, $user) {
            $supplier = ! empty($data['supplier_id']) ? Supplier::find($data['supplier_id']) : null;

            $purchase = Purchase::create([
                'number' => $this->nextNumber(),
                'supplier_id' => $supplier?->id,
                'supplier_name' => $supplier?->name ?? ($data['supplier_name'] ?? null),
                'purchased_at' => $data['purchased_at'],
                'expected_at' => $data['expected_at'] ?? null,
                'status' => Purchase::STATUS_PENDING,
                'account_id' => $data['account_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'user_id' => $user->id,
            ]);

            foreach ($lines as $line) {
                $quantity = max(1, (int) $line['quantity']);
                $isUnit = in_array($line['category'], PurchaseItem::UNIT_CATEGORIES, true);
                $base = [
                    'category' => $line['category'],
                    'condition' => $line['condition'],
                    'model' => trim($line['model']),
                    'storage' => $line['storage'] ?? null,
                    'color' => $line['color'] ?? null,
                    'unit_cost' => round((float) $line['unit_cost'], 2),
                    'status' => PurchaseItem::STATUS_PENDING,
                ];

                // Aparelhos: uma linha por unidade (cada um terá seu IMEI)
                foreach ($isUnit ? range(1, $quantity) : [1] as $_) {
                    $purchase->items()->create($base + ['quantity' => $isUnit ? 1 : $quantity]);
                }
            }

            $purchase->update(['total' => $this->activeTotal($purchase)]);

            if (($data['register_expense'] ?? true) && $purchase->account_id) {
                $this->syncExpense($purchase, $user);
            }

            return $purchase->fresh(['items']);
        });
    }

    /**
     * Item chegou: liga ao produto cadastrado no estoque e atualiza a compra.
     */
    public function markReceived(PurchaseItem $item, Product $product): PurchaseItem
    {
        if (! $item->isPending()) {
            throw new InvalidArgumentException('Este item já foi recebido ou cancelado.');
        }

        return DB::transaction(function () use ($item, $product) {
            $item->update([
                'status' => PurchaseItem::STATUS_RECEIVED,
                'product_id' => $product->id,
                'received_at' => now(),
            ]);
            $this->refreshStatus($item->purchase);

            return $item;
        });
    }

    /**
     * Item não veio. Com $refund, a despesa no financeiro é refeita sem ele
     * (o valor volta para a conta); sem, fica como crédito com o fornecedor.
     */
    public function cancelItem(PurchaseItem $item, bool $refund, User $user): PurchaseItem
    {
        if (! $item->isPending()) {
            throw new InvalidArgumentException('Só é possível cancelar itens que ainda não chegaram.');
        }

        return DB::transaction(function () use ($item, $refund, $user) {
            $item->update(['status' => PurchaseItem::STATUS_CANCELLED, 'cancelled_at' => now()]);
            $purchase = $item->purchase;

            if ($refund) {
                $purchase->update(['total' => $this->activeTotal($purchase)]);
                if ($purchase->financial_transaction_id) {
                    $this->syncExpense($purchase, $user);
                }
            }

            $this->refreshStatus($purchase);

            return $item;
        });
    }

    /**
     * Dados para pré-preencher o cadastro do produto no "Dar entrada".
     */
    public function productPrefill(PurchaseItem $item): array
    {
        $purchase = $item->purchase;

        return [
            'name' => $item->displayName(),
            'sku' => $this->products->generateSku($item->category, $item->model),
            'category' => $item->category,
            'condition' => $item->condition,
            'model' => $item->model,
            'storage' => $item->storage,
            'color' => $item->color,
            'cost_price' => number_format((float) $item->unit_cost, 2, '.', ''),
            'stock_quantity' => $item->quantity,
            'min_stock_alert' => 0,
            'supplier' => $purchase->supplierLabel(),
            'notes' => "Compra {$purchase->number}",
        ];
    }

    /**
     * Custo das compras pagas que ainda não chegaram (itens pendentes).
     */
    public function inTransitCost(): float
    {
        return (float) PurchaseItem::query()
            ->where('status', PurchaseItem::STATUS_PENDING)
            ->whereHas('purchase', fn ($q) => $q->where('status', '!=', Purchase::STATUS_CANCELLED))
            ->selectRaw('COALESCE(SUM(unit_cost * quantity), 0) as total')
            ->value('total');
    }

    /**
     * @return array{units: int, purchases: int, late: int, cost: float}
     */
    public function pendingSummary(): array
    {
        $open = Purchase::open()->withCount(['items as pending_units' => fn ($q) => $q->where('status', PurchaseItem::STATUS_PENDING)])->get();

        return [
            'units' => (int) $open->sum('pending_units'),
            'purchases' => $open->count(),
            'late' => $open->filter->isLate()->count(),
            'cost' => $this->inTransitCost(),
        ];
    }

    public function activeTotal(Purchase $purchase): float
    {
        return (float) $purchase->items()
            ->where('status', '!=', PurchaseItem::STATUS_CANCELLED)
            ->selectRaw('COALESCE(SUM(unit_cost * quantity), 0) as total')
            ->value('total');
    }

    private function refreshStatus(Purchase $purchase): void
    {
        $counts = $purchase->items()->selectRaw('status, COUNT(*) as n')->groupBy('status')->pluck('n', 'status');
        $pending = (int) ($counts[PurchaseItem::STATUS_PENDING] ?? 0);
        $received = (int) ($counts[PurchaseItem::STATUS_RECEIVED] ?? 0);

        $status = match (true) {
            $pending === 0 && $received === 0 => Purchase::STATUS_CANCELLED,
            $pending === 0 => Purchase::STATUS_RECEIVED,
            $received > 0 => Purchase::STATUS_PARTIAL,
            default => Purchase::STATUS_PENDING,
        };

        $purchase->update(['status' => $status]);
    }

    /**
     * Mantém uma despesa paga no financeiro com o total ativo da compra.
     * Ao mudar o total, cancela a anterior (estorna o saldo) e lança a nova.
     */
    private function syncExpense(Purchase $purchase, User $user): void
    {
        $current = $purchase->financial_transaction_id
            ? FinancialTransaction::find($purchase->financial_transaction_id)
            : null;

        if ($current && $current->status->value !== 'cancelled') {
            $this->finance->cancelTransaction($current);
        }

        $total = (float) $purchase->total;
        if ($total <= 0 || ! $purchase->account_id) {
            $purchase->update(['financial_transaction_id' => null]);

            return;
        }

        $category = FinancialCategory::firstOrCreate(
            ['name' => self::FINANCE_CATEGORY, 'type' => 'expense'],
            ['color' => '#f97316', 'is_active' => true]
        );

        $transaction = $this->finance->createTransaction([
            'account_id' => $purchase->account_id,
            'category_id' => $category->id,
            'user_id' => $user->id,
            'type' => 'expense',
            'status' => 'paid',
            'amount' => $total,
            'description' => "Compra {$purchase->number} — {$purchase->supplierLabel()}",
            'due_date' => $purchase->purchased_at->format('Y-m-d'),
            'paid_at' => $purchase->purchased_at,
            'reference_type' => self::FINANCE_REFERENCE,
            'reference_id' => $purchase->id,
        ]);

        $purchase->update(['financial_transaction_id' => $transaction->id]);
    }

    private function nextNumber(): string
    {
        $prefix = 'COMP' . now()->format('Ym');
        $last = Purchase::where('number', 'like', $prefix . '%')->orderByDesc('number')->value('number');
        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
