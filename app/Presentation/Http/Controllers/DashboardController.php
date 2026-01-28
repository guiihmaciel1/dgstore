<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\GenerateReportUseCase;
use App\Domain\Import\Services\ImportOrderService;
use App\Domain\Reservation\Services\ReservationService;
use App\Domain\Warranty\Services\WarrantyService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GenerateReportUseCase $reportUseCase,
        private readonly WarrantyService $warrantyService,
        private readonly ImportOrderService $importService,
        private readonly ReservationService $reservationService
    ) {}

    public function index(): View
    {
        $data = $this->reportUseCase->dashboardData();

        // Alertas dos novos módulos
        $alerts = [
            'warranties_expiring' => $this->warrantyService->countExpiringSoon(30),
            'open_claims' => $this->warrantyService->countOpenClaims(),
            'imports_in_transit' => $this->importService->countInTransit(),
            'reservations_active' => $this->reservationService->countActive(),
            'reservations_expiring' => $this->reservationService->countExpiringSoon(3),
            'reservations_overdue' => $this->reservationService->countOverdue(),
        ];

        return view('dashboard', [
            'todayTotal' => $data['today']['total'],
            'todayCount' => $data['today']['count'],
            'monthTotal' => $data['month']['total'],
            'monthCount' => $data['month']['count'],
            'lowStockCount' => $data['low_stock']['count'],
            'lowStockProducts' => $data['low_stock']['products'],
            'topProducts' => $data['top_products'],
            'salesChart' => $data['sales_chart'],
            'alerts' => $alerts,
        ]);
    }
}
