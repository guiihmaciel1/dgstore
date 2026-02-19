<x-perfumes-admin-layout>
    <div class="p-4 max-w-5xl mx-auto">
        <div class="mb-3">
            <a href="{{ route('admin.perfumes.sales.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Voltar para vendas
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4" x-data="{
            customerId: '{{ old('perfume_customer_id') }}',
            customerSearch: '',
            productSearches: {},
            items: [{ perfume_product_id: '', quantity: 1 }],
            customers: @json($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone])),
            products: @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name . ($p->brand ? ' - ' . $p->brand : '') . ($p->size_ml ? ' (' . $p->size_ml . 'ml)' : ''), 'price' => (float) $p->sale_price, 'stock' => $p->stock_quantity])),
            discount: {{ old('discount', 0) }},
            paymentMethod: '{{ old('payment_method', 'cash') }}',
            installments: {{ old('installments', 1) }},
            get filteredCustomers() {
                if (!this.customerSearch) return this.customers;
                const search = this.customerSearch.toLowerCase();
                return this.customers.filter(c => 
                    c.name.toLowerCase().includes(search) || 
                    c.phone.includes(search)
                );
            },
            getFilteredProducts(index) {
                const search = (this.productSearches[index] || '').toLowerCase();
                if (!search) return this.products;
                return this.products.filter(p => p.name.toLowerCase().includes(search));
            },
            addItem() { this.items.push({ perfume_product_id: '', quantity: 1 }) },
            removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) },
            getPrice(id) { 
                let p = this.products.find(x => x.id === id); 
                return p ? p.price : 0 
            },
            getProductName(id) {
                let p = this.products.find(x => x.id === id);
                return p ? p.name : '';
            },
            getStock(id) {
                let p = this.products.find(x => x.id === id);
                return p ? p.stock : 0;
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

                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Nova Venda</h2>
                    <div class="flex gap-2 text-xs">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-medium">
                            {{ $customers->count() }} cliente(s)
                        </span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full font-medium">
                            {{ $products->count() }} produto(s)
                        </span>
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full font-medium">
                            {{ $products->where('stock_quantity', '>', 0)->count() }} em estoque
                        </span>
                    </div>
                </div>

                @if($customers->isEmpty())
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center gap-2 text-yellow-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <p class="font-medium">Nenhum cliente cadastrado</p>
                                <p class="text-sm">É necessário <a href="{{ route('admin.perfumes.customers.create') }}" class="underline font-medium">cadastrar um cliente</a> antes de criar uma venda.</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($products->isEmpty())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center gap-2 text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <p class="font-medium">Nenhum produto ativo</p>
                                <p class="text-sm">É necessário <a href="{{ route('admin.perfumes.products.create') }}" class="underline font-medium">cadastrar produtos</a> antes de criar uma venda.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <!-- Campo de busca -->
                                <div class="relative mb-1">
                                    <input type="text" 
                                           x-model="customerSearch" 
                                           placeholder="🔍 Buscar cliente por nome ou telefone..."
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 bg-gray-50">
                                </div>
                                
                                <!-- Select com filtro -->
                                <select name="perfume_customer_id" x-model="customerId" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 @error('perfume_customer_id') border-red-500 @enderror">
                                    <option value="">Selecione o cliente</option>
                                    <template x-for="c in filteredCustomers" :key="c.id">
                                        <option :value="c.id" x-text="c.name + ' - ' + c.phone"></option>
                                    </template>
                                </select>
                                
                                <!-- Contador de resultados -->
                                <p class="text-xs text-gray-500 mt-1" x-show="customerSearch">
                                    <span x-text="filteredCustomers.length"></span> cliente(s) encontrado(s)
                                </p>
                            </div>
                            
                            <a href="{{ route('admin.perfumes.customers.create') }}" target="_blank"
                               class="px-3 py-2 bg-gray-200 text-gray-800 text-sm rounded-lg hover:bg-gray-300 transition whitespace-nowrap">
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
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex gap-2 mb-2">
                                        <div class="flex-1">
                                            <!-- Campo de busca do produto -->
                                            <input type="text" 
                                                   :placeholder="'🔍 Buscar produto' + (index > 0 ? ' ' + (index + 1) : '') + '...'"
                                                   x-model="productSearches[index]"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white mb-1">
                                            
                                            <!-- Select com filtro -->
                                            <select :name="'items[' + index + '][perfume_product_id]'"
                                                    x-model="item.perfume_product_id"
                                                    required
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                                <option value="">Selecione o produto</option>
                                                <template x-for="p in getFilteredProducts(index)" :key="p.id">
                                                    <option :value="p.id" x-text="p.name + ' [Estoque: ' + p.stock + ']'"></option>
                                                </template>
                                            </select>
                                            
                                            <!-- Info do produto selecionado -->
                                            <div x-show="item.perfume_product_id" class="mt-1 text-xs">
                                                <span class="text-gray-600">Preço: </span>
                                                <span class="font-semibold text-gray-900" x-text="'R$ ' + getPrice(item.perfume_product_id).toFixed(2).replace('.', ',')"></span>
                                                <span class="mx-2">•</span>
                                                <span class="text-gray-600">Estoque: </span>
                                                <span class="font-semibold" :class="getStock(item.perfume_product_id) > 10 ? 'text-green-600' : getStock(item.perfume_product_id) > 0 ? 'text-yellow-600' : 'text-red-600'" x-text="getStock(item.perfume_product_id)"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="w-24">
                                            <label class="block text-[10px] text-gray-600 mb-1">Qtd</label>
                                            <input type="number" :name="'items[' + index + '][quantity]'"
                                                   x-model.number="item.quantity"
                                                   min="1"
                                                   :max="getStock(item.perfume_product_id)"
                                                   placeholder="Qtd"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                        </div>
                                        
                                        <div class="w-32">
                                            <label class="block text-[10px] text-gray-600 mb-1">Subtotal</label>
                                            <div class="px-3 py-2 text-sm font-semibold text-gray-900 bg-white rounded-lg border border-gray-200" 
                                                 x-text="'R$ ' + (item.perfume_product_id ? (getPrice(item.perfume_product_id) * item.quantity).toFixed(2).replace('.', ',') : '0,00')">
                                            </div>
                                        </div>
                                        
                                        <button type="button" @click="removeItem(index)"
                                                x-show="items.length > 1"
                                                class="self-end px-2 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Contador de resultados da busca -->
                                    <p class="text-[10px] text-gray-500" x-show="productSearches[index]">
                                        <span x-text="getFilteredProducts(index).length"></span> produto(s) encontrado(s)
                                    </p>
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
                            :disabled="!customerId || items.every(i => !i.perfume_product_id)"
                            class="px-4 py-2 text-sm bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        Registrar Venda
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-perfumes-admin-layout>
