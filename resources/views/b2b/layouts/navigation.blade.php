@php
    $retailer = Auth::guard('b2b')->user();
    $cart = session('b2b_cart', []);
    $cartCount = count($cart);
    $cartTotal = 0;
    foreach ($cart as $item) {
        $cartTotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
    }
@endphp

<nav class="bg-gray-900 sticky top-0 z-50" x-data="{ mobileOpen: false, userOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo + Links -->
            <div class="flex items-center gap-2 sm:gap-6">
                <a href="{{ route('b2b.catalog') }}" class="flex items-center gap-2.5 shrink-0">
                    <img src="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}" alt="DG Store" class="h-8 w-auto brightness-0 invert" />
                    <span class="hidden sm:inline text-xs font-bold text-blue-400 tracking-widest uppercase">Distribuidora</span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('b2b.catalog') }}"
                       class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('b2b.catalog') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Catálogo
                    </a>
                    <a href="{{ route('b2b.orders') }}"
                       class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('b2b.orders*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Pedidos
                    </a>
                </div>
            </div>

            <!-- Direita: Carrinho + User -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Carrinho -->
                <a href="{{ route('b2b.cart') }}"
                   class="relative inline-flex items-center gap-2 px-3 py-2 rounded-lg transition {{ request()->routeIs('b2b.cart') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    @if($cartCount > 0)
                        <span class="hidden sm:inline text-sm font-medium">R$ {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center ring-2 ring-gray-900">{{ $cartCount }}</span>
                    @else
                        <span class="hidden sm:inline text-sm">Carrinho</span>
                    @endif
                </a>

                <!-- User desktop -->
                <div class="hidden md:block relative">
                    <button @click="userOpen = !userOpen" @click.away="userOpen = false"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-300 hover:text-white rounded-lg hover:bg-gray-800 transition">
                        <div class="w-7 h-7 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white uppercase">
                            {{ substr($retailer->store_name, 0, 1) }}
                        </div>
                        <span class="max-w-[120px] truncate">{{ $retailer->store_name }}</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': userOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="userOpen" x-transition
                         class="absolute right-0 mt-2 w-56 bg-gray-800 rounded-lg shadow-xl ring-1 ring-black ring-opacity-20 z-50" x-cloak>
                        <div class="px-4 py-3 border-b border-gray-700">
                            <p class="text-sm font-medium text-white">{{ $retailer->store_name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $retailer->email }}</p>
                        </div>
                        <div class="py-1">
                            <p class="px-4 py-2 text-xs text-gray-500">{{ $retailer->city }}/{{ $retailer->state }}</p>
                        </div>
                        <div class="border-t border-gray-700 py-1">
                            <form method="POST" action="{{ route('b2b.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-gray-700 transition">
                                    Sair da conta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile toggle -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
         class="md:hidden border-t border-gray-800" x-cloak>
        <div class="px-3 py-3 space-y-1">
            <a href="{{ route('b2b.catalog') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('b2b.catalog') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Catálogo
            </a>
            <a href="{{ route('b2b.cart') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('b2b.cart') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Carrinho
                </span>
                @if($cartCount > 0)
                    <span class="bg-blue-600 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $cartCount }} - R$ {{ number_format($cartTotal, 0, ',', '.') }}</span>
                @endif
            </a>
            <a href="{{ route('b2b.orders') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('b2b.orders*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Meus Pedidos
            </a>
        </div>
        <div class="border-t border-gray-800 px-3 py-3">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white uppercase">
                    {{ substr($retailer->store_name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ $retailer->store_name }}</p>
                    <p class="text-xs text-gray-400">{{ $retailer->city }}/{{ $retailer->state }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('b2b.logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-gray-800 hover:text-red-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sair da conta
                </button>
            </form>
        </div>
    </div>
</nav>
