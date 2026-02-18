<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Perfumes Admin - {{ config('app.name') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex">
            @include('perfumes.layouts.admin-sidebar')

            <div class="flex-1 flex flex-col lg:ml-64">
                @include('perfumes.layouts.admin-topbar')

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
                        <p class="text-center text-xs text-gray-400">&copy; {{ date('Y') }} DG Store - Painel Perfumes</p>
                    </div>
                </footer>
            </div>
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

            @if(session('whatsapp_link'))
                <div x-data="{ showWa: true }"
                     x-show="showWa"
                     x-init="setTimeout(() => showWa = false, 15000)"
                     x-transition
                     class="rounded-lg shadow-lg bg-white border border-gray-200 p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">Notificar {{ session('whatsapp_retailer') }}?</p>
                            <p class="text-xs text-gray-500 mt-0.5">Envie a atualização via WhatsApp</p>
                            <a href="{{ session('whatsapp_link') }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition">
                                Enviar WhatsApp
                            </a>
                        </div>
                        <button @click="showWa = false" class="text-gray-400 hover:text-gray-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif
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
