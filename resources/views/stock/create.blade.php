<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('stock.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nova Movimentação de Estoque
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('stock.store') }}">
                    @csrf
                    
                    <div class="space-y-6">
                        <x-form-select 
                            name="product_id" 
                            label="Produto" 
                            required 
                            :options="$products->mapWithKeys(fn($p) => [$p->id => $p->name . ' (Estoque: ' . $p->stock_quantity . ')'])"
                            :value="request('product_id')"
                        />
                        
                        <x-form-select 
                            name="type" 
                            label="Tipo de Movimentação" 
                            required 
                            :options="collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])"
                        />
                        
                        <x-form-input 
                            name="quantity" 
                            label="Quantidade" 
                            type="number" 
                            min="1" 
                            required 
                            help="Para entrada, informe a quantidade a adicionar. Para ajuste, informe o novo valor total do estoque."
                        />
                        
                        <x-form-textarea name="reason" label="Motivo" placeholder="Descreva o motivo da movimentação..." />
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="{{ route('stock.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Registrar Movimentação
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
