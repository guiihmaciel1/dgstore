<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('suppliers.show', $supplier) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar Fornecedor
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <x-form-input name="name" label="Nome do Fornecedor" :value="old('name', $supplier->name)" required />
                        
                        <x-form-input name="cnpj" label="CNPJ" :value="old('cnpj', $supplier->formatted_cnpj)" placeholder="00.000.000/0000-00" />
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-form-input name="phone" label="Telefone" :value="old('phone', $supplier->formatted_phone)" placeholder="(00) 00000-0000" />
                            <x-form-input name="email" label="E-mail" type="email" :value="old('email', $supplier->email)" />
                        </div>
                        
                        <x-form-input name="contact_person" label="Pessoa de Contato" :value="old('contact_person', $supplier->contact_person)" />
                        
                        <x-form-textarea name="address" label="Endereço" :value="old('address', $supplier->address)" />
                        
                        <x-form-textarea name="notes" label="Observações" :value="old('notes', $supplier->notes)" />

                        <div class="flex items-center gap-2">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" id="active" value="1" {{ old('active', $supplier->active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="active" class="text-sm text-gray-700 dark:text-gray-300">Fornecedor ativo</label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-between">
                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Excluir
                            </button>
                        </form>
                        
                        <div class="flex gap-4">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
