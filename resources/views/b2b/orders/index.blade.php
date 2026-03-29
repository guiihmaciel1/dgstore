<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-1">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Meus Pedidos</h2>
                @if(!$orders->isEmpty())
                    <p class="text-sm text-gray-500">{{ $orders->total() }} {{ $orders->total() === 1 ? 'pedido' : 'pedidos' }}</p>
                @endif
            </div>
            <a href="{{ route('b2b.catalog') }}" class="apple-btn-primary w-full sm:w-auto justify-center shrink-0 shadow-sm hover:shadow-md transition-shadow duration-300 ease-out">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Pedido
            </a>
        </div>
    </x-slot>

    <div class="space-y-4 sm:space-y-5 p-0">
        @if($orders->isEmpty())
            <div class="apple-card bg-white/80 backdrop-blur-xl text-center py-16 sm:py-20 px-4 sm:px-5 shadow-sm hover:shadow-md transition-shadow duration-300 ease-out">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-5 rounded-2xl bg-gray-50/90 backdrop-blur-sm border border-gray-100 flex items-center justify-center">
                    <svg class="h-8 w-8 sm:h-9 sm:w-9 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Nenhum pedido ainda</h3>
                <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">Faça seu primeiro pedido pelo catálogo.</p>
                <a href="{{ route('b2b.catalog') }}" class="mt-8 inline-flex apple-btn-primary shadow-sm hover:shadow-md transition-shadow duration-300 ease-out">
                    Explorar Catálogo
                </a>
            </div>
        @else
            {{-- Desktop: table from md --}}
            <div class="hidden md:block apple-card overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 ease-out">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/80 backdrop-blur-sm">
                                <th scope="col" class="px-4 lg:px-6 py-3.5 text-left apple-section-title">Pedido</th>
                                <th scope="col" class="px-4 lg:px-6 py-3.5 text-left apple-section-title">Data</th>
                                <th scope="col" class="px-4 lg:px-6 py-3.5 text-left apple-section-title">Itens</th>
                                <th scope="col" class="px-4 lg:px-6 py-3.5 text-left apple-section-title">Total</th>
                                <th scope="col" class="px-4 lg:px-6 py-3.5 text-left apple-section-title">Status</th>
                                <th scope="col" class="px-4 lg:px-6 py-3.5 text-right apple-section-title">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50/80 transition-colors duration-200 ease-out">
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('b2b.orders.show', $order) }}" class="text-sm font-semibold text-blue-500 hover:text-blue-600 transition-colors duration-200 ease-out">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $order->created_at->format('d/m/Y') }}
                                        <span class="text-gray-400 tabular-nums">{{ $order->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'itens' }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 tabular-nums">
                                        {{ $order->formatted_total }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        @php $color = $order->status->color(); @endphp
                                        <span class="apple-badge bg-{{ $color }}-100 text-{{ $color }}-800 shadow-sm">
                                            @if($order->isPendingPayment())
                                                <span class="relative flex h-1.5 w-1.5 shrink-0">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-{{ $color }}-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-{{ $color }}-500"></span>
                                                </span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500 shrink-0"></span>
                                            @endif
                                            {{ $order->status->shortLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            @if($order->isPendingPayment())
                                                <a href="{{ route('b2b.orders.pix', $order) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-amber-50 text-amber-800 hover:bg-amber-100/90 shadow-sm hover:shadow-md transition-all duration-200 ease-out">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/>
                                                    </svg>
                                                    Pagar PIX
                                                </a>
                                            @endif
                                            <a href="{{ route('b2b.orders.show', $order) }}" class="apple-btn-secondary !px-3 !py-2 !text-xs shadow-sm hover:shadow-md transition-all duration-200 ease-out">
                                                Detalhes
                                            </a>
                                            @if(!in_array($order->status->value, ['cancelled', 'pending_payment']))
                                                <form method="POST" action="{{ route('b2b.orders.repeat', $order) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-blue-500/10 text-blue-600 hover:bg-blue-500/15 shadow-sm hover:shadow-md transition-all duration-200 ease-out">
                                                        Repetir
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile: cards --}}
            <div class="md:hidden flex flex-col gap-3 sm:gap-4">
                @foreach($orders as $order)
                    <div class="apple-card p-4 sm:p-5 shadow-sm hover:shadow-md hover:border-gray-300/60 transition-all duration-300 ease-out">
                        <a href="{{ $order->isPendingPayment() ? route('b2b.orders.pix', $order) : route('b2b.orders.show', $order) }}" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/30 focus-visible:ring-offset-2 rounded-xl -m-1 p-1">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <span class="text-sm font-semibold text-blue-500 tracking-tight">{{ $order->order_number }}</span>
                                @php $color = $order->status->color(); @endphp
                                <span class="apple-badge shrink-0 bg-{{ $color }}-100 text-{{ $color }}-800 shadow-sm">
                                    @if($order->isPendingPayment())
                                        <span class="relative flex h-1.5 w-1.5 shrink-0">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-{{ $color }}-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-{{ $color }}-500"></span>
                                        </span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500 shrink-0"></span>
                                    @endif
                                    {{ $order->status->shortLabel() }}
                                </span>
                            </div>
                            <div class="flex items-end justify-between gap-3 pt-1 border-t border-gray-100">
                                <div class="space-y-0.5 min-w-0">
                                    <p class="text-xs text-gray-500 tabular-nums">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'itens' }}</p>
                                </div>
                                <span class="text-base font-semibold text-gray-900 tabular-nums tracking-tight shrink-0">{{ $order->formatted_total }}</span>
                            </div>
                        </a>
                        @if($order->isPendingPayment())
                            <a href="{{ route('b2b.orders.pix', $order) }}" class="mt-4 w-full apple-btn-primary justify-center text-xs sm:text-sm !py-2.5 shadow-sm hover:shadow-md transition-all duration-200 ease-out">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/>
                                </svg>
                                Pagar com PIX
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pt-2 sm:pt-1">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-b2b-app-layout>
