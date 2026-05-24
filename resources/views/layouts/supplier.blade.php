<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Portal Fornecedor') - DG Store</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            background: #F5F5F7;
        }
        
        .apple-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .apple-sidebar {
            background: #FFFFFF;
            border-right: 1px solid #E5E5EA;
        }
        
        .apple-nav-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .apple-nav-item:hover {
            background: #F5F5F7;
        }
        
        .apple-nav-item.active {
            background: #007AFF;
            color: white;
        }
        
        .apple-card {
            background: #FFFFFF;
            border: 1px solid #E5E5EA;
        }
        
        .apple-button {
            background: #007AFF;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .apple-button:hover {
            background: #0051D5;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="h-full antialiased">
    <nav class="fixed top-0 left-0 right-0 apple-nav z-50 h-16">
        <div class="h-full px-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h1 class="text-xl font-semibold" style="color: #1D1D1F; letter-spacing: -0.3px;">DG Store</h1>
                <span class="text-sm" style="color: #86868B;">Portal Fornecedor</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <span class="text-sm font-medium" style="color: #1D1D1F;">{{ auth('supplier')->user()->supplier->name }}</span>
                <form method="POST" action="{{ route('supplier.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium transition-colors" style="color: #007AFF;">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </nav>
    
    <div class="flex pt-16 h-full">
        <aside class="fixed left-0 w-64 h-[calc(100vh-4rem)] apple-sidebar overflow-y-auto">
            <nav class="p-4 space-y-1">
                <a href="{{ route('supplier.dashboard') }}" 
                   class="apple-nav-item flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}"
                   style="{{ request()->routeIs('supplier.dashboard') ? '' : 'color: #1D1D1F;' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('supplier.stock.index') }}" 
                   class="apple-nav-item flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('supplier.stock.*') && !request()->routeIs('supplier.stock.batch-create') ? 'active' : '' }}"
                   style="{{ request()->routeIs('supplier.stock.*') && !request()->routeIs('supplier.stock.batch-create') ? '' : 'color: #1D1D1F;' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Meu Estoque
                </a>
                
                <a href="{{ route('supplier.stock.batch-create') }}" 
                   class="apple-nav-item flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('supplier.stock.batch-create') ? 'active' : '' }}"
                   style="{{ request()->routeIs('supplier.stock.batch-create') ? '' : 'color: #1D1D1F;' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Entrada
                </a>
                
                <a href="{{ route('supplier.reports') }}" 
                   class="apple-nav-item flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('supplier.reports') ? 'active' : '' }}"
                   style="{{ request()->routeIs('supplier.reports') ? '' : 'color: #1D1D1F;' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Relatórios
                </a>
            </nav>
        </aside>
        
        <main class="flex-1 ml-64 p-8 overflow-y-auto">
            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl" style="background: #E8F5E9; border: 1px solid #C8E6C9;">
                    <p class="text-sm font-medium" style="color: #2E7D32;">{{ session('success') }}</p>
                </div>
            @endif
            
            @if (session('error'))
                <div class="mb-6 p-4 rounded-xl" style="background: #FFEBEE; border: 1px solid #FFCDD2;">
                    <p class="text-sm font-medium" style="color: #C62828;">{{ session('error') }}</p>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl" style="background: #FFEBEE; border: 1px solid #FFCDD2;">
                    <ul class="list-disc list-inside text-sm space-y-1" style="color: #C62828;">
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
