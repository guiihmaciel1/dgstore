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
            .bg-dg-gradient {
                background: linear-gradient(180deg, #f5f5f5 0%, #d4d4d4 100%);
                min-height: 100vh;
            }
            .login-card {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.95);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center py-8 px-4 bg-dg-gradient">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="block">
                    <img src="{{ asset('images/logodg.png') }}" alt="DG Store" class="h-64 w-auto mx-auto" />
                </a>
            </div>

            <!-- Card de Login -->
            <div class="w-full max-w-sm px-8 py-8 login-card shadow-2xl rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>
            
            <!-- Rodapé -->
            <p class="mt-8 text-sm text-gray-500">
                &copy; {{ date('Y') }} DG Store - Apple Store
            </p>
        </div>
    </body>
</html>
