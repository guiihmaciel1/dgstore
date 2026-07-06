{{-- Meta Semanal --}}
@php
    $weekly = $internStats['goals']['weekly'];
    $reached = $weekly['reached'];
    $pastDeadline = $weekly['past_deadline'];
    $progress = $weekly['progress'];
@endphp

<div style="background: rgba(255,255,255,0.03); border: 1px solid {{ $reached ? 'rgba(16,185,129,0.2)' : 'rgba(255,255,255,0.06)' }}; border-radius: 12px; padding: 1.5rem; position: relative; overflow: hidden;">
    @if($reached)
        <div style="position: absolute; top: 0; right: 0; width: 120px; height: 120px; background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, transparent 70%); pointer-events: none;"></div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0;">Meta da Semana</h3>
        @if($reached)
            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: rgba(16,185,129,0.15); font-size: 0.6875rem; font-weight: 600; color: #10b981;">
                <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                META BATIDA!
            </span>
        @elseif($pastDeadline)
            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 9999px; background: rgba(239,68,68,0.15); font-size: 0.6875rem; font-weight: 500; color: #ef4444;">
                Não atingida esta semana
            </span>
        @else
            <span style="font-size: 0.6875rem; color: #64748b;">Prazo: {{ $weekly['deadline_label'] }}</span>
        @endif
    </div>

    {{-- Big number --}}
    <div style="text-align: center; margin-bottom: 1.5rem;">
        <div style="display: inline-flex; align-items: baseline; gap: 0.25rem;">
            <span style="font-size: 3rem; font-weight: 800; color: {{ $reached ? '#10b981' : '#f1f5f9' }}; line-height: 1;">{{ $weekly['current'] }}</span>
            <span style="font-size: 1.25rem; color: #64748b; font-weight: 500;">/{{ $weekly['target'] }}</span>
        </div>
        <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">vendas do time esta semana</p>
    </div>

    {{-- Progress bar --}}
    <div style="height: 10px; background: rgba(255,255,255,0.05); border-radius: 5px; overflow: hidden; margin-bottom: 0.75rem;">
        <div style="height: 100%; border-radius: 5px; transition: width 1s cubic-bezier(0.4,0,0.2,1); width: {{ $progress }}%; background: {{ $reached ? 'linear-gradient(90deg, #10b981, #34d399)' : 'linear-gradient(90deg, #06b6d4, #22d3ee)' }};{{ $reached ? ' box-shadow: 0 0 10px rgba(16,185,129,0.4);' : '' }}"></div>
    </div>

    {{-- Status message --}}
    @if($reached)
        <div style="text-align: center; padding: 0.75rem; background: rgba(16,185,129,0.08); border-radius: 8px;">
            <p style="font-size: 0.8125rem; color: #10b981; font-weight: 500;">
                <svg style="width: 14px; height: 14px; display: inline; vertical-align: middle; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                {{ $weekly['reward'] }}
            </p>
        </div>
    @elseif(!$pastDeadline)
        <p style="text-align: center; font-size: 0.75rem; color: #94a3b8;">
            Faltam <strong style="color: #06b6d4;">{{ $weekly['remaining'] }}</strong> vendas para bater a meta
        </p>
    @endif
</div>
