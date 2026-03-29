<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Domain\B2B\Models\B2BSetting::getCompanyName() }} Admin - {{ config('app.name') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50/50 text-gray-900">
        <div class="min-h-screen flex">
            @include('b2b.layouts.admin-sidebar')

            <div class="flex-1 flex flex-col lg:ml-64">
                @include('b2b.layouts.admin-topbar')

                @if (isset($header))
                    <header class="bg-white/60 backdrop-blur-md border-b border-gray-200/40">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1 py-6 sm:py-8">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>

                <footer class="border-t border-gray-200/60 bg-white/40 backdrop-blur-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <p class="text-center text-xs text-gray-400">&copy; {{ date('Y') }} {{ \App\Domain\B2B\Models\B2BSetting::getCompanyName() }}</p>
                    </div>
                </footer>
            </div>
        </div>

        @include('b2b.layouts._toast')
    </body>
</html>
