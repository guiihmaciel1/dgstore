<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Dashboard Perfumes</h2>
    </x-slot>

    {{-- KPI Cards - 6 cards in 3 columns --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Produtos Ativos --}}
        <div class="bg-white rounded-xl border border-gray-100 border-l-4 border-l-pink-500 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Produtos Ativos</span>
                <span class="w-9 h-9 rounded-lg bg-pink-100 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ number_format($totalProducts, 0, ',', '.') }}</p>
            <a href="{{ route('admin.perfumes.products.index') }}" class="text-xs text-pink-600 hover:text-pink-700 font-semibold mt-1.5 inline-block">Ver produtos →</a>
        </div>

        {{-- Lojistas Ativos --}}
        <div class="bg-white rounded-xl border border-gray-100 border-l-4 border-l-violet-500 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lojistas Ativos</span>
                <span class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ number_format($totalRetailers, 0, ',', '.') }}</p>
            <a href="{{ route('admin.perfumes.retailers.index') }}" class="text-xs text-violet-600 hover:text-violet-700 font-semibold mt-1.5 inline-block">Ver lojistas →</a>
        </div>

        {{-- Estoque Total --}}
        <div class="bg-white rounded-xl border border-gray-100 border-l-4 border-l-rose-500 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Estoque Total</span>
                <span class="w-9 h-9 rounded-lg bg-rose-100 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ number_format($totalStock, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1.5">Unidades em estoque</p>
        </div>

        {{-- Amostras em Campo --}}
        <div class="bg-white rounded-xl border border-gray-100 border-l-4 border-l-amber-500 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Amostras em Campo</span>
                <span class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ number_format($samplesOut, 0, ',', '.') }}</p>
            <a href="{{ route('admin.perfumes.samples.index') }}" class="text-xs text-amber-600 hover:text-amber-700 font-semibold mt-1.5 inline-block">Ver amostras →</a>
        </div>

        {{-- Faturamento Mês --}}
        <div class="bg-white rounded-xl border border-gray-100 border-l-4 border-l-green-500 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Faturamento Mês</span>
                <span class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">R$ {{ number_format($monthRevenue, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1.5">{{ now()->translatedFormat('F/Y') }} · {{ $monthOrders }} pedido(s)</p>
        </div>

        {{-- Lucro Mês --}}
        <div class="bg-white rounded-xl border border-gray-100 border-l-4 border-l-emerald-500 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lucro Mês</span>
                <span class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">R$ {{ number_format($monthProfit, 2, ',', '.') }}</p>
            @if($monthRevenue > 0)
                <p class="text-xs text-emerald-600 font-semibold mt-1.5">Margem {{ number_format(($monthProfit / $monthRevenue) * 100, 1) }}%</p>
            @else
                <p class="text-xs text-gray-500 mt-1.5">{{ now()->translatedFormat('F/Y') }}</p>
            @endif
        </div>
    </div>

    {{-- Métricas B2C (Varejo) --}}
    <div class="mb-4">
        <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Varejo (B2C)
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200 p-3">
                <div class="text-xs font-medium text-blue-700">Clientes</div>
                <div class="text-2xl font-bold text-blue-900 mt-1">{{ $totalCustomers }}</div>
                <a href="{{ route('admin.perfumes.customers.index') }}" class="text-[10px] text-blue-600 hover:text-blue-800 font-medium mt-1 inline-block">Ver →</a>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200 p-3">
                <div class="text-xs font-medium text-purple-700">Encomendas</div>
                <div class="text-2xl font-bold text-purple-900 mt-1">{{ $activeReservations }}</div>
                <a href="{{ route('admin.perfumes.reservations.index') }}" class="text-[10px] text-purple-600 hover:text-purple-800 font-medium mt-1 inline-block">Ver →</a>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 p-3">
                <div class="text-xs font-medium text-green-700">Vendas Mês</div>
                <div class="text-2xl font-bold text-green-900 mt-1">{{ $monthSales }}</div>
                <a href="{{ route('admin.perfumes.sales.index') }}" class="text-[10px] text-green-600 hover:text-green-800 font-medium mt-1 inline-block">Ver →</a>
            </div>
            
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg border border-emerald-200 p-3">
                <div class="text-xs font-medium text-emerald-700">Receita Mês</div>
                <div class="text-xl font-bold text-emerald-900 mt-1">R$ {{ number_format($monthSalesRevenue, 0, ',', '.') }}</div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg border border-yellow-200 p-3">
                <div class="text-xs font-medium text-yellow-700">Hoje</div>
                <div class="text-xl font-bold text-yellow-900 mt-1">R$ {{ number_format($todaySales, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Valor Pendente - Yellow warning card --}}
    @if($pendingAmount > 0)
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </span>
                <div>
                    <h3 class="font-semibold text-amber-800">Valor Pendente</h3>
                    <p class="text-sm text-amber-700">Valor a receber de lojistas</p>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-800">R$ {{ number_format($pendingAmount, 2, ',', '.') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Orders --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-pink-50/50 to-transparent">
                <h3 class="font-bold text-gray-900">Pedidos Recentes</h3>
                <a href="{{ route('admin.perfumes.orders.index') }}" class="text-xs text-pink-600 hover:text-pink-700 font-semibold">Ver todos →</a>
            </div>
            @if($recentOrders->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-pink-50/40">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pedido</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lojista</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pagamento</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-5 py-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3">
                                    <a href="{{ route('admin.perfumes.orders.show', $order) }}" class="text-sm font-medium text-pink-600 hover:text-pink-700">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $order->retailer?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-right text-sm font-medium text-gray-800">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full bg-{{ $order->status->badgeColor() }}-100 text-{{ $order->status->badgeColor() }}-700">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full bg-{{ $order->payment_status->badgeColor() }}-100 text-{{ $order->payment_status->badgeColor() }}-700">
                                        {{ $order->payment_status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('admin.perfumes.orders.show', $order) }}" class="text-gray-400 hover:text-pink-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-400 text-sm">Nenhum pedido ainda.</div>
            @endif
        </div>

        {{-- Old Samples Alert --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50/50 to-transparent">
                    <h3 class="font-bold text-gray-900">Amostras em Campo &gt; 30 dias</h3>
                    @if($oldSamples->isNotEmpty())
                        <span class="bg-amber-100 text-amber-700 text-xs font-bold rounded-full px-2.5 py-0.5">{{ $oldSamples->count() }}</span>
                    @endif
                </div>
                @if($oldSamples->isNotEmpty())
                    <div class="divide-y divide-gray-50">
                        @foreach($oldSamples as $sample)
                            <div class="px-5 py-3">
                                <p class="text-sm font-medium text-gray-800">{{ $sample->product?->name ?? 'Produto' }}</p>
                                <p class="text-xs text-gray-500">{{ $sample->retailer?->name ?? 'Lojista' }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-400">Entregue em {{ $sample->delivered_at?->format('d/m/Y') ?? '—' }}</span>
                                    <span class="text-xs font-bold text-amber-600">{{ $sample->days_out }} dias</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-5 py-3 border-t border-gray-100">
                        <a href="{{ route('admin.perfumes.samples.index') }}" class="text-xs text-pink-600 hover:text-pink-700 font-medium">Ver todas amostras</a>
                    </div>
                @else
                    <div class="p-6 text-center">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Nenhuma amostra há mais de 30 dias em campo</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-perfumes-admin-layout>
