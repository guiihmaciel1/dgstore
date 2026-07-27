@php
    $isAdmin = auth()->user()->isAdminGeral();
@endphp

@if($isAdmin)
@php
    $dollarRate = \App\Domain\System\Models\SystemSetting::get('dollar_rate');
    $cpRates = \App\Domain\System\Models\DollarCpRate::lastThirty();
    $cpLatest = $cpRates->first();
    $cpPrevious = $cpRates->skip(1)->first();
    $cpHistory = app(\App\Domain\System\Services\ComprasParaguaiDollarService::class)->getCachedHistory();
    $currencies = $cpHistory['currencies'] ?? [];
    $historyTable = $cpHistory['history'] ?? [];
    $historyUpdatedAt = $cpHistory['updated_at'] ?? null;

    $chartRates = $cpRates->reverse()->values();
@endphp

<div x-data="dollarRateModal()" @open-dollar-modal.window="open = true" @keydown.escape.window="open = false" x-cloak>
    {{-- Backdrop --}}
    <div x-show="open" x-transition.opacity.duration.200ms @click="open = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60]"></div>

    {{-- Modal Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:w-full sm:max-w-2xl sm:max-h-[90vh] bg-surface-overlay border border-border rounded-2xl shadow-2xl z-[61] flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-border/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">Acompanhamento do Dólar</h2>
                    <p class="text-[11px] text-dg-400">Cotações na fronteira &middot; Compras Paraguai</p>
                </div>
            </div>
            <button @click="open = false" class="p-1.5 rounded-lg text-dg-400 hover:text-white hover:bg-surface-elevated transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Scrollable content --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-5" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">

            {{-- Currency Cards --}}
            <div class="grid grid-cols-3 gap-3">
                {{-- BRL --}}
                <div class="rounded-xl border p-3.5 {{ isset($currencies['brl']) ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-border bg-surface-elevated/30' }}">
                    <div class="flex items-center gap-1.5 mb-2">
                        <span class="text-lg">🇧🇷</span>
                        <span class="text-[10px] font-semibold text-dg-400 uppercase tracking-wide">Dólar → Real</span>
                    </div>
                    <div class="text-xl font-bold text-white">
                        R$ {{ isset($currencies['brl']) ? number_format($currencies['brl']['value'], 2, ',', '.') : ($cpLatest ? number_format((float) $cpLatest->rate, 2, ',', '.') : '--') }}
                    </div>
                    @if($cpLatest && $cpPrevious)
                        @php
                            $diff = (float) $cpLatest->rate - (float) $cpPrevious->rate;
                            $pct = (float) $cpPrevious->rate > 0 ? ($diff / (float) $cpPrevious->rate) * 100 : 0;
                        @endphp
                        <span class="text-xs font-semibold mt-1 inline-block {{ $diff > 0 ? 'text-red-400' : ($diff < 0 ? 'text-emerald-400' : 'text-dg-500') }}">
                            {{ $diff > 0 ? '▲' : ($diff < 0 ? '▼' : '–') }} {{ number_format(abs($pct), 2, ',', '.') }}%
                        </span>
                    @endif
                </div>

                {{-- PYG --}}
                <div class="rounded-xl border border-border bg-surface-elevated/30 p-3.5">
                    <div class="flex items-center gap-1.5 mb-2">
                        <span class="text-lg">🇵🇾</span>
                        <span class="text-[10px] font-semibold text-dg-400 uppercase tracking-wide">Dólar → Guarani</span>
                    </div>
                    <div class="text-xl font-bold text-white">
                        ₲ {{ isset($currencies['pyg']) ? number_format($currencies['pyg']['value'], 0, ',', '.') : '--' }}
                    </div>
                </div>

                {{-- ARS --}}
                <div class="rounded-xl border border-border bg-surface-elevated/30 p-3.5">
                    <div class="flex items-center gap-1.5 mb-2">
                        <span class="text-lg">🇦🇷</span>
                        <span class="text-[10px] font-semibold text-dg-400 uppercase tracking-wide">Dólar → Peso</span>
                    </div>
                    <div class="text-xl font-bold text-white">
                        $ {{ isset($currencies['ars']) ? number_format($currencies['ars']['value'], 0, ',', '.') : '--' }}
                    </div>
                </div>
            </div>

            {{-- Chart Section --}}
            @if($chartRates->count() >= 2)
            <div class="rounded-xl border border-border bg-surface-elevated/30 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-white">Dólar para o Real</h3>
                    <div class="flex gap-1">
                        <button @click="setPeriod(7)" :class="period === 7 ? 'bg-emerald-500 text-white' : 'bg-surface-overlay text-dg-400 hover:text-white'" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">7D</button>
                        <button @click="setPeriod(30)" :class="period === 30 ? 'bg-emerald-500 text-white' : 'bg-surface-overlay text-dg-400 hover:text-white'" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">1M</button>
                        <button @click="setPeriod(90)" :class="period === 90 ? 'bg-emerald-500 text-white' : 'bg-surface-overlay text-dg-400 hover:text-white'" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">3M</button>
                    </div>
                </div>
                <div class="relative" style="height: 200px;">
                    <canvas id="dollarChart" class="w-full h-full"></canvas>
                </div>
                {{-- Min/Max labels --}}
                @php
                    $minRate = $chartRates->min('rate');
                    $maxRate = $chartRates->max('rate');
                @endphp
                <div class="flex items-center justify-between mt-2 text-[10px]">
                    <span class="text-emerald-400 font-semibold">Mínima: R$ {{ number_format((float) $minRate, 2, ',', '.') }}</span>
                    <span class="text-red-400 font-semibold">Máxima: R$ {{ number_format((float) $maxRate, 2, ',', '.') }}</span>
                </div>
            </div>
            @endif

            {{-- History Table --}}
            @if(!empty($historyTable))
            <div class="rounded-xl border border-border bg-surface-elevated/30 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-border/50">
                    <h3 class="text-sm font-semibold text-white">Últimos 12 dias</h3>
                    @if($historyUpdatedAt)
                        <span class="text-[10px] text-dg-500 bg-surface-overlay px-2 py-0.5 rounded-full">Atualizado em {{ $historyUpdatedAt }}</span>
                    @endif
                </div>
                <div class="divide-y divide-border/20">
                    @foreach($historyTable as $row)
                        <div class="flex items-center justify-between px-4 py-2.5 hover:bg-surface-elevated/30 transition-colors">
                            <span class="text-[13px] text-dg-300 w-32">{{ $row['date'] }}</span>
                            <span class="text-[13px] font-semibold text-white">R$ {{ number_format($row['rate'], 2, ',', '.') }}</span>
                            @php
                                $changeVal = floatval(str_replace(['+', '%', ','], ['', '', '.'], $row['change']));
                            @endphp
                            <span class="text-[12px] font-semibold w-16 text-right {{ $changeVal > 0 ? 'text-red-400' : ($changeVal < 0 ? 'text-emerald-400' : 'text-dg-500') }}">
                                {{ $row['change'] ?: '0%' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @elseif($cpRates->count() > 0)
            {{-- Fallback: tabela a partir dos dados internos --}}
            <div class="rounded-xl border border-border bg-surface-elevated/30 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-border/50">
                    <h3 class="text-sm font-semibold text-white">Histórico de Cotações</h3>
                    <span class="text-[10px] text-dg-500 bg-surface-overlay px-2 py-0.5 rounded-full">Dados internos</span>
                </div>
                <div class="divide-y divide-border/20">
                    @foreach($cpRates->take(12) as $index => $rateItem)
                        @php
                            $prev = $cpRates->get($index + 1);
                            $itemDiff = $prev ? (float) $rateItem->rate - (float) $prev->rate : 0;
                            $itemPct = ($prev && (float) $prev->rate > 0) ? ($itemDiff / (float) $prev->rate) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between px-4 py-2.5 hover:bg-surface-elevated/30 transition-colors">
                            <span class="text-[13px] text-dg-300 w-32">{{ $rateItem->fetched_at->format('D., d/m') }}</span>
                            <span class="text-[13px] font-semibold text-white">R$ {{ number_format((float) $rateItem->rate, 2, ',', '.') }}</span>
                            <span class="text-[12px] font-semibold w-16 text-right {{ $itemDiff > 0 ? 'text-red-400' : ($itemDiff < 0 ? 'text-emerald-400' : 'text-dg-500') }}">
                                {{ $itemDiff != 0 ? (($itemDiff > 0 ? '+' : '') . number_format($itemPct, 2, ',', '.') . '%') : '0%' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- DG Rate Editor --}}
            <div class="rounded-xl border border-border bg-surface-elevated/30 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-white">Cotação DG Store</h3>
                        <p class="text-[11px] text-dg-400 mt-0.5">Cotação manual usada nos cálculos de importação</p>
                    </div>
                    @if($dollarRate)
                        <span class="text-lg font-bold text-emerald-400">R$ {{ number_format((float) $dollarRate, 2, ',', '.') }}</span>
                    @else
                        <span class="text-sm font-semibold text-amber-400">Não informada</span>
                    @endif
                </div>
                <form @submit.prevent="saveDollarRate()" class="mt-3 flex gap-2">
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-dg-500 font-semibold">R$</span>
                        <input type="text" x-model="rateInput"
                               placeholder="{{ $dollarRate ? number_format((float) $dollarRate, 2, ',', '.') : ($cpLatest ? number_format((float) $cpLatest->rate, 2, ',', '.') : '5,45') }}"
                               class="w-full pl-8 pr-3 py-2 bg-surface-raised border border-border rounded-lg text-sm text-dg-100 font-semibold text-right focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 outline-none transition">
                    </div>
                    <button type="submit" :disabled="saving" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-500 transition disabled:opacity-50">
                        <span x-show="!saving">Salvar</span>
                        <span x-show="saving">...</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-3 border-t border-border/50 flex items-center justify-between bg-surface-elevated/20">
            <span class="text-[10px] text-dg-500">Fonte: <a href="https://www.comprasparaguai.com.br/historico-cotacao" target="_blank" rel="noopener noreferrer" class="text-dg-400 hover:text-white transition">comprasparaguai.com.br</a></span>
            @if($cpLatest)
                <span class="text-[10px] text-dg-500">Atualizado em {{ $cpLatest->fetched_at->format('d/m, H:i') }}</span>
            @endif
        </div>
    </div>
</div>

<script>
function dollarRateModal() {
    return {
        open: false,
        rateInput: '',
        saving: false,
        period: 30,
        chart: null,

        allRates: @json($chartRates->map(fn($r) => ['rate' => (float) $r->rate, 'date' => $r->fetched_at->format('d/m')])->values()),

        setPeriod(days) {
            this.period = days;
            this.renderChart();
        },

        renderChart() {
            const canvas = document.getElementById('dollarChart');
            if (!canvas || this.allRates.length < 2) return;

            if (typeof Chart === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                script.onload = () => this.renderChart();
                document.head.appendChild(script);
                return;
            }

            const data = this.allRates.slice(-this.period);
            const rates = data.map(r => r.rate);
            const labels = data.map(r => r.date);

            const minVal = Math.min(...rates);
            const maxVal = Math.max(...rates);
            const padding = (maxVal - minVal) * 0.2 || 0.02;

            const isUp = rates[rates.length - 1] >= rates[0];

            if (this.chart) {
                this.chart.data.labels = labels;
                this.chart.data.datasets[0].data = rates;
                this.chart.data.datasets[0].borderColor = isUp ? '#f87171' : '#34d399';
                this.chart.data.datasets[0].backgroundColor = isUp ? 'rgba(248,113,113,0.08)' : 'rgba(52,211,153,0.08)';
                this.chart.options.scales.y.min = minVal - padding;
                this.chart.options.scales.y.max = maxVal + padding;
                this.chart.update('none');
                return;
            }

            this.chart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: rates,
                        borderColor: isUp ? '#f87171' : '#34d399',
                        borderWidth: 2,
                        backgroundColor: isUp ? 'rgba(248,113,113,0.08)' : 'rgba(52,211,153,0.08)',
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: isUp ? '#f87171' : '#34d399',
                        pointHoverBorderWidth: 2,
                        tension: 0.35,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { size: 11, weight: '600' },
                            bodyFont: { size: 12, weight: '700' },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: (item) => 'R$ ' + parseFloat(item.raw).toFixed(2).replace('.', ',')
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: { display: false },
                            ticks: { font: { size: 10 }, maxRotation: 0, autoSkipPadding: 20, color: '#666' },
                            border: { display: false },
                        },
                        y: {
                            display: true,
                            position: 'right',
                            min: minVal - padding,
                            max: maxVal + padding,
                            grid: { color: 'rgba(255,255,255,0.04)' },
                            ticks: {
                                font: { size: 10 },
                                color: '#666',
                                callback: (v) => 'R$ ' + v.toFixed(2).replace('.', ',')
                            },
                            border: { display: false },
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        },

        saveDollarRate() {
            if (this.saving) return;
            let value = this.rateInput.trim();
            if (!value) return;
            value = value.replace(/\./g, '').replace(',', '.');
            const numericValue = parseFloat(value);
            if (isNaN(numericValue) || numericValue <= 0 || numericValue > 99.99) {
                alert('Informe um valor válido para a cotação (ex: 5,45)');
                return;
            }
            this.saving = true;
            fetch('{{ route("admin.dollar-rate.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ dollar_rate: numericValue }),
            })
            .then(r => r.json())
            .then(data => { if (data.success) window.location.reload(); })
            .catch(() => alert('Erro ao salvar cotação.'))
            .finally(() => this.saving = false);
        },

        init() {
            this.$watch('open', (val) => {
                if (val) {
                    this.$nextTick(() => setTimeout(() => this.renderChart(), 100));
                }
            });
        }
    }
}
</script>
@endif
