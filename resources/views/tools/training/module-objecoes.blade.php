{{-- Módulo: Objeções — Respostas que Viram o Jogo --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('objecoes')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🛡️</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Objeções — Respostas que Viram o Jogo</span>
                <div style="font-size: 0.75rem; color: #666666;">12 situações reais com scripts prontos</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('objecoes')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'objecoes' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'objecoes'" x-transition style="border-top: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">

        {{-- Intro --}}
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1.25rem; line-height: 1.6;">
            Objeção não é rejeição — é <strong>oportunidade</strong>. O cliente que objeta está interessado; ele só precisa de mais segurança pra decidir. Aprenda o método <strong>AER</strong>: Acolher, Entender, Redirecionar.
        </p>

        {{-- Método AER --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">🔄 Método AER</p>
        <div style="display: flex; flex-direction: column; gap: 0.625rem; margin-bottom: 1.25rem;">
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.375rem;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: rgba(16,185,129,0.15); color: #4ade80; font-size: 0.875rem; font-weight: 800;">A</span>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Acolher</span>
                </div>
                <p style="font-size: 0.8rem; color: #818181; line-height: 1.6;">"Entendo perfeitamente..." — valide a preocupação do cliente antes de qualquer argumento.</p>
            </div>
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.375rem;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 0.875rem; font-weight: 800;">E</span>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Entender</span>
                </div>
                <p style="font-size: 0.8rem; color: #818181; line-height: 1.6;">"Posso perguntar o que especificamente...?" — descubra a objeção real por trás do que foi dito.</p>
            </div>
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.375rem;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: rgba(245,158,11,0.15); color: #fbbf24; font-size: 0.875rem; font-weight: 800;">R</span>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Redirecionar</span>
                </div>
                <p style="font-size: 0.8rem; color: #818181; line-height: 1.6;">Apresente a solução com valor — mostre como o produto resolve exatamente o que preocupa o cliente.</p>
            </div>
        </div>

        {{-- 12 Objeções --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">💬 12 Objeções com Script Pronto</p>
        <div style="display: flex; flex-direction: column; gap: 0.625rem; margin-bottom: 1.25rem;">
            <template x-for="(obj, idx) in objectionsV2" :key="idx">
                <div x-data="{ open: false }" style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
                    <button @click="open = !open" type="button"
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; background: #1a1a1a; border: none; cursor: pointer; text-align: left;">
                        <div style="display: flex; align-items: center; gap: 0.625rem; min-width: 0;">
                            <span style="font-size: 1.125rem; flex-shrink: 0;" x-text="obj.icon"></span>
                            <span style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;" x-text="obj.objection"></span>
                        </div>
                        <svg width="14" height="14" :style="open ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition style="padding: 0.875rem 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                        <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                            <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                            <p style="font-size: 0.8rem; color: #fca5a5;" x-text="'&quot;' + obj.objection + '&quot;'"></p>
                        </div>
                        <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.625rem;">
                            <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                            <p style="font-size: 0.8rem; color: #6ee7b7;" x-text="'&quot;' + obj.response + '&quot;'"></p>
                        </div>
                        <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem; padding: 0.75rem;">
                            <p style="font-size: 0.8rem; color: #93c5fd;" x-text="'💡 ' + obj.tip"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Regra de Ouro --}}
        <div style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); border-radius: 0.5rem; padding: 0.875rem 1rem;">
            <p style="font-size: 0.8125rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.375rem;">🏆 Regra de Ouro</p>
            <p style="font-size: 0.8rem; color: #fcd34d; line-height: 1.6;">NUNCA rebata uma objeção diretamente. Acolha primeiro, entenda depois, redirecione por último.</p>
        </div>
    </div>
</div>
