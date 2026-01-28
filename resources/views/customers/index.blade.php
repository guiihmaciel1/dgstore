<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Clientes</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Gerencie a base de clientes da loja</p>
                </div>
                <a href="{{ route('customers.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background 0.2s;"
                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Novo Cliente
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                
                <!-- Busca -->
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <form method="GET" action="{{ route('customers.index') }}" style="display: flex; gap: 0.75rem;">
                        <div style="flex: 1; position: relative;">
                            <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome, telefone, email ou CPF..." 
                                   style="width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#d1d5db'">
                        </div>
                        <button type="submit" 
                                style="padding: 0.625rem 1.25rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">
                            Buscar
                        </button>
                        @if($search)
                            <a href="{{ route('customers.index') }}" 
                               style="padding: 0.625rem 1.25rem; background: white; color: #374151; font-weight: 500; border-radius: 0.5rem; border: 1px solid #d1d5db; text-decoration: none; font-size: 0.875rem;">
                                Limpar
                            </a>
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
