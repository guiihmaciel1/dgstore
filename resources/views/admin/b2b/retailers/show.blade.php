<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.b2b.retailers.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">{{ $retailer->store_name }}</h2>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Dados do Lojista -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Dados do Lojista</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Nome da Loja</dt>
                        <dd class="font-medium text-gray-900">{{ $retailer->store_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Responsável</dt>
                        <dd class="text-gray-900">{{ $retailer->owner_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">CNPJ/CPF</dt>
                        <dd class="text-gray-900">{{ $retailer->document }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ $retailer->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">WhatsApp</dt>
                        <dd class="text-gray-900">{{ $retailer->whatsapp }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Cidade</dt>
                        <dd class="text-gray-900">{{ $retailer->city }}/{{ $retailer->state }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Data de Cadastro</dt>
                        <dd class="text-gray-900">{{ $retailer->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Últimos Pedidos -->
            @if($retailer->orders->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Últimos Pedidos</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($retailer->orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">
                                            <a href="{{ route('admin.b2b.orders.show', $order) }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $order->order_number }}</a>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900 text-right">{{ $order->formatted_total }}</td>
                                        <td class="px-6 py-3 text-center">
                                            @php $color = $order->status->color(); @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">{{ $order->status->label() }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar - Ações -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Status do Lojista</h3>

                @php $color = $retailer->status->color(); @endphp
                <p class="text-sm mb-4">
                    Status atual:
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                        {{ $retailer->status->label() }}
                    </span>
                </p>

                <div class="space-y-2">
                    @if(!$retailer->isApproved())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved" />
                            <button type="submit" class="w-full py-2 px-4 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors">
                                Aprovar Lojista
                            </button>
                        </form>
                    @endif

                    @if(!$retailer->isBlocked())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}" onsubmit="return confirm('Tem certeza que deseja bloquear este lojista?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="blocked" />
                            <button type="submit" class="w-full py-2 px-4 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors">
                                Bloquear Lojista
                            </button>
                        </form>
                    @endif

                    @if($retailer->isBlocked())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="pending" />
                            <button type="submit" class="w-full py-2 px-4 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-200 transition-colors">
                                Mover para Pendente
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
