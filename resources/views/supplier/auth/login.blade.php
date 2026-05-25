<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F5F5F7">

    <title>Entrar — Portal Fornecedor</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --apple-bg: #F5F5F7;
            --apple-text: #1D1D1F;
            --apple-text-secondary: #86868B;
            --apple-blue: #007AFF;
            --apple-separator: #E5E5EA;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        * {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background: var(--apple-bg);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: calc(1.5rem + var(--safe-top)) 1.25rem calc(1.5rem + var(--safe-bottom));
        }

        .login-card {
            width: 100%;
            max-width: 22rem;
            background: #fff;
            border-radius: 1.25rem;
            border: 0.5px solid var(--apple-separator);
            padding: 1.75rem 1.5rem;
        }

        @media (min-width: 640px) {
            .login-card { padding: 2rem; border-radius: 1.5rem; }
        }

        .login-input {
            width: 100%;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            background: var(--apple-bg);
            border: 0.5px solid var(--apple-separator);
            border-radius: 0.75rem;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            -webkit-appearance: none;
        }
        .login-input:focus {
            border-color: var(--apple-blue);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.12);
        }

        .login-btn {
            width: 100%;
            padding: 0.875rem;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: var(--apple-blue);
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: background 0.15s;
            -webkit-tap-highlight-color: transparent;
        }
        .login-btn:hover { background: #0051D5; }
        .login-btn:active { transform: scale(0.98); }
    </style>
</head>
<body class="antialiased">
    <div class="w-full max-w-sm text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold" style="color: var(--apple-text); letter-spacing: -0.04em;">AS</h1>
        <p class="mt-1.5 text-base" style="color: var(--apple-text-secondary);">Portal do Fornecedor</p>
    </div>

    <div class="login-card">
        <h2 class="text-xl font-semibold text-center mb-6" style="color: var(--apple-text); letter-spacing: -0.02em;">Entrar</h2>

        @if ($errors->any())
            <div class="mb-5 p-3 rounded-xl text-sm font-medium" style="background: rgba(255,59,48,0.1); color: #D70015;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('supplier.login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold mb-1.5" style="color: var(--apple-text);">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       autocomplete="email" inputmode="email"
                       class="login-input" placeholder="seu@email.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold mb-1.5" style="color: var(--apple-text);">Senha</label>
                <input id="password" name="password" type="password" required
                       autocomplete="current-password"
                       class="login-input" placeholder="••••••••">
            </div>

            <label class="flex items-center gap-2.5 py-1 cursor-pointer">
                <input id="remember" name="remember" type="checkbox"
                       class="w-4 h-4 rounded" style="accent-color: var(--apple-blue);">
                <span class="text-sm" style="color: var(--apple-text);">Lembrar-me</span>
            </label>

            <button type="submit" class="login-btn mt-2">Entrar</button>
        </form>
    </div>

    <p class="mt-8 text-xs text-center max-w-xs" style="color: var(--apple-text-secondary);">
        Problemas para acessar? Entre em contato com a DG Store.
    </p>
</body>
</html>
