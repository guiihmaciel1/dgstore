<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <!-- AÇÕES RÁPIDAS -->
            <div class="mb-6 sm:mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- NOVA VENDA -->
                    <a href="{{ route('sales.create') }}" class="quick-action-card quick-action-primary">
                        <div class="quick-action-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <span class="quick-action-label">Nova Venda</span>
                        <span class="quick-action-hint">F2</span>
                    </a>
                    
                    <!-- NOVO PRODUTO -->
                    <a href="{{ route('products.create') }}" class="quick-action-card">
                        <div class="quick-action-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="quick-action-label">Novo Produto</span>
                    </a>
                    
                    <!-- NOVO CLIENTE -->
                    <a href="{{ route('customers.create') }}" class="quick-action-card">
                        <div class="quick-action-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="quick-action-label">Novo Cliente</span>
                    </a>
                    
                    <!-- ENTRADA ESTOQUE -->
                    <a href="{{ route('stock.create') }}" class="quick-action-card">
                        <div class="quick-action-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <span class="quick-action-label">Entrada Estoque</span>
                    </a>
                </div>
            </div>

            <style>
                .quick-action-card {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 1.5rem 1rem;
                    background: white;
                    border: 1px solid #e5e7eb;
                    border-radius: 0.75rem;
                    text-decoration: none;
                    transition: all 0.15s ease;
                    position: relative;
                }
                .quick-action-card:hover {
                    border-color: #111827;
                    background: #f9fafb;
                }
                .quick-action-primary {
                    background: #111827;
                    border-color: #111827;
                }
                .quick-action-primary:hover {
                    background: #1f2937;
                    border-color: #1f2937;
                }
                .quick-action-icon {
                    width: 2.5rem;
                    height: 2.5rem;
                    margin-bottom: 0.75rem;
                    color: #374151;
                }
                .quick-action-icon svg {
                    width: 100%;
                    height: 100%;
                }
                .quick-action-primary .quick-action-icon {
                    color: white;
                }
                .quick-action-label {
                    font-size: 0.875rem;
                    font-weight: 600;
                    color: #111827;
                }
                .quick-action-primary .quick-action-label {
                    color: white;
                }
                .quick-action-hint {
                    position: absolute;
                    top: 0.5rem;
                    right: 0.5rem;
                    font-size: 0.625rem;
                    font-weight: 600;
                    padding: 0.125rem 0.375rem;
                    background: rgba(255,255,255,0.2);
                    border-radius: 0.25rem;
                    color: rgba(255,255,255,0.8);
                }
                @media (min-width: 640px) {
                    .quick-action-card {
                        padding: 2rem 1.5rem;
                    }
                    .quick-action-icon {
                        width: 3rem;
                        height: 3rem;
                    }
                    .quick-action-label {
                        font-size: 0.9375rem;
                    }
                }
            </style>

            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
                <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-gray-500">Vendas Hoje</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">R$ {{ number_format($todayTotal, 2, ',', '.') }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500">Pedidos Hoje</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $todayCount }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-gray-500">Vendas do Mês</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">R$ {{ number_format($monthTotal, 2, ',', '.') }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-500 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border {{ $lowStockCount > 0 ? 'border-red-200 bg-red-50' : 'border-gray-100' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm {{ $lowStockCount > 0 ? 'text-red-600' : 'text-gray-500' }}">Estoque Baixo</p>
                            <p class="text-xl sm:text-2xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $lowStockCount }} <span class="text-base">produtos</span></p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 {{ $lowStockCount > 0 ? 'bg-red-500' : 'bg-gray-400' }} rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    @if($lowStockCount > 0)
                        <a href="{{ route('stock.alerts') }}" class="text-xs text-red-600 hover:underline mt-2 inline-block">Ver produtos →</a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Gráfico de Vendas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Vendas dos Últimos 7 Dias</h3>
                    <div class="h-48 sm:h-auto">
                        <canvas id="salesChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Produtos Mais Vendidos -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Produtos Mais Vendidos</h3>
                    @if(count($topProducts) > 0)
                        <div class="space-y-3">
                            @foreach($topProducts as $index => $item)
                                <div class="flex items-center justify-between p-3 rounded-lg {{ $index === 0 ? 'bg-gray-100' : 'hover:bg-gray-50' }}">
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 bg-gray-900 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item['product']->name ?? 'Produto removido' }}</p>
                                            <p class="text-xs text-gray-500">{{ $item['product']->sku ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-gray-900 text-white text-sm rounded-full">{{ $item['total_sold'] }} un</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Nenhuma venda registrada ainda.</p>
                    @endif
                </div>

                <!-- Alertas de Estoque Baixo -->
                @if($lowStockProducts->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Alertas de Estoque Baixo</h3>
                        <a href="{{ route('stock.alerts') }}" class="text-xs sm:text-sm text-gray-600 hover:text-gray-900">Ver todos →</a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($lowStockProducts->take(6) as $product)
                            <div class="flex items-center justify-between p-3 rounded-lg border {{ $product->stock_quantity <= 0 ? 'border-red-200 bg-red-50' : 'border-yellow-200 bg-yellow-50' }}">
                                <div class="min-w-0 flex-1 mr-2">
                                    <p class="font-medium text-gray-900 text-sm truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-bold rounded flex-shrink-0 {{ $product->stock_quantity <= 0 ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white' }}">
                                    {{ $product->stock_quantity }} un
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Atalho F2 para Nova Venda
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                window.location.href = '{{ route('sales.create') }}';
            }
        });

        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($salesChart['labels']),
                datasets: [{
                    label: 'Vendas (R$)',
                    data: @json($salesChart['data']),
                    borderColor: '#1f2937',
                    backgroundColor: 'rgba(31, 41, 55, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
