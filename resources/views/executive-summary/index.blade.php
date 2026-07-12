<x-app-layout>
    <x-slot name="title">Resumo Executivo</x-slot>

    <div class="py-6" x-data="executiveSummary(@json($initialData))">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Resumo Executivo de Vendas</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;" x-text="data.period_label"></p>
                </div>

                {{-- Period Pills --}}
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <template x-for="p in periods" :key="p.value">
                        <button
                            @click="switchPeriod(p.value)"
                            :style="activePeriod === p.value
                                ? 'padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; background: #4f46e5; color: white; border: none; cursor: pointer; transition: all 0.15s;'
                                : 'padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 500; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; cursor: pointer; transition: all 0.15s;'"
                            x-text="p.label"
                        ></button>
                    </template>
                </div>
            </div>

            {{-- Loading Overlay --}}
            <div x-show="loading" x-cloak style="display: flex; align-items: center; justify-content: center; padding: 3rem;">
                <svg style="width: 2rem; height: 2rem; color: #6366f1; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                </svg>
                <span style="margin-left: 0.75rem; color: #6b7280; font-size: 0.875rem;">Carregando dados...</span>
            </div>

            {{-- Tabs --}}
            <div x-show="!loading" x-cloak>
                {{-- Tab Navigation --}}
                <div style="display: flex; gap: 0; border-bottom: 2px solid #e5e7eb; margin-bottom: 1.5rem;">
                    <template x-for="t in tabs" :key="t.id">
                        <button
                            @click="activeTab = t.id"
                            :style="activeTab === t.id
                                ? 'padding: 0.75rem 1.25rem; font-size: 0.875rem; font-weight: 600; color: #4f46e5; border-bottom: 2px solid #4f46e5; margin-bottom: -2px; background: transparent; border-top: none; border-left: none; border-right: none; cursor: pointer;'
                                : 'padding: 0.75rem 1.25rem; font-size: 0.875rem; font-weight: 500; color: #6b7280; border: none; background: transparent; cursor: pointer;'"
                            x-text="t.label"
                        ></button>
                    </template>
                </div>

                {{-- Tab Contents --}}
                <div x-show="activeTab === 'estagiarias'">
                    @include('executive-summary.partials.tab-estagiarias')
                </div>
                <div x-show="activeTab === 'gerencial'">
                    @include('executive-summary.partials.tab-gerencial')
                </div>
                <div x-show="activeTab === 'detalhado'">
                    @include('executive-summary.partials.tab-detalhado')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
    function executiveSummary(initialData) {
        return {
            data: initialData,
            activePeriod: initialData.period || 'month',
            activeTab: 'gerencial',
            loading: false,
            searchQuery: '',
            periods: [
                { value: 'today', label: 'Hoje' },
                { value: 'week', label: 'Semana' },
                { value: 'month', label: 'Mês' },
                { value: 'year', label: 'Ano' },
            ],
            tabs: [
                { id: 'estagiarias', label: 'Dashboard Estagiárias' },
                { id: 'gerencial', label: 'Dashboard Gerencial' },
                { id: 'detalhado', label: 'Detalhado' },
            ],

            // Charts
            dailyChart: null,
            mixChart: null,
            typeChart: null,
            paymentChart: null,

            async switchPeriod(period) {
                if (this.activePeriod === period || this.loading) return;
                this.activePeriod = period;
                this.loading = true;

                try {
                    const res = await fetch(`{{ route('executive-summary.data') }}?period=${period}`);
                    if (!res.ok) throw new Error('Erro ao carregar dados');
                    this.data = await res.json();
                    this.$nextTick(() => this.renderCharts());
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            init() {
                this.$nextTick(() => this.renderCharts());
            },

            renderCharts() {
                this.renderDailyChart();
                this.renderMixChart();
                this.renderTypeChart();
                this.renderPaymentChart();
            },

            renderDailyChart() {
                const ctx = document.getElementById('dailyChartCanvas');
                if (!ctx) return;
                if (this.dailyChart) this.dailyChart.destroy();

                const chart = this.data.estagiarias?.daily_chart;
                if (!chart || !chart.labels?.length) return;

                this.dailyChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chart.labels,
                        datasets: [{
                            label: 'Vendas',
                            data: chart.data,
                            backgroundColor: 'rgba(99, 102, 241, 0.7)',
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            },

            renderMixChart() {
                const ctx = document.getElementById('mixChartCanvas');
                if (!ctx) return;
                if (this.mixChart) this.mixChart.destroy();

                const g = this.data.gerencial;
                if (!g) return;

                this.mixChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Novos', 'Seminovos'],
                        datasets: [{
                            data: [g.iphone_new?.qty || 0, g.iphone_used?.qty || 0],
                            backgroundColor: ['#6366f1', '#f59e0b'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            },

            renderTypeChart() {
                const ctx = document.getElementById('typeChartCanvas');
                if (!ctx) return;
                if (this.typeChart) this.typeChart.destroy();

                const g = this.data.gerencial;
                if (!g) return;

                this.typeChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Cliente Final', 'Repasse'],
                        datasets: [{
                            data: [g.cliente_final?.count || 0, g.repasse?.count || 0],
                            backgroundColor: ['#10b981', '#8b5cf6'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            },

            renderPaymentChart() {
                const ctx = document.getElementById('paymentChartCanvas');
                if (!ctx) return;
                if (this.paymentChart) this.paymentChart.destroy();

                const methods = this.data.gerencial?.payment_methods || [];
                if (!methods.length) return;

                this.paymentChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: methods.map(m => m.label),
                        datasets: [{
                            label: 'Total (R$)',
                            data: methods.map(m => m.total),
                            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, ticks: { callback: v => 'R$ ' + Number(v).toLocaleString('pt-BR') } }
                        }
                    }
                });
            },

            money(v) {
                return 'R$ ' + Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            get filteredSales() {
                const sales = this.data.detalhado || [];
                if (!this.searchQuery) return sales;
                const q = this.searchQuery.toLowerCase();
                return sales.filter(s =>
                    s.seller?.toLowerCase().includes(q) ||
                    s.customer?.toLowerCase().includes(q) ||
                    s.product?.toLowerCase().includes(q) ||
                    s.type?.toLowerCase().includes(q) ||
                    s.payment?.toLowerCase().includes(q)
                );
            },

            get detailedTotals() {
                const items = this.filteredSales;
                return {
                    value: items.reduce((sum, s) => sum + (s.value || 0), 0),
                    cost: items.reduce((sum, s) => sum + (s.cost || 0), 0),
                    profit: items.reduce((sum, s) => sum + (s.profit || 0), 0),
                };
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
