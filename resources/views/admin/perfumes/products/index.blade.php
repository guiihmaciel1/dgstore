<x-perfumes-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Produtos</h2>
            <div class="flex items-center gap-3">
                <div x-data="dollarRate()" class="flex items-center gap-1.5 bg-gray-100 rounded-lg px-2 py-1">
                    <span class="text-xs text-gray-500">Dólar:</span>
                    <span class="text-xs text-gray-500">R$</span>
                    <input type="number" step="0.01" min="0.01"
                           x-model="rate"
                           @change="save()"
                           @keydown.enter="$event.target.blur()"
                           class="w-16 text-xs font-bold text-gray-700 bg-transparent border-0 border-b border-dashed border-gray-400 focus:border-pink-500 focus:ring-0 p-0 text-center">
                    <template x-if="saving">
                        <svg class="w-3 h-3 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </template>
                    <template x-if="saved">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                </div>
                <a href="{{ route('admin.perfumes.products.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Produto
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.perfumes.products.index') }}"
          class="mb-6 bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                <input type="text" name="search" id="search"
                       value="{{ request('search') }}"
                       placeholder="Nome, marca ou código de barras"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
            </div>
            <div class="w-40">
                <label for="category" class="block text-xs font-medium text-gray-500 mb-1">Categoria</label>
                <select name="category" id="category"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    <option value="">Todas</option>
                    <option value="masculino" {{ request('category') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="feminino" {{ request('category') === 'feminino' ? 'selected' : '' }}>Feminino</option>
                    <option value="unissex" {{ request('category') === 'unissex' ? 'selected' : '' }}>Unissex</option>
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Products table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-pink-50/40">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tamanho</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Custo US$</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Custo R$</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Venda 70%</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Sug. Lojista 60%</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($products as $product)
                    @php
                        $costUsd = (float) $product->cost_price;
                        $costBrl = $costUsd * $dollarRate;
                        $salePrice = $costBrl * 1.70;
                        $retailerPrice = $salePrice * 1.60;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="https://www.google.com/search?q={{ urlencode($product->name) }}"
                               target="_blank"
                               class="text-sm font-medium text-gray-900 hover:text-pink-600 hover:underline transition">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $product->size_ml ? $product->size_ml . 'ml' : '—' }}</td>
                        <td class="px-5 py-3 text-right text-sm text-gray-500 font-mono">
                            $ {{ number_format($costUsd, 2, '.', ',') }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-700 font-medium">
                            R$ {{ number_format($costBrl, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-emerald-700 font-semibold">
                            R$ {{ number_format($salePrice, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-pink-700 font-semibold">
                            R$ {{ number_format($retailerPrice, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.perfumes.products.edit', $product) }}"
                                   class="text-sm text-pink-600 hover:text-pink-700 font-medium">Editar</a>
                                <form method="POST"
                                      action="{{ route('admin.perfumes.products.destroy', $product) }}"
                                      class="inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-500 text-sm">
                            Nenhum produto encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span>Exibir</span>
                @foreach([20, 50, 100] as $size)
                    <a href="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}"
                       class="px-2 py-1 rounded font-medium transition {{ (int) request('per_page', 20) === $size ? 'bg-pink-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $size }}
                    </a>
                @endforeach
                <span>por página</span>
            </div>
            @if($products->hasPages())
                <div>{{ $products->links() }}</div>
            @endif
        </div>
    </div>
@push('scripts')
<script>
function dollarRate() {
    return {
        rate: {{ $dollarRate }},
        saving: false,
        saved: false,
        async save() {
            if (this.rate < 0.01) return;
            this.saving = true;
            this.saved = false;
            try {
                const response = await fetch('{{ route("admin.perfumes.settings.dollar-rate") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ dollar_rate: this.rate }),
                });
                if (response.ok) {
                    this.saved = true;
                    setTimeout(() => { this.saved = false; }, 2000);
                    window.location.reload();
                }
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endpush
</x-perfumes-admin-layout>
