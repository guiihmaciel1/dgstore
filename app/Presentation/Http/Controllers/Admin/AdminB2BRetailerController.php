<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\B2B\DTOs\CreateRetailerDTO;
use App\Domain\B2B\Enums\RetailerStatus;
use App\Domain\B2B\Models\B2BOrder;
use App\Domain\B2B\Models\B2BRetailer;
use App\Domain\B2B\Services\B2BRetailerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        $stats = [
            'total' => B2BRetailer::count(),
            'pending' => B2BRetailer::where('status', 'pending')->count(),
            'approved' => B2BRetailer::where('status', 'approved')->count(),
            'blocked' => B2BRetailer::where('status', 'blocked')->count(),
        ];

        return view('admin.b2b.retailers.index', compact('retailers', 'statuses', 'stats'));
    }

    public function create(): View
    {
        return view('admin.b2b.retailers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:18', 'unique:b2b_retailers,document'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
            'email' => ['required', 'email', 'unique:b2b_retailers,email'],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['required', 'in:pending,approved,blocked'],
        ]);

        $status = $validated['status'];
        $validated['password'] = $validated['password'] ?: Str::random(12);

        $this->retailerService->create(CreateRetailerDTO::fromArray($validated), $status);

        return redirect()->route('admin.b2b.retailers.index')
            ->with('success', 'Lojista cadastrado com sucesso!');
    }

    public function show(B2BRetailer $retailer): View
    {
        $retailer->load(['orders' => function ($q) {
            $q->latest()->limit(10);
        }]);

        $completedOrders = B2BOrder::where('b2b_retailer_id', $retailer->id)
            ->where('status', '!=', 'cancelled');

        $financialStats = [
            'total_orders' => (clone $completedOrders)->count(),
            'total_revenue' => (float) (clone $completedOrders)->sum('total'),
            'avg_ticket' => (float) (clone $completedOrders)->avg('total'),
            'last_order_at' => (clone $completedOrders)->max('created_at'),
        ];

        return view('admin.b2b.retailers.show', compact('retailer', 'financialStats'));
    }

    public function edit(B2BRetailer $retailer): View
    {
        return view('admin.b2b.retailers.edit', compact('retailer'));
    }

    public function update(Request $request, B2BRetailer $retailer): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:18', 'unique:b2b_retailers,document,' . $retailer->id],
            'whatsapp' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
            'email' => ['required', 'email', 'unique:b2b_retailers,email,' . $retailer->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $this->retailerService->update($retailer, $validated);

        return redirect()->route('admin.b2b.retailers.show', $retailer)
            ->with('success', 'Lojista atualizado com sucesso!');
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
