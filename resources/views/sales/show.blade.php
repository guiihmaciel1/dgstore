<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('sales.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Venda #{{ $sale->sale_number }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimir
                </a>
                @if($sale->canBeCancelled())
                    <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('Tem certeza que deseja cancelar esta venda? O estoque será devolvido.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                            Cancelar Venda
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Itens da Venda -->
                    <x-card title="Itens da Venda" :padding="false">
                        <x-data-table :headers="['Produto', 'Qtd', 'Preço Unit.', 'Subtotal']">
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->product_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            SKU: {{ $item->product_sku }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item->formatted_unit_price }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $item->formatted_subtotal }}
                                    </td>
                                </tr>
                            @endforeach
                        </x-data-table>
                        
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Subtotal:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $sale->formatted_subtotal }}</span>
                            </div>
                            @if($sale->discount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Desconto:</span>
                                    <span class="text-red-600">-{{ $sale->formatted_discount }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-gray-900 dark:text-gray-100">Total:</span>
                                <span class="text-green-600 dark:text-green-400">{{ $sale->formatted_total }}</span>
                            </div>
                        </div>
                    </x-card>
                    
                    @if($sale->notes)
                        <x-card title="Observações">
                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $sale->notes }}</p>
                        </x-card>
                    @endif
                </div>

                <div class="space-y-6">
                    <!-- Status -->
                    <x-card title="Status da Venda">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                                <x-badge :color="$sale->payment_status->color()">{{ $sale->payment_status->label() }}</x-badge>
                            </div>
                            
                            @if(!$sale->isCancelled())
                                <form method="POST" action="{{ route('sales.update-status', $sale) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex gap-2">
                                        <select name="payment_status" class="flex-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md">
                                            <option value="pending" {{ $sale->payment_status->value === 'pending' ? 'selected' : '' }}>Pendente</option>
                                            <option value="paid" {{ $sale->payment_status->value === 'paid' ? 'selected' : '' }}>Pago</option>
                                            <option value="partial" {{ $sale->payment_status->value === 'partial' ? 'selected' : '' }}>Parcial</option>
                                        </select>
                                        <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                            Atualizar
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </x-card>
                    
                    <!-- Informações da Venda -->
                    <x-card title="Informações">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Data da Venda</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->sold_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Vendedor</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->user?->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Forma de Pagamento</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $sale->payment_method->label() }}
                                    @if($sale->installments > 1)
                                        ({{ $sale->installments }}x de {{ $sale->formatted_installment_value }})
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </x-card>
                    
                    <!-- Cliente -->
                    <x-card title="Cliente">
                        @if($sale->customer)
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Nome</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('customers.show', $sale->customer) }}" class="text-indigo-600 hover:text-indigo-500">
                                            {{ $sale->customer->name }}
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Telefone</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->customer->formatted_phone }}</dd>
                                </div>
                                @if($sale->customer->email)
                                    <div>
                                        <dt class="text-sm text-gray-500 dark:text-gray-400">E-mail</dt>
                                        <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->customer->email }}</dd>
                                    </div>
                                @endif
                            </dl>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cliente não informado</p>
                        @endif
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
