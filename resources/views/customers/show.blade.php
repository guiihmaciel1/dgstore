<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $customer->name }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sales.create') }}?customer_id={{ $customer->id }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                    Nova Venda
                </a>
                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition">
                    Editar
                </a>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <x-card title="Dados do Cliente">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $customer->formatted_phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $customer->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CPF</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $customer->formatted_cpf ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cliente desde</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $customer->created_at->format('d/m/Y') }}</dd>
                            </div>
                            @if($customer->address)
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Endereço</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $customer->address }}</dd>
                                </div>
                            @endif
                            @if($customer->notes)
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Observações</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $customer->notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </x-card>
                </div>

                <div>
                    <x-card title="Resumo de Compras">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Compras</dt>
                                <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $customer->purchases_count }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Total</dt>
                                <dd class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">R$ {{ number_format($customer->total_purchases, 2, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </x-card>
                </div>
            </div>

            <div class="mt-8">
                <x-card title="Histórico de Compras" :padding="false">
                    <x-data-table :headers="['Venda', 'Data', 'Itens', 'Total', 'Status', 'Ações']">
                        @forelse($customer->sales as $sale)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $sale->sale_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $sale->sold_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $sale->items->count() }} {{ $sale->items->count() === 1 ? 'item' : 'itens' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $sale->formatted_total }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-badge :color="$sale->payment_status->color()">{{ $sale->payment_status->label() }}</x-badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Nenhuma compra registrada.
                                </td>
                            </tr>
                        @endforelse
                    </x-data-table>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
