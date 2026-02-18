<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Relatórios B2B</h2>
                <p class="text-sm text-gray-500 mt-0.5">Análise de desempenho da distribuidora</p>
            </div>

            {{-- Filtros de período --}}
            <form method="GET" class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    @foreach(['week' => 'Semana', 'month' => 'Mês', 'quarter' => 'Trimestre', 'year' => 'Ano'] as $key => $label)
                        <a href="{{ request()->fullUrlWithQuery(['period' => $key]) }}"
                           class="px-4 py-2 text-sm font-medium transition {{ $period === $key ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>
        <p class="text-xs text-gray-400 mt-2">
            Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}
        </p>
    </x-slot>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Faturamento</p>
            <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($revenue, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $ordersCount }} pedido(s) no período</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Lucro</p>
            <p class="text-2xl font-bold text-emerald-700">R$ {{ number_format($profit, 2, ',', '.') }}</p>
            @if($revenue > 0)
                <p class="text-xs text-emerald-600 mt-1">Margem {{ number_format(($profit / $revenue) * 100, 1) }}%</p>
            @else
                <p class="text-xs text-gray-400 mt-1">Sem vendas no período</p>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ticket Médio</p>
            <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($avgTicket, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Por pedido</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pedidos</p>
            <p class="text-2xl font-bold text-gray-900">{{ $ordersCount }}</p>
            <p class="text-xs text-gray-400 mt-1">No período selecionado</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Top Lojistas --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Top Lojistas</h3>
                <p class="text-xs text-gray-400 mt-0.5">Maiores compradores no período</p>
            </div>
            @if($topRetailers->isNotEmpty())
                <div class="divide-y divide-gray-50">
                    @foreach($topRetailers as $i => $retailer)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-200 text-gray-600' : 'bg-gray-100 text-gray-500') }}">
                                    {{ $i + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $retailer->store_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $retailer->city }}/{{ $retailer->state }} &middot; {{ $retailer->orders_count }} pedido(s)</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-gray-800">R$ {{ number_format((float) $retailer->total_revenue, 2, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-gray-400 text-sm">Nenhum pedido no período.</div>
            @endif
        </div>

        {{-- Top Produtos --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Top Produtos</h3>
                <p class="text-xs text-gray-400 mt-0.5">Mais vendidos por quantidade</p>
            </div>
            @if($topProducts->isNotEmpty())
                <div class="divide-y divide-gray-50">
                    @foreach($topProducts as $i => $product)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-200 text-gray-600' : 'bg-gray-100 text-gray-500') }}">
                                    {{ $i + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $product->storage }} {{ $product->color ? '· ' . $product->color : '' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800">{{ $product->total_qty }} un.</p>
                                <p class="text-xs text-gray-400">R$ {{ number_format((float) $product->total_revenue, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-gray-400 text-sm">Nenhum produto vendido no período.</div>
            @endif
        </div>
    </div>

    {{-- Vendas por dia --}}
    @if($salesByDay->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Vendas por Dia</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Pedidos</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Faturamento</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ticket Médio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($salesByDay as $day)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($day->day)->format('d/m/Y (D)') }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700 text-center">{{ $day->orders }}</td>
                                <td class="px-5 py-3 text-sm font-medium text-gray-900 text-right">R$ {{ number_format((float) $day->revenue, 2, ',', '.') }}</td>
                                <td class="px-5 py-3 text-sm text-gray-600 text-right">R$ {{ number_format((float) $day->revenue / max(1, $day->orders), 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td class="px-5 py-3 text-sm text-gray-900">Total</td>
                            <td class="px-5 py-3 text-sm text-gray-900 text-center">{{ $ordersCount }}</td>
                            <td class="px-5 py-3 text-sm text-blue-700 text-right">R$ {{ number_format($revenue, 2, ',', '.') }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600 text-right">R$ {{ number_format($avgTicket, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    {{-- Pedidos recentes --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Pedidos no Período</h3>
        </div>
        @if($recentOrders->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pedido</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lojista</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-sm font-medium text-gray-800">
                                    <a href="{{ route('admin.b2b.orders.show', $order) }}" class="hover:text-blue-600">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $order->retailer?->store_name ?? '—' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                                        {{ $order->status->shortLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-sm font-semibold text-gray-900 text-right">{{ $order->formatted_total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-400 text-sm">Nenhum pedido no período selecionado.</div>
        @endif
    </div>
</x-b2b-admin-layout>
