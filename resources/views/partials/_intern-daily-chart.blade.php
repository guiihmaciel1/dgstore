{{-- Chart de Evolução Diária --}}
<div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
    <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1.25rem;">Evolução Diária (Time)</h3>

    <div style="position: relative; height: 220px;">
        <canvas id="internDailyChart"></canvas>
    </div>

    @if(!empty($internStats['daily_chart']['data']))
        @php
            $totalDays = count($internStats['daily_chart']['data']);
            $activeDays = count(array_filter($internStats['daily_chart']['data'], fn($v) => $v > 0));
            $maxDay = max($internStats['daily_chart']['data']);
            $avgPerDay = $totalDays > 0 ? round(array_sum($internStats['daily_chart']['data']) / $totalDays, 1) : 0;
        @endphp
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05);">
            <div style="text-align: center;">
                <p style="font-size: 1rem; font-weight: 700; color: #f1f5f9;">{{ $avgPerDay }}</p>
                <p style="font-size: 0.625rem; color: #64748b;">Média/dia</p>
            </div>
            <div style="text-align: center;">
                <p style="font-size: 1rem; font-weight: 700; color: #f1f5f9;">{{ $maxDay }}</p>
                <p style="font-size: 0.625rem; color: #64748b;">Melhor dia</p>
            </div>
            <div style="text-align: center;">
                <p style="font-size: 1rem; font-weight: 700; color: #f1f5f9;">{{ $activeDays }}/{{ $totalDays }}</p>
                <p style="font-size: 0.625rem; color: #64748b;">Dias ativos</p>
            </div>
        </div>
    @endif
</div>
