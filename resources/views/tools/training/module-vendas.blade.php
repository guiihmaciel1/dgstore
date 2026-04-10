{{-- Módulo 5: Dicas de Venda --}}
<div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('vendas')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: white; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🎯</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #111827;">Dicas de Venda — Como Fechar Negócio</span>
                <div style="font-size: 0.75rem; color: #9ca3af;">Técnicas que funcionam de verdade</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('vendas')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'vendas' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #9ca3af; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'vendas'" x-transition style="border-top: 1px solid #f3f4f6; padding: 1.25rem;">
        <p style="font-size: 0.875rem; color: #374151; margin-bottom: 1rem; line-height: 1.6;">
            Saber as specs é metade do caminho. A outra metade é <strong>como você fala</strong> com o cliente. Bora aprender umas técnicas que fazem diferença real no dia a dia? 💰
        </p>

        {{-- Técnicas --}}
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1rem;">
            <template x-for="tip in salesTips" :key="tip.title">
                <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="font-size: 1.125rem;" x-text="tip.icon"></span>
                        <span style="font-size: 0.875rem; font-weight: 700; color: #111827;" x-text="tip.title"></span>
                    </div>
                    <p style="font-size: 0.8rem; color: #6b7280; line-height: 1.6; margin-bottom: 0.5rem;" x-text="tip.desc"></p>
                    <div style="background: #f0fdf4; border-radius: 0.375rem; padding: 0.5rem 0.75rem;">
                        <p style="font-size: 0.8rem; color: #166534; font-style: italic;" x-text="'💬 ' + tip.example"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Objeções --}}
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem;">
            <p style="font-size: 0.875rem; font-weight: 700; color: #991b1b; margin-bottom: 0.75rem;">🛡️ Como responder objeções comuns:</p>
            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                <template x-for="obj in objections" :key="obj.q">
                    <div>
                        <p style="font-size: 0.8rem; font-weight: 700; color: #dc2626;" x-text="'❌ Cliente: ' + obj.q"></p>
                        <p style="font-size: 0.8rem; color: #166534; margin-top: 2px;" x-text="'✅ Você: ' + obj.a"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
