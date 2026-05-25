<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F5F5F7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <title>@yield('title', 'Portal Fornecedor') — DG Store</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --apple-bg: #F5F5F7;
            --apple-surface: #FFFFFF;
            --apple-text: #1D1D1F;
            --apple-text-secondary: #86868B;
            --apple-text-tertiary: #AEAEB2;
            --apple-blue: #007AFF;
            --apple-blue-hover: #0051D5;
            --apple-border: rgba(0, 0, 0, 0.06);
            --apple-separator: #E5E5EA;
            --apple-green: #34C759;
            --apple-red: #FF3B30;
            --apple-orange: #FF9500;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --nav-height: 3.5rem;
            --tab-height: 3.25rem;
        }

        * {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background: var(--apple-bg);
            color: var(--apple-text);
        }

        /* ── Top bar ── */
        .supplier-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            height: calc(var(--nav-height) + var(--safe-top));
            padding-top: var(--safe-top);
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 0.5px solid var(--apple-separator);
        }

        /* ── Sidebar (desktop) ── */
        .supplier-sidebar {
            position: fixed;
            top: calc(var(--nav-height) + var(--safe-top));
            left: 0;
            bottom: 0;
            width: 15rem;
            background: var(--apple-surface);
            border-right: 0.5px solid var(--apple-separator);
            overflow-y: auto;
            display: none;
        }

        @media (min-width: 1024px) {
            .supplier-sidebar { display: block; }
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            margin: 0 0.5rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--apple-text);
            transition: background 0.15s ease;
        }

        .sidebar-link:hover { background: var(--apple-bg); }
        .sidebar-link.active {
            background: var(--apple-blue);
            color: #fff;
        }
        .sidebar-link.active svg { opacity: 1; }

        /* ── Bottom tab bar (mobile) ── */
        .supplier-tabbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
            height: calc(var(--tab-height) + var(--safe-bottom));
            padding-bottom: var(--safe-bottom);
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-top: 0.5px solid var(--apple-separator);
            display: flex;
            align-items: stretch;
        }

        @media (min-width: 1024px) {
            .supplier-tabbar { display: none; }
        }

        .tab-link {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.125rem;
            font-size: 0.625rem;
            font-weight: 500;
            color: var(--apple-text-secondary);
            transition: color 0.15s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .tab-link svg { width: 1.375rem; height: 1.375rem; }
        .tab-link.active { color: var(--apple-blue); }

        /* ── Main content ── */
        .supplier-main {
            padding-top: calc(var(--nav-height) + var(--safe-top));
            padding-bottom: calc(var(--tab-height) + var(--safe-bottom) + 1rem);
            min-height: 100vh;
        }

        @media (min-width: 1024px) {
            .supplier-main {
                margin-left: 15rem;
                padding-bottom: 2rem;
            }
        }

        /* ── Design system ── */
        .s-page { max-width: 72rem; margin: 0 auto; padding: 1rem; }
        @media (min-width: 640px) { .s-page { padding: 1.5rem; } }
        @media (min-width: 1024px) { .s-page { padding: 2rem; } }

        .s-title {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.15;
            color: var(--apple-text);
        }
        @media (min-width: 640px) { .s-title { font-size: 2rem; } }

        .s-subtitle {
            font-size: 0.875rem;
            color: var(--apple-text-secondary);
            margin-top: 0.25rem;
        }

        .s-card {
            background: var(--apple-surface);
            border-radius: 1rem;
            border: 0.5px solid var(--apple-separator);
            overflow: hidden;
        }

        .s-card--visible { overflow: visible; }

        /* Search input with icon */
        .s-search-wrap { position: relative; }

        .s-search {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0 0.875rem;
            background: var(--apple-bg);
            border: 0.5px solid var(--apple-separator);
            border-radius: 0.75rem;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .s-search:focus-within {
            border-color: var(--apple-blue);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.12);
        }

        .s-search-icon {
            flex-shrink: 0;
            width: 1rem;
            height: 1rem;
            color: var(--apple-text-tertiary);
            pointer-events: none;
        }

        .s-search-input {
            flex: 1;
            min-width: 0;
            padding: 0.75rem 0;
            font-size: 1rem;
            background: transparent;
            border: none;
            outline: none;
            -webkit-appearance: none;
        }

        .s-search-dropdown {
            position: absolute;
            top: calc(100% + 0.375rem);
            left: 0;
            right: 0;
            z-index: 50;
            background: var(--apple-surface);
            border: 0.5px solid var(--apple-separator);
            border-radius: 0.75rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1), 0 1px 4px rgba(0, 0, 0, 0.06);
            max-height: 15rem;
            overflow-y: auto;
        }

        .s-card-pad { padding: 1rem; }
        @media (min-width: 640px) { .s-card-pad { padding: 1.25rem; } }

        .s-stat {
            background: var(--apple-surface);
            border-radius: 1rem;
            border: 0.5px solid var(--apple-separator);
            padding: 1rem;
        }

        .s-stat-label {
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--apple-text-secondary);
        }

        .s-stat-value {
            font-size: 1.625rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--apple-text);
            margin-top: 0.25rem;
        }

        .s-stat-meta {
            font-size: 0.75rem;
            color: var(--apple-text-tertiary);
            margin-top: 0.125rem;
        }

        .s-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            padding: 0.625rem 1.125rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.15s ease;
            -webkit-tap-highlight-color: transparent;
            white-space: nowrap;
        }

        .s-btn-primary {
            background: var(--apple-blue);
            color: #fff;
        }
        .s-btn-primary:hover { background: var(--apple-blue-hover); }
        .s-btn-primary:disabled { background: #C7C7CC; cursor: not-allowed; }

        .s-btn-secondary {
            background: var(--apple-bg);
            color: var(--apple-text);
            border: 0.5px solid var(--apple-separator);
        }
        .s-btn-secondary:hover { background: #EBEBED; }

        .s-btn-ghost {
            color: var(--apple-blue);
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.375rem 0;
        }

        .s-input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            background: var(--apple-bg);
            border: 0.5px solid var(--apple-separator);
            border-radius: 0.75rem;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            -webkit-appearance: none;
        }
        .s-input:focus {
            border-color: var(--apple-blue);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.12);
        }

        .s-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--apple-text);
            margin-bottom: 0.375rem;
        }

        .s-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.1875rem 0.625rem;
            font-size: 0.6875rem;
            font-weight: 600;
            border-radius: 9999px;
        }
        .s-badge-green { background: rgba(52, 199, 89, 0.12); color: #248A3D; }
        .s-badge-gray { background: rgba(142, 142, 147, 0.12); color: #636366; }
        .s-badge-red { background: rgba(255, 59, 48, 0.12); color: #D70015; }
        .s-badge-yellow { background: rgba(255, 149, 0, 0.12); color: #C93400; }

        .s-alert {
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .s-alert-success { background: rgba(52, 199, 89, 0.1); color: #248A3D; }
        .s-alert-error { background: rgba(255, 59, 48, 0.1); color: #D70015; }

        .s-back {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--apple-blue);
            margin-bottom: 0.75rem;
            -webkit-tap-highlight-color: transparent;
        }

        .s-segment {
            display: flex;
            background: var(--apple-bg);
            border-radius: 0.625rem;
            padding: 0.1875rem;
            gap: 0.125rem;
        }
        .s-segment a {
            flex: 1;
            text-align: center;
            padding: 0.4375rem 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            border-radius: 0.5rem;
            color: var(--apple-text-secondary);
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .s-segment a.active {
            background: var(--apple-surface);
            color: var(--apple-text);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        /* Mobile item cards */
        .s-item-card {
            display: block;
            padding: 0.875rem 1rem;
            border-bottom: 0.5px solid var(--apple-separator);
            transition: background 0.1s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .s-item-card:last-child { border-bottom: none; }
        .s-item-card:active { background: var(--apple-bg); }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased">

    {{-- Top bar --}}
    <header class="supplier-topbar">
        <div class="h-full px-4 sm:px-6 flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-base sm:text-lg font-semibold truncate" style="letter-spacing: -0.02em;">DG Store</p>
                <p class="text-xs truncate hidden sm:block" style="color: var(--apple-text-secondary);">Portal Fornecedor</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="text-xs sm:text-sm font-medium truncate max-w-[120px] sm:max-w-none hidden sm:inline" style="color: var(--apple-text-secondary);">
                    {{ auth('supplier')->user()->supplier->name }}
                </span>
                <form method="POST" action="{{ route('supplier.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium" style="color: var(--apple-blue);">Sair</button>
                </form>
            </div>
        </div>
    </header>

    {{-- Sidebar (desktop) --}}
    <aside class="supplier-sidebar">
        <nav class="p-3 space-y-0.5">
            <a href="{{ route('supplier.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('supplier.stock.index') }}"
               class="sidebar-link {{ request()->routeIs('supplier.stock.index', 'supplier.stock.show', 'supplier.stock.edit') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Meu Estoque
            </a>
            <a href="{{ route('supplier.stock.batch-create') }}"
               class="sidebar-link {{ request()->routeIs('supplier.stock.batch-create') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nova Entrada
            </a>
            <a href="{{ route('supplier.reports') }}"
               class="sidebar-link {{ request()->routeIs('supplier.reports') ? 'active' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Relatórios
            </a>
        </nav>
    </aside>

    {{-- Bottom tab bar (mobile) --}}
    <nav class="supplier-tabbar">
        <a href="{{ route('supplier.dashboard') }}"
           class="tab-link {{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('supplier.dashboard') ? '2' : '1.75' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Início
        </a>
        <a href="{{ route('supplier.stock.index') }}"
           class="tab-link {{ request()->routeIs('supplier.stock.index', 'supplier.stock.show', 'supplier.stock.edit') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('supplier.stock.*') && !request()->routeIs('supplier.stock.batch-create') ? '2' : '1.75' }}" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Estoque
        </a>
        <a href="{{ route('supplier.stock.batch-create') }}"
           class="tab-link {{ request()->routeIs('supplier.stock.batch-create') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('supplier.stock.batch-create') ? '2' : '1.75' }}" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Entrada
        </a>
        <a href="{{ route('supplier.reports') }}"
           class="tab-link {{ request()->routeIs('supplier.reports') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('supplier.reports') ? '2' : '1.75' }}" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Relatórios
        </a>
    </nav>

    {{-- Main --}}
    <main class="supplier-main">
        <div class="s-page">
            @if (session('success'))
                <div class="s-alert s-alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="s-alert s-alert-error">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="s-alert s-alert-error">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
