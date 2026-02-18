<x-perfumes-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Pedidos</h2>
            <a href="{{ route('admin.perfumes.orders.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Pedido
            </a>
        </div>
    </x-slot>

    @php
        $orderBadgeMap = ['blue' => 'bg-blue-100 text-blue-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'indigo' => 'bg-indigo-100 text-indigo-700', 'green' => 'bg-green-100 text-green-700', 'red' => 'bg-red-100 text-red-700'];
        $payBadgeMap = ['red' => 'bg-red-100 text-red-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'green' => 'bg-green-100 text-green-700'];
    @endphp

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.perfumes.orders.index') }}"
          class="mb-6 bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                <input type="text" name="search" id="search"
                       value="{{ request('search') }}"
                       placeholder="Número do pedido ou lojista"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
            </div>
            <div class="w-40">
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    <option value="">Todos</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Recebido</option>
                    <option value="separating" {{ request('status') === 'separating' ? 'selected' : '' }}>Separando</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Enviado</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregue</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="w-40">
                <label for="payment_status" class="block text-xs font-medium text-gray-500 mb-1">Pagamento</label>
                <select name="payment_status" id="payment_status"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Parcial</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Pago</option>
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Orders table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pedido</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lojista</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pagamento</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Método</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.perfumes.orders.show', $order) }}"
                               class="text-sm font-medium text-pink-600 hover:text-pink-700">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $order->retailer?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-sm font-medium text-gray-900">
                            R$ {{ number_format((float) $order->total, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3">
                            @php $orderBadgeClass = $orderBadgeMap[$order->status->badgeColor()] ?? 'bg-gray-100 text-gray-700'; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $orderBadgeClass }}">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @php $payBadgeClass = $payBadgeMap[$order->payment_status->badgeColor()] ?? 'bg-gray-100 text-gray-700'; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payBadgeClass }}">
                                {{ $order->payment_status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700">
                            {{ $order->payment_method->label() }}
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700">
                            {{ $order->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.perfumes.orders.show', $order) }}"
                               class="text-sm text-pink-600 hover:text-pink-700 font-medium">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-500 text-sm">
                            Nenhum pedido encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</x-perfumes-admin-layout>
