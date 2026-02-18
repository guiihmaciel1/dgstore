<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Entregar Amostra</h2>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.perfumes.samples.index') }}"
           class="text-sm text-pink-600 hover:text-pink-700 font-medium inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar às amostras
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.perfumes.samples.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Retailer --}}
                <div>
                    <label for="perfume_retailer_id" class="block text-sm font-medium text-gray-700 mb-1">Lojista *</label>
                    <select name="perfume_retailer_id" id="perfume_retailer_id" required
                            class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('perfume_retailer_id') border-red-300 @enderror">
                        <option value="">Selecione o lojista</option>
                        @foreach($retailers as $r)
                            <option value="{{ $r->id }}" {{ old('perfume_retailer_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                    @error('perfume_retailer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Product --}}
                <div>
                    <label for="perfume_product_id" class="block text-sm font-medium text-gray-700 mb-1">Produto *</label>
                    <select name="perfume_product_id" id="perfume_product_id" required
                            class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('perfume_product_id') border-red-300 @enderror">
                        <option value="">Selecione o produto</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('perfume_product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}{{ $p->brand ? ' - ' . $p->brand : '' }}{{ $p->size_ml ? ' (' . $p->size_ml . 'ml)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('perfume_product_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Quantity --}}
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantidade *</label>
                    <input type="number" name="quantity" id="quantity" min="1" required
                           value="{{ old('quantity', 1) }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('quantity') border-red-300 @enderror">
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex items-center gap-4">
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-pink-600 to-rose-500 text-white text-sm font-semibold rounded-lg hover:from-pink-500 hover:to-rose-400 shadow-md shadow-pink-500/20 transition">
                    Registrar Entrega
                </button>
                <a href="{{ route('admin.perfumes.samples.index') }}"
                   class="text-sm text-pink-600 hover:text-pink-700 font-medium">Voltar</a>
            </div>
        </form>
    </div>
</x-perfumes-admin-layout>
