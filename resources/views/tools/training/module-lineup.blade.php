{{-- Módulo 1: Lineup 2025 --}}
<div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('lineup')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: white; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">📱</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #111827;">Lineup 2025 — Conhece a Família!</span>
                <div style="font-size: 0.75rem; color: #9ca3af;">Os modelos novos que a gente vende</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('lineup')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: 9999px;">✓ lido</span>
            <svg width="16" height="16" :style="openModule === 'lineup' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #9ca3af; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'lineup'" x-transition style="border-top: 1px solid #f3f4f6; padding: 1.25rem;">
        <p style="font-size: 0.875rem; color: #374151; margin-bottom: 1rem; line-height: 1.6;">
            Bora conhecer os iPhones que tão bombando agora? A Apple mandou <strong>muito bem</strong> esse ano. Cada modelo tem seu público — e saber indicar o certo faz TODA a diferença na venda. 🎯
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.75rem;">
            <template x-for="phone in lineup2025" :key="phone.name">
                <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; transition: transform 0.15s, box-shadow 0.15s;"
                     onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';"
                     onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div :style="'background: linear-gradient(135deg, ' + phone.gradient[0] + ', ' + phone.gradient[1] + '); padding: 1.25rem; display: flex; align-items: center; justify-content: center; min-height: 100px;'">
                        <img :src="phone.img" :alt="phone.name" style="height: 80px; object-fit: contain; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none; align-items: center; justify-content: center; font-size: 2.5rem;">📱</div>
                    </div>
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.9375rem; font-weight: 700; color: #111827;" x-text="phone.name"></span>
                            <span :style="'font-size: 0.6875rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background:' + phone.tagColor + '20; color:' + phone.tagColor" x-text="phone.tag"></span>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.375rem; font-size: 0.8rem; color: #6b7280;">
                            <div style="display: flex; align-items: center; gap: 0.375rem;">
                                <span>⚡</span> <span x-text="phone.chip"></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.375rem;">
                                <span>📸</span> <span x-text="phone.camera"></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.375rem;">
                                <span>📺</span> <span x-text="phone.screen"></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.375rem;">
                                <span>🔋</span> <span x-text="phone.battery"></span>
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem; padding-top: 0.625rem; border-top: 1px solid #f3f4f6;">
                            <p style="font-size: 0.75rem; color: #111827; font-weight: 600;" x-text="'💡 ' + phone.sellTip"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div style="margin-top: 1rem; background: #fefce8; border: 1px solid #fef08a; border-radius: 0.5rem; padding: 0.75rem 1rem;">
            <p style="font-size: 0.8rem; color: #854d0e; font-weight: 600;">💡 Dica de ouro:</p>
            <p style="font-size: 0.8rem; color: #854d0e; margin-top: 0.25rem;">Sempre pergunte pro cliente o que ele mais usa no celular. Quem curte foto? Pro Max. Quer algo leve? iPhone Air. Não quer gastar muito? 16e é perfeito.</p>
        </div>
    </div>
</div>
