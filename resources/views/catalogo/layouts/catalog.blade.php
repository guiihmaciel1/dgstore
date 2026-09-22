<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DG Imports — Catálogo')</title>
    <meta name="description" content="@yield('description', 'Catálogo de perfumes importados — DG Imports')">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <meta property="og:title" content="@yield('title', 'DG Imports — Catálogo')">
    <meta property="og:description" content="@yield('description', 'Catálogo de perfumes importados — DG Imports')">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&family=playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --gold: #c9a96e;
            --gold-light: #dcc189;
            --gold-dark: #a88b4a;
            --cream: #f5f0e8;
            --bg: #080808;
            --surface: #111111;
            --surface-elevated: #181818;
            --muted: #6b6560;
        }

        body {
            background: var(--bg);
            color: var(--cream);
            font-family: 'Inter', sans-serif;
        }

        .font-serif { font-family: 'Playfair Display', serif; }

        /* Cards */
        .cat-card {
            background: var(--surface);
            border: 1px solid rgba(201, 169, 110, 0.06);
            transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
        }
        .cat-card:hover {
            border-color: rgba(201, 169, 110, 0.15);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
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
            color: #080808;
        }
        .pill-inactive {
            background: rgba(255, 255, 255, 0.04);
            color: var(--muted);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .pill-inactive:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--cream);
        }

        /* Perfume classes (keep for show page) */
        .perfume-card { background: var(--surface); border: 1px solid rgba(201,169,110,0.06); }
        .perfume-card:hover { border-color: rgba(201,169,110,0.12); }
        .perfume-section-title { color: var(--gold); font-weight: 700; }
        .perfume-text-cream { color: var(--cream); }
        .perfume-text-muted { color: var(--muted); }
        .perfume-text-gold { color: var(--gold); }
        .perfume-badge-masc { background: rgba(59,130,246,0.10); color: #93bbfd; border: 1px solid rgba(59,130,246,0.15); }
        .perfume-badge-fem { background: rgba(201,169,110,0.10); color: var(--gold-light); border: 1px solid rgba(201,169,110,0.15); }
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
                <span class="font-serif text-sm font-semibold text-gold-gradient">DG Imports</span>
                <p class="text-[11px] mt-1" style="color: var(--muted);">Perfumes importados com os melhores preços</p>
            </div>
            <p class="text-[11px]" style="color: var(--muted);">&copy; {{ date('Y') }} DG Imports</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
