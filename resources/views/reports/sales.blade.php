<x-app-layout>
    <x-slot name="title">Relatório de Vendas</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('reports.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Relatório de Vendas</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Análise detalhada de vendas por período</p>
                </div>
            </div>

            <!-- Filtros -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('reports.sales') }}" x-data x-ref="filterForm" style="display: flex; align-items: flex-end; gap: 1rem;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Data Inicial</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                               x-on:change="$refs.filterForm.submit()">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Data Final</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                               x-on:change="$refs.filterForm.submit()">
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('reports.sales.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                           style="padding: 0.625rem 1.25rem; background: #374151; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem;">
                            PDF
                        </a>
                        @include('reports.partials.export-button', ['route' => 'reports.sales.export', 'params' => ['start_date' => $startDate, 'end_date' => $endDate]])
                    </div>
                </form>

                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                    <details>
                        <summary style="cursor: pointer; font-size: 0.875rem; font-weight: 500; color: #6b7280;">Comparar com outro período</summary>
                        <form method="GET" action="{{ route('reports.sales') }}" style="display: flex; align-items: flex-end; gap: 1rem; margin-top: 0.75rem;">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            <div style="flex: 1;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Início Comparação</label>
                                <input type="date" name="compare_start_date" value="{{ $compareStartDate ?? '' }}"
                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Fim Comparação</label>
                                <input type="date" name="compare_end_date" value="{{ $compareEndDate ?? '' }}"
                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                            </div>
                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">
                                Comparar
                            </button>
                        </form>
                    </details>
                </div>
            </div>

            <!-- Resumo -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Total de Vendas</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $report['summary']['total_sales'] }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Faturamento</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #16a34a; margin-top: 0.25rem;">R$ {{ number_format($report['summary']['total_revenue'], 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Descontos</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #dc2626; margin-top: 0.25rem;">R$ {{ number_format($report['summary']['total_discount'], 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Ticket Médio</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">R$ {{ number_format($report['summary']['average_ticket'], 2, ',', '.') }}</div>
                </div>
            </div>

            <!-- Por Pagamento e Vendedor -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Por Forma de Pagamento -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-weight: 600; color: #111827;">Por Forma de Pagamento</h3>
                    </div>
                    <div style="padding: 1rem 1.5rem;">
                        @if($report['by_payment_method']->count() > 0)
                            @foreach($report['by_payment_method'] as $method => $data)
                                @php $methodEnum = \App\Domain\Sale\Enums\PaymentMethod::from($method); @endphp
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                                    <span style="font-size: 0.875rem; color: #374151;">{{ $methodEnum->label() }}</span>
                                    <div style="text-align: right;">
                                        <span style="font-weight: 600; color: #111827;">R$ {{ number_format($data['total'], 2, ',', '.') }}</span>
                                        <span style="font-size: 0.75rem; color: #9ca3af; margin-left: 0.5rem;">({{ $data['count'] }} vendas)</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p style="text-align: center; color: #6b7280; padding: 1rem;">Nenhuma venda no período.</p>
                        @endif
                    </div>
                </div>

                <!-- Por Vendedor -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-weight: 600; color: #111827;">Por Vendedor</h3>
                    </div>
                    <div style="padding: 1rem 1.5rem;">
                        @if($report['by_seller']->count() > 0)
                            @foreach($report['by_seller'] as $seller)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                                    <span style="font-size: 0.875rem; color: #374151;">{{ $seller['seller_name'] }}</span>
                                    <div style="text-align: right;">
                                        <span style="font-weight: 600; color: #111827;">R$ {{ number_format($seller['total'], 2, ',', '.') }}</span>
                                        <span style="font-size: 0.75rem; color: #9ca3af; margin-left: 0.5rem;">({{ $seller['count'] }} vendas)</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p style="text-align: center; color: #6b7280; padding: 1rem;">Nenhuma venda no período.</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($comparison)
            <!-- Comparativo -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f0f9ff;">
                    <h3 style="font-weight: 600; color: #111827;">Comparativo de Períodos</h3>
                    <p style="font-size: 0.75rem; color: #6b7280;">{{ $comparison['period1']['period']['start'] }} - {{ $comparison['period1']['period']['end'] }} vs {{ $comparison['period2']['period']['start'] }} - {{ $comparison['period2']['period']['end'] }}</p>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                        @php
                            $compMetrics = [
                                ['label' => 'Vendas', 'key' => 'total_sales', 'prefix' => '', 'monetary' => false],
                                ['label' => 'Faturamento', 'key' => 'total_revenue', 'prefix' => 'R$ ', 'monetary' => true],
                                ['label' => 'Descontos', 'key' => 'total_discount', 'prefix' => 'R$ ', 'monetary' => true],
                                ['label' => 'Ticket Médio', 'key' => 'average_ticket', 'prefix' => 'R$ ', 'monetary' => true],
                            ];
                        @endphp
                        @foreach($compMetrics as $m)
                            @php
                                $v1 = $comparison['period1']['summary'][$m['key']];
                                $v2 = $comparison['period2']['summary'][$m['key']];
                                $delta = $comparison['deltas'][$m['key']];
                                $isPositive = $delta >= 0;
                                $deltaColor = $m['key'] === 'total_discount' ? ($isPositive ? '#dc2626' : '#16a34a') : ($isPositive ? '#16a34a' : '#dc2626');
                            @endphp
                            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb;">
                                <div style="font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">{{ $m['label'] }}</div>
                                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                                    <div>
                                        <div style="font-size: 0.65rem; color: #9ca3af;">Período 1</div>
                                        <div style="font-weight: 700; color: #111827;">{{ $m['prefix'] }}{{ $m['monetary'] ? number_format((float)$v1, 2, ',', '.') : $v1 }}</div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 0.65rem; color: #9ca3af;">Período 2</div>
                                        <div style="font-weight: 600; color: #6b7280;">{{ $m['prefix'] }}{{ $m['monetary'] ? number_format((float)$v2, 2, ',', '.') : $v2 }}</div>
                                    </div>
                                </div>
                                <div style="margin-top: 0.5rem; text-align: center;">
                                    <span style="display: inline-block; padding: 0.15rem 0.5rem; background: {{ $deltaColor }}15; color: {{ $deltaColor }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                        {{ $isPositive ? '+' : '' }}{{ number_format($delta, 1, ',', '') }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Lista de Vendas -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <h3 style="font-weight: 600; color: #111827;">Vendas do Período</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Venda</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Cliente</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Vendedor</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Total</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['sales'] as $sale)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <a href="{{ route('sales.show', $sale) }}" style="font-weight: 600; color: #111827; text-decoration: none;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#111827'">
                                            {{ $sale->sale_number }}
                                        </a>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $sale->customer?->name ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #111827;">
                                        {{ $sale->formatted_total }}
                                    </td>
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
                                    <td style="padding: 0.75rem 1.5rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $sale->sold_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 3rem; text-align: center; color: #6b7280;">
                                        Nenhuma venda no período.
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
            div[style*="grid-template-columns: repeat(2"] { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: 1fr !important; }
            form[style*="display: flex"] { flex-direction: column !important; }
        }
    </style>
</x-app-layout>
