@php
    $retailer = Auth::guard('b2b')->user();
    $cart = session('b2b_cart', []);
    $cartCount = count($cart);
    $cartTotal = 0;
    foreach ($cart as $item) {
        $cartTotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
    }
@endphp

<nav class="glass sticky top-0 z-50" x-data="{ mobileOpen: false, userOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14 sm:h-16">
            {{-- Logo + Nav links --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('b2b.catalog') }}" class="flex items-center gap-2.5 shrink-0">
                    <x-apple-logo class="h-7 sm:h-8 w-auto text-gray-900" />
                    <span class="hidden sm:inline text-xs font-bold text-blue-500 tracking-widest uppercase">B2B</span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('b2b.catalog') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('b2b.catalog') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Catalogo
                    </a>
                    <a href="{{ route('b2b.orders') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('b2b.orders*') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Pedidos
                    </a>
                </div>
            </div>

            {{-- Right side: Cart + User --}}
            <div class="flex items-center gap-2">
                {{-- Cart --}}
                <a href="{{ route('b2b.cart') }}"
                   class="relative flex items-center gap-2 px-3 py-2 rounded-xl transition-all duration-200 {{ request()->routeIs('b2b.cart') ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    @if($cartCount > 0)
                        <span class="hidden sm:inline text-sm font-semibold">R$ {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        <span class="absolute -top-0.5 -right-0.5 bg-blue-500 text-white text-[10px] font-bold rounded-full h-4.5 w-4.5 flex items-center justify-center min-w-[18px] ring-2 ring-white">{{ $cartCount }}</span>
                    @endif
                </a>

                {{-- User dropdown (desktop) --}}
                <div class="hidden md:block relative">
                    <button @click="userOpen = !userOpen" @click.away="userOpen = false"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-xs font-bold text-white uppercase shadow-sm">
                            {{ substr($retailer->store_name, 0, 1) }}
                        </div>
                        <span class="max-w-[120px] truncate font-medium">{{ $retailer->store_name }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': userOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="userOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-lg ring-1 ring-gray-200/60 z-50 overflow-hidden" x-cloak>
                        <div class="px-4 py-3.5 bg-gray-50/80">
                            <p class="text-sm font-semibold text-gray-900">{{ $retailer->store_name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $retailer->email }}</p>
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100">
                            <p class="text-xs text-gray-400">{{ $retailer->city }}/{{ $retailer->state }}</p>
                        </div>
                        <div class="border-t border-gray-100 p-1.5">
                            <form method="POST" action="{{ route('b2b.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-xl transition-colors duration-200 font-medium">
                                    Sair da conta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile slide-over --}}
    <template x-teleport="body">
        <div x-show="mobileOpen" class="fixed inset-0 z-[70] md:hidden" x-cloak>
            <div x-show="mobileOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/30 backdrop-blur-sm"
                 @click="mobileOpen = false"></div>

            <div x-show="mobileOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="fixed inset-y-0 right-0 w-80 max-w-[85vw] bg-white shadow-2xl flex flex-col">

                {{-- Mobile header --}}
                <div class="flex items-center justify-between px-5 h-14 border-b border-gray-100">
                    <span class="text-sm font-semibold text-gray-900">Menu</span>
                    <button @click="mobileOpen = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Mobile nav --}}
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    <a href="{{ route('b2b.catalog') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('b2b.catalog') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                        Catalogo
                    </a>
                    <a href="{{ route('b2b.cart') }}" class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('b2b.cart') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            Carrinho
                        </span>
                        @if($cartCount > 0)
                            <span class="apple-badge bg-blue-50 text-blue-600">{{ $cartCount }} itens</span>
                        @endif
                    </a>
                    <a href="{{ route('b2b.orders') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('b2b.orders*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                        Meus Pedidos
                    </a>
                </nav>

                {{-- Mobile user --}}
                <div class="border-t border-gray-100 p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-sm font-bold text-white uppercase shadow-sm">
                            {{ substr($retailer->store_name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $retailer->store_name }}</p>
                            <p class="text-xs text-gray-400">{{ $retailer->city }}/{{ $retailer->state }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('b2b.logout') }}">
                        @csrf
                        <button type="submit" class="w-full apple-btn text-red-500 bg-red-50 hover:bg-red-100 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            Sair da conta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</nav>
