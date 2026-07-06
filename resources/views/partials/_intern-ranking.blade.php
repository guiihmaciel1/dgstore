{{-- Ranking das Estagiárias --}}
<div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
    <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1.25rem;">Ranking do Mês</h3>

    @foreach($internStats['interns'] as $index => $intern)
        @php
            $position = $index + 1;
            $medalColors = ['#fbbf24', '#94a3b8', '#cd7f32'];
            $medalColor = $medalColors[$index] ?? null;
            $combinedSales = $internStats['combined']['total_sales'];
            $percentage = $combinedSales > 0 ? round(($intern['sales_count'] / $combinedSales) * 100) : 0;
        @endphp
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; border-radius: 10px; margin-bottom: 0.5rem; background: {{ $index === 0 ? 'rgba(251,191,36,0.05)' : 'rgba(255,255,255,0.02)' }}; border: 1px solid {{ $index === 0 ? 'rgba(251,191,36,0.1)' : 'transparent' }}; transition: background 0.2s;">

            {{-- Position --}}
            <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                        background: {{ $medalColor ? 'rgba(' . ($index === 0 ? '251,191,36' : ($index === 1 ? '148,163,184' : '205,127,50')) . ',0.15)' : 'rgba(255,255,255,0.05)' }};">
                @if($medalColor)
                    <svg style="width: 16px; height: 16px; color: {{ $medalColor }};" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @else
                    <span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">{{ $position }}º</span>
                @endif
            </div>

            {{-- Name + Stats --}}
            <div style="flex: 1; min-width: 0;">
                <p style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $intern['name'] }}</p>
                <p style="font-size: 0.6875rem; color: #64748b;">{{ $percentage }}% do time</p>
            </div>

            {{-- Sales count --}}
            <div style="text-align: right; flex-shrink: 0;">
                <p style="font-size: 1.25rem; font-weight: 700; color: #f1f5f9;">{{ $intern['sales_count'] }}</p>
                <p style="font-size: 0.625rem; color: #64748b;">vendas</p>
            </div>
        </div>
    @endforeach

    @if(empty($internStats['interns']))
        <p style="text-align: center; color: #64748b; font-size: 0.8125rem; padding: 1rem;">Nenhuma estagiária com vendas no período.</p>
    @endif
</div>
