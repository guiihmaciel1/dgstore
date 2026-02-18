<x-perfumes-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-gray-900">{{ $retailer->name }}</h2>
                @php
                    $statusBadgeClass = $retailer->status->badgeColor() === 'green'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700';
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadgeClass }}">
                    {{ $retailer->status->label() }}
                </span>
            </div>
            <a href="{{ route('admin.perfumes.retailers.edit', $retailer) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                Editar Lojista
            </a>
        </div>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.perfumes.retailers.index') }}"
           class="text-sm text-pink-600 hover:text-pink-700 font-medium inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar aos lojistas
        </a>
    </div>

    {{-- Info card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Informações</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500">Nome</dt>
                <dd class="mt-0.5 text-sm text-gray-900">{{ $retailer->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Proprietário</dt>
                <dd class="mt-0.5 text-sm text-gray-900">{{ $retailer->owner_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Documento</dt>
                <dd class="mt-0.5 text-sm text-gray-900">{{ $retailer->document ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">WhatsApp</dt>
                <dd class="mt-0.5">
                    @if($retailer->whatsapp)
                        <a href="{{ $retailer->whatsapp_link }}" target="_blank" rel="noopener noreferrer"
                           class="text-sm text-green-600 hover:text-green-700 font-medium">
                            {{ $retailer->whatsapp }}
                        </a>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Cidade/Estado</dt>
                <dd class="mt-0.5 text-sm text-gray-900">
                    {{ $retailer->city && $retailer->state ? "{$retailer->city}/{$retailer->state}" : ($retailer->city ?? $retailer->state ?? '—') }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Email</dt>
                <dd class="mt-0.5 text-sm text-gray-900">{{ $retailer->email ?? '—' }}</dd>
            </div>
            @if($retailer->notes)
            <div class="md:col-span-2">
                <dt class="text-xs font-medium text-gray-500">Observações</dt>
                <dd class="mt-0.5 text-sm text-gray-900 whitespace-pre-wrap">{{ $retailer->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pedidos</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalOrders, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gasto</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">R$ {{ number_format($totalSpent, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pendente Pagamento</p>
            <p class="mt-1 text-2xl font-bold {{ $pendingPayment > 0 ? 'text-red-600' : 'text-gray-900' }}">
                R$ {{ number_format($pendingPayment, 2, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Amostras Ativas --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Amostras Ativas</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-pink-50/40">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produto</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantidade</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data entrega</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Dias fora</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($retailer->activeSamples as $sample)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-sm font-medium text-gray-900">
                            {{ $sample->product->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">{{ $sample->quantity }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">
                            {{ $sample->delivered_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">
                            {{ $sample->days_out !== null ? $sample->days_out : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-500 text-sm">
                            Nenhuma amostra ativa.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Últimos Pedidos --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Últimos Pedidos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-pink-50/40">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pedido</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pagamento</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($retailer->orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.perfumes.orders.show', $order) }}"
                               class="text-sm font-medium text-pink-600 hover:text-pink-700">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">
                            R$ {{ number_format((float) $order->total, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $orderBadgeMap = ['blue' => 'bg-blue-100 text-blue-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'indigo' => 'bg-indigo-100 text-indigo-700', 'green' => 'bg-green-100 text-green-700', 'red' => 'bg-red-100 text-red-700'];
                                $orderBadgeClass = $orderBadgeMap[$order->status->badgeColor()] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $orderBadgeClass }}">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $payBadgeMap = ['red' => 'bg-red-100 text-red-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'green' => 'bg-green-100 text-green-700'];
                                $payBadgeClass = $payBadgeMap[$order->payment_status->badgeColor()] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payBadgeClass }}">
                                {{ $order->payment_status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700">
                            {{ $order->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500 text-sm">
                            Nenhum pedido encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-perfumes-admin-layout>
