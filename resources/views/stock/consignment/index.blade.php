<x-app-layout>
    <x-slot name="title">Estoque Consignado</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #6ee7b7;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid #fecaca; border-radius: 0.5rem; color: #fca5a5;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Estoque Fornecedor Interno</h1>
                    <p class="text-sm text-dg-500">Controle de estoque consignado por fornecedores</p>
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
                    <button type="button" onclick="openConsignmentEntry()"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; background: linear-gradient(to right, #10b981, #059669); color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem; white-space: nowrap;">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>+ Nova Entrada</span>
                    </button>
                </div>
            </div>

            {{-- Stats --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Disponíveis</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">{{ $stats['available'] }}</div>
                    <div style="font-size: 0.8rem; color: #818181;">R$ {{ number_format($stats['available_value'], 2, ',', '.') }}</div>
                </div>
                <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Vendidos</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">{{ $stats['sold'] }}</div>
                    <div style="font-size: 0.8rem; color: #818181;">R$ {{ number_format($stats['sold_value'], 2, ',', '.') }}</div>
                </div>
            </div>

            {{-- Filtros --}}
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                <form method="GET" action="{{ route('stock.consignment.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: end;">
                    <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                    <div style="flex: 1; min-width: 180px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Fornecedor</label>
                        <select name="supplier_id" style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                            <option value="">Todos</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ ($filters['supplier_id'] ?? '') === $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width: 140px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                            <option value="available" {{ ($filters['status'] ?? 'available') === 'available' ? 'selected' : '' }}>Disponível</option>
                            <option value="sold" {{ ($filters['status'] ?? '') === 'sold' ? 'selected' : '' }}>Vendido</option>
                            <option value="returned" {{ ($filters['status'] ?? '') === 'returned' ? 'selected' : '' }}>Devolvido</option>
                            <option value="all" {{ ($filters['status'] ?? '') === 'all' ? 'selected' : '' }}>Todos</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 180px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Buscar</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nome, IMEI..."
                               style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <button type="submit"
                            style="padding: 0.5rem 1rem; background: #111827; color: white; border: none; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                        Filtrar
                    </button>
                    @if(($filters['supplier_id'] ?? '') || ($filters['status'] ?? 'available') !== 'available' || ($filters['search'] ?? ''))
                        <a href="{{ route('stock.consignment.index', ['view_mode' => $viewMode]) }}" style="padding: 0.5rem 1rem; color: #818181; font-size: 0.875rem; text-decoration: none;">Limpar</a>
                    @endif
                </form>
            </div>

            {{-- Toggle Resumido/Detalhado --}}
            @php
                $currentParams = request()->except('view_mode');
                $summaryUrl = route('stock.consignment.index', array_merge($currentParams, ['view_mode' => 'summary']));
                $detailUrl = route('stock.consignment.index', array_merge($currentParams, ['view_mode' => 'detail']));
            @endphp
            <div style="display: flex; align-items: center; gap: 0; margin-bottom: 1rem;">
                <a href="{{ $summaryUrl }}"
                   style="padding: 0.5rem 1.25rem; font-size: 0.8125rem; font-weight: 600; border: 1px solid rgba(255,255,255,0.08); text-decoration: none; border-radius: 0.5rem 0 0 0.5rem;
                          {{ $viewMode === 'summary' ? 'background: #111827; color: white; border-color: #111827;' : 'background: #141414; color: #818181;' }}">
                    <svg style="width: 0.875rem; height: 0.875rem; display: inline-block; vertical-align: -2px; margin-right: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Resumido
                </a>
                <a href="{{ $detailUrl }}"
                   style="padding: 0.5rem 1.25rem; font-size: 0.8125rem; font-weight: 600; border: 1px solid rgba(255,255,255,0.08); text-decoration: none; border-radius: 0 0.5rem 0.5rem 0; border-left: none;
                          {{ $viewMode === 'detail' ? 'background: #111827; color: white; border-color: #111827;' : 'background: #141414; color: #818181;' }}">
                    <svg style="width: 0.875rem; height: 0.875rem; display: inline-block; vertical-align: -2px; margin-right: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Detalhado
                </a>
            </div>

            {{-- Tabela --}}
            <div style="background: #141414; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                <div style="overflow-x: auto;">
                    @if($viewMode === 'summary')
                        {{-- ===== VISÃO RESUMIDA ===== --}}
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Produto</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Fornecedor</th>
                                    <th style="padding: 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Custo Méd.</th>
                                    <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Disponíveis</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Última Entrada</th>
                                    <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $group)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                        <td style="padding: 0.75rem;">
                                            <span style="font-weight: 600; color: #e3e3e3; font-size: 0.875rem;">{{ $group->name }}</span>
                                            <div style="font-size: 0.75rem; color: #818181; display: flex; align-items: center; gap: 0.375rem; margin-top: 1px; flex-wrap: wrap;">
                                                @if($group->storage) {{ $group->storage }} @endif
                                                @if($group->color) · {{ $group->color }} @endif
                                                @php $condVal = $group->condition instanceof \App\Domain\Product\Enums\ProductCondition ? $group->condition->value : ($group->condition ?? 'new'); @endphp
                                                @if($condVal === 'used')
                                                    <span style="font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:rgba(217,119,6,0.15);color:#fbbf24;">Seminovo</span>
                                                @else
                                                    <span style="font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:rgba(59,130,246,0.15);color:#60a5fa;">Novo</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem; font-size: 0.8125rem; color: #a4a4a4;">{{ $group->supplier_name }}</td>
                                        <td style="padding: 0.75rem; text-align: right; font-size: 0.875rem; font-weight: 600; color: #e3e3e3;">
                                            R$ {{ number_format($group->avg_cost, 2, ',', '.') }}
                                            @if($group->min_cost != $group->max_cost)
                                                <div style="font-size: 0.65rem; color: #666666; font-weight: 400;">
                                                    {{ number_format($group->min_cost, 2, ',', '.') }} ~ {{ number_format($group->max_cost, 2, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center;">
                                            <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 700;
                                                         {{ $group->total_available > 0 ? 'background: rgba(16,185,129,0.12); color: #4ade80;' : 'background: rgba(255,255,255,0.04); color: #515151;' }}">
                                                {{ $group->total_available }}
                                                <span style="font-size: 0.7rem; font-weight: 500; opacity: 0.7;">{{ $group->total_available === 1 ? 'un.' : 'un.' }}</span>
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem; font-size: 0.8125rem; color: #818181;">
                                            {{ \Carbon\Carbon::parse($group->last_received)->format('d/m/Y') }}
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center;">
                                            @php
                                                $detailParams = array_merge(request()->except('view_mode'), [
                                                    'view_mode' => 'detail',
                                                    'search' => $group->name . ($group->storage ? ' ' . $group->storage : ''),
                                                    'supplier_id' => $group->supplier_id,
                                                ]);
                                            @endphp
                                            <a href="{{ route('stock.consignment.index', $detailParams) }}"
                                               style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; background: #222222; color: #a4a4a4; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;"
                                               onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='#222222'">
                                                <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Ver itens
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding: 3rem; text-align: center; color: #666666;">
                                            Nenhum item no estoque consignado
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        {{-- ===== VISÃO DETALHADA ===== --}}
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Produto</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Lote</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Fornecedor</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">IMEI</th>
                                    <th style="padding: 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Custo Forn.</th>
                                    <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 50px;">Bat%</th>
                                    <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 35px;">Cx</th>
                                    <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 40px;">Cabo</th>
                                    <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Status</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Entrada</th>
                                    <th style="padding: 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td style="padding: 0.75rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                @if(!$item->imei && $item->available_quantity > 1)
                                                    <span style="background: rgba(59,130,246,0.15); color: #93c5fd; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">
                                                        {{ $item->available_quantity }}x
                                                    </span>
                                                @endif
                                                <span style="font-weight: 600; color: #e3e3e3; font-size: 0.875rem;">{{ $item->name }}</span>
                                            </div>
                                            <div style="font-size: 0.75rem; color: #818181; display: flex; align-items: center; gap: 0.375rem; margin-top: 1px; flex-wrap: wrap;">
                                                @if($item->storage) {{ $item->storage }} @endif
                                                @if($item->color) · {{ $item->color }} @endif
                                                @if(($item->condition?->value ?? 'new') === 'used')
                                                    <span style="font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:rgba(217,119,6,0.15);color:#fbbf24;">Seminovo</span>
                                                @else
                                                    <span style="font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:rgba(59,130,246,0.15);color:#60a5fa;">Novo</span>
                                                @endif
                                                @if($item->hasBeenExchanged())
                                                    <span title="Item ja foi trocado com outro lojista" style="font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:rgba(219,39,119,0.15);color:#f472b6;">TROCADO</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            @if($item->batch)
                                                <span style="font-size: 0.7rem; font-family: monospace; padding: 0.2rem 0.5rem; background: #222222; border-radius: 0.25rem; color: #a4a4a4; font-weight: 500;">
                                                    {{ $item->batch->batch_code }}
                                                </span>
                                            @else
                                                <span style="color: #515151; font-size: 0.75rem;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem; font-size: 0.8125rem; color: #a4a4a4;">{{ $item->supplier->name }}</td>
                                        <td style="padding: 0.75rem;">
                                            @if($item->imei)
                                                <span style="font-size: 0.8125rem; color: #818181; font-family: monospace;">{{ $item->imei }}</span>
                                                <div style="margin-top: 2px;">
                                                    <span style="font-size: 0.6rem; font-weight: 600; padding: 1px 5px; border-radius: 3px; background: #222222; color: #a4a4a4;">RASTREADO</span>
                                                </div>
                                            @else
                                                <span style="font-size: 0.75rem; color: #666666;">—</span>
                                                <div style="margin-top: 2px;">
                                                    <span style="font-size: 0.6rem; font-weight: 600; padding: 1px 5px; border-radius: 3px; background: rgba(16,185,129,0.15); color: #4ade80;">CONSOLIDADO</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem; text-align: right; font-size: 0.875rem; font-weight: 600; color: #e3e3e3;">
                                            R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center; font-size: 0.8rem;">
                                            @if(($item->condition?->value ?? 'new') === 'used' && $item->battery_health)
                                                <span style="color: #059669; font-weight: 600;">{{ $item->battery_health }}%</span>
                                            @else
                                                <span style="color: #515151;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center; font-size: 0.8rem;">
                                            @if(($item->condition?->value ?? 'new') === 'used')
                                                <span style="{{ $item->has_box ? 'color:#059669;' : 'color:#515151;' }}">{{ $item->has_box ? '✓' : '—' }}</span>
                                            @else
                                                <span style="color: #515151;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center; font-size: 0.8rem;">
                                            @if(($item->condition?->value ?? 'new') === 'used')
                                                <span style="{{ $item->has_cable ? 'color:#059669;' : 'color:#515151;' }}">{{ $item->has_cable ? '✓' : '—' }}</span>
                                            @else
                                                <span style="color: #515151;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center;">
                                            @php
                                                $statusColors = [
                                                    'available' => 'background: rgba(16,185,129,0.12); color: #4ade80;',
                                                    'sold' => 'background: rgba(59,130,246,0.12); color: #60a5fa;',
                                                    'returned' => 'background: rgba(255,255,255,0.06); color: #818181;',
                                                ];
                                            @endphp
                                            <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; {{ $statusColors[$item->status->value] ?? '' }}">
                                                {{ $item->status->label() }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem; font-size: 0.8125rem; color: #818181;">
                                            {{ $item->received_at->format('d/m/Y') }}
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center;">
                                            <div style="display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                                <a href="{{ route('stock.consignment.edit', $item) }}" title="Editar"
                                                   style="padding: 0.375rem; color: #2563eb; background: none; border: none; cursor: pointer; border-radius: 0.25rem; display: inline-flex;"
                                                   onmouseover="this.style.background='rgba(37,99,235,0.1)'" onmouseout="this.style.background='transparent'">
                                                    <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                @if($item->isAvailable())
                                                    <a href="{{ route('stock.consignment.exchange-form', $item) }}" title="Trocar com outro lojista"
                                                       style="padding: 0.375rem; color: #db2777; background: none; border: none; cursor: pointer; border-radius: 0.25rem; display: inline-flex;"
                                                       onmouseover="this.style.background='rgba(219,39,119,0.1)'" onmouseout="this.style.background='transparent'">
                                                        <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <a href="{{ route('stock.consignment.history', $item) }}" title="Historico do item"
                                                   style="padding: 0.375rem; color: #818181; background: none; border: none; cursor: pointer; border-radius: 0.25rem; display: inline-flex;"
                                                   onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='transparent'">
                                                    <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </a>
                                                @if($item->isAvailable())
                                                    <form method="POST" action="{{ route('stock.consignment.return', $item) }}"
                                                          onsubmit="return confirm('Devolver este item ao fornecedor?')" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" title="Devolver ao fornecedor"
                                                                style="padding: 0.375rem; color: #f59e0b; background: none; border: none; cursor: pointer; border-radius: 0.25rem;"
                                                                onmouseover="this.style.background='rgba(245,158,11,0.1)'" onmouseout="this.style.background='transparent'">
                                                            <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @elseif($item->status === \App\Domain\ConsignmentStock\Enums\ConsignmentStatus::Sold)
                                                    <span style="font-size: 0.7rem; color: #818181;">Vendido</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" style="padding: 3rem; text-align: center; color: #666666;">
                                            Nenhum item no estoque consignado
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif
                </div>

                @if($items->hasPages())
                    <div style="padding: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('stock.consignment.partials.entry-modal')

    @if(request('open_entry'))
    <script>
        document.addEventListener('DOMContentLoaded', () => openConsignmentEntry());
    </script>
    @endif
</x-app-layout>
