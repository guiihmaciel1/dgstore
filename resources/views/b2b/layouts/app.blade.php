<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Domain\B2B\Models\B2BSetting::getCompanyName() }} - {{ config('app.name') }}</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 170 170'><path d='M150.4 130.3c-2.4 5.5-5.2 10.6-8.4 15.2-4.4 6.3-8 10.7-10.8 13.1-4.3 4-8.9 6-13.9 6.1-3.6 0-7.9-1-13-3.1-5.1-2-9.8-3.1-14.1-3.1-4.5 0-9.4 1-14.5 3.1-5.2 2.1-9.3 3.1-12.5 3.2-4.8.2-9.5-1.9-14.2-6.2-3-2.6-6.7-7.1-11.2-13.5-4.8-6.9-8.8-14.8-11.8-23.9-3.2-9.8-4.9-19.3-4.9-28.5 0-10.5 2.3-19.6 6.8-27.2 3.5-6.1 8.2-10.9 14.1-14.5 5.9-3.5 12.2-5.3 19.1-5.4 3.8 0 8.8 1.2 15.1 3.5 6.2 2.4 10.2 3.5 12 3.5 1.3 0 5.7-1.3 13.2-4 7.1-2.5 13-3.5 17.9-3.1 13.2 1.1 23.1 6.3 29.7 15.7-11.8 7.2-17.7 17.2-17.5 30.1.1 10 3.8 18.4 10.8 25 3.2 3 6.8 5.4 10.8 7-.9 2.5-1.8 4.9-2.7 7.2zM119.1 7.3c0 7.9-2.9 15.2-8.6 21.9-6.9 8-15.2 12.7-24.2 11.9-.1-1-.2-2-.2-3.1 0-7.5 3.3-15.6 9.1-22.2 2.9-3.3 6.6-6.1 11.1-8.3 4.5-2.2 8.7-3.4 12.7-3.6.1 1.1.1 2.3.1 3.4z'/></svg>">
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
