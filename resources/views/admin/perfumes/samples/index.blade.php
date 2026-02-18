<x-perfumes-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Amostras</h2>
            <a href="{{ route('admin.perfumes.samples.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Entregar Amostra
            </a>
        </div>
    </x-slot>

    @php
        $badgeMap = ['blue' => 'bg-blue-100 text-blue-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'green' => 'bg-green-100 text-green-700'];
    @endphp

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.perfumes.samples.index') }}"
          class="mb-6 bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[200px]">
                <label for="retailer" class="block text-xs font-medium text-gray-500 mb-1">Lojista</label>
                <select name="retailer" id="retailer"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    <option value="">Todos</option>
                    @foreach($retailers as $r)
                        <option value="{{ $r->id }}" {{ request('retailer') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    <option value="">Todos</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregue</option>
                    <option value="with_retailer" {{ request('status') === 'with_retailer' ? 'selected' : '' }}>Com Lojista</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Devolvido</option>
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Samples table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lojista</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Qtd</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Entrega</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Dias Fora</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($samples as $sample)
                    <tr class="hover:bg-gray-50 transition {{ $sample->days_out !== null && $sample->days_out > 30 ? 'bg-amber-50' : '' }}">
                        <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $sample->product?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $sample->retailer?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700">{{ $sample->quantity }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">
                            {{ $sample->delivered_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($sample->days_out !== null)
                                <span class="text-sm {{ $sample->days_out > 30 ? 'text-amber-600 font-medium' : 'text-gray-700' }}">
                                    {{ $sample->days_out }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @php $badgeClass = $badgeMap[$sample->status->badgeColor()] ?? 'bg-gray-100 text-gray-700'; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $sample->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($sample->status->value !== 'returned')
                                <form method="POST" action="{{ route('admin.perfumes.samples.return', $sample) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                                        Devolver
                                    </button>
                                </form>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-500 text-sm">
                            Nenhuma amostra encontrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($samples->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $samples->links() }}
        </div>
        @endif
    </div>
</x-perfumes-admin-layout>
