<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>B2B - {{ config('app.name') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col">
            @include('b2b.layouts.navigation')

            @if (isset($header))
                <header class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="flex-1 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>

            <footer class="border-t border-gray-200 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <p class="text-center text-xs text-gray-400">&copy; {{ date('Y') }} DG Store - Distribuidora Apple B2B</p>
                </div>
            </footer>
        </div>

        <!-- Toast Notifications -->
        <div x-data="toast()" x-init="init()" class="fixed top-20 right-4 z-[60] space-y-2 w-80">
            <template x-if="show">
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-8"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-8"
                     class="rounded-lg shadow-lg p-4 flex items-start gap-3"
                     :class="type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
                    <template x-if="type === 'success'">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="type === 'error'">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                    <p class="text-sm font-medium flex-1" x-text="message"></p>
                    <button @click="show = false" class="shrink-0 opacity-70 hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        <script>
            function toast() {
                return {
                    show: false,
                    message: '',
                    type: 'success',
                    init() {
                        @if(session('success'))
                            this.fire('{{ session('success') }}', 'success');
                        @endif
                        @if(session('error'))
                            this.fire('{{ session('error') }}', 'error');
                        @endif
                    },
                    fire(msg, t) {
                        this.message = msg;
                        this.type = t;
                        this.show = true;
                        setTimeout(() => { this.show = false; }, 4000);
                    }
                }
            }
        </script>
    </body>
</html>
