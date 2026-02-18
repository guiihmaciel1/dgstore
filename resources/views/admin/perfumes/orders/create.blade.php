<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Novo Pedido</h2>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.perfumes.orders.index') }}"
           class="text-sm text-pink-600 hover:text-pink-700 font-medium inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar aos pedidos
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6"
         x-data="{
            items: [{ perfume_product_id: '', quantity: 1 }],
            products: @json($products->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name . ($p->brand ? ' - ' . $p->brand : '') . ($p->size_ml ? ' (' . $p->size_ml . 'ml)' : ''),
                'price' => (float) $p->sale_price
            ])),
            addItem() { this.items.push({ perfume_product_id: '', quantity: 1 }) },
            removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) },
            getPrice(id) { let p = this.products.find(x => x.id === id); return p ? p.price : 0 },
            get subtotal() { return this.items.reduce((s, i) => s + this.getPrice(i.perfume_product_id) * (parseInt(i.quantity) || 0), 0) }
         }">
        <form method="POST" action="{{ route('admin.perfumes.orders.store') }}">
            @csrf

            <div class="space-y-6">
                {{-- Retailer --}}
                <div>
                    <label for="perfume_retailer_id" class="block text-sm font-medium text-gray-700 mb-1">Lojista *</label>
                    <select name="perfume_retailer_id" id="perfume_retailer_id" required
                            class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('perfume_retailer_id') border-red-300 @enderror">
                        <option value="">Selecione o lojista</option>
                        @foreach($retailers as $r)
                            <option value="{{ $r->id }}" {{ old('perfume_retailer_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                    @error('perfume_retailer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Payment method --}}
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Forma de pagamento *</label>
                    <select name="payment_method" id="payment_method" required
                            class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500">
                        <option value="pix" {{ old('payment_method') === 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="consignment" {{ old('payment_method') === 'consignment' ? 'selected' : '' }}>Consignação</option>
                    </select>
                </div>

                {{-- Dynamic items --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-700">Itens do pedido</label>
                        <button type="button" @click="addItem()"
                                class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                            + Adicionar item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex flex-wrap items-end gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-1 min-w-[200px]">
                                    <label :for="'product-' + index" class="block text-xs font-medium text-gray-500 mb-1">Produto</label>
                                    <select :name="'items[' + index + '][perfume_product_id]'"
                                            :id="'product-' + index"
                                            x-model="item.perfume_product_id"
                                            required
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                                        <option value="">Selecione</option>
                                        <template x-for="p in products" :key="p.id">
                                            <option :value="p.id" x-text="p.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="w-24">
                                    <label :for="'qty-' + index" class="block text-xs font-medium text-gray-500 mb-1">Qtd</label>
                                    <input type="number" :name="'items[' + index + '][quantity]'"
                                           :id="'qty-' + index"
                                           x-model.number="item.quantity"
                                           min="1"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                                </div>
                                <div class="w-28">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Preço unit.</label>
                                    <p class="py-2 text-sm font-medium text-gray-700" x-text="'R$ ' + (item.perfume_product_id ? getPrice(item.perfume_product_id).toFixed(2).replace('.', ',') : '0,00')"></p>
                                </div>
                                <div>
                                    <button type="button" @click="removeItem(index)"
                                            x-show="items.length > 1"
                                            class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 p-4 bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-200 rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-semibold text-gray-800">Subtotal:</span>
                        <span class="text-lg font-bold text-pink-700" x-text="'R$ ' + subtotal.toFixed(2).replace('.', ',')"></span>
                    </div>
                </div>

                {{-- Discount --}}
                <div>
                    <label for="discount" class="block text-sm font-medium text-gray-700 mb-1">Desconto (R$)</label>
                    <input type="number" name="discount" id="discount" step="0.01" min="0" value="{{ old('discount', 0) }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500">
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex items-center gap-4">
                <button type="submit"
                        class="px-5 py-2.5 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                    Criar Pedido
                </button>
                <a href="{{ route('admin.perfumes.orders.index') }}"
                   class="text-sm text-pink-600 hover:text-pink-700 font-medium">Cancelar</a>
            </div>
        </form>
    </div>
</x-perfumes-admin-layout>
