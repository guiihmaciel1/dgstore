<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.b2b.products.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100/80 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Editar Produto B2B</h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('admin.b2b.products.update', $product) }}" enctype="multipart/form-data"
              x-data="{
                  costPrice: {{ (float) $product->cost_price }},
                  wholesalePrice: {{ (float) $product->wholesale_price }},
                  get profit() { return this.wholesalePrice - this.costPrice },
                  get margin() { return this.costPrice > 0 ? ((this.profit / this.costPrice) * 100).toFixed(1) : '0.0' }
              }">
            @csrf
            @method('PUT')

            <div class="apple-card p-5 sm:p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                    <div class="sm:col-span-2">
                        <label class="apple-label" for="name">Nome do Produto *</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="apple-input" />
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="apple-label" for="model">Modelo *</label>
                        <input id="model" type="text" name="model" value="{{ old('model', $product->model) }}" required
                               class="apple-input" />
                        @error('model') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="apple-label" for="storage">Armazenamento *</label>
                        <input id="storage" type="text" name="storage" value="{{ old('storage', $product->storage) }}" required
                               class="apple-input" />
                        @error('storage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="apple-label" for="color">Cor *</label>
                        <input id="color" type="text" name="color" value="{{ old('color', $product->color) }}" required
                               class="apple-input" />
                        @error('color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="apple-label" for="condition">Condição *</label>
                        <select id="condition" name="condition" required class="apple-select">
                            @foreach($conditions as $condition)
                                <option value="{{ $condition->value }}" {{ old('condition', $product->condition->value) == $condition->value ? 'selected' : '' }}>{{ $condition->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="apple-label" for="cost_price">Preço de Custo (entrada) *</label>
                        <input id="cost_price" type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required step="0.01" min="0"
                               x-model.number="costPrice"
                               class="apple-input" />
                        @error('cost_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="apple-label" for="wholesale_price">Preço Atacado (saída) *</label>
                        <input id="wholesale_price" type="number" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}" required step="0.01" min="0"
                               x-model.number="wholesalePrice"
                               class="apple-input" />
                        @error('wholesale_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2 p-4 sm:p-5 rounded-xl" :class="profit >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
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
                        <label class="apple-label" for="stock_quantity">Quantidade em Estoque *</label>
                        <input id="stock_quantity" type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0"
                               class="apple-input" />
                    </div>

                    <div>
                        <label class="apple-label" for="sort_order">Ordem de Exibição</label>
                        <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0"
                               class="apple-input" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded-md border border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500/30 focus:ring-offset-0" />
                            <span class="text-sm font-medium text-gray-700">Produto ativo (visível no catálogo)</span>
                        </label>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="apple-label" for="photo">Foto do Produto</label>
                        @if($product->photo)
                            <div class="mb-3">
                                <img src="{{ $product->photo_url }}" alt="" class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover border border-gray-200/80 shadow-sm" />
                            </div>
                        @endif
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50/50 px-4 py-6 sm:px-6 sm:py-8 transition-colors hover:border-gray-400/80 hover:bg-gray-50/80">
                            <input id="photo" type="file" name="photo" accept="image/*"
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800 cursor-pointer" />
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500">Deixe vazio para manter a foto atual.</p>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-gray-200/80">
                    <a href="{{ route('admin.b2b.products.index') }}" class="apple-btn-secondary w-full sm:w-auto text-center">
                        Cancelar
                    </a>
                    <button type="submit" class="apple-btn-primary w-full sm:w-auto">
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-b2b-admin-layout>
