<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div class="flex items-center">
                    <a href="{{ route('suppliers.index') }}" class="mr-3 sm:mr-4 p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg sm:text-2xl font-bold text-gray-900 truncate">{{ $supplier->name }}</h1>
                            @if(!$supplier->active)
                                <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">Inativo</span>
                            @endif
                        </div>
                        @if($supplier->cnpj)
                            <p class="text-sm text-gray-500">CNPJ: {{ $supplier->formatted_cnpj }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('quotations.create', ['supplier_id' => $supplier->id]) }}" 
                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Nova Cotação</span>
                    </a>
                    <a href="{{ route('suppliers.edit', $supplier) }}" 
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 sm:px-6 py-2.5 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Editar
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #16a34a;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-4 lg:gap-6">
                <!-- Coluna Principal -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Informações do Fornecedor -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Informações do Fornecedor</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Origem</dt>
                                    <dd style="margin-top: 0.25rem;">
                                        @if($supplier->origin)
                                            @php
                                                $originColors = [
                                                    'py' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Paraguai (PY) - Frete 4%'],
                                                    'br' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'label' => 'Brasil (BR) - Sem frete'],
                                                ];
                                                $oc = $originColors[$supplier->origin->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => '-'];
                                            @endphp
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $oc['bg'] }}; color: {{ $oc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                                {{ $oc['label'] }}
                                            </span>
                                        @else
                                            <span style="font-size: 0.875rem; color: #9ca3af;">Não definida</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Telefone</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $supplier->formatted_phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">E-mail</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $supplier->email ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Pessoa de Contato</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $supplier->contact_person ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Status</dt>
                                    <dd style="margin-top: 0.25rem;">
                                        @if($supplier->active)
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f0fdf4; color: #16a34a; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">Ativo</span>
                                        @else
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #f3f4f6; color: #6b7280; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">Inativo</span>
                                        @endif
                                    </dd>
                                </div>
                                @if($supplier->address)
                                <div style="grid-column: span 2;">
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Endereço</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $supplier->address }}</dd>
                                </div>
                                @endif
                                @if($supplier->notes)
                                <div style="grid-column: span 2;">
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Observações</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $supplier->notes }}</dd>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Cotações Recentes -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-weight: 600; color: #111827;">Cotações Recentes</h3>
                            <a href="{{ route('quotations.index', ['supplier_id' => $supplier->id]) }}" style="font-size: 0.875rem; color: #111827; text-decoration: none;"
                               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                Ver todas →
                            </a>
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Preço Unit.</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                        <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quotations as $quotation)
                                        <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                            <td style="padding: 0.75rem 1.5rem;">
                                                <div style="font-weight: 500; color: #111827;">{{ $quotation->product_name }}</div>
                                                @if($quotation->product)
                                                    <div style="font-size: 0.75rem; color: #6b7280;">SKU: {{ $quotation->product->sku }}</div>
                                                @endif
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #16a34a;">
                                                {{ $quotation->formatted_unit_price }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.875rem; color: #6b7280;">
                                                {{ $quotation->formatted_quantity }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.875rem; color: #6b7280;">
                                                {{ $quotation->quoted_at->format('d/m/Y') }}
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                                <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" 
                                                      onsubmit="return confirm('Excluir esta cotação?');" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="color: #dc2626; background: none; border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                                                        Excluir
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="padding: 3rem; text-align: center; color: #6b7280;">
                                                Nenhuma cotação registrada.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Resumo -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Resumo</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="margin-bottom: 1.5rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Total de Cotações</dt>
                                <dd style="margin-top: 0.25rem; font-size: 2rem; font-weight: 700; color: #111827;">{{ $quotations->count() }}</dd>
                            </div>
                            @if($supplier->latest_quotation)
                            <div style="margin-bottom: 1.5rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Última Cotação</dt>
                                <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $supplier->latest_quotation->quoted_at->format('d/m/Y') }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Cadastrado em</dt>
                                <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $supplier->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Ações Rápidas</h3>
                        </div>
                        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                            <a href="{{ route('quotations.create', ['supplier_id' => $supplier->id]) }}" 
                               style="display: block; width: 100%; padding: 0.75rem; background: #16a34a; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none; text-align: center;"
                               onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                                + Nova Cotação
                            </a>
                            <a href="{{ route('quotations.bulk-create') }}" 
                               style="display: block; width: 100%; padding: 0.75rem; background: #f3f4f6; color: #374151; font-weight: 500; border-radius: 0.5rem; text-decoration: none; text-align: center; border: 1px solid #e5e7eb;"
                               onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                Cadastro Rápido
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
