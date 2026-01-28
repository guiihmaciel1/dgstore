<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="margin-bottom: 2rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Relatórios</h1>
                <p style="font-size: 0.875rem; color: #6b7280;">Análises e informações gerenciais do seu negócio</p>
            </div>

            <!-- Cards de Relatórios -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                
                <!-- Relatório de Vendas -->
                <a href="{{ route('reports.sales') }}" style="text-decoration: none;">
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem; transition: all 0.2s;"
                         onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'; this.style.borderColor='#111827'"
                         onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)'; this.style.borderColor='#e5e7eb'">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="width: 3.5rem; height: 3.5rem; background: #111827; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.75rem; height: 1.75rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Relatório de Vendas</h3>
                                <p style="font-size: 0.875rem; color: #6b7280; line-height: 1.4;">Vendas por período, vendedor e forma de pagamento</p>
                            </div>
                            <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Relatório de Estoque -->
                <a href="{{ route('reports.stock') }}" style="text-decoration: none;">
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem; transition: all 0.2s;"
                         onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'; this.style.borderColor='#111827'"
                         onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)'; this.style.borderColor='#e5e7eb'">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="width: 3.5rem; height: 3.5rem; background: #374151; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.75rem; height: 1.75rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Relatório de Estoque</h3>
                                <p style="font-size: 0.875rem; color: #6b7280; line-height: 1.4;">Valor em estoque e produtos com baixa quantidade</p>
                            </div>
                            <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Produtos Mais Vendidos -->
                <a href="{{ route('reports.top-products') }}" style="text-decoration: none;">
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem; transition: all 0.2s;"
                         onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'; this.style.borderColor='#111827'"
                         onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)'; this.style.borderColor='#e5e7eb'">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="width: 3.5rem; height: 3.5rem; background: #6b7280; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.75rem; height: 1.75rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Produtos Mais Vendidos</h3>
                                <p style="font-size: 0.875rem; color: #6b7280; line-height: 1.4;">Ranking dos produtos com maior saída</p>
                            </div>
                            <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Estatísticas Rápidas -->
            <div style="margin-top: 2rem;">
                <h2 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem;">Visão Geral</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Vendas Hoje</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">
                            R$ {{ number_format(\App\Domain\Sale\Models\Sale::whereDate('sold_at', today())->sum('total'), 2, ',', '.') }}
                        </div>
                    </div>
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Vendas Mês</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">
                            R$ {{ number_format(\App\Domain\Sale\Models\Sale::whereMonth('sold_at', now()->month)->whereYear('sold_at', now()->year)->sum('total'), 2, ',', '.') }}
                        </div>
                    </div>
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Total Produtos</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">
                            {{ \App\Domain\Product\Models\Product::where('active', true)->count() }}
                        </div>
                    </div>
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Total Clientes</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">
                            {{ \App\Domain\Customer\Models\Customer::count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: repeat(3"] {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            div[style*="grid-template-columns: repeat(4"] {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(3"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: repeat(4"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
