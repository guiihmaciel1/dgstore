<?php

namespace App\Presentation\Http\Controllers\Supplier;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Relatório físico de estoque — sem valores financeiros ou vendas.
 * Baixas são feitas manualmente pela DG Store; repasse via recibo individual.
 */
class SupplierReportController extends Controller
{
    public function index(): View
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        $available = ConsignmentStockItem::bySupplier($supplierId)
            ->available()
            ->orderBy('name')
            ->orderBy('color')
            ->get();

        $whatsappReport = $this->generateInventoryReport($available);

        return view('supplier.reports.index', compact('available', 'whatsappReport'));
    }

    private function generateInventoryReport($available): string
    {
        $supplierName = auth('supplier')->user()->supplier->name;

        $report = "📦 *Inventário Físico — {$supplierName}*\n";
        $report .= 'Atualizado em: ' . now()->format('d/m/Y H:i') . "\n\n";

        if ($available->isEmpty()) {
            $report .= "Nenhum aparelho disponível no momento.\n";
            return $report;
        }

        $report .= "*Total: {$available->count()} unidade(s)*\n";
        $report .= str_repeat('─', 36) . "\n";

        foreach ($available as $index => $item) {
            $report .= "\n" . ($index + 1) . ". *{$item->name}*";

            $details = collect([$item->storage, $item->color, $item->condition->label()])
                ->filter()
                ->join(' · ');

            if ($details) {
                $report .= "\n   {$details}";
            }

            $identifier = $item->imei ?? $item->serial_number;
            if ($identifier) {
                $report .= "\n   IMEI/Serial: {$identifier}";
            }
        }

        return $report;
    }
}
