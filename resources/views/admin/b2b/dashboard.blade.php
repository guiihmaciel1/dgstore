<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Dashboard B2B</h2>
                <p class="text-sm text-gray-500 mt-1">Visão geral da distribuidora</p>
            </div>
            <div class="inline-flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </x-slot>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6 sm:mb-8">
        {{-- Faturamento do mês --}}
        <div class="apple-card rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md border border-gray-200/60 transition-all duration-300">
            <div class="flex items-start justify-between gap-3 mb-4">
                <span class="apple-section-title">Faturamento</span>
                <span class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">R$ {{ number_format($monthRevenue, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ now()->translatedFormat('F/Y') }}</p>
        </div>

        {{-- Lucro do mês --}}
        <div class="apple-card rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md border border-gray-200/60 transition-all duration-300">
            <div class="flex items-start justify-between gap-3 mb-4">
                <span class="apple-section-title">Lucro</span>
                <span class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">R$ {{ number_format($monthProfit, 2, ',', '.') }}</p>
            @if($monthRevenue > 0)
                <p class="text-xs text-blue-500 font-medium mt-2">Margem {{ number_format(($monthProfit / $monthRevenue) * 100, 1) }}%</p>
            @else
                <p class="text-xs text-gray-500 mt-2">{{ now()->translatedFormat('F/Y') }}</p>
            @endif
        </div>

        {{-- Pedidos do mês --}}
        <div class="apple-card rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md border border-gray-200/60 transition-all duration-300">
            <div class="flex items-start justify-between gap-3 mb-4">
                <span class="apple-section-title">Pedidos</span>
                <span class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $monthOrders }}</p>
            @if($monthOrders > 0 && $monthRevenue > 0)
                <p class="text-xs text-gray-500 mt-2">Ticket médio R$ {{ number_format($monthRevenue / $monthOrders, 2, ',', '.') }}</p>
            @else
                <p class="text-xs text-gray-500 mt-2">{{ now()->translatedFormat('F/Y') }}</p>
            @endif
        </div>

        {{-- Pedidos ativos --}}
        <div class="apple-card rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md border border-gray-200/60 transition-all duration-300">
            <div class="flex items-start justify-between gap-3 mb-4">
                <span class="apple-section-title">Em andamento</span>
                <span class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $activeOrders }}</p>
            <p class="text-xs text-gray-500 mt-2">Pedidos não finalizados</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Pedidos recentes --}}
            <div class="apple-card rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-gray-200/60 transition-all duration-300">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Pedidos recentes</h3>
                    <a href="{{ route('admin.b2b.orders.index') }}" class="apple-btn-secondary rounded-xl !px-3 !py-2 !text-xs shadow-sm hover:shadow-md transition-all duration-300 w-fit">Ver todos</a>
                </div>
                @if($recentOrders->isNotEmpty())
                    <div class="divide-y divide-gray-100">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('admin.b2b.orders.show', $order) }}" class="flex items-center justify-between gap-3 px-5 py-4 sm:px-6 hover:bg-gray-50/80 transition-all duration-300">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600 shrink-0">
                                        {{ $order->retailer ? mb_substr($order->retailer->store_name, 0, 2) : '--' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 tracking-tight truncate">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $order->retailer?->store_name ?? 'N/A' }} &middot; {{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-semibold text-gray-900 tracking-tight">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</p>
                                    <span class="apple-badge mt-1.5 bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                                        {{ $order->status->shortLabel() }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-12 sm:py-14 text-center text-sm text-gray-500">Nenhum pedido ainda.</div>
                @endif
            </div>

            {{-- Top lojistas --}}
            <div class="apple-card rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-gray-200/60 transition-all duration-300">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Top lojistas</h3>
                    <a href="{{ route('admin.b2b.retailers.index') }}" class="apple-btn-secondary rounded-xl !px-3 !py-2 !text-xs shadow-sm hover:shadow-md transition-all duration-300 w-fit">Ver todos</a>
                </div>
                @if($topRetailers->isNotEmpty())
                    <div class="divide-y divide-gray-100">
                        @foreach($topRetailers as $i => $retailer)
                            <a href="{{ route('admin.b2b.retailers.show', $retailer) }}" class="flex items-center justify-between gap-3 px-5 py-4 sm:px-6 hover:bg-gray-50/80 transition-all duration-300">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $i === 0 ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $i + 1 }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 tracking-tight truncate">{{ $retailer->store_name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $retailer->city }}/{{ $retailer->state }} &middot; {{ $retailer->orders_count }} pedido(s)</p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 tracking-tight shrink-0">R$ {{ number_format((float) $retailer->total_purchased, 2, ',', '.') }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-12 sm:py-14 text-center text-sm text-gray-500">Nenhum lojista com pedidos ainda.</div>
                @endif
            </div>
        </div>

        {{-- Coluna lateral --}}
        <div class="space-y-6">
            {{-- Lojistas pendentes --}}
            <div class="apple-card rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-gray-200/60 transition-all duration-300">
                <div class="flex items-center justify-between gap-2 px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Cadastros pendentes</h3>
                    @if($pendingRetailers->count() > 0)
                        <span class="apple-badge bg-gray-100 text-gray-700">{{ $pendingRetailers->count() }}</span>
                    @endif
                </div>
                @if($pendingRetailers->isNotEmpty())
                    <div class="divide-y divide-gray-100">
                        @foreach($pendingRetailers->take(5) as $retailer)
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-5 py-4 sm:px-6">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 tracking-tight">{{ $retailer->store_name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $retailer->city }}/{{ $retailer->state }} &middot; {{ $retailer->created_at->diffForHumans() }}</p>
                                </div>
                                <a href="{{ route('admin.b2b.retailers.show', $retailer) }}" class="apple-btn-primary rounded-xl !px-3 !py-2 !text-xs shadow-sm hover:shadow-md transition-all duration-300 w-full sm:w-auto justify-center shrink-0">Analisar</a>
                            </div>
                        @endforeach
                    </div>
                    @if($pendingRetailers->count() > 5)
                        <div class="px-5 py-3 sm:px-6 border-t border-gray-100">
                            <a href="{{ route('admin.b2b.retailers.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-blue-500 hover:text-blue-600 transition-colors duration-200">
                                Ver todos ({{ $pendingRetailers->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="px-5 py-8 sm:py-10 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Nenhum cadastro pendente</p>
                    </div>
                @endif
            </div>

            {{-- Estoque baixo --}}
            <div class="apple-card rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-gray-200/60 transition-all duration-300">
                <div class="flex items-center justify-between gap-2 px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight">Estoque baixo</h3>
                    @if($lowStockProducts->count() > 0)
                        <span class="apple-badge bg-gray-100 text-gray-700">{{ $lowStockProducts->count() }}</span>
                    @endif
                </div>
                @if($lowStockProducts->isNotEmpty())
                    <div class="divide-y divide-gray-100">
                        @foreach($lowStockProducts as $product)
                            <div class="flex items-center justify-between gap-3 px-5 py-4 sm:px-6">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($product->photo_url)
                                        <img src="{{ $product->photo_url }}" alt="{{ $product->model }}" class="w-9 h-9 object-cover rounded-xl flex-shrink-0 border border-gray-100">
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="truncate">
                                        <p class="text-sm font-semibold text-gray-900 tracking-tight truncate">{{ $product->model }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $product->storage }} &middot; {{ $product->color }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold {{ $product->stock_quantity === 0 ? 'text-red-600' : 'text-amber-600' }} flex-shrink-0 ml-2 tabular-nums">
                                    {{ $product->stock_quantity }} un.
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-5 py-3 sm:px-6 border-t border-gray-100">
                        <a href="{{ route('admin.b2b.products.index') }}" class="apple-btn-dark rounded-xl !px-3 !py-2 !text-xs shadow-sm hover:shadow-md transition-all duration-300 inline-flex w-full sm:w-auto justify-center">Gerenciar produtos</a>
                    </div>
                @else
                    <div class="px-5 py-8 sm:py-10 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Estoque saudável</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-b2b-admin-layout>
