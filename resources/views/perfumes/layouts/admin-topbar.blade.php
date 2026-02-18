<header x-data="{ mobileMenu: false, userMenu: false }" class="sticky top-0 z-30 bg-white border-b-2 border-pink-100 lg:ml-0">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
        <div class="flex items-center gap-3 lg:hidden">
            <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-lg text-pink-600 hover:bg-pink-50 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <span class="text-sm font-bold text-pink-700">Perfumes</span>
        </div>

        <div class="hidden lg:flex items-center gap-2 text-sm text-gray-600">
            <span class="w-6 h-6 rounded-md bg-pink-100 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
            </span>
            <span class="font-medium">Painel Revenda de Perfumes</span>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <button @click="userMenu = !userMenu" @click.away="userMenu = false"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-pink-50 transition">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center text-xs font-bold text-white uppercase shadow-md shadow-pink-500/20">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span class="hidden sm:inline font-medium">{{ Auth::user()->name }}</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': userMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="userMenu" x-transition x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-gray-200 z-50">
                    @if(auth()->user()->canAccessDGStore())
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Sistema Principal
                    </a>
                    @endif
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile navigation -->
    <div x-show="mobileMenu" x-transition x-cloak class="lg:hidden border-t border-pink-100 bg-white">
        <nav class="px-3 py-3 space-y-1">
            <a href="{{ route('admin.perfumes.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.dashboard') ? 'bg-pink-100 text-pink-800 font-semibold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-700' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.perfumes.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.products.*') ? 'bg-pink-100 text-pink-800 font-semibold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-700' }}">
                Produtos
            </a>
            <a href="{{ route('admin.perfumes.retailers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.retailers.*') ? 'bg-pink-100 text-pink-800 font-semibold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-700' }}">
                Lojistas
            </a>
            <a href="{{ route('admin.perfumes.samples.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.samples.*') ? 'bg-pink-100 text-pink-800 font-semibold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-700' }}">
                Amostras
            </a>
            <a href="{{ route('admin.perfumes.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.orders.*') ? 'bg-pink-100 text-pink-800 font-semibold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-700' }}">
                Pedidos
            </a>
            <a href="{{ route('admin.perfumes.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.settings.*') ? 'bg-pink-100 text-pink-800 font-semibold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-700' }}">
                Configurações
            </a>
            @if(auth()->user()->canAccessDGStore())
            <div class="border-t border-pink-100 pt-2 mt-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-pink-50 hover:text-pink-700 transition">
                    Voltar ao Sistema Principal
                </a>
            </div>
            @endif
        </nav>
    </div>
</header>
