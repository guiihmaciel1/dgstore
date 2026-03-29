<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Produtos B2B</h2>
            <a href="{{ route('admin.b2b.products.create') }}" class="apple-btn-primary py-2.5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Novo Produto
            </a>
        </div>
    </x-slot>

    {{-- Filtros --}}
    <div class="apple-card p-4 sm:p-5 mb-5">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
            <div class="flex-1 min-w-0 sm:min-w-[200px]">
                <label class="apple-label">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome ou modelo..."
                       class="apple-input" />
            </div>
            <div class="w-full sm:w-44">
                <label class="apple-label">Condicao</label>
                <select name="condition" class="apple-select">
                    <option value="">Todas</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->value }}" {{ request('condition') == $condition->value ? 'selected' : '' }}>{{ $condition->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="apple-btn-dark w-full sm:w-auto py-2.5 text-sm">Filtrar</button>
            </div>
        </form>
    </div>

    {{-- Mobile: cards --}}
    <div class="md:hidden space-y-3">
        @forelse($products as $product)
            <div class="apple-card p-4">
                <div class="flex items-start gap-3">
                    @if($product->photo)
                        <img src="{{ $product->photo_url }}" alt="" class="w-14 h-14 rounded-xl object-cover shrink-0" />
                    @else
                        <div class="w-14 h-14 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $product->storage }} {{ $product->color ? '- ' . $product->color : '' }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="apple-badge text-[10px] {{ $product->condition->value === 'sealed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $product->condition->label() }}
                                </span>
                                @if($product->active)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 mt-3 text-xs">
                            <div>
                                <span class="text-gray-400">Custo</span>
                                <p class="font-medium text-gray-600">{{ $product->formatted_cost_price }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Atacado</span>
                                <p class="font-semibold text-gray-900">{{ $product->formatted_wholesale_price }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Lucro</span>
                                <p class="font-medium {{ $product->profit > 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $product->formatted_profit }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Estoque</span>
                                <p class="font-medium {{ $product->stock_quantity <= 0 ? 'text-red-500' : ($product->stock_quantity <= $lowStockThreshold ? 'text-amber-600' : 'text-gray-900') }}">
                                    {{ $product->stock_quantity }} un.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('admin.b2b.products.edit', $product) }}" class="apple-btn-secondary py-1.5 px-3 text-xs flex-1 justify-center">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <form method="POST" action="{{ route('admin.b2b.products.destroy', $product) }}" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="apple-btn py-1.5 px-3 text-xs text-red-500 bg-red-50 hover:bg-red-100 w-full inline-flex items-center justify-center gap-1.5 rounded-xl font-medium transition-all duration-200">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="apple-card p-10 text-center">
                <p class="text-sm text-gray-400">Nenhum produto B2B cadastrado.</p>
            </div>
        @endforelse
    </div>

    {{-- Desktop: table --}}
    <div class="hidden md:block apple-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Produto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Condicao</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Custo</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Atacado</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Lucro</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Margem</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Estoque</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50/60 transition-colors duration-200">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if($product->photo)
                                        <img src="{{ $product->photo_url }}" alt="" class="w-10 h-10 rounded-xl object-cover" />
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $product->storage }} {{ $product->color ? '- ' . $product->color : '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="apple-badge {{ $product->condition->value === 'sealed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $product->condition->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right text-sm text-gray-500">{{ $product->formatted_cost_price }}</td>
                            <td class="px-5 py-3.5 text-right text-sm font-medium text-gray-900">{{ $product->formatted_wholesale_price }}</td>
                            <td class="px-5 py-3.5 text-right text-sm font-medium {{ $product->profit > 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $product->formatted_profit }}</td>
                            <td class="px-5 py-3.5 text-right text-sm {{ $product->profit_margin > 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($product->profit_margin, 1) }}%</td>
                            <td class="px-5 py-3.5 text-center">
                                @if($product->stock_quantity <= 0)
                                    <span class="apple-badge bg-red-50 text-red-600">Esgotado</span>
                                @elseif($product->stock_quantity <= $lowStockThreshold)
                                    <span class="apple-badge bg-amber-50 text-amber-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        {{ $product->stock_quantity }} un.
                                    </span>
                                @else
                                    <span class="text-sm text-gray-700">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($product->active)
                                    <span class="apple-badge bg-emerald-50 text-emerald-700">Ativo</span>
                                @else
                                    <span class="apple-badge bg-gray-100 text-gray-500">Inativo</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.b2b.products.edit', $product) }}"
                                       class="rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-gray-100 hover:text-gray-900" title="Editar">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.b2b.products.destroy', $product) }}" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-red-50 hover:text-red-500" title="Excluir">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-sm text-gray-400">Nenhum produto B2B cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $products->links() }}</div>
</x-b2b-admin-layout>
