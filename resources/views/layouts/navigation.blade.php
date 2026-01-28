<nav x-data="{ open: false }" class="bg-gray-900 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('images/logodg.png') }}" alt="DG Store" class="h-10 w-auto brightness-0 invert" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                        Dashboard
                    </a>
                    
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('products.*') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                        Produtos
                    </a>
                    
                    <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('customers.*') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                        Clientes
                    </a>
                    
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('sales.*') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                        Vendas
                    </a>
                    
                    <a href="{{ route('stock.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('stock.*') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                        Estoque
                    </a>
                    
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('suppliers.*') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                        Fornecedores
                    </a>
                    
                    <a href="{{ route('quotations.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('quotations.*') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                        Cotações
                    </a>
                    
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('reports.*') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                            Relatórios
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Botão Nova Venda Rápida -->
                <a href="{{ route('sales.create') }}" class="mr-4 px-4 py-2 bg-white text-gray-900 text-sm font-bold rounded-lg hover:bg-gray-100 transition">
                    + Nova Venda
                </a>
                
                <!-- Alerta de Estoque Baixo -->
                <a href="{{ route('stock.alerts') }}" class="relative mr-4 text-gray-300 hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </a>
                
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-700 text-sm leading-4 font-medium rounded-lg text-gray-300 bg-gray-800 hover:text-white hover:border-gray-600 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1 text-xs text-gray-500">({{ Auth::user()->role->label() }})</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Meu Perfil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Sair
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-800">
        <!-- Botão Nova Venda Mobile -->
        <div class="px-4 py-3 border-b border-gray-700">
            <a href="{{ route('sales.create') }}" class="block w-full text-center px-4 py-3 bg-white text-gray-900 font-bold rounded-lg">
                + Nova Venda
            </a>
        </div>
        
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Dashboard
            </a>
            <a href="{{ route('products.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('products.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Produtos
            </a>
            <a href="{{ route('customers.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('customers.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Clientes
            </a>
            <a href="{{ route('sales.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('sales.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Vendas
            </a>
            <a href="{{ route('stock.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('stock.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Estoque
            </a>
            <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('suppliers.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Fornecedores
            </a>
            <a href="{{ route('quotations.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('quotations.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Cotações
            </a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('reports.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('reports.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Relatórios
                </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">
                    Meu Perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
