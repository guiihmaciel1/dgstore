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

        /* ─── Discount tag (só aparece com original_price real) ─── */
        .discount-pin {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 10;
            display: inline-flex;
            align-items: baseline;
            gap: 3px;
            padding: 4px 8px;
            border-radius: 6px;
            background: #1a1a1a;
            color: #fff;
            line-height: 1;
        }
        .discount-pin-value { font-size: 12px; font-weight: 700; font-variant-numeric: tabular-nums; }
        .discount-pin-label { font-size: 10px; font-weight: 600; letter-spacing: 0.04em; }
        .discount-pin-sm { top: 10px; left: 10px; padding: 3px 7px; }
        .discount-pin-sm .discount-pin-value { font-size: 11px; }
        .discount-pin-sm .discount-pin-label { font-size: 9px; }
        .discount-pin-lg { top: 16px; left: 16px; padding: 6px 10px; }
        .discount-pin-lg .discount-pin-value { font-size: 15px; }
        .discount-pin-lg .discount-pin-label { font-size: 11px; }

        /* ─── Card price block ─── */
        .card-price {
            margin-top: auto;
            padding-top: 14px;
            font-variant-numeric: tabular-nums;
        }
        .card-price-from { font-size: 12px; color: #737373; text-decoration: line-through; }
        .card-price-main { display: flex; align-items: baseline; gap: 6px; flex-wrap: wrap; }
        .card-price-value { font-size: 22px; font-weight: 700; color: #1a1a1a; letter-spacing: -0.01em; }
        .card-price-pix { font-size: 12px; font-weight: 600; color: #0b7f6a; }
        .card-price-installments { margin-top: 2px; font-size: 12px; color: #6b6b6b; }
        .card-stock { margin-top: 10px; font-size: 12px; font-weight: 500; }
        .card-stock-ready { color: #0b7f6a; }
        .card-stock-order { color: #8a6a1f; }
        .card-price-sm .card-price-value { font-size: 18px; }
        .card-price-sm .card-price-from,
        .card-price-sm .card-price-installments,
        .card-price-sm .card-stock { font-size: 11px; }

        /* ─── Header ─── */
        .cat-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: saturate(180%) blur(12px);
            -webkit-backdrop-filter: saturate(180%) blur(12px);
            border-bottom: 1px solid #ececec;
        }
        .btn-header-wa {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            border: 1px solid #d9d9d9;
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            transition: border-color 0.2s, background 0.2s;
        }
        .btn-header-wa:hover { border-color: #1a1a1a; background: #fafafa; }
        .btn-header-wa svg { color: #128c7e; }

        /* ─── Search ─── */
        .search-input {
            background: #ffffff;
            border: 1px solid #ddd;
            transition: all 0.3s;
            color: #1a1a1a;
        }
        .search-input::-webkit-search-cancel-button { display: none; }
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

    {{-- Header --}}
    <header class="cat-header">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('catalogo.index') }}" class="flex items-center gap-3 shrink-0" aria-label="DG Imports — início do catálogo">
                <img src="{{ asset('images/logo-dg-imports.png') }}" alt="DG Imports" class="h-14 w-auto">
            </a>
            @if(!empty($whatsappNumber))
                <a href="https://wa.me/55{{ preg_replace('/\D/', '', $whatsappNumber) }}?text={{ urlencode('Olá! Vim pelo catálogo de perfumes.') }}"
                   target="_blank" rel="noopener" class="btn-header-wa">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span>WhatsApp</span>
                </a>
            @endif
        </div>
    </header>

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
