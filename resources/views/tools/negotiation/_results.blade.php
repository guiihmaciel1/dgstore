{{-- Loading --}}
<div x-show="cardLoading" class="bg-white rounded-xl border border-gray-200 p-12 text-center">
    <div class="inline-block w-10 h-10 border-4 border-gray-100 border-t-gray-900 rounded-full animate-spin"></div>
</div>

{{-- Estado vazio --}}
<div x-show="!cardLoading && cardResults.length === 0 && productPrice <= 0" class="bg-white rounded-xl border border-gray-200 p-12 text-center">
    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
    </svg>
    <p class="text-sm text-gray-400">Preencha o valor do produto para montar a proposta</p>
</div>

{{-- Resultados --}}
<div x-show="!cardLoading && cardResults.length > 0" x-transition>
    {{-- Pix --}}
    <div x-show="cardBalance > 0" class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 sm:px-5 sm:py-4 flex items-center justify-between mb-3">
        <div>
            <div class="text-[15px] font-bold text-emerald-600">
                Pix / Dinheiro
                <span x-show="pixDiscount > 0"
                      class="ml-1.5 text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-600 text-white"
                      x-text="'-' + pixDiscount + '% off'"></span>
            </div>
            <div class="text-xs text-gray-500">Melhor preço - sem taxa</div>
        </div>
        <div class="text-right">
            <div x-show="pixDiscount > 0"
                 class="text-[13px] font-medium text-gray-400 line-through"
                 x-text="'R$ ' + fmt(cardBalance)"></div>
            <div class="text-[22px] font-extrabold text-emerald-600" x-text="'R$ ' + fmt(pixPrice)"></div>
        </div>
    </div>

    {{-- Presets --}}
    <div class="flex flex-wrap gap-1.5 mb-3">
        <template x-for="p in presets" :key="p.key">
            <button @click="setSelection(p.key)" type="button"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer border-2 transition-colors"
                    :class="activePreset === p.key
                        ? 'border-gray-900 bg-gray-900 text-white'
                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'"
                    x-text="p.label"></button>
        </template>
    </div>

    {{-- Tabela --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="grid grid-cols-[32px_1fr_auto] bg-gray-900 text-white text-[11px] font-semibold uppercase tracking-wider">
            <div class="py-2.5 pl-2.5 flex items-center">
                <input type="checkbox" :checked="cardResults.every(r => r.selected)" @change="cardResults.forEach(r => r.selected = $event.target.checked); activePreset = $event.target.checked ? 'all' : null"
                       class="w-[15px] h-[15px] accent-white cursor-pointer">
            </div>
            <div class="py-2.5 px-3.5">Forma</div>
            <div class="py-2.5 px-3.5 text-right">Cliente Paga</div>
        </div>
        <template x-for="(row, idx) in cardResults" :key="idx">
            <div class="grid grid-cols-[32px_1fr_auto] items-center border-t border-gray-100 transition-opacity duration-150"
                 :class="row.selected ? (idx % 2 === 0 ? 'bg-white' : 'bg-gray-50') : 'bg-gray-100 opacity-40'">
                <div class="py-2.5 pl-2.5 flex items-center">
                    <input type="checkbox" x-model="row.selected" @change="activePreset = null" class="w-[15px] h-[15px] accent-gray-900 cursor-pointer">
                </div>
                <div class="py-2.5 px-3.5">
                    <div class="text-sm font-semibold text-gray-900" x-text="row.label"></div>
                    <div x-show="row.installments > 1" class="text-xs text-gray-500" x-text="row.installments + 'x de R$ ' + fmt(row.installment_value)"></div>
                    <div class="text-[11px] text-gray-400" x-text="'Taxa ' + row.mdr_rate.toString().replace('.', ',') + '%'"></div>
                </div>
                <div class="py-2.5 px-3.5 text-right">
                    <div class="text-base font-bold text-gray-900" x-text="'R$ ' + fmt(row.gross_amount)"></div>
                    <div class="text-[11px] text-red-500" x-text="'taxa R$ ' + fmt(row.fee_amount)"></div>
                </div>
            </div>
        </template>
    </div>

    {{-- Resumo Negociação --}}
    <div x-show="productPrice > 0" class="mt-3 bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
        <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Resumo</div>
        <div class="flex justify-between py-1 text-[13px]">
            <span class="text-gray-700" x-text="product.description || 'Produto'"></span>
            <span class="font-bold text-gray-900" x-text="'R$ ' + fmt(productPrice)"></span>
        </div>
        <template x-if="tradeInValue > 0">
            <div class="flex justify-between py-1 text-[13px]">
                <span class="text-amber-600" x-text="'Trade-in: ' + (tradeIn.model ? tradeIn.model + ' ' + (tradeIn.storage || '') : 'Seminovo')"></span>
                <span class="font-bold text-amber-600" x-text="'- R$ ' + fmt(tradeInValue)"></span>
            </div>
        </template>
        <template x-if="downPayment > 0">
            <div class="flex justify-between py-1 text-[13px]">
                <span class="text-emerald-600">Entrada (Pix)</span>
                <span class="font-bold text-emerald-600" x-text="'- R$ ' + fmt(downPayment)"></span>
            </div>
        </template>
        <div class="flex justify-between pt-2 mt-1 border-t border-gray-100">
            <span class="text-sm font-bold text-gray-900">Saldo no cartão</span>
            <span class="text-lg font-extrabold text-gray-900" x-text="'R$ ' + fmt(cardBalance)"></span>
        </div>
        <template x-if="pixDiscount > 0 && cardBalance > 0">
            <div class="flex justify-between py-1 text-[13px]">
                <span class="text-emerald-600" x-text="'Desconto Pix (' + pixDiscount + '%)'"></span>
                <span class="font-bold text-emerald-600" x-text="'R$ ' + fmt(pixPrice)"></span>
            </div>
        </template>
    </div>

    {{-- Ações finais --}}
    <div class="flex flex-col sm:flex-row gap-2.5 mt-3">
        <button @click="copyMessage()" type="button"
                class="flex-1 py-3.5 rounded-xl text-sm font-bold cursor-pointer border-none flex items-center justify-center gap-2 transition-colors"
                :class="copied ? 'bg-emerald-600 text-white' : 'bg-gray-900 text-white hover:bg-gray-800'">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <span x-text="copied ? 'Copiado!' : 'Copiar proposta'"></span>
        </button>
        <a :href="'https://wa.me/?text=' + encodeURIComponent(buildMessage())"
           target="_blank"
           class="py-3.5 px-6 rounded-xl text-sm font-bold cursor-pointer border-none bg-green-600 text-white flex items-center justify-center gap-2 no-underline hover:bg-green-700 transition-colors">
            <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            WhatsApp
        </a>
    </div>
</div>
