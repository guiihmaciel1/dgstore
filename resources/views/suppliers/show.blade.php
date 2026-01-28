<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $supplier->name }}
                </h2>
                @if(!$supplier->active)
                    <span class="ml-3 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inativo</span>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('quotations.create', ['supplier_id' => $supplier->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Cotação
                </a>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition">
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
                <!-- Dados do Fornecedor -->
                <div class="lg:col-span-2">
                    <x-card title="Dados do Fornecedor">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CNPJ</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->formatted_cnpj ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->formatted_phone ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pessoa de Contato</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->contact_person ?? '-' }}</dd>
                            </div>
                            @if($supplier->address)
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Endereço</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->address }}</dd>
                                </div>
                            @endif
                            @if($supplier->notes)
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Observações</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->notes }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cadastrado em</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </x-card>
                </div>

                <!-- Resumo -->
                <div>
                    <x-card title="Resumo de Cotações">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Cotações</dt>
                                <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $quotations->count() }}</dd>
                            </div>
                            @if($supplier->latest_quotation)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última Cotação</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->latest_quotation->quoted_at->format('d/m/Y') }}</dd>
                                </div>
                            @endif
                        </dl>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('quotations.index', ['supplier_id' => $supplier->id]) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-sm font-medium">
                                Ver todas as cotações →
                            </a>
                        </div>
                    </x-card>
                </div>
            </div>

            <!-- Histórico de Cotações -->
            <div class="mt-8">
                <x-card title="Cotações Recentes" :padding="false">
                    <x-slot name="actions">
                        <a href="{{ route('quotations.create', ['supplier_id' => $supplier->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium">
                            + Nova Cotação
                        </a>
                    </x-slot>
                    
                    <x-data-table :headers="['Produto', 'Preço Unit.', 'Quantidade', 'Data', 'Registrado por', 'Ações']">
                        @forelse($quotations as $quotation)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $quotation->product_name }}</div>
                                    @if($quotation->product)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $quotation->product->sku }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                                    {{ $quotation->formatted_unit_price }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $quotation->formatted_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $quotation->quoted_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $quotation->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" 
                                          onsubmit="return confirm('Tem certeza que deseja excluir esta cotação?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Nenhuma cotação registrada para este fornecedor.
                                </td>
                            </tr>
                        @endforelse
                    </x-data-table>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
