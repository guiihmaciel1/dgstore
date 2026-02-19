<x-perfumes-admin-layout>
    <div class="p-4 max-w-5xl mx-auto">
        <div class="mb-3">
            <a href="{{ route('admin.perfumes.sales.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Voltar para vendas
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4" x-data="{
            customerId: '{{ old('perfume_customer_id') }}',
            items: [{ perfume_product_id: '', quantity: 1 }],
            products: @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name . ($p->brand ? ' - ' . $p->brand : '') . ($p->size_ml ? ' (' . $p->size_ml . 'ml)' : ''), 'price' => (float) $p->sale_price])),
            discount: {{ old('discount', 0) }},
            paymentMethod: '{{ old('payment_method', 'cash') }}',
            installments: {{ old('installments', 1) }},
            addItem() { this.items.push({ perfume_product_id: '', quantity: 1 }) },
            removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) },
            getPrice(id) { 
                let p = this.products.find(x => x.id === id); 
                return p ? p.price : 0 
            },
            get subtotal() { 
                return this.items.reduce((s, i) => s + this.getPrice(i.perfume_product_id) * (parseInt(i.quantity) || 0), 0) 
            },
            get total() {
                return Math.max(0, this.subtotal - parseFloat(this.discount || 0))
            }
        }">
            <form method="POST" action="{{ route('admin.perfumes.sales.store') }}">
                @csrf

                <h2 class="text-lg font-bold text-gray-900 mb-4">Nova Venda</h2>

                <div class="space-y-4">
                    <!-- Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                        <div class="flex gap-2">
                            <select name="perfume_customer_id" x-model="customerId" required
                                    class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 @error('perfume_customer_id') border-red-500 @enderror">
                                <option value="">Selecione o cliente</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->formatted_phone }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('admin.perfumes.customers.create') }}" target="_blank"
                               class="px-3 py-2 bg-gray-200 text-gray-800 text-sm rounded-lg hover:bg-gray-300 transition">
                                + Novo
                            </a>
                        </div>
                        @error('perfume_customer_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Itens -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700">Produtos *</label>
                            <button type="button" @click="addItem()"
                                    class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                                + Adicionar produto
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-2 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <select :name="'items[' + index + '][perfume_product_id]'"
                                                x-model="item.perfume_product_id"
                                                required
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                            <option value="">Selecione o produto</option>
                                            <template x-for="p in products" :key="p.id">
                                                <option :value="p.id" x-text="p.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="w-24">
                                        <input type="number" :name="'items[' + index + '][quantity]'"
                                               x-model.number="item.quantity"
                                               min="1"
                                               placeholder="Qtd"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                    </div>
                                    <div class="w-32 flex items-center justify-end">
                                        <span class="text-sm font-medium text-gray-700" 
                                              x-text="'R$ ' + (item.perfume_product_id ? (getPrice(item.perfume_product_id) * item.quantity).toFixed(2).replace('.', ',') : '0,00')">
                                        </span>
                                    </div>
                                    <button type="button" @click="removeItem(index)"
                                            x-show="items.length > 1"
                                            class="px-2 text-red-600 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Resumo -->
                        <div class="mt-3 p-3 bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-200 rounded-lg space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Subtotal:</span>
                                <span class="font-semibold" x-text="'R$ ' + subtotal.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Desconto:</span>
                                <span class="font-semibold text-red-600" x-text="'- R$ ' + parseFloat(discount || 0).toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div class="flex justify-between text-base pt-2 border-t border-pink-300">
                                <span class="font-bold text-gray-900">Total:</span>
                                <span class="font-bold text-pink-700 text-lg" x-text="'R$ ' + total.toFixed(2).replace('.', ',')"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Desconto e Pagamento -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desconto (R$)</label>
                            <input type="number" name="discount" x-model.number="discount" step="0.01" min="0"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento *</label>
                            <select name="payment_method" x-model="paymentMethod" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                <option value="cash">Dinheiro</option>
                                <option value="card">Cartão</option>
                                <option value="pix">PIX</option>
                                <option value="mixed">Misto</option>
                            </select>
                        </div>
                    </div>

                    <!-- Parcelamento -->
                    <div x-show="paymentMethod === 'card'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parcelas</label>
                        <select name="installments" x-model.number="installments"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ $i }}x</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Observações -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea name="notes" rows="2"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
                    <a href="{{ route('admin.perfumes.sales.index') }}"
                       class="px-4 py-2 text-sm bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-medium">
                        Registrar Venda
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-perfumes-admin-layout>
