{{-- BANNER: NOVO TREINAMENTO --}}
<a href="{{ route('tools.sales-training') }}" style="display: block; text-decoration: none; margin-bottom: 1rem;">
    <div style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 50%, #6366f1 100%); border-radius: 0.75rem; padding: 1rem 1.25rem; position: relative; overflow: hidden; transition: box-shadow 0.2s;"
         onmouseover="this.style.boxShadow='0 8px 25px rgba(79,70,229,0.3)'" onmouseout="this.style.boxShadow='none'">
        <div style="position: absolute; top: -30%; right: -8%; width: 160px; height: 160px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -40%; left: 10%; width: 120px; height: 120px; background: rgba(255,255,255,0.04); border-radius: 50%;"></div>

        <div style="position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.25rem; height: 2.25rem; background: rgba(255,255,255,0.15); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="font-size: 1.25rem;">🎓</span>
                </div>
                <div>
                    <p style="font-size: 0.9375rem; font-weight: 800; color: white; margin: 0; line-height: 1.2;">Novo treinamento adicionado!</p>
                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin: 0.125rem 0 0 0;">Curso completo de fechamento de vendas — clique aqui para acessar</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <span style="background: #fbbf24; color: #78350f; font-size: 0.6875rem; font-weight: 800; padding: 0.25rem 0.625rem; border-radius: 9999px; white-space: nowrap; box-shadow: 0 2px 8px rgba(251,191,36,0.4);">NOVO</span>
                <div style="width: 1.75rem; height: 1.75rem; background: rgba(255,255,255,0.15); border-radius: 0.375rem; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1rem; height: 1rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</a>
