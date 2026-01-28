<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\GenerateReportUseCase;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GenerateReportUseCase $reportUseCase
    ) {}

    public function index(): View
    {
        $data = $this->reportUseCase->dashboardData();

        return view('dashboard', [
            'todayTotal' => $data['today']['total'],
            'todayCount' => $data['today']['count'],
            'monthTotal' => $data['month']['total'],
            'monthCount' => $data['month']['count'],
            'lowStockCount' => $data['low_stock']['count'],
            'lowStockProducts' => $data['low_stock']['products'],
            'topProducts' => $data['top_products'],
            'salesChart' => $data['sales_chart'],
        ]);
    }
}
