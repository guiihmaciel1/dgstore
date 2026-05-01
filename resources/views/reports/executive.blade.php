<x-app-layout>
    <x-slot name="title">Dashboard Executivo</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('reports.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div style="flex: 1;">
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Dashboard Executivo</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Visão estratégica com evolução e comparativos</p>
                </div>
                @include('reports.partials.export-button', ['route' => 'reports.executive.export', 'params' => ['month' => $referenceDate->month, 'year' => $referenceDate->year]])
            </div>

            {{-- Navegação de mês --}}
            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1.5rem;">
                @php
                    $prevMonth = $referenceDate->copy()->subMonth();
                    $nextMonth = $referenceDate->copy()->addMonth();
                @endphp
                <a href="{{ route('reports.executive', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                   style="padding: 0.5rem; color: #6b7280; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span style="font-size: 1.125rem; font-weight: 600; color: #111827; min-width: 10rem; text-align: center;">
                    {{ $referenceDate->translatedFormat('F / Y') }}
                </span>
                @if(!$isCurrentMonth)
                <a href="{{ route('reports.executive', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                   style="padding: 0.5rem; color: #6b7280; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @else
                <div style="width: 2.25rem;"></div>
                @endif
            </div>

            {{-- KPI Cards com comparativos --}}
            @php
                $kpis = [
                    ['label' => 'Faturamento', 'key' => 'revenue', 'prefix' => 'R$ ', 'monetary' => true],
                    ['label' => 'Lucro', 'key' => 'profit', 'prefix' => 'R$ ', 'monetary' => true],
                    ['label' => 'Vendas', 'key' => 'count', 'prefix' => '', 'monetary' => false],
                    ['label' => 'Ticket Médio', 'key' => 'ticket', 'prefix' => 'R$ ', 'monetary' => true],
                    ['label' => 'Novos Clientes', 'key' => 'new_customers', 'prefix' => '', 'monetary' => false],
                ];
            @endphp
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($kpis as $kpi)
                    @php
                        $val = $report['current'][$kpi['key']];
                        $deltaPrev = $report['deltas_prev'][$kpi['key']];
                        $deltaYear = $report['deltas_year'][$kpi['key']];
                        $prevPositive = $deltaPrev >= 0;
                        $yearPositive = $deltaYear >= 0;
                    @endphp
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <div style="font-size: 0.7rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">{{ $kpi['label'] }}</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">
                            {{ $kpi['prefix'] }}{{ $kpi['monetary'] ? number_format((float)$val, 2, ',', '.') : $val }}
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem; flex-wrap: wrap;">
                            <span title="vs mês anterior" style="display: inline-block; padding: 0.1rem 0.4rem; background: {{ $prevPositive ? '#f0fdf4' : '#fef2f2' }}; color: {{ $prevPositive ? '#16a34a' : '#dc2626' }}; font-size: 0.65rem; font-weight: 600; border-radius: 0.25rem;">
                                {{ $prevPositive ? '+' : '' }}{{ number_format($deltaPrev, 1, ',', '') }}% mês
                            </span>
                            <span title="vs mesmo mês ano anterior" style="display: inline-block; padding: 0.1rem 0.4rem; background: {{ $yearPositive ? '#eff6ff' : '#fef2f2' }}; color: {{ $yearPositive ? '#2563eb' : '#dc2626' }}; font-size: 0.65rem; font-weight: 600; border-radius: 0.25rem;">
                                {{ $yearPositive ? '+' : '' }}{{ number_format($deltaYear, 1, ',', '') }}% ano
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Gráfico de evolução mensal --}}
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 600; color: #111827; margin-bottom: 1rem;">Evolução Mensal (12 meses)</h3>
                <div style="height: 350px;">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>

            {{-- Top Vendedores --}}
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <h3 style="font-weight: 600; color: #111827;">Top 5 Vendedores do Mês</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Vendedor</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Vendas</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Faturamento</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Lucro</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ticket Médio</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Margem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['top_sellers'] as $index => $seller)
                                @php
                                    $isTop3 = $index < 3;
                                    $medalColors = ['#fbbf24', '#9ca3af', '#d97706'];
                                @endphp
                                <tr style="border-bottom: 1px solid #f3f4f6; {{ $isTop3 ? 'background: #fefce8;' : '' }}" onmouseover="this.style.background='{{ $isTop3 ? '#fef9c3' : '#f9fafb' }}'" onmouseout="this.style.background='{{ $isTop3 ? '#fefce8' : 'white' }}'">
                                    <td style="padding: 0.75rem 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            @if($isTop3)
                                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: {{ $medalColors[$index] }}; color: white; border-radius: 50%; font-weight: 700; font-size: 0.875rem; flex-shrink: 0;">
                                                {{ $index + 1 }}
                                            </span>
                                            @endif
                                            <span style="font-weight: 500; color: #111827;">{{ $seller['name'] }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; color: #374151;">{{ $seller['count'] }}</td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #111827;">R$ {{ number_format($seller['revenue'], 2, ',', '.') }}</td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #16a34a;">R$ {{ number_format($seller['profit'], 2, ',', '.') }}</td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; color: #374151;">R$ {{ number_format($seller['ticket'], 2, ',', '.') }}</td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: center;">
                                        @php $mColor = $seller['margin'] >= 20 ? '#16a34a' : ($seller['margin'] >= 10 ? '#ca8a04' : '#dc2626'); @endphp
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $mColor }}15; color: {{ $mColor }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ number_format($seller['margin'], 1, ',', '.') }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="padding: 3rem; text-align: center; color: #6b7280;">Nenhuma venda no período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1280px) {
            div[style*="grid-template-columns: repeat(5"] { grid-template-columns: repeat(3, 1fr) !important; }
        }
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(5"] { grid-template-columns: repeat(2, 1fr) !important; }
        }
        @media (max-width: 480px) {
            div[style*="grid-template-columns: repeat(5"] { grid-template-columns: 1fr !important; }
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('evolutionChart');
            if (!ctx) return;

            const evolution = @json($report['monthly_evolution']);
            const labels = evolution.map(m => m.label);
            const revenueData = evolution.map(m => m.revenue);
            const profitData = evolution.map(m => m.profit);
            const ticketData = evolution.map(m => m.ticket);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Faturamento',
                            data: revenueData,
                            borderColor: '#111827',
                            backgroundColor: 'rgba(17, 24, 39, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Lucro',
                            data: profitData,
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Ticket Médio',
                            data: ticketData,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            tension: 0.3,
                            fill: false,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16, font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.dataset.label + ': R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            ticks: {
                                callback: v => 'R$ ' + (v / 1000).toFixed(0) + 'k',
                                font: { size: 10 }
                            },
                            grid: { color: '#f3f4f6' }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            ticks: {
                                callback: v => 'R$ ' + v.toLocaleString('pt-BR'),
                                font: { size: 10 }
                            },
                            grid: { drawOnChartArea: false }
                        },
                        x: {
                            ticks: { font: { size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
