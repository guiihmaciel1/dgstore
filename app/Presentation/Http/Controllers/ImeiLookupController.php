<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\SaleItem;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\Warranty\Models\Warranty;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImeiLookupController extends Controller
{
    public function index(Request $request): View
    {
        $imei = $request->get('imei');
        $results = null;

        if ($imei) {
            $imei = preg_replace('/\D/', '', $imei);
            $results = $this->lookup($imei);
        }

        return view('imei-lookup.index', [
            'imei' => $imei,
            'results' => $results,
        ]);
    }

    private function lookup(string $imei): array
    {
        // 1. Produto no estoque
        $product = Product::withTrashed()
            ->where('imei', $imei)
            ->first();

        // 2. Venda (via produto ou snapshot com IMEI)
        $saleItem = null;
        $sale = null;

        if ($product) {
            $saleItem = SaleItem::where('product_id', $product->id)
                ->with(['sale.customer', 'sale.user'])
                ->latest()
                ->first();
            $sale = $saleItem?->sale;
        }

        // Busca alternativa: IMEI no snapshot do produto vendido
        if (!$sale) {
            $saleItem = SaleItem::whereJsonContains('product_snapshot->imei', $imei)
                ->with(['sale.customer', 'sale.user'])
                ->latest()
                ->first();
            $sale = $saleItem?->sale;
        }

        // 3. Garantia
        $warranty = Warranty::where('imei', $imei)
            ->with(['saleItem.sale.customer', 'claims'])
            ->first();

        // Se não encontrou garantia direta, buscar via sale_item
        if (!$warranty && $saleItem) {
            $warranty = Warranty::where('sale_item_id', $saleItem->id)
                ->with(['saleItem.sale.customer', 'claims'])
                ->first();
        }

        // 4. Trade-in (aparelho recebido como troca)
        $tradeIn = TradeIn::where('imei', $imei)
            ->with(['sale.customer', 'product'])
            ->first();

        // 5. Determinar status geral
        $status = $this->determineStatus($product, $sale, $warranty, $tradeIn);

        return [
            'found' => $product || $sale || $warranty || $tradeIn,
            'status' => $status,
            'product' => $product,
            'sale' => $sale,
            'sale_item' => $saleItem,
            'warranty' => $warranty,
            'trade_in' => $tradeIn,
        ];
    }

    private function determineStatus(?Product $product, $sale, $warranty, $tradeIn): array
    {
        $flags = [];

        if ($product) {
            if ($product->trashed()) {
                $flags[] = ['label' => 'Produto excluído', 'color' => 'red'];
            } elseif ($product->stock_quantity > 0) {
                $flags[] = ['label' => 'Em estoque', 'color' => 'green'];
            } else {
                $flags[] = ['label' => 'Sem estoque', 'color' => 'yellow'];
            }

            if ($product->reserved) {
                $flags[] = ['label' => 'Reservado', 'color' => 'blue'];
            }
        }

        if ($sale) {
            $flags[] = ['label' => "Vendido (#{$sale->sale_number})", 'color' => 'purple'];

            if ($sale->isCancelled()) {
                $flags[] = ['label' => 'Venda cancelada', 'color' => 'red'];
            }
        }

        if ($warranty) {
            if ($warranty->is_customer_warranty_active) {
                $flags[] = ['label' => "Garantia ativa ({$warranty->customer_days_remaining}d)", 'color' => 'green'];
            } elseif ($warranty->customer_warranty_until) {
                $flags[] = ['label' => 'Garantia expirada', 'color' => 'red'];
            }

            if ($warranty->open_claims_count > 0) {
                $flags[] = ['label' => 'Acionamento aberto', 'color' => 'orange'];
            }
        }

        if ($tradeIn) {
            $statusLabel = match ($tradeIn->status->value) {
                'pending' => 'Trade-in pendente',
                'processed' => 'Trade-in processado',
                'rejected' => 'Trade-in rejeitado',
                default => 'Trade-in',
            };
            $flags[] = ['label' => $statusLabel, 'color' => 'purple'];
        }

        if (empty($flags)) {
            $flags[] = ['label' => 'IMEI não encontrado', 'color' => 'gray'];
        }

        return $flags;
    }
}
