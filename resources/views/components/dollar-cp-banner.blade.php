@php
    $rates = \App\Domain\System\Models\DollarCpRate::lastTen();
    $latest = $rates->first();
    $previous = $rates->skip(1)->first();
@endphp

@if($latest)
<div style="background: #1e293b; padding: 0; border-bottom: 1px solid #334155;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="display: flex; align-items: center; justify-content: center; gap: 14px; padding: 5px 0; flex-wrap: wrap;">
            {{-- Label --}}
            <span style="font-size: 11px; font-weight: 500; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase;">Dólar Compras Paraguai</span>

            {{-- Valor atual --}}
            <span style="font-size: 14px; font-weight: 700; color: #f1f5f9;">
                R$ {{ number_format((float) $latest->rate, 2, ',', '.') }}
            </span>

            {{-- Variação --}}
            @if($previous)
                @php
                    $diff = (float) $latest->rate - (float) $previous->rate;
                    $pct = (float) $previous->rate > 0 ? ($diff / (float) $previous->rate) * 100 : 0;
                    $isUp = $diff > 0;
                    $isDown = $diff < 0;
                @endphp
                <span style="display: inline-flex; align-items: center; gap: 2px; font-size: 11px; font-weight: 600;
                    color: {{ $isUp ? '#f87171' : ($isDown ? '#4ade80' : '#94a3b8') }};">
                    @if($isUp)
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                    @elseif($isDown)
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                    {{ ($isUp ? '+' : '') . number_format($pct, 2, ',', '.') }}%
                </span>
            @endif

            {{-- Sparkline --}}
            @if($rates->count() >= 2)
                <canvas id="cpDollarSparkline" width="120" height="28" style="display: inline-block; vertical-align: middle;"></canvas>
            @endif

            {{-- Timestamp --}}
            <span style="font-size: 10px; color: #64748b;">
                {{ $latest->fetched_at->format('d/m H:i') }}
            </span>
        </div>
    </div>
</div>

@if($rates->count() >= 2)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('cpDollarSparkline');
    if (!ctx) return;

    const ratesData = @json($rates->reverse()->pluck('rate')->map(fn($r) => (float) $r)->values());
    const labels = @json($rates->reverse()->pluck('fetched_at')->map(fn($d) => $d->format('d/m H:i'))->values());

    const minVal = Math.min(...ratesData);
    const maxVal = Math.max(...ratesData);
    const padding = (maxVal - minVal) * 0.15 || 0.01;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: ratesData,
                borderColor: ratesData[ratesData.length - 1] >= ratesData[0] ? '#f87171' : '#4ade80',
                borderWidth: 1.5,
                fill: false,
                pointRadius: 0,
                pointHoverRadius: 3,
                pointHoverBackgroundColor: '#fff',
                tension: 0.3,
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#1e293b',
                    titleFont: { size: 10 },
                    bodyFont: { size: 11 },
                    padding: 6,
                    displayColors: false,
                    callbacks: {
                        title: (items) => items[0].label,
                        label: (item) => 'R$ ' + parseFloat(item.raw).toFixed(2).replace('.', ',')
                    }
                }
            },
            scales: {
                x: { display: false },
                y: {
                    display: false,
                    min: minVal - padding,
                    max: maxVal + padding,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
@endif
@endif
