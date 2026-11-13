<x-app-layout>
    <x-slot name="title">Pipeline desativado</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            <div class="max-w-[32rem] my-16 mx-auto py-10 px-8 text-center bg-surface-raised border border-border rounded-xl">
                <div class="w-14 h-14 mt-0 mx-auto mb-5 flex items-center justify-center rounded-full bg-amber-500/10">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-dg-100 mb-2">Pipeline desativado</h1>
                <p class="text-sm text-dg-400 leading-normal mb-6">
                    O pipeline de vendas está temporariamente desativado.
                    Nenhum lead ou cliente está sendo criado automaticamente a partir do WhatsApp.
                </p>

                <a class="inline-flex items-center py-2 px-4 text-[0.8rem] font-semibold text-dg-100 bg-surface-overlay border border-white/10 rounded-lg no-underline" href="{{ route('dashboard') }}">
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
