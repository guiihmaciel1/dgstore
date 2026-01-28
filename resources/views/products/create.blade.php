<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Novo Produto
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-input name="name" label="Nome do Produto" required class="md:col-span-2" />
                        
                        <div class="md:col-span-2 flex gap-4">
                            <div class="flex-1">
                                <x-form-input name="sku" label="SKU" required />
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="generateSku()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                                    Gerar SKU
                                </button>
                            </div>
                        </div>
                        
                        <x-form-select 
                            name="category" 
                            label="Categoria" 
                            required 
                            :options="collect($categories)->mapWithKeys(fn($c) => [$c->value => $c->label()])"
                            id="category"
                        />
                        
                        <x-form-select 
                            name="condition" 
                            label="Condição" 
                            required 
                            :options="collect($conditions)->mapWithKeys(fn($c) => [$c->value => $c->label()])"
                        />
                        
                        <x-form-input name="model" label="Modelo" placeholder="Ex: 15 Pro Max" id="model" />
                        
                        <x-form-input name="storage" label="Armazenamento" placeholder="Ex: 256GB" />
                        
                        <x-form-input name="color" label="Cor" placeholder="Ex: Preto" />
                        
                        <x-form-input name="imei" label="IMEI" placeholder="Apenas para iPhones" />
                        
                        <x-form-input name="cost_price" label="Preço de Custo" type="number" step="0.01" min="0" required />
                        
                        <x-form-input name="sale_price" label="Preço de Venda" type="number" step="0.01" min="0" required />
                        
                        <x-form-input name="stock_quantity" label="Quantidade em Estoque" type="number" min="0" value="0" required />
                        
                        <x-form-input name="min_stock_alert" label="Alerta de Estoque Mínimo" type="number" min="0" value="1" required />
                        
                        <x-form-input name="supplier" label="Fornecedor" class="md:col-span-2" />
                        
                        <x-form-textarea name="notes" label="Observações" class="md:col-span-2" />
                        
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="active" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Produto ativo</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Salvar Produto
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function generateSku() {
            const category = document.getElementById('category').value || 'iphone';
            const model = document.getElementById('model').value || '';
            
            fetch(`{{ route('products.generate-sku') }}?category=${category}&model=${encodeURIComponent(model)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('sku').value = data.sku;
                });
        }
    </script>
    @endpush
</x-app-layout>
