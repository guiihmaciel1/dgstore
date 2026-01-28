<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('quotations.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nova Cotação
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('quotations.store') }}" x-data="quotationForm()">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Fornecedor -->
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Fornecedor <span class="text-red-500">*</span>
                            </label>
                            <select name="supplier_id" id="supplier_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Selecione um fornecedor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ ($selectedSupplierId ?? old('supplier_id')) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Data da Cotação -->
                        <div>
                            <label for="quoted_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Data da Cotação <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="quoted_at" id="quoted_at" required
                                   value="{{ old('quoted_at', date('Y-m-d')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('quoted_at')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Produto -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Produto <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="product_type" value="free" x-model="productType" class="text-indigo-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Nome livre</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="product_type" value="existing" x-model="productType" class="text-indigo-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Produto existente</span>
                                    </label>
                                </div>

                                <!-- Nome livre -->
                                <div x-show="productType === 'free'">
                                    <input type="text" name="product_name" id="product_name"
                                           placeholder="Ex: iPhone 15 Pro Max 256GB"
                                           :required="productType === 'free'"
                                           value="{{ old('product_name') }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <!-- Produto existente -->
                                <div x-show="productType === 'existing'" class="relative">
                                    <input type="hidden" name="product_id" x-model="selectedProductId">
                                    <input type="text" 
                                           x-model="productSearch"
                                           @input.debounce.300ms="searchProducts"
                                           @focus="showResults = true"
                                           placeholder="Buscar produto cadastrado..."
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    
                                    <!-- Resultados da busca -->
                                    <div x-show="showResults && searchResults.length > 0" 
                                         @click.away="showResults = false"
                                         class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
                                        <template x-for="product in searchResults" :key="product.id">
                                            <div @click="selectProduct(product)"
                                                 class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                                <div class="font-medium text-gray-900 dark:text-gray-100" x-text="product.name"></div>
                                                <div class="text-sm text-gray-500" x-text="'SKU: ' + product.sku"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('product_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preço e Quantidade -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="unit_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Preço Unitário <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">R$</span>
                                    <input type="number" name="unit_price" id="unit_price" required
                                           step="0.01" min="0.01"
                                           value="{{ old('unit_price') }}"
                                           class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                @error('unit_price')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Quantidade
                                </label>
                                <input type="number" name="quantity" id="quantity"
                                       step="0.01" min="0.01"
                                       value="{{ old('quantity', 1) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            
                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Unidade
                                </label>
                                <select name="unit" id="unit"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="un" {{ old('unit') == 'un' ? 'selected' : '' }}>Unidade (un)</option>
                                    <option value="cx" {{ old('unit') == 'cx' ? 'selected' : '' }}>Caixa (cx)</option>
                                    <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Quilograma (kg)</option>
                                    <option value="pç" {{ old('unit') == 'pç' ? 'selected' : '' }}>Peça (pç)</option>
                                    <option value="par" {{ old('unit') == 'par' ? 'selected' : '' }}>Par</option>
                                </select>
                            </div>
                        </div>

                        <!-- Observações -->
                        <x-form-textarea name="notes" label="Observações" :value="old('notes')" />
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="{{ route('quotations.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Salvar Cotação
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        function quotationForm() {
            return {
                productType: 'free',
                productSearch: '',
                selectedProductId: null,
                searchResults: [],
                showResults: false,

                async searchProducts() {
                    if (this.productSearch.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route('quotations.products.search') }}?q=${encodeURIComponent(this.productSearch)}`);
                        this.searchResults = await response.json();
                        this.showResults = true;
                    } catch (error) {
                        console.error('Erro ao buscar produtos:', error);
                    }
                },

                selectProduct(product) {
                    this.selectedProductId = product.id;
                    this.productSearch = product.name;
                    
                    // Preenche o nome do produto também
                    document.getElementById('product_name').value = product.name;
                    
                    this.showResults = false;
                    this.searchResults = [];
                }
            }
        }
    </script>
</x-app-layout>
