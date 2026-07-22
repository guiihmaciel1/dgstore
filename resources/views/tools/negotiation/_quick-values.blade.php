<div class="bg-surface-raised rounded-xl border border-border px-4 py-3 sm:px-5 sm:py-4 mb-3">
    <div class="flex items-center justify-between mb-2">
        <label class="apple-section-title">Valores Rápidos</label>
        <a href="{{ route('marketing.index', ['tab' => 'prices']) }}"
           class="text-[11px] text-dg-500 no-underline flex items-center gap-0.5 hover:text-dg-100 transition-colors">
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Editar</span>
        </a>
    </div>
    <div class="flex flex-wrap gap-1.5">
        <template x-for="(qv, idx) in quickValues" :key="idx">
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
                                <span class="w-3.5 h-3.5 rounded-full border-2 shrink-0"
                                      :style="'background:' + v.color"
                                      :class="isQuickValueActive(qv.name + ' ' + v.label, v.value) ? 'border-white' : 'border-border-strong'"></span>
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

    {{-- Seminovos em Estoque --}}
    <template x-if="quickValuesUsed.length > 0">
        <div class="mt-3 pt-3 border-t border-border">
            <label class="text-[10px] font-semibold uppercase tracking-wider text-amber-600 mb-1.5 block">Seminovos</label>
            <div class="flex flex-wrap gap-1.5">
                <template x-for="(uv, uidx) in quickValuesUsed" :key="'used-' + uidx">
                    <button @click="selectQuickValue(uv.name, uv.value, uv.cost_price || 0, { battery: uv.battery_health, hasBox: uv.has_box, hasCable: uv.has_cable, notes: uv.notes, isUsed: true })" type="button"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer border-2 whitespace-nowrap transition-colors"
                            :class="isQuickValueActive(uv.name, uv.value)
                                ? 'border-amber-600 bg-amber-600 text-white'
                                : 'border-amber-200 bg-amber-50 text-amber-800 hover:border-amber-300'">
                        <span x-text="chipLabel(uv.name)"></span>
                        <span class="opacity-60 ml-1" x-text="'R$ ' + fmt(uv.value)"></span>
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>
