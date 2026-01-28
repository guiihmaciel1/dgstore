<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Alertas de Estoque Baixo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($products->count() > 0)
                <div class="mb-4">
                    <x-alert type="warning" :dismissible="false">
                        {{ $products->count() }} {{ $products->count() === 1 ? 'produto está' : 'produtos estão' }} com estoque baixo ou zerado.
                    </x-alert>
                </div>
            @endif

            <x-card :padding="false">
                <x-data-table :headers="['Produto', 'Categoria', 'Estoque Atual', 'Mínimo', 'Status', 'Ações']">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $product->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    SKU: {{ $product->sku }}
                                </div>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->isOutOfStock())
                                    <x-badge color="red">Sem Estoque</x-badge>
                                @else
                                    <x-badge color="yellow">Estoque Baixo</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Ver</a>
                                    <a href="{{ route('stock.create') }}?product_id={{ $product->id }}" class="text-green-600 hover:text-green-900 dark:text-green-400">Entrada</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Nenhum produto com estoque baixo. Tudo em ordem!
                            </td>
                        </tr>
                    @endforelse
                </x-data-table>
            </x-card>
        </div>
    </div>
</x-app-layout>
