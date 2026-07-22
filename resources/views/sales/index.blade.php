<x-app-layout>
    <x-slot name="title">Vendas</x-slot>
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

            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Vendas</h1>
                    <p class="text-sm text-dg-500">Histórico de vendas realizadas</p>
                </div>
                <a href="{{ route('sales.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-surface text-white font-semibold rounded-lg hover:bg-surface-elevated transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Nova Venda</span>
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                
                <!-- Filtros -->
                <div class="p-4 border-b border-border bg-surface">
                    <form method="GET" action="{{ route('sales.index') }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Buscar</label>
                                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nº da venda, cliente..." 
                                       class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Status</label>
                                <select name="payment_status" class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-gray-900 focus:outline-none">
                                    <option value="">Todos</option>
                                    @foreach($paymentStatuses as $status)
                                        <option value="{{ $status->value }}" {{ ($filters['payment_status'] ?? '') === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Pagamento</label>
                                <select name="payment_method" class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-gray-900 focus:outline-none">
                                    <option value="">Todos</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->value }}" {{ ($filters['payment_method'] ?? '') === $method->value ? 'selected' : '' }}>
                                            {{ $method->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Tipo</label>
                                <select name="sale_type" class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-gray-900 focus:outline-none">
                                    <option value="">Todos os tipos</option>
                                    <option value="cliente_final" {{ ($filters['sale_type'] ?? '') === 'cliente_final' ? 'selected' : '' }}>Cliente Final</option>
                                    <option value="repasse" {{ ($filters['sale_type'] ?? '') === 'repasse' ? 'selected' : '' }}>Repasse</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Data Início</label>
                                <input type="date" name="date_from" value="{{ $filters['date_from'] }}" 
                                       class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Data Fim</label>
                                <input type="date" name="date_to" value="{{ $filters['date_to'] }}" 
                                       class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-4">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-surface text-white font-semibold rounded-lg text-sm hover:bg-surface-elevated transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Filtrar
                            </button>
                            <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-raised text-dg-400 font-medium rounded-lg border border-border-strong text-sm hover:bg-surface transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Limpar
                            </a>
                            @if(collect($filters)->filter()->isNotEmpty())
                                <span class="text-xs text-dg-500">
                                    {{ collect($filters)->filter()->count() }} {{ collect($filters)->filter()->count() === 1 ? 'filtro ativo' : 'filtros ativos' }}
                                </span>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Tabela -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Venda</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Cliente</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Tipo</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Total</th>
                                @if(auth()->user()->canViewFinancials())
                                <th style="padding: 0.75rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Lucro Bruto</th>
                                <th style="padding: 0.75rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Comissão</th>
                                <th style="padding: 0.75rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Lucro Líq.</th>
                                @endif
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Data</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="font-weight: 600; color: #e3e3e3;">{{ $sale->sale_number }}</div>
                                        <div style="font-size: 0.75rem; color: #666666;">{{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name }}</div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #a4a4a4;">
                                        {{ $sale->customer?->name ?? 'Cliente não informado' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        @if($sale->sale_type)
                                            <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; {{ $sale->sale_type->value === 'repasse' ? 'background: rgba(168,85,247,0.1); color: #c4b5fd;' : 'background: rgba(59,130,246,0.1); color: #3b82f6;' }}">
                                                {{ $sale->sale_type->label() }}
                                            </span>
                                        @else
                                            <span style="font-size: 0.75rem; color: #515151;">N/A</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 1rem; color: #e3e3e3;">
                                        {{ $sale->formatted_total }}
                                    </td>
                                    @if(auth()->user()->canViewFinancials())
                                    @php
                                        $commissionTotal = $sale->commissions->sum('commission_amount');
                                        $netProfit = $sale->profit - $commissionTotal;
                                    @endphp
                                    <td style="padding: 0.75rem 0.75rem; text-align: right; font-weight: 600; font-size: 0.8125rem; color: {{ $sale->profit >= 0 ? '#16a34a' : '#dc2626' }};">
                                        R$ {{ number_format($sale->profit, 2, ',', '.') }}
                                    </td>
                                    <td style="padding: 0.75rem 0.75rem; text-align: right; font-size: 0.8125rem; font-weight: 600; color: {{ $commissionTotal > 0 ? '#7c3aed' : '#d1d5db' }};">
                                        {{ $commissionTotal > 0 ? 'R$ ' . number_format($commissionTotal, 2, ',', '.') : '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 0.75rem; text-align: right; font-weight: 700; font-size: 0.8125rem; color: {{ $netProfit >= 0 ? '#059669' : '#dc2626' }};">
                                        R$ {{ number_format($netProfit, 2, ',', '.') }}
                                    </td>
                                    @endif
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @php
                                            $statusColors = [
                                                'paid' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'pending' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                                'partial' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                'cancelled' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                            ];
                                            $sc = $statusColors[$sale->payment_status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                        @endphp
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $sale->payment_status->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #818181;">
                                        {{ $sale->sold_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; background: #222222; color: #a4a4a4; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;"
                                           onmouseover="this.style.background='#222222'" onmouseout="this.style.background='#1a1a1a'">
                                            Ver detalhes
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="padding: 3rem; text-align: center; color: #818181;">
                                        Nenhuma venda encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($sales->count() > 0)
                        <tfoot>
                            <tr style="background: #1a1a1a; border-top: 2px solid rgba(255,255,255,0.08);">
                                <td colspan="3" style="padding: 0.875rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Total ({{ $totals['count'] }} {{ $totals['count'] === 1 ? 'venda' : 'vendas' }})
                                </td>
                                <td style="padding: 0.875rem 1rem; text-align: right; font-weight: 700; font-size: 0.9375rem; color: #e3e3e3;">
                                    R$ {{ number_format($totals['total_revenue'], 2, ',', '.') }}
                                </td>
                                @if(auth()->user()->canViewFinancials())
                                <td style="padding: 0.875rem 0.75rem; text-align: right; font-weight: 700; font-size: 0.8125rem; color: {{ $totals['total_profit'] >= 0 ? '#16a34a' : '#dc2626' }};">
                                    R$ {{ number_format($totals['total_profit'], 2, ',', '.') }}
                                </td>
                                <td style="padding: 0.875rem 0.75rem; text-align: right; font-weight: 700; font-size: 0.8125rem; color: {{ ($totals['total_commissions'] ?? 0) > 0 ? '#7c3aed' : '#d1d5db' }};">
                                    {{ ($totals['total_commissions'] ?? 0) > 0 ? 'R$ ' . number_format($totals['total_commissions'], 2, ',', '.') : '-' }}
                                </td>
                                <td style="padding: 0.875rem 0.75rem; text-align: right; font-weight: 800; font-size: 0.8125rem; color: {{ ($totals['total_net_profit'] ?? 0) >= 0 ? '#059669' : '#dc2626' }};">
                                    R$ {{ number_format($totals['total_net_profit'] ?? 0, 2, ',', '.') }}
                                </td>
                                @endif
                                <td style="padding: 0.875rem 1rem; text-align: center;">
                                    @if($totals['total_revenue'] > 0)
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; background: {{ ($totals['total_net_profit'] ?? $totals['total_profit']) >= 0 ? '#f0fdf4' : '#fef2f2' }}; color: {{ ($totals['total_net_profit'] ?? $totals['total_profit']) >= 0 ? '#16a34a' : '#dc2626' }};">
                                            {{ number_format((($totals['total_net_profit'] ?? $totals['total_profit']) / $totals['total_revenue']) * 100, 1, ',', '.') }}%
                                        </span>
                                    @endif
                                </td>
                                <td colspan="2" style="padding: 0.875rem 1rem;"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                <!-- Paginação -->
                @if($sales->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                        {{ $sales->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
