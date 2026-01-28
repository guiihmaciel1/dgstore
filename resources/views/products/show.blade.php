<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $product->name }}
                </h2>
            </div>
            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Informações do Produto -->
                <div class="lg:col-span-2">
                    <x-card title="Informações do Produto">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->sku }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Categoria</dt>
                                <dd class="mt-1"><x-badge color="indigo">{{ $product->category->label() }}</x-badge></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Condição</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->condition->label() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1"><x-badge :color="$product->active ? 'green' : 'gray'">{{ $product->active ? 'Ativo' : 'Inativo' }}</x-badge></dd>
                            </div>
                            @if($product->model)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Modelo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->model }}</dd>
                                </div>
                            @endif
                            @if($product->storage)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Armazenamento</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->storage }}</dd>
                                </div>
                            @endif
                            @if($product->color)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cor</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->color }}</dd>
                                </div>
                            @endif
                            @if($product->imei)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IMEI</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->imei }}</dd>
                                </div>
                            @endif
                            @if($product->supplier)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fornecedor</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->supplier }}</dd>
                                </div>
                            @endif
                            @if($product->notes)
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Observações</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </x-card>
                </div>

                <!-- Preços e Estoque -->
                <div class="space-y-6">
                    <x-card title="Preços">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preço de Venda</dt>
                                <dd class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ $product->formatted_sale_price }}</dd>
                            </div>
                            @if(auth()->user()->isAdmin())
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preço de Custo</dt>
                                    <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $product->formatted_cost_price }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Margem de Lucro</dt>
                                    <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ number_format($product->profit_margin, 1) }}%</dd>
                                </div>
                            @endif
                        </dl>
                    </x-card>

                    <x-card title="Estoque">
                        <x-slot name="actions">
                            <a href="{{ route('stock.product-history', $product) }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Ver histórico
                            </a>
                        </x-slot>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantidade Atual</dt>
                                <dd class="mt-1">
                                    <span class="text-2xl font-bold {{ $product->isLowStock() ? ($product->isOutOfStock() ? 'text-red-600' : 'text-yellow-600') : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">unidades</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Alerta Mínimo</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->min_stock_alert }} unidades</dd>
                            </div>
                            @if($product->isLowStock())
                                <div class="pt-2">
                                    <x-alert type="warning" :dismissible="false">
                                        Estoque baixo! Considere reabastecer.
                                    </x-alert>
                                </div>
                            @endif
                        </dl>
                        
                        <div class="mt-4">
                            <a href="{{ route('stock.create') }}?product_id={{ $product->id }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition w-full justify-center">
                                Registrar Entrada
                            </a>
                        </div>
                    </x-card>
                </div>
            </div>

            <!-- Movimentações Recentes -->
            <div class="mt-8">
                <x-card title="Movimentações Recentes de Estoque" :padding="false">
                    <x-data-table :headers="['Data', 'Tipo', 'Quantidade', 'Motivo', 'Usuário']">
                        @forelse($product->stockMovements->take(10) as $movement)
                            <tr>
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
    </div>
</x-app-layout>
