{{-- Card de assistência no detalhe do produto: status, composição do custo e histórico --}}
@if($product->canGoToRepair() || $product->repairs->isNotEmpty())
@php
    $canViewRepairFinancials = auth()->user()->canViewFinancials();
    $openRepair = $product->openRepair;
@endphp
<div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid {{ $openRepair ? 'rgba(249,115,22,0.4)' : 'rgba(255,255,255,0.06)' }}; overflow: hidden;">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: {{ $openRepair ? 'rgba(249,115,22,0.08)' : '#1a1a1a' }}; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
        <h3 style="font-weight: 600; color: {{ $openRepair ? '#fb923c' : '#e3e3e3' }};">Assistência</h3>
        @include('products.partials.repair-action', ['product' => $product])
    </div>
    <div style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
        @if($openRepair)
            <div>
                @include('products.partials.repair-badge', ['repair' => $openRepair])
                <p style="font-size: 0.8rem; color: #a4a4a4; margin-top: 0.5rem;">
                    Enviado em {{ $openRepair->sent_at->format('d/m/Y') }}
                    @if($openRepair->provider) para <strong>{{ $openRepair->provider }}</strong>@endif
                </p>
                @if($openRepair->reason)
                    <p style="font-size: 0.75rem; color: #818181;">{{ $openRepair->reason }}</p>
                @endif
            </div>
        @endif

        @if($canViewRepairFinancials && (float) $product->repair_cost > 0)
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; font-size: 0.75rem;">
                <div>
                    <p style="color: #818181; text-transform: uppercase; font-size: 0.65rem;">Compra</p>
                    <p style="color: #e3e3e3; font-weight: 600;">{{ $product->formatted_cost_price }}</p>
                </div>
                <div>
                    <p style="color: #818181; text-transform: uppercase; font-size: 0.65rem;">Assistência</p>
                    <p style="color: #fb923c; font-weight: 600;">R$ {{ number_format((float) $product->repair_cost, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p style="color: #818181; text-transform: uppercase; font-size: 0.65rem;">Custo final</p>
                    <p style="color: #34d399; font-weight: 700;">R$ {{ number_format((float) $product->final_cost, 2, ',', '.') }}</p>
                </div>
            </div>
        @endif

        @if($product->repairs->isNotEmpty())
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <p style="font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Histórico</p>
                @foreach($product->repairs as $repair)
                    <div style="padding: 0.5rem 0.75rem; background: rgba(255,255,255,0.03); border-radius: 0.5rem; font-size: 0.75rem; color: #a4a4a4;">
                        <div style="display: flex; justify-content: space-between; gap: 0.5rem;">
                            <span>{{ $repair->sent_at->format('d/m/Y') }} → {{ $repair->returned_at?->format('d/m/Y') ?? 'em andamento' }} ({{ $repair->days_out }}d)</span>
                            @if($canViewRepairFinancials && $repair->cost !== null)
                                <span style="color: #fb923c; font-weight: 600;">R$ {{ number_format((float) $repair->cost, 2, ',', '.') }}</span>
                            @endif
                        </div>
                        @if($repair->provider || $repair->notes)
                            <p style="color: #818181; margin-top: 0.125rem;">{{ collect([$repair->provider, $repair->notes])->filter()->implode(' — ') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif(! $openRepair)
            <p style="font-size: 0.8rem; color: #666;">Este aparelho nunca foi para a assistência.</p>
        @endif
    </div>
</div>
@endif
