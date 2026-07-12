{{-- Dashboard Inteligente de Estagiárias + Resumo Executivo --}}
<div x-data="{
        open: false,
        hideValues: false,
        tab: '{{ auth()->user()->isAdminGeral() ? 'gerencial' : 'overview' }}',
        selectedIntern: null,
        internStats: @js($internStats),
        execData: @js($executiveSummary ?? []),
        execLoading: false,
        execPeriod: 'month',
        execSearch: '',
        execMixChart: null,
        execTypeChart: null,
        execPayChart: null,
        get selectedInternData() {
            if (!this.selectedIntern) return null;
            return this.internStats.interns.find(i => i.id === this.selectedIntern) || null;
        },
        get combinedSales() { return this.internStats.combined?.total_sales || 0; },
        formatCurrency(val) {
            return 'R$ ' + Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        },
        async switchExecPeriod(period) {
            if (this.execPeriod === period || this.execLoading) return;
            this.execPeriod = period;
            this.execLoading = true;
            try {
                const res = await fetch(`{{ route('executive-summary.data') }}?period=${period}`);
                if (res.ok) this.execData = await res.json();
            } catch(e) { console.error(e); }
            this.execLoading = false;
            this.$nextTick(() => this.renderExecCharts());
        },
        renderExecCharts() {
            this.renderExecMixChart();
            this.renderExecTypeChart();
            this.renderExecPayChart();
        },
        renderExecMixChart() {
            const ctx = document.getElementById('execMixCanvas');
            if (!ctx) return;
            if (this.execMixChart) this.execMixChart.destroy();
            const g = this.execData?.gerencial;
            if (!g) return;
            this.execMixChart = new Chart(ctx, { type: 'doughnut', data: { labels: ['Novos', 'Seminovos'], datasets: [{ data: [g.iphone_new?.qty||0, g.iphone_used?.qty||0], backgroundColor: ['#6366f1','#f59e0b'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 11 } } } } } });
        },
        renderExecTypeChart() {
            const ctx = document.getElementById('execTypeCanvas');
            if (!ctx) return;
            if (this.execTypeChart) this.execTypeChart.destroy();
            const g = this.execData?.gerencial;
            if (!g) return;
            this.execTypeChart = new Chart(ctx, { type: 'doughnut', data: { labels: ['Cliente Final', 'Repasse'], datasets: [{ data: [g.cliente_final?.count||0, g.repasse?.count||0], backgroundColor: ['#10b981','#8b5cf6'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 11 } } } } } });
        },
        renderExecPayChart() {
            const ctx = document.getElementById('execPayCanvas');
            if (!ctx) return;
            if (this.execPayChart) this.execPayChart.destroy();
            const methods = this.execData?.gerencial?.payment_methods || [];
            if (!methods.length) return;
            this.execPayChart = new Chart(ctx, { type: 'bar', data: { labels: methods.map(m=>m.label), datasets: [{ label: 'Total (R$)', data: methods.map(m=>m.total), backgroundColor: ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4'], borderRadius: 4 }] }, options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { color: '#64748b', callback: v => 'R$ '+Number(v).toLocaleString('pt-BR') }, grid: { color: 'rgba(255,255,255,0.03)' } }, y: { ticks: { color: '#94a3b8' }, grid: { display: false } } } } });
        },
        get filteredExecSales() {
            const sales = this.execData?.detalhado || [];
            if (!this.execSearch) return sales;
            const q = this.execSearch.toLowerCase();
            return sales.filter(s => s.seller?.toLowerCase().includes(q) || s.customer?.toLowerCase().includes(q) || s.product?.toLowerCase().includes(q) || s.type?.toLowerCase().includes(q));
        },
        get execTotals() {
            const items = this.filteredExecSales;
            return { value: items.reduce((s,i)=>s+(i.value||0),0), cost: items.reduce((s,i)=>s+(i.cost||0),0), profit: items.reduce((s,i)=>s+(i.profit||0),0) };
        }
     }"
     x-on:open-month-summary.window="open = true; $nextTick(() => { if(typeof internChartInstance !== 'undefined' && internChartInstance) { internChartInstance.destroy(); internChartInstance = null; } if(tab === 'overview') initInternChart(); if(tab === 'gerencial') renderExecCharts(); })"
     x-on:keydown.escape.window="open = false"
     x-show="open" x-cloak
     style="position: fixed; inset: 0; z-index: 9999;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div style="position: absolute; inset: 0; background: linear-gradient(170deg, #050505 0%, #0a0f1a 30%, #0c1220 60%, #080d15 100%); display: flex; flex-direction: column; height: 100vh; overflow-y: auto;">

        {{-- Ambient glows --}}
        <div style="position: fixed; top: -15%; right: -10%; width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(99,102,241,0.06) 0%, transparent 65%); pointer-events: none;"></div>
        <div style="position: fixed; bottom: -10%; left: -10%; width: 40vw; height: 40vw; background: radial-gradient(circle, rgba(16,185,129,0.05) 0%, transparent 65%); pointer-events: none;"></div>

        {{-- Header --}}
        <div style="position: sticky; top: 0; z-index: 50; backdrop-filter: blur(12px); background: rgba(5,5,5,0.8); border-bottom: 1px solid rgba(255,255,255,0.05);">
            <div style="max-width: 1400px; margin: 0 auto; padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="{{ asset('images/logodg.png') }}" alt="DG Store" style="height: 32px; opacity: 0.9;">
                    <div>
                        <h1 style="font-size: 1.125rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.025em;">Resumo Executivo</h1>
                        <p style="font-size: 0.75rem; color: #64748b;">{{ ucfirst($monthSummary['month_label']) }}</p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    {{-- Tabs --}}
                    <div style="display: flex; gap: 2px; background: rgba(255,255,255,0.04); border-radius: 10px; padding: 3px; border: 1px solid rgba(255,255,255,0.06);">
                        @if(auth()->user()->isAdminGeral())
                        <button @click="tab = 'gerencial'; $nextTick(() => renderExecCharts())"
                                :style="tab === 'gerencial' ? 'background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.1)); color: #6ee7b7; box-shadow: 0 0 12px rgba(16,185,129,0.1);' : 'color: #64748b;'"
                                style="padding: 0.4rem 0.875rem; border-radius: 7px; font-size: 0.75rem; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Gerencial
                        </button>
                        <button @click="tab = 'detalhado'"
                                :style="tab === 'detalhado' ? 'background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(245,158,11,0.1)); color: #fcd34d; box-shadow: 0 0 12px rgba(245,158,11,0.1);' : 'color: #64748b;'"
                                style="padding: 0.4rem 0.875rem; border-radius: 7px; font-size: 0.75rem; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Detalhado
                        </button>
                        @endif
                        <button @click="tab = 'overview'"
                                :style="tab === 'overview' ? 'background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(99,102,241,0.1)); color: #a5b4fc; box-shadow: 0 0 12px rgba(99,102,241,0.1);' : 'color: #64748b;'"
                                style="padding: 0.4rem 0.875rem; border-radius: 7px; font-size: 0.75rem; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Estagiárias
                        </button>
                        <button @click="tab = 'individual'"
                                :style="tab === 'individual' ? 'background: linear-gradient(135deg, rgba(139,92,246,0.2), rgba(139,92,246,0.1)); color: #c4b5fd; box-shadow: 0 0 12px rgba(139,92,246,0.1);' : 'color: #64748b;'"
                                style="padding: 0.4rem 0.875rem; border-radius: 7px; font-size: 0.75rem; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Individual
                        </button>
                    </div>

                    {{-- Hide values --}}
                    <button @click="hideValues = !hideValues"
                            style="padding: 0.5rem; border-radius: 8px; color: #94a3b8; transition: color 0.2s;"
                            :style="hideValues ? 'color: #6366f1;' : ''">
                        <svg x-show="!hideValues" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="hideValues" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                    </button>

                    {{-- Close --}}
                    <button @click="open = false" style="padding: 0.5rem; border-radius: 8px; color: #94a3b8;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div style="max-width: 1400px; margin: 0 auto; padding: 1.5rem; width: 100%; flex: 1;">

            {{-- ======================== VISÃO GERAL TAB ======================== --}}
            <div x-show="tab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">

                @if(empty($internStats['interns']))
                    <div style="text-align: center; padding: 4rem 2rem;">
                        <p style="color: #64748b; font-size: 1rem;">Nenhuma estagiária ativa encontrada.</p>
                    </div>
                @else
                    {{-- KPI Cards Row --}}
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                        {{-- Total Vendas --}}
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
                            <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Vendas Combinadas</p>
                            <p style="font-size: 1.75rem; font-weight: 700; color: #f1f5f9;">{{ $internStats['combined']['total_sales'] }}</p>
                            <p style="font-size: 0.6875rem; color: #475569; margin-top: 0.25rem;">no mês</p>
                        </div>

                        {{-- Faturamento --}}
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
                            <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Faturamento Gerado</p>
                            <p style="font-size: 1.75rem; font-weight: 700; color: #f1f5f9;">
                                <span x-show="!hideValues">R$ {{ number_format($internStats['combined']['total_revenue'], 2, ',', '.') }}</span>
                                <span x-show="hideValues" x-cloak>R$ &bull;&bull;&bull;&bull;</span>
                            </p>
                        </div>

                        {{-- Lucro --}}
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
                            <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Lucro Gerado</p>
                            <p style="font-size: 1.75rem; font-weight: 700; color: #10b981;">
                                <span x-show="!hideValues">R$ {{ number_format($internStats['combined']['total_profit'], 2, ',', '.') }}</span>
                                <span x-show="hideValues" x-cloak>R$ &bull;&bull;&bull;&bull;</span>
                            </p>
                        </div>

                        {{-- Comissão Total --}}
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
                            <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Comissões Pagas</p>
                            <p style="font-size: 1.75rem; font-weight: 700; color: #f59e0b;">
                                <span x-show="!hideValues">R$ {{ number_format($internStats['combined']['total_commission'], 2, ',', '.') }}</span>
                                <span x-show="hideValues" x-cloak>R$ &bull;&bull;&bull;&bull;</span>
                            </p>
                        </div>
                    </div>

                    {{-- Metas Section --}}
                    <div class="intern-grid-2col" style="display: grid; gap: 1rem; margin-bottom: 1.5rem;">
                        @include('partials._intern-goals-monthly')
                        @include('partials._intern-goals-weekly')
                    </div>

                    {{-- Bottom row: Ranking + Chart --}}
                    <div class="intern-grid-2col" style="display: grid; gap: 1rem;">
                        @include('partials._intern-ranking')
                        @include('partials._intern-daily-chart')
                    </div>
                @endif
            </div>

            {{-- ======================== INDIVIDUAL TAB ======================== --}}
            <div x-show="tab === 'individual'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">

                @if(empty($internStats['interns']))
                    <div style="text-align: center; padding: 4rem 2rem;">
                        <p style="color: #64748b; font-size: 1rem;">Nenhuma estagiária ativa encontrada.</p>
                    </div>
                @else
                    {{-- Intern Selector --}}
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            @foreach($internStats['interns'] as $intern)
                                <button @click="selectedIntern = '{{ $intern['id'] }}'"
                                        :style="selectedIntern === '{{ $intern['id'] }}' ? 'background: rgba(99,102,241,0.15); border-color: rgba(99,102,241,0.4); color: #a5b4fc;' : 'background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.08); color: #94a3b8;'"
                                        style="padding: 0.625rem 1.25rem; border-radius: 10px; border: 1px solid; font-size: 0.8125rem; font-weight: 500; transition: all 0.2s;">
                                    {{ $intern['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- No selection state --}}
                    <div x-show="!selectedIntern" style="text-align: center; padding: 3rem 2rem;">
                        <svg style="width: 48px; height: 48px; color: #334155; margin: 0 auto 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p style="color: #64748b; font-size: 0.875rem;">Selecione uma estagiária acima para ver os detalhes</p>
                    </div>

                    {{-- Individual Stats --}}
                    <div x-show="selectedIntern && selectedInternData" x-transition>
                        {{-- Individual KPIs --}}
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem; text-align: center;">
                                <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Vendas</p>
                                <p style="font-size: 2rem; font-weight: 700; color: #f1f5f9;" x-text="selectedInternData?.sales_count || 0"></p>
                            </div>
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem; text-align: center;">
                                <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Faturamento</p>
                                <p style="font-size: 1.5rem; font-weight: 700; color: #f1f5f9;">
                                    <span x-show="!hideValues" x-text="formatCurrency(selectedInternData?.total_revenue || 0)"></span>
                                    <span x-show="hideValues" x-cloak>R$ &bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem; text-align: center;">
                                <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Lucro Gerado</p>
                                <p style="font-size: 1.5rem; font-weight: 700; color: #10b981;">
                                    <span x-show="!hideValues" x-text="formatCurrency(selectedInternData?.total_profit || 0)"></span>
                                    <span x-show="hideValues" x-cloak>R$ &bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem; text-align: center;">
                                <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Comissão</p>
                                <p style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">
                                    <span x-show="!hideValues" x-text="formatCurrency(selectedInternData?.commission_earned || 0)"></span>
                                    <span x-show="hideValues" x-cloak>R$ &bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem; text-align: center;">
                                <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Ticket Médio</p>
                                <p style="font-size: 1.5rem; font-weight: 700; color: #8b5cf6;">
                                    <span x-show="!hideValues" x-text="formatCurrency(selectedInternData?.avg_ticket || 0)"></span>
                                    <span x-show="hideValues" x-cloak>R$ &bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem; text-align: center;">
                                <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Vendas na Semana</p>
                                <p style="font-size: 2rem; font-weight: 700; color: #06b6d4;" x-text="selectedInternData?.sales_this_week || 0"></p>
                            </div>
                        </div>

                        {{-- Contribution & Progress --}}
                        <div class="intern-grid-2col" style="display: grid; gap: 1rem;">
                            {{-- Contribution --}}
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
                                <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">Contribuição no Mês</h3>

                                <div style="margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.375rem;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">% das vendas do time</span>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: #a5b4fc;"
                                              x-text="internStats.combined.total_sales > 0 ? Math.round((selectedInternData?.sales_count / internStats.combined.total_sales) * 100) + '%' : '0%'"></span>
                                    </div>
                                    <div style="height: 6px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden;">
                                        <div style="height: 100%; border-radius: 3px; background: linear-gradient(90deg, #6366f1, #8b5cf6); transition: width 0.5s ease;"
                                             :style="'width: ' + (internStats.combined.total_sales > 0 ? Math.round((selectedInternData?.sales_count / internStats.combined.total_sales) * 100) : 0) + '%'"></div>
                                    </div>
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.375rem;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">% do faturamento</span>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: #34d399;"
                                              x-text="internStats.combined.total_revenue > 0 ? Math.round((selectedInternData?.total_revenue / internStats.combined.total_revenue) * 100) + '%' : '0%'"></span>
                                    </div>
                                    <div style="height: 6px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden;">
                                        <div style="height: 100%; border-radius: 3px; background: linear-gradient(90deg, #10b981, #34d399); transition: width 0.5s ease;"
                                             :style="'width: ' + (internStats.combined.total_revenue > 0 ? Math.round((selectedInternData?.total_revenue / internStats.combined.total_revenue) * 100) : 0) + '%'"></div>
                                    </div>
                                </div>

                                <div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.375rem;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">% do lucro</span>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: #fbbf24;"
                                              x-text="internStats.combined.total_profit > 0 ? Math.round((selectedInternData?.total_profit / internStats.combined.total_profit) * 100) + '%' : '0%'"></span>
                                    </div>
                                    <div style="height: 6px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden;">
                                        <div style="height: 100%; border-radius: 3px; background: linear-gradient(90deg, #f59e0b, #fbbf24); transition: width 0.5s ease;"
                                             :style="'width: ' + (internStats.combined.total_profit > 0 ? Math.round((selectedInternData?.total_profit / internStats.combined.total_profit) * 100) : 0) + '%'"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Individual Progress toward goals --}}
                            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
                                <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">Progresso nas Metas</h3>
                                <p style="font-size: 0.6875rem; color: #64748b; margin-bottom: 1rem;">Contribuição individual para as metas do time</p>

                                @foreach($internStats['goals']['monthly'] as $goal)
                                    <div style="margin-bottom: 1rem;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.375rem;">
                                            <span style="font-size: 0.75rem; color: #94a3b8;">Meta {{ $goal['target'] }} vendas</span>
                                            <span style="font-size: 0.75rem; font-weight: 600; color: #e2e8f0;"
                                                  x-text="selectedInternData?.sales_count + ' contribuídas'"></span>
                                        </div>
                                        <div style="height: 6px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden;">
                                            <div style="height: 100%; border-radius: 3px; transition: width 0.5s ease;"
                                                 :style="'width: ' + Math.min(100, Math.round((selectedInternData?.sales_count / {{ $goal['target'] }}) * {{ count($internStats['interns']) }} * 100)) + '%; background: linear-gradient(90deg, #6366f1, #8b5cf6);'"></div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Week goal --}}
                                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.375rem;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">Meta Semanal (10 até Quinta)</span>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: #06b6d4;"
                                              x-text="selectedInternData?.sales_this_week + ' vendas'"></span>
                                    </div>
                                    <div style="height: 6px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden;">
                                        <div style="height: 100%; border-radius: 3px; background: linear-gradient(90deg, #06b6d4, #22d3ee); transition: width 0.5s ease;"
                                             :style="'width: ' + Math.min(100, Math.round((selectedInternData?.sales_this_week / 10) * {{ count($internStats['interns']) }} * 100)) + '%'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if(auth()->user()->isAdminGeral())
            {{-- ======================== GERENCIAL TAB ======================== --}}
            <div x-show="tab === 'gerencial'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                @include('partials._exec-tab-gerencial')
            </div>

            {{-- ======================== DETALHADO TAB ======================== --}}
            <div x-show="tab === 'detalhado'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                @include('partials._exec-tab-detalhado')
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div style="text-align: center; padding: 1rem; border-top: 1px solid rgba(255,255,255,0.03);">
            <p style="font-size: 0.6875rem; color: #334155;">DG Store · Resumo Executivo · {{ ucfirst($monthSummary['month_label']) }}</p>
        </div>
    </div>
</div>

<style>
.intern-grid-2col { grid-template-columns: 1fr 1fr; }
@media (max-width: 768px) {
    .intern-grid-2col { grid-template-columns: 1fr !important; }
}
</style>

<script>
let internChartInstance = null;

function initInternChart() {
    const ctx = document.getElementById('internDailyChart');
    if (!ctx) return;

    if (internChartInstance) {
        internChartInstance.destroy();
        internChartInstance = null;
    }

    const chartData = @json($internStats['daily_chart'] ?? ['labels' => [], 'data' => []]);
    if (!chartData.labels || chartData.labels.length === 0) return;

    internChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Vendas/dia',
                data: chartData.data,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#6366f1',
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.9)',
                    titleColor: '#f1f5f9',
                    bodyColor: '#cbd5e1',
                    borderColor: 'rgba(99,102,241,0.3)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.03)' },
                    ticks: { color: '#475569', font: { size: 10 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.03)' },
                    ticks: { color: '#475569', font: { size: 10 }, stepSize: 1 }
                }
            }
        }
    });
}
</script>
