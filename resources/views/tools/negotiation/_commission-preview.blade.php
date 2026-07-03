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
                        <p class="text-[10px] text-gray-500">Base: R$ 10</p>
                        <p class="text-xs font-bold text-purple-700 mt-1">50% do lucro</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Ex: vendeu R$ 50 → <strong class="text-purple-600">+R$ 20</strong></p>
                    </div>
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-2.5 text-center">
                        <p class="text-[10px] text-purple-600 font-semibold uppercase tracking-wider mb-0.5">Carregador</p>
                        <p class="text-[10px] text-gray-500">Base: R$ 50</p>
                        <p class="text-xs font-bold text-purple-700 mt-1">50% do lucro</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Ex: vendeu R$ 100 → <strong class="text-purple-600">+R$ 25</strong></p>
                    </div>
                </div>
            </div>

            {{-- Potencial com acessórios --}}
            <div class="bg-purple-50 border border-purple-100 rounded-lg p-3 mt-1" x-show="commissionEstimate.total > 0">
                <p class="text-[11px] text-purple-800 leading-relaxed">
                    Se você vender <strong>capinha + carregador</strong> pode ganhar até
                    <strong class="text-purple-700 text-sm" x-text="'R$ ' + fmt(commissionEstimate.total + 20 + 25)"></strong> nessa venda!
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
