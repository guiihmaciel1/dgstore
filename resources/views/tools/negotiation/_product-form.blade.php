<div class="bg-surface-raised rounded-xl border border-border p-4 sm:p-5 mb-3">
    <div class="mb-4">
        <label class="apple-section-title mb-1.5">Descrição do produto</label>
        <input type="text" x-model="product.description" x-ref="descField"
               placeholder="Ex: iPhone 16 Pro Max 256GB"
               class="w-full px-4 py-3 bg-surface border border-border rounded-[10px] text-[15px] text-dg-100 outline-none focus:border-gray-900 focus:bg-surface-raised transition-colors">
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-dg-100 uppercase tracking-wider mb-1.5">Custo (p/ comissão)</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-dg-500 text-base font-medium">R$</span>
                <input type="text"
                       :value="productCost > 0 ? fmt(productCost) : ''"
                       @input="productCost = parseNum($event.target.value)"
                       placeholder="Auto"
                       class="w-full py-4 pl-11 pr-4 bg-surface border-2 border-border rounded-[10px] text-2xl font-bold text-dg-100 outline-none text-right focus:border-gray-900 focus:bg-surface-raised transition-colors">
            </div>
            <p class="text-[10px] text-dg-500 mt-1">Preenchido ao selecionar produto</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-dg-100 uppercase tracking-wider mb-1.5">Preço de Venda *</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-dg-500 text-base font-medium">R$</span>
                <input type="text" x-model="product.priceInput" x-ref="priceField"
                       placeholder="0,00"
                       class="w-full py-4 pl-11 pr-4 bg-surface border-2 border-border rounded-[10px] text-2xl font-bold text-dg-100 outline-none text-right focus:border-gray-900 focus:bg-surface-raised transition-colors">
            </div>
        </div>
    </div>
</div>
