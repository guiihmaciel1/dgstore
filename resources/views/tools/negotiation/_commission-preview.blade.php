@if(true)
<div x-show="productPrice > 0 && productCost > 0" x-transition x-cloak>
    <div class="rounded-xl border overflow-hidden"
         :class="commissionEstimate.total > 0 ? 'border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-teal-50' : 'border-amber-200 bg-gradient-to-br from-amber-50 via-white to-orange-50'">

        {{-- Header com valor total --}}
        <div class="px-4 py-3 flex items-center justify-between"
             :class="commissionEstimate.total > 0 ? 'bg-emerald-600' : 'bg-amber-500'">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs font-bold text-white uppercase tracking-wide">Sua Comissão</span>
            </div>
            <span class="text-lg font-black text-white" x-text="'R$ ' + fmt(commissionEstimate.total)"></span>
        </div>

        <div class="p-4 space-y-3">

            {{-- Barra: Comissão do Aparelho --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-gray-700 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                        Lucro do aparelho
                    </span>
                    <span class="text-xs font-bold" :class="commissionEstimate.profit > 0 ? 'text-emerald-700' : 'text-gray-400'"
                          x-text="commissionEstimate.profit > 0 ? 'R$ ' + fmt(commissionEstimate.profit) : 'R$ 0'"></span>
                </div>
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 ease-out"
                         :class="commissionEstimate.profit > 0 ? 'bg-emerald-500' : 'bg-gray-200'"
                         :style="'width: ' + (productCost > 0 ? Math.min(100, Math.max(0, ((productPrice - productCost) / productCost) * 100)) : 0) + '%'"></div>
                </div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-[10px] text-gray-400" x-text="'Lucro: R$ ' + fmt(Math.max(0, productPrice - productCost))"></span>
                    <span class="text-[10px]"
                          :class="((productPrice - productCost) / productCost) >= 0.10 ? 'text-emerald-600' : 'text-red-500'"
                          x-text="productCost > 0 ? (((productPrice - productCost) / productCost * 100).toFixed(0) + '% margem') : ''"></span>
                </div>
            </div>

            {{-- Barra: Comissão Trade-in --}}
            <div x-show="tradeIn.result && tradeInValue > 0">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-gray-700 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                        Economia no trade-in <span class="text-gray-400 text-[10px] font-normal">(5%)</span>
                    </span>
                    <span class="text-xs font-bold" :class="commissionEstimate.tradein > 0 ? 'text-blue-700' : 'text-gray-400'"
                          x-text="commissionEstimate.tradein > 0 ? 'R$ ' + fmt(commissionEstimate.tradein) : 'R$ 0'"></span>
                </div>
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all duration-500 ease-out"
                         :style="'width: ' + (tradeIn.result ? Math.min(100, Math.max(0, ((tradeIn.result.resale_price - tradeInValue) / tradeIn.result.resale_price) * 100 / 0.20 * 100 / 100)) : 0) + '%'"></div>
                </div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-[10px] text-gray-400" x-text="tradeIn.result ? 'Sistema: R$ ' + fmt(tradeIn.result.resale_price) + ' → Ofereceu: R$ ' + fmt(tradeInValue) : ''"></span>
                    <span class="text-[10px] text-blue-600" x-text="tradeIn.result && tradeInValue < tradeIn.result.resale_price ? 'Economizou R$ ' + fmt(Math.min(tradeIn.result.resale_price - tradeInValue, tradeIn.result.resale_price * 0.20)) : 'Sem economia'"></span>
                </div>
                {{-- Dica: pagou demais --}}
                <div x-show="tradeIn.result && tradeInValue >= tradeIn.result.resale_price"
                     class="mt-1 flex items-start gap-1.5 bg-amber-50 border border-amber-100 rounded-lg px-2.5 py-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-[11px] text-amber-700 leading-tight">
                        Avaliação igual ou acima do sistema — <strong>sem bônus trade-in</strong>.
                        Negocie abaixo de R$ <span x-text="fmt(tradeIn.result?.resale_price || 0)"></span> para ganhar.
                    </span>
                </div>
            </div>

            {{-- Seção: Acessórios --}}
            <div class="border-t border-dashed pt-3" :class="commissionEstimate.total > 0 ? 'border-emerald-200' : 'border-amber-200'">
                <div class="flex items-center gap-1.5 mb-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500 inline-block"></span>
                    <span class="text-xs font-semibold text-gray-700">Acessórios</span>
                    <span class="text-[10px] text-gray-400 font-normal">— comissão extra por venda</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-2.5 text-center">
                        <p class="text-[10px] text-purple-600 font-semibold uppercase tracking-wider mb-0.5">Capinha</p>
                        <p class="text-[10px] text-gray-500">Base: R$ 30</p>
                        <p class="text-xs font-bold text-purple-700 mt-1">50% do lucro</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Ex: vendeu R$ 50 → <strong class="text-purple-600">+R$ 10</strong></p>
                    </div>
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-2.5 text-center">
                        <p class="text-[10px] text-purple-600 font-semibold uppercase tracking-wider mb-0.5">Carregador</p>
                        <p class="text-[10px] text-gray-500">Base: R$ 100</p>
                        <p class="text-xs font-bold text-purple-700 mt-1">20% do lucro</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Ex: vendeu R$ 150 → <strong class="text-purple-600">+R$ 10</strong></p>
                    </div>
                </div>
            </div>

            {{-- Potencial com acessórios --}}
            <div class="bg-purple-50 border border-purple-100 rounded-lg p-3 mt-1" x-show="commissionEstimate.total > 0">
                <p class="text-[11px] text-purple-800 leading-relaxed">
                    Se você vender <strong>capinha + carregador</strong> pode ganhar até
                    <strong class="text-purple-700 text-sm" x-text="'R$ ' + fmt(commissionEstimate.total + 10 + 10)"></strong> nessa venda!
                </p>
            </div>


        </div>
    </div>
</div>

{{-- Estado vazio: sem custo informado --}}
<div x-show="productPrice > 0 && productCost <= 0" x-cloak
     class="rounded-xl border border-gray-200 bg-white p-4">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs font-semibold text-gray-500">Comissão</span>
    </div>
    <p class="text-xs text-gray-400">Selecione um <strong>seminovo do estoque</strong> ou preencha o campo <strong>Custo</strong> para ver sua comissão estimada.</p>
</div>
@endif
