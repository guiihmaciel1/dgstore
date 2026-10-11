{{-- Simulador de Negociação integrado ao Deal --}}
<div style="background: #0d0d0d; border: 1px solid #222; border-top: none; border-radius: 0 0 0.75rem 0.75rem; overflow: hidden;">

    {{-- Simulações Salvas --}}
    @if($deal->negotiationSnapshots->isNotEmpty())
        <div style="padding: 0.75rem; border-bottom: 1px solid #1a1a1a;">
            <div style="font-size: 0.7rem; font-weight: 700; color: #818181; margin-bottom: 0.5rem;">SIMULAÇÕES SALVAS</div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 250px; overflow-y: auto;">
                @foreach($deal->negotiationSnapshots->sortByDesc('created_at') as $snap)
                    <div style="padding: 0.625rem; background: #111; border: 1px solid #222; border-radius: 8px; transition: border-color 0.15s;"
                         onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='#222'">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.25rem;">
                            <div style="font-size: 0.8rem; font-weight: 600; color: #e3e3e3;">{{ $snap->product_description }}</div>
                            <span style="font-size: 0.8rem; font-weight: 700; color: #8b5cf6; white-space: nowrap;">
                                R$ {{ number_format((float)$snap->product_price, 2, ',', '.') }}
                            </span>
                        </div>
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; font-size: 0.7rem; color: #818181;">
                            @if($snap->trade_in_model)
                                <span style="display: flex; align-items: center; gap: 0.2rem;">
                                    <svg style="width: 10px; height: 10px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    {{ $snap->trade_in_model }} {{ $snap->trade_in_storage }}
                                    @if($snap->trade_in_value > 0)
                                        (R$ {{ number_format((float)$snap->trade_in_value, 0, ',', '.') }})
                                    @endif
                                </span>
                            @endif
                            @if($snap->down_payment > 0)
                                <span>Entrada: R$ {{ number_format((float)$snap->down_payment, 0, ',', '.') }}</span>
                            @endif
                            @if($snap->card_balance > 0)
                                <span>Cartão: R$ {{ number_format((float)$snap->card_balance, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.375rem;">
                            <span style="font-size: 0.6rem; color: #555;">
                                {{ $snap->user?->name }} · {{ $snap->created_at->format('d/m H:i') }}
                                @if($snap->expires_at && $snap->expires_at->isPast())
                                    <span style="color: #ef4444;">· Expirada</span>
                                @endif
                            </span>
                            <div style="display: flex; gap: 0.375rem;">
                                @if($snap->message_text)
                                    <button onclick="copySnapMessage(this, {{ json_encode($snap->message_text) }})"
                                            style="font-size: 0.6rem; color: #25D366; background: none; border: none; cursor: pointer; padding: 0;" title="Copiar proposta">
                                        📋 Copiar
                                    </button>
                                @endif
                                <a href="{{ route('tools.negotiation-simulator') }}?snap_product={{ urlencode($snap->product_description) }}&snap_price={{ $snap->product_price }}&snap_cost={{ $snap->product_cost ?? 0 }}&snap_tradein_model={{ urlencode($snap->trade_in_model ?? '') }}&snap_tradein_storage={{ urlencode($snap->trade_in_storage ?? '') }}&snap_tradein_battery={{ $snap->trade_in_battery ?? 100 }}&snap_tradein_value={{ $snap->trade_in_value ?? 0 }}&snap_tradein_system_value={{ $snap->trade_in_system_value ?? 0 }}"
                                   style="font-size: 0.6rem; color: #8b5cf6; text-decoration: none;" title="Abrir no simulador">
                                    🔄 Reabrir
                                </a>
                            </div>
                        </div>
                        @if($snap->notes)
                            <div style="margin-top: 0.25rem; font-size: 0.65rem; color: #666; font-style: italic;">{{ $snap->notes }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Ação: Nova Simulação --}}
    <div style="padding: 0.75rem;">
        <a href="{{ route('tools.negotiation-simulator') }}?deal_id={{ $deal->id }}&customer_id={{ $deal->customer_id }}&product={{ urlencode($deal->product_interest ?? '') }}"
           style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem; background: #1a1a1a; border: 1px dashed #333; border-radius: 8px; color: #8b5cf6; font-size: 0.8rem; font-weight: 600; text-decoration: none; transition: all 0.15s;"
           onmouseover="this.style.borderColor='#8b5cf6'; this.style.background='#111';" onmouseout="this.style.borderColor='#333'; this.style.background='#1a1a1a';">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nova Simulação para este Negócio
        </a>

        @if($deal->product_interest)
            <p style="font-size: 0.65rem; color: #555; text-align: center; margin-top: 0.375rem;">
                Produto de interesse: <strong style="color: #818181;">{{ $deal->product_interest }}</strong>
            </p>
        @endif
    </div>

    {{-- Resumo se não tem simulações --}}
    @if($deal->negotiationSnapshots->isEmpty())
        <div style="padding: 1.5rem 1rem; text-align: center;">
            <svg style="width: 36px; height: 36px; margin: 0 auto 0.5rem; color: #333;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <p style="font-size: 0.8rem; color: #555;">Nenhuma simulação salva</p>
            <p style="font-size: 0.7rem; color: #444; margin-top: 0.25rem;">Crie simulações para montar propostas com trade-in e parcelamento</p>
        </div>
    @endif
</div>

<script>
function copySnapMessage(btn, text) {
    navigator.clipboard.writeText(text).then(() => {
        const original = btn.textContent;
        btn.textContent = '✓ Copiado!';
        setTimeout(() => btn.textContent = original, 2000);
    });
}
</script>
