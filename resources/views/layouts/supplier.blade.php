<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Portal Fornecedor') - DG Store</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body class="h-full antialiased">
    <nav class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-50 h-16">
        <div class="h-full px-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h1 class="text-xl font-semibold text-gray-900">DG Store</h1>
                <span class="text-sm text-gray-500">Portal Fornecedor</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <span class="text-sm text-gray-700">{{ auth('supplier')->user()->supplier->name }}</span>
                <form method="POST" action="{{ route('supplier.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </nav>
    
    <div class="flex pt-16 h-full">
        <aside class="fixed left-0 w-64 h-[calc(100vh-4rem)] bg-white border-r border-gray-200 overflow-y-auto">
            <nav class="p-4 space-y-1">
                <a href="{{ route('supplier.dashboard') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('supplier.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('supplier.stock.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('supplier.stock.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Meu Estoque
                </a>
                
                <a href="{{ route('supplier.stock.batch-create') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('supplier.stock.batch-create') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Entrada
                </a>
                
                <a href="{{ route('supplier.reports') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('supplier.reports') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Relatórios
                </a>
            </nav>
        </aside>
        
        <main class="flex-1 ml-64 p-8 overflow-y-auto">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-sm text-red-800 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
</body>
</html>
