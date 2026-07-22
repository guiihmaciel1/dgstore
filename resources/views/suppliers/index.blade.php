<x-app-layout>
    <x-slot name="title">Fornecedores</x-slot>
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
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Fornecedores</h1>
                    <p class="text-sm text-gray-500">Gerencie os fornecedores e suas cotações</p>
                </div>
                <a href="{{ route('suppliers.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-surface text-white font-semibold rounded-lg hover:bg-surface-elevated transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Novo Fornecedor</span>
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                
                <!-- Filtros -->
                <div class="p-4 border-b border-border bg-surface">
                    <form method="GET" action="{{ route('suppliers.index') }}" x-data x-ref="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Nome, CNPJ, email, contato..." 
                                   class="w-full px-3 py-2 border border-border rounded-lg text-sm focus:border-dg-400 focus:outline-none"
                                   x-on:input.debounce.400ms="$refs.filterForm.submit()">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <select name="active" class="w-full px-3 py-2 border border-border rounded-lg text-sm bg-surface-overlay focus:border-dg-400 focus:outline-none"
                                    x-on:change="$refs.filterForm.submit()">
                                <option value="">Todos</option>
                                <option value="1" {{ $active === true ? 'selected' : '' }}>Ativos</option>
                                <option value="0" {{ $active === false ? 'selected' : '' }}>Inativos</option>
                            </select>
                        </div>
                        @if($search || $active !== null)
                            <div class="sm:col-span-2 lg:col-span-2">
                                <a href="{{ route('suppliers.index') }}" class="inline-flex justify-center px-4 py-2 bg-surface-overlay text-dg-300 font-medium rounded-lg border border-border text-sm text-center hover:bg-surface-elevated transition-colors">
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
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Fornecedor</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">CNPJ</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Contato</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Cotações</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="font-weight: 500; color: #e3e3e3;">{{ $supplier->name }}</div>
                                        @if($supplier->contact_person)
                                            <div style="font-size: 0.75rem; color: #818181;">{{ $supplier->contact_person }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #a4a4a4;">
                                        {{ $supplier->formatted_cnpj ?? '-' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #818181;">
                                        @if($supplier->phone || $supplier->email)
                                            <div>{{ $supplier->formatted_phone ?? '' }}</div>
                                            <div style="font-size: 0.75rem;">{{ $supplier->email ?? '' }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @if($supplier->active)
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(16,185,129,0.1); color: #16a34a; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                                Ativo
                                            </span>
                                        @else
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #222222; color: #818181; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                                Inativo
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #222222; color: #a4a4a4; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $supplier->quotations_count }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                            <a href="{{ route('quotations.create', ['supplier_id' => $supplier->id]) }}" style="color: #16a34a; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#15803d'" onmouseout="this.style.color='#16a34a'">+ Cotação</a>
                                            <a href="{{ route('suppliers.show', $supplier) }}" style="color: #818181; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Ver</a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" style="color: #818181; text-decoration: none; font-size: 0.875rem; font-weight: 500;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">Editar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 3rem; text-align: center; color: #818181;">
                                        Nenhum fornecedor encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($suppliers->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                        {{ $suppliers->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
