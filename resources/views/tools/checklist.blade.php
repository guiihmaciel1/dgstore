<x-app-layout>
    <x-slot name="title">Checklist Seminovo</x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="checklistApp()">

            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Checklist Seminovo</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Avaliacao tecnica completa do aparelho</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <button @click="resetAll()" type="button"
                            style="padding: 0.5rem 0.875rem; font-size: 0.8rem; font-weight: 500; color: #6b7280; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;"
                            onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444';"
                            onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#6b7280';">
                        Limpar tudo
                    </button>
                    <button @click="copySummary()" type="button"
                            :style="copied
                                ? 'padding: 0.5rem 0.875rem; font-size: 0.8rem; font-weight: 600; color: white; background: #059669; border: none; border-radius: 0.5rem; cursor: pointer;'
                                : 'padding: 0.5rem 0.875rem; font-size: 0.8rem; font-weight: 600; color: white; background: #111827; border: none; border-radius: 0.5rem; cursor: pointer;'">
                        <span x-text="copied ? 'Copiado!' : 'Copiar resumo'"></span>
                    </button>
                    <button @click="openSaveModal()" type="button"
                            style="padding: 0.5rem 0.875rem; font-size: 0.8rem; font-weight: 600; color: white; background: #4f46e5; border: none; border-radius: 0.5rem; cursor: pointer;"
                            onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">
                        Salvar Checklist
                    </button>
                </div>
            </div>

            <!-- Upload 3uTools Report -->
            <div style="margin-bottom: 1rem;">
                <div x-show="!deviceInfo" style="background: white; border: 2px dashed #d1d5db; border-radius: 0.75rem; padding: 1.25rem; text-align: center; cursor: pointer; transition: border-color 0.2s;"
                     @click="$refs.fileInput.click()"
                     @dragover.prevent="$el.style.borderColor='#6366f1'"
                     @dragleave.prevent="$el.style.borderColor='#d1d5db'"
                     @drop.prevent="$el.style.borderColor='#d1d5db'; handleFileDrop($event)"
                     onmouseover="this.style.borderColor='#9ca3af'" onmouseout="this.style.borderColor='#d1d5db'">
                    <input type="file" accept=".txt" x-ref="fileInput" @change="handleFileUpload($event)" style="display: none;">
                    <svg style="width: 2rem; height: 2rem; color: #9ca3af; margin: 0 auto 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p style="font-size: 0.8rem; font-weight: 600; color: #374151; margin: 0;">Importar Report do 3uTools</p>
                    <p style="font-size: 0.7rem; color: #9ca3af; margin: 0.25rem 0 0;">Arraste o arquivo .txt ou clique para selecionar</p>
                </div>

                <!-- Device Info Card -->
                <div x-show="deviceInfo" x-transition style="background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%); border: 1px solid #c7d2fe; border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.875rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(199,210,254,0.5);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1rem;">📱</span>
                            <span style="font-size: 0.8rem; font-weight: 700; color: #312e81;" x-text="deviceInfo?.modelName || 'Dispositivo'"></span>
                            <span style="font-size: 0.65rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #c7d2fe; color: #4338ca;" x-text="deviceInfo?.capacity || ''"></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span x-show="importMessage" x-transition style="font-size: 0.65rem; font-weight: 600; color: #059669; background: #dcfce7; padding: 0.125rem 0.5rem; border-radius: 9999px;" x-text="importMessage"></span>
                            <button @click="removeReport()" type="button" style="width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e5e7eb; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 0.75rem;" title="Remover report">✕</button>
                        </div>
                    </div>
                    <div style="padding: 0.75rem 1.25rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem 1rem;">
                        <div x-show="deviceInfo?.color">
                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Cor</span>
                            <p style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0;" x-text="deviceInfo?.color"></p>
                        </div>
                        <div x-show="deviceInfo?.iosVersion">
                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">iOS</span>
                            <p style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0;" x-text="deviceInfo?.iosVersion"></p>
                        </div>
                        <div x-show="deviceInfo?.batteryLife">
                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Bateria</span>
                            <p style="font-size: 0.75rem; font-weight: 600; margin: 0;" :style="'color:' + (parseInt(deviceInfo?.batteryLife) >= 80 ? '#059669' : '#dc2626')" x-text="deviceInfo?.batteryLife + ' / ' + deviceInfo?.chargeCycles + ' ciclos'"></p>
                        </div>
                        <div x-show="deviceInfo?.serialNumber">
                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Serial</span>
                            <p style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0; font-family: monospace;" x-text="deviceInfo?.serialNumber"></p>
                        </div>
                        <div x-show="deviceInfo?.region">
                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Regiao</span>
                            <p style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0;" x-text="deviceInfo?.region"></p>
                        </div>
                        <div x-show="deviceInfo?.snMatch !== undefined">
                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">SN Match</span>
                            <p style="font-size: 0.75rem; font-weight: 600; margin: 0;" :style="'color:' + (deviceInfo?.snMatch === 'Yes' ? '#059669' : '#dc2626')" x-text="deviceInfo?.snMatch"></p>
                        </div>
                        <div x-show="deviceInfo?.fiveCodeMatch !== undefined">
                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">5-Code</span>
                            <p style="font-size: 0.75rem; font-weight: 600; margin: 0;" :style="'color:' + (deviceInfo?.fiveCodeMatch === 'Yes' ? '#059669' : '#dc2626')" x-text="deviceInfo?.fiveCodeMatch"></p>
                        </div>
                        <div x-show="deviceInfo?.activation">
                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ativacao</span>
                            <p style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0;" x-text="deviceInfo?.activation"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de progresso -->
            <div style="margin-bottom: 1.5rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.8rem; font-weight: 600; color: #374151;">Progresso</span>
                    <span style="font-size: 0.8rem; font-weight: 700; color: #111827;" x-text="checkedCount + ' / ' + totalCount"></span>
                </div>
                <div style="width: 100%; height: 8px; background: #f3f4f6; border-radius: 9999px; overflow: hidden;">
                    <div :style="'height: 100%; border-radius: 9999px; transition: width 0.3s; background:' + (percent === 100 ? '#059669' : '#111827') + '; width:' + percent + '%'"></div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 0.375rem;">
                    <span style="font-size: 0.7rem; color: #9ca3af;" x-text="percent + '% concluido'"></span>
                    <span x-show="failCount > 0" style="font-size: 0.7rem; font-weight: 600; color: #dc2626;" x-text="failCount + ' reprovado(s)'"></span>
                </div>
            </div>

            <!-- Secoes do checklist -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <template x-for="(section, sIdx) in sections" :key="sIdx">
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <!-- Header da secao -->
                        <button @click="section.open = !section.open" type="button"
                                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1.25rem; background: none; border: none; cursor: pointer; text-align: left;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="font-size: 1.25rem;" x-text="section.icon"></span>
                                <div>
                                    <div style="font-size: 0.9375rem; font-weight: 700; color: #111827;" x-text="section.title"></div>
                                    <div style="font-size: 0.7rem; color: #9ca3af;" x-text="sectionProgress(sIdx)"></div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <!-- Mini progress -->
                                <div :style="'width: 40px; height: 4px; background: #f3f4f6; border-radius: 9999px; overflow: hidden;'">
                                    <div :style="'height: 100%; border-radius: 9999px; background: #059669; transition: width 0.2s; width:' + sectionPercent(sIdx) + '%'"></div>
                                </div>
                                <svg :style="section.open ? 'transform: rotate(180deg); transition: transform 0.2s; width: 16px; height: 16px; color: #9ca3af;' : 'transition: transform 0.2s; width: 16px; height: 16px; color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        <!-- Itens -->
                        <div x-show="section.open" x-transition style="border-top: 1px solid #f3f4f6;">
                            <!-- Subsecoes -->
                            <template x-for="(sub, subIdx) in section.subs" :key="subIdx">
                                <div>
                                    <div x-show="sub.label" style="padding: 0.5rem 1.25rem 0.25rem; font-size: 0.7rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;" x-text="sub.label"></div>
                                    <template x-for="(item, iIdx) in sub.items" :key="iIdx">
                                        <div style="display: flex; align-items: center; gap: 0; padding: 0 1.25rem; border-bottom: 1px solid #fafafa;">
                                            <!-- Botoes OK / Falha -->
                                            <div style="display: flex; gap: 2px; margin-right: 0.75rem; flex-shrink: 0;">
                                                <button @click="toggleItem(sIdx, subIdx, iIdx, 'ok')" type="button"
                                                        :style="item.status === 'ok'
                                                            ? 'width: 28px; height: 28px; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; background: #dcfce7; color: #16a34a;'
                                                            : 'width: 28px; height: 28px; border-radius: 6px; border: 1px solid #e5e7eb; cursor: pointer; display: flex; align-items: center; justify-content: center; background: white; color: #d1d5db;'"
                                                        title="OK">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                                <button @click="toggleItem(sIdx, subIdx, iIdx, 'fail')" type="button"
                                                        :style="item.status === 'fail'
                                                            ? 'width: 28px; height: 28px; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; background: #fef2f2; color: #dc2626;'
                                                            : 'width: 28px; height: 28px; border-radius: 6px; border: 1px solid #e5e7eb; cursor: pointer; display: flex; align-items: center; justify-content: center; background: white; color: #d1d5db;'"
                                                        title="Falha">
                                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <!-- Label -->
                                            <div style="flex: 1; padding: 0.625rem 0; min-width: 0;">
                                                <span :style="item.status === 'ok' ? 'font-size: 0.875rem; color: #374151; text-decoration: line-through; opacity: 0.5;' : item.status === 'fail' ? 'font-size: 0.875rem; color: #dc2626; font-weight: 500;' : 'font-size: 0.875rem; color: #374151;'"
                                                      x-text="item.label"></span>
                                                <span x-show="item.hint" style="display: block; font-size: 0.7rem; color: #9ca3af; margin-top: 1px;" x-text="item.hint"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Resultado final -->
            <div x-show="checkedCount === totalCount && totalCount > 0" x-transition
                 style="margin-top: 1.5rem; border-radius: 0.75rem; overflow: hidden;"
                 :style="failCount === 0
                    ? 'background: #f0fdf4; border: 2px solid #86efac;'
                    : 'background: #fef2f2; border: 2px solid #fca5a5;'">
                <div style="padding: 1.25rem; text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.25rem;" x-text="failCount === 0 ? '✅' : '⚠️'"></div>
                    <div style="font-size: 1rem; font-weight: 700;" :style="failCount === 0 ? 'color: #166534;' : 'color: #991b1b;'"
                         x-text="failCount === 0 ? 'Aparelho aprovado!' : failCount + ' item(ns) reprovado(s)'"></div>
                    <div style="font-size: 0.8rem; margin-top: 0.25rem;" :style="failCount === 0 ? 'color: #15803d;' : 'color: #b91c1c;'"
                         x-text="failCount === 0 ? 'Todos os itens passaram na avaliacao.' : 'Revise os itens com falha antes de prosseguir.'"></div>
                </div>
            </div>

            <!-- Modal Salvar Checklist -->
            <div x-show="showSaveModal" x-transition.opacity style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(0,0,0,0.5);" @click.self="showSaveModal = false">
                <div x-show="showSaveModal" x-transition style="background: white; border-radius: 0.75rem; width: 100%; max-width: 28rem; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                    <div style="padding: 1.5rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0 0 0.25rem;">Salvar Checklist</h3>
                        <p style="font-size: 0.8rem; color: #6b7280; margin: 0 0 1rem;">Dê um nome para identificar este checklist.</p>
                        <input type="text" x-model="saveName" x-ref="saveNameInput"
                               @keydown.enter="saveChecklist()"
                               placeholder="Ex: iPhone 16 Pro Max 256GB Titanio"
                               style="width: 100%; padding: 0.625rem 0.75rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <p x-show="saveError" style="font-size: 0.75rem; color: #dc2626; margin: 0.5rem 0 0;" x-text="saveError"></p>
                    </div>
                    <div style="padding: 0.75rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid #f3f4f6;">
                        <button @click="showSaveModal = false" type="button"
                                style="padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 500; color: #6b7280; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;">
                            Cancelar
                        </button>
                        <button @click="saveChecklist()" type="button" :disabled="saving"
                                style="padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 600; color: white; background: #4f46e5; border: none; border-radius: 0.5rem; cursor: pointer;"
                                :style="saving ? 'opacity: 0.6; cursor: wait;' : ''">
                            <span x-text="saving ? 'Salvando...' : 'Salvar'"></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    function checklistApp() {
        return {
            copied: false,
            deviceInfo: null,
            importMessage: '',
            showSaveModal: false,
            saveName: '',
            saveError: '',
            saving: false,

            sections: [
                {
                    icon: '🔐', title: 'Identidade e Bloqueios', open: true,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'imei', label: 'IMEI confere (*#06# vs Ajustes)', hint: 'Comparar com caixa e nota fiscal', status: '' },
                            { id: 'id_lock', label: 'iCloud deslogado (Buscar iPhone OFF)', hint: 'Ajustes > [nome] > Buscar > Buscar iPhone', status: '' },
                            { label: 'Sem MDM / perfil corporativo', hint: 'Ajustes > Geral > VPN e Gerenciamento', status: '' },
                            { id: 'sim_lock', label: 'Sem bloqueio de operadora', hint: 'Testar chip de operadora diferente', status: '' },
                            { label: 'Sem relato de roubo/furto', hint: 'Consultar na Anatel (Celular Legal)', status: '' },
                            { id: 'sn_match', label: 'Numero de serie / modelo confere', hint: 'Ajustes > Geral > Sobre (letra M=novo, F=refurb, N=troca)', status: '' },
                        ]
                    }]
                },
                {
                    icon: '📱', title: 'Tela e Display', open: false,
                    subs: [
                        {
                            label: 'Touch',
                            items: [
                                { label: 'Touch funciona em toda a tela', hint: 'Abrir teclado e digitar em todos os cantos', status: '' },
                                { label: 'Deslizamento suave (sem travamentos)', hint: '', status: '' },
                                { label: 'Multitouch funcional', hint: 'Testar zoom com dois dedos no Maps/Fotos', status: '' },
                                { label: 'Force Touch / Haptic Touch OK', hint: 'Pressionar icone na tela inicial', status: '' },
                            ]
                        },
                        {
                            label: 'Display',
                            items: [
                                { label: 'Sem pixels mortos ou manchas', hint: 'Abrir imagem branca pura em tela cheia', status: '' },
                                { label: 'Brilho maximo e minimo OK', hint: '', status: '' },
                                { label: 'True Tone funcionando', hint: 'Ajustes > Tela e Brilho > True Tone', status: '' },
                                { label: 'Sem marcas de burn-in', hint: 'Abrir fundo cinza e verificar sombras (OLED)', status: '' },
                                { label: 'Sem vazamento de luz nas bordas', hint: 'Verificar em ambiente escuro com fundo preto', status: '' },
                                { label: 'Sensor de brilho automatico OK', hint: 'Tapar sensor frontal e ver se escurece', status: '' },
                            ]
                        }
                    ]
                },
                {
                    icon: '🔐', title: 'Biometria', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'face_id', label: 'Face ID funcionando', hint: 'Cadastrar e testar desbloqueio', status: '' },
                            { label: 'Face ID com angulo lateral', hint: 'Testar em angulos diferentes', status: '' },
                        ]
                    }]
                },
                {
                    icon: '📷', title: 'Cameras', open: false,
                    subs: [
                        {
                            label: 'Camera traseira',
                            items: [
                                { id: 'rear_camera', label: 'Foto traseira OK', hint: 'Verificar nitidez e foco automatico', status: '' },
                                { label: 'Video traseiro OK', hint: 'Gravar 10s e reproduzir', status: '' },
                                { label: 'Flash / Lanterna OK', hint: '', status: '' },
                                { label: 'Lente 0.5x (Ultra Wide)', hint: '', status: '' },
                                { label: 'Lente 1x (Principal)', hint: '', status: '' },
                                { label: 'Lente 2x (Telephoto)', hint: '', status: '' },
                                { label: 'Lente 3x / 5x (se aplicavel)', hint: 'iPhone Pro / Pro Max', status: '' },
                                { label: 'Modo retrato funcional', hint: '', status: '' },
                                { label: 'Modo panoramico funcional', hint: '', status: '' },
                                { label: 'Estabilizacao de imagem OK', hint: 'Gravar video andando', status: '' },
                            ]
                        },
                        {
                            label: 'Camera frontal',
                            items: [
                                { id: 'front_camera', label: 'Foto frontal OK', hint: '', status: '' },
                                { label: 'Video frontal OK', hint: '', status: '' },
                            ]
                        }
                    ]
                },
                {
                    icon: '📞', title: 'Chamadas e Audio', open: false,
                    subs: [
                        {
                            label: 'Chamadas',
                            items: [
                                { label: 'Chip inserido e reconhecido', hint: '', status: '' },
                                { label: 'Ligacao realizada com sucesso', hint: '', status: '' },
                                { label: 'Audio do auricular OK (voce ouve)', hint: '', status: '' },
                                { label: 'Microfone OK (outra pessoa ouve)', hint: '', status: '' },
                                { label: 'Viva-voz funcional', hint: '', status: '' },
                                { label: 'Sem chiados ou falhas', hint: '', status: '' },
                            ]
                        },
                        {
                            label: 'Alto-falantes',
                            items: [
                                { label: 'Alto-falante superior OK', hint: 'Tocar musica e verificar', status: '' },
                                { label: 'Alto-falante inferior OK', hint: 'Volume maximo sem distorcao', status: '' },
                                { label: 'Audio estereo balanceado', hint: 'Ouvir musica e perceber se sai dos dois lados', status: '' },
                            ]
                        },
                        {
                            label: 'Microfone',
                            items: [
                                { label: 'Microfone principal (gravador de voz)', hint: 'Gravar e reproduzir — audio limpo', status: '' },
                                { label: 'Microfone secundario (reducao ruido)', hint: 'Gravar video e verificar audio', status: '' },
                            ]
                        }
                    ]
                },
                {
                    icon: '🔘', title: 'Botoes e Controles', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'Botao Volume +', hint: '', status: '' },
                            { label: 'Botao Volume -', hint: '', status: '' },
                            { label: 'Botao Lateral (Power)', hint: 'Ligar/desligar tela', status: '' },
                            { label: 'Botao Acao / Silencioso', hint: 'iPhone 15 Pro+: Action Button / Outros: chave mute', status: '' },
                            { label: 'Vibrar ao silenciar', hint: 'Motor Taptic Engine', status: '' },
                        ]
                    }]
                },
                {
                    icon: '🔋', title: 'Bateria e Carregamento', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'battery_health', label: 'Saude da bateria verificada', hint: 'Ajustes > Bateria > Saude e Carregamento', status: '' },
                            { id: 'battery_above_80', label: 'Capacidade acima de 80%', hint: 'Abaixo de 80% = troca recomendada', status: '' },
                            { id: 'charge_cycles', label: 'Contagem de ciclos aceitavel', hint: 'iOS 17.4+: Ajustes > Geral > Sobre > Bateria', status: '' },
                            { id: 'battery_sn', label: 'Bateria original (sem aviso de servico)', hint: 'Ajustes > Geral > Sobre: Pecas e Servico', status: '' },
                            { label: 'Carregamento via cabo USB-C / Lightning', hint: '', status: '' },
                            { label: 'Carregamento wireless / MagSafe', hint: 'Se aplicavel ao modelo', status: '' },
                            { label: 'Sem aquecimento excessivo', hint: 'Usar por 5min e verificar temperatura', status: '' },
                        ]
                    }]
                },
                {
                    icon: '📶', title: 'Conectividade', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'wifi', label: 'Wi-Fi conecta e navega', hint: 'Conectar e abrir site', status: '' },
                            { id: 'bluetooth', label: 'Bluetooth funcional', hint: 'Parear com fone ou outro dispositivo', status: '' },
                            { id: 'cellular', label: 'Dados moveis (4G/5G)', hint: 'Desligar Wi-Fi e navegar', status: '' },
                            { label: 'GPS / Localizacao', hint: 'Abrir Apple Maps e verificar posicao', status: '' },
                            { label: 'NFC funcional', hint: 'Testar Apple Pay ou aproximar tag NFC', status: '' },
                            { label: 'AirDrop funcional', hint: '', status: '' },
                        ]
                    }]
                },
                {
                    icon: '🧲', title: 'Sensores', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'Sensor de proximidade', hint: 'Em ligacao, colocar perto do rosto — tela apaga', status: '' },
                            { label: 'Acelerometro', hint: 'App Bussola > Nivel — verificar se responde', status: '' },
                            { label: 'Giroscopio', hint: 'Rotacao automatica da tela', status: '' },
                            { label: 'Bussola', hint: 'App Bussola — apontar para norte', status: '' },
                            { label: 'Barometro (altimetro)', hint: 'App Saude > Dados de Mobilidade', status: '' },
                            { id: 'lidar', label: 'LiDAR (Pro/Pro Max)', hint: 'App Medida — apontar para objeto', status: '' },
                        ]
                    }]
                },
                {
                    icon: '🔍', title: 'Inspecao Fisica', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'Tela sem trincas ou rachaduras', hint: '', status: '' },
                            { label: 'Traseira sem danos', hint: '', status: '' },
                            { label: 'Laterais sem amassados ou riscos profundos', hint: 'Verificar cantos e quinas', status: '' },
                            { label: 'Sem gap entre tela e moldura', hint: 'Sinal de abertura ou bateria inchada', status: '' },
                            { label: 'Porta USB-C / Lightning limpa', hint: 'Sem sujeira, oxidacao ou pinos tortos', status: '' },
                            { label: 'Bandeja SIM sem danos', hint: '', status: '' },
                            { label: 'Indicador de liquido (LCI) branco/prata', hint: 'Dentro da bandeja SIM e porta Lightning — vermelho = dano por liquido', status: '' },
                            { label: 'Lentes das cameras sem riscos', hint: '', status: '' },
                            { id: 'screen_sn', label: 'Tela original (sem troca)', hint: 'Ajustes > Geral > Sobre: Pecas e Servico', status: '' },
                        ]
                    }]
                },
                {
                    icon: '⚙️', title: 'Software e Sistema', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'iOS atualizado ou atualizavel', hint: 'Ajustes > Geral > Atualizacao de Software', status: '' },
                            { id: 'jailbreak', label: 'Sem jailbreak', hint: 'Verificar se ha apps como Cydia ou Sileo', status: '' },
                            { label: 'Historico de pecas e servico limpo', hint: 'Ajustes > Geral > Sobre', status: '' },
                            { label: 'Aparelho restaurado de fabrica', hint: 'Garantir que o aparelho esta zerado', status: '' },
                            { label: 'Siri funcional', hint: 'Dizer "E ai, Siri"', status: '' },
                        ]
                    }]
                },
            ],

            // --- Computed ---

            get totalCount() {
                let c = 0;
                this.sections.forEach(s => s.subs.forEach(sub => c += sub.items.length));
                return c;
            },

            get checkedCount() {
                let c = 0;
                this.sections.forEach(s => s.subs.forEach(sub => sub.items.forEach(i => { if (i.status) c++; })));
                return c;
            },

            get failCount() {
                let c = 0;
                this.sections.forEach(s => s.subs.forEach(sub => sub.items.forEach(i => { if (i.status === 'fail') c++; })));
                return c;
            },

            get percent() {
                return this.totalCount ? Math.round((this.checkedCount / this.totalCount) * 100) : 0;
            },

            sectionProgress(sIdx) {
                let total = 0, checked = 0;
                this.sections[sIdx].subs.forEach(sub => {
                    total += sub.items.length;
                    sub.items.forEach(i => { if (i.status) checked++; });
                });
                return checked + ' de ' + total;
            },

            sectionPercent(sIdx) {
                let total = 0, checked = 0;
                this.sections[sIdx].subs.forEach(sub => {
                    total += sub.items.length;
                    sub.items.forEach(i => { if (i.status) checked++; });
                });
                return total ? Math.round((checked / total) * 100) : 0;
            },

            // --- Actions ---

            toggleItem(sIdx, subIdx, iIdx, value) {
                const item = this.sections[sIdx].subs[subIdx].items[iIdx];
                item.status = item.status === value ? '' : value;
            },

            resetAll() {
                if (!confirm('Limpar todo o checklist?')) return;
                this.sections.forEach(s => s.subs.forEach(sub => sub.items.forEach(i => {
                    i.status = '';
                    if (i._originalHint !== undefined) { i.hint = i._originalHint; delete i._originalHint; }
                })));
                this.deviceInfo = null;
                this.importMessage = '';
            },

            // --- 3uTools Import ---

            findItem(id) {
                for (const s of this.sections) {
                    for (const sub of s.subs) {
                        for (const item of sub.items) {
                            if (item.id === id) return item;
                        }
                    }
                }
                return null;
            },

            _setItem(id, status, hintOverride) {
                const item = this.findItem(id);
                if (!item) return;
                item.status = status;
                if (hintOverride) {
                    item._originalHint = item._originalHint ?? item.hint;
                    item.hint = hintOverride;
                }
            },

            handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                this._readAndParse(file);
                event.target.value = '';
            },

            handleFileDrop(event) {
                const file = event.dataTransfer.files[0];
                if (!file) return;
                this._readAndParse(file);
            },

            _readAndParse(file) {
                if (!file.name.endsWith('.txt')) {
                    alert('Selecione um arquivo .txt do 3uTools');
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    const parsed = this.parse3uToolsReport(e.target.result);
                    if (!parsed) {
                        alert('Nao foi possivel ler o report. Verifique se e um Verification Report do 3uTools.');
                        return;
                    }
                    this.applyReport(parsed);
                };
                reader.readAsText(file);
            },

            parse3uToolsReport(text) {
                const lines = text.split(/\r?\n/).filter(l => l.trim());
                const device = {};
                const tests = {};
                const extras = {};

                let section = '';
                for (const line of lines) {
                    const parts = line.split(/\t+/).map(s => s.trim()).filter(Boolean);
                    if (parts.length === 0) continue;

                    if (parts[0] === 'Device Information') { section = 'device'; continue; }
                    if (parts[0] === 'Test Items') { section = 'tests'; continue; }

                    if (section === 'device' && parts.length >= 2) {
                        const key = parts[0].trim();
                        const val = parts.slice(1).join(' ').trim();
                        const keyMap = {
                            'Model Identifier': 'modelIdentifier',
                            'iOS Version': 'iosVersion',
                            'Activation': 'activation',
                            'Jailbreak': 'jailbreak',
                            'SN Match': 'snMatch',
                            '5-Code Match': 'fiveCodeMatch',
                            'SIM Lock': 'simLock',
                            'Mfg. Date': 'mfgDate',
                            'Warranty Period': 'warrantyPeriod',
                            'ID Lock': 'idLock',
                            'Battery Life': 'batteryLife',
                            'Charge Cycles': 'chargeCycles',
                        };
                        if (keyMap[key]) device[keyMap[key]] = val;
                        continue;
                    }

                    if (section === 'tests') {
                        const keyMap = {
                            'Model Name': 'modelName',
                            'Device Color': 'deviceColor',
                            'Capacity': 'capacity',
                            'Model Number': 'modelNumber',
                            'Sales Region': 'salesRegion',
                            'Regulatory Model': 'regulatoryModel',
                            'Serial Number': 'serialNumber',
                            'Logic Board SN': 'logicBoardSN',
                            'Battery SN': 'batterySN',
                            'Front Camera': 'frontCamera',
                            'Rear Camera': 'rearCamera',
                            'Screen SN': 'screenSN',
                            'LiDAR': 'lidar',
                            'Bluetooth': 'bluetooth',
                            'Cellular Address': 'cellular',
                            'Wi-Fi Address': 'wifi',
                        };

                        const faceIdKeys = ['Face ID', 'Infrared Camera', 'Dot Projector', 'Distance Senror', 'Distance Sensor'];

                        for (const fk of faceIdKeys) {
                            const fkIdx = line.indexOf(fk);
                            if (fkIdx > -1) {
                                const after = line.substring(fkIdx + fk.length).split(/\t+/).map(s => s.trim()).filter(Boolean);
                                const fKeyMap = {
                                    'Face ID': 'faceId',
                                    'Infrared Camera': 'infraredCamera',
                                    'Dot Projector': 'dotProjector',
                                    'Distance Senror': 'distanceSensor',
                                    'Distance Sensor': 'distanceSensor',
                                };
                                extras[fKeyMap[fk]] = after[0] || 'Pending Inspection';
                            }
                        }

                        const key = parts[0].trim();
                        if (keyMap[key]) {
                            if (parts.length >= 4) {
                                tests[keyMap[key]] = { factory: parts[1], read: parts[2], result: parts[3] };
                            } else if (parts.length >= 2) {
                                const lastPart = parts[parts.length - 1];
                                tests[keyMap[key]] = { factory: parts[1] || '', read: '', result: lastPart };
                            }
                        }
                    }
                }

                if (!device.modelIdentifier && !tests.modelName) return null;

                return { device, tests, extras };
            },

            applyReport(parsed) {
                const { device, tests, extras } = parsed;
                let filled = 0;

                this.deviceInfo = {
                    modelName: tests.modelName?.read || tests.modelName?.factory || device.modelIdentifier,
                    capacity: tests.capacity?.read || '',
                    color: (tests.deviceColor?.read || '').replace(/\u00a3\u00ac/g, ', '),
                    iosVersion: device.iosVersion || '',
                    batteryLife: (device.batteryLife || '').replace('%', ''),
                    chargeCycles: device.chargeCycles || '',
                    serialNumber: tests.serialNumber?.read || '',
                    region: tests.salesRegion?.read || '',
                    snMatch: device.snMatch,
                    fiveCodeMatch: device.fiveCodeMatch,
                    activation: device.activation || '',
                    idLock: device.idLock,
                };

                const ok = (id, hint) => { this._setItem(id, 'ok', hint); filled++; };
                const fail = (id, hint) => { this._setItem(id, 'fail', hint); filled++; };

                // SN Match
                if (device.snMatch === 'Yes') ok('sn_match', '3uTools: SN Match OK');
                else if (device.snMatch === 'No') fail('sn_match', '3uTools: SN NAO confere!');

                // Jailbreak
                if (device.jailbreak === 'No') ok('jailbreak', '3uTools: Sem jailbreak');
                else if (device.jailbreak === 'Yes') fail('jailbreak', '3uTools: JAILBREAK detectado!');

                // ID Lock
                if (device.idLock === 'Off' || device.idLock === 'OFF') ok('id_lock', '3uTools: ID Lock OFF');
                else if (device.idLock === 'On' || device.idLock === 'ON') fail('id_lock', '3uTools: ID Lock ATIVO!');

                // SIM Lock
                if (device.simLock === 'Unlocked' || device.simLock === 'No') ok('sim_lock', '3uTools: Desbloqueado');
                else if (device.simLock === 'Locked') fail('sim_lock', '3uTools: Bloqueado por operadora!');

                // Battery
                const battPct = parseInt(device.batteryLife);
                if (!isNaN(battPct)) {
                    ok('battery_health', '3uTools: ' + battPct + '%');
                    if (battPct >= 80) ok('battery_above_80', '3uTools: ' + battPct + '%');
                    else fail('battery_above_80', '3uTools: ' + battPct + '% — abaixo de 80%');
                }
                if (device.chargeCycles) {
                    ok('charge_cycles', '3uTools: ' + device.chargeCycles + ' ciclos');
                }

                // Battery SN
                if (tests.batterySN?.result === 'Normal') ok('battery_sn', '3uTools: SN original');
                else if (tests.batterySN?.result) fail('battery_sn', '3uTools: ' + tests.batterySN.result);

                // Screen SN
                if (tests.screenSN?.result === 'Normal') ok('screen_sn', '3uTools: Tela original');
                else if (tests.screenSN?.result) fail('screen_sn', '3uTools: ' + tests.screenSN.result);

                // Cameras
                if (tests.frontCamera?.result === 'Normal') ok('front_camera', '3uTools: SN original');
                if (tests.rearCamera?.result === 'Normal') ok('rear_camera', '3uTools: SN original');

                // Connectivity
                if (tests.bluetooth?.result === 'Normal') ok('bluetooth', '3uTools: Normal');
                if (tests.wifi?.result === 'Normal') ok('wifi', '3uTools: Normal');
                if (tests.cellular?.result === 'Normal') ok('cellular', '3uTools: Normal');

                // LiDAR
                if (tests.lidar?.result === 'Normal') ok('lidar', '3uTools: Normal');

                // Face ID
                if (extras.faceId === 'Normal' || extras.faceId === 'Tap to Check') {
                    // only auto-fill if confirmed normal
                    if (extras.faceId === 'Normal') ok('face_id', '3uTools: Face ID Normal');
                }

                const remaining = this.totalCount - this.checkedCount;
                this.importMessage = filled + ' itens preenchidos, ' + remaining + ' para verificar';
            },

            removeReport() {
                this.deviceInfo = null;
                this.importMessage = '';
                this.sections.forEach(s => s.subs.forEach(sub => sub.items.forEach(i => {
                    i.status = '';
                    if (i._originalHint !== undefined) { i.hint = i._originalHint; delete i._originalHint; }
                })));
            },

            // --- Save ---

            openSaveModal() {
                let suggestion = '';
                if (this.deviceInfo) {
                    const parts = [this.deviceInfo.modelName, this.deviceInfo.capacity, this.deviceInfo.color].filter(Boolean);
                    suggestion = parts.join(' ');
                }
                this.saveName = suggestion;
                this.saveError = '';
                this.showSaveModal = true;
                this.$nextTick(() => this.$refs.saveNameInput?.focus());
            },

            async saveChecklist() {
                if (!this.saveName.trim()) {
                    this.saveError = 'Informe um nome para o checklist.';
                    return;
                }
                this.saving = true;
                this.saveError = '';
                try {
                    const res = await fetch('{{ route("checklists.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            name: this.saveName.trim(),
                            device_info: this.deviceInfo,
                            sections: this.sections.map(s => ({
                                icon: s.icon,
                                title: s.title,
                                subs: s.subs.map(sub => ({
                                    label: sub.label,
                                    items: sub.items.map(i => ({
                                        id: i.id || null,
                                        label: i.label,
                                        hint: i._originalHint ?? i.hint,
                                        status: i.status,
                                    })),
                                })),
                            })),
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.saveError = data.message || 'Erro ao salvar.';
                        return;
                    }
                    this.showSaveModal = false;
                    window.location.href = data.redirect;
                } catch (e) {
                    this.saveError = 'Erro de rede. Tente novamente.';
                } finally {
                    this.saving = false;
                }
            },

            // --- Copy ---

            copySummary() {
                let lines = ['*CHECKLIST SEMINOVO - DG Store*', ''];

                if (this.deviceInfo) {
                    lines.push('📱 *' + this.deviceInfo.modelName + '* ' + this.deviceInfo.capacity);
                    if (this.deviceInfo.color) lines.push('🎨 ' + this.deviceInfo.color);
                    if (this.deviceInfo.batteryLife) lines.push('🔋 Bateria: ' + this.deviceInfo.batteryLife + '% / ' + this.deviceInfo.chargeCycles + ' ciclos');
                    if (this.deviceInfo.serialNumber) lines.push('🔢 Serial: ' + this.deviceInfo.serialNumber);
                    if (this.deviceInfo.snMatch) lines.push((this.deviceInfo.snMatch === 'Yes' ? '✅' : '❌') + ' SN Match: ' + this.deviceInfo.snMatch);
                    lines.push('');
                }

                this.sections.forEach(s => {
                    let sectionItems = [];
                    s.subs.forEach(sub => sub.items.forEach(i => {
                        if (i.status) {
                            const icon = i.status === 'ok' ? '✅' : '❌';
                            sectionItems.push(icon + ' ' + i.label);
                        }
                    }));
                    if (sectionItems.length > 0) {
                        lines.push(s.icon + ' *' + s.title + '*');
                        sectionItems.forEach(l => lines.push(l));
                        lines.push('');
                    }
                });

                if (this.failCount > 0) {
                    lines.push('⚠️ *' + this.failCount + ' item(ns) com falha*');
                } else if (this.checkedCount === this.totalCount) {
                    lines.push('✅ *Aparelho APROVADO*');
                }
                lines.push('');
                lines.push(this.checkedCount + '/' + this.totalCount + ' verificados (' + this.percent + '%)');

                navigator.clipboard.writeText(lines.join('\n')).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                });
            },
        };
    }
    </script>
</x-app-layout>
