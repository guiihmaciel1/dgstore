<x-b2b-guest-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Aguardando Aprovacao</h1>
        <p class="text-gray-400 mt-1.5 text-sm">Seu cadastro esta em analise</p>
    </x-slot>

    <div class="text-center py-2">
        <div class="mx-auto w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h2 class="text-lg font-semibold text-gray-900 mb-2">Cadastro em Analise</h2>
        <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto">
            Seu cadastro foi recebido e esta sendo analisado. Voce recebera uma notificacao assim que for aprovado.
        </p>

        {{-- Timeline steps --}}
        <div class="mt-8 flex items-center justify-center gap-0">
            {{-- Step 1: Done --}}
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-xs font-medium text-green-600 mt-2">Enviado</span>
            </div>

            <div class="w-8 sm:w-12 h-0.5 bg-green-200 mt-[-16px]"></div>

            {{-- Step 2: In progress --}}
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center ring-4 ring-amber-50">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                </div>
                <span class="text-xs font-medium text-amber-600 mt-2">Em analise</span>
            </div>

            <div class="w-8 sm:w-12 h-0.5 bg-gray-100 mt-[-16px]"></div>

            {{-- Step 3: Pending --}}
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-400 mt-2">Aprovado</span>
            </div>
        </div>

        <form method="POST" action="{{ route('b2b.logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="apple-btn-secondary py-2.5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                Sair e voltar depois
            </button>
        </form>
    </div>
</x-b2b-guest-layout>
