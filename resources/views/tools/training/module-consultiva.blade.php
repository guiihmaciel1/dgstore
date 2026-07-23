{{-- Módulo: Venda Consultiva --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('consultiva')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🎯</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Venda Consultiva — Entenda Antes de Vender</span>
                <div style="font-size: 0.75rem; color: #666666;">Pare de empurrar produto. Comece a resolver problemas.</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('consultiva')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'consultiva' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'consultiva'" x-transition style="border-top: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">

        {{-- Intro --}}
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1.25rem; line-height: 1.6;">
            Venda agressiva empurra produto. Venda consultiva <strong>resolve problemas</strong>. Em vez de falar specs logo de cara, você faz perguntas, entende a rotina do cliente e só então indica o iPhone certo. Resultado: confiança, menos objeção e fechamento natural.
        </p>

        {{-- Perguntas-Chave --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">🔑 Perguntas-Chave</p>
        <div style="display: flex; flex-direction: column; gap: 0.625rem; margin-bottom: 1.25rem;">
            <template x-for="(item, idx) in consultiveQuestions" :key="idx">
                <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem;">
                    <div style="display: flex; align-items: flex-start; gap: 0.625rem;">
                        <span style="font-size: 1.125rem; flex-shrink: 0;" x-text="item.icon"></span>
                        <div>
                            <p style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.25rem;" x-text="item.question"></p>
                            <p style="font-size: 0.75rem; color: #818181; line-height: 1.5;"><span style="color: #666666; font-weight: 600;">Por quê?</span> <span x-text="item.why"></span></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Técnica SPIN Adaptada --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">🔄 Técnica SPIN Adaptada</p>
        <div style="display: flex; flex-direction: column; gap: 0.625rem; margin-bottom: 1.25rem;">
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.5rem;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 0.875rem; font-weight: 800;">S</span>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Situação</span>
                </div>
                <p style="font-size: 0.8rem; color: #818181; line-height: 1.6;">"Qual iPhone você usa hoje?" / "Pra que você mais usa?"</p>
            </div>
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.5rem;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: rgba(239,68,68,0.15); color: #f87171; font-size: 0.875rem; font-weight: 800;">P</span>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Problema</span>
                </div>
                <p style="font-size: 0.8rem; color: #818181; line-height: 1.6;">"O que te incomoda no atual?" / "A bateria dura o dia?"</p>
            </div>
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.5rem;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: rgba(245,158,11,0.15); color: #fbbf24; font-size: 0.875rem; font-weight: 800;">I</span>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Implicação</span>
                </div>
                <p style="font-size: 0.8rem; color: #818181; line-height: 1.6;">"Quanto tempo você perde carregando?" / "Já perdeu fotos por falta de espaço?"</p>
            </div>
            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.875rem 1rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.5rem;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: rgba(16,185,129,0.15); color: #4ade80; font-size: 0.875rem; font-weight: 800;">N</span>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Necessidade</span>
                </div>
                <p style="font-size: 0.8rem; color: #818181; line-height: 1.6;">"Se tivesse um iPhone que dura o dia todo, mudaria sua rotina?"</p>
            </div>
        </div>

        {{-- Diálogo completo --}}
        <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">💬 Exemplo de Conversa Consultiva</p>
        <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 0.5rem;">
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Quero trocar de celular, tô pensando em iPhone."</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Legal! Me conta: qual você usa hoje e o que mais faz nele?"</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Tenho um Android de 3 anos. Uso muito pra foto dos filhos e WhatsApp de trabalho."</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"E o que mais te incomoda nele hoje? Bateria, espaço, lentidão?"</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"A bateria não dura nem meio dia. E já perdi foto porque acabou o espaço."</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Imagina perder aquele momento especial dos filhos por causa de espaço... Se tivesse um iPhone com bateria que dura o dia todo e 256GB, isso mudaria sua rotina?"</p>
            </div>
            <div style="background: rgba(239,68,68,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin-bottom: 0.25rem;">
                <span style="font-size: 0.7rem; color: #f87171; font-weight: 600;">CLIENTE</span>
                <p style="font-size: 0.8rem; color: #fca5a5;">"Com certeza! Qual você recomenda?"</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border-radius: 0.5rem; padding: 0.5rem 0.75rem;">
                <span style="font-size: 0.7rem; color: #4ade80; font-weight: 600;">VOCÊ</span>
                <p style="font-size: 0.8rem; color: #6ee7b7;">"Pelo que você me contou — fotos, trabalho e bateria — o iPhone 17 de 256GB é perfeito. Câmera de 48MP, bateria que dura o dia e Apple Intelligence pra resumir suas mensagens de trabalho. Quer ver na mão?"</p>
            </div>
        </div>

        <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem; padding: 0.75rem; margin-top: 1rem;">
            <p style="font-size: 0.8rem; color: #93c5fd;">💡 Quando o cliente pede recomendação no final, a venda já está feita. Você só confirmou a escolha dele.</p>
        </div>
    </div>
</div>
