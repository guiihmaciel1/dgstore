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
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('fragrance-tags.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 border border-border-strong text-dg-400 rounded-lg hover:bg-surface-overlay transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Tags
                        </a>
                        <a href="{{ route('fragrances.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white font-semibold rounded-lg hover:bg-pink-700 transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Importar Perfume
                        </a>
                    @endif
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
                        <div class="flex items-end gap-3">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none pb-2">
                                <input type="checkbox" name="in_stock" value="1"
                                       {{ request('in_stock') ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-border-strong text-emerald-500 focus:ring-emerald-500/30 bg-surface-raised">
                                <span class="text-sm text-dg-300">Em estoque</span>
                            </label>
                            <button type="submit" class="px-4 py-2 bg-surface-overlay text-sm font-medium text-dg-200 rounded-lg hover:bg-surface-elevated transition">Filtrar</button>
                            @if(request()->hasAny(['search', 'gender', 'active', 'in_stock']))
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
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('fragrances.create') }}" class="mt-2 inline-block text-pink-400 hover:text-pink-300 text-sm">Importar primeiro perfume</a>
                        @endif
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
                                    <th class="px-4 py-3 font-medium text-dg-500 text-right">De <span class="text-dg-600">(fake)</span></th>
                                    <th class="px-4 py-3 font-medium text-dg-500 text-right">10x</th>
                                    <th class="px-4 py-3 font-medium text-dg-500 text-right">PIX</th>
                                    @if(auth()->user()->isAdmin())
                                        <th class="px-4 py-3 font-medium text-dg-500 text-right">Custo</th>
                                        <th class="px-4 py-3 font-medium text-dg-500 text-right">Lucro Bruto</th>
                                        <th class="px-4 py-3 font-medium text-dg-500 text-right">Comissão</th>
                                        <th class="px-4 py-3 font-medium text-dg-500 text-right">Lucro Líq.</th>
                                    @elseif(auth()->user()->isSeller() || auth()->user()->isIntern())
                                        <th class="px-4 py-3 font-medium text-dg-500 text-right">Minha Comissão</th>
                                    @endif
                                    <th class="px-4 py-3 font-medium text-dg-500 text-center">Estq</th>
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
                                            @if($f->inspired_by)
                                                <div class="text-xs text-purple-400 mt-0.5" title="Inspirado em">💡 {{ $f->inspired_by }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-dg-300">{{ $f->brand ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $f->gender->badgeColor() }}-500/20 text-{{ $f->gender->badgeColor() }}-400">
                                                {{ $f->gender->label() }}
                                            </span>
                                        </td>
                                        {{-- De (fake) --}}
                                        <td class="px-4 py-3 text-right">
                                            @if($f->original_price)
                                                <span class="text-dg-500 line-through text-xs">R$ {{ number_format($f->original_price, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-dg-700">—</span>
                                            @endif
                                        </td>
                                        {{-- 10x --}}
                                        <td class="px-4 py-3 text-right">
                                            @if($f->sale_price)
                                                <span class="text-dg-200 font-medium">R$ {{ number_format($f->sale_price, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-dg-700">—</span>
                                            @endif
                                        </td>
                                        {{-- PIX --}}
                                        <td class="px-4 py-3 text-right">
                                            @if($f->pix_price)
                                                <span class="text-emerald-400 font-medium">R$ {{ number_format($f->pix_price, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-dg-700">—</span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                            {{-- Custo --}}
                                            <td class="px-4 py-3 text-right">
                                                @if($f->cost_price)
                                                    <span class="text-dg-400 text-xs">R$ {{ number_format($f->total_cost, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-dg-700">—</span>
                                                @endif
                                            </td>
                                            {{-- Lucro Bruto / Comissão / Lucro Líq. --}}
                                            @if($f->pix_price && $f->cost_price)
                                                @php
                                                    $lucroBruto = (float)$f->pix_price - $f->total_cost;
                                                    $comissao = $lucroBruto > 0 ? round($lucroBruto * 0.10, 2) : 0;
                                                    $lucroLiq = $lucroBruto - $comissao;
                                                @endphp
                                                <td class="px-4 py-3 text-right relative"
                                                    x-data="parcelaCalc({{ (float)$f->pix_price }}, {{ (float)$f->sale_price }}, {{ $f->total_cost }})"
                                                    @click.away="open = false">
                                                    <button @click="open = !open" class="text-left cursor-pointer hover:opacity-80 transition">
                                                        <span class="{{ $lucroBruto >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-medium text-xs">
                                                            R$ {{ number_format($lucroBruto, 0, ',', '.') }}
                                                        </span>
                                                        @if($f->profit_margin !== null)
                                                            <div class="text-dg-600 text-[10px]">{{ number_format($f->profit_margin, 0) }}%</div>
                                                        @endif
                                                    </button>
                                                    {{-- Popover calculadora --}}
                                                    <div x-show="open" x-cloak x-transition.opacity.duration.150ms
                                                         class="absolute right-0 top-full mt-1 z-50 rounded-xl shadow-2xl border text-xs"
                                                         style="background: #1a1a1a; border-color: rgba(255,255,255,0.08); min-width: 420px;"
                                                         @click.stop>
                                                        <div class="px-4 py-2.5 border-b" style="border-color: rgba(255,255,255,0.06);">
                                                            <span class="text-dg-400 font-semibold text-[11px] uppercase tracking-wider">Simulador por parcela</span>
                                                        </div>
                                                        <table class="w-full text-[11px]">
                                                            <thead>
                                                                <tr class="text-dg-500" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                                                    <th class="px-3 py-2 text-left font-medium">Parcelas</th>
                                                                    <th class="px-3 py-2 text-right font-medium">Recebe</th>
                                                                    <th class="px-3 py-2 text-right font-medium">L. Bruto</th>
                                                                    <th class="px-3 py-2 text-right font-medium">Comissão</th>
                                                                    <th class="px-3 py-2 text-right font-medium">L. Líq.</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <template x-for="row in rows" :key="row.label">
                                                                    <tr class="hover:bg-white/[0.03] transition"
                                                                        :class="row.isPix ? 'bg-emerald-500/[0.05]' : ''"
                                                                        style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                                                        <td class="px-3 py-1.5 font-medium" :class="row.isPix ? 'text-emerald-400' : 'text-dg-300'" x-text="row.label"></td>
                                                                        <td class="px-3 py-1.5 text-right text-dg-300" x-text="row.recebe"></td>
                                                                        <td class="px-3 py-1.5 text-right font-medium" :class="row.lucroNum >= 0 ? 'text-emerald-400' : 'text-red-400'" x-text="row.bruto"></td>
                                                                        <td class="px-3 py-1.5 text-right text-amber-400" x-text="row.comissao"></td>
                                                                        <td class="px-3 py-1.5 text-right font-medium" :class="row.liqNum >= 0 ? 'text-emerald-400' : 'text-red-400'" x-text="row.liq"></td>
                                                                    </tr>
                                                                </template>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <span class="text-amber-400 font-medium text-xs">
                                                        R$ {{ number_format($comissao, 0, ',', '.') }}
                                                    </span>
                                                    <div class="text-dg-600 text-[10px]">10%</div>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <span class="{{ $lucroLiq >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-medium text-xs">
                                                        R$ {{ number_format($lucroLiq, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                            @else
                                                <td class="px-4 py-3 text-right"><span class="text-dg-700">—</span></td>
                                                <td class="px-4 py-3 text-right"><span class="text-dg-700">—</span></td>
                                                <td class="px-4 py-3 text-right"><span class="text-dg-700">—</span></td>
                                            @endif
                                        @elseif(auth()->user()->isSeller() || auth()->user()->isIntern())
                                            {{-- Vendedora: apenas comissão com calculadora --}}
                                            @if($f->pix_price && $f->cost_price)
                                                @php
                                                    $sellerLucroPix = (float)$f->pix_price - $f->total_cost;
                                                    $sellerComissao = $sellerLucroPix > 0 ? round($sellerLucroPix * 0.10, 2) : 0;
                                                @endphp
                                                <td class="px-4 py-3 text-right relative"
                                                    x-data="comissaoCalc({{ (float)$f->pix_price }}, {{ (float)$f->sale_price }}, {{ $f->total_cost }})"
                                                    @click.away="open = false">
                                                    <button @click="open = !open" class="text-left cursor-pointer hover:opacity-80 transition">
                                                        <span class="text-amber-400 font-medium text-xs">
                                                            R$ {{ number_format($sellerComissao, 0, ',', '.') }}
                                                        </span>
                                                        <div class="text-dg-600 text-[10px]">10%</div>
                                                    </button>
                                                    {{-- Popover calculadora de comissão --}}
                                                    <div x-show="open" x-cloak x-transition.opacity.duration.150ms
                                                         class="absolute right-0 top-full mt-1 z-50 rounded-xl shadow-2xl border text-xs"
                                                         style="background: #1a1a1a; border-color: rgba(255,255,255,0.08); min-width: 280px;"
                                                         @click.stop>
                                                        <div class="px-4 py-2.5 border-b" style="border-color: rgba(255,255,255,0.06);">
                                                            <span class="text-dg-400 font-semibold text-[11px] uppercase tracking-wider">Minha comissão por parcela</span>
                                                        </div>
                                                        <table class="w-full text-[11px]">
                                                            <thead>
                                                                <tr class="text-dg-500" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                                                    <th class="px-3 py-2 text-left font-medium">Parcelas</th>
                                                                    <th class="px-3 py-2 text-right font-medium">Você recebe</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <template x-for="row in rows" :key="row.label">
                                                                    <tr class="hover:bg-white/[0.03] transition"
                                                                        :class="row.isPix ? 'bg-emerald-500/[0.05]' : ''"
                                                                        style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                                                        <td class="px-3 py-1.5 font-medium" :class="row.isPix ? 'text-emerald-400' : 'text-dg-300'" x-text="row.label"></td>
                                                                        <td class="px-3 py-1.5 text-right font-medium text-amber-400" x-text="row.comissao"></td>
                                                                    </tr>
                                                                </template>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            @else
                                                <td class="px-4 py-3 text-right"><span class="text-dg-700">—</span></td>
                                            @endif
                                        @endif
                                        {{-- Estoque --}}
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
                                                @if(auth()->user()->isAdmin())
                                                    <a href="{{ route('fragrances.edit', $f) }}" class="p-1.5 text-dg-500 hover:text-dg-200 transition" title="Editar">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                @if(auth()->user()->isAdmin())
                                                    <a href="{{ $f->fragrantica_url }}" target="_blank" class="p-1.5 text-dg-500 hover:text-dg-200 transition" title="Ver no Fragrantica">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                @else
                                                    <a href="{{ route('catalogo.show', $f->slug) }}" target="_blank" class="p-1.5 text-dg-500 hover:text-dg-200 transition" title="Ver no Catálogo">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                @if(auth()->user()->isAdmin())
                                                    <form method="POST" action="{{ route('fragrances.destroy', $f) }}" class="inline" onsubmit="return confirm('Remover {{ addslashes($f->name) }}?')">
                                                        @csrf @method('DELETE')
                                                        <button class="p-1.5 text-dg-500 hover:text-red-400 transition" title="Remover">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
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

    @push('scripts')
    <script>
        const MDR_RATES = [0, 2.96, 4.03, 4.72, 5.41, 6.09, 6.78, 7.62, 8.31, 8.99, 9.68];
        const fmtBRL = (v) => 'R$ ' + Math.round(v).toLocaleString('pt-BR');

        function parcelaCalc(pixPrice, salePrice, totalCost) {
            const buildRow = (label, recebe, isPix) => {
                const lucro = recebe - totalCost;
                const com = lucro > 0 ? lucro * 0.10 : 0;
                const liq = lucro - com;
                return {
                    label, isPix,
                    recebe: fmtBRL(recebe),
                    bruto: fmtBRL(lucro),
                    comissao: fmtBRL(com),
                    liq: fmtBRL(liq),
                    lucroNum: lucro,
                    liqNum: liq,
                };
            };

            const rows = [buildRow('PIX', pixPrice, true)];
            for (let i = 1; i <= 10; i++) {
                rows.push(buildRow(i + 'x', salePrice * (1 - MDR_RATES[i] / 100), false));
            }

            return { open: false, rows };
        }

        function comissaoCalc(pixPrice, salePrice, totalCost) {
            const calcComissao = (recebe) => {
                const lucro = recebe - totalCost;
                return lucro > 0 ? lucro * 0.10 : 0;
            };

            const rows = [{ label: 'PIX', isPix: true, comissao: fmtBRL(calcComissao(pixPrice)) }];
            for (let i = 1; i <= 10; i++) {
                const recebe = salePrice * (1 - MDR_RATES[i] / 100);
                rows.push({ label: i + 'x', isPix: false, comissao: fmtBRL(calcComissao(recebe)) });
            }

            return { open: false, rows };
        }
    </script>
    @endpush
</x-app-layout>
