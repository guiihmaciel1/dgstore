<x-perfumes-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Lojistas</h2>
            <a href="{{ route('admin.perfumes.retailers.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Lojista
            </a>
        </div>
    </x-slot>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.perfumes.retailers.index') }}"
          class="mb-6 bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                <input type="text" name="search" id="search"
                       value="{{ request('search') }}"
                       placeholder="Nome, proprietário, WhatsApp ou cidade"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
            </div>
            <div class="w-40">
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Retailers table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Proprietário</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">WhatsApp</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cidade</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Pedidos</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amostras</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($retailers as $retailer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <span class="text-sm font-medium text-gray-900">{{ $retailer->name }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $retailer->owner_name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($retailer->whatsapp)
                                <a href="{{ $retailer->whatsapp_link }}" target="_blank" rel="noopener noreferrer"
                                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                                    {{ $retailer->whatsapp }}
                                </a>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $retailer->city ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">
                            {{ number_format($retailer->orders_count, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">
                            {{ number_format($retailer->active_samples_count, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $badgeClass = $retailer->status->badgeColor() === 'green'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $retailer->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.perfumes.retailers.show', $retailer) }}"
                                   class="text-sm text-pink-600 hover:text-pink-700 font-medium">Ver</a>
                                <a href="{{ route('admin.perfumes.retailers.edit', $retailer) }}"
                                   class="text-sm text-pink-600 hover:text-pink-700 font-medium">Editar</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-500 text-sm">
                            Nenhum lojista encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($retailers->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $retailers->links() }}
        </div>
        @endif
    </div>
</x-perfumes-admin-layout>
