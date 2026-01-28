<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('reports.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Produtos Mais Vendidos</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Ranking dos produtos com maior saída</p>
                </div>
            </div>

            <!-- Filtros -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('reports.top-products') }}" style="display: flex; align-items: flex-end; gap: 1rem;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Data Inicial</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Data Final</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                    </div>
                    <div style="width: 10rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Quantidade</label>
                        <select name="limit" style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                            <option value="10" {{ $limit == 10 ? 'selected' : '' }}>Top 10</option>
                            <option value="20" {{ $limit == 20 ? 'selected' : '' }}>Top 20</option>
                            <option value="50" {{ $limit == 50 ? 'selected' : '' }}>Top 50</option>
                        </select>
                    </div>
                    <button type="submit" style="padding: 0.625rem 1.5rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;">
                        Filtrar
                    </button>
                </form>
            </div>

            <!-- Ranking -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <h3 style="font-weight: 600; color: #111827;">Ranking de Vendas</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 4rem;">#</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Categoria</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Total Vendido</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Estoque Atual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['products'] as $index => $item)
                                @php
                                    $isTop3 = $index < 3;
                                    $medalColors = ['#fbbf24', '#9ca3af', '#d97706']; // ouro, prata, bronze
                                @endphp
                                <tr style="border-bottom: 1px solid #f3f4f6; {{ $isTop3 ? 'background: #fefce8;' : '' }}" 
                                    onmouseover="this.style.background='{{ $isTop3 ? '#fef9c3' : '#f9fafb' }}'" 
                                    onmouseout="this.style.background='{{ $isTop3 ? '#fefce8' : 'white' }}'">
                                    <td style="padding: 1rem 1.5rem; text-align: center;">
                                        @if($isTop3)
                                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; background: {{ $medalColors[$index] }}; color: white; border-radius: 50%; font-weight: 700; font-size: 1rem;">
                                                {{ $index + 1 }}
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: #f3f4f6; color: #6b7280; border-radius: 50%; font-weight: 600; font-size: 0.875rem;">
                                                {{ $index + 1 }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <a href="{{ route('products.show', $item['product']) }}" style="font-weight: 600; color: #111827; text-decoration: none;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#111827'">
                                            {{ $item['product']->name }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: #9ca3af;">SKU: {{ $item['product']->sku }}</div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $item['product']->category->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <span style="font-size: 1.25rem; font-weight: 700; color: #16a34a;">{{ $item['total_sold'] }}</span>
                                        <span style="font-size: 0.75rem; color: #6b7280; margin-left: 0.25rem;">un.</span>
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: center;">
                                        @php
                                            $stockBg = $item['product']->isLowStock() ? ($item['product']->isOutOfStock() ? '#fef2f2' : '#fefce8') : '#f0fdf4';
                                            $stockColor = $item['product']->isLowStock() ? ($item['product']->isOutOfStock() ? '#dc2626' : '#ca8a04') : '#16a34a';
                                        @endphp
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $stockBg }}; color: {{ $stockColor }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $item['product']->stock_quantity }} un.
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 3rem; text-align: center; color: #6b7280;">
                                        <svg style="margin: 0 auto 1rem; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Nenhuma venda registrada no período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 640px) {
            form[style*="display: flex"] { flex-direction: column !important; }
            form[style*="display: flex"] > div { width: 100% !important; }
        }
    </style>
</x-app-layout>
