<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Painel Financeiro</h1>
                    <p class="text-sm text-gray-500">Visão geral das finanças da empresa</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('finance.payables') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors text-sm">
                        Contas a Pagar
                    </a>
                    <a href="{{ route('finance.receivables') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors text-sm">
                        Contas a Receber
                    </a>
                </div>
            </div>

            <!-- Cards Principais -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                    <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 600; opacity: 0.7; letter-spacing: 0.05em;">Saldo Total</div>
                    <div style="font-size: 1.5rem; font-weight: 800; margin-top: 0.25rem;">R$ {{ number_format($totalBalance, 2, ',', '.') }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.5; margin-top: 0.25rem;">{{ $accounts->count() }} {{ $accounts->count() === 1 ? 'carteira' : 'carteiras' }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
                    <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 600; color: #6b7280; letter-spacing: 0.05em;">Receitas do Mês</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #16a34a; margin-top: 0.25rem;">R$ {{ number_format($monthIncome, 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
                    <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 600; color: #6b7280; letter-spacing: 0.05em;">Despesas do Mês</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #dc2626; margin-top: 0.25rem;">R$ {{ number_format($monthExpense, 2, ',', '.') }}</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid {{ $monthProfit >= 0 ? '#bbf7d0' : '#fecaca' }};">
                    <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 600; color: #6b7280; letter-spacing: 0.05em;">Lucro do Mês</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: {{ $monthProfit >= 0 ? '#16a34a' : '#dc2626' }}; margin-top: 0.25rem;">R$ {{ number_format($monthProfit, 2, ',', '.') }}</div>
                </div>
            </div>

            <!-- Vendas do Mês -->
            @if($salesData['salesCount'] > 0)
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span style="font-size: 0.9375rem; font-weight: 600; color: #111827;">Vendas do Mês</span>
                        <span style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #f3f4f6; color: #6b7280; font-weight: 600;">{{ $salesData['salesCount'] }} {{ $salesData['salesCount'] === 1 ? 'venda' : 'vendas' }}</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <div style="font-size: 0.625rem; text-transform: uppercase; font-weight: 600; color: #6b7280; letter-spacing: 0.05em;">Faturamento</div>
                            <div style="font-size: 1.125rem; font-weight: 800; color: #111827;">R$ {{ number_format($salesData['salesRevenue'], 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.625rem; text-transform: uppercase; font-weight: 600; color: #6b7280; letter-spacing: 0.05em;">Custo (CMV)</div>
                            <div style="font-size: 1.125rem; font-weight: 800; color: #dc2626;">R$ {{ number_format($salesData['salesCost'], 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.625rem; text-transform: uppercase; font-weight: 600; color: #6b7280; letter-spacing: 0.05em;">Lucro das Vendas</div>
                            <div style="font-size: 1.125rem; font-weight: 800; color: {{ $salesData['salesProfit'] >= 0 ? '#16a34a' : '#dc2626' }};">R$ {{ number_format($salesData['salesProfit'], 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.625rem; text-transform: uppercase; font-weight: 600; color: #6b7280; letter-spacing: 0.05em;">Margem</div>
                            <div style="font-size: 1.125rem; font-weight: 800; color: {{ $salesData['salesMargin'] >= 0 ? '#16a34a' : '#dc2626' }};">{{ $salesData['salesMargin'] }}%</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Carteiras -->
            @if($accounts->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
                    @foreach($accounts as $account)
                        <a href="{{ route('finance.accounts', ['account_id' => $account->id]) }}" 
                           style="background: white; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem; text-decoration: none; transition: box-shadow 0.15s;"
                           onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; background: {{ $account->color }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $account->type->icon() }}"/>
                                </svg>
                            </div>
                            <div style="min-width: 0;">
                                <div style="font-size: 0.75rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $account->name }}</div>
                                <div style="font-size: 1rem; font-weight: 700; color: {{ (float)$account->current_balance >= 0 ? '#111827' : '#dc2626' }};">{{ $account->formatted_balance }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Gráfico Receitas vs Despesas -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb;">
                        <h2 style="font-size: 0.9375rem; font-weight: 600; color: #111827;">Receitas vs Despesas (7 dias)</h2>
                    </div>
                    <div style="padding: 1.25rem;">
                        <canvas id="financeChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Contas Vencendo -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 0.9375rem; font-weight: 600; color: #111827;">Próximos Vencimentos</h2>
                        @if($dueSoon->count() > 0)
                            <span style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #fef2f2; color: #dc2626; font-weight: 600;">{{ $dueSoon->count() }}</span>
                        @endif
                    </div>
                    <div style="max-height: 300px; overflow-y: auto;">
                        @forelse($dueSoon as $item)
                            <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $item->description }}</div>
                                    <div style="font-size: 0.6875rem; color: {{ $item->due_date->isPast() ? '#dc2626' : '#d97706' }};">
                                        {{ $item->due_date->format('d/m/Y') }}
                                        @if($item->due_date->isPast()) — Vencido @endif
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.875rem; font-weight: 700; color: {{ $item->type->value === 'expense' ? '#dc2626' : '#16a34a' }};">
                                        {{ $item->formatted_amount_plain }}
                                    </div>
                                    <div style="font-size: 0.625rem; padding: 0.0625rem 0.375rem; border-radius: 9999px; display: inline-block; background: {{ $item->category->color ?? '#6b7280' }}20; color: {{ $item->category->color ?? '#6b7280' }};">
                                        {{ $item->category->name ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="padding: 2rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">
                                Nenhuma conta pendente nos próximos 7 dias.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Últimas Movimentações -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-top: 1.5rem;">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="font-size: 0.9375rem; font-weight: 600; color: #111827;">Últimas Movimentações</h2>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb;">
                                <th style="padding: 0.625rem 1.25rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                <th style="padding: 0.625rem 1rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Descrição</th>
                                <th style="padding: 0.625rem 1rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Categoria</th>
                                <th style="padding: 0.625rem 1rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Carteira</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 0.625rem 1.25rem; text-align: right; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 0.625rem 1.25rem; font-size: 0.8125rem; color: #6b7280;">
                                        {{ $tx->paid_at ? $tx->paid_at->format('d/m H:i') : $tx->due_date->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 0.625rem 1rem; font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ Str::limit($tx->description, 40) }}</td>
                                    <td style="padding: 0.625rem 1rem;">
                                        <span style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: {{ $tx->category->color ?? '#6b7280' }}20; color: {{ $tx->category->color ?? '#6b7280' }}; font-weight: 500;">
                                            {{ $tx->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.625rem 1rem; font-size: 0.75rem; color: #6b7280;">{{ $tx->account->name ?? '-' }}</td>
                                    <td style="padding: 0.625rem 1rem; text-align: center;">
                                        <span style="font-size: 0.625rem; padding: 0.125rem 0.375rem; border-radius: 9999px; font-weight: 600; background: {{ $tx->status->bgColor() }}; color: {{ $tx->status->color() }};">
                                            {{ $tx->status->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.625rem 1.25rem; text-align: right; font-size: 0.875rem; font-weight: 700; color: {{ $tx->type->color() }};">
                                        {{ $tx->formatted_amount }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 2rem; text-align: center; color: #9ca3af;">Nenhuma movimentação registrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('financeChart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Receitas',
                            data: @json($chartData['incomes']),
                            backgroundColor: 'rgba(22, 163, 74, 0.7)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Despesas',
                            data: @json($chartData['expenses']),
                            backgroundColor: 'rgba(220, 38, 38, 0.7)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16, font: { size: 11 } } }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(v) { return 'R$ ' + v.toLocaleString('pt-BR'); },
                                font: { size: 10 }
                            },
                            grid: { color: '#f3f4f6' }
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
