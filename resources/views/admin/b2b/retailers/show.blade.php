<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.b2b.retailers.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-900 flex items-center justify-center text-sm font-bold text-white uppercase">
                        {{ substr($retailer->store_name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $retailer->store_name }}</h2>
                        <p class="text-xs text-gray-500">{{ $retailer->owner_name }} &middot; Cadastrado em {{ $retailer->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="https://wa.me/{{ $retailer->formatted_whatsapp }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                    WhatsApp
                </a>
                <a href="{{ route('admin.b2b.retailers.edit', $retailer) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Dados do Lojista -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Dados da Loja
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 mb-0.5">Nome da Loja</dt>
                        <dd class="font-medium text-gray-900">{{ $retailer->store_name }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 mb-0.5">Responsável</dt>
                        <dd class="text-gray-900">{{ $retailer->owner_name }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 mb-0.5">CNPJ/CPF</dt>
                        <dd class="text-gray-900">{{ $retailer->document }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 mb-0.5">Email</dt>
                        <dd class="text-gray-900">{{ $retailer->email }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 mb-0.5">WhatsApp</dt>
                        <dd class="text-gray-900">
                            <a href="https://wa.me/{{ $retailer->formatted_whatsapp }}" target="_blank" class="text-green-600 hover:text-green-800">
                                {{ $retailer->whatsapp }}
                            </a>
                        </dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 mb-0.5">Cidade</dt>
                        <dd class="text-gray-900">{{ $retailer->city }}/{{ $retailer->state }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 mb-0.5">Data de Cadastro</dt>
                        <dd class="text-gray-900">{{ $retailer->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-500 mb-0.5">Última Atualização</dt>
                        <dd class="text-gray-900">{{ $retailer->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Últimos Pedidos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Histórico de Pedidos
                    </h3>
                    <span class="text-xs text-gray-400">Últimos 10</span>
                </div>

                @if($retailer->orders->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($retailer->orders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900 text-right">{{ $order->formatted_total }}</td>
                                        <td class="px-6 py-3 text-center">
                                            @php $oColor = $order->status->color(); @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $oColor }}-100 text-{{ $oColor }}-800">{{ $order->status->shortLabel() }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('admin.b2b.orders.show', $order) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition inline-flex">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-8 text-center">
                        <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm text-gray-500">Nenhum pedido realizado ainda</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar - Status e Ações -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Status do Lojista</h3>

                @php $color = $retailer->status->color(); @endphp
                <div class="flex items-center gap-3 mb-5 p-3 bg-{{ $color }}-50 rounded-lg border border-{{ $color }}-200">
                    <div class="w-8 h-8 rounded-full bg-{{ $color }}-100 flex items-center justify-center">
                        @if($retailer->isApproved())
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($retailer->isPending())
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-{{ $color }}-800">{{ $retailer->status->label() }}</p>
                        <p class="text-xs text-{{ $color }}-600">
                            @if($retailer->isApproved()) Lojista ativo, pode realizar compras @endif
                            @if($retailer->isPending()) Aguardando aprovação do administrador @endif
                            @if($retailer->isBlocked()) Acesso bloqueado ao sistema B2B @endif
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    @if(!$retailer->isApproved())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved" />
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Aprovar Lojista
                            </button>
                        </form>
                    @endif

                    @if(!$retailer->isBlocked())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}" onsubmit="return confirm('Tem certeza que deseja bloquear este lojista?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="blocked" />
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white text-red-600 text-sm font-medium rounded-lg border border-red-200 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Bloquear Lojista
                            </button>
                        </form>
                    @endif

                    @if($retailer->isBlocked())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved" />
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Desbloquear e Aprovar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.b2b.retailers.edit', $retailer) }}"
                       class="w-full flex items-center gap-2 py-2.5 px-4 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar Dados
                    </a>
                    <a href="https://wa.me/{{ $retailer->formatted_whatsapp }}" target="_blank"
                       class="w-full flex items-center gap-2 py-2.5 px-4 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        Enviar WhatsApp
                    </a>
                    <a href="mailto:{{ $retailer->email }}"
                       class="w-full flex items-center gap-2 py-2.5 px-4 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Enviar Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-b2b-admin-layout>
