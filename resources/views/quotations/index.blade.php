@php
    function quotationFlag(string $name): string {
        $upper = mb_strtoupper($name);
        if (preg_match('/\bUSA\b/', $upper) || preg_match('/[A-Z0-9]LL\b/', $upper)) {
            return '<svg style="width:1.125rem;height:0.75rem;display:inline-block;vertical-align:middle;border-radius:2px;flex-shrink:0" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg"><rect width="36" height="24" fill="#B22234"/><rect y="1.85" width="36" height="1.85" fill="#fff"/><rect y="5.54" width="36" height="1.85" fill="#fff"/><rect y="9.23" width="36" height="1.85" fill="#fff"/><rect y="12.92" width="36" height="1.85" fill="#fff"/><rect y="16.62" width="36" height="1.85" fill="#fff"/><rect y="20.31" width="36" height="1.85" fill="#fff"/><rect width="14.4" height="12.92" fill="#3C3B6E"/><text x="7.2" y="8" text-anchor="middle" fill="#fff" font-size="5" font-family="sans-serif">★</text></svg>';
        }
        if (preg_match('/\bJP\b/', $upper) || preg_match('/\sJ\s*$/', $upper) || preg_match('/\sJ\s+-/', $upper)) {
            return '<svg style="width:1.125rem;height:0.75rem;display:inline-block;vertical-align:middle;border-radius:2px;flex-shrink:0" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg"><rect width="36" height="24" fill="#fff"/><circle cx="18" cy="12" r="7.2" fill="#BC002D"/></svg>';
        }
        return '';
    }

    function formatFinalPrice(float $unitPrice): string {
        $final = $unitPrice * 1.04;
        $diff = $final - $unitPrice;
        return '<span style="font-weight:700;color:#16a34a;">R$ ' . number_format($final, 2, ',', '.') . '</span>'
             . '<div style="font-size:0.6875rem;color:#ca8a04;">+R$ ' . number_format($diff, 2, ',', '.') . '</div>';
    }
