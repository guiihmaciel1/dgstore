<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DG Imports — Catálogo')</title>
    <meta name="description" content="@yield('description', 'Catálogo de perfumes importados — DG Imports')">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @else
        <meta property="og:image" content="{{ asset('images/logo-dg-imports.png') }}">
    @endif
    <meta property="og:title" content="@yield('title', 'DG Imports — Catálogo')">
    <meta property="og:description" content="@yield('description', 'Catálogo de perfumes importados — DG Imports')">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&family=playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --gold: #d4a540;
            --gold-light: #e8c060;
            --gold-dark: #b08830;
            --cream: #f0ece4;
            --bg: #0a0f18;
            --surface: #101822;
            --surface-elevated: #182030;
            --muted: #5a6578;
            --teal: #2ec4b6;
            --teal-light: #5cdbd3;
            --teal-glow: rgba(46, 196, 182, 0.08);
        }

        body {
            background: var(--bg);
            background-image: radial-gradient(ellipse at 50% 0%, rgba(46, 196, 182, 0.03) 0%, transparent 50%),
                              radial-gradient(ellipse at 80% 20%, rgba(212, 165, 64, 0.02) 0%, transparent 40%);
            color: var(--cream);
            font-family: 'Inter', sans-serif;
        }

        .font-serif { font-family: 'Playfair Display', serif; }

        /* Cards */
        .cat-card {
            background: var(--surface);
            border: 1px solid rgba(212, 165, 64, 0.06);
            transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
        }
        .cat-card:hover {
            border-color: rgba(46, 196, 182, 0.18);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4), 0 0 30px rgba(46, 196, 182, 0.04);
            transform: translateY(-2px);
        }

        /* Accord bars */
        .accord-bar { transition: width 0.6s ease-out; }

        /* Gold gradient text */
        .text-gold-gradient {
            background: linear-gradient(135deg, var(--gold-light), var(--gold), var(--gold-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* WhatsApp button */
        .btn-whatsapp {
            background: #25d366;
            transition: all 0.25s;
        }
        .btn-whatsapp:hover {
            background: #22c55e;
            box-shadow: 0 6px 24px rgba(37, 211, 102, 0.25);
            transform: translateY(-1px);
        }

        /* Scrollbar */
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }

        /* Pill active */
        .pill-active {
            background: var(--gold);
            color: var(--bg);
            box-shadow: 0 2px 12px rgba(212, 165, 64, 0.2);
        }
        .pill-inactive {
            background: rgba(255, 255, 255, 0.03);
            color: var(--muted);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .pill-inactive:hover {
            background: rgba(46, 196, 182, 0.06);
            color: var(--cream);
            border-color: rgba(46, 196, 182, 0.12);
        }

        /* Teal accent utilities */
        .text-teal { color: var(--teal); }
        .text-teal-light { color: var(--teal-light); }
        .bg-teal-glow { background: var(--teal-glow); }
        .teal-accent { color: var(--teal); }

        /* Info cards glow */
        .info-card {
            background: var(--surface);
            border: 1px solid rgba(212, 165, 64, 0.05);
            transition: border-color 0.3s;
        }
        .info-card:hover {
            border-color: rgba(212, 165, 64, 0.12);
        }

        /* Perfume classes (keep for show page) */
        .perfume-card { background: var(--surface); border: 1px solid rgba(212,165,64,0.06); }
        .perfume-card:hover { border-color: rgba(212,165,64,0.12); }
        .perfume-section-title { color: var(--gold); font-weight: 700; }
        .perfume-text-cream { color: var(--cream); }
        .perfume-text-muted { color: var(--muted); }
        .perfume-text-gold { color: var(--gold); }
        .perfume-badge-masc { background: rgba(46,196,182,0.10); color: var(--teal-light); border: 1px solid rgba(46,196,182,0.15); }
        .perfume-badge-fem { background: rgba(212,165,64,0.10); color: var(--gold-light); border: 1px solid rgba(212,165,64,0.15); }
        .perfume-badge-uni { background: rgba(147,51,234,0.10); color: #c084fc; border: 1px solid rgba(147,51,234,0.15); }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer minimal --}}
    <footer class="border-t border-white/5 mt-16">
        <div class="max-w-6xl mx-auto px-5 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-center sm:text-left">
                <img src="{{ asset('images/logo-dg-imports.png') }}" alt="DG Imports" class="h-10 w-auto">
                <p class="text-[11px] mt-1" style="color: var(--muted);">Perfumes importados com os melhores preços</p>
            </div>
            <p class="text-[11px]" style="color: var(--muted);">&copy; {{ date('Y') }} DG Imports</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
