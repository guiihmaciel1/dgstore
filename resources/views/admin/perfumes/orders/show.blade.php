<x-perfumes-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-gray-800">Pedido {{ $order->order_number }}</h2>
                @php
                    $orderBadgeMap = ['blue' => 'bg-blue-100 text-blue-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'indigo' => 'bg-indigo-100 text-indigo-700', 'green' => 'bg-green-100 text-green-700', 'red' => 'bg-red-100 text-red-700'];
                    $payBadgeMap = ['red' => 'bg-red-100 text-red-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'green' => 'bg-green-100 text-green-700'];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $orderBadgeMap[$order->status->badgeColor()] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $order->status->label() }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payBadgeMap[$order->payment_status->badgeColor()] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $order->payment_status->label() }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.perfumes.orders.index') }}"
           class="text-sm text-pink-600 hover:text-pink-700 font-medium inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar aos pedidos
        </a>
    </div>

    @php
        $paymentMethodLabels = ['pix' => 'PIX', 'cash' => 'Dinheiro', 'transfer' => 'Transferência'];
    @endphp

    {{-- Info cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lojista</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $order->retailer->name }}</p>
            @if($order->retailer->whatsapp)
                <a href="{{ $order->retailer->whatsapp_link }}" target="_blank" rel="noopener noreferrer"
                   class="mt-1 inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                    {{ $order->retailer->whatsapp }}
                </a>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Método</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $order->payment_method->label() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $order->status->label() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pagamento</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $order->payment_status->label() }}</p>
        </div>
    </div>

    {{-- Status change form --}}
    @if(count($order->status->nextStatuses()) > 0)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6">
        <form method="POST" action="{{ route('admin.perfumes.orders.status', $order) }}" class="flex flex-wrap items-end gap-4">
            @csrf
            @method('PATCH')
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Alterar status</label>
                <select name="status" id="status" required
                        class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    @foreach($order->status->nextStatuses() as $nextStatus)
                        <option value="{{ $nextStatus->value }}">{{ $nextStatus->label() }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                Atualizar
            </button>
        </form>
    </div>
    @endif

    {{-- Items table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Itens do pedido</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produto</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Qtd</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Preço Unit</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Custo</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Lucro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($order->items as $item)
                    @php $snap = $item->product_snapshot ?? []; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-sm font-medium text-gray-900">
                            {{ $snap['name'] ?? $item->product?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">R$ {{ number_format((float) $item->cost_price, 2, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right text-sm font-medium text-gray-900">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right text-sm text-green-600 font-medium">R$ {{ number_format($item->profit, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- Totals row --}}
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
            <dl class="flex flex-wrap gap-x-8 gap-y-2 text-sm">
                <div class="flex gap-2">
                    <dt class="text-gray-500">Subtotal:</dt>
                    <dd class="font-medium text-gray-900">R$ {{ number_format((float) $order->subtotal, 2, ',', '.') }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="text-gray-500">Desconto:</dt>
                    <dd class="font-medium text-gray-900">R$ {{ number_format((float) $order->discount, 2, ',', '.') }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="text-gray-500">Total:</dt>
                    <dd class="font-bold text-gray-900">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="text-gray-500">Custo Total:</dt>
                    <dd class="font-medium text-gray-900">R$ {{ number_format($order->total_cost, 2, ',', '.') }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="text-gray-500">Lucro:</dt>
                    <dd class="font-bold text-green-600">R$ {{ number_format($order->profit, 2, ',', '.') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Payments section --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Pagamentos</h3>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Método</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Referência</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($order->payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm text-gray-700">{{ $payment->paid_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-5 py-3 text-right text-sm font-medium text-gray-900">R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</td>
                            <td class="px-5 py-3 text-sm text-gray-700">{{ $paymentMethodLabels[$payment->method] ?? $payment->method }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $payment->reference ?? '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('admin.perfumes.payments.destroy', $payment) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Remover este pagamento?')"
                                            class="text-sm text-red-600 hover:text-red-700 font-medium">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-gray-500 text-sm">Nenhum pagamento registrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mb-6 flex flex-wrap gap-6 text-sm">
                <div>
                    <span class="text-gray-500">Total pago:</span>
                    <span class="ml-2 font-bold text-green-600">R$ {{ number_format($order->total_paid, 2, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Restante:</span>
                    <span class="ml-2 font-bold {{ $order->remaining > 0 ? 'text-red-600' : 'text-gray-900' }}">R$ {{ number_format($order->remaining, 2, ',', '.') }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.perfumes.payments.store', $order) }}"
                  class="p-4 bg-gray-50 rounded-lg space-y-4">
                @csrf
                <h4 class="text-sm font-semibold text-gray-700">Adicionar pagamento</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="amount" class="block text-xs font-medium text-gray-500 mb-1">Valor (R$) *</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    </div>
                    <div>
                        <label for="method" class="block text-xs font-medium text-gray-500 mb-1">Método *</label>
                        <select name="method" id="method" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                            <option value="pix">PIX</option>
                            <option value="cash">Dinheiro</option>
                            <option value="transfer">Transferência</option>
                        </select>
                    </div>
                    <div>
                        <label for="reference" class="block text-xs font-medium text-gray-500 mb-1">Referência</label>
                        <input type="text" name="reference" id="reference"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500"
                               placeholder="ex: PIX, comprovante">
                    </div>
                    <div>
                        <label for="paid_at" class="block text-xs font-medium text-gray-500 mb-1">Data</label>
                        <input type="datetime-local" name="paid_at" id="paid_at"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    </div>
                </div>
                <div>
                    <label for="payment_notes" class="block text-xs font-medium text-gray-500 mb-1">Observações</label>
                    <textarea name="notes" id="payment_notes" rows="2"
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500"></textarea>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                    Registrar pagamento
                </button>
            </form>
        </div>
    </div>

    {{-- WhatsApp button --}}
    @php
        $lines = ["*Pedido {$order->order_number}*", "Status: {$order->status->label()}", ''];
        foreach ($order->items as $item) {
            $snap = $item->product_snapshot ?? [];
            $name = $snap['name'] ?? 'Produto';
            $lines[] = "• {$item->quantity}x {$name} - R$ " . number_format((float) $item->subtotal, 2, ',', '.');
        }
        $lines[] = '';
        $lines[] = "*Total: R$ " . number_format((float) $order->total, 2, ',', '.') . '*';
        $lines[] = "Pagamento: {$order->payment_method->label()}";
        $waMessage = implode("\n", $lines);
        $waNumber = preg_replace('/\D/', '', $order->retailer->whatsapp);
        $waLink = "https://wa.me/55{$waNumber}?text=" . urlencode($waMessage);
    @endphp
    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        </svg>
        Enviar WhatsApp
    </a>
</x-perfumes-admin-layout>
