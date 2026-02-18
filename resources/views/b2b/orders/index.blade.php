<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Meus Pedidos</h2>
                @if(!$orders->isEmpty())
                    <p class="text-sm text-gray-500 mt-1">{{ $orders->total() }} {{ $orders->total() === 1 ? 'pedido' : 'pedidos' }}</p>
                @endif
            </div>
            <a href="{{ route('b2b.catalog') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Novo Pedido
            </a>
        </div>
    </x-slot>

    @if($orders->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-200">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Nenhum pedido ainda</h3>
            <p class="mt-1 text-sm text-gray-500">Faça seu primeiro pedido pelo catálogo.</p>
            <a href="{{ route('b2b.catalog') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition">
                Explorar Catálogo
            </a>
        </div>
    @else
        <!-- Desktop: tabela | Mobile: cards -->
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pedido</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Itens</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('b2b.orders.show', $order) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 transition">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $order->created_at->format('d/m/Y') }}
                                <span class="text-gray-400">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'itens' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ $order->formatted_total }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $color = $order->status->color(); @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $color }}-100 text-{{ $color }}-800">
                                    @if($order->isPendingPayment())
                                        <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-{{ $color }}-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-{{ $color }}-500"></span></span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>
                                    @endif
                                    {{ $order->status->shortLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($order->isPendingPayment())
                                        <a href="{{ route('b2b.orders.pix', $order) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
                                            Pagar PIX
                                        </a>
                                    @endif
                                    <a href="{{ route('b2b.orders.show', $order) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                        Detalhes
                                    </a>
                                    @if(!in_array($order->status->value, ['cancelled', 'pending_payment']))
                                        <form method="POST" action="{{ route('b2b.orders.repeat', $order) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
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

        <!-- Mobile: cards -->
        <div class="md:hidden space-y-3">
            @foreach($orders as $order)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:border-gray-300 transition">
                    <a href="{{ $order->isPendingPayment() ? route('b2b.orders.pix', $order) : route('b2b.orders.show', $order) }}" class="block">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-bold text-blue-600">{{ $order->order_number }}</span>
                            @php $color = $order->status->color(); @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $color }}-100 text-{{ $color }}-800">
                                @if($order->isPendingPayment())
                                    <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-{{ $color }}-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-{{ $color }}-500"></span></span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>
                                @endif
                                {{ $order->status->shortLabel() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'itens' }}</p>
                            </div>
                            <span class="text-lg font-extrabold text-gray-900">{{ $order->formatted_total }}</span>
                        </div>
                    </a>
                    @if($order->isPendingPayment())
                        <a href="{{ route('b2b.orders.pix', $order) }}" class="mt-3 w-full py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded-lg transition flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
                            Pagar com PIX
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</x-b2b-app-layout>
