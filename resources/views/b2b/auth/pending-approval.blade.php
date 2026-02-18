<x-b2b-guest-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Aguardando Aprovação</h1>
        <p class="text-gray-500 mt-1 text-sm">Seu cadastro está em análise</p>
    </x-slot>

    <div class="text-center py-4">
        <div class="mx-auto w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h2 class="text-lg font-bold text-gray-900 mb-2">Cadastro em Análise</h2>
        <p class="text-gray-600 text-sm leading-relaxed max-w-xs mx-auto">
            Seu cadastro foi recebido e está sendo analisado. Você receberá uma notificação assim que for aprovado.
        </p>

        <div class="mt-6 p-4 bg-gray-50 rounded-xl">
            <div class="flex items-center justify-center gap-3 text-sm text-gray-600">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    Cadastro enviado
                </span>
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Em análise
                </span>
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="flex items-center gap-1.5 text-gray-400">
                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                    Aprovado
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('b2b.logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sair e voltar depois
            </button>
        </form>
    </div>
</x-b2b-guest-layout>
