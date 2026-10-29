<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductRepairService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ProductRepairController extends Controller
{
    public function __construct(
        private readonly ProductRepairService $repairService
    ) {}

    public function send(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->repairService->send($product, $validated['provider'] ?? null, $validated['reason'] ?? null, auth()->id());
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "{$product->name} marcado como na assistência.");
    }

    public function markReturned(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'cost' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->repairService->markReturned($product, (float) $validated['cost'], $validated['notes'] ?? null, auth()->id());
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "{$product->name} disponível novamente.");
    }
}
