<x-app-layout>
    <x-slot name="title">Editar — {{ $fragrance->name }}</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8 max-w-5xl">
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

            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('fragrances.index') }}" class="text-sm text-dg-500 hover:text-dg-300 transition">← Voltar para Perfumaria</a>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('fragrances.rescrape', $fragrance) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 border border-border-strong text-dg-400 rounded-lg hover:bg-surface-overlay transition text-xs"
                                onclick="return confirm('Reimportar dados do Fragrantica? Os dados de preço e estoque serão mantidos.')">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reimportar Fragrantica
                        </button>
                    </form>
                    <a href="{{ $fragrance->fragrantica_url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 border border-border-strong text-dg-400 rounded-lg hover:bg-surface-overlay transition text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Ver no Fragrantica
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Coluna esquerda: Preview do perfume --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Hero --}}
                    <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="overflow-hidden">
                        <div class="p-6">
                            <div class="flex gap-6">
                                @if($fragrance->image_url)
                                    <div class="flex-shrink-0">
                                        <img src="{{ $fragrance->image_url }}" alt="{{ $fragrance->name }}" class="w-32 h-40 object-contain rounded-lg bg-white/5">
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h1 class="text-xl font-bold text-dg-100">{{ $fragrance->name }}</h1>
                                    <p class="text-sm text-dg-400 mt-0.5">{{ $fragrance->brand ?? 'Marca desconhecida' }}</p>
                                    <div class="flex items-center gap-3 mt-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $fragrance->gender->badgeColor() }}-500/20 text-{{ $fragrance->gender->badgeColor() }}-400">
                                            {{ $fragrance->gender->label() }}
                                        </span>
                                        @if($fragrance->year)
                                            <span class="text-xs text-dg-500">{{ $fragrance->year }}</span>
                                        @endif
                                        @if($fragrance->concentration)
                                            <span class="text-xs text-dg-500">{{ $fragrance->concentration }}</span>
                                        @endif
                                    </div>
                                    @if($fragrance->rating)
                                        <div class="flex items-center gap-2 mt-3">
                                            <span class="text-amber-400 font-bold text-lg">{{ number_format($fragrance->rating, 2) }}</span>
                                            <span class="text-dg-600 text-sm">/5</span>
                                            <span class="text-dg-600 text-xs">({{ number_format($fragrance->votes_count, 0, ',', '.') }} votos)</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Acordes --}}
                    @if($fragrance->accords->isNotEmpty())
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="p-6">
                            <h3 class="text-sm font-semibold text-dg-300 mb-4">Principais Acordes</h3>
                            <div class="space-y-2">
                                @foreach($fragrance->accords as $accord)
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <div class="h-6 rounded-r-lg flex items-center px-3 text-xs font-medium"
                                                 style="width: {{ $accord->percentage }}%; background: {{ $accord->color }}; opacity: 0.85; color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                                                {{ $accord->name }}
                                            </div>
                                        </div>
                                        <span class="text-xs text-dg-500 w-12 text-right">{{ number_format($accord->percentage, 0) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Pirâmide Olfativa --}}
                    @if($fragrance->notes->isNotEmpty())
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="p-6">
                            <h3 class="text-sm font-semibold text-dg-300 mb-4">Pirâmide Olfativa</h3>
                            @foreach(['top' => 'Notas de Topo', 'heart' => 'Notas de Coração', 'base' => 'Notas de Base'] as $layer => $label)
                                @php $layerNotes = $fragrance->notes->where('layer', $layer); @endphp
                                @if($layerNotes->isNotEmpty())
                                    <div class="mb-4 last:mb-0">
                                        <h4 class="text-xs font-semibold text-dg-500 uppercase tracking-wider mb-2">{{ $label }}</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($layerNotes as $note)
                                                <div class="flex items-center gap-2 px-3 py-1.5 bg-surface-overlay rounded-lg">
                                                    @if($note->image_url)
                                                        <img src="{{ $note->image_url }}" alt="{{ $note->name }}" class="w-6 h-6 rounded-full object-cover">
                                                    @endif
                                                    <span class="text-xs text-dg-300">{{ $note->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Descrição --}}
                    @if($fragrance->description)
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="p-6">
                            <h3 class="text-sm font-semibold text-dg-300 mb-3">Descrição</h3>
                            <p class="text-sm text-dg-400 leading-relaxed">{{ $fragrance->description }}</p>
                        </div>
                    @endif
                </div>

                {{-- Coluna direita: Precificação --}}
                <div class="space-y-6" x-data="pricingCalculator()">
                    {{-- Card de Precificação --}}
                    <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="p-6">
                        <h3 class="text-sm font-semibold text-dg-300 mb-4">Precificação</h3>

                        <form method="POST" action="{{ route('fragrances.update', $fragrance) }}">
                            @csrf @method('PUT')

                            <div class="space-y-4">
                                {{-- Custo --}}
                                <div>
                                    <label class="block text-xs font-medium text-dg-500 mb-1">Valor de Custo (R$)</label>
                                    <input type="number" name="cost_price" step="0.01" min="0"
                                           x-model="costPrice"
                                           placeholder="0,00"
                                           class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none">
                                    @error('cost_price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>

                                {{-- Frete % --}}
                                <div>
                                    <label class="block text-xs font-medium text-dg-500 mb-1">Taxa de Frete (%)</label>
                                    <input type="number" name="shipping_rate_percent" step="0.01" min="0" max="100"
                                           x-model="shippingRate"
                                           placeholder="0"
                                           class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none">
                                    @error('shipping_rate_percent') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>

                                {{-- Custo total (auto) --}}
                                <div class="pt-2 border-t border-white/5 space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-dg-500">Frete</span>
                                        <span class="text-dg-400" x-text="'R$ ' + shippingValue.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-dg-500">Custo Total</span>
                                        <span class="text-dg-300 font-medium" x-text="'R$ ' + totalCost.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-white/5"></div>

                                {{-- Preço à vista PIX --}}
                                <div>
                                    <label class="block text-xs font-medium text-dg-500 mb-1">
                                        Preço à Vista no PIX (R$)
                                        <span class="text-dg-600">— valor final que você recebe</span>
                                    </label>
                                    <input type="number" name="pix_price" step="0.01" min="0"
                                           x-model="pixPrice"
                                           placeholder="0,00"
                                           class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none">
                                    @error('pix_price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>

                                {{-- Desconto PIX % --}}
                                <div>
                                    <label class="block text-xs font-medium text-dg-500 mb-1">Desconto PIX (%)</label>
                                    <input type="number" name="pix_discount_percent" min="1" max="30"
                                           x-model="pixDiscount"
                                           class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none">
                                    @error('pix_discount_percent') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div class="pt-2 border-t border-white/5"></div>

                                {{-- Estoque --}}
                                <div>
                                    <label class="block text-xs font-medium text-dg-500 mb-1">Quantidade em Estoque</label>
                                    <input type="number" name="stock_quantity" min="0"
                                           value="{{ old('stock_quantity', $fragrance->stock_quantity) }}"
                                           class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none">
                                    @error('stock_quantity') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>

                                {{-- Ordem --}}
                                <div>
                                    <label class="block text-xs font-medium text-dg-500 mb-1">Ordem de exibição</label>
                                    <input type="number" name="sort_order" min="0"
                                           value="{{ old('sort_order', $fragrance->sort_order) }}"
                                           class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none">
                                </div>

                                {{-- Ativo --}}
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" name="active" value="1" id="active"
                                           {{ old('active', $fragrance->active) ? 'checked' : '' }}
                                           class="rounded border-border-strong text-pink-600 focus:ring-pink-500">
                                    <label for="active" class="text-sm text-dg-300">Visível no catálogo</label>
                                </div>

                                <button type="submit"
                                        class="w-full px-4 py-2.5 bg-pink-600 text-white font-semibold rounded-lg hover:bg-pink-700 transition text-sm">
                                    Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Preview do preço para o catálogo --}}
                    <div style="border-radius: 1rem; border: 2px solid rgba(236,72,153,0.3); background: linear-gradient(135deg, rgba(236,72,153,0.05), rgba(168,85,247,0.05));" class="p-6"
                         x-show="pixPrice > 0" x-cloak>
                        <h3 class="text-sm font-semibold text-pink-400 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview do Catálogo
                        </h3>

                        <div class="text-center space-y-2">
                            <div>
                                <span class="text-2xl font-bold text-white" x-text="'R$ ' + installmentPrice"></span>
                                <span class="text-sm text-dg-400"> em até </span>
                                <span class="text-lg font-bold text-white">10x</span>
                                <span class="text-sm text-dg-400"> sem juros</span>
                            </div>

                            <div class="text-sm text-dg-500">
                                <span class="text-xs">de </span>
                                <span class="text-xs text-dg-600 line-through" x-text="'R$ ' + installmentPrice"></span>
                            </div>

                            <div class="text-sm text-dg-400">ou</div>

                            <div>
                                <span class="text-xl font-bold text-green-400" x-text="'R$ ' + pixFormatted"></span>
                                <span class="text-sm text-dg-400"> à vista no </span>
                                <span class="font-bold text-green-400">PIX</span>
                            </div>
                            <div class="text-xs text-green-500/80">
                                com <span class="font-bold" x-text="pixDiscount + '%'"></span> de desconto
                            </div>

                            <div class="text-xs text-dg-600 mt-2" x-text="'Parcela: 10x de R$ ' + installmentValue"></div>
                        </div>

                        {{-- Margem --}}
                        <div class="mt-4 pt-4 border-t border-white/5 space-y-1" x-show="totalCost > 0">
                            <div class="flex justify-between text-xs">
                                <span class="text-dg-500">Custo Total</span>
                                <span class="text-dg-400" x-text="'R$ ' + totalCost.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-dg-500">Lucro (PIX)</span>
                                <span :class="profit >= 0 ? 'text-green-400' : 'text-red-400'" x-text="'R$ ' + profit.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-dg-500">Margem</span>
                                <span :class="marginPercent >= 0 ? 'text-green-400' : 'text-red-400'" x-text="marginPercent.toFixed(1).replace('.', ',') + '%'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Info card --}}
                    <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="p-6">
                        <h3 class="text-sm font-semibold text-dg-300 mb-3">Informações</h3>
                        <dl class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-dg-500">ID Fragrantica</dt>
                                <dd class="text-dg-300">#{{ $fragrance->fragrantica_id }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-dg-500">Slug</dt>
                                <dd class="text-dg-300">{{ $fragrance->slug }}</dd>
                            </div>
                            @if($fragrance->scraped_at)
                                <div class="flex justify-between">
                                    <dt class="text-dg-500">Importado em</dt>
                                    <dd class="text-dg-300">{{ $fragrance->scraped_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-dg-500">Link do catálogo</dt>
                                <dd>
                                    <a href="{{ route('catalogo.show', $fragrance->slug) }}" target="_blank" class="text-pink-400 hover:text-pink-300">
                                        Ver página →
                                    </a>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Quando Usar --}}
                    @if($fragrance->seasons || $fragrance->day_night)
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="p-6">
                            <h3 class="text-sm font-semibold text-dg-300 mb-3">Quando Usar</h3>
                            @if($fragrance->seasons)
                                <div class="mb-3">
                                    <h4 class="text-xs text-dg-500 mb-2">Estações</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($fragrance->seasons as $season => $votes)
                                            <div class="text-center p-2 bg-surface-overlay rounded-lg">
                                                <span class="text-xs text-dg-300 capitalize">{{ $season }}</span>
                                                <span class="block text-xs text-dg-500">{{ number_format($votes, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($fragrance->day_night)
                                <div>
                                    <h4 class="text-xs text-dg-500 mb-2">Horário</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($fragrance->day_night as $period => $votes)
                                            <div class="text-center p-2 bg-surface-overlay rounded-lg">
                                                <span class="text-xs text-dg-300 capitalize">{{ $period }}</span>
                                                <span class="block text-xs text-dg-500">{{ number_format($votes, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function pricingCalculator() {
            const MDR_10X = 0.0968;

            return {
                costPrice: {{ old('cost_price', $fragrance->cost_price ?? 0) }},
                shippingRate: {{ old('shipping_rate_percent', $fragrance->shipping_rate_percent ?? 0) }},
                pixPrice: {{ old('pix_price', $fragrance->pix_price ?? 0) }},
                pixDiscount: {{ old('pix_discount_percent', $fragrance->pix_discount_percent ?? 10) }},

                get shippingValue() {
                    const cost = parseFloat(this.costPrice || 0);
                    const rate = parseFloat(this.shippingRate || 0);
                    return cost * (rate / 100);
                },

                get totalCost() {
                    return parseFloat(this.costPrice || 0) + this.shippingValue;
                },

                get grossUp() {
                    const pix = parseFloat(this.pixPrice || 0);
                    if (pix <= 0) return 0;
                    return pix / (1 - MDR_10X);
                },

                get installmentPriceRaw() {
                    return Math.ceil(this.grossUp / 10) * 10;
                },

                get installmentPrice() {
                    return this.installmentPriceRaw.toLocaleString('pt-BR');
                },

                get installmentValue() {
                    const raw = this.installmentPriceRaw / 10;
                    return raw.toFixed(2).replace('.', ',');
                },

                get pixFormatted() {
                    const pix = parseFloat(this.pixPrice || 0);
                    return pix.toFixed(2).replace('.', ',');
                },

                get profit() {
                    return parseFloat(this.pixPrice || 0) - this.totalCost;
                },

                get marginPercent() {
                    const pix = parseFloat(this.pixPrice || 0);
                    if (pix <= 0 || this.totalCost <= 0) return 0;
                    return ((pix - this.totalCost) / pix) * 100;
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
