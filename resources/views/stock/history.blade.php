<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('products.show', $product) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Histórico de Estoque: {{ $product->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-stat-card title="Estoque Atual" :value="$product->stock_quantity . ' un.'" color="blue" />
                <x-stat-card title="SKU" :value="$product->sku" color="gray" />
                <x-stat-card title="Alerta Mínimo" :value="$product->min_stock_alert . ' un.'" :color="$product->isLowStock() ? 'red' : 'green'" />
            </div>

            <x-card title="Movimentações" :padding="false">
                <x-data-table :headers="['Data', 'Tipo', 'Quantidade', 'Motivo', 'Usuário']">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :color="$movement->type_color">{{ $movement->type->label() }}</x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $movement->isAddition() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->isAddition() ? '+' : '-' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $movement->reason ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $movement->user?->name ?? 'Sistema' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Nenhuma movimentação registrada.
                            </td>
                        </tr>
                    @endforelse
                </x-data-table>
            </x-card>
        </div>
    </div>
</x-app-layout>
