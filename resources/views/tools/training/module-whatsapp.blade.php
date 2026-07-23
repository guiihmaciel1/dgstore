{{-- Módulo: Atendimento WhatsApp --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('whatsapp')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">📱</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Atendimento WhatsApp — Do Lead ao Fechamento</span>
                <div style="font-size: 0.75rem; color: #666666;">Onde 90% das vendas começam (e muitas se perdem)</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('whatsapp')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'whatsapp' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'whatsapp'" x-transition style="border-top: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">

        {{-- Intro --}}
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1.25rem; line-height: 1.6;">
            A maioria das vendas começa no WhatsApp. A <strong>velocidade</strong> e a <strong>qualidade</strong> da resposta definem se o lead vira cliente ou vai pro concorrente. Cada minuto conta.
        </p>

        {{-- Regra dos 5 Minutos --}}
        <div style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.25rem;">
            <p style="font-size: 0.8125rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.375rem;">⏱️ Regra dos 5 Minutos</p>
            <p style="font-size: 0.8rem; color: #fcd34d; line-height: 1.6;">Responder em até <strong>5 minutos</strong> aumenta a conversão em <strong>21x</strong> comparado a responder em 30 minutos. O cliente está quente — esfriou, perdeu.</p>
        </div>

        {{-- Estrutura da Conversa --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">📋 Estrutura da Conversa</p>
        <div style="display: flex; flex-direction: column; gap: 0.625rem; margin-bottom: 1.25rem;">
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem; display: flex; gap: 0.75rem; align-items: flex-start;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 0.75rem; font-weight: 800; flex-shrink: 0;">1</span>
                <div>
                    <p style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.125rem;">Saudação</p>
                    <p style="font-size: 0.8rem; color: #818181; line-height: 1.5;">Acolhedora e pessoal. Use o nome, seja humano — não robô de atendimento.</p>
                </div>
            </div>
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem; display: flex; gap: 0.75rem; align-items: flex-start;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 0.75rem; font-weight: 800; flex-shrink: 0;">2</span>
                <div>
                    <p style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.125rem;">Qualificação</p>
                    <p style="font-size: 0.8rem; color: #818181; line-height: 1.5;">Entenda o que o cliente precisa antes de mandar preço. Pergunte uso, orçamento, urgência.</p>
                </div>
            </div>
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem; display: flex; gap: 0.75rem; align-items: flex-start;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 0.75rem; font-weight: 800; flex-shrink: 0;">3</span>
                <div>
                    <p style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.125rem;">Apresentação</p>
                    <p style="font-size: 0.8rem; color: #818181; line-height: 1.5;">Apresente com valor, não só preço. Conecte o produto ao que ele disse que precisa.</p>
                </div>
            </div>
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem; display: flex; gap: 0.75rem; align-items: flex-start;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 0.75rem; font-weight: 800; flex-shrink: 0;">4</span>
                <div>
                    <p style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.125rem;">Fechamento</p>
                    <p style="font-size: 0.8rem; color: #818181; line-height: 1.5;">Feche ou agende. Ofereça retirada, parcelamento e próximo passo claro.</p>
                </div>
            </div>
        </div>

        {{-- Scripts Prontos --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">💬 Scripts Prontos</p>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
            <template x-for="(script, idx) in whatsappScripts" :key="idx">
                <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
                    <div style="background: #1a1a1a; padding: 0.75rem 1rem;">
                        <p style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;" x-text="script.title"></p>
                        <p style="font-size: 0.75rem; color: #666666; margin-top: 0.125rem;" x-text="script.context"></p>
                    </div>
                    <div style="padding: 0.875rem 1rem; display: flex; flex-direction: column; gap: 0.375rem;">
                        <template x-for="(msg, msgIdx) in script.messages" :key="msgIdx">
                            <div x-show="msg.role === 'cliente'" style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                                <p style="font-size: 0.8rem; color: #fca5a5;" x-text="'&quot;' + msg.text + '&quot;'"></p>
                            </div>
                            <div x-show="msg.role === 'vendedor'" style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                                <p style="font-size: 0.8rem; color: #6ee7b7;" x-text="'&quot;' + msg.text + '&quot;'"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- Erros Fatais --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">🚫 Erros Fatais</p>
        <div style="display: flex; flex-direction: column; gap: 0.625rem; margin-bottom: 1.25rem;">
            <template x-for="(item, idx) in whatsappMistakes" :key="idx">
                <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
                    <div style="background: rgba(239,68,68,0.08); padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="font-size: 1rem; flex-shrink: 0;" x-text="item.icon"></span>
                        <p style="font-size: 0.8rem; color: #fca5a5; line-height: 1.5;" x-text="item.mistake"></p>
                    </div>
                    <div style="background: rgba(16,185,129,0.08); padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #4ade80; flex-shrink: 0;">✓</span>
                        <p style="font-size: 0.8rem; color: #6ee7b7; line-height: 1.5;" x-text="item.fix"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Dica áudio vs texto --}}
        <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem; padding: 0.75rem;">
            <p style="font-size: 0.8rem; color: #93c5fd;">💡 Áudio é ótimo pra criar conexão, mas NUNCA envie áudio de mais de 1 minuto. Texto para informações importantes (preço, specs). Áudio para tom pessoal e negociação.</p>
        </div>
    </div>
</div>
