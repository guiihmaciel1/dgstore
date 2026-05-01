<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\GenerateReportUseCase;
use App\Domain\Report\Services\ReportExportService;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly GenerateReportUseCase $reportUseCase,
        private readonly ReportExportService $exportService,
    ) {}

    public function index(): View
    {
        return view('reports.index');
    }

    // ─── Vendas ───

    public function sales(Request $request): View
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'compare_start_date' => 'nullable|date',
            'compare_end_date' => 'nullable|date',
        ]);

        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        $report = $this->reportUseCase->salesReport($startDate, $endDate);

        $comparison = null;
        if ($request->filled('compare_start_date') && $request->filled('compare_end_date')) {
            $compareStart = Carbon::parse($request->compare_start_date)->startOfDay();
            $compareEnd = Carbon::parse($request->compare_end_date)->endOfDay();
            $comparison = $this->reportUseCase->salesComparison(
                $startDate, $endDate, $compareStart, $compareEnd
            );
        }

        return view('reports.sales', [
            'report' => $report,
            'comparison' => $comparison,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'compareStartDate' => $request->get('compare_start_date'),
            'compareEndDate' => $request->get('compare_end_date'),
        ]);
    }

    public function salesPdf(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        $report = $this->reportUseCase->salesReport($startDate, $endDate);
        $pdf = Pdf::loadView('reports.sales-pdf', ['report' => $report]);

        return $pdf->download("relatorio-vendas-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.pdf");
    }

    public function salesExport(Request $request): StreamedResponse
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        return $this->exportService->salesCsv(
            $this->reportUseCase->salesReport($startDate, $endDate)
        );
    }

    // ─── Estoque ───

    public function stock(): View
    {
        return view('reports.stock', [
            'report' => $this->reportUseCase->stockReport(),
        ]);
    }

    public function stockExport(): StreamedResponse
    {
        return $this->exportService->stockCsv(
            $this->reportUseCase->stockReport()
        );
    }

    // ─── Top Produtos ───

    public function topProducts(Request $request): View
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        $limit = (int) $request->get('limit', 10);

        return view('reports.top-products', [
            'report' => $this->reportUseCase->topProductsReport($limit, $startDate, $endDate),
            'startDate' => $startDate?->format('Y-m-d'),
            'endDate' => $endDate?->format('Y-m-d'),
            'limit' => $limit,
        ]);
    }

    public function topProductsExport(Request $request): StreamedResponse
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        $limit = (int) $request->get('limit', 10);

        return $this->exportService->topProductsCsv(
            $this->reportUseCase->topProductsReport($limit, $startDate, $endDate)
        );
    }

    // ─── Margens ───

    public function margins(Request $request): View
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        return view('reports.margins', [
            'report' => $this->reportUseCase->marginsReport($startDate, $endDate),
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }

    public function marginsExport(Request $request): StreamedResponse
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        return $this->exportService->marginsCsv(
            $this->reportUseCase->marginsReport($startDate, $endDate)
        );
    }

    // ─── Comissões ───

    public function commissions(Request $request): View
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'user_id' => 'nullable|string|exists:users,id',
        ]);

        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        return view('reports.commissions', [
            'report' => $this->reportUseCase->commissionsReport(
                $startDate, $endDate, $request->get('user_id')
            ),
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'selectedUserId' => $request->get('user_id'),
        ]);
    }

    public function commissionsExport(Request $request): StreamedResponse
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        return $this->exportService->commissionsCsv(
            $this->reportUseCase->commissionsReport(
                $startDate, $endDate, $request->get('user_id')
            )
        );
    }

    // ─── Dashboard Executivo ───

    public function executive(Request $request): View
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $referenceDate = Carbon::createFromDate($year, $month, 1);

        return view('reports.executive', [
            'report' => $this->reportUseCase->executiveReport($referenceDate),
            'referenceDate' => $referenceDate,
            'isCurrentMonth' => $referenceDate->isSameMonth(now()),
        ]);
    }

    public function executiveExport(Request $request): StreamedResponse
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $referenceDate = Carbon::createFromDate($year, $month, 1);

        return $this->exportService->executiveCsv(
            $this->reportUseCase->executiveReport($referenceDate)
        );
    }
}
