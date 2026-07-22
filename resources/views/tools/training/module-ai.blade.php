{{-- Módulo 4: Apple Intelligence --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('ai')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🤖</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Apple Intelligence & Novidades 2025</span>
                <div style="font-size: 0.75rem; color: #666666;">O que tá mudando o jogo este ano</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('ai')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'ai' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'ai'" x-transition style="border-top: 1px solid #f3f4f6; padding: 1.25rem;">
        <p style="font-size: 0.875rem; color: #a4a4a4; margin-bottom: 1rem; line-height: 1.6;">
            A Apple Intelligence é tipo o "cérebro" novo dos iPhones. Usa IA direto no aparelho — privacidade total, sem mandar seus dados pra nuvem. Esse é o grande diferencial! 🔐
        </p>

        {{-- Compatibilidade --}}
        <div style="background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
            <p style="font-size: 0.8125rem; font-weight: 700; color: #c4b5fd; margin-bottom: 0.5rem;">🧠 Quais iPhones têm Apple Intelligence?</p>
            <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
                <template x-for="model in aiCompatible" :key="model">
                    <span style="font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; background: #7c3aed; color: white;" x-text="model"></span>
                </template>
            </div>
            <p style="font-size: 0.75rem; color: #6b21a8; margin-top: 0.5rem;">⚠️ iPhone 15 e anteriores (sem Pro) <strong>NÃO</strong> têm. Esse é um argumento forte pra upgrade!</p>
        </div>

        {{-- Features --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
            <template x-for="feat in aiFeatures" :key="feat.name">
                <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                    <div style="font-size: 1.25rem; margin-bottom: 0.375rem;" x-text="feat.icon"></div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.25rem;" x-text="feat.name"></div>
                    <p style="font-size: 0.75rem; color: #818181; line-height: 1.5;" x-text="feat.desc"></p>
                </div>
            </template>
        </div>

        {{-- Botões físicos --}}
        <div style="background: #1a1a1a; border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.75rem;">
            <p style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem;">🆕 Botões novos que o cliente vai amar:</p>
            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <span style="font-size: 1.25rem;">📸</span>
                    <div>
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Camera Control</span>
                        <span style="font-size: 0.75rem; color: #666666;"> (iPhone 16+)</span>
                        <p style="font-size: 0.8rem; color: #818181; margin-top: 2px;">Botão dedicado pra câmera na lateral. Desliza pra dar zoom, aperta leve pra focar. Mostra isso pro cliente na loja — eles piram!</p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <span style="font-size: 1.25rem;">⚡</span>
                    <div>
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #e3e3e3;">Action Button</span>
                        <span style="font-size: 0.75rem; color: #666666;"> (iPhone 15 Pro+)</span>
                        <p style="font-size: 0.8rem; color: #818181; margin-top: 2px;">Substituiu a chave de silencioso. Configura pra lanterna, câmera, gravador, atalho... Cada cliente pode personalizar do jeito dele.</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="background: rgba(245,158,11,0.1); border: 1px solid #fef08a; border-radius: 0.5rem; padding: 0.75rem 1rem;">
            <p style="font-size: 0.8rem; color: #fbbf24; font-weight: 600;">💡 Na hora de vender:</p>
            <p style="font-size: 0.8rem; color: #fbbf24; margin-top: 0.25rem;">"Esse iPhone tem inteligência artificial integrada. Ele reescreve seus textos, resume notificações e até cria emojis personalizados — tudo sem precisar de internet e com total privacidade."</p>
        </div>
    </div>
</div>
