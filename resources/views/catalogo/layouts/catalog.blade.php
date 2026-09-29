<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
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
            --gold-light: #c49a30;
            --gold-dark: #b08830;
            --cream: #1a1a1a;
            --bg: #ffffff;
            --surface: #f7f7f7;
            --surface-elevated: #f0f0f0;
            --muted: #888888;
            --teal: #0ea58a;
            --teal-light: #10b981;
            --teal-glow: rgba(14, 165, 138, 0.06);
            --gold-glow: rgba(212, 165, 64, 0.06);
        }

        body {
            background: var(--bg);
            color: var(--cream);
            font-family: 'Inter', -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .font-serif { font-family: 'Playfair Display', Georgia, serif; }

        /* ─── Product cards ─── */
        .cat-card {
            background: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 1.25rem;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cat-card:hover, .cat-card:active {
            border-color: #ccc;
            transform: translateY(-3px);
        }
        @media (max-width: 639px) {
            .cat-card:active { transform: scale(0.98); }
            .cat-card:hover { transform: none; }
        }

        .cat-card-img {
            background: #ffffff;
            position: relative;
        }
        .cat-card-img::after {
            display: none;
        }

        /* ─── Accord bars ─── */
        .accord-bar { transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1); }

        /* ─── Gold gradient text ─── */
        .text-gold-gradient {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
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
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 12px rgba(212, 165, 64, 0.25);
        }
        .pill-inactive {
            background: #f5f5f5;
            color: #666;
            border: 1px solid #e0e0e0;
        }
        .pill-inactive:hover {
            background: #eee;
            color: #333;
            border-color: #ccc;
        }

        /* ─── Glass surface ─── */
        .glass {
            background: var(--surface);
            border: 1px solid #eee;
        }

        /* ─── Info cards ─── */
        .info-card {
            background: #ffffff;
            border: none;
            border-radius: 1.25rem;
            transition: border-color 0.3s;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
        }
        .info-card:hover {
            border-color: #ddd;
        }

        /* ─── PIX badge ─── */
        .pix-badge {
            background: rgba(14, 165, 138, 0.06);
            border: 1px solid rgba(14, 165, 138, 0.15);
        }

        /* ─── Discount pin (corner badge on product images) ─── */
        .discount-pin {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            min-width: 46px;
            min-height: 46px;
            padding: 6px 4px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00c9a7 0%, #0ea58a 100%);
            box-shadow: 0 4px 14px rgba(0, 201, 167, 0.35), 0 0 0 2.5px rgba(0, 201, 167, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            line-height: 1;
            animation: pinPulse 2.5s ease-in-out infinite;
        }
        @keyframes pinPulse {
            0%, 100% { box-shadow: 0 4px 14px rgba(0, 201, 167, 0.35), 0 0 0 2.5px rgba(0, 201, 167, 0.15); }
            50% { box-shadow: 0 4px 20px rgba(0, 201, 167, 0.45), 0 0 0 4px rgba(0, 201, 167, 0.08); }
        }
        .discount-pin-value {
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .discount-pin-label {
            font-size: 7px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 1px;
        }
        .discount-pin-sm {
            min-width: 40px;
            min-height: 40px;
            padding: 5px 3px;
            top: 8px;
            right: 8px;
        }
        .discount-pin-sm .discount-pin-value {
            font-size: 11px;
        }
        .discount-pin-sm .discount-pin-label {
            font-size: 6px;
        }
        .discount-pin-lg {
            min-width: 60px;
            min-height: 60px;
            padding: 8px 5px;
        }
        .discount-pin-lg .discount-pin-value {
            font-size: 18px;
        }
        .discount-pin-lg .discount-pin-label {
            font-size: 8px;
        }

        /* ─── Search ─── */
        .search-input {
            background: #ffffff;
            border: 1px solid #ddd;
            transition: all 0.3s;
            color: #1a1a1a;
        }
        .search-input::placeholder {
            color: #aaa;
        }
        .search-input:focus {
            background: #fff;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 165, 64, 0.10);
            outline: none;
        }

        /* ─── Decorative gold separator ─── */
        .gold-line {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 165, 64, 0.3) 50%, transparent 100%);
        }

        /* ─── Perfume badges ─── */
        .perfume-card { background: #fff; border: 1px solid #eee; }
        .perfume-card:hover { border-color: #ddd; }
        .perfume-section-title { color: var(--gold); font-weight: 700; }
        .perfume-text-cream { color: var(--cream); }
        .perfume-text-muted { color: var(--muted); }
        .perfume-text-gold { color: var(--gold); }
        .perfume-badge-masc { background: rgba(14,165,138,0.08); color: var(--teal); border: 1px solid rgba(14,165,138,0.15); }
        .perfume-badge-fem { background: rgba(212,165,64,0.08); color: var(--gold); border: 1px solid rgba(212,165,64,0.15); }
        .perfume-badge-uni { background: rgba(147,51,234,0.08); color: #9333ea; border: 1px solid rgba(147,51,234,0.15); }

        /* ─── Carousel arrows ─── */
        .carousel-arrow {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f0f0f0;
            border: 1px solid #ddd;
            color: #666;
            cursor: pointer;
            transition: all 0.2s;
        }
        .carousel-arrow:hover {
            background: rgba(212, 165, 64, 0.10);
            border-color: rgba(212, 165, 64, 0.3);
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

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-20" style="background: var(--surface);">
        <div class="gold-line"></div>
        <div class="max-w-6xl mx-auto px-5 py-10 flex flex-col items-center gap-4">
            <img src="{{ asset('images/logo-dg-imports.png') }}" alt="DG Imports" class="h-12 w-auto opacity-70">
            <p class="text-[11px] tracking-wider uppercase" style="color: var(--muted);">Perfumes importados com os melhores preços</p>
            <p class="text-[10px]" style="color: var(--muted); opacity: 0.5;">&copy; {{ date('Y') }} DG Imports — Todos os direitos reservados</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
