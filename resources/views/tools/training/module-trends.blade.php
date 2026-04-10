{{-- Módulo 6: Tendências e Curiosidades --}}
<div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('trends')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: white; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🔥</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #111827;">Tendências & Curiosidades</span>
                <div style="font-size: 0.75rem; color: #9ca3af;">Fatos que impressionam qualquer cliente</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('trends')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'trends' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #9ca3af; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'trends'" x-transition style="border-top: 1px solid #f3f4f6; padding: 1.25rem;">
        <p style="font-size: 0.875rem; color: #374151; margin-bottom: 1rem; line-height: 1.6;">
            Quer impressionar o cliente e mostrar que você manja? Solta essas informações na conversa — funciona demais pra criar conexão e passar credibilidade. 🌟
        </p>

        {{-- Stats --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
            <template x-for="stat in marketStats" :key="stat.label">
                <div style="background: #f9fafb; border-radius: 0.75rem; padding: 1rem; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 800; color: #111827;" x-text="stat.value"></div>
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;" x-text="stat.label"></div>
                </div>
            </template>
        </div>

        {{-- Ecossistema --}}
        <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
            <p style="font-size: 0.875rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">🔗 Ecossistema Apple — O argumento matador</p>
            <p style="font-size: 0.8rem; color: #6b7280; line-height: 1.6; margin-bottom: 0.75rem;">
                Quando o cliente já tem AirPods, Apple Watch ou Mac, o iPhone se torna praticamente obrigatório. Tudo funciona junto de forma mágica:
            </p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem;">
                <template x-for="eco in ecosystem" :key="eco.name">
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: #f9fafb; border-radius: 0.5rem;">
                        <span style="font-size: 1.125rem;" x-text="eco.icon"></span>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 600; color: #111827;" x-text="eco.name"></div>
                            <div style="font-size: 0.7rem; color: #9ca3af;" x-text="eco.hook"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Sustentabilidade --}}
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
            <p style="font-size: 0.875rem; font-weight: 700; color: #065f46; margin-bottom: 0.5rem;">🌿 Sustentabilidade Apple</p>
            <div style="display: flex; flex-direction: column; gap: 0.375rem; font-size: 0.8rem; color: #065f46;">
                <p>• iPhone 17 Pro usa <strong>titânio reciclado</strong> e <strong>alumínio 100% reciclado</strong></p>
                <p>• Embalagem sem plástico desde o iPhone 13</p>
                <p>• Meta de ser carbono neutro em toda a cadeia até 2030</p>
                <p>• Troca de aparelho antigo pelo Apple Trade In — bom pra convencer seminovo!</p>
            </div>
        </div>

        {{-- Fun facts --}}
        <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem;">
            <p style="font-size: 0.875rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">🎲 Curiosidades pra quebrar o gelo:</p>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <template x-for="fact in funFacts" :key="fact">
                    <div style="display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.8rem; color: #374151; line-height: 1.5;">
                        <span style="flex-shrink: 0;">💎</span>
                        <span x-text="fact"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
