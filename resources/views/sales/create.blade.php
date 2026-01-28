<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('sales.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nova Venda
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}" x-data="saleForm()">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Produtos -->
                        <x-card title="Produtos">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar Produto</label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        x-model="searchTerm"
                                        @input.debounce.300ms="searchProducts"
                                        placeholder="Digite o nome, SKU ou IMEI..."
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md"
                                    >
                                    
                                    <!-- Resultados da busca -->
                                    <div x-show="searchResults.length > 0" x-cloak class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg rounded-md border border-gray-200 dark:border-gray-700 max-h-60 overflow-auto">
                                        <template x-for="product in searchResults" :key="product.id">
                                            <button 
                                                type="button"
                                                @click="addItem(product)"
                                                class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 flex justify-between items-center"
                                            >
                                                <div>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100" x-text="product.name"></span>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="' - ' + product.sku"></span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-green-600 dark:text-green-400 font-medium" x-text="product.formatted_price"></span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 block" x-text="product.stock + ' em estoque'"></span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Lista de itens -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Produto</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-24">Qtd</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-32">Preço</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-32">Subtotal</th>
                                            <th class="px-4 py-3 w-16"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="item.name"></span>
                                                    <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.id">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input 
                                                        type="number" 
                                                        :name="'items['+index+'][quantity]'"
                                                        x-model.number="item.quantity"
                                                        @input="updateTotals"
                                                        min="1"
                                                        :max="item.stock"
                                                        class="w-full text-center shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md"
                                                    >
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <input 
                                                        type="number" 
                                                        :name="'items['+index+'][unit_price]'"
                                                        x-model.number="item.price"
                                                        @input="updateTotals"
                                                        step="0.01"
                                                        min="0"
                                                        class="w-full text-right shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md"
                                                    >
                                                </td>
                                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    <span x-text="formatMoney(item.quantity * item.price)"></span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                
                                <div x-show="items.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    Adicione produtos à venda
                                </div>
                            </div>
                        </x-card>
                        
                        <!-- Observações -->
                        <x-card title="Observações">
                            <x-form-textarea name="notes" placeholder="Observações sobre a venda..." />
                        </x-card>
                    </div>
                    
                    <!-- Resumo -->
                    <div class="space-y-6">
                        <x-card title="Cliente">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="customerSearch"
                                    @input.debounce.300ms="searchCustomers"
                                    placeholder="Buscar cliente..."
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md"
                                >
                                <input type="hidden" name="customer_id" x-model="selectedCustomer.id">
                                
                                <div x-show="customerResults.length > 0" x-cloak class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg rounded-md border border-gray-200 dark:border-gray-700 max-h-40 overflow-auto">
                                    <template x-for="customer in customerResults" :key="customer.id">
                                        <button 
                                            type="button"
                                            @click="selectCustomer(customer)"
                                            class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700"
                                        >
                                            <span class="font-medium text-gray-900 dark:text-gray-100" x-text="customer.name"></span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400 block" x-text="customer.phone"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            
                            <div x-show="selectedCustomer.id" class="mt-3 p-3 bg-gray-100 dark:bg-gray-700 rounded-md">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100" x-text="selectedCustomer.name"></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedCustomer.phone"></p>
                                    </div>
                                    <button type="button" @click="clearCustomer" class="text-red-600 hover:text-red-900">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </x-card>
                        
                        <x-card title="Pagamento">
                            <div class="space-y-4">
                                <x-form-select 
                                    name="payment_method" 
                                    label="Forma de Pagamento" 
                                    required
                                    :options="collect($paymentMethods)->mapWithKeys(fn($m) => [$m->value => $m->label()])"
                                />
                                
                                <x-form-select 
                                    name="payment_status" 
                                    label="Status" 
                                    required
                                    :options="collect($paymentStatuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])"
                                    value="paid"
                                />
                                
                                <x-form-input name="installments" label="Parcelas" type="number" min="1" max="24" value="1" />
                            </div>
                        </x-card>
                        
                        <x-card title="Resumo">
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Subtotal</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="formatMoney(subtotal)"></dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Desconto</dt>
                                    <dd>
                                        <input 
                                            type="number" 
                                            name="discount"
                                            x-model.number="discount"
                                            @input="updateTotals"
                                            step="0.01"
                                            min="0"
                                            class="w-24 text-right shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md"
                                        >
                                    </dd>
                                </div>
                                <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <dt class="text-lg font-bold text-gray-900 dark:text-gray-100">Total</dt>
                                    <dd class="text-lg font-bold text-green-600 dark:text-green-400" x-text="formatMoney(total)"></dd>
                                </div>
                            </dl>
                            
                            <button 
                                type="submit" 
                                :disabled="items.length === 0"
                                class="mt-6 w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Finalizar Venda
                            </button>
                        </x-card>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function saleForm() {
            return {
                items: [],
                searchTerm: '',
                searchResults: [],
                customerSearch: '',
                customerResults: [],
                selectedCustomer: { id: '', name: '', phone: '' },
                discount: 0,
                subtotal: 0,
                total: 0,
                
                async searchProducts() {
                    if (this.searchTerm.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    
                    const response = await fetch(`{{ route('products.search') }}?q=${encodeURIComponent(this.searchTerm)}`);
                    this.searchResults = await response.json();
                },
                
                addItem(product) {
                    const existing = this.items.find(i => i.id === product.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.items.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            quantity: 1,
                            stock: product.stock
                        });
                    }
                    this.searchTerm = '';
                    this.searchResults = [];
                    this.updateTotals();
                },
                
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.updateTotals();
                },
                
                async searchCustomers() {
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }
                    
                    const response = await fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(this.customerSearch)}`);
                    this.customerResults = await response.json();
                },
                
                selectCustomer(customer) {
                    this.selectedCustomer = customer;
                    this.customerSearch = '';
                    this.customerResults = [];
                },
                
                clearCustomer() {
                    this.selectedCustomer = { id: '', name: '', phone: '' };
                },
                
                updateTotals() {
                    this.subtotal = this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                    this.total = Math.max(0, this.subtotal - this.discount);
                },
                
                formatMoney(value) {
                    return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
