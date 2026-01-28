<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('reports.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Relatório de Estoque
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Resumo -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <x-stat-card title="Total de Produtos" :value="$report['summary']['total_products']" color="blue" />
                <x-stat-card title="Valor em Estoque" :value="'R$ ' . number_format($report['summary']['total_stock_value'], 2, ',', '.')" color="green" />
                <x-stat-card title="Sem Estoque" :value="$report['summary']['out_of_stock']" color="red" />
                <x-stat-card title="Estoque Baixo" :value="$report['summary']['low_stock']" color="yellow" />
            </div>

            <!-- Por Categoria -->
            <x-card title="Por Categoria" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($report['by_category'] as $category => $data)
                        @php $categoryEnum = \App\Domain\Product\Enums\ProductCategory::from($category); @endphp
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">{{ $categoryEnum->label() }}</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Produtos:</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $data['count'] }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Em Estoque:</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $data['stock_quantity'] }} un.</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Valor:</dt>
                                    <dd class="text-sm font-medium text-green-600">R$ {{ number_format($data['stock_value'], 2, ',', '.') }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Produtos com Estoque Baixo -->
            <x-card title="Produtos com Estoque Baixo" :padding="false">
                <x-data-table :headers="['Produto', 'Categoria', 'Estoque', 'Mínimo', 'Valor Unit.', 'Ações']">
                    @forelse($report['low_stock_products'] as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge color="indigo">{{ $product->category->label() }}</x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $product->isOutOfStock() ? 'text-red-600 font-bold' : 'text-yellow-600' }}">
                                {{ $product->stock_quantity }} un.
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $product->min_stock_alert }} un.
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $product->formatted_cost_price }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('stock.create') }}?product_id={{ $product->id }}" class="text-green-600 hover:text-green-900">
                                    Registrar Entrada
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Nenhum produto com estoque baixo.
                            </td>
                        </tr>
                    @endforelse
                </x-data-table>
            </x-card>
        </div>
    </div>
</x-app-layout>
