<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Pedidos de Importação</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Controle de pedidos em trânsito</p>
                </div>
                <a href="{{ route('imports.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Pedido
                </a>
            </div>

            <!-- Cards de Estatísticas -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="padding: 1.25rem; background: {{ $stats['in_transit'] > 0 ? '#eff6ff' : 'white' }}; border-radius: 0.75rem; border: 1px solid {{ $stats['in_transit'] > 0 ? '#bfdbfe' : '#e5e7eb' }};">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.625rem; background: {{ $stats['in_transit'] > 0 ? '#dbeafe' : '#f3f4f6' }}; border-radius: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: {{ $stats['in_transit'] > 0 ? '#2563eb' : '#6b7280' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 1.5rem; font-weight: 700; color: {{ $stats['in_transit'] > 0 ? '#2563eb' : '#111827' }};">{{ $stats['in_transit'] }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280;">Em trânsito</p>
                        </div>
                    </div>
                </div>

                <div style="padding: 1.25rem; background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.625rem; background: #f3f4f6; border-radius: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $stats['active'] }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280;">Pedidos ativos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem; margin-bottom: 1rem;">
                <form method="GET" x-data x-ref="filterForm" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Buscar</label>
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Número, rastreio ou fornecedor..."
                               style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;"
                               x-on:input.debounce.400ms="$refs.filterForm.submit()">
                    </div>
                    <div style="min-width: 150px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                                x-on:change="$refs.filterForm.submit()">
                            <option value="">Todos</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" {{ $filters['status'] === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($filters['search'] || $filters['status'])
                        <a href="{{ route('imports.index') }}" style="padding: 0.5rem 1rem; color: #6b7280; font-size: 0.875rem; text-decoration: none;">Limpar</a>
                    @endif
                </form>
            </div>

            <!-- Lista -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                @if($orders->isEmpty())
                    <div style="padding: 3rem; text-align: center;">
                        <svg style="margin: 0 auto; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p style="margin-top: 1rem; color: #6b7280;">Nenhum pedido encontrado</p>
                        <a href="{{ route('imports.create') }}" style="display: inline-block; margin-top: 1rem; color: #2563eb; text-decoration: none;">Criar primeiro pedido</a>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Pedido</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Fornecedor</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Itens</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Custo Est. (R$)</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Previsão</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 0.75rem 1rem;">
                                            <div style="font-weight: 600; color: #111827;">{{ $order->order_number }}</div>
                                            <div style="font-size: 0.75rem; color: #6b7280;">{{ $order->ordered_at->format('d/m/Y') }}</div>
                                            @if($order->tracking_code)
                                                <div style="font-size: 0.75rem; color: #9ca3af; font-family: monospace;">{{ $order->tracking_code }}</div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem;">
                                            <span style="color: #374151;">{{ $order->supplier?->name ?? 'Não informado' }}</span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @php
                                                $statusColors = [
                                                    'ordered' => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
                                                    'shipped' => ['bg' => '#dbeafe', 'color' => '#2563eb'],
                                                    'in_transit' => ['bg' => '#e0e7ff', 'color' => '#4f46e5'],
                                                    'customs' => ['bg' => '#fef3c7', 'color' => '#d97706'],
                                                    'received' => ['bg' => '#dcfce7', 'color' => '#16a34a'],
                                                    'cancelled' => ['bg' => '#fee2e2', 'color' => '#dc2626'],
                                                ];
                                                $sc = $statusColors[$order->status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                            @endphp
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                                {{ $order->status->label() }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            <span style="font-weight: 500; color: #111827;">{{ $order->total_items }}</span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right;">
                                            <span style="font-weight: 600; color: #111827;">{{ $order->formatted_estimated_total_brl }}</span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @if($order->estimated_arrival)
                                                <span style="color: #374151;">{{ $order->estimated_arrival->format('d/m/Y') }}</span>
                                            @else
                                                <span style="color: #9ca3af;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right;">
                                            <a href="{{ route('imports.show', $order) }}" 
                                               style="padding: 0.375rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;"
                                               onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                                Ver detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
