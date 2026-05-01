<?php

declare(strict_types=1);

namespace App\Domain\Report\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    public function salesCsv(array $reportData): StreamedResponse
    {
        $filename = 'relatorio-vendas-' . now()->format('Y-m-d') . '.csv';

        return $this->stream($filename, function () use ($reportData) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['Venda', 'Data', 'Cliente', 'Vendedor', 'Total', 'Desconto', 'Pagamento', 'Status'], ';');

            foreach ($reportData['sales'] as $sale) {
                fputcsv($out, [
                    $sale->sale_number,
                    $sale->sold_at?->format('d/m/Y H:i'),
                    $sale->customer?->name ?? '-',
                    $sale->user?->name ?? '-',
                    number_format((float) $sale->total, 2, ',', ''),
                    number_format((float) $sale->discount, 2, ',', ''),
                    $sale->payment_method->label(),
                    $sale->payment_status->label(),
                ], ';');
            }

            fputcsv($out, []);
            fputcsv($out, ['RESUMO'], ';');
            fputcsv($out, ['Total de Vendas', $reportData['summary']['total_sales']], ';');
            fputcsv($out, ['Faturamento', number_format((float) $reportData['summary']['total_revenue'], 2, ',', '')], ';');
            fputcsv($out, ['Descontos', number_format((float) $reportData['summary']['total_discount'], 2, ',', '')], ';');
            fputcsv($out, ['Ticket Médio', number_format((float) $reportData['summary']['average_ticket'], 2, ',', '')], ';');

            fclose($out);
        });
    }

    public function stockCsv(array $reportData): StreamedResponse
    {
        $filename = 'relatorio-estoque-' . now()->format('Y-m-d') . '.csv';

        return $this->stream($filename, function () use ($reportData) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['RESUMO'], ';');
            fputcsv($out, ['Total Produtos', $reportData['summary']['total_products']], ';');
            fputcsv($out, ['Total Unidades', $reportData['summary']['total_units']], ';');
            fputcsv($out, ['Sem Estoque', $reportData['summary']['out_of_stock']], ';');
            fputcsv($out, ['Estoque Baixo', $reportData['summary']['low_stock']], ';');
            fputcsv($out, []);

            fputcsv($out, ['PRODUTOS COM ESTOQUE BAIXO'], ';');
            fputcsv($out, ['Produto', 'SKU', 'Categoria', 'Estoque Atual', 'Estoque Mínimo'], ';');

            foreach ($reportData['low_stock_products'] as $product) {
                fputcsv($out, [
                    $product->name,
                    $product->sku,
                    $product->category->label(),
                    $product->stock_quantity,
                    $product->min_stock_alert,
                ], ';');
            }

            fclose($out);
        });
    }

    public function topProductsCsv(array $reportData): StreamedResponse
    {
        $filename = 'relatorio-top-produtos-' . now()->format('Y-m-d') . '.csv';

        return $this->stream($filename, function () use ($reportData) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['#', 'Produto', 'SKU', 'Categoria', 'Total Vendido', 'Estoque Atual'], ';');

            foreach ($reportData['products'] as $index => $item) {
                fputcsv($out, [
                    $index + 1,
                    $item['product']->name,
                    $item['product']->sku,
                    $item['product']->category->label(),
                    $item['total_sold'],
                    $item['product']->stock_quantity,
                ], ';');
            }

            fclose($out);
        });
    }

    public function commissionsCsv(array $reportData): StreamedResponse
    {
        $filename = 'relatorio-comissoes-' . now()->format('Y-m-d') . '.csv';

        return $this->stream($filename, function () use ($reportData) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['RESUMO GERAL'], ';');
            fputcsv($out, ['Total Comissões', number_format($reportData['summary']['total_commissions'], 2, ',', '')], ';');
            fputcsv($out, ['Total Saques', number_format($reportData['summary']['total_withdrawn'], 2, ',', '')], ';');
            fputcsv($out, ['Saldo Pendente', number_format($reportData['summary']['total_balance'], 2, ',', '')], ';');
            fputcsv($out, []);

            fputcsv($out, ['POR VENDEDOR'], ';');
            fputcsv($out, ['Vendedor', 'Vendas', 'Faturamento', 'Comissão', 'Manuais', 'Saques', 'Saldo'], ';');

            foreach ($reportData['by_seller'] as $seller) {
                fputcsv($out, [
                    $seller['name'],
                    $seller['sales_count'],
                    number_format($seller['sales_total'], 2, ',', ''),
                    number_format($seller['commission_total'], 2, ',', ''),
                    number_format($seller['manual_total'], 2, ',', ''),
                    number_format($seller['withdrawn'], 2, ',', ''),
                    number_format($seller['balance'], 2, ',', ''),
                ], ';');
            }

            fclose($out);
        });
    }

    public function marginsCsv(array $reportData): StreamedResponse
    {
        $filename = 'relatorio-margens-' . now()->format('Y-m-d') . '.csv';

        return $this->stream($filename, function () use ($reportData) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['POR CATEGORIA'], ';');
            fputcsv($out, ['Categoria', 'Qtd Vendida', 'Faturamento', 'Custo', 'Lucro', 'Margem %'], ';');
            foreach ($reportData['by_category'] as $row) {
                fputcsv($out, [
                    $row['label'],
                    $row['quantity'],
                    number_format($row['revenue'], 2, ',', ''),
                    number_format($row['cost'], 2, ',', ''),
                    number_format($row['profit'], 2, ',', ''),
                    number_format($row['margin'], 1, ',', '') . '%',
                ], ';');
            }

            fputcsv($out, []);
            fputcsv($out, ['POR FORNECEDOR'], ';');
            fputcsv($out, ['Fornecedor', 'Qtd Vendida', 'Faturamento', 'Custo', 'Lucro', 'Margem %'], ';');
            foreach ($reportData['by_supplier'] as $row) {
                fputcsv($out, [
                    $row['supplier'],
                    $row['quantity'],
                    number_format($row['revenue'], 2, ',', ''),
                    number_format($row['cost'], 2, ',', ''),
                    number_format($row['profit'], 2, ',', ''),
                    number_format($row['margin'], 1, ',', '') . '%',
                ], ';');
            }

            fputcsv($out, []);
            fputcsv($out, ['POR CONDIÇÃO'], ';');
            fputcsv($out, ['Condição', 'Qtd Vendida', 'Faturamento', 'Custo', 'Lucro', 'Margem %'], ';');
            foreach ($reportData['by_condition'] as $row) {
                fputcsv($out, [
                    $row['label'],
                    $row['quantity'],
                    number_format($row['revenue'], 2, ',', ''),
                    number_format($row['cost'], 2, ',', ''),
                    number_format($row['profit'], 2, ',', ''),
                    number_format($row['margin'], 1, ',', '') . '%',
                ], ';');
            }

            fclose($out);
        });
    }

    public function executiveCsv(array $reportData): StreamedResponse
    {
        $filename = 'relatorio-executivo-' . now()->format('Y-m-d') . '.csv';

        return $this->stream($filename, function () use ($reportData) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['INDICADORES', 'Mês Atual', 'Mês Anterior', 'Var %', 'Mesmo Mês Ano Anterior', 'Var %'], ';');

            $metrics = [
                ['Faturamento', 'revenue'],
                ['Lucro', 'profit'],
                ['Vendas', 'count'],
                ['Ticket Médio', 'ticket'],
                ['Novos Clientes', 'new_customers'],
            ];

            $current = $reportData['current'];
            $prev = $reportData['previous_month'];
            $lastYear = $reportData['same_month_last_year'];

            foreach ($metrics as [$label, $key]) {
                $isMonetary = in_array($key, ['revenue', 'profit', 'ticket']);
                $fmt = fn ($v) => $isMonetary ? number_format((float) $v, 2, ',', '') : (string) $v;

                fputcsv($out, [
                    $label,
                    $fmt($current[$key]),
                    $fmt($prev[$key]),
                    $this->deltaLabel($current[$key], $prev[$key]),
                    $fmt($lastYear[$key]),
                    $this->deltaLabel($current[$key], $lastYear[$key]),
                ], ';');
            }

            fputcsv($out, []);
            fputcsv($out, ['EVOLUÇÃO MENSAL'], ';');
            fputcsv($out, ['Mês', 'Faturamento', 'Lucro', 'Vendas', 'Ticket Médio'], ';');
            foreach ($reportData['monthly_evolution'] as $month) {
                fputcsv($out, [
                    $month['label'],
                    number_format($month['revenue'], 2, ',', ''),
                    number_format($month['profit'], 2, ',', ''),
                    $month['count'],
                    number_format($month['ticket'], 2, ',', ''),
                ], ';');
            }

            fclose($out);
        });
    }

    private function deltaLabel(float $current, float $previous): string
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $delta = (($current - $previous) / abs($previous)) * 100;

        return ($delta >= 0 ? '+' : '') . number_format($delta, 1, ',', '') . '%';
    }

    private function stream(string $filename, callable $callback): StreamedResponse
    {
        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store',
        ]);
    }
}
