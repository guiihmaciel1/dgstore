<x-perfumes-admin-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Vendas</h1>
                <p class="text-sm text-gray-600 mt-1">Gerencie as vendas para consumidor final</p>
            </div>
            <a href="{{ route('admin.perfumes.sales.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Venda
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Estatísticas -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <div class="text-sm text-blue-600 font-medium">Total Vendido</div>
                <div class="text-2xl font-bold text-blue-900 mt-1">R$ {{ number_format($stats['total'], 2, ',', '.') }}</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="text-sm text-green-600 font-medium">Lucro</div>
                <div class="text-2xl font-bold text-green-900 mt-1">R$ {{ number_format($stats['profit'], 2, ',', '.') }}</div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <div class="text-sm text-purple-600 font-medium">Vendas</div>
                <div class="text-2xl font-bold text-purple-900 mt-1">{{ $stats['count'] }}</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                <div class="text-sm text-yellow-600 font-medium">Hoje</div>
                <div class="text-2xl font-bold text-yellow-900 mt-1">R$ {{ number_format($stats['today'], 2, ',', '.') }}</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <form method="GET" class="grid grid-cols-5 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar..."
                       class="px-4 py-2 border border-gray-300 rounded-lg">
                
                <select name="payment_status" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Status Pagamento</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Pago</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Parcial</option>
                </select>
                
                <select name="payment_method" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Forma Pagamento</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Dinheiro</option>
                    <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Cartão</option>
                    <option value="pix" {{ request('payment_method') === 'pix' ? 'selected' : '' }}>PIX</option>
                </select>
                
                <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                    Filtrar
                </button>
                
                @if(request()->hasAny(['search', 'payment_status', 'payment_method']))
                    <a href="{{ route('admin.perfumes.sales.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-center">
                        Limpar
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pagamento</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-medium text-gray-900">{{ $sale->sale_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $sale->customer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $sale->customer->formatted_phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sale->sold_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-gray-900">R$ {{ number_format($sale->total, 2, ',', '.') }}</div>
                                @if($sale->installments > 1)
                                    <div class="text-xs text-gray-500">{{ $sale->installments }}x</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                {{ $sale->payment_method->label() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($sale->payment_status->value === 'paid') bg-green-100 text-green-800
                                    @elseif($sale->payment_status->value === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($sale->payment_status->value === 'partial') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $sale->payment_status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.perfumes.sales.show', $sale) }}"
                                   class="text-pink-600 hover:text-pink-900">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <p class="mt-2">Nenhuma venda encontrada</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    </div>
</x-perfumes-admin-layout>
