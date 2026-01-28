<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="margin-bottom: 1.5rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Alertas de Estoque Baixo</h1>
                <p style="font-size: 0.875rem; color: #6b7280;">Produtos que precisam de reposição</p>
            </div>

            @if($products->count() > 0)
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fefce8; border: 1px solid #fde68a; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <svg style="width: 1.5rem; height: 1.5rem; color: #ca8a04; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span style="color: #92400e; font-weight: 500;">
                        {{ $products->count() }} {{ $products->count() === 1 ? 'produto está' : 'produtos estão' }} com estoque baixo ou zerado.
                    </span>
                </div>
            @endif

            <!-- Tabela -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Categoria</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Estoque</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Mínimo</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr style="border-bottom: 1px solid #f3f4f6; {{ $product->isOutOfStock() ? 'background: #fef2f2;' : '' }}" 
                                    onmouseover="this.style.background='{{ $product->isOutOfStock() ? '#fee2e2' : '#f9fafb' }}'" 
                                    onmouseout="this.style.background='{{ $product->isOutOfStock() ? '#fef2f2' : 'white' }}'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="font-weight: 500; color: #111827;">{{ $product->name }}</div>
                                        <div style="font-size: 0.75rem; color: #9ca3af;">SKU: {{ $product->sku }}</div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $product->category->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: {{ $product->isOutOfStock() ? '#dc2626' : '#ca8a04' }};">
                                        {{ $product->stock_quantity }} un.
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.875rem; color: #6b7280;">
                                        {{ $product->min_stock_alert }} un.
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @if($product->isOutOfStock())
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #fef2f2; color: #dc2626; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; border: 1px solid #fecaca;">
                                                Sem Estoque
                                            </span>
                                        @else
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #fefce8; color: #ca8a04; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; border: 1px solid #fde68a;">
                                                Estoque Baixo
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                            <a href="{{ route('products.show', $product) }}" 
                                               style="padding: 0.375rem 0.75rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none; border: 1px solid #e5e7eb;"
                                               onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                                                Ver
                                            </a>
                                            <a href="{{ route('stock.create') }}?product_id={{ $product->id }}" 
                                               style="padding: 0.375rem 0.75rem; background: #111827; color: white; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;"
                                               onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                                + Entrada
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 3rem; text-align: center; color: #6b7280;">
                                        <svg style="margin: 0 auto 1rem; width: 3rem; height: 3rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p style="font-weight: 500; color: #16a34a;">Nenhum produto com estoque baixo.</p>
                                        <p style="font-size: 0.875rem;">Tudo em ordem!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
