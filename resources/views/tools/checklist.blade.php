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

            <!-- Importar Device Info -->
            <div style="margin-bottom: 1rem;">
                <div x-show="!deviceInfo">
                    <div x-show="!showPasteArea" style="background: white; border: 2px dashed #d1d5db; border-radius: 0.75rem; padding: 1.25rem; text-align: center; cursor: pointer; transition: border-color 0.2s;"
                         @click="showPasteArea = true"
                         onmouseover="this.style.borderColor='#6366f1'" onmouseout="this.style.borderColor='#d1d5db'">
                        <svg style="width: 2rem; height: 2rem; color: #9ca3af; margin: 0 auto 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p style="font-size: 0.8rem; font-weight: 600; color: #374151; margin: 0;">Importar Device Info (3uTools)</p>
                        <p style="font-size: 0.7rem; color: #9ca3af; margin: 0.25rem 0 0;">Clique para colar o texto do Device Report</p>
                    </div>
                    <div x-show="showPasteArea" x-transition style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f3f4f6;">
                            <span style="font-size: 0.8rem; font-weight: 600; color: #374151;">Cole o texto do 3uTools abaixo</span>
                            <button @click="showPasteArea = false; pasteText = ''" type="button" style="width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e5e7eb; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 0.75rem;">✕</button>
                        </div>
                        <div style="padding: 0.75rem 1rem;">
                            <textarea x-model="pasteText" x-ref="pasteArea"
                                      placeholder="Cole aqui o conteudo do Device Report ou Device Info do 3uTools..."
                                      style="width: 100%; height: 120px; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.75rem; font-family: monospace; resize: vertical; box-sizing: border-box;"
                                      onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                                      onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"></textarea>
                            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.5rem;">
                                <button @click="showPasteArea = false; pasteText = ''" type="button"
                                        style="padding: 0.375rem 0.75rem; font-size: 0.75rem; color: #6b7280; background: white; border: 1px solid #d1d5db; border-radius: 0.375rem; cursor: pointer;">
                                    Cancelar
                                </button>
                                <button @click="processPastedText()" type="button"
                                        style="padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: white; background: #4f46e5; border: none; border-radius: 0.375rem; cursor: pointer;"
                                        onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">
                                    Processar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Device Info Card -->
                <div x-show="deviceInfo" x-transition style="background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%); border: 1px solid #c7d2fe; border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.875rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(199,210,254,0.5);">
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-size: 1rem;">📱</span>
                            <span style="font-size: 0.8rem; font-weight: 700; color: #312e81;" x-text="deviceInfo?.modelName || 'Dispositivo'"></span>
                            <span x-show="deviceInfo?.capacity" style="font-size: 0.65rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #c7d2fe; color: #4338ca;" x-text="deviceInfo?.capacity"></span>
                            <span x-show="deviceInfo?.color" style="font-size: 0.65rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #e0e7ff; color: #4338ca;" x-text="deviceInfo?.color"></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span x-show="importMessage" x-transition style="font-size: 0.65rem; font-weight: 600; color: #059669; background: #dcfce7; padding: 0.125rem 0.5rem; border-radius: 9999px;" x-text="importMessage"></span>
                            <button @click="removeReport()" type="button" style="width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e5e7eb; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 0.75rem;" title="Remover report">✕</button>
                        </div>
                    </div>

                    <!-- Identificacao principal -->
                    <div style="padding: 0.75rem 1.25rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem 1rem;">
                        <template x-for="field in _cardFields('identity')">
                            <div x-show="field.show">
                                <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;" x-text="field.label"></span>
                                <p style="font-size: 0.75rem; font-weight: 600; margin: 0;" :style="(field.mono ? 'font-family:monospace;' : '') + 'color:' + (field.color || '#111827')" x-text="field.value"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Status e Verificacoes -->
                    <template x-if="_cardFields('status').some(f => f.show)">
                        <div style="padding: 0 1.25rem 0.75rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem 1rem; border-top: 1px solid rgba(199,210,254,0.3); padding-top: 0.75rem;">
                            <template x-for="field in _cardFields('status')">
                                <div x-show="field.show">
                                    <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;" x-text="field.label"></span>
                                    <p style="font-size: 0.75rem; font-weight: 600; margin: 0;" :style="'color:' + (field.color || '#111827')" x-text="field.value"></p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Rede e SIM -->
                    <template x-if="_cardFields('network').some(f => f.show)">
                        <div style="padding: 0 1.25rem 0.75rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem 1rem; border-top: 1px solid rgba(199,210,254,0.3); padding-top: 0.75rem;">
                            <template x-for="field in _cardFields('network')">
                                <div x-show="field.show">
                                    <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;" x-text="field.label"></span>
                                    <p style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0;" :style="field.mono ? 'font-family:monospace;font-size:0.65rem;' : ''" x-text="field.value"></p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Hardware e SNs -->
                    <template x-if="_cardFields('hardware').some(f => f.show)">
                        <div style="padding: 0 1.25rem 0.75rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem 1rem; border-top: 1px solid rgba(199,210,254,0.3); padding-top: 0.75rem;">
                            <template x-for="field in _cardFields('hardware')">
                                <div x-show="field.show">
                                    <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;" x-text="field.label"></span>
                                    <p style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0;" :style="field.mono ? 'font-family:monospace;font-size:0.65rem;word-break:break-all;' : ''" x-text="field.value"></p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Componentes / Seriais de Pecas -->
                    <template x-if="_cardFields('components').some(f => f.show)">
                        <div style="padding: 0 1.25rem 0.75rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem 1rem; border-top: 1px solid rgba(199,210,254,0.3); padding-top: 0.75rem;">
                            <template x-for="field in _cardFields('components')">
                                <div x-show="field.show">
                                    <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;" x-text="field.label"></span>
                                    <p style="font-size: 0.75rem; font-weight: 600; color: #111827; margin: 0; font-family: monospace; font-size: 0.65rem; word-break: break-all;" x-text="field.value"></p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Face ID e Sensores -->
                    <template x-if="_cardFields('sensors').some(f => f.show)">
                        <div style="padding: 0 1.25rem 0.75rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem 1rem; border-top: 1px solid rgba(199,210,254,0.3); padding-top: 0.75rem;">
                            <template x-for="field in _cardFields('sensors')">
                                <div x-show="field.show">
                                    <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;" x-text="field.label"></span>
                                    <p style="font-size: 0.75rem; font-weight: 600; margin: 0;" :style="'color:' + (field.color || '#111827')" x-text="field.value"></p>
                                </div>
                            </template>
                        </div>
                    </template>
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
            showPasteArea: false,
            pasteText: '',
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
                            { id: 'sn_match', label: 'Numero de serie confere', hint: 'Ajustes > Geral > Sobre', status: '' },
                            { id: 'id_lock', label: 'iCloud deslogado (Buscar iPhone OFF)', hint: 'Ajustes > [nome] > Buscar > Buscar iPhone', status: '' },
                            { id: 'sim_lock', label: 'Sem bloqueio de operadora', hint: 'Testar chip de operadora diferente', status: '' },
                            { label: 'Sem MDM / perfil corporativo', hint: 'Ajustes > Geral > VPN e Gerenciamento', status: '' },
                            { label: 'Sem relato de roubo/furto', hint: 'Consultar Anatel (Celular Legal)', status: '' },
                        ]
                    }]
                },
                {
                    icon: '📱', title: 'Tela e Touch', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'Tela sem trincas ou rachaduras', hint: '', status: '' },
                            { label: 'Touch funciona em toda a tela', hint: 'Digitar em todos os cantos do teclado', status: '' },
                            { label: 'Sem pixels mortos ou manchas', hint: 'Abrir imagem branca em tela cheia', status: '' },
                            { label: 'True Tone funcionando', hint: 'Ajustes > Tela e Brilho > True Tone', status: '' },
                            { id: 'screen_sn', label: 'Tela original (sem troca)', hint: 'Ajustes > Geral > Sobre: Pecas e Servico', status: '' },
                        ]
                    }]
                },
                {
                    icon: '🔓', title: 'Face ID', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'face_id', label: 'Face ID funcionando', hint: 'Cadastrar e testar desbloqueio', status: '' },
                        ]
                    }]
                },
                {
                    icon: '📷', title: 'Cameras', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'rear_camera', label: 'Foto traseira OK (todas as lentes)', hint: 'Testar 0.5x, 1x, 2x/3x/5x', status: '' },
                            { id: 'front_camera', label: 'Foto frontal OK', hint: '', status: '' },
                            { label: 'Flash / Lanterna OK', hint: '', status: '' },
                            { label: 'Video grava e reproduz', hint: 'Gravar 10s e verificar audio', status: '' },
                        ]
                    }]
                },
                {
                    icon: '🔊', title: 'Audio e Chamada', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'Ligacao funcional (ouve e fala)', hint: 'Testar auricular + microfone', status: '' },
                            { label: 'Alto-falantes OK (som estereo)', hint: 'Tocar musica no volume maximo', status: '' },
                            { label: 'Microfone OK (gravador de voz)', hint: 'Gravar e reproduzir', status: '' },
                        ]
                    }]
                },
                {
                    icon: '🔘', title: 'Botoes', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'Volume + / Volume -', hint: '', status: '' },
                            { label: 'Botao Lateral (Power)', hint: '', status: '' },
                            { label: 'Botao Acao / Silencioso', hint: 'Action Button ou chave mute', status: '' },
                            { label: 'Vibracao (Taptic Engine)', hint: 'Ativar modo silencioso', status: '' },
                        ]
                    }]
                },
                {
                    icon: '🔋', title: 'Bateria', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'battery_health', label: 'Saude da bateria verificada', hint: 'Ajustes > Bateria > Saude', status: '' },
                            { id: 'battery_above_80', label: 'Capacidade acima de 80%', hint: 'Abaixo de 80% = troca recomendada', status: '' },
                            { id: 'battery_sn', label: 'Bateria original', hint: 'Ajustes > Geral > Sobre: Pecas e Servico', status: '' },
                            { label: 'Carrega via cabo', hint: '', status: '' },
                        ]
                    }]
                },
                {
                    icon: '📶', title: 'Conectividade', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'wifi', label: 'Wi-Fi funcional', hint: 'Conectar e abrir site', status: '' },
                            { id: 'bluetooth', label: 'Bluetooth funcional', hint: 'Parear com fone', status: '' },
                            { id: 'cellular', label: 'Dados moveis (4G/5G)', hint: 'Desligar Wi-Fi e navegar', status: '' },
                            { label: 'GPS funcional', hint: 'Abrir Maps e verificar posicao', status: '' },
                        ]
                    }]
                },
                {
                    icon: '🔍', title: 'Inspecao Fisica', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'Traseira sem danos', hint: '', status: '' },
                            { label: 'Laterais sem amassados', hint: 'Verificar cantos e quinas', status: '' },
                            { label: 'Sem gap entre tela e moldura', hint: 'Sinal de bateria inchada', status: '' },
                            { label: 'Porta USB-C limpa', hint: 'Sem oxidacao ou pinos tortos', status: '' },
                            { label: 'Indicador de liquido OK', hint: 'Dentro da bandeja SIM — vermelho = dano', status: '' },
                            { label: 'Lentes das cameras sem riscos', hint: '', status: '' },
                        ]
                    }]
                },
                {
                    icon: '⚙️', title: 'Software', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { id: 'jailbreak', label: 'Sem jailbreak', hint: 'Sem Cydia ou Sileo', status: '' },
                            { label: 'Pecas e servico limpo', hint: 'Ajustes > Geral > Sobre', status: '' },
                            { label: 'Restaurado de fabrica', hint: 'Aparelho zerado', status: '' },
                        ]
                    }]
                },
            ],

            // --- Card fields ---

            _cardFields(group) {
                const d = this.deviceInfo;
                if (!d) return [];
                const yesNo = (v) => v === 'Yes' ? '#059669' : (v === 'No' ? '#dc2626' : '#111827');
                const statusColor = (v) => {
                    if (!v) return '#111827';
                    const low = v.toLowerCase();
                    if (['normal','yes','no','off','unlocked','activated'].includes(low)) return '#059669';
                    if (['on','locked','abnormal'].includes(low)) return '#dc2626';
                    if (['unknown','unknow','pending inspection','no testing'].includes(low)) return '#d97706';
                    return '#111827';
                };

                const groups = {
                    identity: [
                        { show: d.iosVersion, label: 'iOS', value: d.iosVersion + (d.buildVersion ? ' (' + d.buildVersion + ')' : '') },
                        { show: d.serialNumber, label: 'Serial', value: d.serialNumber, mono: true },
                        { show: d.imei, label: 'IMEI', value: d.imei, mono: true },
                        { show: d.imei2, label: 'IMEI 2', value: d.imei2, mono: true },
                        { show: d.region, label: 'Regiao', value: d.region },
                        { show: d.modelNumber, label: 'Modelo', value: d.modelNumber + (d.productType ? ' (' + d.productType + ')' : '') },
                        { show: d.regulatoryModel, label: 'Homologacao', value: d.regulatoryModel },
                        { show: d.batteryLife, label: 'Bateria', value: d.batteryLife + '%' + (d.chargeCycles ? ' / ' + d.chargeCycles + ' ciclos' : ''), color: parseInt(d.batteryLife) >= 80 ? '#059669' : '#dc2626' },
                    ],
                    status: [
                        { show: d.activation, label: 'Ativacao', value: d.activation, color: statusColor(d.activation) },
                        { show: d.jailbreak, label: 'Jailbreak', value: d.jailbreak, color: d.jailbreak === 'No' ? '#059669' : '#dc2626' },
                        { show: d.snMatch !== undefined && d.snMatch !== '', label: 'SN Match', value: d.snMatch, color: yesNo(d.snMatch) },
                        { show: d.fiveCodeMatch !== undefined && d.fiveCodeMatch !== '', label: '5-Code', value: d.fiveCodeMatch, color: yesNo(d.fiveCodeMatch) },
                        { show: d.simLock, label: 'SIM Lock', value: d.simLock, color: statusColor(d.simLock) },
                        { show: d.idLock, label: 'ID Lock', value: d.idLock, color: statusColor(d.idLock) },
                        { show: d.mfgDate, label: 'Fabricacao', value: d.mfgDate },
                        { show: d.warrantyPeriod, label: 'Garantia', value: d.warrantyPeriod },
                    ],
                    network: [
                        { show: d.phoneNumber, label: 'Telefone', value: d.phoneNumber },
                        { show: d.simStatus, label: 'SIM Status', value: d.simStatus },
                        { show: d.simTrayStatus, label: 'Bandeja SIM', value: d.simTrayStatus },
                        { show: d.bluetoothAddress, label: 'Bluetooth', value: d.bluetoothAddress, mono: true },
                        { show: d.wifiAddress, label: 'Wi-Fi', value: d.wifiAddress, mono: true },
                        { show: d.cellularAddress, label: 'Cellular', value: d.cellularAddress, mono: true },
                        { show: d.iccid, label: 'ICCID', value: d.iccid, mono: true },
                        { show: d.iccid2, label: 'ICCID 2', value: d.iccid2, mono: true },
                    ],
                    hardware: [
                        { show: d.hardwareModel, label: 'Hardware', value: d.hardwareModel },
                        { show: d.cpuArchitecture, label: 'CPU', value: d.cpuArchitecture },
                        { show: d.basebandVersion, label: 'Baseband', value: d.basebandVersion },
                        { show: d.firmwareVersion, label: 'Firmware', value: d.firmwareVersion },
                        { show: d.mlbSerialNumber, label: 'MLB Serial', value: d.mlbSerialNumber, mono: true },
                        { show: d.wirelessBoardSN, label: 'Wireless Board', value: d.wirelessBoardSN, mono: true },
                        { show: d.udid, label: 'UDID', value: d.udid, mono: true },
                        { show: d.deviceName, label: 'Nome Dispositivo', value: d.deviceName },
                        { show: d.timeZone, label: 'Fuso Horario', value: d.timeZone },
                    ],
                    components: [
                        { show: d.batterySN, label: 'Bateria SN', value: d.batterySN },
                        { show: d.frontCameraSN, label: 'Camera Frontal SN', value: d.frontCameraSN },
                        { show: d.rearCameraSN, label: 'Camera Traseira SN', value: d.rearCameraSN },
                        { show: d.lidarSN, label: 'LiDAR SN', value: d.lidarSN },
                        { show: d.screenSN, label: 'Tela SN', value: d.screenSN },
                    ],
                    sensors: [
                        { show: d.faceId, label: 'Face ID', value: d.faceId, color: statusColor(d.faceId) },
                        { show: d.infraredCamera, label: 'Infrared Camera', value: d.infraredCamera, color: statusColor(d.infraredCamera) },
                        { show: d.dotProjector, label: 'Dot Projector', value: d.dotProjector, color: statusColor(d.dotProjector) },
                        { show: d.distanceSensor, label: 'Distance Sensor', value: d.distanceSensor, color: statusColor(d.distanceSensor) },
                    ],
                };
                return groups[group] || [];
            },

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
                this.pasteText = '';
                this.showPasteArea = false;
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

            _setItemHint(id, hintText) {
                const item = this.findItem(id);
                if (!item) return;
                item._originalHint = item._originalHint ?? item.hint;
                item.hint = hintText;
            },

            processPastedText() {
                if (!this.pasteText.trim()) {
                    alert('Cole o texto do Device Report ou Device Info do 3uTools.');
                    return;
                }
                const text = this.pasteText.trim();
                const isTabularReport = text.includes('Device Information') && text.includes('Test Items');
                const parsed = isTabularReport ? this._parseTabularReport(text) : this._parseDeviceInfoDump(text);
                if (!parsed) {
                    alert('Nao foi possivel interpretar o texto. Verifique se e um Device Report do 3uTools.');
                    return;
                }
                this._applyParsed(parsed);
                this.showPasteArea = false;
                this.pasteText = '';
            },

            _PRODUCT_TYPE_MAP: {
                'iPhone17,5': 'iPhone 16e',
                'iPhone17,4': 'iPhone 16 Plus',
                'iPhone17,3': 'iPhone 16',
                'iPhone17,2': 'iPhone 16 Pro Max',
                'iPhone17,1': 'iPhone 16 Pro',
                'iPhone16,2': 'iPhone 15 Pro Max',
                'iPhone16,1': 'iPhone 15 Pro',
                'iPhone15,5': 'iPhone 15 Plus',
                'iPhone15,4': 'iPhone 15',
                'iPhone15,3': 'iPhone 14 Pro Max',
                'iPhone15,2': 'iPhone 14 Pro',
                'iPhone14,8': 'iPhone 14 Plus',
                'iPhone14,7': 'iPhone 14',
                'iPhone14,5': 'iPhone 13',
                'iPhone14,4': 'iPhone 13 mini',
                'iPhone14,3': 'iPhone 13 Pro Max',
                'iPhone14,2': 'iPhone 13 Pro',
                'iPhone13,4': 'iPhone 12 Pro Max',
                'iPhone13,3': 'iPhone 12 Pro',
                'iPhone13,2': 'iPhone 12',
                'iPhone13,1': 'iPhone 12 mini',
                'iPhone12,5': 'iPhone 11 Pro Max',
                'iPhone12,3': 'iPhone 11 Pro',
                'iPhone12,1': 'iPhone 11',
            },

            _DEVICE_COLOR_MAP: {
                '1': 'Preto', '2': 'Branco', '3': 'Dourado', '4': 'Azul',
                '5': 'Titanio Natural', '6': 'Titanio Preto', '7': 'Titanio Branco',
                '8': 'Titanio Deserto', '9': 'Verde', '10': 'Roxo', '11': 'Vermelho',
                '12': 'Rosa', '13': 'Amarelo',
            },

            _parseDeviceInfoDump(text) {
                const lines = text.split(/\r?\n/).filter(l => l.trim());
                const kv = {};
                for (const line of lines) {
                    const match = line.match(/^(\S+)\s{2,}(.+)$/);
                    if (match) {
                        kv[match[1].trim()] = match[2].trim();
                    }
                }
                if (!kv.ProductType && !kv.SerialNumber) return null;

                const modelName = this._PRODUCT_TYPE_MAP[kv.ProductType] || kv.ProductType || '';
                const imei = kv.InternationalMobileEquipmentIdentity || '';
                const serial = kv.SerialNumber || '';
                const colorCode = kv.DeviceEnclosureColor || kv.DeviceColor || '';
                const colorName = this._DEVICE_COLOR_MAP[colorCode] || '';

                return {
                    deviceInfo: {
                        modelName,
                        capacity: '',
                        color: colorName,
                        iosVersion: kv.ProductVersion || kv.HumanReadableProductVersionString || '',
                        buildVersion: kv.BuildVersion || '',
                        batteryLife: '',
                        chargeCycles: '',
                        serialNumber: serial,
                        region: kv.RegionInfo || '',
                        imei,
                        imei2: kv.InternationalMobileEquipmentIdentity2 || '',
                        activation: kv.ActivationState || '',
                        modelNumber: kv.ModelNumber || '',
                        productType: kv.ProductType || '',
                        bluetoothAddress: kv.BluetoothAddress || '',
                        wifiAddress: kv.WiFiAddress || '',
                        mlbSerialNumber: kv.MLBSerialNumber || '',
                        phoneNumber: kv.PhoneNumber || '',
                        deviceName: kv.DeviceName || '',
                        basebandVersion: kv.BasebandVersion || '',
                        firmwareVersion: kv.FirmwareVersion || '',
                        cpuArchitecture: kv.CPUArchitecture || '',
                        simStatus: kv.SIMStatus ? kv.SIMStatus.replace('kCTSIMSupportSIMStatus', '') : '',
                        simTrayStatus: kv.SIMTrayStatus ? kv.SIMTrayStatus.replace('kCTSIMSupportSIMTray', '') : '',
                        iccid: kv.IntegratedCircuitCardIdentity || '',
                        iccid2: kv.IntegratedCircuitCardIdentity2 || '',
                        udid: kv.UniqueDeviceID || '',
                        wirelessBoardSN: kv.WirelessBoardSerialNumber || '',
                        timeZone: kv.TimeZone || '',
                        hardwareModel: kv.HardwareModel || '',
                    },
                    autoChecks: { isDump: true, imei, serial },
                };
            },

            _parseTabularReport(text) {
                const lines = text.split(/\r?\n/).filter(l => l.trim());
                const device = {};
                const tests = {};
                const extras = {};

                let section = '';
                for (const line of lines) {
                    const parts = line.split(/\t+/).map(s => s.trim()).filter(Boolean);
                    if (parts.length === 0) continue;
                    if (parts[0] === 'Device Information') { section = 'device'; continue; }
                    if (parts[0] === 'Test Items' || parts[0] === 'Ex-factory Value') { section = 'tests'; continue; }

                    if (section === 'device' && parts.length >= 2) {
                        const key = parts[0].trim();
                        const val = parts.slice(1).join(' ').trim();
                        const keyMap = {
                            'Model Identifier': 'modelIdentifier', 'iOS Version': 'iosVersion',
                            'Activation': 'activation', 'Jailbreak': 'jailbreak',
                            'SN Match': 'snMatch', '5-Code Match': 'fiveCodeMatch',
                            'SIM Lock': 'simLock', 'ID Lock': 'idLock',
                            'Battery Life': 'batteryLife', 'Charge Cycles': 'chargeCycles',
                            'Mfg. Date': 'mfgDate', 'Warranty Period': 'warrantyPeriod',
                        };
                        if (keyMap[key]) device[keyMap[key]] = val;
                        continue;
                    }

                    if (section === 'tests') {
                        const keyMap = {
                            'Model Name': 'modelName', 'Device Color': 'deviceColor',
                            'Capacity': 'capacity', 'Model Number': 'modelNumber',
                            'Sales Region': 'salesRegion', 'Regulatory Model': 'regulatoryModel',
                            'Serial Number': 'serialNumber',
                            'Logic Board SN': 'logicBoardSN', 'Battery SN': 'batterySN',
                            'Front Camera': 'frontCamera', 'Rear Camera': 'rearCamera',
                            'Screen SN': 'screenSN', 'LiDAR': 'lidar',
                            'Bluetooth': 'bluetooth', 'Cellular Address': 'cellular',
                            'Wi-Fi Address': 'wifi',
                        };
                        const faceIdKeys = ['Face ID', 'Infrared Camera', 'Dot Projector', 'Distance Senror', 'Distance Sensor'];
                        for (const fk of faceIdKeys) {
                            const fkIdx = line.indexOf(fk);
                            if (fkIdx > -1) {
                                const after = line.substring(fkIdx + fk.length).split(/\t+/).map(s => s.trim()).filter(Boolean);
                                const fKeyMap = { 'Face ID': 'faceId', 'Infrared Camera': 'infraredCamera', 'Dot Projector': 'dotProjector', 'Distance Senror': 'distanceSensor', 'Distance Sensor': 'distanceSensor' };
                                extras[fKeyMap[fk]] = after[0] || 'Pending Inspection';
                            }
                        }
                        const key = parts[0].trim();
                        if (keyMap[key]) {
                            if (parts.length >= 4) tests[keyMap[key]] = { factory: parts[1], read: parts[2], result: parts[3] };
                            else if (parts.length >= 2) tests[keyMap[key]] = { factory: parts[1] || '', read: '', result: parts[parts.length - 1] };
                        }
                    }
                }
                if (!device.modelIdentifier && !tests.modelName) return null;

                const iosRaw = device.iosVersion || '';
                const iosBuildMatch = iosRaw.match(/^([\d.]+)\(([^)]+)\)$/);
                const iosVersion = iosBuildMatch ? iosBuildMatch[1] : iosRaw;
                const buildVersion = iosBuildMatch ? iosBuildMatch[2] : '';

                const readVal = (t) => t?.read || t?.factory || '';
                const snVal = (t) => t?.read || t?.factory || '';

                return {
                    deviceInfo: {
                        modelName: readVal(tests.modelName) || device.modelIdentifier,
                        capacity: readVal(tests.capacity),
                        color: (readVal(tests.deviceColor)).replace(/\u00a3\u00ac/g, ', '),
                        iosVersion,
                        buildVersion,
                        batteryLife: (device.batteryLife || '').replace('%', ''),
                        chargeCycles: device.chargeCycles || '',
                        serialNumber: readVal(tests.serialNumber),
                        region: readVal(tests.salesRegion),
                        imei: '',
                        snMatch: device.snMatch,
                        fiveCodeMatch: device.fiveCodeMatch,
                        activation: device.activation || '',
                        idLock: device.idLock,
                        simLock: device.simLock || '',
                        jailbreak: device.jailbreak || '',
                        productType: device.modelIdentifier || '',
                        modelNumber: readVal(tests.modelNumber),
                        regulatoryModel: readVal(tests.regulatoryModel),
                        mlbSerialNumber: snVal(tests.logicBoardSN),
                        batterySN: snVal(tests.batterySN),
                        frontCameraSN: snVal(tests.frontCamera),
                        rearCameraSN: snVal(tests.rearCamera),
                        screenSN: snVal(tests.screenSN),
                        lidarSN: snVal(tests.lidar),
                        bluetoothAddress: readVal(tests.bluetooth),
                        wifiAddress: readVal(tests.wifi),
                        cellularAddress: readVal(tests.cellular),
                        mfgDate: device.mfgDate || '',
                        warrantyPeriod: device.warrantyPeriod || '',
                        faceId: extras.faceId || '',
                        infraredCamera: extras.infraredCamera || '',
                        dotProjector: extras.dotProjector || '',
                        distanceSensor: extras.distanceSensor || '',
                    },
                    autoChecks: { device, tests, extras },
                };
            },

            _applyParsed(parsed) {
                this.deviceInfo = parsed.deviceInfo;
                let filled = 0;
                const ok = (id, hint) => { this._setItem(id, 'ok', hint); filled++; };
                const fail = (id, hint) => { this._setItem(id, 'fail', hint); filled++; };
                const ac = parsed.autoChecks;

                if (ac.isDump) {
                    if (ac.imei) this._setItemHint('imei', 'IMEI: ' + ac.imei + ' — conferir com *#06#');
                    if (ac.serial) this._setItemHint('sn_match', 'SN: ' + ac.serial + ' — conferir Ajustes > Geral > Sobre');
                }

                if (ac.device) {
                    const d = ac.device;
                    if (d.snMatch === 'Yes') ok('sn_match', '3uTools: SN Match OK');
                    else if (d.snMatch === 'No') fail('sn_match', '3uTools: SN NAO confere!');

                    if (d.jailbreak === 'No') ok('jailbreak', '3uTools: Sem jailbreak');
                    else if (d.jailbreak === 'Yes') fail('jailbreak', '3uTools: JAILBREAK detectado!');

                    if (d.idLock === 'Off' || d.idLock === 'OFF') ok('id_lock', '3uTools: ID Lock OFF');
                    else if (d.idLock === 'On' || d.idLock === 'ON') fail('id_lock', '3uTools: ID Lock ATIVO!');

                    if (d.simLock === 'Unlocked' || d.simLock === 'No') ok('sim_lock', '3uTools: Desbloqueado');
                    else if (d.simLock === 'Locked') fail('sim_lock', '3uTools: Bloqueado por operadora!');

                    const battPct = parseInt(d.batteryLife);
                    if (!isNaN(battPct)) {
                        ok('battery_health', '3uTools: ' + battPct + '%');
                        if (battPct >= 80) ok('battery_above_80', '3uTools: ' + battPct + '%');
                        else fail('battery_above_80', '3uTools: ' + battPct + '% — abaixo de 80%');
                    }
                }

                if (ac.tests) {
                    const t = ac.tests;
                    if (t.batterySN?.result === 'Normal') ok('battery_sn', '3uTools: SN original');
                    else if (t.batterySN?.result) fail('battery_sn', '3uTools: ' + t.batterySN.result);
                    if (t.screenSN?.result === 'Normal') ok('screen_sn', '3uTools: Tela original');
                    else if (t.screenSN?.result) fail('screen_sn', '3uTools: ' + t.screenSN.result);
                    if (t.frontCamera?.result === 'Normal') ok('front_camera', '3uTools: SN original');
                    if (t.rearCamera?.result === 'Normal') ok('rear_camera', '3uTools: SN original');
                    if (t.bluetooth?.result === 'Normal') ok('bluetooth', '3uTools: Normal');
                    if (t.wifi?.result === 'Normal') ok('wifi', '3uTools: Normal');
                    if (t.cellular?.result === 'Normal') ok('cellular', '3uTools: Normal');
                }

                if (ac.extras?.faceId === 'Normal') ok('face_id', '3uTools: Face ID Normal');

                const remaining = this.totalCount - this.checkedCount;
                this.importMessage = filled > 0
                    ? filled + ' itens preenchidos, ' + remaining + ' para verificar'
                    : 'Dispositivo identificado, ' + remaining + ' itens para verificar';
            },

            removeReport() {
                this.deviceInfo = null;
                this.importMessage = '';
                this.pasteText = '';
                this.showPasteArea = false;
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
                const d = this.deviceInfo;

                if (d) {
                    lines.push('📱 *' + d.modelName + '*' + (d.capacity ? ' ' + d.capacity : '') + (d.color ? ' — ' + d.color : ''));
                    if (d.iosVersion) lines.push('⚙️ iOS ' + d.iosVersion + (d.buildVersion ? ' (' + d.buildVersion + ')' : ''));
                    if (d.batteryLife) lines.push('🔋 Bateria: ' + d.batteryLife + '%' + (d.chargeCycles ? ' / ' + d.chargeCycles + ' ciclos' : ''));
                    if (d.serialNumber) lines.push('🔢 Serial: ' + d.serialNumber);
                    if (d.imei) lines.push('📟 IMEI: ' + d.imei);
                    if (d.imei2) lines.push('📟 IMEI 2: ' + d.imei2);
                    if (d.region) lines.push('🌎 Regiao: ' + d.region);
                    if (d.modelNumber) lines.push('🏷️ Modelo: ' + d.modelNumber + (d.regulatoryModel ? ' / ' + d.regulatoryModel : '') + (d.productType ? ' (' + d.productType + ')' : ''));
                    if (d.activation) lines.push('🔓 Ativacao: ' + d.activation);
                    if (d.jailbreak) lines.push((d.jailbreak === 'No' ? '✅' : '❌') + ' Jailbreak: ' + d.jailbreak);
                    if (d.snMatch) lines.push((d.snMatch === 'Yes' ? '✅' : '❌') + ' SN Match: ' + d.snMatch);
                    if (d.fiveCodeMatch) lines.push((d.fiveCodeMatch === 'Yes' ? '✅' : '❌') + ' 5-Code: ' + d.fiveCodeMatch);
                    if (d.simLock) lines.push('🔒 SIM Lock: ' + d.simLock);
                    if (d.idLock) lines.push('🔒 ID Lock: ' + d.idLock);
                    lines.push('');
                    const hasNet = d.phoneNumber || d.bluetoothAddress || d.wifiAddress || d.cellularAddress;
                    if (hasNet) {
                        if (d.phoneNumber) lines.push('📞 Tel: ' + d.phoneNumber);
                        if (d.bluetoothAddress) lines.push('🔵 BT: ' + d.bluetoothAddress);
                        if (d.wifiAddress) lines.push('📡 Wi-Fi: ' + d.wifiAddress);
                        if (d.cellularAddress) lines.push('📶 Cellular: ' + d.cellularAddress);
                        lines.push('');
                    }
                    const hasSN = d.mlbSerialNumber || d.batterySN || d.frontCameraSN || d.rearCameraSN;
                    if (hasSN) {
                        if (d.mlbSerialNumber) lines.push('🔧 MLB: ' + d.mlbSerialNumber);
                        if (d.batterySN) lines.push('🔋 Bateria SN: ' + d.batterySN);
                        if (d.frontCameraSN) lines.push('📷 Cam Frontal: ' + d.frontCameraSN);
                        if (d.rearCameraSN) lines.push('📷 Cam Traseira: ' + d.rearCameraSN);
                        if (d.lidarSN) lines.push('📐 LiDAR: ' + d.lidarSN);
                        lines.push('');
                    }
                    const hasFace = d.faceId || d.infraredCamera || d.dotProjector || d.distanceSensor;
                    if (hasFace) {
                        if (d.faceId) lines.push('🔓 Face ID: ' + d.faceId);
                        if (d.infraredCamera) lines.push('  Infrared Camera: ' + d.infraredCamera);
                        if (d.dotProjector) lines.push('  Dot Projector: ' + d.dotProjector);
                        if (d.distanceSensor) lines.push('  Distance Sensor: ' + d.distanceSensor);
                        lines.push('');
                    }
                    if (d.udid) lines.push('🆔 UDID: ' + d.udid);
                    if (d.udid) lines.push('');
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
