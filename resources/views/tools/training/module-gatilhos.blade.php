{{-- Módulo: Gatilhos Mentais --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('gatilhos')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🧠</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Gatilhos Mentais — A Psicologia da Venda</span>
                <div style="font-size: 0.75rem; color: #666666;">Use a ciência a seu favor (com ética!)</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('gatilhos')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'gatilhos' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'gatilhos'" x-transition style="border-top: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">

        {{-- Intro --}}
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1.25rem; line-height: 1.6;">
            Gatilhos mentais são atalhos psicológicos que ajudam o cliente a <strong>decidir com confiança</strong>. Não é manipulação — é apresentar valor no momento certo, da forma certa. Usados com ética, aceleram a venda e melhoram a experiência.
        </p>

        {{-- Gatilhos expandíveis --}}
        <div style="display: flex; flex-direction: column; gap: 0.625rem; margin-bottom: 1.25rem;">
            <template x-for="(trigger, idx) in mentalTriggers" :key="idx">
                <div x-data="{ open: false }" style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
                    <button @click="open = !open" type="button"
                            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; background: #1a1a1a; border: none; cursor: pointer; text-align: left;">
                        <div style="display: flex; align-items: center; gap: 0.625rem;">
                            <span style="font-size: 1.125rem;" x-text="trigger.icon"></span>
                            <span style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;" x-text="trigger.name"></span>
                        </div>
                        <svg width="14" height="14" :style="open ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition style="padding: 0.875rem 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                        <p style="font-size: 0.8rem; color: #818181; line-height: 1.6; margin-bottom: 0.625rem;" x-text="trigger.desc"></p>
                        <div style="background: rgba(16,185,129,0.08); border-radius: 0.375rem; padding: 0.5rem 0.75rem; margin-bottom: 0.625rem;">
                            <p style="font-size: 0.75rem; color: #4ade80; font-weight: 600; margin-bottom: 0.125rem;">Exemplo:</p>
                            <p style="font-size: 0.8rem; color: #6ee7b7; font-style: italic;" x-text="trigger.example"></p>
                        </div>
                        <p style="font-size: 0.75rem; color: #666666;"><span style="font-weight: 600; color: #818181;">Quando usar:</span> <span x-text="trigger.when"></span></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Anti-patterns --}}
        <div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.15); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1rem;">
            <p style="font-size: 0.8125rem; font-weight: 700; color: #fca5a5; margin-bottom: 0.625rem;">❌ CUIDADO: O que NÃO fazer</p>
            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                <p style="font-size: 0.8rem; color: #fca5a5;">• Pressão excessiva ("é agora ou nunca")</p>
                <p style="font-size: 0.8rem; color: #fca5a5;">• Escassez falsa (mentir sobre estoque)</p>
                <p style="font-size: 0.8rem; color: #fca5a5;">• Excesso de informação técnica</p>
                <p style="font-size: 0.8rem; color: #fca5a5;">• Falar mal do concorrente</p>
            </div>
        </div>

        {{-- Combo Perfeito --}}
        <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem; padding: 0.75rem;">
            <p style="font-size: 0.8rem; color: #93c5fd;">💡 <strong>Combo Perfeito:</strong> Use 2-3 gatilhos por conversa, não todos de uma vez. Sugestão: <strong>Reciprocidade</strong> na abertura → <strong>Prova Social</strong> na apresentação → <strong>Escassez real</strong> no fechamento.</p>
        </div>
    </div>
</div>
