<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Novo Produto</h2>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.perfumes.products.index') }}"
           class="text-sm text-pink-600 hover:text-pink-700 font-medium inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar aos produtos
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6"
         x-data="{ cost: {{ json_encode((float) (old('cost_price') ?? 0)) }}, sale: {{ json_encode((float) (old('sale_price') ?? 0)) }} }">
        <form method="POST"
              action="{{ route('admin.perfumes.products.store') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Brand --}}
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('brand') border-red-300 @enderror">
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
                    <select name="category" id="category" required
                            class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('category') border-red-300 @enderror">
                        <option value="">Selecione</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}" {{ old('category') === $cat->value ? 'selected' : '' }}>
                                {{ $cat->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Size --}}
                <div>
                    <label for="size_ml" class="block text-sm font-medium text-gray-700 mb-1">Tamanho (ml)</label>
                    <input type="text" name="size_ml" id="size_ml" value="{{ old('size_ml') }}" placeholder="ex: 100ml"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('size_ml') border-red-300 @enderror">
                    @error('size_ml')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cost price --}}
                <div>
                    <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">Preço de custo (R$) *</label>
                    <input type="number" name="cost_price" id="cost_price" step="0.01" min="0" required
                           value="{{ old('cost_price') }}"
                           x-model.number="cost"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('cost_price') border-red-300 @enderror">
                    @error('cost_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sale price --}}
                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1">Preço de venda (R$) *</label>
                    <input type="number" name="sale_price" id="sale_price" step="0.01" min="0" required
                           value="{{ old('sale_price') }}"
                           x-model.number="sale"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('sale_price') border-red-300 @enderror">
                    @error('sale_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Profit calculator --}}
                <div class="md:col-span-2 p-4 bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-100 rounded-lg">
                    <div class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                        <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Lucro: <span class="text-pink-700" x-text="'R$ ' + (sale - cost).toFixed(2).replace('.', ',')">R$ 0,00</span>
                        <span class="text-gray-400 mx-1">|</span>
                        Margem: <span class="text-pink-700" x-text="(sale > 0 ? ((sale - cost) / sale * 100).toFixed(1) : '0') + '%'">0%</span>
                    </div>
                </div>

                {{-- Stock --}}
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Estoque *</label>
                    <input type="number" name="stock_quantity" id="stock_quantity" min="0" required
                           value="{{ old('stock_quantity', 0) }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('stock_quantity') border-red-300 @enderror">
                    @error('stock_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Barcode --}}
                <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Código de barras</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('barcode') border-red-300 @enderror">
                    @error('barcode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Photo --}}
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                    <input type="file" name="photo" id="photo" accept="image/*"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('photo') border-red-300 @enderror">
                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="active" id="active" value="1"
                           {{ old('active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                    <label for="active" class="text-sm font-medium text-gray-700">Ativo</label>
                </div>
            </div>

            <div class="mt-8 flex items-center gap-4">
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-pink-600 to-rose-500 text-white text-sm font-semibold rounded-lg hover:from-pink-500 hover:to-rose-400 shadow-md shadow-pink-500/20 transition">
                    Salvar Produto
                </button>
                <a href="{{ route('admin.perfumes.products.index') }}"
                   class="text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
            </div>
        </form>
    </div>
</x-perfumes-admin-layout>
