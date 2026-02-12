<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="checklistApp()">

            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Checklist Seminovo</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Avaliacao tecnica completa do aparelho</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
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

        </div>
    </div>

    <script>
    function checklistApp() {
        return {
            copied: false,

            sections: [
                {
                    icon: '🔐', title: 'Identidade e Bloqueios', open: true,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'IMEI confere (*#06# vs Ajustes)', hint: 'Comparar com caixa e nota fiscal', status: '' },
                            { label: 'iCloud deslogado (Buscar iPhone OFF)', hint: 'Ajustes > [nome] > Buscar > Buscar iPhone', status: '' },
                            { label: 'Sem MDM / perfil corporativo', hint: 'Ajustes > Geral > VPN e Gerenciamento', status: '' },
                            { label: 'Sem bloqueio de operadora', hint: 'Testar chip de operadora diferente', status: '' },
                            { label: 'Sem relato de roubo/furto', hint: 'Consultar na Anatel (Celular Legal)', status: '' },
                            { label: 'Numero de serie / modelo confere', hint: 'Ajustes > Geral > Sobre (letra M=novo, F=refurb, N=troca)', status: '' },
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
                            { label: 'Face ID funcionando', hint: 'Cadastrar e testar desbloqueio', status: '' },
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
                                { label: 'Foto traseira OK', hint: 'Verificar nitidez e foco automatico', status: '' },
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
                                { label: 'Foto frontal OK', hint: '', status: '' },
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
                            { label: 'Saude da bateria verificada', hint: 'Ajustes > Bateria > Saude e Carregamento', status: '' },
                            { label: 'Capacidade acima de 80%', hint: 'Abaixo de 80% = troca recomendada', status: '' },
                            { label: 'Contagem de ciclos aceitavel', hint: 'iOS 17.4+: Ajustes > Geral > Sobre > Bateria', status: '' },
                            { label: 'Bateria original (sem aviso de servico)', hint: 'Ajustes > Geral > Sobre: Pecas e Servico', status: '' },
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
                            { label: 'Wi-Fi conecta e navega', hint: 'Conectar e abrir site', status: '' },
                            { label: 'Bluetooth funcional', hint: 'Parear com fone ou outro dispositivo', status: '' },
                            { label: 'Dados moveis (4G/5G)', hint: 'Desligar Wi-Fi e navegar', status: '' },
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
                            { label: 'LiDAR (Pro/Pro Max)', hint: 'App Medida — apontar para objeto', status: '' },
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
                            { label: 'Tela original (sem troca)', hint: 'Ajustes > Geral > Sobre: Pecas e Servico', status: '' },
                        ]
                    }]
                },
                {
                    icon: '⚙️', title: 'Software e Sistema', open: false,
                    subs: [{
                        label: '',
                        items: [
                            { label: 'iOS atualizado ou atualizavel', hint: 'Ajustes > Geral > Atualizacao de Software', status: '' },
                            { label: 'Sem jailbreak', hint: 'Verificar se ha apps como Cydia ou Sileo', status: '' },
                            { label: 'Historico de pecas e servico limpo', hint: 'Ajustes > Geral > Sobre', status: '' },
                            { label: 'Aparelho restaurado de fabrica', hint: 'Garantir que o aparelho esta zerado', status: '' },
                            { label: 'Siri funcional', hint: 'Dizer "E ai, Siri"', status: '' },
                        ]
                    }]
                },
            ],

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

            toggleItem(sIdx, subIdx, iIdx, value) {
                const item = this.sections[sIdx].subs[subIdx].items[iIdx];
                item.status = item.status === value ? '' : value;
            },

            resetAll() {
                if (!confirm('Limpar todo o checklist?')) return;
                this.sections.forEach(s => s.subs.forEach(sub => sub.items.forEach(i => i.status = '')));
            },

            copySummary() {
                let lines = ['*CHECKLIST SEMINOVO - DG Store*', ''];
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
