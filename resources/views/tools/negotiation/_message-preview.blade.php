<div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
        <label class="apple-section-title">Preview da mensagem</label>
        <div class="flex gap-2 flex-wrap">
            <button @click="copyMessage()" type="button"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer border-none flex items-center gap-1 transition-colors"
                    :class="copied ? 'bg-emerald-600 text-white' : 'bg-gray-900 text-white hover:bg-gray-800'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
            </button>
            <a :href="'https://wa.me/?text=' + encodeURIComponent(buildMessage())"
               target="_blank"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer border-none bg-green-600 text-white flex items-center gap-1 no-underline hover:bg-green-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                WhatsApp
            </a>
            <button @click="openSaveModal()" type="button"
                    x-show="productPrice > 0"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer border-none bg-indigo-600 text-white flex items-center gap-1 hover:bg-indigo-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Salvar p/ Cliente
            </button>
        </div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-[13px] text-gray-700 whitespace-pre-wrap font-mono leading-relaxed min-h-[100px] max-h-[520px] overflow-y-auto"
         x-text="productPrice > 0 ? buildMessage() : 'Preencha o valor do produto para visualizar a mensagem...'"></div>
</div>

{{-- Modal: Salvar Simulação p/ Cliente --}}
<div x-show="saveModal.open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="saveModal.open = false">
    <div class="fixed inset-0 bg-black/50" @click="saveModal.open = false"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
        <button @click="saveModal.open = false" type="button"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 bg-transparent border-none cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <h3 class="text-lg font-bold text-gray-900 mb-1">Salvar Simulação</h3>
        <p class="text-sm text-gray-500 mb-4">Vincule esta proposta a um cliente para consultar depois.</p>

        {{-- Busca de cliente --}}
        <div x-show="!saveModal.selectedCustomer" class="relative mb-4">
            <label class="text-xs font-semibold text-gray-600 mb-1 block">Buscar cliente</label>
            <input type="text"
                   x-model="saveModal.search"
                   @input.debounce.300ms="searchSaveCustomers()"
                   placeholder="Nome ou telefone..."
                   class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:outline-none transition-colors">
            
            <div x-show="saveModal.results.length > 0" x-cloak
                 class="absolute z-50 mt-1 w-full bg-white shadow-xl rounded-lg border border-gray-200 max-h-48 overflow-y-auto">
                <template x-for="c in saveModal.results" :key="c.id">
                    <button type="button" @click="selectSaveCustomer(c)"
                            class="w-full px-3 py-2.5 text-left border-b border-gray-100 cursor-pointer bg-white hover:bg-gray-50 transition-colors">
                        <span class="font-medium text-gray-900 text-sm" x-text="c.name"></span>
                        <span class="text-xs text-gray-500 block" x-text="c.phone"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Cliente selecionado --}}
        <div x-show="saveModal.selectedCustomer" class="mb-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200 flex items-center justify-between">
            <div>
                <span class="font-semibold text-gray-900 text-sm" x-text="saveModal.selectedCustomer?.name"></span>
                <span class="text-xs text-gray-600 block" x-text="saveModal.selectedCustomer?.phone"></span>
            </div>
            <button type="button" @click="saveModal.selectedCustomer = null; saveModal.search = ''"
                    class="text-indigo-600 hover:text-indigo-800 bg-transparent border-none cursor-pointer text-xs font-semibold">
                Trocar
            </button>
        </div>

        {{-- Notas opcionais --}}
        <div class="mb-4">
            <label class="text-xs font-semibold text-gray-600 mb-1 block">Observação (opcional)</label>
            <input type="text" x-model="saveModal.notes" placeholder="Ex: Cliente quer receber segunda..."
                   class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:outline-none transition-colors"
                   maxlength="500">
        </div>

        {{-- Resumo --}}
        <div class="bg-gray-50 rounded-lg p-3 mb-4 text-xs text-gray-600 space-y-1">
            <div class="flex justify-between"><span>Produto:</span><span class="font-medium text-gray-900" x-text="product.description || '-'"></span></div>
            <div class="flex justify-between"><span>Preço:</span><span class="font-medium text-gray-900" x-text="'R$ ' + fmt(productPrice)"></span></div>
            <div x-show="tradeInValue > 0" class="flex justify-between"><span>Seminovo:</span><span class="font-medium text-gray-900" x-text="'R$ ' + fmt(tradeInValue)"></span></div>
        </div>

        {{-- Botões --}}
        <div class="flex gap-3">
            <button @click="saveModal.open = false" type="button"
                    class="flex-1 px-4 py-2.5 rounded-lg text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border-none cursor-pointer transition-colors">
                Cancelar
            </button>
            <button @click="saveSnapshot()" type="button"
                    :disabled="!saveModal.selectedCustomer || saveModal.saving"
                    class="flex-1 px-4 py-2.5 rounded-lg text-sm font-semibold text-white border-none cursor-pointer transition-colors"
                    :class="saveModal.selectedCustomer && !saveModal.saving ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-300 cursor-not-allowed'">
                <span x-show="!saveModal.saving">Salvar</span>
                <span x-show="saveModal.saving">Salvando...</span>
            </button>
        </div>
    </div>
</div>
