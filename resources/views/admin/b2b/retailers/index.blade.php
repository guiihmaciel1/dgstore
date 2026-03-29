<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Lojistas B2B</h2>
                <p class="mt-1 text-sm text-gray-500">Gerencie cadastros e aprovações</p>
            </div>
            <a href="{{ route('admin.b2b.retailers.create') }}" class="apple-btn-primary w-full justify-center rounded-xl shadow-sm transition-all duration-200 sm:w-auto">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Lojista
            </a>
        </div>
    </x-slot>

    {{-- Stats: mobile-first --}}
    <div class="mb-5 grid grid-cols-1 gap-3 sm:mb-6 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
        <div class="apple-card p-4 transition-all duration-200 hover:shadow-md sm:p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                    <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="apple-section-title">Total</p>
                    <p class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="apple-card p-4 transition-all duration-200 hover:shadow-md sm:p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                    <svg class="h-5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="apple-section-title">Pendentes</p>
                    <p class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="apple-card p-4 transition-all duration-200 hover:shadow-md sm:p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="apple-section-title">Aprovados</p>
                    <p class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </div>
        <div class="apple-card p-4 transition-all duration-200 hover:shadow-md sm:col-span-2 sm:p-5 lg:col-span-1">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="apple-section-title">Bloqueados</p>
                    <p class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">{{ $stats['blocked'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="apple-card mb-5 p-4 transition-all duration-200 sm:mb-6 sm:p-5">
        <form method="GET" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div class="min-w-0 flex-1 sm:min-w-[200px]">
                <label for="search" class="apple-label">Buscar</label>
                <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nome, CNPJ ou cidade..."
                       class="apple-input rounded-xl shadow-sm transition-all duration-200"/>
            </div>
            <div class="w-full sm:w-44">
                <label for="status_filter" class="apple-label">Status</label>
                <select id="status_filter" name="status" class="apple-select rounded-xl shadow-sm transition-all duration-200">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button type="submit" class="apple-btn-dark flex-1 justify-center rounded-xl shadow-sm transition-all duration-200 sm:flex-none">Filtrar</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.b2b.retailers.index') }}" class="apple-btn-secondary flex-1 justify-center rounded-xl shadow-sm transition-all duration-200 sm:flex-none">Limpar</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Mobile: um apple-card por lojista --}}
    <div class="space-y-3 md:hidden">
        @forelse($retailers as $retailer)
            <div class="apple-card p-4 transition-all duration-200 hover:shadow-md">
                <div class="mb-3 flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-900 text-sm font-bold uppercase text-white">
                            {{ substr($retailer->store_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold tracking-tight text-gray-900">{{ $retailer->store_name }}</p>
                            <p class="text-xs text-gray-500">{{ $retailer->owner_name }}</p>
                        </div>
                    </div>
                    @php $color = $retailer->status->color(); @endphp
                    <span class="apple-badge shrink-0 bg-{{ $color }}-100 text-{{ $color }}-700">
                        {{ $retailer->status->label() }}
                    </span>
                </div>

                <div class="mb-4 grid grid-cols-1 gap-2 text-xs text-gray-600">
                    <div><span class="text-gray-400">Documento:</span> {{ $retailer->document }}</div>
                    <div><span class="text-gray-400">Cidade:</span> {{ $retailer->city }}/{{ $retailer->state }}</div>
                </div>

                <div class="flex flex-wrap items-stretch gap-2 border-t border-gray-100 pt-3">
                    <a href="{{ route('admin.b2b.retailers.show', $retailer) }}"
                       class="apple-btn-secondary min-w-[100px] flex-1 justify-center rounded-xl !px-3 !py-2 !text-xs shadow-sm transition-all duration-200">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Visualizar
                    </a>
                    <a href="{{ route('admin.b2b.retailers.edit', $retailer) }}"
                       class="apple-btn-secondary min-w-[100px] flex-1 justify-center rounded-xl !px-3 !py-2 !text-xs shadow-sm transition-all duration-200">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                    @if($retailer->isPending())
                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}" class="min-w-[100px] flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved"/>
                            <button type="submit" class="apple-btn-primary w-full justify-center rounded-xl !px-3 !py-2 !text-xs shadow-sm transition-all duration-200">
                                Aprovar
                            </button>
                        </form>
                    @endif
                    <a href="https://wa.me/{{ $retailer->formatted_whatsapp }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center rounded-xl bg-gray-100 p-2.5 text-blue-500 transition-all duration-200 hover:bg-gray-200" aria-label="WhatsApp">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="apple-card p-10 text-center transition-all duration-200">
                <svg class="mx-auto mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="mb-2 text-sm text-gray-500">Nenhum lojista encontrado</p>
                <a href="{{ route('admin.b2b.retailers.create') }}" class="text-sm font-semibold text-blue-500 transition-all duration-200 hover:text-blue-600">Cadastrar primeiro lojista</a>
            </div>
        @endforelse
    </div>

    {{-- Desktop: tabela dentro de apple-card --}}
    <div class="apple-card hidden overflow-hidden transition-all duration-200 md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Lojista</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Documento</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Contato</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Cadastro</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($retailers as $retailer)
                        <tr class="transition-all duration-200 hover:bg-gray-50/80">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-900 text-xs font-bold uppercase text-white">
                                        {{ substr($retailer->store_name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold tracking-tight text-gray-900">{{ $retailer->store_name }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $retailer->owner_name }} &middot; {{ $retailer->city }}/{{ $retailer->state }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">{{ $retailer->document }}</td>
                            <td class="px-5 py-4">
                                <div class="text-sm text-gray-700">{{ $retailer->email }}</div>
                                <a href="https://wa.me/{{ $retailer->formatted_whatsapp }}" target="_blank" rel="noopener noreferrer"
                                   class="mt-1 inline-flex items-center gap-1 text-xs text-blue-500 transition-all duration-200 hover:text-blue-600">
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                    {{ $retailer->whatsapp }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                @php $color = $retailer->status->color(); @endphp
                                <span class="apple-badge bg-{{ $color }}-100 text-{{ $color }}-700">
                                    {{ $retailer->status->label() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">{{ $retailer->created_at->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.b2b.retailers.show', $retailer) }}"
                                       class="rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-blue-50 hover:text-blue-500" title="Visualizar">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.b2b.retailers.edit', $retailer) }}"
                                       class="rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-gray-100 hover:text-gray-900" title="Editar">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($retailer->isPending())
                                        <form method="POST" action="{{ route('admin.b2b.retailers.status', $retailer) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved"/>
                                            <button type="submit" class="rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-blue-50 hover:text-blue-500" title="Aprovar">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <svg class="mx-auto mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500">Nenhum lojista encontrado</p>
                                <a href="{{ route('admin.b2b.retailers.create') }}" class="text-sm font-semibold text-blue-500 transition-all duration-200 hover:text-blue-600">Cadastrar primeiro lojista</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 sm:mt-6">{{ $retailers->links() }}</div>
</x-b2b-admin-layout>
