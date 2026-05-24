<?php

namespace App\Presentation\Http\Controllers\Supplier;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\ConsignmentStock\Services\ConsignmentStockService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierReportController extends Controller
{
    public function __construct(
        private ConsignmentStockService $consignmentService
    ) {}

    public function index(Request $request): View
    {
        $supplierId = auth('supplier')->user()->supplier_id;
        $supplier = auth('supplier')->user()->supplier;
        
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        
        $available = ConsignmentStockItem::bySupplier($supplierId)
            ->available()
            ->orderBy('name')
            ->get();
        
        $sold = $this->consignmentService->getSoldBySupplier(
            $supplierId,
            $from,
            $to
        );
        
        $availableTotal = $available->sum(fn($item) => $item->quantity * $item->supplier_cost);
        $soldTotal = $sold->sum(fn($item) => $item->quantity * $item->supplier_cost);
        
        $whatsappReport = $this->generateWhatsAppReport(
            $supplier->name,
            $available,
            $sold,
            $from,
            $to
        );
        
        return view('supplier.reports.index', compact(
            'available',
            'sold',
            'availableTotal',
            'soldTotal',
            'from',
            'to',
            'whatsappReport'
        ));
    }

    private function generateWhatsAppReport(
        string $supplierName,
        $available,
        $sold,
        string $from,
        string $to
    ): string {
        $report = "📊 *Relatório de Estoque - {$supplierName}*\n";
        $report .= "Período: " . date('d/m/Y', strtotime($from)) . " a " . date('d/m/Y', strtotime($to)) . "\n\n";
        
        $report .= "📦 *ESTOQUE DISPONÍVEL*\n";
        $report .= str_repeat('─', 40) . "\n";
        
        if ($available->isEmpty()) {
            $report .= "Nenhum item disponível\n";
        } else {
            $totalAvailable = 0;
            foreach ($available as $item) {
                $report .= "\n▸ {$item->name}";
                if ($item->storage) $report .= " {$item->storage}";
                if ($item->color) $report .= " {$item->color}";
                $report .= "\n";
                $report .= "  IMEI: " . ($item->imei ?? $item->serial_number ?? 'N/A') . "\n";
                $report .= "  Custo: R$ " . number_format($item->supplier_cost, 2, ',', '.') . "\n";
                $totalValue = $item->quantity * $item->supplier_cost;
                $totalAvailable += $totalValue;
            }
            $report .= "\n*Total Disponível: R$ " . number_format($totalAvailable, 2, ',', '.') . "*\n";
        }
        
        $report .= "\n\n💰 *VENDIDOS NO PERÍODO*\n";
        $report .= str_repeat('─', 40) . "\n";
        
        if ($sold->isEmpty()) {
            $report .= "Nenhuma venda no período\n";
        } else {
            $totalSold = 0;
            foreach ($sold as $item) {
                $report .= "\n▸ {$item->name}";
                if ($item->storage) $report .= " {$item->storage}";
                if ($item->color) $report .= " {$item->color}";
                $report .= "\n";
                $report .= "  IMEI: " . ($item->imei ?? $item->serial_number ?? 'N/A') . "\n";
                $report .= "  Vendido em: " . $item->sold_at->format('d/m/Y') . "\n";
                $report .= "  Repasse: R$ " . number_format($item->supplier_cost, 2, ',', '.') . "\n";
                $totalValue = $item->quantity * $item->supplier_cost;
                $totalSold += $totalValue;
            }
            $report .= "\n*Total a Receber: R$ " . number_format($totalSold, 2, ',', '.') . "*\n";
        }
        
        $report .= "\n\n" . str_repeat('─', 40) . "\n";
        $report .= "Gerado em: " . now()->format('d/m/Y H:i') . "\n";
        
        return $report;
    }
}
