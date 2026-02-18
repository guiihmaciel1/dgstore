<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Carrinho de Compras</h2>
                @if(!empty($cartItems))
                    <p class="text-sm text-gray-500 mt-1">{{ count($cartItems) }} {{ count($cartItems) === 1 ? 'produto' : 'produtos' }} no carrinho</p>
                @endif
            </div>
            @if(!empty($cartItems))
                <a href="{{ route('b2b.catalog') }}" class="hidden sm:inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Continuar comprando
                </a>
            @endif
        </div>
    </x-slot>

    @if(empty($cartItems))
        <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-200">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Seu carrinho está vazio</h3>
            <p class="mt-1 text-sm text-gray-500">Adicione produtos do catálogo para começar.</p>
            <a href="{{ route('b2b.catalog') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
                Explorar Catálogo
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Lista de Itens -->
            <div class="lg:col-span-2 space-y-3">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:border-gray-300 transition-colors">
                        <div class="flex items-center gap-4">
                            @if($item['product']->photo)
                                <img src="{{ Storage::url($item['product']->photo) }}" alt="" class="w-20 h-20 rounded-xl object-cover shrink-0" />
                            @else
                                <div class="w-20 h-20 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $item['product']->full_name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $item['product']->condition->label() }} &bull; {{ $item['product']->storage }}
                                    @if($item['product']->color) &bull; {{ $item['product']->color }} @endif
                                </p>
                                <p class="text-sm font-semibold text-gray-700 mt-1">{{ $item['product']->formatted_wholesale_price }} / un.</p>
                            </div>

                            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3 shrink-0">
                                <form method="POST" action="{{ route('b2b.cart.update') }}" class="flex items-center gap-1" x-data="{ qty: {{ $item['quantity'] }} }">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="product_id" value="{{ $item['product']->id }}" />
                                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                        <button type="submit" @click="qty = Math.max(1, qty - 1)" class="px-2 py-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        </button>
                                        <input type="number" name="quantity" x-model.number="qty" min="1" max="{{ $item['product']->stock_quantity }}"
                                               class="w-12 text-center text-sm font-medium border-0 focus:ring-0 p-0 py-1.5"
                                               onchange="this.form.submit()" />
                                        <button type="submit" @click="qty = Math.min({{ $item['product']->stock_quantity }}, qty + 1)" class="px-2 py-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                </form>

                                <span class="font-bold text-gray-900 text-sm whitespace-nowrap min-w-[80px] text-right">
                                    R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                                </span>

                                <form method="POST" action="{{ route('b2b.cart.remove') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="product_id" value="{{ $item['product']->id }}" />
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Resumo -->
            <div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                    <div class="bg-gray-900 px-6 py-4">
                        <h3 class="font-bold text-white text-lg">Resumo do Pedido</h3>
                    </div>

                    <div class="p-6">
                        <div class="space-y-3 pb-4 border-b border-gray-200">
                            @foreach($cartItems as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 truncate mr-2">{{ $item['product']->name }} <span class="text-gray-400">x{{ $item['quantity'] }}</span></span>
                                    <span class="font-medium text-gray-900 whitespace-nowrap">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between py-4">
                            <span class="font-bold text-gray-900 text-lg">Total</span>
                            <span class="font-extrabold text-2xl text-gray-900">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>

                        @if($total < $minimumOrder)
                            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    <div>
                                        <p class="text-sm font-medium text-amber-800">Pedido mínimo: R$ {{ number_format($minimumOrder, 2, ',', '.') }}</p>
                                        <p class="text-xs text-amber-600 mt-0.5">Faltam R$ {{ number_format($minimumOrder - $total, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('b2b.orders.store') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                                <textarea name="notes" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 resize-none"
                                          placeholder="Algo sobre o pedido? (opcional)"></textarea>
                            </div>

                            <button type="submit"
                                    @if($total < $minimumOrder) disabled @endif
                                    class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98] flex items-center justify-center gap-2 text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Finalizar e Pagar com PIX
                            </button>
                            <p class="text-center text-xs text-gray-400 mt-2 flex items-center justify-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Pagamento seguro via PIX
                            </p>
                        </form>

                        <a href="{{ route('b2b.catalog') }}" class="flex items-center justify-center gap-2 mt-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Continuar comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-b2b-app-layout>
