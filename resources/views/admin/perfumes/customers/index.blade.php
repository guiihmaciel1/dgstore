<x-perfumes-admin-layout>
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold text-gray-900">Clientes</h1>
            <a href="{{ route('admin.perfumes.customers.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Cliente
            </a>
        </div>

        @if(session('success'))
            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Busca Compacta -->
        <div class="mb-4 bg-white rounded-lg shadow-sm p-3">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar por nome, telefone, CPF..."
                       class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                <button type="submit" 
                        class="px-4 py-2 text-sm bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                    Buscar
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.perfumes.customers.index') }}"
                       class="px-4 py-2 text-sm bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Limpar
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabela Compacta -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">CPF</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Vendas</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Encomendas</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                @if($customer->email)
                                    <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-900">
                                {{ $customer->formatted_phone }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-500">
                                {{ $customer->formatted_cpf ?? '-' }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $customer->sales_count }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $customer->reservations_count }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-right text-xs font-medium">
                                <a href="{{ route('admin.perfumes.customers.show', $customer) }}"
                                   class="text-pink-600 hover:text-pink-900 mr-2">
                                    Ver
                                </a>
                                <a href="{{ route('admin.perfumes.customers.edit', $customer) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="mt-2 text-sm">Nenhum cliente encontrado</p>
                                <a href="{{ route('admin.perfumes.customers.create') }}"
                                   class="mt-3 inline-block text-pink-600 hover:text-pink-700 font-medium text-sm">
                                    Cadastrar primeiro cliente
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </div>
</x-perfumes-admin-layout>
