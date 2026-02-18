<x-perfumes-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Produtos</h2>
            <a href="{{ route('admin.perfumes.products.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Produto
            </a>
        </div>
    </x-slot>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.perfumes.products.index') }}"
          class="mb-6 bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                <input type="text" name="search" id="search"
                       value="{{ request('search') }}"
                       placeholder="Nome, marca ou código de barras"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
            </div>
            <div class="w-40">
                <label for="category" class="block text-xs font-medium text-gray-500 mb-1">Categoria</label>
                <select name="category" id="category"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    <option value="">Todas</option>
                    <option value="masculino" {{ request('category') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="feminino" {{ request('category') === 'feminino' ? 'selected' : '' }}>Feminino</option>
                    <option value="unissex" {{ request('category') === 'unissex' ? 'selected' : '' }}>Unissex</option>
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Products table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-pink-50/40">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Marca</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tamanho</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Custo</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Venda</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Lucro</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Estoque</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $product->brand ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @php
                                $badgeClass = match ($product->category->badgeColor()) {
                                    'blue' => 'bg-blue-100 text-blue-700',
                                    'pink' => 'bg-pink-100 text-pink-700',
                                    'purple' => 'bg-purple-100 text-purple-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $product->category->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $product->size_ml ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">
                            R$ {{ number_format((float) $product->cost_price, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">
                            R$ {{ number_format((float) $product->sale_price, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <span class="text-sm font-medium {{ $product->profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                R$ {{ number_format($product->profit, 2, ',', '.') }}
                            </span>
                            <span class="text-xs text-gray-500 block">{{ $product->profit_margin }}%</span>
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">{{ number_format($product->stock_quantity, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">
                            @if($product->active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Inativo
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.perfumes.products.edit', $product) }}"
                                   class="text-sm text-pink-600 hover:text-pink-700 font-medium">Editar</a>
                                <form method="POST"
                                      action="{{ route('admin.perfumes.products.destroy', $product) }}"
                                      class="inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-12 text-center text-gray-500 text-sm">
                            Nenhum produto encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</x-perfumes-admin-layout>
