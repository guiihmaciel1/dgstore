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
                          :class="((productPrice - productCost) / productCost) >= 0.17 ? 'text-emerald-600' : 'text-red-500'"
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

                {{-- Capinha: toggle + combos --}}
                <div class="rounded-lg p-3 border transition-all duration-200 mb-2"
                     :class="caseEnabled ? 'bg-purple-50 border-purple-300' : 'bg-gray-50 border-gray-200'">
                    <label class="flex items-center justify-between cursor-pointer mb-0"
                           :class="caseEnabled ? 'mb-2' : ''">
                        <span class="text-[10px] font-semibold uppercase tracking-wider"
                              :class="caseEnabled ? 'text-purple-600' : 'text-gray-500'">Capinha</span>
                        <div class="relative" @click.prevent="caseEnabled = !caseEnabled; if(caseEnabled) { caseQty = caseQty || 1; } else { caseQty = 0; caseTotalOverride = null; }">
                            <div class="w-8 h-[18px] rounded-full transition-colors duration-200"
                                 :class="caseEnabled ? 'bg-purple-500' : 'bg-gray-300'"></div>
                            <div class="absolute top-[2px] left-[2px] w-[14px] h-[14px] bg-white rounded-full shadow transition-transform duration-200"
                                 :class="caseEnabled ? 'translate-x-[14px]' : ''"></div>
                        </div>
                    </label>

                    <div x-show="caseEnabled" x-transition x-collapse>
                        <div>
                            <label class="text-[9px] text-gray-400 font-medium block mb-0.5">Preço unitário (1 capinha)</label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400">R$</span>
                                <input type="number" :value="caseUnitPrice" readonly
                                       class="w-full pl-7 pr-2 py-1.5 text-xs font-semibold border rounded-md text-center bg-gray-100 border-purple-300 text-purple-800 cursor-not-allowed">
                            </div>
                        </div>

                        {{-- Resultado atual --}}
                        <div x-show="caseQty > 0 && caseUnitPrice > 10" x-transition class="mt-2 text-center">
                            <span class="text-[10px] text-gray-400">
                                <span x-text="caseQty"></span> capinha<span x-show="caseQty > 1">s</span> por
                                <strong x-text="'R$ ' + fmt(caseTotalOverride !== null ? caseTotalOverride : caseUnitPrice * caseQty)"></strong>
                                <span x-show="caseTotalOverride !== null && caseQty > 1" class="line-through text-gray-300 ml-1"
                                      x-text="'R$ ' + fmt(caseUnitPrice * caseQty)"></span>
                            </span>
                            <p class="text-xs font-bold text-purple-700">+R$ <span x-text="fmt(commissionEstimate.caseComm)"></span></p>
                        </div>

                        {{-- Sugestão de combos com desconto progressivo --}}
                        <div x-show="caseUnitPrice > 10" x-transition class="mt-2.5 pt-2.5 border-t border-purple-100">
                        <p class="text-[9px] font-semibold text-purple-500 uppercase tracking-wider mb-1.5">Ofereça combos ao cliente</p>
                        <div class="space-y-1"
                             x-data="{
                                 combos() {
                                     const p = caseUnitPrice;
                                     const base = 10;
                                     const p2 = Math.round(p * 2 * 0.85 / 5) * 5;
                                     const p3 = Math.round(p * 3 * 0.75 / 5) * 5;
                                     return [
                                         { qty: 1, total: p, discount: 0 },
                                         { qty: 2, total: p2, discount: Math.round((1 - p2 / (p * 2)) * 100) },
                                         { qty: 3, total: p3, discount: Math.round((1 - p3 / (p * 3)) * 100) },
                                     ];
                                 }
                             }">
                            <template x-for="combo in combos()" :key="combo.qty">
                                <button type="button"
                                        @click="caseQty = combo.qty; caseTotalOverride = combo.qty === 1 ? null : combo.total"
                                        class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-[11px] transition-all border"
                                        :class="caseQty === combo.qty
                                            ? 'bg-purple-100 border-purple-300 text-purple-800 font-bold shadow-sm'
                                            : 'bg-white border-gray-100 text-gray-600 hover:bg-purple-50 hover:border-purple-200'">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold"
                                              :class="caseQty === combo.qty ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-500'"
                                              x-text="combo.qty"></span>
                                        <span>
                                            <span x-text="combo.qty === 1 ? '1 capinha' : combo.qty + ' capinhas'"></span>
                                            <span class="font-bold ml-0.5" x-text="'R$ ' + fmt(combo.total)"></span>
                                        </span>
                                        <span x-show="combo.discount > 0"
                                              class="text-[9px] px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 font-bold"
                                              x-text="'-' + combo.discount + '%'"></span>
                                    </span>
                                    <span class="font-bold text-emerald-600"
                                          x-text="'+R$ ' + fmt(Math.max(0, combo.total - 10 * combo.qty) * 0.5)"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    </div>
                </div>

                {{-- Carregador --}}
                <div class="rounded-lg p-2.5 border transition-all duration-200"
                     :class="chargerEnabled && accessoryChargerPrice > 50 ? 'bg-purple-50 border-purple-300' : 'bg-gray-50 border-gray-200'">
                    <label class="flex items-center justify-between cursor-pointer"
                           :class="chargerEnabled ? 'mb-1.5' : ''">
                        <span class="text-[10px] font-semibold uppercase tracking-wider"
                              :class="chargerEnabled && accessoryChargerPrice > 50 ? 'text-purple-600' : 'text-gray-500'">Carregador</span>
                        <div class="relative" @click.prevent="chargerEnabled = !chargerEnabled; if(!chargerEnabled) { accessoryChargerPrice = 0; }">
                            <div class="w-8 h-[18px] rounded-full transition-colors duration-200"
                                 :class="chargerEnabled ? 'bg-purple-500' : 'bg-gray-300'"></div>
                            <div class="absolute top-[2px] left-[2px] w-[14px] h-[14px] bg-white rounded-full shadow transition-transform duration-200"
                                 :class="chargerEnabled ? 'translate-x-[14px]' : ''"></div>
                        </div>
                    </label>
                    <div x-show="chargerEnabled" x-transition x-collapse>
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
