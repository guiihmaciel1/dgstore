{{-- Módulo: Seminovo — Como Vender com Confiança --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('seminovo-venda')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">♻️</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Seminovo — Como Vender com Confiança</span>
                <div style="font-size: 0.75rem; color: #666666;">Por que seminovo na DG é diferente</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('seminovo-venda')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'seminovo-venda' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'seminovo-venda'" x-transition style="border-top: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">

        {{-- Intro --}}
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1.25rem; line-height: 1.6;">
            O mercado de iPhone seminovo é <strong>ENORME</strong>. O vendedor que sabe apresentar seminovo com confiança <strong>dobra as conversões</strong>. O segredo? Mostrar que comprar na DG Store é completamente diferente de comprar de desconhecidos.
        </p>

        {{-- Por que DG Store é Diferente --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">✅ Por que DG Store é Diferente</p>
        <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.25rem;">
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">✅</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">IMEI verificado (sem bloqueio, sem roubo)</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">✅</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Bateria com saúde verificada e informada</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">✅</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Garantia de 3 meses na loja</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">✅</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Nota fiscal</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">✅</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Suporte pós-venda</p>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <span style="color: #4ade80; font-size: 0.875rem; flex-shrink: 0;">✅</span>
                    <p style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5;">Checklist completo de funcionamento</p>
                </div>
            </div>
        </div>

        {{-- Como Apresentar o Seminovo --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">🎤 Como Apresentar o Seminovo</p>
        <div style="display: flex; flex-direction: column; gap: 0.625rem; margin-bottom: 1.25rem;">
            <template x-for="(arg, idx) in seminovoArguments" :key="idx">
                <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.625rem;">
                        <span style="font-size: 1.125rem;" x-text="arg.icon"></span>
                        <span style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;" x-text="arg.title"></span>
                    </div>
                    <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                        <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                        <p style="font-size: 0.8rem; color: #6ee7b7;" x-text="'&quot;' + arg.script + '&quot;'"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Trade-in como Ponte --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">🔄 Trade-in como Ponte</p>
        <p style="font-size: 0.8rem; color: #818181; margin-bottom: 0.625rem; line-height: 1.6;">
            O trade-in é uma ponte poderosa pro fechamento. Em vez de falar só no preço final, mostre quanto o aparelho atual do cliente vale como entrada — isso reduz a percepção de gasto e facilita a decisão.
        </p>
        <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 0.625rem;">
            <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
            <p style="font-size: 0.8rem; color: #6ee7b7;">"Seu iPhone 12 vale R$ X de entrada. O iPhone 15 fica por só R$ Y por mês com esse desconto."</p>
        </div>
        <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1.25rem;">
            <p style="font-size: 0.8rem; color: #93c5fd;">💡 Sempre mencione o trade-in ANTES de discutir o preço final. O cliente precisa saber que já tem dinheiro na mesa.</p>
        </div>

        {{-- Comparativo --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">📊 Comparativo</p>
        <div style="overflow-x: auto; margin-bottom: 1.25rem;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.75rem; min-width: 480px;">
                <thead>
                    <tr style="background: #1a1a1a;">
                        <th style="padding: 0.625rem 0.75rem; text-align: left; color: #818181; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.06);"></th>
                        <th style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.06);">Seminovo DG Store</th>
                        <th style="padding: 0.625rem 0.75rem; text-align: center; color: #60a5fa; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.06);">Novo Parcelado</th>
                        <th style="padding: 0.625rem 0.75rem; text-align: center; color: #f87171; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.06);">Mercado Livre</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 0.625rem 0.75rem; color: #a4a4a4; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.04);">Garantia</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; border-bottom: 1px solid rgba(255,255,255,0.04);">3 meses na loja</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #93c5fd; border-bottom: 1px solid rgba(255,255,255,0.04);">1 ano Apple</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #fca5a5; border-bottom: 1px solid rgba(255,255,255,0.04);">Variável / nenhuma</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.625rem 0.75rem; color: #a4a4a4; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.04);">IMEI Verificado</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; border-bottom: 1px solid rgba(255,255,255,0.04);">✓ Sim</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; border-bottom: 1px solid rgba(255,255,255,0.04);">✓ Sim</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #fca5a5; border-bottom: 1px solid rgba(255,255,255,0.04);">✗ Não garantido</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.625rem 0.75rem; color: #a4a4a4; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.04);">Nota Fiscal</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; border-bottom: 1px solid rgba(255,255,255,0.04);">✓ Sim</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; border-bottom: 1px solid rgba(255,255,255,0.04);">✓ Sim</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #fbbf24; border-bottom: 1px solid rgba(255,255,255,0.04);">Depende</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.625rem 0.75rem; color: #a4a4a4; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.04);">Suporte</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; border-bottom: 1px solid rgba(255,255,255,0.04);">Local, pessoal</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #93c5fd; border-bottom: 1px solid rgba(255,255,255,0.04);">Apple autorizada</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #fca5a5; border-bottom: 1px solid rgba(255,255,255,0.04);">Limitado</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.625rem 0.75rem; color: #a4a4a4; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.04);">Risco</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; border-bottom: 1px solid rgba(255,255,255,0.04);">Baixo</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80; border-bottom: 1px solid rgba(255,255,255,0.04);">Baixo</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #fca5a5; border-bottom: 1px solid rgba(255,255,255,0.04);">Alto</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.625rem 0.75rem; color: #a4a4a4; font-weight: 600;">Preço</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #4ade80;">Competitivo</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #fbbf24;">Mais alto</td>
                        <td style="padding: 0.625rem 0.75rem; text-align: center; color: #93c5fd;">Variável</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Diálogo completo --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">💬 Exemplo: Vendendo iPhone 14 Pro pra Cliente Desconfiado</p>
        <div style="display: flex; flex-direction: column; gap: 0.375rem;">
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Seminovo me dá medo. E se vier com problema?"</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Entendo perfeitamente! Por isso aqui na DG a gente faz diferente. Posso te mostrar o checklist completo desse iPhone 14 Pro?"</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Pode. Mas como eu sei que não é roubado?"</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Olha aqui: IMEI verificado, sem bloqueio. Bateria com 89% de saúde — tá informado na etiqueta. E você leva nota fiscal e 3 meses de garantia na loja. Se der qualquer problema, é só voltar aqui."</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"No Mercado Livre achei mais barato..."</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Pode ser! Mas lá você não sabe a procedência, não tem garantia local e se travar o IMEI, ninguém resolve. Aqui você paga um pouco a mais pela tranquilidade — e ainda parcela no cartão com nota fiscal."</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Hmm, faz sentido. E se eu der meu iPhone 11 de entrada?"</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Perfeito! Seu 11 vale R$ 1.800 de entrada. O 14 Pro fica por R$ 2.400 à vista, ou 12x de R$ 230. Quer que eu faça a simulação certinha no sistema?"</p>
            </div>
        </div>
    </div>
</div>
