<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <a href="{{ route('admin.b2b.retailers.index') }}" class="-ml-2 mt-0.5 shrink-0 rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-gray-100 hover:text-gray-600" aria-label="Voltar">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gray-900 text-sm font-bold uppercase text-white">
                        {{ substr($retailer->store_name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-semibold tracking-tight text-gray-900 sm:text-2xl">{{ $retailer->store_name }}</h2>
                        <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">{{ $retailer->owner_name }} &middot; Cadastrado em {{ $retailer->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex w-full flex-col gap-2 sm:flex-row sm:gap-3 lg:w-auto lg:shrink-0">
                <a href="https://wa.me/{{ $retailer->formatted_whatsapp }}" target="_blank" rel="noopener noreferrer"
                   class="apple-btn-secondary justify-center rounded-xl !text-gray-700 shadow-sm transition-all duration-200">
                    <svg class="h-4 w-4 shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                    WhatsApp
                </a>
                <a href="{{ route('admin.b2b.retailers.edit', $retailer) }}"
                   class="apple-btn-primary justify-center rounded-xl shadow-sm transition-all duration-200">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Mobile-first: status/ações primeiro no telefone --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 lg:gap-6">
        <aside class="order-1 space-y-5 lg:order-2 lg:col-span-1 lg:space-y-6">
            <div class="apple-card p-5 transition-all duration-200 sm:p-6">
                <p class="apple-section-title mb-1">Conta</p>
                <h3 class="mb-4 text-base font-semibold tracking-tight text-gray-900">Status do lojista</h3>

                @php $color = $retailer->status->color(); @endphp
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-{{ $color }}-200 bg-{{ $color }}-50 p-4 transition-all duration-200">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/80 shadow-sm">
                        @if($retailer->isApproved())
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        @elseif($retailer->isPending())
                            <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <span class="apple-badge mb-2 bg-{{ $color }}-100 text-{{ $color }}-700">{{ $retailer->status->label() }}</span>
                        <p class="mt-2 text-xs leading-relaxed text-gray-600">
                            @if($retailer->isApproved()) Lojista ativo, pode realizar compras. @endif
                            @if($retailer->isPending()) Aguardando aprovação do administrador. @endif
                            @if($retailer->isBlocked()) Acesso bloqueado ao sistema B2B. @endif
                        </p>
                    </div>
                </div>

                <div class="space-y-2.5">
                    @if(!$retailer->isApproved())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved"/>
                            <button type="submit" class="apple-btn-primary w-full justify-center rounded-xl shadow-sm transition-all duration-200">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aprovar lojista
                            </button>
                        </form>
                    @endif

                    @if(!$retailer->isBlocked())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}" onsubmit="return confirm('Tem certeza que deseja bloquear este lojista?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="blocked"/>
                            <button type="submit" class="apple-btn-danger w-full justify-center rounded-xl shadow-sm transition-all duration-200">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Bloquear lojista
                            </button>
                        </form>
                    @endif

                    @if($retailer->isBlocked())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved"/>
                            <button type="submit" class="apple-btn-primary w-full justify-center rounded-xl shadow-sm transition-all duration-200">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Desbloquear e aprovar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="apple-card p-5 transition-all duration-200 sm:p-6">
                <p class="apple-section-title mb-1">Atalhos</p>
                <h3 class="mb-4 text-base font-semibold tracking-tight text-gray-900">Ações rápidas</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.b2b.retailers.edit', $retailer) }}"
                       class="apple-btn-secondary w-full rounded-xl !justify-start shadow-sm transition-all duration-200">
                        <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar dados
                    </a>
                    <a href="https://wa.me/{{ $retailer->formatted_whatsapp }}" target="_blank" rel="noopener noreferrer"
                       class="apple-btn-secondary w-full rounded-xl !justify-start shadow-sm transition-all duration-200">
                        <svg class="h-4 w-4 shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        Enviar WhatsApp
                    </a>
                    <a href="mailto:{{ $retailer->email }}"
                       class="apple-btn-secondary w-full rounded-xl !justify-start shadow-sm transition-all duration-200">
                        <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Enviar email
                    </a>
                </div>
            </div>
        </aside>

        <div class="order-2 space-y-5 lg:order-1 lg:col-span-2 lg:space-y-6">
            <div class="apple-card p-5 transition-all duration-200 sm:p-6">
                <p class="apple-section-title mb-1">Identificação</p>
                <h3 class="mb-4 flex items-center gap-2 text-base font-semibold tracking-tight text-gray-900 sm:mb-5">
                    <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Dados da loja
                </h3>
                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2 sm:gap-4">
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-200 sm:p-4">
                        <dt class="mb-1 text-xs font-medium text-gray-500">Nome da Loja</dt>
                        <dd class="font-semibold tracking-tight text-gray-900">{{ $retailer->store_name }}</dd>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-200 sm:p-4">
                        <dt class="mb-1 text-xs font-medium text-gray-500">Responsável</dt>
                        <dd class="text-gray-900">{{ $retailer->owner_name }}</dd>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-200 sm:p-4">
                        <dt class="mb-1 text-xs font-medium text-gray-500">CNPJ/CPF</dt>
                        <dd class="text-gray-900">{{ $retailer->document }}</dd>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-200 sm:p-4">
                        <dt class="mb-1 text-xs font-medium text-gray-500">Email</dt>
                        <dd class="break-all text-gray-900">{{ $retailer->email }}</dd>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-200 sm:p-4">
                        <dt class="mb-1 text-xs font-medium text-gray-500">WhatsApp</dt>
                        <dd class="text-gray-900">
                            <a href="https://wa.me/{{ $retailer->formatted_whatsapp }}" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-500 transition-all duration-200 hover:text-blue-600">
                                {{ $retailer->whatsapp }}
                            </a>
                        </dd>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-200 sm:p-4">
                        <dt class="mb-1 text-xs font-medium text-gray-500">Cidade</dt>
                        <dd class="text-gray-900">{{ $retailer->city }}/{{ $retailer->state }}</dd>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-200 sm:p-4">
                        <dt class="mb-1 text-xs font-medium text-gray-500">Data de Cadastro</dt>
                        <dd class="text-gray-900">{{ $retailer->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-all duration-200 sm:p-4">
                        <dt class="mb-1 text-xs font-medium text-gray-500">Última Atualização</dt>
                        <dd class="text-gray-900">{{ $retailer->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="apple-card p-5 transition-all duration-200 sm:p-6">
                <p class="apple-section-title mb-1">Métricas</p>
                <h3 class="mb-4 flex items-center gap-2 text-base font-semibold tracking-tight text-gray-900 sm:mb-5">
                    <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Resumo financeiro
                </h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
                    <div class="rounded-xl border border-blue-100/80 bg-blue-50/60 p-3 text-center transition-all duration-200 sm:p-4">
                        <p class="mb-1 text-xs font-medium text-blue-600">Pedidos</p>
                        <p class="text-xl font-bold tracking-tight text-gray-900">{{ $financialStats['total_orders'] }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 text-center transition-all duration-200 sm:p-4">
                        <p class="mb-1 text-xs font-medium text-gray-500">Total comprado</p>
                        <p class="text-lg font-bold tracking-tight text-gray-900 sm:text-xl">R$ {{ number_format($financialStats['total_revenue'], 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 text-center transition-all duration-200 sm:p-4">
                        <p class="mb-1 text-xs font-medium text-gray-500">Ticket médio</p>
                        <p class="text-lg font-bold tracking-tight text-gray-900 sm:text-xl">R$ {{ number_format($financialStats['avg_ticket'] ?? 0, 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3 text-center transition-all duration-200 sm:col-span-2 sm:p-4 lg:col-span-1">
                        <p class="mb-1 text-xs font-medium text-gray-500">Última compra</p>
                        <p class="text-sm font-semibold tracking-tight text-gray-900">
                            @if($financialStats['last_order_at'])
                                {{ \Carbon\Carbon::parse($financialStats['last_order_at'])->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="apple-card overflow-hidden transition-all duration-200">
                <div class="flex flex-col gap-1 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-5">
                    <div>
                        <p class="apple-section-title">Histórico</p>
                        <h3 class="mt-1 flex items-center gap-2 text-base font-semibold tracking-tight text-gray-900">
                            <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Pedidos recentes
                        </h3>
                    </div>
                    <span class="text-xs font-medium text-gray-400">Últimos 10</span>
                </div>

                @if($retailer->orders->isNotEmpty())
                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Pedido</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Data</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="w-14 px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($retailer->orders as $order)
                                    <tr class="transition-all duration-200 hover:bg-gray-50/80">
                                        <td class="px-5 py-3.5 text-sm font-semibold tracking-tight text-gray-900">{{ $order->order_number }}</td>
                                        <td class="px-5 py-3.5 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td class="px-5 py-3.5 text-right text-sm font-semibold tracking-tight text-gray-900">{{ $order->formatted_total }}</td>
                                        <td class="px-5 py-3.5 text-center">
                                            @php $oColor = $order->status->color(); @endphp
                                            <span class="apple-badge bg-{{ $oColor }}-100 text-{{ $oColor }}-700">{{ $order->status->shortLabel() }}</span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <a href="{{ route('admin.b2b.orders.show', $order) }}" class="inline-flex rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-blue-50 hover:text-blue-500" title="Ver pedido">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-gray-100 md:hidden">
                        @foreach($retailer->orders as $order)
                            @php $oColor = $order->status->color(); @endphp
                            <div class="flex items-start justify-between gap-3 px-4 py-4 transition-all duration-200 hover:bg-gray-50/50">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold tracking-tight text-gray-900">{{ $order->order_number }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $order->created_at->format('d/m/Y') }}</p>
                                    <p class="mt-2 text-sm font-semibold tracking-tight text-gray-900">{{ $order->formatted_total }}</p>
                                    <span class="apple-badge mt-2 bg-{{ $oColor }}-100 text-{{ $oColor }}-700">{{ $order->status->shortLabel() }}</span>
                                </div>
                                <a href="{{ route('admin.b2b.orders.show', $order) }}" class="apple-btn-secondary shrink-0 rounded-xl !px-3 !py-2 !text-xs shadow-sm transition-all duration-200">
                                    Ver
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-10 text-center sm:py-12">
                        <svg class="mx-auto mb-3 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-gray-500">Nenhum pedido realizado ainda</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-b2b-admin-layout>
