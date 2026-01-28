<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('products.show', $product) }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Histórico de Estoque</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">{{ $product->name }}</p>
                </div>
            </div>

            <!-- Cards de Resumo -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Estoque Atual</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: {{ $product->isLowStock() ? ($product->isOutOfStock() ? '#dc2626' : '#ca8a04') : '#111827' }}; margin-top: 0.25rem;">
                        {{ $product->stock_quantity }} un.
                    </div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">SKU</div>
                    <div style="font-size: 1.25rem; font-weight: 600; color: #111827; margin-top: 0.25rem; font-family: monospace;">{{ $product->sku }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; {{ $product->isLowStock() ? 'background: #fef2f2; border-color: #fecaca;' : '' }}">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Alerta Mínimo</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: {{ $product->isLowStock() ? '#dc2626' : '#16a34a' }}; margin-top: 0.25rem;">{{ $product->min_stock_alert }} un.</div>
                </div>
            </div>

            <!-- Tabela de Movimentações -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <h3 style="font-weight: 600; color: #111827;">Movimentações</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Tipo</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Quantidade</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Motivo</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Usuário</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
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
                                        <svg style="margin: 0 auto 1rem; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        Nenhuma movimentação registrada.
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
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(3"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
