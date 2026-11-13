{{-- Simulador de Negociação integrado ao Deal --}}
<div class="flex flex-col h-full bg-[#0a0a0a]">

    {{-- Simulações Salvas --}}
    @if($allSnapshots->isNotEmpty())
        <div  class="custom-scroll flex-1 overflow-y-auto p-3">
            <div class="text-[0.65rem] font-bold text-dg-500 mb-2 uppercase tracking-wider">
                Simulações Salvas ({{ $allSnapshots->count() }})
            </div>
            <div class="flex flex-col gap-2">
                @foreach($allSnapshots->sortByDesc('created_at') as $snap)
                    <div class="p-2.5 bg-surface-raised border border-border rounded-[8px] transition-colors hover:border-violet-500/30">
                        <div class="flex items-start justify-between mb-1.5">
                            <div class="text-[0.8rem] font-semibold text-dg-100">{{ $snap->product_description }}</div>
                            <span class="text-[0.85rem] font-bold text-violet-500 whitespace-nowrap">
                                R$ {{ number_format((float)$snap->product_price, 2, ',', '.') }}
                            </span>
                        </div>

                        {{-- Detalhes do trade-in, entrada, cartão --}}
                        <div class="flex gap-2 flex-wrap text-[0.7rem] text-dg-500">
                            @if($snap->trade_in_model)
                                <span class="inline-flex items-center gap-[0.2rem] py-0.5 px-1.5 bg-amber-500/[0.08] rounded-[4px] text-amber-500">
                                    ↔ {{ $snap->trade_in_model }} {{ $snap->trade_in_storage }}
                                    @if($snap->trade_in_value > 0)
                                        (R$ {{ number_format((float)$snap->trade_in_value, 0, ',', '.') }})
                                    @endif
                                </span>
                            @endif
                            @if($snap->down_payment > 0)
                                <span class="inline-flex items-center py-0.5 px-1.5 bg-blue-500/[0.08] rounded-[4px] text-blue-300">
                                    💰 Entrada: R$ {{ number_format((float)$snap->down_payment, 0, ',', '.') }}
                                </span>
                            @endif
                            @if($snap->card_balance > 0)
                                <span class="inline-flex items-center py-0.5 px-1.5 bg-emerald-500/[0.08] rounded-[4px] text-emerald-300">
                                    💳 Cartão: R$ {{ number_format((float)$snap->card_balance, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>

                        {{-- Footer: autor + ações --}}
                        <div class="flex items-center justify-between mt-1.5 pt-1.5 border-t border-border-subtle">
                            <span class="text-[0.6rem] text-[#444]">
                                {{ $snap->user?->name }} · {{ $snap->created_at->format('d/m H:i') }}
                                @if($snap->expires_at && $snap->expires_at->isPast())
                                    <span class="text-red-500 font-semibold">· Expirada</span>
                                @endif
                            </span>
                            <div class="flex gap-2">
                                @if($snap->message_text)
                                    <button class="text-[0.6rem] text-[#25d366] bg-transparent border-0 cursor-pointer p-0 font-medium" onclick="copySnapMessage(this, {{ json_encode($snap->message_text) }})"
                                             title="Copiar proposta">
                                        📋 Copiar
                                    </button>
                                @endif
                                <a class="text-[0.6rem] text-violet-500 no-underline font-medium" href="{{ route('tools.negotiation-simulator') }}?snap_product={{ urlencode($snap->product_description) }}&snap_price={{ $snap->product_price }}&snap_cost={{ $snap->product_cost ?? 0 }}&snap_tradein_model={{ urlencode($snap->trade_in_model ?? '') }}&snap_tradein_storage={{ urlencode($snap->trade_in_storage ?? '') }}&snap_tradein_battery={{ $snap->trade_in_battery ?? 100 }}&snap_tradein_value={{ $snap->trade_in_value ?? 0 }}&snap_tradein_system_value={{ $snap->trade_in_system_value ?? 0 }}"
                                    title="Abrir no simulador">
                                    🔄 Reabrir
                                </a>
                            </div>
                        </div>

                        @if($snap->notes)
                            <div class="mt-1 text-[0.6rem] text-dg-500 italic">{{ $snap->notes }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Estado vazio --}}
        <div class="flex-1 flex items-center justify-center py-8 px-4">
            <div class="text-center">
                <svg class="w-[36px] h-[36px] mt-0 mx-auto mb-2 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p class="text-[0.8rem] text-[#444] m-0">Nenhuma simulação salva</p>
                <p class="text-[0.7rem] text-[#333] mt-1">Monte propostas com trade-in e parcelamento</p>
            </div>
        </div>
    @endif

    {{-- Botão Nova Simulação (fixo no fundo) --}}
    <div class="shrink-0 p-2.5 bg-[#111] border-t border-[#1a1a1a]">
        <a class="flex items-center justify-center gap-1.5 p-2 bg-surface-overlay rounded-[6px] text-violet-500 text-xs font-semibold no-underline transition-all hover:border-violet-500 hover:bg-surface-raised" href="{{ route('tools.negotiation-simulator') }}?deal_id={{ $deal->id }}&customer_id={{ $deal->customer_id }}&product={{ urlencode($deal->product_interest ?? '') }}"
           style="border: 1px dashed #333;">
            <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nova Simulação
        </a>
        @if($deal->product_interest)
            <p class="text-[0.6rem] text-[#444] text-center mt-1 mx-0 mb-0">
                Produto: <strong class="text-dg-500">{{ $deal->product_interest }}</strong>
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
