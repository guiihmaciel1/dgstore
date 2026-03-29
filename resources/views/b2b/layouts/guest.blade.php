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
