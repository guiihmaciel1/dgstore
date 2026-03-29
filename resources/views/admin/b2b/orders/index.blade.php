<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Pedidos B2B</h2>
            <p class="text-sm text-gray-500">Lista e filtros de pedidos da distribuidora</p>
        </div>
    </x-slot>

    {{-- Filtros --}}
    <div class="apple-card p-4 sm:p-5 mb-5 sm:mb-6">
        <p class="apple-section-title mb-3">Filtros</p>
        <form method="GET" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div class="w-full sm:flex-1 sm:min-w-[200px]">
                <label for="search" class="apple-label">Buscar</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nº pedido ou lojista..."
                    class="apple-input rounded-xl shadow-sm transition-all duration-300"
                />
            </div>
            <div class="w-full sm:w-52">
                <label for="status" class="apple-label">Status</label>
                <select id="status" name="status" class="apple-select rounded-xl shadow-sm transition-all duration-300">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="apple-btn-dark w-full sm:w-auto rounded-xl shadow-sm transition-all duration-300">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- Mobile: cards --}}
    <div class="lg:hidden space-y-3 sm:space-y-4">
        @forelse($orders as $order)
            @php $color = $order->status->color(); @endphp
            <article class="apple-card p-4 sm:p-5 overflow-hidden">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('admin.b2b.orders.show', $order) }}"
                           class="text-sm font-semibold text-blue-500 hover:text-blue-600 tracking-tight truncate block">
                            {{ $order->order_number }}
                        </a>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="apple-badge shrink-0 bg-{{ $color }}-50 text-{{ $color }}-700 text-[10px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>
                        {{ $order->status->shortLabel() }}
                    </span>
                </div>

                <dl class="space-y-1.5 text-sm">
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <dt class="text-gray-400 shrink-0 text-xs">Lojista</dt>
                        <dd class="font-medium text-gray-900 truncate text-right text-xs">{{ $order->retailer->store_name ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-gray-400 shrink-0 text-xs">Total</dt>
                        <dd class="font-semibold text-gray-900 text-xs">{{ $order->formatted_total }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-gray-400 shrink-0 text-xs">Lucro</dt>
                        <dd class="font-semibold text-emerald-600 text-xs">{{ $order->formatted_total_profit }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-gray-400 shrink-0 text-xs">Pagamento</dt>
                        <dd>
                            @if($order->paid_at)
                                <span class="apple-badge bg-emerald-50 text-emerald-700 text-[10px]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                                    PIX Pago
                                </span>
                            @elseif($order->status->value === 'cancelled')
                                <span class="text-xs text-gray-300">—</span>
                            @else
                                <span class="apple-badge bg-amber-50 text-amber-700 text-[10px]">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span>
                                    </span>
                                    Aguardando
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="mt-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.b2b.orders.show', $order) }}"
                       class="apple-btn-primary w-full text-xs py-2">
                        Ver detalhes
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </article>
        @empty
            <div class="apple-card p-8 sm:p-10 text-center">
                <p class="text-sm text-gray-400">Nenhum pedido B2B registrado.</p>
            </div>
        @endforelse
    </div>

    {{-- Desktop: table --}}
    <div class="hidden lg:block apple-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pedido</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Lojista</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Data</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Lucro</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Pagamento</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/80 transition-all duration-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.b2b.orders.show', $order) }}" class="text-sm font-semibold text-blue-500 hover:text-blue-600">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->retailer->store_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                {{ $order->formatted_total }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 text-right">
                                {{ $order->formatted_total_profit }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($order->paid_at)
                                    <span class="apple-badge bg-emerald-100 text-emerald-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        PIX Pago
                                    </span>
                                @elseif($order->status->value === 'cancelled')
                                    <span class="text-xs text-gray-400">—</span>
                                @else
                                    <span class="apple-badge bg-amber-100 text-amber-700">
                                        <span class="relative flex h-1.5 w-1.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span>
                                        </span>
                                        Aguardando
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php $color = $order->status->color(); @endphp
                                <span class="apple-badge bg-{{ $color }}-100 text-{{ $color }}-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>
                                    {{ $order->status->shortLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.b2b.orders.show', $order) }}" class="text-sm font-medium text-blue-500 hover:text-blue-600 transition-colors duration-200">
                                    Detalhes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">Nenhum pedido B2B registrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5 sm:mt-6">{{ $orders->links() }}</div>
</x-b2b-admin-layout>
