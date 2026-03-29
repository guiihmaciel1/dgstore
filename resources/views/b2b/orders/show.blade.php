<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                <a href="{{ route('b2b.orders') }}"
                   class="apple-btn-secondary shrink-0 p-2.5 sm:px-3 sm:py-2.5 -ml-1 sm:-ml-2"
                   aria-label="Voltar aos pedidos">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold tracking-tight text-gray-900 sm:text-2xl">{{ $order->order_number }}</h2>
                    <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">{{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                @php $color = $order->status->color(); @endphp
                <span class="apple-badge bg-{{ $color }}-100 text-{{ $color }}-800">
                    <span class="h-1.5 w-1.5 rounded-full bg-{{ $color }}-500"></span>
                    {{ $order->status->label() }}
                </span>
                @if($order->isPendingPayment())
                    <a href="{{ route('b2b.orders.pix', $order) }}" class="apple-btn-primary py-2.5 px-4 text-xs sm:text-sm shrink-0">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pagar com PIX
                    </a>
                @endif
                @php
                    $waMessage = "Olá! Gostaria de informações sobre meu pedido *{$order->order_number}* realizado em {$order->created_at->format('d/m/Y')} no valor de {$order->formatted_total}.";
                    $waLink = "https://wa.me/?text=" . urlencode($waMessage);
                @endphp
                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-green-500 bg-white px-4 py-2.5 text-xs font-semibold text-green-600 shadow-sm transition-all duration-300 hover:bg-green-50 active:scale-[0.97] sm:text-sm">
                    <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    WhatsApp
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-4 transition-all duration-300 sm:gap-6 lg:grid-cols-3">
        {{-- Coluna principal: itens + banners --}}
        <div class="space-y-4 lg:col-span-2 sm:space-y-5">
            @if($order->isPaid())
                <div class="apple-card flex items-start gap-3 border border-gray-200/80 p-4 shadow-sm transition-all duration-300 sm:items-center sm:gap-4 sm:p-5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-500/10">
                        <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold tracking-tight text-gray-900">Pagamento PIX confirmado</p>
                        <p class="mt-0.5 text-xs text-gray-500">Pago em {{ $order->paid_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                </div>
            @elseif($order->isPendingPayment())
                <div class="apple-card flex flex-col gap-4 border border-gray-200/80 p-4 shadow-sm transition-all duration-300 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:p-5">
                    <div class="flex items-start gap-3 sm:items-center sm:gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100">
                            <svg class="h-5 w-5 animate-pulse text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold tracking-tight text-gray-900">Aguardando pagamento PIX</p>
                            <p class="mt-0.5 text-xs text-gray-500">Efetue o pagamento para que o pedido seja processado</p>
                        </div>
                    </div>
                    <a href="{{ route('b2b.orders.pix', $order) }}" class="apple-btn-primary w-full shrink-0 py-2.5 text-center text-sm sm:w-auto sm:px-6">
                        Pagar agora
                    </a>
                </div>
            @endif

            <div class="apple-card overflow-hidden shadow-sm transition-all duration-300">
                <div class="border-b border-gray-100 bg-gray-50/80 px-4 py-3.5 sm:px-6 sm:py-4">
                    <h3 class="flex items-center gap-2 text-sm font-semibold tracking-tight text-gray-900 sm:text-base">
                        <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Itens do Pedido
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between gap-4 px-4 py-3.5 transition-all duration-300 hover:bg-gray-50/80 sm:px-6 sm:py-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $item->product_snapshot['name'] ?? 'Produto' }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    @if(!empty($item->product_snapshot['storage'])){{ $item->product_snapshot['storage'] }}@endif
                                    @if(!empty($item->product_snapshot['color']))&bull; {{ $item->product_snapshot['color'] }}@endif
                                </p>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $item->quantity }}x
                                    <span class="text-gray-400">R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</span>
                                </p>
                            </div>
                            <span class="shrink-0 text-sm font-semibold text-gray-900">
                                R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="-mx-px flex items-center justify-between bg-gray-900 px-4 py-4 sm:px-6 sm:py-5 rounded-none">
                    <span class="text-sm font-medium text-gray-300">Total do Pedido</span>
                    <span class="text-xl font-semibold tracking-tight text-white sm:text-2xl">{{ $order->formatted_total }}</span>
                </div>
            </div>

            @if($order->notes)
                <div class="apple-card p-4 shadow-sm transition-all duration-300 sm:p-6">
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold tracking-tight text-gray-900 sm:text-base">
                        <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Observações
                    </h3>
                    <p class="rounded-xl border border-gray-100 bg-gray-50/80 px-4 py-3 text-sm leading-relaxed text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Lateral: timeline + pagamento + ações --}}
        <div class="space-y-4 sm:space-y-5">
            <div class="apple-card p-4 shadow-sm transition-all duration-300 sm:p-6">
                <h3 class="mb-5 flex items-center gap-2 text-sm font-semibold tracking-tight text-gray-900 sm:text-base">
                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
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

                <div class="relative pl-1">
                    @foreach($allStatuses as $index => $statusEnum)
                        @php
                            $isActive = !$isCancelled && $currentIndex !== false && $index <= $currentIndex;
                            $isCurrent = !$isCancelled && $index === $currentIndex;
                        @endphp
                        <div class="relative flex gap-3 sm:gap-4 {{ !$loop->last ? 'pb-7 sm:pb-8' : '' }}">
                            @if(!$loop->last)
                                <div
                                    class="absolute left-[0.9375rem] top-[2.125rem] bottom-0 w-px sm:left-4 sm:top-9 {{ $isActive && !$isCurrent ? 'bg-blue-500/40' : 'bg-gray-200' }}"
                                    aria-hidden="true"></div>
                            @endif
                            <div class="relative z-10 flex shrink-0 justify-center" style="width: 2rem;">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-[0.625rem] font-semibold transition-all duration-300 sm:h-9 sm:w-9
                                        {{ $isCurrent ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/25 ring-4 ring-blue-500/15' : ($isActive ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-400') }}">
                                    @if($isActive && !$isCurrent)
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        <span class="flex h-4 w-4 items-center justify-center [&_svg]:h-4 [&_svg]:w-4">
                                            {!! $statusEnum->icon() !!}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="min-w-0 pt-1">
                                <p class="text-sm font-medium {{ $isCurrent ? 'text-blue-600' : ($isActive ? 'text-gray-900' : 'text-gray-400') }}">
                                    {{ $statusEnum->label() }}
                                </p>
                                @if($isCurrent)
                                    <p class="mt-1 flex items-center gap-1.5 text-xs text-blue-500">
                                        <span class="relative flex h-1.5 w-1.5">
                                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-60"></span>
                                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                        </span>
                                        Status atual
                                    </p>
                                @endif
                                @if($isActive && !$isCurrent && $statusEnum === App\Domain\B2B\Enums\B2BOrderStatus::Paid && $order->paid_at)
                                    <p class="mt-1 text-xs text-gray-400">{{ $order->paid_at->format('d/m H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($isCancelled)
                        <div class="mt-5 border-t border-gray-100 pt-5">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-500 text-white shadow-sm ring-4 ring-red-500/10 sm:h-9 sm:w-9">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold tracking-tight text-red-600">Pedido Cancelado</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="apple-card p-4 shadow-sm transition-all duration-300 sm:p-6">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold tracking-tight text-gray-900 sm:text-base">
                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Pagamento
                </h3>
                <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-300 sm:p-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500/10">
                        <span class="text-xs font-bold tracking-tight text-blue-600">PIX</span>
                    </div>
                    <div class="min-w-0">
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

            <div class="space-y-3">
                @if($order->isPendingPayment())
                    <a href="{{ route('b2b.orders.pix', $order) }}" class="apple-btn-primary w-full py-3.5 text-sm">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Efetuar Pagamento PIX
                    </a>
                @endif

                @if($order->status->value !== 'cancelled' && $order->status->value !== 'pending_payment')
                    <form method="POST" action="{{ route('b2b.orders.repeat', $order) }}">
                        @csrf
                        <button type="submit" class="apple-btn-dark w-full py-3.5 text-sm">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Repetir este Pedido
                        </button>
                    </form>
                @endif

                @php
                    $waMessage2 = "Olá! Preciso de ajuda com o pedido *{$order->order_number}*.";
                    $waLink2 = "https://wa.me/?text=" . urlencode($waMessage2);
                @endphp
                <a href="{{ $waLink2 }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-green-500 bg-white py-3.5 text-sm font-semibold text-green-600 shadow-sm transition-all duration-300 hover:bg-green-50 active:scale-[0.97]">
                    <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    Falar no WhatsApp
                </a>
            </div>
        </div>
    </div>
</x-b2b-app-layout>
