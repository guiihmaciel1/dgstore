<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Carrinho</h2>
                @if(!empty($cartItems))
                    <p class="text-sm text-gray-400 mt-1">{{ count($cartItems) }} {{ count($cartItems) === 1 ? 'produto' : 'produtos' }}</p>
                @endif
            </div>
            @if(!empty($cartItems))
                <a href="{{ route('b2b.catalog') }}" class="apple-btn-secondary py-2 px-4 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    <span class="hidden sm:inline">Continuar comprando</span>
                </a>
            @endif
        </div>
    </x-slot>

    @if(empty($cartItems))
        <div class="text-center py-20 apple-card">
            <div class="w-16 h-16 mx-auto bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900">Seu carrinho esta vazio</h3>
            <p class="mt-1 text-sm text-gray-400">Adicione produtos do catalogo para comecar.</p>
            <a href="{{ route('b2b.catalog') }}" class="mt-6 apple-btn-dark py-3 px-6 text-sm">
                Explorar Catalogo
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Cart items --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach($cartItems as $item)
                    <div class="apple-card p-4 hover:border-gray-300/60 transition-all duration-200">
                        <div class="flex items-start gap-3 sm:gap-4">
                            @if($item['product']->photo)
                                <img src="{{ $item['product']->photo_url }}" alt="" class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover shrink-0" />
                            @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                                    <svg class="w-7 h-7 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $item['product']->full_name }}</h3>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $item['product']->condition->label() }} &bull; {{ $item['product']->storage }}
                                    @if($item['product']->color) &bull; {{ $item['product']->color }} @endif
                                </p>
                                <p class="text-sm font-medium text-gray-500 mt-1">{{ $item['product']->formatted_wholesale_price }} / un.</p>

                                {{-- Mobile: qty + total + remove inline --}}
                                <div class="flex items-center justify-between mt-3">
                                    <form method="POST" action="{{ route('b2b.cart.update') }}" class="flex items-center" x-data="{ qty: {{ $item['quantity'] }} }">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="product_id" value="{{ $item['product']->id }}" />
                                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-gray-50/50">
                                            <button type="submit" @click="qty = Math.max(1, qty - 1)" class="px-2 py-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 12h-15"/></svg>
                                            </button>
                                            <input type="number" name="quantity" x-model.number="qty" min="1" max="{{ $item['product']->stock_quantity }}"
                                                   class="w-10 text-center text-sm font-medium border-0 bg-transparent focus:ring-0 p-0 py-1.5"
                                                   onchange="this.form.submit()" />
                                            <button type="submit" @click="qty = Math.min({{ $item['product']->stock_quantity }}, qty + 1)" class="px-2 py-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            </button>
                                        </div>
                                    </form>

                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-gray-900 text-sm">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                                        <form method="POST" action="{{ route('b2b.cart.remove') }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="product_id" value="{{ $item['product']->id }}" />
                                            <button type="submit" class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Order summary --}}
            <div>
                <div class="apple-card overflow-hidden sticky top-20">
                    <div class="bg-gray-900 px-5 py-4 sm:px-6">
                        <h3 class="font-semibold text-white text-base">Resumo do Pedido</h3>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="space-y-2.5 pb-4 border-b border-gray-100">
                            @foreach($cartItems as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 truncate mr-2">{{ $item['product']->name }} <span class="text-gray-300">x{{ $item['quantity'] }}</span></span>
                                    <span class="font-medium text-gray-900 whitespace-nowrap">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between py-4">
                            <span class="font-semibold text-gray-900">Total</span>
                            <span class="font-bold text-2xl text-gray-900">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>

                        @if($total < $minimumOrder)
                            <div class="mb-4 p-3.5 bg-amber-50 border border-amber-200/60 rounded-xl">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-amber-800">Pedido minimo: R$ {{ number_format($minimumOrder, 2, ',', '.') }}</p>
                                        <p class="text-xs text-amber-600 mt-0.5">Faltam R$ {{ number_format($minimumOrder - $total, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('b2b.orders.store') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="apple-label">Observacoes</label>
                                <textarea name="notes" rows="2"
                                          class="apple-input resize-none py-2.5"
                                          placeholder="Algo sobre o pedido? (opcional)"></textarea>
                            </div>

                            <button type="submit"
                                    @if($total < $minimumOrder) disabled @endif
                                    class="w-full apple-btn-primary py-3.5 text-sm disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Finalizar e Pagar com PIX
                            </button>
                            <p class="text-center text-xs text-gray-300 mt-2.5 flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                Pagamento seguro via PIX
                            </p>
                        </form>

                        <a href="{{ route('b2b.catalog') }}" class="flex items-center justify-center gap-1.5 mt-4 py-2 text-sm text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                            Continuar comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-b2b-app-layout>
