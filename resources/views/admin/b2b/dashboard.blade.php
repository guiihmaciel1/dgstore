<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Dashboard B2B</h2>
                <p class="text-sm text-gray-500 mt-0.5">Visão geral da distribuidora</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Faturamento do mês --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Faturamento</span>
                <span class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($monthRevenue, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('F/Y') }}</p>
        </div>

        {{-- Lucro do mês --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lucro</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($monthProfit, 2, ',', '.') }}</p>
            @if($monthRevenue > 0)
                <p class="text-xs text-emerald-600 mt-1">Margem {{ number_format(($monthProfit / $monthRevenue) * 100, 1) }}%</p>
            @else
                <p class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('F/Y') }}</p>
            @endif
        </div>

        {{-- Pedidos do mês --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pedidos</span>
                <span class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $monthOrders }}</p>
            @if($monthOrders > 0 && $monthRevenue > 0)
                <p class="text-xs text-gray-400 mt-1">Ticket médio R$ {{ number_format($monthRevenue / $monthOrders, 2, ',', '.') }}</p>
            @else
                <p class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('F/Y') }}</p>
            @endif
        </div>

        {{-- Pedidos ativos --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Em andamento</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $activeOrders }}</p>
            <p class="text-xs text-gray-400 mt-1">Pedidos não finalizados</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Pedidos recentes --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Pedidos Recentes</h3>
                    <a href="{{ route('admin.b2b.orders.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Ver todos</a>
                </div>
                @if($recentOrders->isNotEmpty())
                    <div class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('admin.b2b.orders.show', $order) }}" class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                        {{ $order->retailer ? mb_substr($order->retailer->store_name, 0, 2) : '--' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->retailer?->store_name ?? 'N/A' }} &middot; {{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-800">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</p>
                                    <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                                        {{ $order->status->shortLabel() }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400 text-sm">Nenhum pedido ainda.</div>
                @endif
            </div>

            {{-- Top lojistas --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Top Lojistas</h3>
                    <a href="{{ route('admin.b2b.retailers.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Ver todos</a>
                </div>
                @if($topRetailers->isNotEmpty())
                    <div class="divide-y divide-gray-50">
                        @foreach($topRetailers as $i => $retailer)
                            <a href="{{ route('admin.b2b.retailers.show', $retailer) }}" class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $i === 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $i + 1 }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $retailer->store_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $retailer->city }}/{{ $retailer->state }} &middot; {{ $retailer->orders_count }} pedido(s)</p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-gray-800">R$ {{ number_format((float) $retailer->total_purchased, 2, ',', '.') }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400 text-sm">Nenhum lojista com pedidos ainda.</div>
                @endif
            </div>
        </div>

        {{-- Coluna lateral --}}
        <div class="space-y-6">
            {{-- Lojistas pendentes --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Cadastros Pendentes</h3>
                    @if($pendingRetailers->count() > 0)
                        <span class="bg-orange-100 text-orange-700 text-xs font-bold rounded-full px-2.5 py-0.5">{{ $pendingRetailers->count() }}</span>
                    @endif
                </div>
                @if($pendingRetailers->isNotEmpty())
                    <div class="divide-y divide-gray-50">
                        @foreach($pendingRetailers->take(5) as $retailer)
                            <div class="flex items-center justify-between px-5 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $retailer->store_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $retailer->city }}/{{ $retailer->state }} &middot; {{ $retailer->created_at->diffForHumans() }}</p>
                                </div>
                                <a href="{{ route('admin.b2b.retailers.show', $retailer) }}" class="text-xs bg-blue-50 text-blue-600 font-medium px-3 py-1.5 rounded-lg hover:bg-blue-100 transition">Analisar</a>
                            </div>
                        @endforeach
                    </div>
                    @if($pendingRetailers->count() > 5)
                        <div class="px-5 py-3 border-t border-gray-100">
                            <a href="{{ route('admin.b2b.retailers.index', ['status' => 'pending']) }}" class="text-xs text-blue-600 font-medium hover:text-blue-800">
                                Ver todos ({{ $pendingRetailers->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="p-6 text-center">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Nenhum cadastro pendente</p>
                    </div>
                @endif
            </div>

            {{-- Estoque baixo --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Estoque Baixo</h3>
                    @if($lowStockProducts->count() > 0)
                        <span class="bg-red-100 text-red-700 text-xs font-bold rounded-full px-2.5 py-0.5">{{ $lowStockProducts->count() }}</span>
                    @endif
                </div>
                @if($lowStockProducts->isNotEmpty())
                    <div class="divide-y divide-gray-50">
                        @foreach($lowStockProducts as $product)
                            <div class="flex items-center justify-between px-5 py-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($product->photo_url)
                                        <img src="{{ $product->photo_url }}" alt="{{ $product->model }}" class="w-8 h-8 object-cover rounded-lg flex-shrink-0">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="truncate">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $product->model }}</p>
                                        <p class="text-xs text-gray-400">{{ $product->storage }} &middot; {{ $product->color }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold {{ $product->stock_quantity === 0 ? 'text-red-600' : 'text-amber-600' }} flex-shrink-0 ml-2">
                                    {{ $product->stock_quantity }} un.
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-5 py-3 border-t border-gray-100">
                        <a href="{{ route('admin.b2b.products.index') }}" class="text-xs text-blue-600 font-medium hover:text-blue-800">Gerenciar produtos</a>
                    </div>
                @else
                    <div class="p-6 text-center">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Estoque saudável</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-b2b-admin-layout>
