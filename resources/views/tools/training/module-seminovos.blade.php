{{-- Módulo 2: Seminovos --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('seminovos')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">♻️</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Seminovos — Do 11 ao 15</span>
                <div style="font-size: 0.75rem; color: #666666;">Por que esses aparelhos ainda arrasam</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('seminovos')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'seminovos' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'seminovos'" x-transition style="border-top: 1px solid #f3f4f6; padding: 1.25rem;">
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1rem; line-height: 1.6;">
            Seminovo não é sinônimo de velho! Um iPhone 13, por exemplo, ainda roda tudo liso e o cliente paga bem menos. A chave é saber os <strong>pontos fortes</strong> de cada geração pra convencer com segurança. 💪
        </p>

        <div style="display: flex; flex-direction: column; gap: 0.625rem;">
            <template x-for="phone in seminovos" :key="phone.name">
                <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem 1.25rem; display: flex; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                    <div style="min-width: 100px;">
                        <span style="font-size: 0.9375rem; font-weight: 700; color: #e3e3e3;" x-text="phone.name"></span>
                        <div style="font-size: 0.75rem; color: #666666;" x-text="phone.year"></div>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; margin-bottom: 0.5rem;">
                            <template x-for="tag in phone.tags" :key="tag">
                                <span style="font-size: 0.6875rem; font-weight: 500; padding: 2px 8px; border-radius: 9999px; background: #222222; color: #a4a4a4;" x-text="tag"></span>
                            </template>
                        </div>
                        <p style="font-size: 0.8rem; color: #818181; line-height: 1.5;" x-text="phone.whyBuy"></p>
                    </div>
                </div>
            </template>
        </div>

        <div style="margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('tools.checklist') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; text-decoration: none;">
                📋 Checklist Seminovo
            </a>
            <a href="{{ route('tools.specs') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #141414; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; text-decoration: none;">
                📊 Comparar Specs
            </a>
        </div>

        <div style="margin-top: 1rem; background: rgba(59,130,246,0.1); border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 0.75rem 1rem;">
            <p style="font-size: 0.8rem; color: #93c5fd; font-weight: 600;">🔑 Na hora de vender seminovo:</p>
            <p style="font-size: 0.8rem; color: #93c5fd; margin-top: 0.25rem;">Sempre mostre o Checklist pro cliente! Isso passa confiança e mostra que a gente avalia tudo direitinho antes de vender.</p>
        </div>
    </div>
</div>
