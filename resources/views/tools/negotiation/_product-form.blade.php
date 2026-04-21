<div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-3">
    <div class="mb-4">
        <label class="apple-section-title mb-1.5">Descrição do produto</label>
        <input type="text" x-model="product.description" x-ref="descField"
               placeholder="Ex: iPhone 16 Pro Max 256GB"
               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-[10px] text-[15px] text-gray-900 outline-none focus:border-gray-900 focus:bg-white transition-colors">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-900 uppercase tracking-wider mb-1.5">Preço de Venda *</label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-base font-medium">R$</span>
            <input type="text" x-model="product.priceInput" x-ref="priceField"
                   placeholder="0,00"
                   class="w-full py-4 pl-11 pr-4 bg-gray-50 border-2 border-gray-200 rounded-[10px] text-2xl font-bold text-gray-900 outline-none text-right focus:border-gray-900 focus:bg-white transition-colors">
        </div>
    </div>
</div>
