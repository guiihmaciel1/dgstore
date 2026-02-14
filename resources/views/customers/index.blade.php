<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Clientes</h1>
                    <p class="text-sm text-gray-500">Gerencie a base de clientes da loja</p>
                </div>
                <a href="{{ route('customers.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Novo Cliente</span>
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                
                <!-- Busca -->
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <form method="GET" action="{{ route('customers.index') }}" x-data x-ref="filterForm" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome, telefone, email ou CPF..." 
                                   class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-gray-900 focus:outline-none"
                                   x-on:input.debounce.400ms="$refs.filterForm.submit()">
                        </div>
                        @if($search)
                            <div>
                                <a href="{{ route('customers.index') }}" class="inline-flex justify-center px-5 py-2.5 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 text-sm text-center hover:bg-gray-50 transition-colors">
                                    Limpar
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Tabela -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Cliente</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Telefone</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Email</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">CPF</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Compras</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 2.5rem; height: 2.5rem; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <span style="font-weight: 600; color: #6b7280; font-size: 0.875rem;">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                            </div>
                                            <div style="font-weight: 500; color: #111827;">{{ $customer->name }}</div>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151;">
                                        {{ $customer->formatted_phone }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $customer->email ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $customer->formatted_cpf ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $customer->purchases_count }} compras
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                            <a href="{{ route('customers.show', $customer) }}" style="color: #6b7280; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Ver</a>
                                            <a href="{{ route('customers.edit', $customer) }}" style="color: #6b7280; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Editar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 3rem; text-align: center; color: #6b7280;">
                                        Nenhum cliente encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($customers->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #f9fafb;">
                        {{ $customers->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
