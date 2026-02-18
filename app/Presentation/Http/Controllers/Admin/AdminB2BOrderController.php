<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\B2B\Enums\B2BOrderStatus;
use App\Domain\B2B\Models\B2BOrder;
use App\Domain\B2B\Models\B2BSetting;
use App\Domain\B2B\Services\B2BOrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminB2BOrderController extends Controller
{
    public function __construct(
        private readonly B2BOrderService $orderService,
    ) {}

    public function index(Request $request): View
    {
        $orders = $this->orderService->listForAdmin(
            search: $request->get('search'),
            status: $request->get('status'),
        );

        $statuses = B2BOrderStatus::cases();

        return view('admin.b2b.orders.index', compact('orders', 'statuses'));
    }

    public function show(B2BOrder $order): View
    {
        $order->load(['items.product', 'retailer']);

        return view('admin.b2b.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, B2BOrder $order): RedirectResponse
    {
        $allStatusValues = array_map(fn($s) => $s->value, B2BOrderStatus::cases());

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', $allStatusValues)],
        ]);

        try {
            $newStatus = B2BOrderStatus::from($validated['status']);
            $this->orderService->updateStatus($order, $newStatus);

            $order->refresh();
            $order->load('retailer');

            $companyName = B2BSetting::getCompanyName();
            $message = "Olá {$order->retailer->owner_name}! Atualização do pedido *{$order->order_number}*:\n\n"
                . "Status: *{$order->status->label()}*\n"
                . "Valor: {$order->formatted_total}\n\n"
                . "{$companyName}";
            $waLink = 'https://wa.me/' . $order->retailer->formatted_whatsapp . '?text=' . urlencode($message);

            return back()->with([
                'success' => "Status atualizado para: {$order->status->label()}",
                'whatsapp_link' => $waLink,
                'whatsapp_retailer' => $order->retailer->store_name,
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
