<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Catálogo Atacado</h2>
                <div class="flex items-center gap-3 mt-1">
                    <p class="text-sm text-gray-500">{{ $products->total() }} {{ $products->total() === 1 ? 'produto disponível' : 'produtos disponíveis' }}</p>
                    <span class="text-gray-300">|</span>
                    <div x-data="stockPulse()" class="flex items-center gap-1.5">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-medium text-emerald-700" x-text="label"></span>
                    </div>
                </div>
            </div>
            <!-- Filtros inline -->
            <form method="GET" action="{{ route('b2b.catalog') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar..."
                       class="w-40 sm:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-gray-50" />
                <select name="model" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-gray-50">
                    <option value="">Todos os modelos</option>
                    @foreach($availableModels as $model)
                        <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                    @endforeach
                </select>
                <select name="condition" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-gray-50">
                    <option value="">Todas condições</option>
                    <option value="sealed" {{ request('condition') == 'sealed' ? 'selected' : '' }}>Lacrado</option>
                    <option value="semi_new" {{ request('condition') == 'semi_new' ? 'selected' : '' }}>Semi-novo</option>
                </select>
                @if(request()->hasAny(['search', 'model', 'condition']))
                    <a href="{{ route('b2b.catalog') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-900 transition">Limpar</a>
                @endif
            </form>
        </div>
    </x-slot>

    @if($products->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-200">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="mt-4 text-base font-medium text-gray-900">Nenhum produto encontrado</h3>
            <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros de busca.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($products as $product)
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-gray-300 transition-all duration-300"
                     x-data="{ qty: 1, adding: false }">
                    <!-- Imagem -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
                        @if($product->photo)
                            <div class="aspect-[4/3]">
                                <img src="{{ Storage::url($product->photo) }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            </div>
                        @else
                            <div class="aspect-[4/3] flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <!-- Badge condição -->
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm
                                {{ $product->condition->value === 'sealed' ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                                {{ $product->condition->label() }}
                            </span>
                        </div>
                        <!-- Badge estoque baixo -->
                        @if($product->stock_quantity <= 5)
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-500 text-white shadow-sm">
                                    {{ $product->stock_quantity <= 2 ? 'Últimas unidades' : 'Poucas unidades' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="p-4">
                        <div class="mb-2">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $product->name }}</h3>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $product->storage }}</span>
                                @if($product->color)
                                    <span class="text-xs text-gray-500">{{ $product->color }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-end justify-between mt-3 mb-3">
                            <div>
                                <span class="text-xl font-extrabold text-gray-900">{{ $product->formatted_wholesale_price }}</span>
                                <span class="text-xs text-gray-400 ml-1">/ un.</span>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $product->stock_quantity > 10 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-700' }}">
                                {{ $product->stock_quantity }} em estoque
                            </span>
                        </div>

                        <!-- Adicionar ao carrinho -->
                        <form method="POST" action="{{ route('b2b.cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}" />
                            <div class="flex gap-2">
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                    <button type="button" @click="qty = Math.max(1, qty - 1)"
                                            class="px-2.5 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <input type="number" name="quantity" x-model.number="qty" min="1" max="{{ $product->stock_quantity }}"
                                           class="w-12 text-center text-sm font-medium border-0 focus:ring-0 p-0 py-2" />
                                    <button type="button" @click="qty = Math.min({{ $product->stock_quantity }}, qty + 1)"
                                            class="px-2.5 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                                <button type="submit"
                                        class="flex-1 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-lg transition-all active:scale-95 flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Adicionar
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
                        if (this.seconds < 5) {
                            this.label = 'Atualizado agora';
                        } else if (this.seconds < 60) {
                            this.label = 'Atualizado há ' + this.seconds + 's';
                        } else {
                            this.label = 'Atualizado há ' + Math.floor(this.seconds / 60) + 'min';
                        }
                    }, 1000);
                }
            }
        }
    </script>
</x-b2b-app-layout>
