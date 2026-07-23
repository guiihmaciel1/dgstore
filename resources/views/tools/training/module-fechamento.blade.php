{{-- Módulo: Fechamento --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('fechamento')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🤝</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Fechamento — Técnicas que Convertem</span>
                <div style="font-size: 0.75rem; color: #666666;">A hora H. Não deixe a venda escapar.</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('fechamento')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'fechamento' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'fechamento'" x-transition style="border-top: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">

        {{-- Intro --}}
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1.25rem; line-height: 1.6;">
            Fechamento é o fim natural de uma boa conversa. Se você fez as etapas anteriores certo — entendeu o cliente, apresentou valor — fechar é só <strong>confirmar a decisão</strong> que ele já tomou na cabeça.
        </p>

        {{-- Técnicas de fechamento --}}
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
            <template x-for="(tech, idx) in closingTechniques" :key="idx">
                <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="font-size: 1.125rem;" x-text="tech.icon"></span>
                        <span style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;" x-text="tech.name"></span>
                    </div>
                    <p style="font-size: 0.8rem; color: #818181; line-height: 1.6; margin-bottom: 0.625rem;" x-text="tech.desc"></p>
                    <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.625rem;">
                        <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                        <p style="font-size: 0.8rem; color: #6ee7b7; font-style: italic; margin-top: 0.125rem;" x-text="'&quot;' + tech.example + '&quot;'"></p>
                    </div>
                    <p style="font-size: 0.75rem; color: #666666;"><span style="font-weight: 600; color: #818181;">💡 Dica:</span> <span x-text="tech.tip"></span></p>
                </div>
            </template>
        </div>

        {{-- Sinais de Compra --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">✅ Sinais de Compra</p>
        <p style="font-size: 0.8rem; color: #818181; margin-bottom: 0.625rem; line-height: 1.5;">Quando o cliente faz isso, ele já está pronto — é hora de fechar:</p>
        <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.25rem;">
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">☑</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Pergunta sobre cores ou armazenamento disponíveis</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">☑</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Pergunta sobre parcelamento ou condições</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">☑</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Pergunta sobre garantia</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">☑</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Compara modelos dentro da loja (não com concorrente)</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">☑</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Pega o celular atual e olha como se fosse trocar</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">☑</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Diz "vou pensar" mas continua perguntando</p>
                </div>
            </div>
        </div>

        {{-- Warning overselling --}}
        <div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.15); border-radius: 0.5rem; padding: 0.75rem;">
            <p style="font-size: 0.8125rem; font-weight: 700; color: #fca5a5; margin-bottom: 0.375rem;">❌ Quando PARAR de vender</p>
            <p style="font-size: 0.8rem; color: #fca5a5; line-height: 1.6;">Overselling é real. Depois que o cliente disse sim, <strong>pare de vender</strong>. Não adicione mais argumentos — isso cria dúvida e pode desfazer a venda.</p>
        </div>
    </div>
</div>
