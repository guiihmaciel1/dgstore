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
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen flex flex-col lg:flex-row">
            {{-- Left panel - Branding (hidden on mobile, shown as top bar) --}}
            <div class="lg:w-1/2 lg:fixed lg:inset-y-0 lg:left-0 relative overflow-hidden bg-gray-950">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-950/40 via-gray-950 to-gray-950"></div>
                <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500/8 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-500/5 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4"></div>

                {{-- Mobile: compact bar --}}
                <div class="lg:hidden relative z-10 px-6 py-8 text-center">
                    <a href="{{ route('b2b.login') }}">
                        <x-apple-logo class="h-12 w-auto mx-auto text-white" />
                    </a>
                    <p class="text-sm font-bold text-blue-400 tracking-widest uppercase mt-3">B2B</p>
                </div>

                {{-- Desktop: full branding --}}
                <div class="hidden lg:flex relative z-10 flex-col justify-between h-full p-12">
                    <div>
                        <a href="{{ route('b2b.login') }}">
                            <x-apple-logo class="h-10 w-auto text-white" />
                        </a>
                    </div>
                    <div class="flex-1 flex flex-col justify-center max-w-md">
                        <h2 class="text-4xl font-bold text-white leading-tight tracking-tight">
                            Apple <span class="text-blue-400">B2B</span>
                        </h2>
                        <p class="text-gray-400 mt-4 text-lg leading-relaxed">
                            Plataforma exclusiva de atacado para lojistas. Melhores precos, estoque direto.
                        </p>
                        <div class="mt-10 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-sm text-gray-300">Precos exclusivos de atacado</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <span class="text-sm text-gray-300">Pedidos rapidos via PIX</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="text-sm text-gray-300">Estoque garantido e rastreavel</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">&copy; {{ date('Y') }} {{ \App\Domain\B2B\Models\B2BSetting::getCompanyName() }}</p>
                    </div>
                </div>
            </div>

            {{-- Right panel - Form --}}
            <div class="flex-1 lg:ml-[50%] flex flex-col justify-center items-center px-4 sm:px-6 py-8 sm:py-12 bg-gray-50/50 min-h-[70vh] lg:min-h-screen">
                <div class="w-full max-w-sm sm:max-w-md">
                    <div class="mb-8">
                        {{ $header ?? '' }}
                    </div>

                    <div class="apple-card p-6 sm:p-8">
                        {{ $slot }}
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                            Voltar ao login principal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
