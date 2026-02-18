<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.b2b.orders.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">Pedido {{ $order->order_number }}</h2>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Itens do Pedido -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Info de Pagamento -->
            @if($order->paid_at)
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-emerald-800">PIX Recebido</p>
                        <p class="text-xs text-emerald-600">Pago em {{ $order->paid_at->format('d/m/Y \à\s H:i') }} &bull; {{ $order->formatted_total }}</p>
                    </div>
                </div>
            @elseif($order->isPendingPayment())
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Aguardando pagamento PIX</p>
                        <p class="text-xs text-yellow-600">O lojista ainda não efetuou o pagamento</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Itens do Pedido</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qtd</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Preço Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Custo Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Lucro</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $item->product_snapshot['name'] ?? 'Produto' }}
                                        @if(!empty($item->product_snapshot['storage'])) {{ $item->product_snapshot['storage'] }} @endif
                                        @if(!empty($item->product_snapshot['color'])) {{ $item->product_snapshot['color'] }} @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-right">R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 text-right">R$ {{ number_format((float) $item->cost_price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-green-600 text-right">R$ {{ number_format($item->profit, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">Total</td>
                                <td class="px-6 py-4 text-sm font-bold text-blue-600 text-right">{{ $order->formatted_total }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-green-600 text-right">{{ $order->formatted_total_profit }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Observações do Lojista</h3>
                    <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Info do Lojista -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Lojista</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Loja</dt>
                        <dd class="font-medium text-gray-900">{{ $order->retailer->store_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Responsável</dt>
                        <dd class="text-gray-900">{{ $order->retailer->owner_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Cidade</dt>
                        <dd class="text-gray-900">{{ $order->retailer->city }}/{{ $order->retailer->state }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">WhatsApp</dt>
                        <dd class="text-gray-900">{{ $order->retailer->whatsapp }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Alterar Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Gerenciar Status</h3>

                <!-- Timeline visual compacta -->
                @php
                    $flowStatuses = [
                        App\Domain\B2B\Enums\B2BOrderStatus::PendingPayment,
                        App\Domain\B2B\Enums\B2BOrderStatus::Paid,
                        App\Domain\B2B\Enums\B2BOrderStatus::Separating,
                        App\Domain\B2B\Enums\B2BOrderStatus::Ready,
                        App\Domain\B2B\Enums\B2BOrderStatus::Completed,
                    ];
                    $currentFlowIdx = collect($flowStatuses)->search(fn($s) => $s === $order->status);
                    $isCancelled = $order->status === App\Domain\B2B\Enums\B2BOrderStatus::Cancelled;
                @endphp

                <div class="flex items-center gap-1 mb-4 overflow-x-auto pb-1">
                    @foreach($flowStatuses as $idx => $fs)
                        @php
                            $done = !$isCancelled && $currentFlowIdx !== false && $idx <= $currentFlowIdx;
                            $current = !$isCancelled && $idx === $currentFlowIdx;
                        @endphp
                        <div class="flex items-center gap-1">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs shrink-0
                                {{ $current ? 'bg-blue-600 text-white ring-2 ring-blue-200' : ($done ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400') }}">
                                @if($done && !$current)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ $idx + 1 }}
                                @endif
                            </div>
                            @if(!$loop->last)
                                <div class="w-3 h-0.5 {{ $done && !$current ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @php $color = $order->status->color(); @endphp
                <p class="text-sm mb-4">
                    Atual:
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $color }}-100 text-{{ $color }}-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>
                        {{ $order->status->label() }}
                    </span>
                </p>

                @php $nextStatuses = $order->status->nextStatuses(); @endphp

                @if(count($nextStatuses) > 0)
                    <div class="space-y-2">
                        @foreach($nextStatuses as $nextStatus)
                            <form method="POST" action="{{ route('admin.b2b.orders.status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $nextStatus->value }}" />
                                @php $nextColor = $nextStatus->color(); @endphp
                                <button type="submit" class="w-full py-2.5 px-4 text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2
                                    {{ $nextStatus->value === 'cancelled'
                                        ? 'bg-red-100 text-red-700 hover:bg-red-200'
                                        : 'bg-blue-600 text-white hover:bg-blue-700' }}"
                                    @if($nextStatus->value === 'cancelled') onclick="return confirm('Tem certeza que deseja cancelar este pedido? O estoque será restaurado.')" @endif>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $nextStatus->icon() !!}</svg>
                                    @if($nextStatus->value === 'cancelled')
                                        Cancelar Pedido
                                    @else
                                        Avançar para: {{ $nextStatus->label() }}
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-2">Este pedido está finalizado.</p>
                @endif
            </div>

            <!-- WhatsApp -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Notificar via WhatsApp</h3>
                @php
                    $waMessage = "Olá {$order->retailer->owner_name}! Atualização do pedido *{$order->order_number}*:\n\nStatus: *{$order->status->label()}*\nValor: {$order->formatted_total}\n\nDG Store - Distribuidora Apple B2B";
                    $waLink = "https://wa.me/{$order->retailer->formatted_whatsapp}?text=" . urlencode($waMessage);
                @endphp
                <a href="{{ $waLink }}" target="_blank"
                   class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    Enviar Mensagem WhatsApp
                </a>
            </div>
        </div>
    </div>
</x-b2b-admin-layout>
