<x-app-layout>
    <x-slot name="title">Relatório de Comissões</x-slot>
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Relatório de Comissões</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Detalhamento de comissões por vendedor e categoria</p>
                </div>
            </div>

            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('reports.commissions') }}" x-data x-ref="filterForm" style="display: flex; align-items: flex-end; gap: 1rem;">
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
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Vendedor</label>
                        <select name="user_id" style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                                x-on:change="$refs.filterForm.submit()">
                            <option value="">Todos</option>
                            @foreach($report['sellers'] as $seller)
                                <option value="{{ $seller->id }}" {{ $selectedUserId == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @include('reports.partials.export-button', ['route' => 'reports.commissions.export', 'params' => ['start_date' => $startDate, 'end_date' => $endDate, 'user_id' => $selectedUserId]])
                </form>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Vendedores</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $report['summary']['total_sellers'] }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Total Comissões</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #16a34a; margin-top: 0.25rem;">R$ {{ number_format($report['summary']['total_commissions'], 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Total Saques</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #dc2626; margin-top: 0.25rem;">R$ {{ number_format($report['summary']['total_withdrawn'], 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Saldo Pendente</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #ca8a04; margin-top: 0.25rem;">R$ {{ number_format($report['summary']['total_balance'], 2, ',', '.') }}</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-weight: 600; color: #111827;">Por Vendedor</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Vendedor</th>
                                    <th style="padding: 0.75rem 0.5rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Vendas</th>
                                    <th style="padding: 0.75rem 0.5rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Faturamento</th>
                                    <th style="padding: 0.75rem 0.5rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Comissão</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report['by_seller'] as $seller)
                                    <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                        <td style="padding: 0.75rem 1rem; font-weight: 500; color: #111827; font-size: 0.875rem;">{{ $seller['name'] }}</td>
                                        <td style="padding: 0.75rem 0.5rem; text-align: center; color: #374151; font-size: 0.875rem;">{{ $seller['sales_count'] }}</td>
                                        <td style="padding: 0.75rem 0.5rem; text-align: right; color: #374151; font-size: 0.875rem;">R$ {{ number_format($seller['sales_total'], 2, ',', '.') }}</td>
                                        <td style="padding: 0.75rem 0.5rem; text-align: right; font-weight: 600; color: #16a34a; font-size: 0.875rem;">R$ {{ number_format($seller['commission_total'], 2, ',', '.') }}</td>
                                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: {{ $seller['balance'] > 0 ? '#ca8a04' : '#6b7280' }}; font-size: 0.875rem;">R$ {{ number_format($seller['balance'], 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" style="padding: 2rem; text-align: center; color: #6b7280;">Nenhuma comissão no período.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-weight: 600; color: #111827;">Por Categoria de Produto</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Categoria</th>
                                    <th style="padding: 0.75rem 0.5rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                    <th style="padding: 0.75rem 0.5rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Faturamento</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Comissão</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report['by_category'] as $cat)
                                    <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                        <td style="padding: 0.75rem 1rem; font-weight: 500; color: #111827; font-size: 0.875rem;">{{ $cat['label'] }}</td>
                                        <td style="padding: 0.75rem 0.5rem; text-align: center; color: #374151; font-size: 0.875rem;">{{ $cat['quantity'] }}</td>
                                        <td style="padding: 0.75rem 0.5rem; text-align: right; color: #374151; font-size: 0.875rem;">R$ {{ number_format($cat['revenue'], 2, ',', '.') }}</td>
                                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #16a34a; font-size: 0.875rem;">R$ {{ number_format($cat['commission'], 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" style="padding: 2rem; text-align: center; color: #6b7280;">Nenhum dado no período.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: repeat(2, 1fr) !important; }
            div[style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: 1fr !important; }
            form[style*="display: flex"] { flex-direction: column !important; }
        }
    </style>
</x-app-layout>
