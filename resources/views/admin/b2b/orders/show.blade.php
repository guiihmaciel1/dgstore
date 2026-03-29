<x-b2b-admin-layout>
    <x-slot name="header">
        @php $headerColor = $order->status->color(); @endphp
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-3 min-w-0">
                <a
                    href="{{ route('admin.b2b.orders.index') }}"
                    class="mt-0.5 p-2 -ml-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-300 shrink-0"
                    aria-label="Voltar à lista de pedidos"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">
                        Pedido {{ $order->order_number }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $order->created_at->format('d/m/Y \à\s H:i') }}
                        <span class="text-gray-300 mx-2" aria-hidden="true">·</span>
                        <span class="text-gray-900 font-medium">{{ $order->formatted_total }}</span>
                    </p>
                    <div class="mt-3">
                        <span class="apple-badge bg-{{ $headerColor }}-100 text-{{ $headerColor }}-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $headerColor }}-500"></span>
                            {{ $order->status->label() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">
        {{-- Coluna principal: itens e observações --}}
        <div class="lg:col-span-2 space-y-5 sm:space-y-6">
            @if($order->paid_at)
                <div class="apple-card rounded-2xl p-4 sm:p-5 border border-emerald-200/60 bg-emerald-50/50 shadow-sm transition-all duration-300 flex items-center gap-3 sm:gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-emerald-900 tracking-tight">PIX recebido</p>
                        <p class="text-xs sm:text-sm text-emerald-700/90 mt-0.5">
                            Pago em {{ $order->paid_at->format('d/m/Y \à\s H:i') }} · {{ $order->formatted_total }}
                        </p>
                    </div>
                </div>
            @elseif($order->isPendingPayment())
                <div class="apple-card rounded-2xl p-4 sm:p-5 border border-amber-200/60 bg-amber-50/50 shadow-sm transition-all duration-300 flex items-center gap-3 sm:gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-amber-900 tracking-tight">Aguardando pagamento PIX</p>
                        <p class="text-xs sm:text-sm text-amber-700/90 mt-0.5">O lojista ainda não efetuou o pagamento.</p>
                    </div>
                </div>
            @endif

            {{-- Itens: lista mobile --}}
            <div class="lg:hidden space-y-3">
                <p class="apple-section-title px-1">Itens do pedido</p>
                @foreach($order->items as $item)
                    <div class="apple-card rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-200/60 transition-all duration-300">
                        <p class="text-sm font-semibold text-gray-900 tracking-tight leading-snug">
                            {{ $item->product_snapshot['name'] ?? 'Produto' }}
                            @if(!empty($item->product_snapshot['storage'])) {{ $item->product_snapshot['storage'] }} @endif
                            @if(!empty($item->product_snapshot['color'])) {{ $item->product_snapshot['color'] }} @endif
                        </p>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">Quantidade</dt>
                                <dd class="font-medium text-gray-900">{{ $item->quantity }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">Preço unit.</dt>
                                <dd class="text-gray-900">R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">Custo unit.</dt>
                                <dd class="text-gray-500">R$ {{ number_format((float) $item->cost_price, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between gap-3 pt-2 border-t border-gray-100">
                                <dt class="text-gray-600 font-medium">Subtotal</dt>
                                <dd class="font-semibold text-gray-900">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-emerald-700 font-medium">Lucro</dt>
                                <dd class="font-semibold text-emerald-600">R$ {{ number_format($item->profit, 2, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>
                @endforeach
                <div class="apple-card rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-200/60 bg-gray-50/80 transition-all duration-300">
                    <div class="flex justify-between gap-3 text-sm">
                        <span class="font-semibold text-gray-900">Total</span>
                        <span class="font-bold text-blue-500">{{ $order->formatted_total }}</span>
                    </div>
                    <div class="flex justify-between gap-3 text-sm mt-2">
                        <span class="font-semibold text-emerald-800">Lucro total</span>
                        <span class="font-bold text-emerald-600">{{ $order->formatted_total_profit }}</span>
                    </div>
                </div>
            </div>

            {{-- Itens: tabela desktop --}}
            <div class="hidden lg:block apple-card rounded-2xl shadow-sm border border-gray-200/60 transition-all duration-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-gray-900 tracking-tight">Itens do pedido</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produto</th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Qtd</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Preço Unit.</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Custo Unit.</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Subtotal</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Lucro</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50/50 transition-all duration-300">
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $item->product_snapshot['name'] ?? 'Produto' }}
                                        @if(!empty($item->product_snapshot['storage'])) {{ $item->product_snapshot['storage'] }} @endif
                                        @if(!empty($item->product_snapshot['color'])) {{ $item->product_snapshot['color'] }} @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-right">R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 text-right">R$ {{ number_format((float) $item->cost_price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-emerald-600 text-right">R$ {{ number_format($item->profit, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/80">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">Total</td>
                                <td class="px-6 py-4 text-sm font-bold text-blue-500 text-right">{{ $order->formatted_total }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-emerald-600 text-right">{{ $order->formatted_total_profit }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->notes)
                <div class="apple-card rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-200/60 transition-all duration-300">
                    <p class="apple-section-title mb-3">Observações do lojista</p>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5 sm:space-y-6">
            <div class="apple-card rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-200/60 transition-all duration-300">
                <p class="apple-section-title mb-4">Lojista</p>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="apple-label mb-1">Loja</dt>
                        <dd class="font-medium text-gray-900">{{ $order->retailer->store_name }}</dd>
                    </div>
                    <div>
                        <dt class="apple-label mb-1">Responsável</dt>
                        <dd class="text-gray-900">{{ $order->retailer->owner_name }}</dd>
                    </div>
                    <div>
                        <dt class="apple-label mb-1">Cidade</dt>
                        <dd class="text-gray-900">{{ $order->retailer->city }}/{{ $order->retailer->state }}</dd>
                    </div>
                    <div>
                        <dt class="apple-label mb-1">WhatsApp</dt>
                        <dd class="text-gray-900 tabular-nums">{{ $order->retailer->whatsapp }}</dd>
                    </div>
                </dl>
            </div>

            <div class="apple-card rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-200/60 transition-all duration-300">
                <p class="apple-section-title mb-4">Gerenciar status</p>

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

                <div class="flex items-center gap-1 mb-5 overflow-x-auto pb-1 -mx-1 px-1">
                    @foreach($flowStatuses as $idx => $fs)
                        @php
                            $done = !$isCancelled && $currentFlowIdx !== false && $idx <= $currentFlowIdx;
                            $current = !$isCancelled && $idx === $currentFlowIdx;
                        @endphp
                        <div class="flex items-center gap-1">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold shrink-0 transition-all duration-300
                                {{ $current ? 'bg-blue-500 text-white ring-2 ring-blue-500/25' : ($done ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400') }}">
                                @if($done && !$current)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    {{ $idx + 1 }}
                                @endif
                            </div>
                            @if(!$loop->last)
                                <div class="w-4 h-0.5 rounded-full transition-colors duration-300 {{ $done && !$current ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @php $color = $order->status->color(); @endphp
                <p class="text-sm text-gray-600 mb-5">
                    <span class="text-gray-500">Atual:</span>
                    <span class="apple-badge ml-2 bg-{{ $color }}-100 text-{{ $color }}-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>
                        {{ $order->status->label() }}
                    </span>
                </p>

                @php $nextStatuses = $order->status->nextStatuses(); @endphp

                @if(count($nextStatuses) > 0)
                    <div class="space-y-2.5">
                        @foreach($nextStatuses as $nextStatus)
                            <form method="POST" action="{{ route('admin.b2b.orders.status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $nextStatus->value }}" />
                                <button
                                    type="submit"
                                    class="w-full py-3 px-4 text-sm font-semibold rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-sm active:scale-[0.98]
                                        {{ $nextStatus->value === 'cancelled'
                                            ? 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100'
                                            : ($nextStatus->value === 'paid'
                                                ? 'apple-btn-primary'
                                                : 'bg-blue-500 hover:bg-blue-600 text-white shadow-sm shadow-blue-500/20') }}"
                                    @if($nextStatus->value === 'cancelled') onclick="return confirm('Tem certeza que deseja cancelar este pedido? O estoque será restaurado.')" @endif
                                >
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $nextStatus->icon() !!}</svg>
                                    @if($nextStatus->value === 'cancelled')
                                        Cancelar pedido
                                    @elseif($nextStatus->value === 'paid')
                                        Confirmar pagamento recebido
                                    @else
                                        Avançar para: {{ $nextStatus->label() }}
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-3">Este pedido está finalizado.</p>
                @endif
            </div>

            <div class="apple-card rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-200/60 transition-all duration-300">
                <p class="apple-section-title mb-4">Notificar via WhatsApp</p>
                @php
                    $companyName = \App\Domain\B2B\Models\B2BSetting::getCompanyName();
                    $waMessage = "Olá {$order->retailer->owner_name}! Atualização do pedido *{$order->order_number}*:\n\nStatus: *{$order->status->label()}*\nValor: {$order->formatted_total}\n\n{$companyName}";
                    $waLink = "https://wa.me/{$order->retailer->formatted_whatsapp}?text=" . urlencode($waMessage);
                @endphp
                <a
                    href="{{ $waLink }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-300 active:scale-[0.98]"
                >
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    Enviar mensagem WhatsApp
                </a>
            </div>
        </div>
    </div>
</x-b2b-admin-layout>
