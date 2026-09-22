<x-app-layout>
    <x-slot name="title">Perfumaria</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #6ee7b7;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid #fca5a5; border-radius: 0.5rem; color: #fca5a5;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Perfumaria</h1>
                    <p class="text-sm text-dg-500">Catálogo de perfumes importados do Fragrantica</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('catalogo.index') }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 border border-border-strong text-dg-300 rounded-lg hover:bg-surface-overlay transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Ver Catálogo
                    </a>
                    <a href="{{ route('fragrances.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white font-semibold rounded-lg hover:bg-pink-700 transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Importar Perfume
                    </a>
                </div>
            </div>

            <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                <div class="p-4 border-b border-border bg-surface">
                    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-dg-500 mb-1">Buscar</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome ou marca..."
                                   class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-dg-500 mb-1">Gênero</label>
                            <select name="gender" class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-gray-900 focus:outline-none">
                                <option value="">Todos</option>
                                @foreach(\App\Domain\Fragrance\Enums\FragranceGender::cases() as $g)
                                    <option value="{{ $g->value }}" {{ request('gender') === $g->value ? 'selected' : '' }}>{{ $g->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-dg-500 mb-1">Status</label>
                            <select name="active" class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-gray-900 focus:outline-none">
                                <option value="">Todos</option>
                                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Ativo</option>
                                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-surface-overlay text-sm font-medium text-dg-200 rounded-lg hover:bg-surface-elevated transition">Filtrar</button>
                            @if(request()->hasAny(['search', 'gender', 'active']))
                                <a href="{{ route('fragrances.index') }}" class="px-4 py-2 text-sm text-dg-500 hover:text-dg-300 transition">Limpar</a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($fragrances->isEmpty())
                    <div class="text-center py-16">
                        <svg class="mx-auto h-16 w-16 text-dg-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <p class="mt-4 text-dg-500">Nenhum perfume cadastrado.</p>
                        <a href="{{ route('fragrances.create') }}" class="mt-2 inline-block text-pink-400 hover:text-pink-300 text-sm">Importar primeiro perfume</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-border text-left">
                                    <th class="px-4 py-3 font-medium text-dg-500 w-12"></th>
                                    <th class="px-4 py-3 font-medium text-dg-500">Perfume</th>
                                    <th class="px-4 py-3 font-medium text-dg-500">Marca</th>
                                    <th class="px-4 py-3 font-medium text-dg-500 text-center">Gênero</th>
                                    <th class="px-4 py-3 font-medium text-dg-500 text-center">Nota</th>
                                    <th class="px-4 py-3 font-medium text-dg-500 text-right">Preço</th>
                                    <th class="px-4 py-3 font-medium text-dg-500 text-center">Estoque</th>
                                    <th class="px-4 py-3 font-medium text-dg-500 text-center">Status</th>
                                    <th class="px-4 py-3 font-medium text-dg-500 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($fragrances as $f)
                                    <tr class="hover:bg-surface-overlay/50 transition">
                                        <td class="px-4 py-3">
                                            @if($f->image_url)
                                                <img src="{{ $f->image_url }}" alt="{{ $f->name }}" class="w-10 h-10 object-cover rounded-lg">
                                            @else
                                                <div class="w-10 h-10 bg-surface-overlay rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-dg-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-dg-100">{{ $f->name }}</div>
                                            @if($f->year || $f->concentration)
                                                <div class="text-xs text-dg-500">
                                                    {{ $f->year ? $f->year : '' }}{{ $f->year && $f->concentration ? ' · ' : '' }}{{ $f->concentration ?? '' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-dg-300">{{ $f->brand ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $f->gender->badgeColor() }}-500/20 text-{{ $f->gender->badgeColor() }}-400">
                                                {{ $f->gender->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($f->rating)
                                                <span class="text-amber-400 font-medium">{{ number_format($f->rating, 1) }}</span>
                                                <span class="text-dg-600 text-xs">/5</span>
                                            @else
                                                <span class="text-dg-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($f->sale_price)
                                                <span class="text-emerald-400 font-medium">R$ {{ number_format($f->sale_price, 2, ',', '.') }}</span>
                                            @else
                                                <span class="text-dg-600 text-xs">Não definido</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="{{ $f->stock_quantity > 0 ? 'text-emerald-400' : 'text-dg-600' }}">{{ $f->stock_quantity }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($f->active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">Ativo</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">Inativo</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('fragrances.edit', $f) }}" class="p-1.5 text-dg-500 hover:text-dg-200 transition" title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ $f->fragrantica_url }}" target="_blank" class="p-1.5 text-dg-500 hover:text-dg-200 transition" title="Ver no Fragrantica">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('fragrances.destroy', $f) }}" class="inline" onsubmit="return confirm('Remover {{ addslashes($f->name) }}?')">
                                                    @csrf @method('DELETE')
                                                    <button class="p-1.5 text-dg-500 hover:text-red-400 transition" title="Remover">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 border-t border-border">
                        {{ $fragrances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
