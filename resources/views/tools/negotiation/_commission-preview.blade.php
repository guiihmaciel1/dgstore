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
                <span class="text-xs font-bold text-white uppercase tracking-wide">Sua Comissão Aproximada</span>
            </div>
            <span class="text-lg font-black text-white" x-text="'R$ ' + fmt(commissionEstimate.total)"></span>
        </div>

        <div class="p-4 space-y-3">

            <p class="text-[10px] text-gray-400 italic leading-tight">Pode alterar caso o preço de custo aumente até o ato da venda.</p>

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
                    <span class="text-[10px] text-gray-400 font-normal">— preencha o valor de venda</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    {{-- Capinha --}}
                    <div class="rounded-lg p-2.5 border transition-all duration-200"
                         :class="accessoryCasePrice > 10 ? 'bg-purple-50 border-purple-300' : 'bg-gray-50 border-gray-200'">
                        <p class="text-[10px] font-semibold uppercase tracking-wider mb-1.5"
                           :class="accessoryCasePrice > 10 ? 'text-purple-600' : 'text-gray-500'">Capinha</p>
                        <div class="relative">
                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400">R$</span>
                            <input type="number" x-model.number="accessoryCasePrice" min="0" step="5" placeholder="0"
                                   class="w-full pl-7 pr-2 py-1.5 text-xs font-semibold border rounded-md text-center focus:outline-none focus:ring-1 transition-all"
                                   :class="accessoryCasePrice > 10 ? 'border-purple-300 bg-white focus:ring-purple-400 text-purple-800' : 'border-gray-200 bg-white focus:ring-gray-300 text-gray-700'">
                        </div>
                        <div class="mt-1.5 text-center" x-show="accessoryCasePrice > 10">
                            <span class="text-[10px] text-gray-400">Lucro: R$ <span x-text="fmt(accessoryCasePrice - 10)"></span></span>
                            <p class="text-xs font-bold text-purple-700">+R$ <span x-text="fmt(commissionEstimate.caseComm)"></span></p>
                        </div>
                        <p class="text-[9px] text-gray-400 mt-1 text-center" x-show="accessoryCasePrice <= 10">Base R$ 10 · 50% do lucro</p>
                    </div>
                    {{-- Carregador --}}
                    <div class="rounded-lg p-2.5 border transition-all duration-200"
                         :class="accessoryChargerPrice > 50 ? 'bg-purple-50 border-purple-300' : 'bg-gray-50 border-gray-200'">
                        <p class="text-[10px] font-semibold uppercase tracking-wider mb-1.5"
                           :class="accessoryChargerPrice > 50 ? 'text-purple-600' : 'text-gray-500'">Carregador</p>
                        <div class="relative">
                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400">R$</span>
                            <input type="number" x-model.number="accessoryChargerPrice" min="0" step="10" placeholder="0"
                                   class="w-full pl-7 pr-2 py-1.5 text-xs font-semibold border rounded-md text-center focus:outline-none focus:ring-1 transition-all"
                                   :class="accessoryChargerPrice > 50 ? 'border-purple-300 bg-white focus:ring-purple-400 text-purple-800' : 'border-gray-200 bg-white focus:ring-gray-300 text-gray-700'">
                        </div>
                        <div class="mt-1.5 text-center" x-show="accessoryChargerPrice > 50">
                            <span class="text-[10px] text-gray-400">Lucro: R$ <span x-text="fmt(accessoryChargerPrice - 50)"></span></span>
                            <p class="text-xs font-bold text-purple-700">+R$ <span x-text="fmt(commissionEstimate.chargerComm)"></span></p>
                        </div>
                        <p class="text-[9px] text-gray-400 mt-1 text-center" x-show="accessoryChargerPrice <= 50">Base R$ 50 · 50% do lucro</p>
                    </div>
                </div>
                {{-- Total de acessórios --}}
                <div x-show="commissionEstimate.accessoryTotal > 0" x-transition
                     class="mt-2 bg-purple-100 border border-purple-200 rounded-lg px-3 py-2 flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-purple-700">Comissão acessórios</span>
                    <span class="text-sm font-black text-purple-800" x-text="'+R$ ' + fmt(commissionEstimate.accessoryTotal)"></span>
                </div>
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
