<div class="bg-surface-raised rounded-xl border border-border px-4 py-3 sm:px-5 sm:py-4 mb-3">
    <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
        <div class="flex items-center gap-2 flex-wrap">
            <label class="apple-section-title">Valores Rápidos</label>
            <div class="inline-flex rounded-lg border border-border overflow-hidden text-[11px] font-semibold">
                <button type="button" @click="setSaleMode('pronta')"
                        class="px-2.5 py-1 border-none cursor-pointer transition-colors"
                        :class="saleMode === 'pronta' ? 'bg-surface text-white' : 'bg-transparent text-dg-500 hover:text-dg-300'">
                    Pronta Entrega
                </button>
                <button type="button" @click="setSaleMode('encomenda')"
                        class="px-2.5 py-1 border-none cursor-pointer transition-colors"
                        :class="saleMode === 'encomenda' ? 'bg-amber-600 text-white' : 'bg-transparent text-dg-500 hover:text-dg-300'">
                    Encomenda
                </button>
            </div>
        </div>
        <a href="{{ route('marketing.index', ['tab' => 'prices']) }}"
           class="text-[11px] text-dg-500 no-underline flex items-center gap-0.5 hover:text-dg-100 transition-colors">
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Editar</span>
        </a>
    </div>

    {{-- Encomenda: previsão de entrega --}}
    <template x-if="saleMode === 'encomenda'">
        <div class="mb-3 flex items-center gap-2 flex-wrap">
            <label class="text-[11px] font-semibold text-amber-500">Previsão de entrega</label>
            <input type="date" x-model="deliveryDate" :min="todayIso()" style="color-scheme: dark;"
                   class="px-2 py-1 rounded-lg text-xs bg-surface border border-amber-600 text-dg-100 focus:outline-none focus:border-amber-500">
            <span x-show="!deliveryDate" class="text-[11px] text-amber-500">Selecione a data para aparecer na simulação</span>
        </div>
    </template>

    <template x-if="saleMode === 'encomenda' && groupedQuickValues.length === 0">
        <div class="text-xs text-dg-500 mb-1">Nenhum valor de encomenda cadastrado no Marketing.</div>
    </template>

    {{-- Novos — agrupados por modelo --}}
    <template x-if="groupedQuickValues.length > 0">
        <div class="flex flex-col gap-1.5">
            <template x-for="(group, gi) in groupedQuickValues" :key="gi">
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="(qv, idx) in group.items" :key="idx">
                        <div class="inline-flex">
                            <template x-if="!qv.variants || qv.variants.length === 0">
                                <button @click="selectQuickValue(qv.name, qv.value, qv.cost_price || 0)" type="button"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer border-2 whitespace-nowrap transition-colors"
                                        :class="isQuickValueActive(qv.name, qv.value)
                                            ? 'border-gray-900 bg-surface text-white'
                                            : 'border-border bg-surface-raised text-dg-300 hover:border-border-strong'">
                                    <span x-text="chipLabel(qv.name)"></span>
                                    <span class="opacity-60 ml-1" x-text="'R$ ' + fmt(qv.value)"></span>
                                </button>
                            </template>
                            <template x-if="qv.variants && qv.variants.length > 0">
                                <div class="inline-flex items-center rounded-lg border-2 overflow-hidden transition-colors"
                                     :class="isQuickValueActiveByName(qv.name) ? 'border-gray-900' : 'border-border'">
                                    <span class="py-1.5 pl-3 pr-2 text-xs font-semibold text-dg-300 whitespace-nowrap" x-text="chipLabel(qv.name)"></span>
                                    <template x-for="(v, vi) in qv.variants" :key="vi">
                                        <button @click="selectQuickValue(qv.name + ' ' + v.label, v.value, v.cost_price || 0)" type="button"
                                                class="px-2 py-1 cursor-pointer border-none flex items-center gap-1 transition-colors"
                                                :class="isQuickValueActive(qv.name + ' ' + v.label, v.value)
                                                    ? 'bg-surface'
                                                    : 'bg-transparent hover:bg-surface-overlay'"
                                                :title="v.label + ' - R$ ' + fmt(v.value)">
                                            <template x-if="!v.colors || v.colors.length <= 1">
                                                <span class="w-3.5 h-3.5 rounded-full border-2 shrink-0"
                                                      :style="'background:' + v.color"
                                                      :class="isQuickValueActive(qv.name + ' ' + v.label, v.value) ? 'border-white' : 'border-border-strong'"></span>
                                            </template>
                                            <template x-if="v.colors && v.colors.length > 1">
                                                <span class="flex -space-x-1">
                                                    <template x-for="(c, ci) in v.colors" :key="ci">
                                                        <span class="w-3 h-3 rounded-full border-2 shrink-0"
                                                              :style="'background:' + c.hex"
                                                              :class="isQuickValueActive(qv.name + ' ' + v.label, v.value) ? 'border-white' : 'border-border-strong'"></span>
                                                    </template>
                                                </span>
                                            </template>
                                            <span class="text-[11px] font-semibold"
                                                  :class="isQuickValueActive(qv.name + ' ' + v.label, v.value) ? 'text-white' : 'text-dg-500'"
                                                  x-text="fmt(v.value)"></span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </template>

    {{-- Seminovos em Estoque --}}
    <template x-if="quickValuesUsed.length > 0">
        <div class="mt-3 pt-3 border-t border-border">
            <label class="text-[10px] font-semibold uppercase tracking-wider text-amber-600 mb-1.5 block">Seminovos</label>
            <div class="flex flex-wrap gap-1.5">
                <template x-for="(uv, uidx) in quickValuesUsed" :key="'used-' + uidx">
                    <button @click="if (uv.in_repair) return; selectQuickValue(uv.name, uv.value, uv.cost_price || 0, { battery: uv.battery_health, hasBox: uv.has_box, hasCable: uv.has_cable, notes: uv.notes, isUsed: true })" type="button"
                            :disabled="uv.in_repair"
                            :title="uv.in_repair ? 'Na assistência — indisponível para simulação' : ''"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold border-2 whitespace-nowrap transition-colors"
                            :style="uv.in_repair ? 'opacity: 0.45; cursor: not-allowed;' : 'cursor: pointer;'"
                            :class="isQuickValueActive(uv.name, uv.value)
                                ? 'border-amber-600 bg-amber-600 text-white'
                                : 'border-amber-200 bg-amber-50 text-amber-800 hover:border-amber-300'">
                        <span x-show="uv.in_repair">🔧</span>
                        <span x-text="chipLabel(uv.name)"></span>
                        <span class="opacity-60 ml-1" x-text="uv.in_repair ? 'Assistência' : 'R$ ' + fmt(uv.value)"></span>
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>
