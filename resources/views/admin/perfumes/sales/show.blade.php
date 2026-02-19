<x-perfumes-admin-layout>
    <div class="p-4 max-w-6xl mx-auto">
        <div class="mb-3">
            <a href="{{ route('admin.perfumes.sales.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Voltar para vendas
            </a>
        </div>

        @if(session('success'))
            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-3 gap-4">
            <!-- Informações Principais -->
            <div class="col-span-2 space-y-3">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Venda {{ $sale->sale_number }}</h1>
                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium rounded-full bg-{{ $sale->payment_status->badgeColor() }}-100 text-{{ $sale->payment_status->badgeColor() }}-800">
                                {{ $sale->payment_status->label() }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Cliente:</span>
                            <div class="font-medium text-gray-900 mt-1">
                                <a href="{{ route('admin.perfumes.customers.show', $sale->customer) }}"
                                   class="text-pink-600 hover:text-pink-900">
                                    {{ $sale->customer->name }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $sale->customer->formatted_phone }}</div>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-600">Data:</span>
                            <div class="font-medium text-gray-900 mt-1">{{ $sale->sold_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Vendedor:</span>
                            <div class="font-medium text-gray-900 mt-1">{{ $sale->user->name }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Pagamento:</span>
                            <div class="font-medium text-gray-900 mt-1">
                                {{ $sale->payment_method->label() }}
                                @if($sale->installments > 1)
                                    <span class="text-xs text-gray-500">({{ $sale->installments }}x)</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($sale->notes)
                        <div class="mt-3 pt-3 border-t text-sm">
                            <span class="text-gray-600">Observações:</span>
                            <div class="text-gray-900 mt-1">{{ $sale->notes }}</div>
                        </div>
                    @endif
                </div>

                <!-- Itens da Venda -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-base font-bold text-gray-900 mb-3">Itens da Venda</h3>
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Produto</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Qtd</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Preço Unit.</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($sale->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-sm">
                                            <div class="font-medium text-gray-900">
                                                @if($item->product)
                                                    {{ $item->product->name }}
                                                    @if($item->product->brand)
                                                        <span class="text-gray-500">- {{ $item->product->brand }}</span>
                                                    @endif
                                                @else
                                                    {{ $item->product_snapshot['name'] ?? 'Produto removido' }}
                                                @endif
                                            </div>
                                            @if($item->product && $item->product->size_ml)
                                                <div class="text-xs text-gray-500">{{ $item->product->size_ml }}ml</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center text-sm font-medium">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-sm">
                                            R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-sm font-semibold">
                                            R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Análise de Lucro -->
                @if($profitAnalysis)
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <h3 class="text-base font-bold text-gray-900 mb-3">Análise de Lucro</h3>
                        <div class="grid grid-cols-4 gap-3">
                            <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                <div class="text-xs text-blue-600 font-medium">Custo Total</div>
                                <div class="text-lg font-bold text-blue-900 mt-1">R$ {{ number_format($profitAnalysis['total_cost'], 2, ',', '.') }}</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                <div class="text-xs text-green-600 font-medium">Receita</div>
                                <div class="text-lg font-bold text-green-900 mt-1">R$ {{ number_format($profitAnalysis['revenue'], 2, ',', '.') }}</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                                <div class="text-xs text-purple-600 font-medium">Lucro Líquido</div>
                                <div class="text-lg font-bold text-purple-900 mt-1">R$ {{ number_format($profitAnalysis['profit'], 2, ',', '.') }}</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                                <div class="text-xs text-yellow-600 font-medium">Margem</div>
                                <div class="text-lg font-bold text-yellow-900 mt-1">{{ number_format($profitAnalysis['margin'], 1) }}%</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-3">
                <!-- Resumo Financeiro -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Resumo</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">R$ {{ number_format($sale->subtotal, 2, ',', '.') }}</span>
                        </div>
                        @if($sale->discount > 0)
                            <div class="flex justify-between text-red-600">
                                <span>Desconto:</span>
                                <span class="font-medium">- R$ {{ number_format($sale->discount, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t">
                            <span class="font-bold text-gray-900">Total:</span>
                            <span class="font-bold text-pink-700 text-lg">R$ {{ number_format($sale->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Origem da Venda -->
                @if($sale->originReservation)
                    <div class="bg-purple-50 rounded-lg border border-purple-200 p-4">
                        <h3 class="text-xs font-bold text-purple-900 mb-2">Origem</h3>
                        <p class="text-xs text-purple-800 mb-2">
                            Convertida da encomenda:
                        </p>
                        <a href="{{ route('admin.perfumes.reservations.show', $sale->originReservation) }}"
                           class="text-sm font-mono text-purple-700 hover:text-purple-900 font-medium">
                            {{ $sale->originReservation->reservation_number }}
                        </a>
                    </div>
                @endif

                <!-- Ações -->
                @if($sale->payment_status->value !== 'cancelled')
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-2">Ações</h3>
                        <div class="space-y-2">
                            <button onclick="window.print()"
                                    class="w-full px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Imprimir Recibo
                            </button>
                            @if($sale->payment_status->value !== 'cancelled')
                                <form method="POST" action="{{ route('admin.perfumes.sales.cancel', $sale) }}"
                                      onsubmit="return confirm('Tem certeza que deseja cancelar esta venda? O estoque será atualizado.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                        Cancelar Venda
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-perfumes-admin-layout>
