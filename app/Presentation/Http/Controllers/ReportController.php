<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\GenerateReportUseCase;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly GenerateReportUseCase $reportUseCase
    ) {}

    public function index(): View
    {
        return view('reports.index');
    }

    public function sales(Request $request): View
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        $report = $this->reportUseCase->salesReport($startDate, $endDate);

        return view('reports.sales', [
            'report' => $report,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
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

        $pdf = Pdf::loadView('reports.sales-pdf', [
            'report' => $report,
        ]);

        $filename = "relatorio-vendas-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.pdf";

        return $pdf->download($filename);
    }

    public function stock(): View
    {
        $report = $this->reportUseCase->stockReport();

        return view('reports.stock', [
            'report' => $report,
        ]);
    }

    public function topProducts(Request $request): View
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        $limit = $request->get('limit', 10);

        $report = $this->reportUseCase->topProductsReport($limit, $startDate, $endDate);

        return view('reports.top-products', [
            'report' => $report,
            'startDate' => $startDate?->format('Y-m-d'),
            'endDate' => $endDate?->format('Y-m-d'),
            'limit' => $limit,
        ]);
    }
}
