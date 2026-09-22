<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DG Perfumes — Catálogo')</title>
    <meta name="description" content="@yield('description', 'Catálogo de perfumes importados — DG Perfumes')">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <meta property="og:title" content="@yield('title', 'DG Perfumes — Catálogo')">
    <meta property="og:description" content="@yield('description', 'Catálogo de perfumes importados — DG Perfumes')">
    <link rel="icon" type="image/png" href="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { background: #0a0a0a; color: #e5e5e5; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.5); }
        .accord-bar { transition: width 0.6s ease-out; }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">
    <!-- Header -->
    <header class="sticky top-0 z-30 border-b border-white/10" style="background: linear-gradient(135deg, #1a1025, #2d1a3e);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('catalogo.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}" alt="DG Store" class="h-8 w-auto brightness-0 invert">
                    <div>
                        <span class="text-sm font-bold text-white">DG Perfumes</span>
                        <span class="block text-[10px] font-semibold text-pink-300 tracking-widest uppercase">Catálogo</span>
                    </div>
                </a>
                @auth
                    <a href="{{ route('fragrances.index') }}" class="text-sm text-pink-300/70 hover:text-white font-medium transition">
                        Área Admin
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-white/10 mt-12" style="background: #111;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
            <p class="text-xs text-gray-500">&copy; {{ date('Y') }} DG Perfumes. Todos os direitos reservados.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
