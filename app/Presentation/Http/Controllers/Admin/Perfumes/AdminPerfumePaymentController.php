<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeOrder;
use App\Domain\Perfumes\Models\PerfumePayment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPerfumePaymentController extends Controller
{
    public function store(Request $request, PerfumeOrder $order)
    {
        $request->validate([
            'amount'    => 'required|numeric|min:0.01',
            'method'    => 'required|in:pix,cash,transfer',
            'reference' => 'nullable|string|max:255',
            'paid_at'   => 'nullable|date',
            'notes'     => 'nullable|string|max:500',
        ]);

        $order->payments()->create([
            'amount'    => $request->input('amount'),
            'method'    => $request->input('method'),
            'reference' => $request->input('reference'),
            'paid_at'   => $request->input('paid_at', now()),
            'notes'     => $request->input('notes'),
        ]);

        $order->recalculatePaymentStatus();

        return redirect()->route('admin.perfumes.orders.show', $order)
            ->with('success', 'Pagamento registrado com sucesso.');
    }

    public function destroy(PerfumePayment $payment)
    {
        $order = $payment->order;
        $payment->delete();
        $order->recalculatePaymentStatus();

        return redirect()->route('admin.perfumes.orders.show', $order)
            ->with('success', 'Pagamento removido.');
    }
}
