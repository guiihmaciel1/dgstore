<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Consulta IMEI</h1>
                <p class="text-sm text-gray-500 mt-1">Busque por IMEI para ver produto, venda, garantia e trade-in</p>
            </div>

            <!-- Campo de busca -->
            <form method="GET" action="{{ route('imei-lookup') }}" class="mb-8">
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="imei"
                               value="{{ $imei }}"
                               placeholder="Digite o IMEI (15 dígitos)"
                               autofocus
                               class="block w-full pl-12 pr-4 py-4 text-lg font-mono border-gray-300 rounded-xl shadow-sm focus:ring-gray-900 focus:border-gray-900 tracking-wider"
                               maxlength="20">
                    </div>
                    <button type="submit"
                            class="px-8 py-4 bg-gray-900 text-white rounded-xl font-semibold hover:bg-gray-800 transition-colors shadow-sm">
                        Consultar
                    </button>
                </div>
            </form>

            @if($results)
                <!-- Link Anatel - Celular Legal -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-blue-900">Verificar na Anatel (Celular Legal)</p>
                            <p class="text-xs text-blue-600 truncate">Consulte se o IMEI está regular, homologado ou com restrição por roubo/furto</p>
                        </div>
                    </div>
                    <a href="https://www.gov.br/anatel/pt-br/assuntos/celular-legal/consulte-sua-situacao"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shrink-0">
                        Consultar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>

                @if($results['found'])
                    <!-- Status flags -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 font-mono tracking-wider">
                            IMEI: {{ $imei }}
                        </span>
                        @foreach($results['status'] as $flag)
                            @php
                                $colorMap = [
                                    'green' => 'bg-green-100 text-green-800',
                                    'red' => 'bg-red-100 text-red-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                    'blue' => 'bg-blue-100 text-blue-800',
                                    'purple' => 'bg-purple-100 text-purple-800',
                                    'orange' => 'bg-orange-100 text-orange-800',
                                    'gray' => 'bg-gray-100 text-gray-600',
                                ];
                                $classes = $colorMap[$flag['color']] ?? $colorMap['gray'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $classes }}">
                                {{ $flag['label'] }}
                            </span>
                        @endforeach
                    </div>

                    <div class="space-y-6">
                        {{-- ═══ PRODUTO ═══ --}}
                        @if($results['product'])
                            @php $product = $results['product']; @endphp
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900">Produto</h3>
                                    </div>
                                    <a href="{{ route('products.show', $product) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                        Ver produto &rarr;
                                    </a>
                                </div>
                                <div class="px-6 py-4">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Nome</span>
                                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">SKU</span>
                                            <p class="font-mono text-gray-700">{{ $product->sku }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Condição</span>
                                            <p class="text-gray-700">{{ $product->condition->label() }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Preço Venda</span>
                                            <p class="font-bold text-green-600">{{ $product->formatted_sale_price }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Estoque</span>
                                            <p class="font-semibold {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $product->stock_quantity }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Status</span>
                                            <p class="font-medium {{ $product->active ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $product->active ? 'Ativo' : 'Inativo' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ═══ VENDA ═══ --}}
                        @if($results['sale'])
                            @php $sale = $results['sale']; @endphp
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-purple-50 border-b border-purple-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-purple-600 text-white flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900">Venda</h3>
                                    </div>
                                    <a href="{{ route('sales.show', $sale) }}" class="text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors">
                                        Ver venda &rarr;
                                    </a>
                                </div>
                                <div class="px-6 py-4">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Número</span>
                                            <p class="font-bold text-gray-900">#{{ $sale->sale_number }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Data</span>
                                            <p class="text-gray-700">{{ $sale->sold_at?->format('d/m/Y H:i') ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Total</span>
                                            <p class="font-bold text-gray-900">{{ $sale->formatted_total }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Cliente</span>
                                            <p class="text-gray-700">
                                                @if($sale->customer)
                                                    <a href="{{ route('customers.show', $sale->customer) }}" class="text-purple-600 hover:underline">
                                                        {{ $sale->customer->name }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">Não informado</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Pagamento</span>
                                            <p class="text-gray-700">{{ $sale->payment_method?->label() ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Status</span>
                                            @php
                                                $statusColors = [
                                                    'paid' => 'bg-green-100 text-green-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'partial' => 'bg-blue-100 text-blue-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ];
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$sale->payment_status->value] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $sale->payment_status->label() ?? $sale->payment_status->value }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ═══ GARANTIA ═══ --}}
                        @if($results['warranty'])
                            @php $warranty = $results['warranty']; @endphp
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-green-50 border-b border-green-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-green-600 text-white flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900">Garantia</h3>
                                    </div>
                                    <a href="{{ route('warranties.show', $warranty) }}" class="text-sm font-medium text-green-600 hover:text-green-800 transition-colors">
                                        Ver garantia &rarr;
                                    </a>
                                </div>
                                <div class="px-6 py-4">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Garantia Fornecedor</span>
                                            @if($warranty->supplier_warranty_until)
                                                <p class="font-semibold {{ $warranty->is_supplier_warranty_active ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $warranty->is_supplier_warranty_active ? 'Ativa' : 'Expirada' }}
                                                    <span class="text-xs text-gray-500 font-normal block">
                                                        até {{ $warranty->supplier_warranty_until->format('d/m/Y') }}
                                                        @if($warranty->is_supplier_warranty_active)
                                                            ({{ $warranty->supplier_days_remaining }}d restantes)
                                                        @endif
                                                    </span>
                                                </p>
                                            @else
                                                <p class="text-gray-400">Não registrada</p>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Garantia Cliente</span>
                                            @if($warranty->customer_warranty_until)
                                                <p class="font-semibold {{ $warranty->is_customer_warranty_active ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $warranty->is_customer_warranty_active ? 'Ativa' : 'Expirada' }}
                                                    <span class="text-xs text-gray-500 font-normal block">
                                                        até {{ $warranty->customer_warranty_until->format('d/m/Y') }}
                                                        @if($warranty->is_customer_warranty_active)
                                                            ({{ $warranty->customer_days_remaining }}d restantes)
                                                        @endif
                                                    </span>
                                                </p>
                                            @else
                                                <p class="text-gray-400">Não registrada</p>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Acionamentos</span>
                                            <p class="font-semibold text-gray-900">
                                                {{ $warranty->claims->count() }}
                                                @if($warranty->open_claims_count > 0)
                                                    <span class="text-xs text-orange-600">({{ $warranty->open_claims_count }} abertos)</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ═══ TRADE-IN ═══ --}}
                        @if($results['trade_in'])
                            @php $tradeIn = $results['trade_in']; @endphp
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900">Trade-in</h3>
                                    </div>
                                    @if($tradeIn->sale)
                                        <a href="{{ route('sales.show', $tradeIn->sale) }}" class="text-sm font-medium text-amber-600 hover:text-amber-800 transition-colors">
                                            Ver venda de origem &rarr;
                                        </a>
                                    @endif
                                </div>
                                <div class="px-6 py-4">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Aparelho</span>
                                            <p class="font-semibold text-gray-900">{{ $tradeIn->full_name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Valor</span>
                                            <p class="font-bold text-gray-900">{{ $tradeIn->formatted_value }}</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wider">Status</span>
                                            @php
                                                $tiColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'processed' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                ];
                                                $tiLabels = [
                                                    'pending' => 'Pendente',
                                                    'processed' => 'Processado',
                                                    'rejected' => 'Rejeitado',
                                                ];
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $tiColors[$tradeIn->status->value] ?? 'bg-gray-100' }}">
                                                {{ $tiLabels[$tradeIn->status->value] ?? $tradeIn->status->value }}
                                            </span>
                                        </div>
                                        @if($tradeIn->sale?->customer)
                                            <div>
                                                <span class="text-xs text-gray-500 uppercase tracking-wider">Cliente (venda origem)</span>
                                                <p class="text-gray-700">{{ $tradeIn->sale->customer->name }}</p>
                                            </div>
                                        @endif
                                        @if($tradeIn->product)
                                            <div>
                                                <span class="text-xs text-gray-500 uppercase tracking-wider">Produto gerado</span>
                                                <a href="{{ route('products.show', $tradeIn->product) }}" class="text-purple-600 hover:underline font-medium">
                                                    {{ $tradeIn->product->name }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($tradeIn->condition)
                                            <div>
                                                <span class="text-xs text-gray-500 uppercase tracking-wider">Condição</span>
                                                <p class="text-gray-700">{{ $tradeIn->condition->label() }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- IMEI não encontrado -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">IMEI não encontrado</h3>
                        <p class="text-sm text-gray-500 font-mono mb-2">{{ $imei }}</p>
                        <p class="text-sm text-gray-500">Nenhum produto, venda, garantia ou trade-in registrado com este IMEI.</p>
                    </div>
                @endif
            @else
                <!-- Estado inicial -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Consulta rápida por IMEI</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto">
                        Digite o IMEI do aparelho para ver todas as informações:
                        produto, venda, garantia e trade-in em um único lugar.
                    </p>
                    <div class="mt-6 flex justify-center gap-6 text-xs text-gray-400">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-gray-900"></div> Produto
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-purple-500"></div> Venda
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div> Garantia
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div> Trade-in
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
