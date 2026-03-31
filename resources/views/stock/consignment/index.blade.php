<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Estoque Fornecedor Interno</h1>
                    <p class="text-sm text-gray-500">Controle de estoque consignado por fornecedores</p>
                </div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="{{ route('stock.consignment.report') }}"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; background: #16a34a; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; white-space: nowrap;"
                       onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        <svg style="width: 1.25rem; height: 1.25rem;" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Relatório WhatsApp
                    </a>
                    <a href="{{ route('stock.consignment.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="whitespace-nowrap">Nova Entrada</span>
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Disponíveis</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">{{ $stats['available'] }}</div>
                    <div style="font-size: 0.8rem; color: #6b7280;">R$ {{ number_format($stats['available_value'], 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Vendidos</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">{{ $stats['sold'] }}</div>
                    <div style="font-size: 0.8rem; color: #6b7280;">R$ {{ number_format($stats['sold_value'], 2, ',', '.') }}</div>
                </div>
            </div>

            {{-- Filtros --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                <form method="GET" action="{{ route('stock.consignment.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: end;">
                    <div style="flex: 1; min-width: 180px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Fornecedor</label>
                        <select name="supplier_id" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                            <option value="">Todos</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ ($filters['supplier_id'] ?? '') === $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width: 140px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                            <option value="available" {{ ($filters['status'] ?? 'available') === 'available' ? 'selected' : '' }}>Disponível</option>
                            <option value="sold" {{ ($filters['status'] ?? '') === 'sold' ? 'selected' : '' }}>Vendido</option>
                            <option value="returned" {{ ($filters['status'] ?? '') === 'returned' ? 'selected' : '' }}>Devolvido</option>
                            <option value="all" {{ ($filters['status'] ?? '') === 'all' ? 'selected' : '' }}>Todos</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 180px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Buscar</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nome, IMEI..."
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <button type="submit"
                            style="padding: 0.5rem 1rem; background: #111827; color: white; border: none; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                        Filtrar
                    </button>
                    @if(($filters['supplier_id'] ?? '') || ($filters['status'] ?? 'available') !== 'available' || ($filters['search'] ?? ''))
                        <a href="{{ route('stock.consignment.index') }}" style="padding: 0.5rem 1rem; color: #6b7280; font-size: 0.875rem; text-decoration: none;">Limpar</a>
                    @endif
                </form>
            </div>

            {{-- Tabela --}}
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Fornecedor</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">IMEI</th>
                                <th style="padding: 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Custo Forn.</th>
                                <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 50px;">Bat%</th>
                                <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 35px;">Cx</th>
                                <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 40px;">Cabo</th>
                                <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Entrada</th>
                                <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 0.75rem;">
                                        <div style="font-weight: 600; color: #111827; font-size: 0.875rem;">{{ $item->name }}</div>
                                        <div style="font-size: 0.75rem; color: #6b7280; display: flex; align-items: center; gap: 0.375rem; margin-top: 1px;">
                                            @if($item->storage) {{ $item->storage }} @endif
                                            @if($item->color) · {{ $item->color }} @endif
                                            @if(($item->condition?->value ?? 'new') === 'used')
                                                <span style="font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:#fef3c7;color:#92400e;">Seminovo</span>
                                            @else
                                                <span style="font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:#dbeafe;color:#1e40af;">Novo</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem; font-size: 0.8125rem; color: #374151;">{{ $item->supplier->name }}</td>
                                    <td style="padding: 0.75rem; font-size: 0.8125rem; color: #6b7280; font-family: monospace;">{{ $item->imei ?? '-' }}</td>
                                    <td style="padding: 0.75rem; text-align: right; font-size: 0.875rem; font-weight: 600; color: #111827;">
                                        R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; font-size: 0.8rem;">
                                        @if(($item->condition?->value ?? 'new') === 'used' && $item->battery_health)
                                            <span style="color: #059669; font-weight: 600;">{{ $item->battery_health }}%</span>
                                        @else
                                            <span style="color: #d1d5db;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; font-size: 0.8rem;">
                                        @if(($item->condition?->value ?? 'new') === 'used')
                                            <span style="{{ $item->has_box ? 'color:#059669;' : 'color:#d1d5db;' }}">{{ $item->has_box ? '✓' : '—' }}</span>
                                        @else
                                            <span style="color: #d1d5db;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; font-size: 0.8rem;">
                                        @if(($item->condition?->value ?? 'new') === 'used')
                                            <span style="{{ $item->has_cable ? 'color:#059669;' : 'color:#d1d5db;' }}">{{ $item->has_cable ? '✓' : '—' }}</span>
                                        @else
                                            <span style="color: #d1d5db;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; font-size: 0.875rem;">
                                        {{ $item->available_quantity }}/{{ $item->quantity }}
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center;">
                                        @php
                                            $statusColors = [
                                                'available' => 'background: #dcfce7; color: #16a34a;',
                                                'sold' => 'background: #dbeafe; color: #2563eb;',
                                                'returned' => 'background: #f3f4f6; color: #6b7280;',
                                            ];
                                        @endphp
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; {{ $statusColors[$item->status->value] ?? '' }}">
                                            {{ $item->status->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem; font-size: 0.8125rem; color: #6b7280;">
                                        {{ $item->received_at->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                            <a href="{{ route('stock.consignment.edit', $item) }}" title="Editar"
                                               style="padding: 0.375rem; color: #2563eb; background: none; border: none; cursor: pointer; border-radius: 0.25rem; display: inline-flex;"
                                               onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='transparent'">
                                                <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            @if($item->isAvailable())
                                                <form method="POST" action="{{ route('stock.consignment.return', $item) }}"
                                                      onsubmit="return confirm('Devolver este item ao fornecedor?')" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" title="Devolver ao fornecedor"
                                                            style="padding: 0.375rem; color: #f59e0b; background: none; border: none; cursor: pointer; border-radius: 0.25rem;"
                                                            onmouseover="this.style.background='#fffbeb'" onmouseout="this.style.background='transparent'">
                                                        <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @elseif($item->status === \App\Domain\ConsignmentStock\Enums\ConsignmentStatus::Sold)
                                                <span style="font-size: 0.7rem; color: #6b7280;">Vendido</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" style="padding: 3rem; text-align: center; color: #9ca3af;">
                                        Nenhum item no estoque consignado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
