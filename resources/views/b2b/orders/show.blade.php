<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('b2b.orders') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @php $color = $order->status->color(); @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-{{ $color }}-100 text-{{ $color }}-800">
                    <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span>
                    {{ $order->status->label() }}
                </span>
                @if($order->isPendingPayment())
                    <a href="{{ route('b2b.orders.pix', $order) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg hover:bg-yellow-600 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pagar com PIX
                    </a>
                @endif
                @php
                    $waMessage = "Olá! Gostaria de informações sobre meu pedido *{$order->order_number}* realizado em {$order->created_at->format('d/m/Y')} no valor de {$order->formatted_total}.";
                    $waLink = "https://wa.me/?text=" . urlencode($waMessage);
                @endphp
                <a href="{{ $waLink }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition active:scale-95">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    WhatsApp
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Itens do Pedido -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Info de pagamento -->
            @if($order->isPaid())
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-emerald-800">Pagamento PIX confirmado</p>
                        <p class="text-xs text-emerald-600">Pago em {{ $order->paid_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                </div>
            @elseif($order->isPendingPayment())
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">Aguardando pagamento PIX</p>
                            <p class="text-xs text-yellow-600">Efetue o pagamento para que o pedido seja processado</p>
                        </div>
                    </div>
                    <a href="{{ route('b2b.orders.pix', $order) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg transition shrink-0">
                        Pagar agora
                    </a>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Itens do Pedido
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 text-sm">
                                    {{ $item->product_snapshot['name'] ?? 'Produto' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    @if(!empty($item->product_snapshot['storage'])){{ $item->product_snapshot['storage'] }}@endif
                                    @if(!empty($item->product_snapshot['color']))&bull; {{ $item->product_snapshot['color'] }}@endif
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $item->quantity }}x
                                    <span class="text-gray-400">R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</span>
                                </p>
                            </div>
                            <span class="font-bold text-gray-900 text-sm shrink-0 ml-4">
                                R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-gray-900 flex justify-between items-center">
                    <span class="font-semibold text-gray-300">Total do Pedido</span>
                    <span class="font-extrabold text-2xl text-white">{{ $order->formatted_total }}</span>
                </div>
            </div>

            @if($order->notes)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        Observações
                    </h3>
                    <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Lateral: Status + Ações -->
        <div class="space-y-4">
            <!-- Timeline de Status -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Acompanhamento
                </h3>

                @php
                    $allStatuses = [
                        App\Domain\B2B\Enums\B2BOrderStatus::PendingPayment,
                        App\Domain\B2B\Enums\B2BOrderStatus::Paid,
                        App\Domain\B2B\Enums\B2BOrderStatus::Separating,
                        App\Domain\B2B\Enums\B2BOrderStatus::Ready,
                        App\Domain\B2B\Enums\B2BOrderStatus::Completed,
                    ];
                    $currentIndex = collect($allStatuses)->search(fn($s) => $s === $order->status);
                    $isCancelled = $order->status === App\Domain\B2B\Enums\B2BOrderStatus::Cancelled;
                @endphp

                <div class="relative">
                    @foreach($allStatuses as $index => $statusEnum)
                        @php
                            $isActive = !$isCancelled && $currentIndex !== false && $index <= $currentIndex;
                            $isCurrent = !$isCancelled && $index === $currentIndex;
                        @endphp
                        <div class="flex items-start gap-3 relative {{ !$loop->last ? 'pb-6' : '' }}">
                            @if(!$loop->last)
                                <div class="absolute left-4 top-8 bottom-0 w-0.5 {{ $isActive && !$isCurrent ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                            @endif
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 relative z-10 transition-all
                                {{ $isCurrent ? 'bg-blue-600 text-white ring-4 ring-blue-100' : ($isActive ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400') }}">
                                @if($isActive && !$isCurrent)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $statusEnum->icon() !!}</svg>
                                @endif
                            </div>
                            <div class="pt-1">
                                <p class="text-sm font-medium {{ $isCurrent ? 'text-blue-700' : ($isActive ? 'text-gray-900' : 'text-gray-400') }}">
                                    {{ $statusEnum->label() }}
                                </p>
                                @if($isCurrent)
                                    <p class="text-xs text-blue-600 mt-0.5 flex items-center gap-1">
                                        <span class="relative flex h-1.5 w-1.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-500"></span>
                                        </span>
                                        Status atual
                                    </p>
                                @endif
                                @if($isActive && !$isCurrent && $statusEnum === App\Domain\B2B\Enums\B2BOrderStatus::Paid && $order->paid_at)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $order->paid_at->format('d/m H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($isCancelled)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center ring-4 ring-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-red-700">Pedido Cancelado</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Forma de Pagamento -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Pagamento
                </h3>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <span class="text-emerald-700 font-bold text-xs">PIX</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">PIX</p>
                        <p class="text-xs text-gray-500">
                            @if($order->isPaid())
                                Pago em {{ $order->paid_at->format('d/m/Y H:i') }}
                            @elseif($order->isPendingPayment())
                                Aguardando pagamento
                            @elseif($isCancelled)
                                Cancelado
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="space-y-3">
                @if($order->isPendingPayment())
                    <a href="{{ route('b2b.orders.pix', $order) }}"
                       class="w-full py-3 px-4 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-xl transition active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Efetuar Pagamento PIX
                    </a>
                @endif

                @if($order->status->value !== 'cancelled' && $order->status->value !== 'pending_payment')
                    <form method="POST" action="{{ route('b2b.orders.repeat', $order) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition active:scale-[0.98] flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Repetir este Pedido
                        </button>
                    </form>
                @endif

                @php
                    $waMessage2 = "Olá! Preciso de ajuda com o pedido *{$order->order_number}*.";
                    $waLink2 = "https://wa.me/?text=" . urlencode($waMessage2);
                @endphp
                <a href="{{ $waLink2 }}" target="_blank"
                   class="w-full py-3 px-4 border border-green-600 text-green-700 text-sm font-semibold rounded-xl hover:bg-green-50 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    Falar no WhatsApp
                </a>
            </div>
        </div>
    </div>
</x-b2b-app-layout>
