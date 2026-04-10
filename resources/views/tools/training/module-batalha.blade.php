{{-- Módulo 3: Batalha de Specs --}}
<div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('batalha')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: white; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">⚔️</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #111827;">Batalha de Specs — iPhone vs iPhone</span>
                <div style="font-size: 0.75rem; color: #9ca3af;">Comparativos que aparecem toda hora na loja</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('batalha')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'batalha' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #9ca3af; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'batalha'" x-transition style="border-top: 1px solid #f3f4f6; padding: 1.25rem;">
        <p style="font-size: 0.875rem; color: #374151; margin-bottom: 1rem; line-height: 1.6;">
            "Qual a diferença do 16 pro o 15 Pro?" — essa pergunta aparece <strong>todo dia</strong>. Cola essas comparações na cabeça e vai mandar bem demais. 🧠
        </p>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <template x-for="battle in battles" :key="battle.title">
                <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                    <div style="background: #f9fafb; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.875rem; font-weight: 700; color: #111827;" x-text="'⚡ ' + battle.title"></span>
                    </div>
                    <div style="padding: 1rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <template x-for="(side, idx) in battle.sides" :key="idx">
                                <div style="text-align: center;">
                                    <div style="font-size: 0.8125rem; font-weight: 700; color: #111827; margin-bottom: 0.375rem;" x-text="side.name"></div>
                                    <template x-for="spec in side.highlights" :key="spec">
                                        <div style="font-size: 0.75rem; color: #6b7280; padding: 2px 0;" x-text="spec"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div style="background: #f0fdf4; border-radius: 0.5rem; padding: 0.625rem 0.75rem;">
                            <p style="font-size: 0.8rem; color: #166534; font-weight: 500;" x-text="'🗣️ ' + battle.script"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div style="margin-top: 1rem; text-align: center;">
            <a href="{{ route('tools.specs') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.625rem 1.25rem; background: #111827; color: white; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; text-decoration: none;">
                📊 Abrir Ficha Técnica Completa
            </a>
        </div>
    </div>
</div>
