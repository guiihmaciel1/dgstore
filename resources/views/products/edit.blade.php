<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('products.show', $product) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar Produto: {{ $product->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-input name="name" label="Nome do Produto" :value="$product->name" required class="md:col-span-2" />
                        
                        <x-form-input name="sku" label="SKU" :value="$product->sku" required />
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Categoria <span class="text-red-500">*</span>
                            </label>
                            <select name="category" id="category" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(\App\Domain\Product\Enums\ProductCategory::grouped() as $group => $items)
                                    <optgroup label="{{ $group }}">
                                        @foreach($items as $category)
                                            <option value="{{ $category->value }}" {{ $product->category->value == $category->value ? 'selected' : '' }}>
                                                {{ $category->label() }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <x-form-select 
                            name="condition" 
                            label="Condição" 
                            required 
                            :options="collect($conditions)->mapWithKeys(fn($c) => [$c->value => $c->label()])"
                            :value="$product->condition->value"
                        />
                        
                        <x-form-input name="model" label="Modelo" :value="$product->model" />
                        
                        <x-form-input name="storage" label="Armazenamento" :value="$product->storage" />
                        
                        <x-form-input name="color" label="Cor" :value="$product->color" />
                        
                        <x-form-input name="imei" label="IMEI/Serial" :value="$product->imei" />
                        
                        <x-form-input name="cost_price" label="Preço de Custo" type="number" step="0.01" min="0" :value="$product->cost_price" required />
                        
                        <x-form-input name="sale_price" label="Preço de Venda" type="number" step="0.01" min="0" :value="$product->sale_price" required />
                        
                        <x-form-input name="stock_quantity" label="Quantidade em Estoque" type="number" min="0" :value="$product->stock_quantity" required />
                        
                        <x-form-input name="min_stock_alert" label="Alerta de Estoque Mínimo" type="number" min="0" :value="$product->min_stock_alert" required />
                        
                        <x-form-input name="supplier" label="Fornecedor" :value="$product->supplier" class="md:col-span-2" />
                        
                        <x-form-textarea name="notes" label="Observações" :value="$product->notes" class="md:col-span-2" />
                        
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="active" value="1" {{ $product->active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Produto ativo</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-between">
                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Excluir
                            </button>
                        </form>
                        
                        <div class="flex gap-4">
                            <a href="{{ route('products.show', $product) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
