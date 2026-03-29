<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Relatórios B2B</h2>
                <p class="text-sm text-gray-500 mt-1">Análise de desempenho da distribuidora</p>
            </div>
            <div class="inline-flex items-center gap-2 text-xs text-gray-500 shrink-0">
                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="tabular-nums">{{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }}</span>
            </div>
        </div>
    </x-slot>

    {{-- Filtro de período --}}
    <div class="apple-card p-4 sm:p-6 mb-6 sm:mb-8 transition-shadow duration-200 hover:shadow-md">
        <form method="GET" action="{{ route('admin.b2b.reports.index') }}" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div class="w-full sm:flex-1 sm:min-w-[200px]">
                <label for="report-period" class="apple-label">Período</label>
                <select id="report-period" name="period" class="apple-select mt-0">
                    @foreach(['week' => 'Semana', 'month' => 'Mês', 'quarter' => 'Trimestre', 'year' => 'Ano'] as $key => $label)
                        <option value="{{ $key }}" @selected($period === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:shrink-0 w-full sm:w-auto">
                <button type="submit" class="apple-btn-primary w-full sm:w-auto">
                    Aplicar
                </button>
                <a href="{{ route('admin.b2b.reports.index') }}" class="apple-btn-secondary w-full sm:w-auto text-center">
                    Redefinir
                </a>
            </div>
        </form>
        <p class="text-xs text-gray-400 mt-4 pt-4 border-t border-gray-100">
            Comparando pedidos não cancelados no intervalo acima.
        </p>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 mb-6 sm:mb-8">
        <div class="apple-card p-4 sm:p-6 transition-shadow duration-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-3 mb-3 sm:mb-4">
                <span class="apple-section-title">Faturamento</span>
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight tabular-nums">R$ {{ number_format($revenue, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $ordersCount }} pedido(s) no período</p>
        </div>

        <div class="apple-card p-4 sm:p-6 transition-shadow duration-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-3 mb-3 sm:mb-4">
                <span class="apple-section-title">Lucro</span>
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight tabular-nums">R$ {{ number_format($profit, 2, ',', '.') }}</p>
            @if($revenue > 0)
                <p class="text-xs text-blue-500 font-medium mt-2">Margem {{ number_format(($profit / $revenue) * 100, 1) }}%</p>
            @else
                <p class="text-xs text-gray-500 mt-2">Sem vendas no período</p>
            @endif
        </div>

        <div class="apple-card p-4 sm:p-6 transition-shadow duration-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-3 mb-3 sm:mb-4">
                <span class="apple-section-title">Ticket médio</span>
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight tabular-nums">R$ {{ number_format($avgTicket, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-2">Por pedido</p>
        </div>

        <div class="apple-card p-4 sm:p-6 transition-shadow duration-200 hover:shadow-md sm:col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between gap-3 mb-3 sm:mb-4">
                <span class="apple-section-title">Pedidos</span>
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight tabular-nums">{{ $ordersCount }}</p>
            <p class="text-xs text-gray-500 mt-2">No período selecionado</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-6 mb-6 lg:mb-8">
        {{-- Top Lojistas --}}
        <div class="apple-card overflow-hidden transition-shadow duration-200 hover:shadow-md">
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Top lojistas</h3>
                <p class="text-xs text-gray-500 mt-1">Maiores compradores no período</p>
            </div>
            @if($topRetailers->isNotEmpty())
                <div class="divide-y divide-gray-100">
                    @foreach($topRetailers as $i => $retailer)
                        <div class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-6 sm:py-4 active:bg-gray-50 sm:hover:bg-gray-50/80 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 {{ $i === 0 ? 'bg-blue-50 text-blue-600' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-gray-50 text-gray-500') }}">
                                    {{ $i + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 tracking-tight truncate">{{ $retailer->store_name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $retailer->city }}/{{ $retailer->state }} · {{ $retailer->orders_count }} pedido(s)</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 tracking-tight shrink-0 tabular-nums">R$ {{ number_format((float) $retailer->total_revenue, 2, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-10 sm:py-14 text-center text-sm text-gray-500">Nenhum pedido no período.</div>
            @endif
        </div>

        {{-- Top Produtos --}}
        <div class="apple-card overflow-hidden transition-shadow duration-200 hover:shadow-md">
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Top produtos</h3>
                <p class="text-xs text-gray-500 mt-1">Mais vendidos por quantidade</p>
            </div>
            @if($topProducts->isNotEmpty())
                <div class="divide-y divide-gray-100">
                    @foreach($topProducts as $i => $product)
                        <div class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-6 sm:py-4 active:bg-gray-50 sm:hover:bg-gray-50/80 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 {{ $i === 0 ? 'bg-blue-50 text-blue-600' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-gray-50 text-gray-500') }}">
                                    {{ $i + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 tracking-tight truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $product->storage }}{{ $product->color ? ' · ' . $product->color : '' }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-semibold text-gray-900 tracking-tight tabular-nums">{{ $product->total_qty }} un.</p>
                                <p class="text-xs text-gray-500 mt-0.5 tabular-nums">R$ {{ number_format((float) $product->total_revenue, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-10 sm:py-14 text-center text-sm text-gray-500">Nenhum produto vendido no período.</div>
            @endif
        </div>
    </div>

    {{-- Gráfico e tabela: vendas por dia --}}
    @if($salesByDay->isNotEmpty())
        @php
            $maxDayRevenue = max(1e-9, (float) $salesByDay->max('revenue'));
            $chartInnerH = 160;
        @endphp
        <div class="apple-card overflow-hidden mb-6 lg:mb-8 transition-shadow duration-200 hover:shadow-md">
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Vendas por dia</h3>
                <p class="text-xs text-gray-500 mt-1">Faturamento diário no período</p>
            </div>
            <div class="px-3 py-5 sm:px-6 sm:py-8 overflow-x-auto">
                <div class="min-w-[280px] sm:min-w-0">
                    <div class="flex items-end justify-between gap-0.5 sm:gap-1.5 border-b border-gray-200 pb-px" style="min-height: {{ $chartInnerH }}px;">
                        @foreach($salesByDay as $day)
                            @php
                                $barPx = (int) max(4, round(((float) $day->revenue / $maxDayRevenue) * $chartInnerH));
                            @endphp
                            <div class="flex-1 min-w-0 flex flex-col items-center justify-end group self-end" style="height: {{ $chartInnerH }}px;">
                                <span class="text-[10px] sm:text-xs text-gray-400 mb-1 opacity-0 sm:group-hover:opacity-100 transition-opacity truncate max-w-full text-center hidden sm:block tabular-nums" title="{{ \Carbon\Carbon::parse($day->day)->format('d/m/Y') }}">
                                    R$ {{ number_format((float) $day->revenue, 0, ',', '.') }}
                                </span>
                                <div
                                    class="w-full max-w-[10px] sm:max-w-[14px] md:max-w-5 mx-auto rounded-t-lg bg-blue-500/90 hover:bg-blue-500 transition-colors shadow-sm"
                                    style="height: {{ $barPx }}px;"
                                    role="img"
                                    aria-label="{{ \Carbon\Carbon::parse($day->day)->format('d/m/Y') }}: R$ {{ number_format((float) $day->revenue, 2, ',', '.') }}"
                                ></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between gap-0.5 sm:gap-1.5 mt-2 text-[10px] sm:text-xs text-gray-400">
                        @foreach($salesByDay as $day)
                            <span class="flex-1 min-w-0 text-center truncate tabular-nums">{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="apple-card overflow-hidden mb-6 lg:mb-8 transition-shadow duration-200 hover:shadow-md">
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Detalhamento diário</h3>
                <p class="text-xs text-gray-500 mt-1 md:hidden">Resumo por dia no período</p>
            </div>

            {{-- Mobile: cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($salesByDay as $day)
                    <div class="px-4 py-4 space-y-2">
                        <p class="text-sm font-semibold text-gray-900 tracking-tight">{{ \Carbon\Carbon::parse($day->day)->format('d/m/Y') }}</p>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-[11px] font-medium text-gray-400">Pedidos</span>
                                <p class="text-gray-900 font-medium mt-0.5 tabular-nums">{{ $day->orders }}</p>
                            </div>
                            <div>
                                <span class="text-[11px] font-medium text-gray-400">Faturamento</span>
                                <p class="text-blue-500 font-semibold mt-0.5 tabular-nums">R$ {{ number_format((float) $day->revenue, 2, ',', '.') }}</p>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[11px] font-medium text-gray-400">Ticket médio</span>
                                <p class="text-gray-700 mt-0.5 tabular-nums">R$ {{ number_format((float) $day->revenue / max(1, $day->orders), 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="px-4 py-4 bg-gray-50/80 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Total</p>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <span class="text-gray-900 tabular-nums"><strong>{{ $ordersCount }}</strong> pedidos</span>
                        <span class="text-blue-500 font-semibold tabular-nums">R$ {{ number_format($revenue, 2, ',', '.') }}</span>
                        <span class="text-gray-600 tabular-nums">Ticket R$ {{ number_format($avgTicket, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Desktop: table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/90 border-b border-gray-100">
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Data</th>
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Pedidos</th>
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Faturamento</th>
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Ticket médio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($salesByDay as $day)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 lg:px-6 py-3.5 text-sm text-gray-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($day->day)->format('d/m/Y (D)') }}</td>
                                <td class="px-4 lg:px-6 py-3.5 text-sm text-gray-700 text-center tabular-nums">{{ $day->orders }}</td>
                                <td class="px-4 lg:px-6 py-3.5 text-sm font-medium text-gray-900 text-right whitespace-nowrap tabular-nums">R$ {{ number_format((float) $day->revenue, 2, ',', '.') }}</td>
                                <td class="px-4 lg:px-6 py-3.5 text-sm text-gray-600 text-right whitespace-nowrap tabular-nums">R$ {{ number_format((float) $day->revenue / max(1, $day->orders), 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50/90 border-t border-gray-200 font-semibold">
                            <td class="px-4 lg:px-6 py-3.5 text-sm text-gray-900">Total</td>
                            <td class="px-4 lg:px-6 py-3.5 text-sm text-gray-900 text-center tabular-nums">{{ $ordersCount }}</td>
                            <td class="px-4 lg:px-6 py-3.5 text-sm text-blue-500 text-right whitespace-nowrap tabular-nums">R$ {{ number_format($revenue, 2, ',', '.') }}</td>
                            <td class="px-4 lg:px-6 py-3.5 text-sm text-gray-600 text-right whitespace-nowrap tabular-nums">R$ {{ number_format($avgTicket, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    {{-- Pedidos no período --}}
    <div class="apple-card overflow-hidden transition-shadow duration-200 hover:shadow-md">
        <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Pedidos no período</h3>
                <p class="text-xs text-gray-500 mt-1 md:hidden">Últimos pedidos do intervalo</p>
            </div>
            <a href="{{ route('admin.b2b.orders.index') }}" class="apple-btn-secondary !px-4 !py-2 !text-xs w-full sm:w-auto text-center shrink-0">
                Ver todos os pedidos
            </a>
        </div>
        @if($recentOrders->isNotEmpty())
            {{-- Mobile: cards --}}
            <div class="md:hidden p-3 flex flex-col gap-3 bg-gray-50/40">
                @foreach($recentOrders as $order)
                    <a href="{{ route('admin.b2b.orders.show', $order) }}" class="apple-card block p-4 shadow-sm hover:shadow-md transition-shadow active:scale-[0.99] no-underline">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-blue-500 tracking-tight">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $order->retailer?->store_name ?? '—' }}</p>
                                <p class="text-xs text-gray-400 mt-2 tabular-nums">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right shrink-0 flex flex-col items-end gap-2">
                                <span class="apple-badge bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                                    {{ $order->status->shortLabel() }}
                                </span>
                                <p class="text-sm font-semibold text-gray-900 tabular-nums">{{ $order->formatted_total }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Desktop: table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/90 border-b border-gray-100">
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pedido</th>
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Lojista</th>
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Data</th>
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Status</th>
                            <th scope="col" class="px-4 lg:px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 lg:px-6 py-3.5 text-sm font-medium text-gray-900 whitespace-nowrap">
                                    <a href="{{ route('admin.b2b.orders.show', $order) }}" class="text-blue-500 hover:text-blue-600 transition-colors">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-4 lg:px-6 py-3.5 text-sm text-gray-700 max-w-[140px] lg:max-w-xs truncate" title="{{ $order->retailer?->store_name ?? '—' }}">{{ $order->retailer?->store_name ?? '—' }}</td>
                                <td class="px-4 lg:px-6 py-3.5 text-sm text-gray-500 whitespace-nowrap tabular-nums">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 lg:px-6 py-3.5 text-center">
                                    <span class="apple-badge bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                                        {{ $order->status->shortLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 lg:px-6 py-3.5 text-sm font-semibold text-gray-900 text-right whitespace-nowrap tabular-nums">{{ $order->formatted_total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-4 py-10 sm:py-14 text-center text-sm text-gray-500">Nenhum pedido no período selecionado.</div>
        @endif
    </div>
</x-b2b-admin-layout>
