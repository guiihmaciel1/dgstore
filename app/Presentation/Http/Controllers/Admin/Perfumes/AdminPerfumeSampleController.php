<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Models\PerfumeRetailer;
use App\Domain\Perfumes\Models\PerfumeSample;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPerfumeSampleController extends Controller
{
    public function index(Request $request)
    {
        $query = PerfumeSample::with(['product', 'retailer']);

        if ($retailerId = $request->get('retailer')) {
            $query->where('perfume_retailer_id', $retailerId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $samples = $query->latest()->paginate(20)->withQueryString();
        $retailers = PerfumeRetailer::where('status', 'active')->orderBy('name')->get();

        return view('admin.perfumes.samples.index', compact('samples', 'retailers'));
    }

    public function create()
    {
        $retailers = PerfumeRetailer::where('status', 'active')->orderBy('name')->get();
        $products = PerfumeProduct::where('active', true)->orderBy('name')->get();

        return view('admin.perfumes.samples.create', compact('retailers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'perfume_product_id'  => 'required|exists:perfume_products,id',
            'perfume_retailer_id' => 'required|exists:perfume_retailers,id',
            'quantity'            => 'required|integer|min:1',
            'notes'               => 'nullable|string|max:500',
        ]);

        PerfumeSample::create([
            ...$validated,
            'delivered_at' => now(),
            'status'       => 'delivered',
        ]);

        return redirect()->route('admin.perfumes.samples.index')
            ->with('success', 'Amostra entregue com sucesso.');
    }

    public function markReturned(PerfumeSample $sample)
    {
        $sample->update([
            'status'      => 'returned',
            'returned_at' => now(),
        ]);

        return redirect()->route('admin.perfumes.samples.index')
            ->with('success', 'Amostra marcada como devolvida.');
    }
}
