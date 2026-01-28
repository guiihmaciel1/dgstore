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
                padding: 1.5rem;
                background-color: #f9fafb;
            }
            /* Tablet */
            @media (max-width: 1024px) {
                .split-layout {
                    flex-direction: column;
                }
                .split-left {
                    width: 100%;
                    min-height: 30vh;
                    padding: 2rem 1.5rem;
                }
                .split-right {
                    width: 100%;
                    min-height: 70vh;
                    padding: 2rem 1.5rem;
                }
                .split-left img {
                    height: 180px !important;
                }
            }
            /* Mobile */
            @media (max-width: 640px) {
                .split-left {
                    min-height: 25vh;
                    padding: 1.5rem 1rem;
                }
                .split-right {
                    min-height: 75vh;
                    padding: 1.5rem 1rem;
                }
                .split-left img {
                    height: 120px !important;
                }
                .split-left p {
                    font-size: 0.75rem !important;
                    margin-top: 1rem !important;
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
                <div class="w-full max-w-md px-2 sm:px-0">
                    <!-- Título -->
                    <div class="mb-6 sm:mb-8">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Bem-vindo de volta</h1>
                        <p class="text-gray-500 mt-2 text-sm sm:text-base">Acesse sua conta para continuar</p>
                    </div>

                    <!-- Formulário -->
                    <div class="bg-white p-5 sm:p-8 rounded-xl shadow-lg border border-gray-100">
                        {{ $slot }}
                    </div>
                    
                    <!-- Rodapé -->
                    <p class="mt-6 sm:mt-8 text-center text-xs sm:text-sm text-gray-400">
                        &copy; {{ date('Y') }} DG Store - Todos os direitos reservados
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
