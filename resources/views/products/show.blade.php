<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('products.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $product->name }}</h1>
                        <p style="font-size: 0.875rem; color: #6b7280;">SKU: {{ $product->sku }}</p>
                    </div>
                </div>
                <a href="{{ route('products.edit', $product) }}" 
                   style="padding: 0.625rem 1.5rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    Editar Produto
                </a>
            </div>

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #16a34a;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <!-- Coluna Principal -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Informações do Produto -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Informações do Produto</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Categoria</dt>
                                    <dd style="margin-top: 0.25rem;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $product->category->label() }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Condição</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $product->condition->label() }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Status</dt>
                                    <dd style="margin-top: 0.25rem;">
                                        @if($product->active)
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f0fdf4; color: #16a34a; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">Ativo</span>
                                        @else
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #6b7280; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">Inativo</span>
                                        @endif
                                    </dd>
                                </div>
                                @if($product->model)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Modelo</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $product->model }}</dd>
                                </div>
                                @endif
                                @if($product->storage)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Armazenamento</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $product->storage }}</dd>
                                </div>
                                @endif
                                @if($product->color)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Cor</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $product->color }}</dd>
                                </div>
                                @endif
                                @if($product->imei)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">IMEI/Serial</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827; font-family: monospace;">{{ $product->imei }}</dd>
                                </div>
                                @endif
                                @if($product->supplier)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Fornecedor</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $product->supplier }}</dd>
                                </div>
                                @endif
                            </div>
                            @if($product->notes)
                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Observações</dt>
                                <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $product->notes }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Movimentações Recentes -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-weight: 600; color: #111827;">Movimentações Recentes</h3>
                            <a href="{{ route('stock.product-history', $product) }}" style="font-size: 0.875rem; color: #111827; text-decoration: none;"
                               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                Ver histórico completo →
                            </a>
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Tipo</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Motivo</th>
                                        <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Usuário</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->stockMovements->take(5) as $movement)
                                        @php
                                            $typeColors = [
                                                'in' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'out' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                                'adjustment' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                'return' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                            ];
                                            $tc = $typeColors[$movement->type->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                        @endphp
                                        <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                            <td style="padding: 0.75rem 1.5rem; font-size: 0.875rem; color: #6b7280;">
                                                {{ $movement->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $tc['bg'] }}; color: {{ $tc['color'] }}; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                                    {{ $movement->type->label() }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: {{ $movement->isAddition() ? '#16a34a' : '#dc2626' }};">
                                                {{ $movement->isAddition() ? '+' : '-' }}{{ $movement->quantity }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                                {{ $movement->reason ?? '-' }}
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; font-size: 0.875rem; color: #6b7280;">
                                                {{ $movement->user?->name ?? 'Sistema' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="padding: 3rem; text-align: center; color: #6b7280;">
                                                Nenhuma movimentação registrada.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Preços -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Preços</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="margin-bottom: 1rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Preço de Venda</dt>
                                <dd style="margin-top: 0.25rem; font-size: 1.75rem; font-weight: 700; color: #16a34a;">{{ $product->formatted_sale_price }}</dd>
                            </div>
                            @if(auth()->user()->isAdmin())
                            <div style="margin-bottom: 1rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Preço de Custo</dt>
                                <dd style="margin-top: 0.25rem; font-size: 1.25rem; font-weight: 600; color: #111827;">{{ $product->formatted_cost_price }}</dd>
                            </div>
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Margem de Lucro</dt>
                                <dd style="margin-top: 0.25rem; font-size: 1.25rem; font-weight: 600; color: #111827;">{{ number_format($product->profit_margin, 1) }}%</dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Estoque -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; {{ $product->isLowStock() ? ($product->isOutOfStock() ? 'border-color: #fecaca;' : 'border-color: #fde68a;') : '' }}">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: {{ $product->isLowStock() ? ($product->isOutOfStock() ? '#fef2f2' : '#fefce8') : '#f9fafb' }};">
                            <h3 style="font-weight: 600; color: #111827;">Estoque</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="margin-bottom: 1rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Quantidade Atual</dt>
                                <dd style="margin-top: 0.25rem;">
                                    <span style="font-size: 2rem; font-weight: 700; color: {{ $product->isLowStock() ? ($product->isOutOfStock() ? '#dc2626' : '#ca8a04') : '#111827' }};">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    <span style="color: #6b7280;"> unidades</span>
                                </dd>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Alerta Mínimo</dt>
                                <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $product->min_stock_alert }} unidades</dd>
                            </div>
                            @if($product->isLowStock())
                            <div style="margin-bottom: 1rem; padding: 0.75rem; background: {{ $product->isOutOfStock() ? '#fef2f2' : '#fefce8' }}; border-radius: 0.5rem; border: 1px solid {{ $product->isOutOfStock() ? '#fecaca' : '#fde68a' }};">
                                <p style="font-size: 0.875rem; font-weight: 500; color: {{ $product->isOutOfStock() ? '#dc2626' : '#ca8a04' }};">
                                    {{ $product->isOutOfStock() ? '⚠️ Produto sem estoque!' : '⚠️ Estoque baixo! Considere reabastecer.' }}
                                </p>
                            </div>
                            @endif
                            <a href="{{ route('stock.create') }}?product_id={{ $product->id }}" 
                               style="display: block; width: 100%; padding: 0.75rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none; text-align: center;"
                               onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                + Registrar Entrada
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: 2fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
