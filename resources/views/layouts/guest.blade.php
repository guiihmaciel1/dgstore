<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logodg.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .bg-logo-gradient {
                background: linear-gradient(180deg, #ffffff 0%, #a0a0a0 50%, #1a1a1a 100%);
            }
            .split-layout {
                display: flex;
                min-height: 100vh;
            }
            .split-left {
                width: 50%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 2rem;
            }
            .split-right {
                width: 50%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 2rem;
                background-color: #f9fafb;
            }
            @media (max-width: 1024px) {
                .split-layout {
                    flex-direction: column;
                }
                .split-left, .split-right {
                    width: 100%;
                    min-height: 50vh;
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="split-layout">
            <!-- Lado Esquerdo - Logo com Degradê -->
            <div class="split-left bg-logo-gradient">
                <a href="/" class="block">
                    <img src="{{ asset('images/logodg.png') }}" alt="DG Store" style="height: 320px; width: auto;" />
                </a>
                <p class="mt-8 text-white text-sm tracking-widest uppercase">
                    Tecnologia & Lifestyle
                </p>
            </div>

            <!-- Lado Direito - Formulário -->
            <div class="split-right">
                <div style="width: 100%; max-width: 420px;">
                    <!-- Título -->
                    <div class="mb-8">
                        <h1 style="font-size: 1.75rem; font-weight: 700; color: #111827;">Bem-vindo de volta</h1>
                        <p style="color: #6b7280; margin-top: 0.5rem;">Acesse sua conta para continuar</p>
                    </div>

                    <!-- Formulário -->
                    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border: 1px solid #f3f4f6;">
                        {{ $slot }}
                    </div>
                    
                    <!-- Rodapé -->
                    <p style="margin-top: 2rem; text-align: center; font-size: 0.875rem; color: #9ca3af;">
                        &copy; {{ date('Y') }} DG Store - Todos os direitos reservados
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
