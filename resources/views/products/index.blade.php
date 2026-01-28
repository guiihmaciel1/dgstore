<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Produtos</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Gerencie o catálogo de produtos da loja</p>
                </div>
                <a href="{{ route('products.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background 0.2s;"
                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Novo Produto
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                
                <!-- Filtros -->
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <form method="GET" action="{{ route('products.index') }}" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; align-items: end;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Buscar</label>
                            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nome, SKU, IMEI..." 
                                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#d1d5db'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Categoria</label>
                            <select name="category" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                <option value="">Todas</option>
                                @foreach(\App\Domain\Product\Enums\ProductCategory::grouped() as $group => $items)
                                    <optgroup label="{{ $group }}">
                                        @foreach($items as $category)
                                            <option value="{{ $category->value }}" {{ $filters['category'] === $category->value ? 'selected' : '' }}>
                                                {{ $category->label() }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Condição</label>
                            <select name="condition" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                <option value="">Todas</option>
                                @foreach($conditions as $condition)
                                    <option value="{{ $condition->value }}" {{ $filters['condition'] === $condition->value ? 'selected' : '' }}>
                                        {{ $condition->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display: flex; align-items: center; padding-top: 1.25rem;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" name="low_stock" value="1" {{ $filters['low_stock'] ? 'checked' : '' }} 
                                       style="width: 1rem; height: 1rem; border-radius: 0.25rem; border: 1px solid #d1d5db; margin-right: 0.5rem;">
                                <span style="font-size: 0.875rem; color: #374151;">Estoque Baixo</span>
                            </label>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" 
                                    style="flex: 1; padding: 0.5rem 1rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">
                                Filtrar
                            </button>
                            <a href="{{ route('products.index') }}" 
                               style="padding: 0.5rem 1rem; background: white; color: #374151; font-weight: 500; border-radius: 0.5rem; border: 1px solid #d1d5db; text-decoration: none; font-size: 0.875rem;">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabela -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Produto</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Categoria</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Condição</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Preço</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Estoque</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="font-weight: 500; color: #111827;">{{ $product->name }}</div>
                                        <div style="font-size: 0.75rem; color: #6b7280;">
                                            SKU: {{ $product->sku }}
                                            @if($product->imei)
                                                | IMEI: {{ $product->imei }}
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $product->category->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $product->condition->label() }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right;">
                                        <div style="font-weight: 600; color: #111827;">{{ $product->formatted_sale_price }}</div>
                                        @if(auth()->user()->isAdmin())
                                            <div style="font-size: 0.75rem; color: #9ca3af;">Custo: {{ $product->formatted_cost_price }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @php
                                            $stockBg = $product->isLowStock() ? ($product->isOutOfStock() ? '#fef2f2' : '#fefce8') : '#f0fdf4';
                                            $stockColor = $product->isLowStock() ? ($product->isOutOfStock() ? '#dc2626' : '#ca8a04') : '#16a34a';
                                        @endphp
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $stockBg }}; color: {{ $stockColor }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $product->stock_quantity }} un.
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @if($product->active)
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f0fdf4; color: #16a34a; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">Ativo</span>
                                        @else
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #6b7280; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">Inativo</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                            <a href="{{ route('products.show', $product) }}" style="color: #6b7280; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Ver</a>
                                            <a href="{{ route('products.edit', $product) }}" style="color: #6b7280; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Editar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 3rem; text-align: center; color: #6b7280;">
                                        Nenhum produto encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($products->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #f9fafb;">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            form[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
