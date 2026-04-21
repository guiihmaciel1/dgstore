<div class="mt-3 border-t border-gray-100 pt-3">
    <div @click="tradeIn.showEval = !tradeIn.showEval"
         class="flex items-center gap-1.5 cursor-pointer text-xs text-gray-400 select-none hover:text-amber-600 transition-colors">
        <span x-text="tradeIn.showEval ? '▾ Ocultar avaliador' : '▸ Avaliar seminovo (DGiFipe)'"></span>
    </div>

    <div x-show="tradeIn.showEval" x-collapse>
        <div class="bg-gray-50 rounded-[10px] p-4 mt-3">

            {{-- Modelo --}}
            <div class="mb-4">
                <label class="block text-[0.7rem] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Modelo</label>
                <div class="relative" @keydown.escape.prevent="evalDropdownOpen = false">
                    <button type="button" @click="evalDropdownOpen = !evalDropdownOpen"
                            class="w-full py-2.5 px-3.5 bg-white border border-gray-200 rounded-[10px] text-sm text-gray-900 outline-none cursor-pointer text-left flex items-center justify-between transition-all"
                            :class="evalDropdownOpen ? 'border-amber-600 ring-[3px] ring-amber-600/10' : ''">
                        <span :class="tradeIn.model ? 'text-gray-900 font-medium' : 'text-gray-400'"
                              x-text="tradeIn.model || 'Selecione o modelo...'"></span>
                        <span class="text-gray-400 text-xs transition-transform duration-200 inline-block"
                              :class="evalDropdownOpen ? 'rotate-180' : ''">&darr;</span>
                    </button>

                    <div x-show="evalDropdownOpen" x-cloak
                         @click.outside="evalDropdownOpen = false"
                         class="absolute z-50 mt-1.5 w-full bg-white rounded-xl shadow-[0_12px_28px_rgba(0,0,0,0.12),0_2px_8px_rgba(0,0,0,0.06)] border border-gray-200 overflow-hidden">

                        <div class="p-2.5 border-b border-gray-100">
                            <input type="text" x-model="evalSearch" x-ref="evalSearchInput"
                                   x-init="$watch('evalDropdownOpen', v => { if(v) $nextTick(() => $refs.evalSearchInput.focus()) })"
                                   placeholder="🔍 Buscar modelo..."
                                   @keydown.escape.prevent="evalDropdownOpen = false"
                                   class="w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-lg text-[13px] text-gray-900 outline-none focus:border-amber-600 focus:bg-white transition-colors">
                        </div>

                        <div class="max-h-[280px] overflow-y-auto py-1">
                            <template x-for="group in groupedTradeInModels(evalSearch)" :key="group.generation">
                                <div>
                                    <div class="block px-3.5 pt-2 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 sticky top-0"
                                         x-text="group.generation"></div>
                                    <template x-for="modelName in group.models" :key="modelName">
                                        <div @click="selectTradeInModel(modelName); evalDropdownOpen = false; evalSearch = ''"
                                             class="px-3.5 py-2.5 text-[13px] cursor-pointer border-b border-gray-50 transition-colors"
                                             :class="tradeIn.model === modelName
                                                 ? 'bg-amber-50 text-amber-600 font-semibold'
                                                 : 'text-gray-700 hover:bg-gray-100'">
                                            <span x-show="tradeIn.model === modelName" class="mr-1.5 text-amber-600">✓</span>
                                            <span x-text="modelName"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <div x-show="groupedTradeInModels(evalSearch).length === 0"
                                 class="py-5 text-center text-[13px] text-gray-400">
                                Nenhum modelo encontrado
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Armazenamento --}}
            <div class="mb-4">
                <label class="block text-[0.7rem] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Armazenamento</label>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="s in tradeInStorages" :key="s">
                        <button type="button" @click="tradeIn.storage = s; debouncedEvaluate()"
                                class="px-4 py-2 rounded-[10px] text-[13px] cursor-pointer border-none transition-colors"
                                :class="tradeIn.storage === s
                                    ? 'font-semibold bg-amber-600 text-white shadow-sm'
                                    : 'font-medium bg-white text-gray-700 hover:bg-gray-100'"
                                x-text="s"></button>
                    </template>
                </div>
            </div>

            {{-- Saúde da Bateria --}}
            <div class="mb-4">
                <label class="block text-[0.7rem] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Saúde da Bateria: <span class="text-gray-900" x-text="tradeIn.battery + '%'"></span>
                    <span class="ml-1.5 text-[11px] font-medium px-2 py-0.5 rounded-full"
                          :class="tradeIn.battery >= 90 ? 'bg-green-100 text-green-800' : (tradeIn.battery >= 80 ? 'bg-yellow-100 text-yellow-800' : (tradeIn.battery >= 70 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))"
                          x-text="tradeIn.battery >= 90 ? 'Excelente' : (tradeIn.battery >= 80 ? 'Bom' : (tradeIn.battery >= 70 ? 'Regular' : 'Ruim'))"></span>
                </label>
                <input type="range" x-model.number="tradeIn.battery" min="0" max="100" step="1"
                       @input.debounce.500ms="debouncedEvaluate()"
                       class="w-full h-2 rounded bg-gray-200 appearance-none cursor-pointer accent-amber-600">
                <div class="flex justify-between text-[11px] text-gray-400 mt-0.5">
                    <span>0%</span><span>100%</span>
                </div>
            </div>

            {{-- Estado do Aparelho --}}
            <div class="mb-4">
                <label class="block text-[0.7rem] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Estado do Aparelho</label>
                <div class="flex gap-2">
                    <button type="button" @click="tradeIn.deviceState = 'original'; debouncedEvaluate()"
                            class="flex-1 py-2.5 rounded-[10px] text-[13px] cursor-pointer border-none transition-colors"
                            :class="tradeIn.deviceState === 'original'
                                ? 'font-semibold bg-amber-600 text-white shadow-sm'
                                : 'font-medium bg-white text-gray-700 hover:bg-gray-100'">
                        Original
                    </button>
                    <button type="button" @click="tradeIn.deviceState = 'repaired'; debouncedEvaluate()"
                            class="flex-1 py-2.5 rounded-[10px] text-[13px] cursor-pointer border-none transition-colors"
                            :class="tradeIn.deviceState === 'repaired'
                                ? 'font-semibold bg-amber-600 text-white shadow-sm'
                                : 'font-medium bg-white text-gray-700 hover:bg-gray-100'">
                        Já foi aberto / trocou peça
                    </button>
                </div>
            </div>

            {{-- Acessórios --}}
            <div class="mb-2">
                <label class="block text-[0.7rem] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Acessórios
                    <span class="ml-1.5 text-[11px] font-medium px-2 py-0.5 rounded-full"
                          :class="(!tradeIn.noBox && !tradeIn.noCable) ? 'bg-green-100 text-green-800' : ((tradeIn.noBox && tradeIn.noCable) ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')"
                          x-text="(!tradeIn.noBox && !tradeIn.noCable) ? 'Completo (+3%)' : ((tradeIn.noBox && tradeIn.noCable) ? 'Nenhum (-3%)' : 'Parcial (0%)')"></span>
                </label>
                <label class="flex items-center gap-2.5 py-2.5 px-3 rounded-[10px] bg-white cursor-pointer mb-1.5 hover:bg-gray-100 transition-colors">
                    <input type="checkbox" x-model="tradeIn.noBox" @change="debouncedEvaluate()"
                           class="w-[18px] h-[18px] rounded accent-amber-600 cursor-pointer">
                    <span class="text-[13px] text-gray-700">Sem caixa</span>
                </label>
                <label class="flex items-center gap-2.5 py-2.5 px-3 rounded-[10px] bg-white cursor-pointer hover:bg-gray-100 transition-colors">
                    <input type="checkbox" x-model="tradeIn.noCable" @change="debouncedEvaluate()"
                           class="w-[18px] h-[18px] rounded accent-amber-600 cursor-pointer">
                    <span class="text-[13px] text-gray-700">Sem cabo</span>
                </label>
            </div>

            {{-- Loading --}}
            <div x-show="tradeIn.loading" class="flex items-center justify-center gap-2 p-2 text-[13px] text-gray-400">
                <svg class="w-4 h-4 shrink-0 animate-spin" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Calculando...
            </div>

            {{-- Resultado --}}
            <div x-show="tradeIn.result && !tradeIn.loading" x-transition class="mt-3">
                <div class="bg-white rounded-[10px] p-4 mb-2">
                    <div class="text-[0.65rem] font-semibold text-gray-500 uppercase tracking-wider">Preço Sugerido de Compra</div>
                    <div class="text-[1.75rem] font-extrabold text-emerald-600 tracking-tight" x-text="'R$ ' + fmt(tradeIn.result?.suggested_price)"></div>
                    <div class="text-xs text-gray-400 mt-0.5">Valor ideal para adquirir este aparelho.</div>
                </div>
                <div class="bg-white rounded-[10px] p-4 mb-2">
                    <div class="text-[0.65rem] font-semibold text-gray-500 uppercase tracking-wider">Preço Sugerido de Revenda</div>
                    <div class="text-2xl font-bold text-blue-600 tracking-tight" x-text="'R$ ' + fmt(tradeIn.result?.resale_price)"></div>
                    <div class="text-xs text-gray-400 mt-0.5">Margem de <span x-text="tradeIn.result?.resale_margin + '%'"></span> sobre a compra.</div>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <div class="bg-white rounded-[10px] p-3">
                        <div class="text-[0.6rem] font-semibold text-gray-500 uppercase tracking-wider">Média</div>
                        <div class="text-[1.1rem] font-semibold text-gray-900" x-text="'R$ ' + fmt(tradeIn.result?.market_average)"></div>
                    </div>
                    <div class="bg-white rounded-[10px] p-3">
                        <div class="text-[0.6rem] font-semibold text-gray-500 uppercase tracking-wider">Mediana</div>
                        <div class="text-[1.1rem] font-semibold text-gray-900" x-text="'R$ ' + fmt(tradeIn.result?.median)"></div>
                    </div>
                </div>
                <div class="bg-white rounded-[10px] p-3 mb-2 flex items-center justify-between">
                    <div>
                        <div class="text-[0.6rem] font-semibold text-gray-500 uppercase tracking-wider">Anúncios</div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[1.1rem] font-semibold text-gray-900" x-text="tradeIn.result?.listings_count"></span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                  :class="tradeIn.result?.confidence === 'high' ? 'bg-green-100 text-green-800' : (tradeIn.result?.confidence === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')"
                                  x-text="tradeIn.result?.confidence === 'high' ? 'Confiança Alta' : (tradeIn.result?.confidence === 'medium' ? 'Confiança Média' : 'Confiança Baixa')"></span>
                        </div>
                    </div>
                </div>
                <button @click="tradeIn.offeredInput = fmt(tradeIn.result?.suggested_price); recalculate()" type="button"
                        class="w-full py-2.5 rounded-[10px] text-sm font-semibold cursor-pointer border-none bg-amber-600 text-white hover:bg-amber-700 transition-colors">
                    Usar valor sugerido
                </button>
            </div>

            {{-- Erro --}}
            <div x-show="tradeIn.error && !tradeIn.loading" x-transition
                 class="p-3 rounded-[10px] bg-red-50 text-[13px] text-red-800 text-center mt-3">
                <span x-text="tradeIn.error"></span>
            </div>
        </div>
    </div>
</div>
