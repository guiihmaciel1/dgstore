<x-perfumes-admin-layout>
    <div class="p-4 max-w-4xl mx-auto">
        <div class="mb-3">
            <a href="{{ route('admin.perfumes.reservations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Voltar para encomendas
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4" x-data="{
            customerId: '{{ old('perfume_customer_id') }}',
            customerSearch: '',
            productId: '{{ old('perfume_product_id') }}',
            productSearch: '',
            productPrice: {{ old('product_price', 0) }},
            depositAmount: {{ old('deposit_amount', 0) }},
            initialPayment: {{ old('initial_payment', 0) }},
            hasInitialPayment: {{ old('initial_payment') ? 'true' : 'false' }},
            customers: @json($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone])),
            products: @json($products->map(fn($p) => [
                'id' => $p->id, 
                'name' => $p->name . ($p->brand ? ' - ' . $p->brand : ''), 
                'price' => (float) $p->sale_price, 
                'stock' => $p->stock_quantity
            ])),
            get filteredCustomers() {
                if (!this.customerSearch) return this.customers;
                const search = this.customerSearch.toLowerCase();
                return this.customers.filter(c => 
                    c.name.toLowerCase().includes(search) || 
                    c.phone.includes(search)
                );
            },
            get filteredProducts() {
                if (!this.productSearch) return this.products;
                const search = this.productSearch.toLowerCase();
                return this.products.filter(p => p.name.toLowerCase().includes(search));
            },
            updatePrice() {
                if (this.productId) {
                    const p = this.products.find(x => x.id === this.productId);
                    if (p) this.productPrice = p.price;
                }
            }
        }">
            <form method="POST" action="{{ route('admin.perfumes.reservations.store') }}">
                @csrf

                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Nova Encomenda</h2>
                    <div class="flex gap-2 text-xs">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-medium">
                            {{ $customers->count() }} cliente(s)
                        </span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full font-medium">
                            {{ $products->count() }} produto(s)
                        </span>
                    </div>
                </div>

                @if($customers->isEmpty())
                    <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center gap-2 text-yellow-800 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p><span class="font-medium">Nenhum cliente cadastrado.</span> <a href="{{ route('admin.perfumes.customers.create') }}" class="underline font-medium">Cadastrar cliente</a></p>
                        </div>
                    </div>
                @endif

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                        
                        <!-- Campo de busca -->
                        <input type="text" 
                               x-model="customerSearch" 
                               placeholder="🔍 Buscar cliente por nome ou telefone..."
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 bg-gray-50 mb-1">
                        
                        <!-- Select com filtro -->
                        <select name="perfume_customer_id" x-model="customerId" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 @error('perfume_customer_id') border-red-500 @enderror">
                            <option value="">Selecione o cliente</option>
                            <template x-for="c in filteredCustomers" :key="c.id">
                                <option :value="c.id" x-text="c.name + ' - ' + c.phone"></option>
                            </template>
                        </select>
                        
                        <!-- Contador -->
                        <p class="text-xs text-gray-500 mt-1" x-show="customerSearch">
                            <span x-text="filteredCustomers.length"></span> cliente(s) encontrado(s)
                        </p>
                        
                        @error('perfume_customer_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                        
                        <!-- Campo de busca -->
                        <input type="text" 
                               x-model="productSearch" 
                               placeholder="🔍 Buscar produto..."
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 bg-gray-50 mb-1">
                        
                        <!-- Select com filtro -->
                        <select name="perfume_product_id" x-model="productId" @change="updatePrice()"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                            <option value="">Produto não disponível (descrever abaixo)</option>
                            <template x-for="p in filteredProducts" :key="p.id">
                                <option :value="p.id" x-text="p.name + ' [Estoque: ' + p.stock + ']'"></option>
                            </template>
                        </select>
                        
                        <!-- Contador -->
                        <p class="text-xs text-gray-500 mt-1" x-show="productSearch">
                            <span x-text="filteredProducts.length"></span> produto(s) encontrado(s)
                        </p>
                    </div>

                    <div x-show="!productId">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição do Produto *</label>
                        <textarea name="product_description" rows="2"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">{{ old('product_description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Descreva o produto que será encomendado</p>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Preço do Produto *</label>
                            <input type="number" name="product_price" x-model.number="productPrice" step="0.01" min="0" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 @error('product_price') border-red-500 @enderror">
                            @error('product_price')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor do Sinal *</label>
                            <input type="number" name="deposit_amount" x-model.number="depositAmount" step="0.01" min="0" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 @error('deposit_amount') border-red-500 @enderror">
                            @error('deposit_amount')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vencimento</label>
                            <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        </div>
                    </div>

                    <!-- Pagamento Inicial -->
                    <div class="border-t pt-3">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <input type="checkbox" x-model="hasInitialPayment" class="rounded border-gray-300">
                            Registrar pagamento inicial
                        </label>

                        <div x-show="hasInitialPayment" class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Valor</label>
                                <input type="number" name="initial_payment" x-model.number="initialPayment" step="0.01" min="0"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Forma</label>
                                <select name="payment_method"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                    <option value="pix">PIX</option>
                                    <option value="cash">Dinheiro</option>
                                    <option value="card">Cartão</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea name="notes" rows="2"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
                    <a href="{{ route('admin.perfumes.reservations.index') }}"
                       class="px-4 py-2 text-sm bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">
                        Registrar Encomenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-perfumes-admin-layout>
