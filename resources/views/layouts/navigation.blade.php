<nav x-data="{ open: false, commercialOpen: false, financeOpen: false, stockOpen: false, purchasesOpen: false, toolsOpen: false, crmOpen: false }" class="bg-gray-900 shadow-lg" @click.away="commercialOpen = false; financeOpen = false; stockOpen = false; purchasesOpen = false; toolsOpen = false; crmOpen = false">
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
                <div class="hidden sm:-my-px sm:ms-6 sm:flex items-center gap-0.5">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-gray-800 rounded-lg' : 'text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg' }} transition">
                        Dashboard
                    </a>
                    
                    <!-- CRM Pipeline -->
                    <div class="relative">
                        <button @click="crmOpen = !crmOpen; commercialOpen = false; financeOpen = false; stockOpen = false; purchasesOpen = false; toolsOpen = false" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('crm.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                            </svg>
                            <span>CRM</span>
                            @if(($openDealsCount ?? 0) > 0)
                                <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full bg-blue-500 text-white">{{ $openDealsCount }}</span>
                            @endif
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': crmOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="crmOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-48 rounded-lg shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                             x-cloak>
                            <div class="py-1">
                                <a href="{{ route('crm.board') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('crm.board') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                    </svg>
                                    Pipeline
                                </a>
                                <a href="{{ route('crm.history') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('crm.history') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Histórico
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown: Comercial -->
                    <div class="relative">
                        <button @click="commercialOpen = !commercialOpen; crmOpen = false; financeOpen = false; stockOpen = false; purchasesOpen = false; toolsOpen = false" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('customers.*') || request()->routeIs('sales.*') || request()->routeIs('reservations.*') || request()->routeIs('warranties.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <span>Comercial</span>
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': commercialOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="commercialOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-48 rounded-lg shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                             x-cloak>
                            <div class="py-1">
                                <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('customers.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Clientes
                                </a>
                                <a href="{{ route('sales.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('sales.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Vendas
                                </a>
                                <a href="{{ route('reservations.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('reservations.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Reservas
                                </a>
                                <a href="{{ route('warranties.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('warranties.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Garantias
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropdown: Financeiro -->
                    <div class="relative">
                        <button @click="financeOpen = !financeOpen; crmOpen = false; commercialOpen = false; stockOpen = false; purchasesOpen = false; toolsOpen = false" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('finance.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <span>Financeiro</span>
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': financeOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="financeOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-48 rounded-lg shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                             x-cloak>
                            <div class="py-1">
                                <a href="{{ route('finance.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('finance.index') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Painel
                                </a>
                                <a href="{{ route('finance.payables') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('finance.payables') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Contas a Pagar
                                </a>
                                <a href="{{ route('finance.receivables') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('finance.receivables') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Contas a Receber
                                </a>
                                <a href="{{ route('finance.accounts') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('finance.accounts') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Carteiras
                                </a>
                                <a href="{{ route('finance.categories') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('finance.categories') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Categorias
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropdown: Estoque -->
                    <div class="relative">
                        <button @click="stockOpen = !stockOpen; crmOpen = false; commercialOpen = false; financeOpen = false; purchasesOpen = false; toolsOpen = false" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('products.*') || request()->routeIs('stock.*') || request()->routeIs('imports.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <span>Estoque</span>
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': stockOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="stockOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-48 rounded-lg shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                             x-cloak>
                            <div class="py-1">
                                <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('products.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Produtos
                                </a>
                                <a href="{{ route('stock.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('stock.*') && !request()->routeIs('stock.alerts') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                    </svg>
                                    Movimentações
                                </a>
                                <a href="{{ route('imports.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('imports.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Importações
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropdown: Compras -->
                    <div class="relative">
                        <button @click="purchasesOpen = !purchasesOpen; crmOpen = false; commercialOpen = false; financeOpen = false; stockOpen = false; toolsOpen = false" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('suppliers.*') || request()->routeIs('quotations.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <span>Compras</span>
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': purchasesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="purchasesOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-48 rounded-lg shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                             x-cloak>
                            <div class="py-1">
                                <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('suppliers.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Fornecedores
                                </a>
                                <a href="{{ route('quotations.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('quotations.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    Cotações
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropdown: Ferramentas -->
                    <div class="relative">
                        <button @click="toolsOpen = !toolsOpen; crmOpen = false; commercialOpen = false; financeOpen = false; stockOpen = false; purchasesOpen = false" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('valuations.*') || request()->routeIs('imei-lookup') || request()->routeIs('tools.*') || request()->routeIs('followups.*') || request()->routeIs('reports.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Ferramentas</span>
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': toolsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="toolsOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-48 rounded-lg shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                             x-cloak>
                            <div class="py-1">
                                <a href="{{ route('valuations.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('valuations.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Avaliador
                                </a>
                                <a href="{{ route('imei-lookup') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('imei-lookup') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Consulta IMEI
                                </a>
                                <a href="{{ route('tools.checklist') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('tools.checklist') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    Checklist Seminovo
                                </a>
                                <a href="{{ route('tools.whatsapp-messages') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('tools.whatsapp-messages') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Mensagens WhatsApp
                                </a>
                                <a href="{{ route('tools.specs') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('tools.specs') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Ficha Tecnica
                                </a>
                                <a href="{{ route('followups.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('followups.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Follow-ups
                                    @if(($pendingFollowups ?? 0) > 0)
                                        <span class="ml-auto inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full bg-red-500 text-white">{{ $pendingFollowups }}</span>
                                    @endif
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <div class="border-t border-gray-700 my-1"></div>
                                    <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2.5 text-sm {{ request()->routeIs('reports.*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Relatórios
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Botão Nova Venda Rápida -->
                <a href="{{ route('sales.create') }}" class="group relative mr-4 inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:from-emerald-400 hover:to-green-500 transform hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nova Venda</span>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-200"></span>
                    </span>
                </a>
                
                <!-- Alerta de Estoque Baixo -->
                <a href="{{ route('stock.alerts') }}" class="relative mr-4 text-gray-300 hover:text-white transition" title="Alertas de estoque">
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
            <a href="{{ route('sales.create') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nova Venda</span>
            </a>
        </div>
        
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Dashboard
            </a>
            
            <!-- Mobile: CRM -->
            <div class="border-t border-gray-700 mt-2 pt-2">
                <div class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">CRM</div>
                <a href="{{ route('crm.board') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('crm.board') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Pipeline
                    @if(($openDealsCount ?? 0) > 0)
                        <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full bg-blue-500 text-white">{{ $openDealsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('crm.history') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('crm.history') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Histórico
                </a>
            </div>

            <!-- Mobile: Comercial -->
            <div class="border-t border-gray-700 mt-2 pt-2">
                <div class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Comercial</div>
                <a href="{{ route('customers.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('customers.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Clientes
                </a>
                <a href="{{ route('sales.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('sales.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Vendas
                </a>
                <a href="{{ route('reservations.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('reservations.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Reservas
                </a>
                <a href="{{ route('warranties.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('warranties.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Garantias
                </a>
            </div>
            
            <!-- Mobile: Financeiro -->
            <div class="border-t border-gray-700 mt-2 pt-2">
                <div class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Financeiro</div>
                <a href="{{ route('finance.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('finance.index') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Painel
                </a>
                <a href="{{ route('finance.payables') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('finance.payables') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Contas a Pagar
                </a>
                <a href="{{ route('finance.receivables') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('finance.receivables') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Contas a Receber
                </a>
                <a href="{{ route('finance.accounts') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('finance.accounts') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Carteiras
                </a>
                <a href="{{ route('finance.categories') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('finance.categories') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Categorias
                </a>
            </div>
            
            <!-- Mobile: Estoque -->
            <div class="border-t border-gray-700 mt-2 pt-2">
                <div class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estoque</div>
                <a href="{{ route('products.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('products.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Produtos
                </a>
                <a href="{{ route('stock.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('stock.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Movimentações
                </a>
                <a href="{{ route('imports.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('imports.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Importações
                </a>
            </div>
            
            <!-- Mobile: Compras -->
            <div class="border-t border-gray-700 mt-2 pt-2">
                <div class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Compras</div>
                <a href="{{ route('suppliers.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('suppliers.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Fornecedores
                </a>
                <a href="{{ route('quotations.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('quotations.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Cotações
                </a>
            </div>
            
            <!-- Mobile: Ferramentas -->
            <div class="border-t border-gray-700 mt-2 pt-2">
                <div class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ferramentas</div>
                <a href="{{ route('valuations.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('valuations.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Avaliador
                </a>
                <a href="{{ route('imei-lookup') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('imei-lookup') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Consulta IMEI
                </a>
                <a href="{{ route('tools.checklist') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('tools.checklist') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Checklist Seminovo
                </a>
                <a href="{{ route('tools.whatsapp-messages') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('tools.whatsapp-messages') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Mensagens WhatsApp
                </a>
                <a href="{{ route('tools.specs') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('tools.specs') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Ficha Tecnica
                </a>
                <a href="{{ route('followups.index') }}" class="block px-6 py-2 text-base font-medium {{ request()->routeIs('followups.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Follow-ups
                    @if(($pendingFollowups ?? 0) > 0)
                        <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full bg-red-500 text-white">{{ $pendingFollowups }}</span>
                    @endif
                </a>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="border-t border-gray-700 mt-2 pt-2">
                    <a href="{{ route('reports.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('reports.*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                        Relatórios
                    </a>
                </div>
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
