<x-perfumes-admin-layout>
    <div class="p-4">
        <!-- Header Compacto -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-6">
                <h1 class="text-xl font-bold text-gray-900">Vendas</h1>
                
                <!-- Estatísticas Inline -->
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-bold text-blue-600">R$ {{ number_format($stats['total'], 2, ',', '.') }}</span>
                    </div>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Lucro:</span>
                        <span class="font-bold text-green-600">R$ {{ number_format($stats['profit'], 2, ',', '.') }}</span>
                    </div>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Vendas:</span>
                        <span class="font-bold text-purple-600">{{ $stats['count'] }}</span>
                    </div>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Hoje:</span>
                        <span class="font-bold text-yellow-600">R$ {{ number_format($stats['today'], 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('admin.perfumes.sales.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Venda
            </a>
        </div>

        @if(session('success'))
            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filtros Compactos -->
        <div class="mb-4 bg-white rounded-lg shadow-sm p-3">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar..."
                       class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                
                <select name="payment_status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    <option value="">Status</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Pago</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Parcial</option>
                </select>
                
                <select name="payment_method" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    <option value="">Forma</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Dinheiro</option>
                    <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Cartão</option>
                    <option value="pix" {{ request('payment_method') === 'pix' ? 'selected' : '' }}>PIX</option>
                </select>
                
                <button type="submit" class="px-4 py-2 text-sm bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                    Filtrar
                </button>
                
                @if(request()->hasAny(['search', 'payment_status', 'payment_method']))
                    <a href="{{ route('admin.perfumes.sales.index') }}"
                       class="px-4 py-2 text-sm bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Limpar
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabela Compacta -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Pagamento</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="text-xs font-mono font-medium text-gray-900">{{ $sale->sale_number }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-medium text-gray-900">{{ $sale->customer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $sale->customer->formatted_phone }}</div>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-500">
                                {{ $sale->sold_at->format('d/m/Y') }}
                                <div class="text-[10px] text-gray-400">{{ $sale->sold_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-gray-900">R$ {{ number_format($sale->total, 2, ',', '.') }}</div>
                                @if($sale->installments > 1)
                                    <div class="text-[10px] text-gray-500">{{ $sale->installments }}x</div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-center text-xs">
                                {{ $sale->payment_method->label() }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-center">
                                <span class="px-2 py-0.5 text-[10px] font-medium rounded-full
                                    @if($sale->payment_status->value === 'paid') bg-green-100 text-green-800
                                    @elseif($sale->payment_status->value === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($sale->payment_status->value === 'partial') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $sale->payment_status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-right text-xs font-medium">
                                <a href="{{ route('admin.perfumes.sales.show', $sale) }}"
                                   class="text-pink-600 hover:text-pink-900">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <p class="mt-2 text-sm">Nenhuma venda encontrada</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
</x-perfumes-admin-layout>
