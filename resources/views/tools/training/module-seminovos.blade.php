{{-- Módulo 2: Seminovos --}}
<div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('seminovos')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: white; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">♻️</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #111827;">Seminovos — Do 11 ao 15</span>
                <div style="font-size: 0.75rem; color: #9ca3af;">Por que esses aparelhos ainda arrasam</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('seminovos')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'seminovos' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #9ca3af; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'seminovos'" x-transition style="border-top: 1px solid #f3f4f6; padding: 1.25rem;">
        <p style="font-size: 0.875rem; color: #374151; margin-bottom: 1rem; line-height: 1.6;">
            Seminovo não é sinônimo de velho! Um iPhone 13, por exemplo, ainda roda tudo liso e o cliente paga bem menos. A chave é saber os <strong>pontos fortes</strong> de cada geração pra convencer com segurança. 💪
        </p>

        <div style="display: flex; flex-direction: column; gap: 0.625rem;">
            <template x-for="phone in seminovos" :key="phone.name">
                <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem 1.25rem; display: flex; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                    <div style="min-width: 100px;">
                        <span style="font-size: 0.9375rem; font-weight: 700; color: #111827;" x-text="phone.name"></span>
                        <div style="font-size: 0.75rem; color: #9ca3af;" x-text="phone.year"></div>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; margin-bottom: 0.5rem;">
                            <template x-for="tag in phone.tags" :key="tag">
                                <span style="font-size: 0.6875rem; font-weight: 500; padding: 2px 8px; border-radius: 9999px; background: #f3f4f6; color: #374151;" x-text="tag"></span>
                            </template>
                        </div>
                        <p style="font-size: 0.8rem; color: #6b7280; line-height: 1.5;" x-text="phone.whyBuy"></p>
                    </div>
                </div>
            </template>
        </div>

        <div style="margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('tools.checklist') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; text-decoration: none;">
                📋 Checklist Seminovo
            </a>
            <a href="{{ route('tools.specs') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: white; color: #111827; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; text-decoration: none;">
                📊 Comparar Specs
            </a>
        </div>

        <div style="margin-top: 1rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 0.75rem 1rem;">
            <p style="font-size: 0.8rem; color: #1e40af; font-weight: 600;">🔑 Na hora de vender seminovo:</p>
            <p style="font-size: 0.8rem; color: #1e40af; margin-top: 0.25rem;">Sempre mostre o Checklist pro cliente! Isso passa confiança e mostra que a gente avalia tudo direitinho antes de vender.</p>
        </div>
    </div>
</div>
