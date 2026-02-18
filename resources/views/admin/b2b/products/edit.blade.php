<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.b2b.products.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">Editar Produto B2B</h2>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.b2b.products.update', $product) }}" enctype="multipart/form-data"
              x-data="{
                  costPrice: {{ (float) $product->cost_price }},
                  wholesalePrice: {{ (float) $product->wholesale_price }},
                  get profit() { return this.wholesalePrice - this.costPrice },
                  get margin() { return this.costPrice > 0 ? ((this.profit / this.costPrice) * 100).toFixed(1) : '0.0' }
              }">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                        <input type="text" name="model" value="{{ old('model', $product->model) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Armazenamento</label>
                        <input type="text" name="storage" value="{{ old('storage', $product->storage) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                        <input type="text" name="color" value="{{ old('color', $product->color) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Condição *</label>
                        <select name="condition" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($conditions as $condition)
                                <option value="{{ $condition->value }}" {{ old('condition', $product->condition->value) == $condition->value ? 'selected' : '' }}>{{ $condition->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço de Custo (entrada) *</label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required step="0.01" min="0"
                               x-model.number="costPrice"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        @error('cost_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço Atacado (saída) *</label>
                        <input type="number" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}" required step="0.01" min="0"
                               x-model.number="wholesalePrice"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        @error('wholesale_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2 p-4 rounded-lg" :class="profit >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium" :class="profit >= 0 ? 'text-green-800' : 'text-red-800'">Lucro por unidade:</span>
                                <span class="ml-2 text-lg font-bold" :class="profit >= 0 ? 'text-green-700' : 'text-red-700'"
                                      x-text="'R$ ' + profit.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div>
                                <span class="text-sm font-medium" :class="profit >= 0 ? 'text-green-800' : 'text-red-800'">Margem:</span>
                                <span class="ml-2 text-lg font-bold" :class="profit >= 0 ? 'text-green-700' : 'text-red-700'"
                                      x-text="margin + '%'"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade em Estoque *</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordem de Exibição</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            <span class="text-sm font-medium text-gray-700">Produto ativo (visível no catálogo)</span>
                        </label>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto do Produto</label>
                        @if($product->photo)
                            <div class="mb-2">
                                <img src="{{ $product->photo_url }}" alt="" class="w-24 h-24 rounded-lg object-cover" />
                            </div>
                        @endif
                        <input type="file" name="photo" accept="image/*"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" />
                        <p class="mt-1 text-xs text-gray-500">Deixe vazio para manter a foto atual.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.b2b.products.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
