<x-app-layout>
    <x-slot name="title">Clientes</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #6ee7b7;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Clientes</h1>
                    <p class="text-sm text-dg-500">Gerencie a base de clientes da loja</p>
                </div>
                <a href="{{ route('customers.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-surface text-white font-semibold rounded-lg hover:bg-surface-elevated transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Novo Cliente</span>
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                
                <!-- Busca -->
                <div class="p-4 border-b border-border bg-surface">
                    <form method="GET" action="{{ route('customers.index') }}" x-data x-ref="filterForm" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-dg-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome, telefone, Instagram ou CPF..." 
                                   class="w-full pl-10 pr-3 py-2.5 border border-border-strong rounded-lg text-sm focus:border-gray-900 focus:outline-none"
                                   x-on:input.debounce.400ms="$refs.filterForm.submit()">
                        </div>
                        @if($search)
                            <div>
                                <a href="{{ route('customers.index') }}" class="inline-flex justify-center px-5 py-2.5 bg-surface-raised text-dg-300 font-medium rounded-lg border border-border-strong text-sm text-center hover:bg-surface transition-colors">
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
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Cliente</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Telefone</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Instagram</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">CPF</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Compras</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 2.5rem; height: 2.5rem; background: #222222; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <span style="font-weight: 600; color: #818181; font-size: 0.875rem;">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                            </div>
                                            <div style="font-weight: 500; color: #e3e3e3;">{{ $customer->name }}</div>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #a4a4a4;">
                                        {{ $customer->formatted_phone }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #818181;">
                                        {{ $customer->formatted_instagram ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #818181;">
                                        {{ $customer->formatted_cpf ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #222222; color: #a4a4a4; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $customer->purchases_count }} compras
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                            <a href="{{ route('customers.show', $customer) }}" style="color: #818181; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Ver</a>
                                            <a href="{{ route('customers.edit', $customer) }}" style="color: #818181; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Editar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 3rem; text-align: center; color: #818181;">
                                        Nenhum cliente encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($customers->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                        {{ $customers->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
