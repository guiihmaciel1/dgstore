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
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Painel de Cotações</h1>
                    <p class="text-sm text-gray-500">Compare preços entre fornecedores e visualize cotações</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ route('quotations.create') }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Nova Cotação</span>
                    </a>
                    <a href="{{ route('quotations.bulk-create') }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Cadastro Rápido</span>
                    </a>
                </div>
            </div>

            <!-- Cards de Resumo -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-5 rounded-xl border border-gray-200">
                    <div class="text-xs text-gray-500 uppercase font-semibold">Cotações Hoje</div>
                    <div class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $todayQuotations->count() }}</div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-200">
                    <div class="text-xs text-gray-500 uppercase font-semibold">Fornecedores Ativos</div>
                    <div class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $suppliers->count() }}</div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-200">
                    <div class="text-xs text-gray-500 uppercase font-semibold">Produtos Cotados</div>
                    <div class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $productNames->count() }}</div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Produto</label>
                        <input type="text" name="product_name" value="{{ $filters['product_name'] }}" placeholder="Buscar produto..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Fornecedor</label>
                        <select name="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:border-gray-900 focus:outline-none">
                            <option value="">Todos</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $filters['supplier_id'] == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Data Início</label>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Data Fim</label>
                        <input type="date" name="end_date" value="{{ $filters['end_date'] }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                    </div>
                    <div class="flex gap-2 sm:col-span-2 lg:col-span-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-900 text-white font-medium rounded-lg text-sm hover:bg-gray-700 transition-colors">
                            Filtrar
                        </button>
                        @if(array_filter($filters))
                            <a href="{{ route('quotations.index') }}" class="flex-1 px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 text-sm text-center hover:bg-gray-50 transition-colors">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Comparativo de Preços -->
            @if($priceComparison->count() > 0)
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #fef3c7;">
                        <h2 style="font-size: 1rem; font-weight: 600; color: #92400e; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Comparativo de Preços (Último Preço por Fornecedor)
                        </h2>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Fornecedores e Preços</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Menor Preço</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($priceComparison as $productName => $quotes)
                                    @php
                                        $lowestPrice = $quotes->first();
                                    @endphp
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 1rem 1.5rem;">
                                            <div style="font-weight: 500; color: #111827;">{{ $productName }}</div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem;">
                                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                                @foreach($quotes as $quote)
                                                    @php
                                                        $isLowest = $quote->id === $lowestPrice->id;
                                                    @endphp
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; {{ $isLowest ? 'background: #dcfce7; color: #166534; font-weight: 600;' : 'background: #f3f4f6; color: #374151;' }}">
                                                        {{ $quote->supplier->name }}: {{ $quote->formatted_unit_price }}
                                                        @if($isLowest)
                                                            <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            <span style="font-weight: 700; color: #16a34a; font-size: 1rem;">{{ $lowestPrice->formatted_unit_price }}</span>
                                            <div style="font-size: 0.75rem; color: #6b7280;">{{ $lowestPrice->supplier->name }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Histórico de Cotações -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <h2 style="font-size: 1rem; font-weight: 600; color: #111827;">Histórico de Cotações</h2>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Fornecedor</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Preço Unit.</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Registrado por</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotations as $quotation)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 0.75rem 1.5rem;">
                                        <div style="font-weight: 500; color: #111827;">{{ $quotation->product_name }}</div>
                                        @if($quotation->notes)
                                            <div style="font-size: 0.75rem; color: #6b7280;">{{ Str::limit($quotation->notes, 50) }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <a href="{{ route('suppliers.show', $quotation->supplier) }}" style="color: #111827; text-decoration: none; font-size: 0.875rem;"
                                           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                            {{ $quotation->supplier->name }}
                                        </a>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #16a34a; font-size: 0.875rem;">
                                        {{ $quotation->formatted_unit_price }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.875rem; color: #6b7280;">
                                        {{ $quotation->formatted_quantity }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.875rem; color: #6b7280;">
                                        {{ $quotation->quoted_at->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $quotation->user->name ?? '-' }}
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
                                    <td colspan="7" style="padding: 3rem; text-align: center; color: #6b7280;">
                                        Nenhuma cotação encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($quotations->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #f9fafb;">
                        {{ $quotations->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
