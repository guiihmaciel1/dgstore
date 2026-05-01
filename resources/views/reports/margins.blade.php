<x-app-layout>
    <x-slot name="title">Relatório de Margens</x-slot>
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Margem por Categoria e Fornecedor</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Análise de lucratividade por segmento</p>
                </div>
            </div>

            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('reports.margins') }}" x-data x-ref="filterForm" style="display: flex; align-items: flex-end; gap: 1rem;">
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
                    @include('reports.partials.export-button', ['route' => 'reports.margins.export', 'params' => ['start_date' => $startDate, 'end_date' => $endDate]])
                </form>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Itens Vendidos</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $report['summary']['total_quantity'] }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Faturamento</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">R$ {{ number_format($report['summary']['total_revenue'], 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Lucro Total</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #16a34a; margin-top: 0.25rem;">R$ {{ number_format($report['summary']['total_profit'], 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Margem Média</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: {{ $report['summary']['avg_margin'] >= 20 ? '#16a34a' : ($report['summary']['avg_margin'] >= 10 ? '#ca8a04' : '#dc2626') }}; margin-top: 0.25rem;">{{ number_format($report['summary']['avg_margin'], 1, ',', '.') }}%</div>
                </div>
            </div>

            @php
                $sections = [
                    ['title' => 'Por Categoria', 'data' => $report['by_category'], 'nameKey' => 'label'],
                    ['title' => 'Por Fornecedor', 'data' => $report['by_supplier'], 'nameKey' => 'supplier'],
                    ['title' => 'Por Condição', 'data' => $report['by_condition'], 'nameKey' => 'label'],
                ];
            @endphp

            @foreach($sections as $section)
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <h3 style="font-weight: 600; color: #111827;">{{ $section['title'] }}</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Nome</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Faturamento</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Custo</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Lucro</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Margem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($section['data'] as $row)
                                @php $marginColor = $row['margin'] >= 20 ? '#16a34a' : ($row['margin'] >= 10 ? '#ca8a04' : '#dc2626'); @endphp
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 0.75rem 1.5rem; font-weight: 500; color: #111827;">{{ $row[$section['nameKey']] }}</td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; color: #374151;">{{ $row['quantity'] }}</td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; color: #374151;">R$ {{ number_format($row['revenue'], 2, ',', '.') }}</td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; color: #6b7280;">R$ {{ number_format($row['cost'], 2, ',', '.') }}</td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: {{ $row['profit'] >= 0 ? '#16a34a' : '#dc2626' }};">R$ {{ number_format($row['profit'], 2, ',', '.') }}</td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: center;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $marginColor }}15; color: {{ $marginColor }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ number_format($row['margin'], 1, ',', '.') }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="padding: 2rem; text-align: center; color: #6b7280;">Nenhum dado no período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: repeat(2, 1fr) !important; }
        }
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: 1fr !important; }
            form[style*="display: flex"] { flex-direction: column !important; }
        }
    </style>
</x-app-layout>
