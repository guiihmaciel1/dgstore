<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('reports.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Produtos Mais Vendidos
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('reports.top-products') }}" class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Inicial</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Final</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md">
                    </div>
                    <div class="w-32">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade</label>
                        <select name="limit" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md">
                            <option value="10" {{ $limit == 10 ? 'selected' : '' }}>Top 10</option>
                            <option value="20" {{ $limit == 20 ? 'selected' : '' }}>Top 20</option>
                            <option value="50" {{ $limit == 50 ? 'selected' : '' }}>Top 50</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Filtrar
                    </button>
                </form>
            </x-card>

            <!-- Ranking -->
            <x-card title="Ranking de Vendas" :padding="false">
                <x-data-table :headers="['#', 'Produto', 'Categoria', 'Total Vendido', 'Estoque Atual']">
                    @forelse($report['products'] as $index => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }} dark:bg-gray-700 dark:text-gray-300 text-sm font-bold">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('products.show', $item['product']) }}" class="text-indigo-600 hover:text-indigo-500">
                                        {{ $item['product']->name }}
                                    </a>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    SKU: {{ $item['product']->sku }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge color="indigo">{{ $item['product']->category->label() }}</x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold text-green-600">{{ $item['total_sold'] }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">unidades</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :color="$item['product']->isLowStock() ? ($item['product']->isOutOfStock() ? 'red' : 'yellow') : 'green'">
                                    {{ $item['product']->stock_quantity }} un.
                                </x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Nenhuma venda registrada no período.
                            </td>
                        </tr>
                    @endforelse
                </x-data-table>
            </x-card>
        </div>
    </div>
</x-app-layout>
