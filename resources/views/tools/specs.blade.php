<x-app-layout>
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" x-data="specsApp()">

            <!-- Header + Seletores -->
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div>
                        <h1 style="font-size: 1.375rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width:1.5rem;height:1.5rem;" viewBox="0 0 24 24" fill="#111827"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                            Ficha T&eacute;cnica Apple
                        </h1>
                        <p style="font-size: 0.8125rem; color: #6b7280;">Compare especifica&ccedil;&otilde;es entre modelos</p>
                    </div>
                    <button x-show="model1 && model2" @click="swapModels()" type="button" title="Inverter modelos"
                            style="padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; background: white; cursor: pointer; color: #6b7280;"
                            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </button>
                </div>
                <div style="display: flex; gap: 0.75rem; align-items: end; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Modelo 1</label>
                        <select x-model="selected1" style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; font-size: 0.875rem; background: white; outline: none;" onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            <option value="">Selecione...</option>
                            <template x-for="(cat, catName) in groupedModels" :key="catName">
                                <optgroup :label="catName">
                                    <template x-for="m in cat" :key="m.name">
                                        <option :value="m.name" x-text="m.name"></option>
                                    </template>
                                </optgroup>
                            </template>
                        </select>
                    </div>
                    <div style="padding-bottom: 0.5rem;">
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #d1d5db;">VS</span>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label style="font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Modelo 2 (comparar)</label>
                        <select x-model="selected2" style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; font-size: 0.875rem; background: white; outline: none;" onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            <option value="">Nenhum</option>
                            <template x-for="(cat, catName) in groupedModels" :key="catName">
                                <optgroup :label="catName">
                                    <template x-for="m in cat" :key="m.name">
                                        <option :value="m.name" x-text="m.name"></option>
                                    </template>
                                </optgroup>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Estado vazio -->
            <div x-show="!model1" style="text-align: center; padding: 4rem 1rem; color: #9ca3af;">
                <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.75rem; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p style="font-size: 0.875rem;">Selecione um modelo para ver as especifica&ccedil;&otilde;es</p>
            </div>

            <!-- Tabela de Comparação -->
            <div x-show="model1" x-transition style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; overflow: hidden;">
                <!-- Cabeçalho com nomes dos modelos -->
                <div :style="model2
                    ? 'display: grid; grid-template-columns: minmax(120px, 0.8fr) 1fr 1fr;'
                    : 'display: grid; grid-template-columns: minmax(120px, 0.8fr) 1fr;'">
                    <div style="padding: 1rem 1.25rem; background: #f9fafb; border-bottom: 2px solid #e5e7eb; border-right: 1px solid #e5e7eb;">
                        <span style="font-size: 0.6875rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Especifica&ccedil;&atilde;o</span>
                    </div>
                    <div style="padding: 1rem 1.25rem; background: #111827; border-bottom: 2px solid #111827;">
                        <template x-if="model1">
                            <div>
                                <div style="font-size: 1rem; font-weight: 700; color: white;" x-text="model1.name"></div>
                                <div style="font-size: 0.75rem; color: #9ca3af;" x-text="model1.year"></div>
                            </div>
                        </template>
                    </div>
                    <div x-show="model2" style="padding: 1rem 1.25rem; background: #374151; border-bottom: 2px solid #374151; border-left: 1px solid #4b5563;">
                        <template x-if="model2">
                            <div>
                                <div style="font-size: 1rem; font-weight: 700; color: white;" x-text="model2.name"></div>
                                <div style="font-size: 0.75rem; color: #9ca3af;" x-text="model2.year"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Seções e linhas -->
                <template x-for="(section, sIdx) in specSections" :key="sIdx">
                    <div>
                        <!-- Cabeçalho da seção -->
                        <div style="padding: 0.5rem 1.25rem; background: #f3f4f6; border-bottom: 1px solid #e5e7eb; border-top: 1px solid #e5e7eb;">
                            <span style="font-size: 0.6875rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;" x-text="section.label"></span>
                        </div>

                        <!-- Linhas de specs -->
                        <template x-for="(field, fIdx) in section.fields" :key="field.key">
                            <div :style="(model2
                                ? 'display: grid; grid-template-columns: minmax(120px, 0.8fr) 1fr 1fr;'
                                : 'display: grid; grid-template-columns: minmax(120px, 0.8fr) 1fr;')
                                + 'border-bottom: 1px solid #f3f4f6;'">
                                <!-- Label -->
                                <div style="padding: 0.625rem 1.25rem; font-size: 0.8125rem; color: #6b7280; border-right: 1px solid #f3f4f6; display: flex; align-items: center;" x-text="field.label"></div>
                                <!-- Valor Modelo 1 -->
                                <div style="padding: 0.625rem 1.25rem; font-size: 0.8125rem; font-weight: 500; display: flex; align-items: center; gap: 0.375rem;"
                                     :style="getCellStyle(field.key, 1)">
                                    <span x-text="model1 ? (model1[field.key] || '-') : '-'"></span>
                                    <template x-if="model2 && getComparison(field.key) === 1">
                                        <svg style="width:0.875rem;height:0.875rem;flex-shrink:0;color:#16a34a;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </template>
                                </div>
                                <!-- Valor Modelo 2 -->
                                <div x-show="model2" style="padding: 0.625rem 1.25rem; font-size: 0.8125rem; font-weight: 500; display: flex; align-items: center; gap: 0.375rem; border-left: 1px solid #f3f4f6;"
                                     :style="getCellStyle(field.key, 2)">
                                    <span x-text="model2 ? (model2[field.key] || '-') : '-'"></span>
                                    <template x-if="model2 && getComparison(field.key) === -1">
                                        <svg style="width:0.875rem;height:0.875rem;flex-shrink:0;color:#16a34a;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Legenda -->
            <div x-show="model1 && model2" style="display: flex; gap: 1.25rem; justify-content: center; margin-top: 0.75rem; font-size: 0.75rem; color: #6b7280; flex-wrap: wrap;">
                <span style="display: flex; align-items: center; gap: 0.25rem;">
                    <span style="width: 0.75rem; height: 0.75rem; border-radius: 0.25rem; background: #dcfce7; border: 1px solid #bbf7d0;"></span>
                    Melhor
                </span>
                <span style="display: flex; align-items: center; gap: 0.25rem;">
                    <span style="width: 0.75rem; height: 0.75rem; border-radius: 0.25rem; background: #fee2e2; border: 1px solid #fecaca;"></span>
                    Inferior
                </span>
                <span style="display: flex; align-items: center; gap: 0.25rem;">
                    <span style="width: 0.75rem; height: 0.75rem; border-radius: 0.25rem; background: #fefce8; border: 1px solid #fef08a;"></span>
                    Diferente
                </span>
                <span style="display: flex; align-items: center; gap: 0.25rem;">
                    <span style="width: 0.75rem; height: 0.75rem; border-radius: 0.25rem; background: white; border: 1px solid #e5e7eb;"></span>
                    Igual
                </span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/apple-specs-data.js') }}"></script>
    <script>
    function specsApp() {
        return {
            models: specsModels(),
            selected1: '',
            selected2: '',

            get groupedModels() {
                const g = {};
                this.models.forEach(m => { if (!g[m.cat]) g[m.cat] = []; g[m.cat].push(m); });
                return g;
            },
            get model1() { return this.models.find(m => m.name === this.selected1) || null; },
            get model2() { return this.models.find(m => m.name === this.selected2) || null; },

            swapModels() {
                const tmp = this.selected1;
                this.selected1 = this.selected2;
                this.selected2 = tmp;
            },

            specSections: [
                { label: 'Tela', fields: [
                    { key: 'screen', label: 'Tela' },
                    { key: 'brightness', label: 'Brilho' },
                    { key: 'refresh', label: 'Taxa atualiz.' },
                ]},
                { label: 'Desempenho', fields: [
                    { key: 'chip', label: 'Chip' },
                    { key: 'ram', label: 'RAM' },
                    { key: 'storage', label: 'Armazenamento' },
                ]},
                { label: 'C\u00e2meras', fields: [
                    { key: 'mainCam', label: 'Cam. traseira' },
                    { key: 'frontCam', label: 'Cam. frontal' },
                    { key: 'video', label: 'V\u00eddeo' },
                ]},
                { label: 'Bateria e Energia', fields: [
                    { key: 'batteryCap', label: 'Capacidade' },
                    { key: 'battery', label: 'Dura\u00e7\u00e3o' },
                    { key: 'charging', label: 'Carregamento' },
                ]},
                { label: 'Conectividade', fields: [
                    { key: 'sim', label: 'SIM' },
                    { key: 'connectivity', label: 'Conex\u00f5es' },
                    { key: 'water', label: 'Resist. \u00e1gua' },
                ]},
                { label: 'F\u00edsico', fields: [
                    { key: 'dimensions', label: 'Dimens\u00f5es' },
                    { key: 'weight', label: 'Peso' },
                    { key: 'material', label: 'Material' },
                    { key: 'biometrics', label: 'Biometria' },
                ]},
                { label: 'Destaque', fields: [
                    { key: 'highlight', label: 'Recursos' },
                ]},
            ],

            getComparison(key) {
                if (!this.model1 || !this.model2) return 0;
                const v1 = this.model1[key], v2 = this.model2[key];
                if (!v1 || !v2 || v1 === '-' || v2 === '-' || v1 === v2) return 0;

                const noRank = ['storage', 'charging', 'connectivity', 'material', 'highlight', 'sim', 'biometrics', 'dimensions', 'video'];
                if (noRank.includes(key)) return 2;

                if (key === 'chip') {
                    const r1 = this.chipRank(v1), r2 = this.chipRank(v2);
                    if (r1 === r2) return 2;
                    return r1 > r2 ? 1 : -1;
                }

                if (key === 'water') {
                    const r1 = this.waterRank(v1), r2 = this.waterRank(v2);
                    if (r1 === r2) return 2;
                    return r1 > r2 ? 1 : -1;
                }

                const n1 = this.extractNum(v1), n2 = this.extractNum(v2);
                if (n1 === null || n2 === null) return 2;
                if (n1 === n2) return 2;

                if (key === 'weight') return n1 < n2 ? 1 : -1;
                return n1 > n2 ? 1 : -1;
            },

            getCellStyle(key, modelNum) {
                if (!this.model2) return 'color: #111827;';
                const cmp = this.getComparison(key);
                if (cmp === 0) return 'color: #111827;';
                if (cmp === 2) return 'background: #fefce8; color: #92400e;';

                if (modelNum === 1) {
                    return cmp === 1
                        ? 'background: #f0fdf4; color: #166534; font-weight: 600;'
                        : 'background: #fef2f2; color: #991b1b;';
                }
                return cmp === -1
                    ? 'background: #f0fdf4; color: #166534; font-weight: 600;'
                    : 'background: #fef2f2; color: #991b1b;';
            },

            extractNum(str) {
                if (!str) return null;
                const m = str.match(/([\d.]+)/);
                return m ? parseFloat(m[1]) : null;
            },

            chipRank(chip) {
                const ranks = {
                    'A19 Pro': 100, 'A19': 95,
                    'A18 Pro': 90, 'A18 + Apple C1': 86, 'A18': 85,
                    'A17 Pro': 80, 'A16 Bionic': 75, 'A15 Bionic': 70, 'A14 Bionic': 65,
                    'Apple M4 Pro': 120, 'Apple M4': 115, 'Apple M3': 110, 'Apple M2': 105,
                    'S10 SiP': 50, 'S9 SiP': 45,
                    'H3': 30, 'H2': 25,
                };
                return ranks[chip] || 0;
            },

            waterRank(val) {
                if (!val || val === '-') return 0;
                const u = val.toUpperCase();
                if (u.includes('IP68') && u.includes('6M')) return 68.6;
                if (u.includes('IP68')) return 68;
                if (u.includes('IP54')) return 54;
                if (u.includes('IPX4')) return 4;
                if (u.includes('WR100') || u.includes('EN13319')) return 100;
                if (u.includes('WR50')) return 50;
                return 0;
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
