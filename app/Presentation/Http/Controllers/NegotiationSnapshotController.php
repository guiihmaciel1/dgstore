<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Models\Customer;
use App\Domain\Negotiation\Models\NegotiationSnapshot;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NegotiationSnapshotController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'string', 'exists:customers,id'],
            'product_description' => ['required', 'string', 'max:255'],
            'product_price' => ['required', 'numeric', 'min:0'],
            'product_cost' => ['nullable', 'numeric', 'min:0'],
            'trade_in_model' => ['nullable', 'string', 'max:255'],
            'trade_in_storage' => ['nullable', 'string', 'max:50'],
            'trade_in_battery' => ['nullable', 'integer', 'min:0', 'max:100'],
            'trade_in_value' => ['nullable', 'numeric', 'min:0'],
            'trade_in_system_value' => ['nullable', 'numeric', 'min:0'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'card_balance' => ['nullable', 'numeric', 'min:0'],
            'commission_estimate' => ['nullable', 'numeric', 'min:0'],
            'message_text' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $snapshot = NegotiationSnapshot::create([
            ...$validated,
            'user_id' => auth()->id(),
            'expires_at' => Carbon::now()->addDays(7),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'snapshot' => $snapshot->load('customer:id,name,phone'),
        ], 201);
    }

    public function forCustomer(string $customerId): JsonResponse
    {
        $snapshots = NegotiationSnapshot::active()
            ->forCustomer($customerId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json($snapshots);
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customerIds = Customer::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->pluck('id');

        if ($customerIds->isEmpty()) {
            return response()->json([]);
        }

        $snapshots = NegotiationSnapshot::active()
            ->whereIn('customer_id', $customerIds)
            ->with('customer:id,name,phone')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return response()->json($snapshots);
    }

    public function destroy(NegotiationSnapshot $snapshot): JsonResponse
    {
        $snapshot->delete();

        return response()->json(['success' => true]);
    }
}
