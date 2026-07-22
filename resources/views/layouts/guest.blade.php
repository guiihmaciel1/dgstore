<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' . config('app.name', 'Laravel') : 'Login — ' . config('app.name', 'Laravel') }}</title>
        
        <link rel="icon" type="image/png" href="{{ asset('images/logodgnovo.png') }}?v={{ filemtime(public_path('images/logodgnovo.png')) }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }

            .login-page {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
                background: #050505;
            }

            /* Animated grid */
            .fx-grid {
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
                background-size: 60px 60px;
                mask-image: radial-gradient(ellipse at 50% 50%, black 20%, transparent 70%);
                -webkit-mask-image: radial-gradient(ellipse at 50% 50%, black 20%, transparent 70%);
                animation: grid-drift 20s linear infinite;
            }

            @keyframes grid-drift {
                0% { background-position: 0 0; }
                100% { background-position: 60px 60px; }
            }

            /* Glowing orbs */
            .fx-orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.4;
            }

            .fx-orb-1 {
                width: 500px; height: 500px;
                top: -15%; right: -10%;
                background: radial-gradient(circle, rgba(255,255,255,0.06), transparent 70%);
                animation: orb-float 12s ease-in-out infinite;
            }

            .fx-orb-2 {
                width: 400px; height: 400px;
                bottom: -10%; left: -8%;
                background: radial-gradient(circle, rgba(255,255,255,0.04), transparent 70%);
                animation: orb-float 15s ease-in-out infinite reverse;
            }

            .fx-orb-3 {
                width: 200px; height: 200px;
                top: 40%; left: 20%;
                background: radial-gradient(circle, rgba(255,255,255,0.03), transparent 70%);
                animation: orb-float 10s ease-in-out infinite 3s;
            }

            @keyframes orb-float {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(30px, -20px) scale(1.05); }
                66% { transform: translate(-20px, 15px) scale(0.95); }
            }

            /* Horizon line */
            .fx-horizon {
                position: absolute;
                top: 50%;
                left: 0; right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06) 20%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.06) 80%, transparent);
                z-index: 1;
            }

            /* Scan line effect */
            .fx-scan {
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, transparent 0%, rgba(255,255,255,0.01) 50%, transparent 100%);
                background-size: 100% 4px;
                pointer-events: none;
                z-index: 2;
                opacity: 0.4;
            }

            /* Particle dots */
            .fx-particles {
                position: absolute;
                inset: 0;
                z-index: 1;
            }

            .fx-dot {
                position: absolute;
                width: 2px; height: 2px;
                background: rgba(255,255,255,0.15);
                border-radius: 50%;
                animation: dot-pulse 4s ease-in-out infinite;
            }

            .fx-dot:nth-child(1) { top: 15%; left: 25%; animation-delay: 0s; }
            .fx-dot:nth-child(2) { top: 30%; right: 20%; animation-delay: 0.8s; }
            .fx-dot:nth-child(3) { bottom: 25%; left: 15%; animation-delay: 1.6s; }
            .fx-dot:nth-child(4) { top: 60%; right: 30%; animation-delay: 2.4s; }
            .fx-dot:nth-child(5) { bottom: 35%; right: 12%; animation-delay: 3.2s; }
            .fx-dot:nth-child(6) { top: 20%; left: 60%; animation-delay: 1.2s; }
            .fx-dot:nth-child(7) { bottom: 15%; left: 45%; animation-delay: 2s; }
            .fx-dot:nth-child(8) { top: 45%; left: 8%; animation-delay: 0.4s; }

            @keyframes dot-pulse {
                0%, 100% { opacity: 0; transform: scale(0.5); }
                50% { opacity: 1; transform: scale(1.5); }
            }

            .login-card {
                width: 100%;
                max-width: 400px;
                position: relative;
                z-index: 10;
                padding: 0 1.5rem;
            }

            .login-logo {
                height: 160px;
                width: auto;
                margin: 0 auto 2.5rem;
                display: block;
                filter: drop-shadow(0 0 40px rgba(255,255,255,0.08)) drop-shadow(0 4px 20px rgba(0,0,0,0.5));
                animation: logo-breathe 6s ease-in-out infinite;
            }

            @keyframes logo-breathe {
                0%, 100% { filter: drop-shadow(0 0 30px rgba(255,255,255,0.06)) drop-shadow(0 4px 20px rgba(0,0,0,0.5)); }
                50% { filter: drop-shadow(0 0 50px rgba(255,255,255,0.1)) drop-shadow(0 4px 20px rgba(0,0,0,0.5)); }
            }

            .login-form-card {
                background: rgba(14, 14, 14, 0.8);
                backdrop-filter: blur(40px) saturate(120%);
                -webkit-backdrop-filter: blur(40px) saturate(120%);
                border: 1px solid rgba(255,255,255,0.07);
                border-radius: 1.5rem;
                padding: 2rem;
                box-shadow:
                    0 30px 100px rgba(0,0,0,0.6),
                    inset 0 1px 0 rgba(255,255,255,0.04);
                position: relative;
                overflow: hidden;
            }

            .login-form-card::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1) 30%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.1) 70%, transparent);
            }

            .login-input {
                width: 100%;
                padding: 0.875rem 1rem 0.875rem 2.75rem;
                background: rgba(255,255,255,0.03);
                border: 1px solid rgba(255,255,255,0.07);
                border-radius: 0.75rem;
                font-size: 0.875rem;
                color: #e3e3e3;
                outline: none;
                transition: all 0.3s;
                box-sizing: border-box;
            }

            .login-input:focus {
                border-color: rgba(255,255,255,0.25);
                box-shadow: 0 0 0 3px rgba(255,255,255,0.04), 0 0 20px rgba(255,255,255,0.03);
                background: rgba(255,255,255,0.06);
            }

            .login-input::placeholder {
                color: #3a3a3a;
            }

            .login-btn {
                width: 100%;
                padding: 0.875rem;
                background: #ffffff;
                color: #0a0a0a;
                font-weight: 700;
                font-size: 0.875rem;
                border: none;
                border-radius: 0.75rem;
                cursor: pointer;
                transition: all 0.3s;
                letter-spacing: 0.02em;
                position: relative;
                overflow: hidden;
            }

            .login-btn::after {
                content: '';
                position: absolute;
                top: -50%; left: -50%;
                width: 200%; height: 200%;
                background: linear-gradient(45deg, transparent 40%, rgba(255,255,255,0.3) 50%, transparent 60%);
                transition: transform 0.5s;
                transform: translateX(-100%);
            }

            .login-btn:hover::after {
                transform: translateX(100%);
            }

            .login-btn:hover {
                box-shadow: 0 8px 40px rgba(255,255,255,0.12);
                transform: translateY(-1px);
            }

            .login-btn:active {
                transform: scale(0.98);
            }

            .login-b2b {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                width: 100%;
                padding: 0.75rem;
                background: rgba(255,255,255,0.03);
                color: #666666;
                font-weight: 600;
                font-size: 0.8rem;
                border: 1px solid rgba(255,255,255,0.05);
                border-radius: 0.75rem;
                text-decoration: none;
                transition: all 0.3s;
            }

            .login-b2b:hover {
                background: rgba(255,255,255,0.07);
                color: #e3e3e3;
                border-color: rgba(255,255,255,0.1);
            }

            @media (max-width: 480px) {
                .login-card { padding: 0 1rem; }
                .login-form-card { padding: 1.5rem; }
                .login-logo { height: 120px; margin-bottom: 2rem; }
                .fx-orb-1 { width: 300px; height: 300px; }
                .fx-orb-2 { width: 250px; height: 250px; }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="login-page">
            <!-- Futuristic Background Effects -->
            <div class="fx-grid"></div>
            <div class="fx-orb fx-orb-1"></div>
            <div class="fx-orb fx-orb-2"></div>
            <div class="fx-orb fx-orb-3"></div>
            <div class="fx-horizon"></div>
            <div class="fx-scan"></div>
            <div class="fx-particles">
                <div class="fx-dot"></div>
                <div class="fx-dot"></div>
                <div class="fx-dot"></div>
                <div class="fx-dot"></div>
                <div class="fx-dot"></div>
                <div class="fx-dot"></div>
                <div class="fx-dot"></div>
                <div class="fx-dot"></div>
            </div>

            <!-- Login Card -->
            <div class="login-card">
                <img src="{{ asset('images/logodgnovo.png') }}?v={{ filemtime(public_path('images/logodgnovo.png')) }}" 
                     alt="DG Store" 
                     class="login-logo" />

                <div class="login-form-card">
                    {{ $slot }}
                </div>

                <p style="text-align: center; font-size: 0.65rem; color: #333333; margin-top: 1.5rem;">
                    &copy; {{ date('Y') }} DG Store
                </p>
            </div>
        </div>
    </body>
</html>
