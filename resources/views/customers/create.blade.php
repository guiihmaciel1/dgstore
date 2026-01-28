<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Novo Cliente
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    
                    <div class="space-y-6">
                        <x-form-input name="name" label="Nome Completo" required />
                        
                        <x-form-input name="phone" label="Telefone" required placeholder="(00) 00000-0000" />
                        
                        <x-form-input name="email" label="E-mail" type="email" />
                        
                        <x-form-input name="cpf" label="CPF" placeholder="000.000.000-00" />
                        
                        <x-form-textarea name="address" label="Endereço" />
                        
                        <x-form-textarea name="notes" label="Observações" />
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Cadastrar Cliente
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
