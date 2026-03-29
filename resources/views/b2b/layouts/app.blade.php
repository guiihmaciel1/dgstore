<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Domain\B2B\Models\B2BSetting::getCompanyName() }} - {{ config('app.name') }}</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 814 1000'><path d='M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76.5 0-103.7 40.8-165.9 40.8-62.2 0-106.9-56.3-155.5-130.8-56.5-86.2-102.4-219.8-102.4-347.5 0-204.1 132.8-312.5 263.9-312.5 69.5 0 127.4 45.6 170.9 45.6 41.5 0 106.2-48.4 184.8-48.4 29.8 0 137 2.6 207.9 97.3zm-290.7-80.3c31.2-36.9 53.3-88.2 53.3-139.5 0-7.1-.6-14.3-1.9-20.1-50.8 1.9-110.5 33.8-146.7 75.8-26.4 29.8-54.6 80.3-54.6 132.4 0 7.8.6 15.7 1.3 18.2 2.6.6 6.4 1.3 10.8 1.3 45.3 0 102.5-30.4 137.8-68.1z'/></svg>">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50/50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            @include('b2b.layouts.navigation')

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

        @include('b2b.layouts._toast')
    </body>
</html>
