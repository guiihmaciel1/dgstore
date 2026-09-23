<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0f18">
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
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&family=playfair-display:400,500,600,700,700i&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --gold: #d4a540;
            --gold-light: #e8c060;
            --gold-dark: #b08830;
            --cream: #f0ece4;
            --bg: #0a0f18;
            --surface: #0f1520;
            --surface-elevated: #151d2c;
            --muted: #6b7a90;
            --teal: #2ec4b6;
            --teal-light: #5cdbd3;
            --teal-glow: rgba(46, 196, 182, 0.08);
            --gold-glow: rgba(212, 165, 64, 0.06);
        }

        body {
            background: var(--bg);
            color: var(--cream);
            font-family: 'Inter', -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Fixed background image ─── */
        .catalog-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: url('{{ asset("images/catalog-bg.jpg") }}') center / cover no-repeat fixed;
            filter: blur(3px) brightness(0.22);
            transform: scale(1.05);
        }

        .font-serif { font-family: 'Playfair Display', Georgia, serif; }

        /* ─── Product cards ─── */
        .cat-card {
            background: rgba(10, 15, 24, 0.55);
            backdrop-filter: blur(24px) saturate(1.3);
            -webkit-backdrop-filter: blur(24px) saturate(1.3);
            border: 1px solid rgba(212, 165, 64, 0.06);
            border-radius: 1.25rem;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cat-card:hover, .cat-card:active {
            background: rgba(10, 15, 24, 0.7);
            border-color: rgba(212, 165, 64, 0.18);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4),
                         0 0 48px rgba(212, 165, 64, 0.05),
                         inset 0 1px 0 rgba(255, 255, 255, 0.04);
            transform: translateY(-3px);
        }
        @media (max-width: 639px) {
            .cat-card:active { transform: scale(0.98); }
            .cat-card:hover { transform: none; }
        }

        .cat-card-img {
            background: rgba(255, 255, 255, 0.02);
            position: relative;
        }
        .cat-card-img::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 8%;
            right: 8%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212, 165, 64, 0.10), transparent);
        }

        /* ─── Accord bars ─── */
        .accord-bar { transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1); }

        /* ─── Gold gradient text ─── */
        .text-gold-gradient {
            background: linear-gradient(135deg, var(--gold-light), var(--gold), var(--gold-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ─── WhatsApp button ─── */
        .btn-whatsapp {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 16px rgba(37, 211, 102, 0.15);
        }
        .btn-whatsapp:hover {
            box-shadow: 0 8px 32px rgba(37, 211, 102, 0.25);
            transform: translateY(-1px);
        }

        /* ─── Scrollbar ─── */
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }

        /* ─── Filter pills ─── */
        .pill-active {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--bg);
            font-weight: 600;
            box-shadow: 0 2px 16px rgba(212, 165, 64, 0.25);
        }
        .pill-inactive {
            background: rgba(10, 15, 24, 0.45);
            color: var(--muted);
            border: 1px solid rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(16px) saturate(1.2);
            -webkit-backdrop-filter: blur(16px) saturate(1.2);
        }
        .pill-inactive:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--cream);
            border-color: rgba(212, 165, 64, 0.12);
        }

        /* ─── Glass surface ─── */
        .glass {
            background: rgba(15, 21, 32, 0.7);
            backdrop-filter: blur(20px) saturate(1.2);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        /* ─── Info cards ─── */
        .info-card {
            background: rgba(10, 15, 24, 0.55);
            backdrop-filter: blur(24px) saturate(1.3);
            -webkit-backdrop-filter: blur(24px) saturate(1.3);
            border: 1px solid rgba(212, 165, 64, 0.05);
            border-radius: 1.25rem;
            transition: border-color 0.3s;
        }
        .info-card:hover {
            border-color: rgba(212, 165, 64, 0.12);
        }

        /* ─── PIX badge ─── */
        .pix-badge {
            background: linear-gradient(135deg, rgba(46, 196, 182, 0.08) 0%, rgba(46, 196, 182, 0.03) 100%);
            border: 1px solid rgba(46, 196, 182, 0.12);
        }

        /* ─── Search ─── */
        .search-input {
            background: rgba(10, 15, 24, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px) saturate(1.2);
            -webkit-backdrop-filter: blur(20px) saturate(1.2);
            transition: all 0.3s;
        }
        .search-input:focus {
            background: rgba(10, 15, 24, 0.7);
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 165, 64, 0.08), 0 8px 32px rgba(0, 0, 0, 0.3);
            outline: none;
        }

        /* ─── Decorative gold separator ─── */
        .gold-line {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 165, 64, 0.2) 50%, transparent 100%);
        }

        /* ─── Perfume badges ─── */
        .perfume-card { background: var(--surface); border: 1px solid rgba(212,165,64,0.06); }
        .perfume-card:hover { border-color: rgba(212,165,64,0.12); }
        .perfume-section-title { color: var(--gold); font-weight: 700; }
        .perfume-text-cream { color: var(--cream); }
        .perfume-text-muted { color: var(--muted); }
        .perfume-text-gold { color: var(--gold); }
        .perfume-badge-masc { background: rgba(46,196,182,0.08); color: var(--teal-light); border: 1px solid rgba(46,196,182,0.12); }
        .perfume-badge-fem { background: rgba(212,165,64,0.08); color: var(--gold-light); border: 1px solid rgba(212,165,64,0.12); }
        .perfume-badge-uni { background: rgba(147,51,234,0.08); color: #c084fc; border: 1px solid rgba(147,51,234,0.12); }

        /* ─── Carousel arrows ─── */
        .carousel-arrow {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(10, 15, 24, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--muted);
            cursor: pointer;
            transition: all 0.2s;
            backdrop-filter: blur(12px);
        }
        .carousel-arrow:hover {
            background: rgba(212, 165, 64, 0.12);
            border-color: rgba(212, 165, 64, 0.25);
            color: var(--gold);
        }

        .carousel-track {
            cursor: grab;
            scroll-behavior: smooth;
        }
        .carousel-track:active {
            cursor: grabbing;
        }

        /* ─── Smooth appearance ─── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">
    <div class="catalog-bg" aria-hidden="true"></div>

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-20">
        <div class="gold-line"></div>
        <div class="max-w-6xl mx-auto px-5 py-10 flex flex-col items-center gap-4">
            <img src="{{ asset('images/logo-dg-imports.png') }}" alt="DG Imports" class="h-12 w-auto opacity-60">
            <p class="text-[11px] tracking-wider uppercase" style="color: var(--muted);">Perfumes importados com os melhores preços</p>
            <p class="text-[10px]" style="color: var(--muted); opacity: 0.5;">&copy; {{ date('Y') }} DG Imports — Todos os direitos reservados</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
