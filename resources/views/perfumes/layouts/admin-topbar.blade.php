@php
    $pendingPayments = \App\Domain\Perfumes\Models\PerfumeOrder::where('payment_status', 'pending')->count();
    $samplesOut = \App\Domain\Perfumes\Models\PerfumeSample::whereIn('status', ['delivered', 'with_retailer'])->count();
@endphp

<header x-data="{ mobileMenu: false, userMenu: false }" class="sticky top-0 z-30 bg-white border-b border-gray-200 lg:ml-0">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
        <!-- Mobile: hamburger + logo -->
        <div class="flex items-center gap-3 lg:hidden">
            <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <span class="text-sm font-bold text-gray-900">Perfumes</span>
        </div>

        <!-- Desktop: breadcrumb area -->
        <div class="hidden lg:flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
            <span>Painel Revenda de Perfumes</span>
        </div>

        <!-- Right: user dropdown -->
        <div class="flex items-center gap-3">
            <div class="relative">
                <button @click="userMenu = !userMenu" @click.away="userMenu = false"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-xs font-bold text-white uppercase">
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
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="lg:hidden border-t border-gray-200 bg-white" x-cloak>
        <nav class="px-3 py-3 space-y-1">
            <a href="{{ route('admin.perfumes.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.dashboard') ? 'bg-pink-50 text-pink-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.perfumes.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.products.*') ? 'bg-pink-50 text-pink-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Produtos
            </a>
            <a href="{{ route('admin.perfumes.import') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.import*') ? 'bg-pink-50 text-pink-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Importar PDF
            </a>
            <a href="{{ route('admin.perfumes.retailers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.retailers.*') ? 'bg-pink-50 text-pink-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Lojistas
            </a>
            <a href="{{ route('admin.perfumes.samples.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.samples.*') ? 'bg-pink-50 text-pink-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Amostras
                </span>
                @if($samplesOut > 0)
                    <span class="bg-amber-500 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $samplesOut }}</span>
                @endif
            </a>
            <a href="{{ route('admin.perfumes.orders.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.orders.*') ? 'bg-pink-50 text-pink-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Pedidos
                </span>
                @if($pendingPayments > 0)
                    <span class="bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $pendingPayments }}</span>
                @endif
            </a>
            <a href="{{ route('admin.perfumes.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.perfumes.settings.*') ? 'bg-pink-50 text-pink-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Configurações
            </a>

            @if(auth()->user()->canAccessDGStore())
            <div class="border-t border-gray-200 pt-2 mt-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    Voltar ao Sistema Principal
                </a>
            </div>
            @endif
        </nav>
    </div>
</header>
