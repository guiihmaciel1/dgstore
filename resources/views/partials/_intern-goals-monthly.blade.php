{{-- Metas Mensais --}}
<div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0;">Metas do Mês (Time)</h3>
        <span style="font-size: 0.6875rem; color: #64748b;">{{ $internStats['combined']['total_sales'] }} vendas até agora</span>
    </div>

    @foreach($internStats['goals']['monthly'] as $index => $goal)
        @php
            $reached = $goal['reached'];
            $progress = $goal['progress'];
            $gradients = [
                'linear-gradient(90deg, #06b6d4, #22d3ee)',
                'linear-gradient(90deg, #8b5cf6, #a78bfa)',
                'linear-gradient(90deg, #f59e0b, #fbbf24)',
            ];
            $gradient = $gradients[$index] ?? $gradients[0];
        @endphp
        <div style="margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    @if($reached)
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: rgba(16,185,129,0.2); animation: pulse 2s infinite;">
                            <svg style="width: 12px; height: 12px; color: #10b981;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: rgba(255,255,255,0.05);">
                            <span style="font-size: 0.625rem; color: #64748b;">{{ $goal['target'] }}</span>
                        </span>
                    @endif
                    <span style="font-size: 0.8125rem; color: {{ $reached ? '#10b981' : '#cbd5e1' }}; font-weight: {{ $reached ? '600' : '400' }};">
                        {{ $goal['target'] }} vendas
                    </span>
                </div>
                <span style="font-size: 0.6875rem; color: #64748b;">{{ $progress }}%</span>
            </div>

            {{-- Progress bar --}}
            <div style="height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden; position: relative;">
                <div style="height: 100%; border-radius: 4px; background: {{ $gradient }}; transition: width 1s cubic-bezier(0.4,0,0.2,1); width: {{ $progress }}%;{{ $reached ? ' box-shadow: 0 0 8px rgba(16,185,129,0.4);' : '' }}"></div>
            </div>

            {{-- Reward --}}
            <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem;">
                <svg style="width: 12px; height: 12px; color: {{ $reached ? '#fbbf24' : '#475569' }}; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5z" clip-rule="evenodd"/></svg>
                <span style="font-size: 0.6875rem; color: {{ $reached ? '#fbbf24' : '#64748b' }};">{{ $goal['reward'] }}</span>
            </div>
        </div>
    @endforeach
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>
