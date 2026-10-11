{{-- Simulador de Negociação integrado ao Deal --}}
<div style="display: flex; flex-direction: column; height: 100%; background: #0a0a0a;">

    {{-- Simulações Salvas --}}
    @if($allSnapshots->isNotEmpty())
        <div style="flex: 1; overflow-y: auto; padding: 0.75rem;" class="custom-scroll">
            <div style="font-size: 0.65rem; font-weight: 700; color: #555; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
                Simulações Salvas ({{ $allSnapshots->count() }})
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach($allSnapshots->sortByDesc('created_at') as $snap)
                    <div style="padding: 0.625rem; background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; transition: border-color 0.15s;"
                         onmouseover="this.style.borderColor='rgba(139,92,246,0.3)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.375rem;">
                            <div style="font-size: 0.8rem; font-weight: 600; color: #e3e3e3;">{{ $snap->product_description }}</div>
                            <span style="font-size: 0.85rem; font-weight: 700; color: #8b5cf6; white-space: nowrap;">
                                R$ {{ number_format((float)$snap->product_price, 2, ',', '.') }}
                            </span>
                        </div>

                        {{-- Detalhes do trade-in, entrada, cartão --}}
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; font-size: 0.7rem; color: #666;">
                            @if($snap->trade_in_model)
                                <span style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.125rem 0.375rem; background: rgba(245,158,11,0.08); border-radius: 4px; color: #f59e0b;">
                                    ↔ {{ $snap->trade_in_model }} {{ $snap->trade_in_storage }}
                                    @if($snap->trade_in_value > 0)
                                        (R$ {{ number_format((float)$snap->trade_in_value, 0, ',', '.') }})
                                    @endif
                                </span>
                            @endif
                            @if($snap->down_payment > 0)
                                <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.375rem; background: rgba(59,130,246,0.08); border-radius: 4px; color: #93c5fd;">
                                    💰 Entrada: R$ {{ number_format((float)$snap->down_payment, 0, ',', '.') }}
                                </span>
                            @endif
                            @if($snap->card_balance > 0)
                                <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.375rem; background: rgba(16,185,129,0.08); border-radius: 4px; color: #6ee7b7;">
                                    💳 Cartão: R$ {{ number_format((float)$snap->card_balance, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>

                        {{-- Footer: autor + ações --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.375rem; padding-top: 0.375rem; border-top: 1px solid rgba(255,255,255,0.04);">
                            <span style="font-size: 0.6rem; color: #444;">
                                {{ $snap->user?->name }} · {{ $snap->created_at->format('d/m H:i') }}
                                @if($snap->expires_at && $snap->expires_at->isPast())
                                    <span style="color: #ef4444; font-weight: 600;">· Expirada</span>
                                @endif
                            </span>
                            <div style="display: flex; gap: 0.5rem;">
                                @if($snap->message_text)
                                    <button onclick="copySnapMessage(this, {{ json_encode($snap->message_text) }})"
                                            style="font-size: 0.6rem; color: #25D366; background: none; border: none; cursor: pointer; padding: 0; font-weight: 500;" title="Copiar proposta">
                                        📋 Copiar
                                    </button>
                                @endif
                                <a href="{{ route('tools.negotiation-simulator') }}?snap_product={{ urlencode($snap->product_description) }}&snap_price={{ $snap->product_price }}&snap_cost={{ $snap->product_cost ?? 0 }}&snap_tradein_model={{ urlencode($snap->trade_in_model ?? '') }}&snap_tradein_storage={{ urlencode($snap->trade_in_storage ?? '') }}&snap_tradein_battery={{ $snap->trade_in_battery ?? 100 }}&snap_tradein_value={{ $snap->trade_in_value ?? 0 }}&snap_tradein_system_value={{ $snap->trade_in_system_value ?? 0 }}"
                                   style="font-size: 0.6rem; color: #8b5cf6; text-decoration: none; font-weight: 500;" title="Abrir no simulador">
                                    🔄 Reabrir
                                </a>
                            </div>
                        </div>

                        @if($snap->notes)
                            <div style="margin-top: 0.25rem; font-size: 0.6rem; color: #555; font-style: italic;">{{ $snap->notes }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Estado vazio --}}
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
            <div style="text-align: center;">
                <svg style="width: 36px; height: 36px; margin: 0 auto 0.5rem; color: #222;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p style="font-size: 0.8rem; color: #444; margin: 0;">Nenhuma simulação salva</p>
                <p style="font-size: 0.7rem; color: #333; margin-top: 0.25rem;">Monte propostas com trade-in e parcelamento</p>
            </div>
        </div>
    @endif

    {{-- Botão Nova Simulação (fixo no fundo) --}}
    <div style="flex-shrink: 0; padding: 0.625rem; background: #111; border-top: 1px solid #1a1a1a;">
        <a href="{{ route('tools.negotiation-simulator') }}?deal_id={{ $deal->id }}&customer_id={{ $deal->customer_id }}&product={{ urlencode($deal->product_interest ?? '') }}"
           style="display: flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem; background: #1a1a1a; border: 1px dashed #333; border-radius: 6px; color: #8b5cf6; font-size: 0.75rem; font-weight: 600; text-decoration: none; transition: all 0.15s;"
           onmouseover="this.style.borderColor='#8b5cf6'; this.style.background='#141414';" onmouseout="this.style.borderColor='#333'; this.style.background='#1a1a1a';">
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nova Simulação
        </a>
        @if($deal->product_interest)
            <p style="font-size: 0.6rem; color: #444; text-align: center; margin: 0.25rem 0 0;">
                Produto: <strong style="color: #666;">{{ $deal->product_interest }}</strong>
            </p>
        @endif
    </div>
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
