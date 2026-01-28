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
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Movimentações de Estoque</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Histórico de entradas, saídas e ajustes de estoque</p>
                </div>
                <a href="{{ route('stock.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background 0.2s;"
                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Movimentação
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                
                <!-- Tabela -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Data</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Produto</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Tipo</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Quantidade</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Motivo</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Usuário</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 1rem 1.5rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $movement->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <a href="{{ route('products.show', $movement->product) }}" style="font-weight: 500; color: #111827; text-decoration: none;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#111827'">
                                            {{ $movement->product?->name ?? 'Produto removido' }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: #9ca3af;">
                                            SKU: {{ $movement->product?->sku ?? '-' }}
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @php
                                            $typeColors = [
                                                'in' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'out' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                                'adjustment' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                'return' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                            ];
                                            $tc = $typeColors[$movement->type->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                        @endphp
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $tc['bg'] }}; color: {{ $tc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $movement->type->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @if($movement->isAddition())
                                            <span style="font-weight: 700; font-size: 1rem; color: #16a34a;">+{{ $movement->quantity }}</span>
                                        @else
                                            <span style="font-weight: 700; font-size: 1rem; color: #dc2626;">-{{ $movement->quantity }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280; max-width: 250px;">
                                        <span style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $movement->reason ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $movement->user?->name ?? 'Sistema' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 3rem; text-align: center; color: #6b7280;">
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
</x-app-layout>
