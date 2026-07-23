{{-- Módulo: Simulador e Pré-Venda — Ferramentas que Fecham --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('ferramentas')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🛠️</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Simulador e Pré-Venda — Ferramentas que Fecham</span>
                <div style="font-size: 0.75rem; color: #666666;">Use o sistema a seu favor</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('ferramentas')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'ferramentas' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'ferramentas'" x-transition style="border-top: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">

        {{-- Intro --}}
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1.25rem; line-height: 1.6;">
            O sistema da DG Store tem ferramentas poderosas que ajudam a fechar vendas. Aprenda a usar o <strong>Simulador</strong> e a <strong>Pré-Venda</strong> a seu favor — elas passam profissionalismo e evitam que a venda escape.
        </p>

        {{-- Simulador de Negociação --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">📱 Simulador de Negociação</p>
        <p style="font-size: 0.8rem; color: #818181; margin-bottom: 0.625rem; line-height: 1.6;">
            O simulador calcula parcelas, valor de trade-in e preço final — tudo na hora, sem erro e com visual profissional pro cliente.
        </p>
        <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.625rem;">
            <p style="font-size: 0.75rem; font-weight: 700; color: #818181; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Passo a passo</p>
            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                <p style="font-size: 0.8rem; color: #a4a4a4;"><span style="color: #4ade80; font-weight: 700;">1.</span> Selecione o produto</p>
                <p style="font-size: 0.8rem; color: #a4a4a4;"><span style="color: #4ade80; font-weight: 700;">2.</span> Adicione o trade-in (se houver)</p>
                <p style="font-size: 0.8rem; color: #a4a4a4;"><span style="color: #4ade80; font-weight: 700;">3.</span> Escolha a forma de pagamento</p>
                <p style="font-size: 0.8rem; color: #a4a4a4;"><span style="color: #4ade80; font-weight: 700;">4.</span> Mostre a simulação pro cliente</p>
            </div>
        </div>
        <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 0.625rem;">
            <p style="font-size: 0.8rem; color: #93c5fd;">💡 Nunca diga o preço de cabeça. Sempre use o simulador. Mostra profissionalismo e evita erros.</p>
        </div>
        <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1.25rem;">
            <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
            <p style="font-size: 0.8rem; color: #6ee7b7;">"Vou fazer uma simulação personalizada pra você. Me dá 1 minutinho..."</p>
        </div>

        {{-- Pré-Venda --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">🔒 Pré-Venda — Garanta o Produto</p>
        <p style="font-size: 0.8rem; color: #818181; margin-bottom: 0.625rem; line-height: 1.6;">
            A pré-venda reserva o produto pro cliente enquanto ele decide. É a ferramenta perfeita quando o interesse existe, mas falta um empurrãozinho final.
        </p>
        <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.625rem;">
            <p style="font-size: 0.75rem; font-weight: 700; color: #818181; margin-bottom: 0.375rem;">Quando usar:</p>
            <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.6;">Cliente interessado, mas precisa de tempo — "vou pensar", "preciso consultar", "não tenho o sinal agora".</p>
        </div>
        <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 0.625rem;">
            <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
            <p style="font-size: 0.8rem; color: #6ee7b7;">"Posso reservar esse iPhone pra você sem compromisso. Assim ninguém compra antes."</p>
        </div>
        <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.625rem;">
            <p style="font-size: 0.75rem; font-weight: 700; color: #818181; margin-bottom: 0.375rem;">Benefícios:</p>
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <p style="font-size: 0.8rem; color: #a4a4a4;">• Cliente se sente seguro</p>
                <p style="font-size: 0.8rem; color: #a4a4a4;">• Produto garantido enquanto decide</p>
                <p style="font-size: 0.8rem; color: #a4a4a4;">• Cria urgência natural (sem pressão)</p>
            </div>
        </div>
        <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.25rem;">
            <p style="font-size: 0.75rem; font-weight: 700; color: #818181; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Passo a passo</p>
            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                <p style="font-size: 0.8rem; color: #a4a4a4;"><span style="color: #4ade80; font-weight: 700;">1.</span> Busque o IMEI do aparelho</p>
                <p style="font-size: 0.8rem; color: #a4a4a4;"><span style="color: #4ade80; font-weight: 700;">2.</span> Preencha os dados do cliente</p>
                <p style="font-size: 0.8rem; color: #a4a4a4;"><span style="color: #4ade80; font-weight: 700;">3.</span> Gere a pré-venda no sistema</p>
                <p style="font-size: 0.8rem; color: #a4a4a4;"><span style="color: #4ade80; font-weight: 700;">4.</span> Colete o sinal (entrada)</p>
            </div>
        </div>

        {{-- Dica de Ouro --}}
        <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1.25rem;">
            <p style="font-size: 0.8125rem; font-weight: 700; color: #93c5fd; margin-bottom: 0.375rem;">💡 Dica de Ouro</p>
            <p style="font-size: 0.8rem; color: #93c5fd; line-height: 1.6;">Quando o cliente diz "vou pensar", em vez de deixar ir, use a pré-venda: "Posso reservar pra você enquanto decide? Assim não corre o risco de alguém comprar antes." Isso cria segurança sem pressão.</p>
        </div>

        {{-- Diálogo combinado --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">💬 Exemplo: Simulador + Pré-Venda na Prática</p>
        <div style="display: flex; flex-direction: column; gap: 0.375rem;">
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Gostei do iPhone 16, mas preciso ver quanto fica no cartão."</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Perfeito! Vou fazer uma simulação personalizada pra você. Me dá 1 minutinho... Você tem algum iPhone pra dar de entrada?"</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Tenho um 13 de 128GB."</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Pronto! Seu 13 vale R$ 2.200 de entrada. O iPhone 16 de 256GB fica 12x de R$ 287 no cartão. Olha aqui na tela — tudo certinho."</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Fica bom... mas vou pensar e volto amanhã."</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Sem problema! Posso reservar esse iPhone 16 pra você enquanto decide? É só um sinal simbólico e ninguém leva antes. A simulação que fiz fica válida."</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Ah, faz sentido. Quanto é o sinal?"</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"R$ 200 e o aparelho fica reservado no seu nome. Amanhã você volta, a gente finaliza e desconta do total. Vou gerar a pré-venda agora — me passa seu CPF?"</p>
            </div>
        </div>
    </div>
</div>
