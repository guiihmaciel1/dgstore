{{-- Painel inicial: aparelhos que estão na assistência --}}
@if($openRepairs->isNotEmpty())
@php
    $lateRepairs = $openRepairs->filter(fn ($r) => $r->days_out >= \App\Domain\Product\Services\ProductRepairService::LATE_AFTER_DAYS)->count();
@endphp
<div style="background: #141414; border: 2px solid {{ $lateRepairs ? '#ef4444' : '#f97316' }}; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1rem;">
    <div style="padding: 0.625rem 0.875rem; background: rgba(249,115,22,0.08); border-bottom: 1px solid rgba(249,115,22,0.15); display: flex; align-items: center; gap: 0.5rem;">
        <span style="font-size: 1rem;">🔧</span>
        <div>
            <span style="font-size: 0.8rem; font-weight: 800; color: #fdba74;">Na assistência</span>
            <span style="font-size: 0.65rem; color: #fb923c; margin-left: 0.375rem;">({{ $openRepairs->count() }} {{ $openRepairs->count() === 1 ? 'aparelho' : 'aparelhos' }}@if($lateRepairs), {{ $lateRepairs }} atrasado{{ $lateRepairs > 1 ? 's' : '' }}@endif)</span>
        </div>
        <a href="{{ route('products.index', ['status' => 'in_repair']) }}" style="margin-left: auto; font-size: 0.65rem; font-weight: 700; color: #fb923c; text-decoration: none; background: rgba(249,115,22,0.1); padding: 0.25rem 0.625rem; border-radius: 9999px; white-space: nowrap;">
            Ver todos →
        </a>
    </div>
    <div style="padding: 0.375rem 0.5rem;">
        @foreach($openRepairs as $repair)
            <a href="{{ route('products.show', $repair->product) }}" style="display: flex; align-items: center; gap: 0.625rem; padding: 0.5rem; text-decoration: none; {{ !$loop->last ? 'border-bottom: 1px solid rgba(255,255,255,0.04);' : '' }}">
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #e3e3e3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $repair->product->full_name }}</div>
                    <div style="font-size: 0.65rem; color: #818181;">
                        Desde {{ $repair->sent_at->format('d/m') }}
                        @if($repair->provider) · {{ $repair->provider }}@endif
                        @if($repair->reason) · {{ $repair->reason }}@endif
                    </div>
                </div>
                @include('products.partials.repair-badge', ['repair' => $repair])
            </a>
        @endforeach
    </div>
</div>
@endif