@endphp
<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="quotationIndex()">
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
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Cadastro Rápido</span>
                    </a>
                    <a href="{{ route('quotations.import') }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Importar Cotação</span>
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
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #16a34a; text-transform: uppercase;">Final (+4%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($priceComparison as $productName => $quotes)
                                    @php
                                        $lowestPrice = $quotes->first();
                                    @endphp
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 1rem 1.5rem;">
                                            <a href="https://www.google.com/search?q={{ urlencode($productName) }}" target="_blank" rel="noopener"
                                               style="font-weight: 500; color: #111827; display: inline-flex; align-items: center; gap: 0.375rem; text-decoration: none; cursor: pointer;"
                                               onmouseover="this.style.color='#2563eb'; this.querySelector('.search-icon').style.opacity='1'"
                                               onmouseout="this.style.color='#111827'; this.querySelector('.search-icon').style.opacity='0'">
                                                {!! quotationFlag($productName) !!} {{ $productName }}
                                                <svg class="search-icon" style="width: 0.875rem; height: 0.875rem; opacity: 0; transition: opacity 0.15s; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                            </a>
                                        </td>
                                        <td style="padding: 0.75rem 1rem;">
                                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                                @foreach($quotes as $quote)
                                                    @php
                                                        $isLowest = $quote->id === $lowestPrice->id;
                                                    @endphp
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; {{ $isLowest ? 'background: #dcfce7; color: #166534; font-weight: 600;' : 'background: #f3f4f6; color: #374151;' }}">
                                                        {{ $quote->supplier->name }}:
                                                        @if($quote->price_usd)
                                                            {{ $quote->formatted_price_usd }} <span style="color: #9ca3af;">({{ $quote->formatted_unit_price }})</span>
                                                        @else
                                                            {{ $quote->formatted_unit_price }}
                                                        @endif
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
                                            @if($lowestPrice->price_usd)
                                                <span style="font-weight: 700; color: #2563eb; font-size: 1rem;">{{ $lowestPrice->formatted_price_usd }}</span>
                                                <div style="font-size: 0.75rem; color: #16a34a;">{{ $lowestPrice->formatted_unit_price }}</div>
                                            @else
                                                <span style="font-weight: 700; color: #16a34a; font-size: 1rem;">{{ $lowestPrice->formatted_unit_price }}</span>
                                            @endif
                                            <div style="font-size: 0.75rem; color: #6b7280;">{{ $lowestPrice->supplier->name }}</div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            <div style="text-align: center;">{!! formatFinalPrice((float) $lowestPrice->unit_price) !!}</div>
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
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="font-size: 1rem; font-weight: 600; color: #111827;">Histórico de Cotações</h2>
                    <!-- Botão excluir selecionados -->
                    <div x-show="selectedIds.length > 0" x-cloak
                         style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 0.8125rem; color: #6b7280;" x-text="selectedIds.length + ' selecionado(s)'"></span>
                        <button type="button" @click="bulkDelete()"
                                style="padding: 0.375rem 0.75rem; font-size: 0.8125rem; font-weight: 500; border: 1px solid #fecaca; border-radius: 0.375rem; background: #fef2f2; color: #dc2626; cursor: pointer; display: inline-flex; align-items: center; gap: 0.375rem;"
                                onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Excluir Selecionados
                        </button>
                    </div>
                </div>

                <!-- Form oculto para exclusão em massa -->
                <form id="bulk-delete-form" method="POST" action="{{ route('quotations.bulk-destroy') }}" style="display: none;">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                </form>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 0.75rem 0.75rem 1.5rem; text-align: center; width: 2.5rem;">
                                    <input type="checkbox" @change="toggleAll($event.target.checked)"
                                           :checked="allSelected" :indeterminate.prop="someSelected"
                                           style="width: 0.875rem; height: 0.875rem; cursor: pointer; accent-color: #111827;">
                                </th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Fornecedor</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Preço</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #16a34a; text-transform: uppercase;">Final (+4%)</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Registrado por</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotations as $quotation)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 0.5rem 0.75rem 0.5rem 1.5rem; text-align: center;">
                                        <input type="checkbox" value="{{ $quotation->id }}"
                                               @change="toggleId('{{ $quotation->id }}', $event.target.checked)"
                                               :checked="selectedIds.includes('{{ $quotation->id }}')"
                                               style="width: 0.875rem; height: 0.875rem; cursor: pointer; accent-color: #111827;">
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <a href="https://www.google.com/search?q={{ urlencode($quotation->product_name) }}" target="_blank" rel="noopener"
                                           style="font-weight: 500; color: #111827; display: inline-flex; align-items: center; gap: 0.375rem; text-decoration: none; cursor: pointer;"
                                           onmouseover="this.style.color='#2563eb'; this.querySelector('.search-icon').style.opacity='1'"
                                           onmouseout="this.style.color='#111827'; this.querySelector('.search-icon').style.opacity='0'">
                                            {!! quotationFlag($quotation->product_name) !!} {{ $quotation->product_name }}
                                            <svg class="search-icon" style="width: 0.875rem; height: 0.875rem; opacity: 0; transition: opacity 0.15s; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </a>
                                        @if($quotation->category)
                                            <div><span style="font-size: 0.6875rem; padding: 0.0625rem 0.375rem; border-radius: 9999px; background: #f3f4f6; color: #6b7280;">{{ $quotation->category }}</span></div>
                                        @endif
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
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem;">
                                        @if($quotation->price_usd)
                                            <div style="font-weight: 600; color: #2563eb;">{{ $quotation->formatted_price_usd }}</div>
                                            <div style="font-size: 0.75rem; color: #16a34a;">{{ $quotation->formatted_unit_price }}</div>
                                        @else
                                            <div style="font-weight: 600; color: #16a34a;">{{ $quotation->formatted_unit_price }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem;">
                                        {!! formatFinalPrice((float) $quotation->unit_price) !!}
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
                                    <td colspan="9" style="padding: 3rem; text-align: center; color: #6b7280;">
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

    @push('scripts')
    <style>[x-cloak] { display: none !important; }</style>
    <script>
        function quotationIndex() {
            return {
                selectedIds: [],
                allIds: @json($quotations->pluck('id')->values()),

                get allSelected() {
                    return this.allIds.length > 0 && this.selectedIds.length === this.allIds.length;
                },

                get someSelected() {
                    return this.selectedIds.length > 0 && this.selectedIds.length < this.allIds.length;
                },

                toggleAll(checked) {
                    this.selectedIds = checked ? [...this.allIds] : [];
                },

                toggleId(id, checked) {
                    if (checked) {
                        if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                    } else {
                        this.selectedIds = this.selectedIds.filter(i => i !== id);
                    }
                },

                bulkDelete() {
                    if (this.selectedIds.length === 0) return;
                    if (!confirm('Excluir ' + this.selectedIds.length + ' cotação(ões) selecionada(s)?')) return;
                    document.getElementById('bulk-delete-form').submit();
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
