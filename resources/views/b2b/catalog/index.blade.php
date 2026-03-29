<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Catalogo Atacado</h2>
                <div class="flex items-center gap-3 mt-1.5">
                    <p class="text-sm text-gray-400">{{ $products->total() }} {{ $products->total() === 1 ? 'produto disponivel' : 'produtos disponiveis' }}</p>
                    <div x-data="stockPulse()" class="flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-medium text-emerald-600" x-text="label"></span>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('b2b.catalog') }}" x-data="{ filtersOpen: false }" class="relative">
                {{-- Mobile filter toggle --}}
                <button type="button" @click="filtersOpen = !filtersOpen" class="sm:hidden w-full apple-btn-secondary py-2.5 text-sm justify-between">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                        Filtros
                    </span>
                    @if(request()->hasAny(['search', 'model', 'condition']))
                        <span class="apple-badge bg-blue-50 text-blue-600">Ativo</span>
                    @endif
                </button>

                {{-- Filter controls --}}
                <div class="hidden sm:flex flex-wrap items-center gap-2" :class="{ '!flex flex-col sm:flex-row mt-3': filtersOpen }" x-cloak>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Buscar produto..."
                           class="apple-input w-full sm:w-48 py-2.5" />
                    <select name="model" onchange="this.form.submit()" class="apple-select w-full sm:w-auto py-2.5">
                        <option value="">Todos os modelos</option>
                        @foreach($availableModels as $model)
                            <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                        @endforeach
                    </select>
                    <select name="condition" onchange="this.form.submit()" class="apple-select w-full sm:w-auto py-2.5">
                        <option value="">Todas condicoes</option>
                        <option value="sealed" {{ request('condition') == 'sealed' ? 'selected' : '' }}>Lacrado</option>
                        <option value="semi_new" {{ request('condition') == 'semi_new' ? 'selected' : '' }}>Semi-novo</option>
                    </select>
                    @if(request()->hasAny(['search', 'model', 'condition']))
                        <a href="{{ route('b2b.catalog') }}" class="text-sm text-gray-400 hover:text-gray-600 transition-colors px-2">Limpar</a>
                    @endif
                </div>
            </form>
        </div>
    </x-slot>

    @if($products->isEmpty())
        <div class="text-center py-20 apple-card">
            <div class="w-16 h-16 mx-auto bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900">Nenhum produto encontrado</h3>
            <p class="mt-1 text-sm text-gray-400">Tente ajustar os filtros de busca.</p>
        </div>
    @else
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
            @foreach($products as $product)
                <div class="group apple-card overflow-hidden hover:shadow-md hover:border-gray-300/60 transition-all duration-300"
                     x-data="{ qty: 1 }">
                    {{-- Image --}}
                    <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100/80">
                        @if($product->photo)
                            <div class="aspect-square sm:aspect-[4/3]">
                                <img src="{{ $product->photo_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" />
                            </div>
                        @else
                            <div class="aspect-square sm:aspect-[4/3] flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                                </svg>
                            </div>
                        @endif
                        {{-- Condition badge --}}
                        <div class="absolute top-2 left-2 sm:top-3 sm:left-3">
                            <span class="apple-badge text-[10px] sm:text-xs shadow-sm {{ $product->condition->value === 'sealed' ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                                {{ $product->condition->label() }}
                            </span>
                        </div>
                        @if($product->stock_quantity <= 5)
                            <div class="absolute top-2 right-2 sm:top-3 sm:right-3">
                                <span class="apple-badge text-[10px] sm:text-xs bg-red-500 text-white shadow-sm">
                                    {{ $product->stock_quantity <= 2 ? 'Ultimas' : 'Poucas' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-3 sm:p-4">
                        <h3 class="font-semibold text-gray-900 text-xs sm:text-sm leading-tight truncate">{{ $product->name }}</h3>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="text-[10px] sm:text-xs font-medium text-gray-500 bg-gray-50 px-1.5 py-0.5 rounded-md">{{ $product->storage }}</span>
                            @if($product->color)
                                <span class="text-[10px] sm:text-xs text-gray-400 truncate">{{ $product->color }}</span>
                            @endif
                        </div>

                        <div class="flex items-end justify-between mt-3 mb-3">
                            <div>
                                <span class="text-base sm:text-xl font-bold text-gray-900">{{ $product->formatted_wholesale_price }}</span>
                                <span class="text-[10px] sm:text-xs text-gray-400 ml-0.5">/ un.</span>
                            </div>
                            <span class="hidden sm:inline text-xs font-medium px-2 py-0.5 rounded-full {{ $product->stock_quantity > 10 ? 'bg-gray-50 text-gray-500' : 'bg-orange-50 text-orange-600' }}">
                                {{ $product->stock_quantity }} un.
                            </span>
                        </div>

                        {{-- Add to cart --}}
                        <form method="POST" action="{{ route('b2b.cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}" />
                            <div class="flex gap-2">
                                <div class="hidden sm:flex items-center border border-gray-200 rounded-xl overflow-hidden bg-gray-50/50">
                                    <button type="button" @click="qty = Math.max(1, qty - 1)"
                                            class="px-2.5 py-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 12h-15"/></svg>
                                    </button>
                                    <input type="number" name="quantity" x-model.number="qty" min="1" max="{{ $product->stock_quantity }}"
                                           class="w-10 text-center text-sm font-medium border-0 bg-transparent focus:ring-0 p-0 py-2" />
                                    <button type="button" @click="qty = Math.min({{ $product->stock_quantity }}, qty + 1)"
                                            class="px-2.5 py-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    </button>
                                </div>
                                <input type="hidden" name="quantity" x-bind:value="qty" class="sm:hidden" />
                                <button type="submit"
                                        class="flex-1 py-2.5 apple-btn-dark text-xs sm:text-sm rounded-xl">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    <span class="sm:inline">Adicionar</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif

    <script>
        function stockPulse() {
            return {
                seconds: 0,
                label: 'Atualizado agora',
                init() {
                    setInterval(() => {
                        this.seconds++;
                        if (this.seconds < 5) this.label = 'Atualizado agora';
                        else if (this.seconds < 60) this.label = 'Ha ' + this.seconds + 's';
                        else this.label = 'Ha ' + Math.floor(this.seconds / 60) + 'min';
                    }, 1000);
                }
            }
        }
    </script>
</x-b2b-app-layout>
