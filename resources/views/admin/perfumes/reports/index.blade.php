<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Relatórios</h2>
    </x-slot>

    <!-- Filtro de período -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">De</label>
                <input type="date" name="from" value="{{ $from }}" class="rounded-lg border-gray-300 text-sm focus:ring-pink-500 focus:border-pink-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Até</label>
                <input type="date" name="to" value="{{ $to }}" class="rounded-lg border-gray-300 text-sm focus:ring-pink-500 focus:border-pink-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                Filtrar
            </button>
        </form>
    </div>

    <!-- KPIs do Período -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase">Faturamento</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($revenue, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase">Custo</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($cost, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase">Lucro</p>
            <p class="text-2xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">R$ {{ number_format($profit, 2, ',', '.') }}</p>
            @if($revenue > 0)
                <p class="text-xs text-gray-400 mt-1">Margem: {{ number_format(($profit / $revenue) * 100, 1) }}%</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Lojistas -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Top Lojistas por Volume</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Lojista</th>
                            <th class="px-4 py-3 text-right">Pedidos</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topRetailers as $r)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $r->name }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $r->orders_count }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">R$ {{ number_format($r->total_spent, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Nenhum dado no período</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Produtos -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Produtos Mais Vendidos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Produto</th>
                            <th class="px-4 py-3 text-left">Marca</th>
                            <th class="px-4 py-3 text-right">Qtd</th>
                            <th class="px-4 py-3 text-right">Receita</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topProducts as $p)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $p->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->brand }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $p->total_qty }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">R$ {{ number_format($p->total_revenue, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Nenhum dado no período</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Amostras em Campo -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Amostras em Campo</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Produto</th>
                        <th class="px-4 py-3 text-left">Lojista</th>
                        <th class="px-4 py-3 text-center">Qtd</th>
                        <th class="px-4 py-3 text-center">Entrega</th>
                        <th class="px-4 py-3 text-center">Dias Fora</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($samplesInField as $s)
                        <tr class="{{ $s->days_out > 30 ? 'bg-amber-50' : '' }}">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $s->product->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->retailer->name }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $s->quantity }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $s->delivered_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="{{ $s->days_out > 30 ? 'text-red-600 font-bold' : 'text-gray-600' }}">{{ $s->days_out }}d</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Nenhuma amostra em campo</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inadimplência -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Inadimplência (Consignação Pendente)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Pedido</th>
                        <th class="px-4 py-3 text-left">Lojista</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Pago</th>
                        <th class="px-4 py-3 text-right">Restante</th>
                        <th class="px-4 py-3 text-center">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($overdue as $o)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.perfumes.orders.show', $o) }}" class="text-pink-600 hover:text-pink-800 font-medium">{{ $o->order_number }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $o->retailer->name }}</td>
                            <td class="px-4 py-3 text-right font-medium">R$ {{ number_format($o->total, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-green-600">R$ {{ number_format($o->total_paid, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-red-600 font-bold">R$ {{ number_format($o->remaining, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $o->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Nenhuma inadimplência</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-perfumes-admin-layout>
