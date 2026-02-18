<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeRetailer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPerfumeRetailerController extends Controller
{
    public function index(Request $request)
    {
        $query = PerfumeRetailer::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $retailers = $query->withCount(['orders', 'activeSamples'])->latest()->paginate(20)->withQueryString();

        return view('admin.perfumes.retailers.index', compact('retailers'));
    }

    public function create()
    {
        return view('admin.perfumes.retailers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'document'   => 'nullable|string|max:20',
            'whatsapp'   => 'required|string|max:20',
            'city'       => 'nullable|string|max:100',
            'state'      => 'nullable|string|max:2',
            'email'      => 'nullable|email|max:255',
            'status'     => 'in:active,inactive',
            'notes'      => 'nullable|string|max:1000',
        ]);

        PerfumeRetailer::create($validated);

        return redirect()->route('admin.perfumes.retailers.index')
            ->with('success', 'Lojista cadastrado com sucesso.');
    }

    public function show(PerfumeRetailer $retailer)
    {
        $retailer->load([
            'orders' => fn ($q) => $q->latest()->take(10),
            'orders.items',
            'activeSamples.product',
        ]);

        $totalOrders = $retailer->orders()->count();
        $totalSpent = $retailer->orders()->where('status', '!=', 'cancelled')->sum('total');
        $pendingPayment = $retailer->orders()
            ->whereIn('payment_status', ['pending', 'partial'])
            ->where('status', '!=', 'cancelled')
            ->sum('total') - $retailer->orders()
            ->join('perfume_payments', 'perfume_orders.id', '=', 'perfume_payments.perfume_order_id')
            ->sum('perfume_payments.amount');

        return view('admin.perfumes.retailers.show', compact('retailer', 'totalOrders', 'totalSpent', 'pendingPayment'));
    }

    public function edit(PerfumeRetailer $retailer)
    {
        return view('admin.perfumes.retailers.edit', compact('retailer'));
    }

    public function update(Request $request, PerfumeRetailer $retailer)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'document'   => 'nullable|string|max:20',
            'whatsapp'   => 'required|string|max:20',
            'city'       => 'nullable|string|max:100',
            'state'      => 'nullable|string|max:2',
            'email'      => 'nullable|email|max:255',
            'status'     => 'in:active,inactive',
            'notes'      => 'nullable|string|max:1000',
        ]);

        $retailer->update($validated);

        return redirect()->route('admin.perfumes.retailers.index')
            ->with('success', 'Lojista atualizado com sucesso.');
    }
}
