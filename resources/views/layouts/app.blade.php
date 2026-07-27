<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' . config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-surface text-dg-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-surface-raised border-b border-border">
                    <div class="py-6 px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <!-- Botões Flutuantes + Modais -->
        @auth
            <x-dollar-rate-modal />
            <x-import-calculator />
            @unless(request()->routeIs('products.index'))
                @include('products.partials.create-modal')
            @endunless
            @unless(request()->routeIs('stock.consignment.index'))
                @include('stock.consignment.partials.entry-modal')
            @endunless
        @endauth
        <x-stone-fab-calculator />

        @stack('scripts')

        {{-- Chart.js dark theme defaults --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Chart !== 'undefined') {
                    Chart.defaults.color = '#a4a4a4';
                    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
                    Chart.defaults.backgroundColor = 'transparent';
                    if (Chart.defaults.plugins && Chart.defaults.plugins.legend) {
                        Chart.defaults.plugins.legend.labels.color = '#a4a4a4';
                    }
                }
            });
        </script>

        @auth
        <script>
            setInterval(() => fetch('{{ route("keepalive") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(() => {}), 300000);
        </script>
        @endauth
    </body>
</html>
