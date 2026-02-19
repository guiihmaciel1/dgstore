<x-perfumes-admin-layout>
    <div class="p-4 max-w-6xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('admin.perfumes.customers.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Voltar para clientes
            </a>
        </div>

        @if(session('success'))
            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-3 gap-4">
            <!-- Informações do Cliente -->
            <div class="col-span-2 bg-white rounded-lg shadow-sm p-4">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">Cliente #{{ substr($customer->id, 0, 8) }}</p>
                    </div>
                    <a href="{{ route('admin.perfumes.customers.edit', $customer) }}"
                       class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition">
                        Editar
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Telefone:</span>
                        <div class="font-medium text-gray-900 mt-1">{{ $customer->formatted_phone }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">CPF:</span>
                        <div class="font-medium text-gray-900 mt-1">{{ $customer->formatted_cpf ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">E-mail:</span>
                        <div class="font-medium text-gray-900 mt-1">{{ $customer->email ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Data de Nascimento:</span>
                        <div class="font-medium text-gray-900 mt-1">{{ $customer->birth_date?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </div>

                @if($customer->address)
                    <div class="mt-4 text-sm">
                        <span class="text-gray-600">Endereço:</span>
                        <div class="font-medium text-gray-900 mt-1">{{ $customer->address }}</div>
                    </div>
                @endif

                @if($customer->notes)
                    <div class="mt-4 text-sm">
                        <span class="text-gray-600">Observações:</span>
                        <div class="text-gray-900 mt-1">{{ $customer->notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Estatísticas -->
            <div class="space-y-3">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-xs text-blue-600 font-medium">Total de Compras</div>
                    <div class="text-2xl font-bold text-blue-900 mt-1">{{ $totalPurchases }}</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-xs text-green-600 font-medium">Valor Gasto</div>
                    <div class="text-xl font-bold text-green-900 mt-1">R$ {{ number_format($totalSpent, 2, ',', '.') }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-xs text-purple-600 font-medium">Encomendas Ativas</div>
                    <div class="text-2xl font-bold text-purple-900 mt-1">{{ $activeReservations }}</div>
                </div>
                @if($lastPurchase)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="text-xs text-gray-600 font-medium">Última Compra</div>
                        <div class="text-sm font-bold text-gray-900 mt-1">{{ $lastPurchase->format('d/m/Y') }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Histórico de Vendas -->
        <div class="mt-4 bg-white rounded-lg shadow-sm p-4">
            <h2 class="text-base font-bold text-gray-900 mb-3">Histórico de Vendas</h2>
            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Número</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Data</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($customer->sales as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-xs font-mono">{{ $sale->sale_number }}</td>
                                <td class="px-4 py-2 text-xs">{{ $sale->sold_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-right text-sm font-semibold">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-{{ $sale->payment_status->badgeColor() }}-100 text-{{ $sale->payment_status->badgeColor() }}-800">
                                        {{ $sale->payment_status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('admin.perfumes.sales.show', $sale) }}" class="text-xs text-pink-600 hover:text-pink-900">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                    Nenhuma venda realizada
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Encomendas Ativas -->
        @if($customer->reservations->where('status', 'active')->count() > 0)
            <div class="mt-4 bg-white rounded-lg shadow-sm p-4">
                <h2 class="text-base font-bold text-gray-900 mb-3">Encomendas Ativas</h2>
                <div class="space-y-2">
                    @foreach($customer->reservations->where('status', 'active') as $reservation)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $reservation->reservation_number }}</div>
                                    <div class="text-xs text-gray-600">{{ $reservation->product?->name ?? $reservation->product_description }}</div>
                                </div>
                                <a href="{{ route('admin.perfumes.reservations.show', $reservation) }}"
                                   class="text-xs text-pink-600 hover:text-pink-900 font-medium">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-perfumes-admin-layout>
