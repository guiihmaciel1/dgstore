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

            <!-- BANNER DG STORE -->
            <div class="mb-4 sm:mb-6">
                <div id="banner-container" style="width: 100%; aspect-ratio: 1200/280; background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #374151 100%); border-radius: 0.75rem; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center;">
                    {{-- Substitua o conteúdo abaixo pela tag <img> quando tiver o banner --}}
                    {{-- <img src="{{ asset('images/banner-dashboard.png') }}" alt="DG Store" style="width: 100%; height: 100%; object-fit: cover;"> --}}
                    <div style="text-align: center; color: rgba(255,255,255,0.25); padding: 1rem;">
                        <svg style="width: 32px; height: 32px; margin: 0 auto 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p style="font-size: 0.8rem; font-weight: 600;">Coloque seu banner aqui</p>
                        <p style="font-size: 0.65rem; margin-top: 0.25rem;">Tamanho ideal: <strong>1200 x 280 px</strong> (PNG ou JPG)</p>
                    </div>
                </div>
            </div>

            <!-- AÇÕES RÁPIDAS -->
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                <a href="{{ route('crm.board') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: #111827; color: white; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #111827; transition: all 0.15s;" onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    Pipeline CRM
                </a>
                <a href="{{ route('quotations.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb; transition: all 0.15s;" onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Cotações
                </a>
                <a href="{{ route('valuations.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb; transition: all 0.15s;" onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Avaliador
                </a>
                <a href="{{ route('tools.specs') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb; transition: all 0.15s;" onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Ficha Técnica
                </a>
            </div>

            <!-- Alertas dos Módulos -->
            @if(($alerts['warranties_expiring'] ?? 0) > 0 || ($alerts['open_claims'] ?? 0) > 0 || ($alerts['imports_in_transit'] ?? 0) > 0 || ($alerts['reservations_expiring'] ?? 0) > 0 || ($alerts['reservations_overdue'] ?? 0) > 0 || ($alerts['deals_open'] ?? 0) > 0 || ($alerts['deals_overdue'] ?? 0) > 0)
            <div class="mb-6 sm:mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Alertas</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @if(($alerts['warranties_expiring'] ?? 0) > 0)
                        <a href="{{ route('warranties.index', ['status' => 'expiring']) }}" class="alert-card alert-card-yellow">
                            <div class="alert-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="alert-card-content">
                                <span class="alert-card-number">{{ $alerts['warranties_expiring'] }}</span>
                                <span class="alert-card-label">Garantias vencendo</span>
                            </div>
                        </a>
                    @endif

                    @if(($alerts['open_claims'] ?? 0) > 0)
                        <a href="{{ route('warranties.index', ['status' => 'with_claims']) }}" class="alert-card alert-card-red">
                            <div class="alert-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="alert-card-content">
                                <span class="alert-card-number">{{ $alerts['open_claims'] }}</span>
                                <span class="alert-card-label">Acionamentos abertos</span>
                            </div>
                        </a>
                    @endif

                    @if(($alerts['imports_in_transit'] ?? 0) > 0)
                        <a href="{{ route('imports.index') }}" class="alert-card alert-card-blue">
                            <div class="alert-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                            </div>
                            <div class="alert-card-content">
                                <span class="alert-card-number">{{ $alerts['imports_in_transit'] }}</span>
                                <span class="alert-card-label">Pedidos em trânsito</span>
                            </div>
                        </a>
                    @endif

                    @if(($alerts['reservations_overdue'] ?? 0) > 0)
                        <a href="{{ route('reservations.index', ['status' => 'active']) }}" class="alert-card alert-card-red">
                            <div class="alert-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </div>
                            <div class="alert-card-content">
                                <span class="alert-card-number">{{ $alerts['reservations_overdue'] }}</span>
                                <span class="alert-card-label">Reservas vencidas</span>
                            </div>
                        </a>
                    @elseif(($alerts['reservations_expiring'] ?? 0) > 0)
                        <a href="{{ route('reservations.index') }}" class="alert-card alert-card-yellow">
                            <div class="alert-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="alert-card-content">
                                <span class="alert-card-number">{{ $alerts['reservations_expiring'] }}</span>
                                <span class="alert-card-label">Reservas vencendo</span>
                            </div>
                        </a>
                    @endif

                    @if(($alerts['deals_overdue'] ?? 0) > 0)
                        <a href="{{ route('crm.board') }}" class="alert-card alert-card-red">
                            <div class="alert-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                </svg>
                            </div>
                            <div class="alert-card-content">
                                <span class="alert-card-number">{{ $alerts['deals_overdue'] }}</span>
                                <span class="alert-card-label">Negócios atrasados</span>
                            </div>
                        </a>
                    @elseif(($alerts['deals_open'] ?? 0) > 0)
                        <a href="{{ route('crm.board') }}" class="alert-card alert-card-blue">
                            <div class="alert-card-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                </svg>
                            </div>
                            <div class="alert-card-content">
                                <span class="alert-card-number">{{ $alerts['deals_open'] }}</span>
                                <span class="alert-card-label">Negócios em aberto</span>
                            </div>
                        </a>
                    @endif
                </div>
            </div>

            <style>
                .alert-card {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 1rem;
                    border-radius: 0.75rem;
                    text-decoration: none;
                    transition: all 0.15s ease;
                    border: 1px solid;
                }
                .alert-card:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
                }
                .alert-card-icon {
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: 0.5rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                }
                .alert-card-icon svg {
                    width: 1.25rem;
                    height: 1.25rem;
                }
                .alert-card-content {
                    display: flex;
                    flex-direction: column;
                }
                .alert-card-number {
                    font-size: 1.25rem;
                    font-weight: 700;
                    line-height: 1.2;
                }
                .alert-card-label {
                    font-size: 0.75rem;
                    opacity: 0.8;
                }
                .alert-card-yellow {
                    background: #fefce8;
                    border-color: #fde68a;
                    color: #92400e;
                }
                .alert-card-yellow .alert-card-icon {
                    background: #fef3c7;
                    color: #d97706;
                }
                .alert-card-red {
                    background: #fef2f2;
                    border-color: #fecaca;
                    color: #991b1b;
                }
                .alert-card-red .alert-card-icon {
                    background: #fee2e2;
                    color: #dc2626;
                }
                .alert-card-blue {
                    background: #eff6ff;
                    border-color: #bfdbfe;
                    color: #1e40af;
                }
                .alert-card-blue .alert-card-icon {
                    background: #dbeafe;
                    color: #2563eb;
                }
                .alert-card-green {
                    background: #f0fdf4;
                    border-color: #bbf7d0;
                    color: #166534;
                }
                .alert-card-green .alert-card-icon {
                    background: #dcfce7;
                    color: #16a34a;
                }
            </style>
            @endif

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
