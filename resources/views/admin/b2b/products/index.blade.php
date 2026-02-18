<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Produtos B2B - Distribuidora</h2>
            <a href="{{ route('admin.b2b.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Produto
            </a>
        </div>
    </x-slot>

    <!-- Filtros -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome ou modelo..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="w-44">
                <label class="block text-sm font-medium text-gray-700 mb-1">Condição</label>
                <select name="condition" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->value }}" {{ request('condition') == $condition->value ? 'selected' : '' }}>{{ $condition->label() }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-800">Filtrar</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condição</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Custo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Atacado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Lucro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Margem</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estoque</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($product->photo)
                                        <img src="{{ $product->photo_url }}" alt="" class="w-10 h-10 rounded-lg object-cover" />
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $product->storage }} {{ $product->color ? '- ' . $product->color : '' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $product->condition->value === 'sealed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $product->condition->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">{{ $product->formatted_cost_price }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ $product->formatted_wholesale_price }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium {{ $product->profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->formatted_profit }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm {{ $product->profit_margin > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($product->profit_margin, 1) }}%
                            </td>
                            <td class="px-6 py-4 text-center text-sm {{ $product->stock_quantity <= 0 ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                {{ $product->stock_quantity }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($product->active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Ativo</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">Inativo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.b2b.products.edit', $product) }}" class="text-sm text-blue-600 hover:text-blue-800">Editar</a>
                                    <form method="POST" action="{{ route('admin.b2b.products.destroy', $product) }}" onsubmit="return confirm('Tem certeza?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum produto B2B cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</x-app-layout>
