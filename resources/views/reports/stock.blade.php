<x-app-layout>
    <x-slot name="title">Relatório de Estoque</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('reports.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #818181; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.backgroundColor='#222222'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div style="flex: 1;">
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;">Relatório de Estoque</h1>
                    <p style="font-size: 0.875rem; color: #818181;">Visão geral do inventário da loja</p>
                </div>
                @include('reports.partials.export-button', ['route' => 'reports.stock.export'])
            </div>

            <!-- Resumo -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Total de Produtos</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #e3e3e3; margin-top: 0.25rem;">{{ $report['summary']['total_products'] }}</div>
                </div>
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Total de Unidades</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #16a34a; margin-top: 0.25rem;">{{ $report['summary']['total_units'] }}</div>
                </div>
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; {{ $report['summary']['out_of_stock'] > 0 ? 'background: rgba(239,68,68,0.1); border-color: #fecaca;' : '' }}">
                    <div style="font-size: 0.75rem; font-weight: 500; color: {{ $report['summary']['out_of_stock'] > 0 ? '#dc2626' : '#6b7280' }}; text-transform: uppercase;">Sem Estoque</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: {{ $report['summary']['out_of_stock'] > 0 ? '#dc2626' : '#111827' }}; margin-top: 0.25rem;">{{ $report['summary']['out_of_stock'] }}</div>
                </div>
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; {{ $report['summary']['low_stock'] > 0 ? 'background: rgba(245,158,11,0.1); border-color: #fde68a;' : '' }}">
                    <div style="font-size: 0.75rem; font-weight: 500; color: {{ $report['summary']['low_stock'] > 0 ? '#ca8a04' : '#6b7280' }}; text-transform: uppercase;">Estoque Baixo</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: {{ $report['summary']['low_stock'] > 0 ? '#ca8a04' : '#111827' }}; margin-top: 0.25rem;">{{ $report['summary']['low_stock'] }}</div>
                </div>
            </div>

            <!-- Por Categoria -->
            <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                    <h3 style="font-weight: 600; color: #e3e3e3;">Por Categoria</h3>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        @foreach($report['by_category'] as $category => $data)
                            @php $categoryEnum = \App\Domain\Product\Enums\ProductCategory::from($category); @endphp
                            <div style="background: #1a1a1a; border-radius: 0.75rem; padding: 1rem; border: 1px solid rgba(255,255,255,0.06);">
                                <h4 style="font-weight: 600; color: #e3e3e3; margin-bottom: 0.75rem;">{{ $categoryEnum->label() }}</h4>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-size: 0.875rem; color: #818181;">Produtos:</span>
                                        <span style="font-size: 0.875rem; font-weight: 500; color: #e3e3e3;">{{ $data['count'] }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-size: 0.875rem; color: #818181;">Em Estoque:</span>
                                        <span style="font-size: 0.875rem; font-weight: 500; color: #e3e3e3;">{{ $data['stock_quantity'] }} un.</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Produtos com Estoque Baixo -->
            <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                    <h3 style="font-weight: 600; color: #e3e3e3;">Produtos com Estoque Baixo</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Produto</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Categoria</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Estoque</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Mínimo</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['low_stock_products'] as $product)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="font-weight: 500; color: #e3e3e3;">{{ $product->name }}</div>
                                        <div style="font-size: 0.75rem; color: #666666;">SKU: {{ $product->sku }}</div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #222222; color: #a4a4a4; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $product->category->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @if($product->isOutOfStock())
                                            <span style="font-weight: 700; color: #fca5a5;">{{ $product->stock_quantity }} un.</span>
                                        @else
                                            <span style="font-weight: 600; color: #ca8a04;">{{ $product->stock_quantity }} un.</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.875rem; color: #818181;">
                                        {{ $product->min_stock_alert }} un.
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <a href="{{ route('stock.create') }}?product_id={{ $product->id }}" 
                                           style="display: inline-block; padding: 0.375rem 0.75rem; background: #111827; color: white; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;"
                                           onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                            + Entrada
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 3rem; text-align: center; color: #818181;">
                                        <svg style="margin: 0 auto 1rem; width: 3rem; height: 3rem; color: #515151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Nenhum produto com estoque baixo. Tudo em ordem!
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
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: repeat(2, 1fr) !important; }
            div[style*="grid-template-columns: repeat(3"] { grid-template-columns: repeat(2, 1fr) !important; }
        }
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: 1fr !important; }
            div[style*="grid-template-columns: repeat(3"] { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
