<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Distribuidora Apple B2B - {{ config('app.name') }}</title>

        <link rel="icon" type="image-png" href="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen">
            <!-- Painel esquerdo -->
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gray-900 flex-col justify-between p-12">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 via-gray-900 to-gray-950"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative z-10">
                    <a href="{{ route('b2b.login') }}">
                        <img src="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}" alt="DG Store" class="h-12 w-auto brightness-0 invert" />
                    </a>
                </div>
                <div class="relative z-10 flex-1 flex flex-col justify-center">
                    <h2 class="text-4xl font-bold text-white leading-tight">
                        Distribuidora<br>Apple <span class="text-blue-400">B2B</span>
                    </h2>
                    <p class="text-gray-400 mt-4 text-lg max-w-sm">
                        Plataforma exclusiva de atacado para lojistas. Melhores preços, estoque direto.
                    </p>
                    <div class="mt-8 flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm text-gray-300">Preços exclusivos</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm text-gray-300">Pedido rápido</span>
                        </div>
                    </div>
                </div>
                <div class="relative z-10">
                    <p class="text-xs text-gray-600">&copy; {{ date('Y') }} DG Store</p>
                </div>
            </div>

            <!-- Painel direito -->
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-gray-50">
                <!-- Logo mobile -->
                <div class="lg:hidden mb-8 text-center">
                    <a href="{{ route('b2b.login') }}">
                        <img src="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}" alt="DG Store" class="h-16 w-auto mx-auto" />
                    </a>
                    <p class="text-sm font-bold text-blue-600 tracking-widest uppercase mt-2">Distribuidora B2B</p>
                </div>

                <div class="w-full max-w-md">
                    <div class="mb-8">
                        {{ $header ?? '' }}
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-200">
                        {{ $slot }}
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Voltar ao login principal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
