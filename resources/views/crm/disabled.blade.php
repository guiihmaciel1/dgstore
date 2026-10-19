<x-app-layout>
    <x-slot name="title">Pipeline desativado</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            <div style="max-width: 32rem; margin: 4rem auto; padding: 2.5rem 2rem; text-align: center; background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem;">
                <div style="width: 3.5rem; height: 3.5rem; margin: 0 auto 1.25rem; display: flex; align-items: center; justify-content: center; border-radius: 9999px; background: rgba(245,158,11,0.1);">
                    <svg style="width: 1.75rem; height: 1.75rem; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.5rem;">Pipeline desativado</h1>
                <p style="font-size: 0.875rem; color: #818181; line-height: 1.5; margin-bottom: 1.5rem;">
                    O pipeline de vendas está temporariamente desativado.
                    Nenhum lead ou cliente está sendo criado automaticamente a partir do WhatsApp.
                </p>

                <a href="{{ route('dashboard') }}"
                   style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 600; color: #e3e3e3; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; text-decoration: none;">
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
