<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\B2B\Enums\RetailerStatus;
use App\Domain\B2B\Models\B2BRetailer;
use App\Domain\B2B\Services\B2BRetailerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminB2BRetailerController extends Controller
{
    public function __construct(
        private readonly B2BRetailerService $retailerService,
    ) {}

    public function index(Request $request): View
    {
        $retailers = $this->retailerService->list(
            search: $request->get('search'),
            status: $request->get('status'),
        );

        $statuses = RetailerStatus::cases();

        return view('admin.b2b.retailers.index', compact('retailers', 'statuses'));
    }

    public function show(B2BRetailer $retailer): View
    {
        $retailer->load(['orders' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('admin.b2b.retailers.show', compact('retailer'));
    }

    public function updateStatus(Request $request, B2BRetailer $retailer): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,blocked'],
        ]);

        $this->retailerService->updateStatus($retailer, RetailerStatus::from($validated['status']));

        return back()->with('success', "Status do lojista atualizado para: {$retailer->fresh()->status->label()}");
    }
}
