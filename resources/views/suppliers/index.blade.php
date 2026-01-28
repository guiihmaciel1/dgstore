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
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Fornecedores</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Gerencie os fornecedores e suas cotações</p>
                </div>
                <a href="{{ route('suppliers.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background 0.2s;"
                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Novo Fornecedor
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                
                <!-- Filtros -->
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <form method="GET" action="{{ route('suppliers.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px; position: relative;">
                            <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome, CNPJ, email..." 
                                   style="width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#d1d5db'">
                        </div>
                        <select name="active" 
                                style="padding: 0.625rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; outline: none; background: white;">
                            <option value="">Todos os status</option>
                            <option value="1" {{ $active === true ? 'selected' : '' }}>Ativos</option>
                            <option value="0" {{ $active === false ? 'selected' : '' }}>Inativos</option>
                        </select>
                        <button type="submit" 
                                style="padding: 0.625rem 1.25rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">
                            Filtrar
                        </button>
                        @if($search || $active !== null)
                            <a href="{{ route('suppliers.index') }}" 
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
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Fornecedor</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">CNPJ</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Contato</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Cotações</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 2.5rem; height: 2.5rem; background: {{ $supplier->active ? '#dbeafe' : '#f3f4f6' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <span style="font-weight: 600; color: {{ $supplier->active ? '#1d4ed8' : '#6b7280' }}; font-size: 0.875rem;">{{ strtoupper(substr($supplier->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div style="font-weight: 500; color: #111827;">{{ $supplier->name }}</div>
                                                @if($supplier->contact_person)
                                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $supplier->contact_person }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151;">
                                        {{ $supplier->formatted_cnpj ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        @if($supplier->phone || $supplier->email)
                                            <div>{{ $supplier->formatted_phone ?? '' }}</div>
                                            <div style="font-size: 0.75rem;">{{ $supplier->email ?? '' }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @if($supplier->active)
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #dcfce7; color: #166534; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                                Ativo
                                            </span>
                                        @else
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #fee2e2; color: #991b1b; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                                Inativo
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $supplier->quotations_count }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                            <a href="{{ route('quotations.create', ['supplier_id' => $supplier->id]) }}" style="color: #059669; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#047857'" onmouseout="this.style.color='#059669'" title="Nova Cotação">+ Cotação</a>
                                            <a href="{{ route('suppliers.show', $supplier) }}" style="color: #6b7280; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Ver</a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" style="color: #6b7280; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Editar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 3rem; text-align: center; color: #6b7280;">
                                        Nenhum fornecedor encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($suppliers->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #f9fafb;">
                        {{ $suppliers->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
