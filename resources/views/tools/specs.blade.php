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
    <script>
    function specsApp() {
        const models = @json([]) ;
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
                ]},
                { label: 'Bateria', fields: [
                    { key: 'battery', label: 'Dura\u00e7\u00e3o' },
                    { key: 'charging', label: 'Carregamento' },
                ]},
                { label: 'Conectividade', fields: [
                    { key: 'connectivity', label: 'Conex\u00f5es' },
                    { key: 'water', label: 'Resist. \u00e1gua' },
                ]},
                { label: 'F\u00edsico', fields: [
                    { key: 'weight', label: 'Peso' },
                    { key: 'material', label: 'Material' },
                ]},
                { label: 'Destaque', fields: [
                    { key: 'highlight', label: 'Recursos' },
                ]},
            ],

            // Retorna: 1 = model1 melhor, -1 = model2 melhor, 0 = igual/não comparável, 2 = diferente mas sem ranking
            getComparison(key) {
                if (!this.model1 || !this.model2) return 0;
                const v1 = this.model1[key], v2 = this.model2[key];
                if (!v1 || !v2 || v1 === '-' || v2 === '-' || v1 === v2) return 0;

                // Campos que não são ranqueáveis
                const noRank = ['storage', 'charging', 'connectivity', 'material', 'highlight'];
                if (noRank.includes(key)) return 2;

                // Chip tem ranking próprio
                if (key === 'chip') {
                    const r1 = this.chipRank(v1), r2 = this.chipRank(v2);
                    if (r1 === r2) return 2;
                    return r1 > r2 ? 1 : -1;
                }

                // Water resistance ranking
                if (key === 'water') {
                    const r1 = this.waterRank(v1), r2 = this.waterRank(v2);
                    if (r1 === r2) return 2;
                    return r1 > r2 ? 1 : -1;
                }

                // Extrair primeiro número
                const n1 = this.extractNum(v1), n2 = this.extractNum(v2);
                if (n1 === null || n2 === null) return 2;
                if (n1 === n2) return 2;

                // Peso: menor é melhor
                if (key === 'weight') return n1 < n2 ? 1 : -1;

                // Demais: maior é melhor (tela, refresh, ram, camera, bateria)
                return n1 > n2 ? 1 : -1;
            },

            getCellStyle(key, modelNum) {
                if (!this.model2) return 'color: #111827;';
                const cmp = this.getComparison(key);
                if (cmp === 0) return 'color: #111827;';
                if (cmp === 2) return 'background: #fefce8; color: #92400e;';

                // cmp = 1: model1 melhor; cmp = -1: model2 melhor
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
                    'A17 Pro': 80, 'A16 Bionic': 75, 'A15 Bionic': 70,
                    'Apple M4 Pro': 115, 'Apple M4': 110, 'Apple M3': 105, 'Apple M2': 100,
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

    function specsModels() {
        return [
            // ── iPhone 17 Series ──
            { name: 'iPhone 17 Pro Max', cat: 'iPhone', year: '2025', screen: '6.9" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A19 Pro', ram: '12GB', mainCam: '48MP + 48MP UW + 12MP Tele 5x', frontCam: '24MP TrueDepth', battery: 'At\u00e9 33h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB, 2TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.4, UWB', water: 'IP68 (6m)', weight: '227g', material: 'Tit\u00e2nio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control' },
            { name: 'iPhone 17 Pro', cat: 'iPhone', year: '2025', screen: '6.3" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A19 Pro', ram: '12GB', mainCam: '48MP + 48MP UW + 12MP Tele 5x', frontCam: '24MP TrueDepth', battery: 'At\u00e9 27h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.4, UWB', water: 'IP68 (6m)', weight: '199g', material: 'Tit\u00e2nio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control' },
            { name: 'iPhone 17', cat: 'iPhone', year: '2025', screen: '6.1" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A19', ram: '8GB', mainCam: '48MP + 24MP UW', frontCam: '24MP TrueDepth', battery: 'At\u00e9 22h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.4', water: 'IP68', weight: '170g', material: 'Alum\u00ednio', highlight: 'Dynamic Island, Camera Control, Apple Intelligence' },
            { name: 'iPhone Air', cat: 'iPhone', year: '2025', screen: '6.6" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A19', ram: '8GB', mainCam: '48MP + 24MP UW', frontCam: '24MP TrueDepth', battery: 'At\u00e9 24h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.4', water: 'IP68', weight: '163g', material: 'Alum\u00ednio', highlight: 'Mais fino da linha, Dynamic Island, Apple Intelligence' },
            { name: 'iPhone 16e', cat: 'iPhone', year: '2025', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A18 + Apple C1', ram: '8GB', mainCam: '48MP', frontCam: '12MP TrueDepth', battery: 'At\u00e9 26h video', charging: 'USB-C, MagSafe', storage: '128GB, 256GB, 512GB', connectivity: '5G (C1), Wi-Fi 7, Bluetooth 5.3', water: 'IP68', weight: '170g', material: 'Alum\u00ednio', highlight: 'Modem Apple C1, Apple Intelligence, Face ID' },
            // ── iPhone 16 Series ──
            { name: 'iPhone 16 Pro Max', cat: 'iPhone', year: '2024', screen: '6.9" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A18 Pro', ram: '8GB', mainCam: '48MP + 48MP UW + 12MP Tele 5x', frontCam: '12MP TrueDepth', battery: 'At\u00e9 33h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.3, UWB', water: 'IP68 (6m)', weight: '227g', material: 'Tit\u00e2nio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control' },
            { name: 'iPhone 16 Pro', cat: 'iPhone', year: '2024', screen: '6.3" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A18 Pro', ram: '8GB', mainCam: '48MP + 48MP UW + 12MP Tele 5x', frontCam: '12MP TrueDepth', battery: 'At\u00e9 27h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.3, UWB', water: 'IP68 (6m)', weight: '199g', material: 'Tit\u00e2nio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control' },
            { name: 'iPhone 16', cat: 'iPhone', year: '2024', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A18', ram: '8GB', mainCam: '48MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'At\u00e9 22h video', charging: 'USB-C, MagSafe', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.3', water: 'IP68', weight: '170g', material: 'Alum\u00ednio', highlight: 'Dynamic Island, Camera Control, Action Button' },
            { name: 'iPhone 16 Plus', cat: 'iPhone', year: '2024', screen: '6.7" Super Retina XDR OLED', refresh: '60Hz', chip: 'A18', ram: '8GB', mainCam: '48MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'At\u00e9 27h video', charging: 'USB-C, MagSafe', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.3', water: 'IP68', weight: '199g', material: 'Alum\u00ednio', highlight: 'Dynamic Island, Camera Control, Action Button' },
            // ── iPhone 15 Series ──
            { name: 'iPhone 15 Pro Max', cat: 'iPhone', year: '2023', screen: '6.7" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A17 Pro', ram: '8GB', mainCam: '48MP + 12MP UW + 12MP Tele 5x', frontCam: '12MP TrueDepth', battery: 'At\u00e9 29h video', charging: 'USB-C, MagSafe 15W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6E, Bluetooth 5.3, UWB', water: 'IP68 (6m)', weight: '221g', material: 'Tit\u00e2nio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button' },
            { name: 'iPhone 15 Pro', cat: 'iPhone', year: '2023', screen: '6.1" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A17 Pro', ram: '8GB', mainCam: '48MP + 12MP UW + 12MP Tele 3x', frontCam: '12MP TrueDepth', battery: 'At\u00e9 23h video', charging: 'USB-C, MagSafe 15W', storage: '128GB, 256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6E, Bluetooth 5.3, UWB', water: 'IP68 (6m)', weight: '187g', material: 'Tit\u00e2nio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button' },
            { name: 'iPhone 15', cat: 'iPhone', year: '2023', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A16 Bionic', ram: '6GB', mainCam: '48MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'At\u00e9 20h video', charging: 'USB-C, MagSafe 15W', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68', weight: '171g', material: 'Alum\u00ednio', highlight: 'Dynamic Island, USB-C' },
            { name: 'iPhone 15 Plus', cat: 'iPhone', year: '2023', screen: '6.7" Super Retina XDR OLED', refresh: '60Hz', chip: 'A16 Bionic', ram: '6GB', mainCam: '48MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'At\u00e9 26h video', charging: 'USB-C, MagSafe 15W', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68', weight: '201g', material: 'Alum\u00ednio', highlight: 'Dynamic Island, USB-C' },
            // ── iPhone 14 Series ──
            { name: 'iPhone 14 Pro Max', cat: 'iPhone', year: '2022', screen: '6.7" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A16 Bionic', ram: '6GB', mainCam: '48MP + 12MP UW + 12MP Tele 3x', frontCam: '12MP TrueDepth', battery: 'At\u00e9 29h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68 (6m)', weight: '240g', material: 'A\u00e7o inoxid\u00e1vel', highlight: 'Dynamic Island, Tela sempre ativa' },
            { name: 'iPhone 14 Pro', cat: 'iPhone', year: '2022', screen: '6.1" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A16 Bionic', ram: '6GB', mainCam: '48MP + 12MP UW + 12MP Tele 3x', frontCam: '12MP TrueDepth', battery: 'At\u00e9 23h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68 (6m)', weight: '206g', material: 'A\u00e7o inoxid\u00e1vel', highlight: 'Dynamic Island, Tela sempre ativa' },
            { name: 'iPhone 14', cat: 'iPhone', year: '2022', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A15 Bionic', ram: '6GB', mainCam: '12MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'At\u00e9 20h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68', weight: '172g', material: 'Alum\u00ednio', highlight: 'SOS Emerg\u00eancia via Sat\u00e9lite' },
            // ── iPhone 13 Series ──
            { name: 'iPhone 13 Pro Max', cat: 'iPhone', year: '2021', screen: '6.7" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A15 Bionic', ram: '6GB', mainCam: '12MP + 12MP UW + 12MP Tele 3x', frontCam: '12MP TrueDepth', battery: 'At\u00e9 28h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.0', water: 'IP68 (6m)', weight: '238g', material: 'A\u00e7o inoxid\u00e1vel', highlight: 'ProMotion 120Hz, Modo Macro' },
            { name: 'iPhone 13', cat: 'iPhone', year: '2021', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A15 Bionic', ram: '4GB', mainCam: '12MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'At\u00e9 19h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.0', water: 'IP68', weight: '173g', material: 'Alum\u00ednio', highlight: 'Modo Cinem\u00e1tico' },
            // ── iPads ──
            { name: 'iPad Pro M4 13"', cat: 'iPad', year: '2024', screen: '13" Ultra Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'Apple M4', ram: '8/16GB', mainCam: '12MP + 10MP UW + LiDAR', frontCam: '12MP Paisagem', battery: 'At\u00e9 10h nav web', charging: 'USB-C Thunderbolt', storage: '256GB a 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, 5G opc.', water: '-', weight: '579g (Wi-Fi)', material: 'Alum\u00ednio', highlight: 'Tela Tandem OLED, Apple Pencil Pro, Magic Keyboard' },
            { name: 'iPad Pro M4 11"', cat: 'iPad', year: '2024', screen: '11" Ultra Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'Apple M4', ram: '8/16GB', mainCam: '12MP + 10MP UW + LiDAR', frontCam: '12MP Paisagem', battery: 'At\u00e9 10h nav web', charging: 'USB-C Thunderbolt', storage: '256GB a 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, 5G opc.', water: '-', weight: '444g (Wi-Fi)', material: 'Alum\u00ednio', highlight: 'Tela Tandem OLED, Apple Pencil Pro, Magic Keyboard' },
            { name: 'iPad Air M3 13"', cat: 'iPad', year: '2025', screen: '13" Liquid Retina', refresh: '60Hz', chip: 'Apple M3', ram: '8GB', mainCam: '12MP', frontCam: '12MP Paisagem', battery: 'At\u00e9 10h nav web', charging: 'USB-C', storage: '128GB a 1TB', connectivity: 'Wi-Fi 7, Bluetooth 5.3, 5G opc.', water: '-', weight: '617g (Wi-Fi)', material: 'Alum\u00ednio', highlight: 'Apple Pencil Pro, Magic Keyboard' },
            { name: 'iPad Air M3 11"', cat: 'iPad', year: '2025', screen: '11" Liquid Retina', refresh: '60Hz', chip: 'Apple M3', ram: '8GB', mainCam: '12MP', frontCam: '12MP Paisagem', battery: 'At\u00e9 10h nav web', charging: 'USB-C', storage: '128GB a 1TB', connectivity: 'Wi-Fi 7, Bluetooth 5.3, 5G opc.', water: '-', weight: '462g (Wi-Fi)', material: 'Alum\u00ednio', highlight: 'Apple Pencil Pro, Magic Keyboard' },
            { name: 'iPad Mini 7a Ger.', cat: 'iPad', year: '2024', screen: '8.3" Liquid Retina', refresh: '60Hz', chip: 'A17 Pro', ram: '8GB', mainCam: '12MP', frontCam: '12MP Paisagem', battery: 'At\u00e9 10h nav web', charging: 'USB-C', storage: '128GB, 256GB, 512GB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, 5G opc.', water: '-', weight: '293g (Wi-Fi)', material: 'Alum\u00ednio', highlight: 'Apple Pencil Pro, compacto' },
            // ── Macs ──
            { name: 'MacBook Pro 16" M4 Pro', cat: 'Mac', year: '2025', screen: '16.2" Liquid Retina XDR', refresh: '120Hz ProMotion', chip: 'Apple M4 Pro', ram: '24/48GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'At\u00e9 24h video', charging: 'MagSafe 3 / USB-C', storage: '512GB, 1TB, 2TB, 4TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, HDMI 2.1', water: '-', weight: '2.14kg', material: 'Alum\u00ednio', highlight: 'Thunderbolt 5, 3x externo, Bateria enorme' },
            { name: 'MacBook Pro 14" M4 Pro', cat: 'Mac', year: '2025', screen: '14.2" Liquid Retina XDR', refresh: '120Hz ProMotion', chip: 'Apple M4 Pro', ram: '24/48GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'At\u00e9 17h video', charging: 'MagSafe 3 / USB-C', storage: '512GB, 1TB, 2TB, 4TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, HDMI 2.1', water: '-', weight: '1.55kg', material: 'Alum\u00ednio', highlight: 'Thunderbolt 5, 3x externo' },
            { name: 'MacBook Pro 14" M4', cat: 'Mac', year: '2025', screen: '14.2" Liquid Retina XDR', refresh: '120Hz ProMotion', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'At\u00e9 17h video', charging: 'MagSafe 3 / USB-C', storage: '512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, HDMI 2.1', water: '-', weight: '1.55kg', material: 'Alum\u00ednio', highlight: 'Thunderbolt 4, 2x externo' },
            { name: 'MacBook Air 15" M4', cat: 'Mac', year: '2025', screen: '15.3" Liquid Retina', refresh: '60Hz', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'At\u00e9 18h video', charging: 'MagSafe / USB-C', storage: '256GB, 512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3', water: '-', weight: '1.51kg', material: 'Alum\u00ednio', highlight: '2x externo, MagSafe, silencioso (fanless)' },
            { name: 'MacBook Air 13" M4', cat: 'Mac', year: '2025', screen: '13.6" Liquid Retina', refresh: '60Hz', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'At\u00e9 18h video', charging: 'MagSafe / USB-C', storage: '256GB, 512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3', water: '-', weight: '1.24kg', material: 'Alum\u00ednio', highlight: '2x externo, MagSafe, silencioso (fanless)' },
            { name: 'iMac 24" M4', cat: 'Mac', year: '2024', screen: '24" Retina 4.5K', refresh: '60Hz', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '12MP Center Stage', battery: '-', charging: 'Fonte externa', storage: '256GB, 512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3', water: '-', weight: '4.48kg', material: 'Alum\u00ednio', highlight: 'Tudo-em-um, 7 cores, USB-C/Thunderbolt' },
            { name: 'Mac Mini M4', cat: 'Mac', year: '2024', screen: '-', refresh: '-', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '-', battery: '-', charging: 'Fonte interna', storage: '256GB, 512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3', water: '-', weight: '0.68kg', material: 'Alum\u00ednio', highlight: 'Compacto, 2x USB-C frontal, Thunderbolt 4' },
            // ── Apple Watch ──
            { name: 'Apple Watch Ultra 2', cat: 'Watch', year: '2024', screen: '49mm OLED Flat', refresh: 'Always-On', chip: 'S9 SiP', ram: '-', mainCam: '-', frontCam: '-', battery: 'At\u00e9 36h', charging: 'MagSafe', storage: '64GB', connectivity: 'LTE, Wi-Fi, Bluetooth 5.3, L1+L5 GPS', water: 'WR100 / EN13319', weight: '61.4g', material: 'Tit\u00e2nio', highlight: 'Action Button, Profund\u00edmetro, Sirene 86dB' },
            { name: 'Apple Watch Series 10', cat: 'Watch', year: '2024', screen: '42/46mm OLED LTPO3', refresh: 'Always-On', chip: 'S10 SiP', ram: '-', mainCam: '-', frontCam: '-', battery: 'At\u00e9 18h', charging: 'MagSafe', storage: '64GB', connectivity: 'LTE opc., Wi-Fi, Bluetooth 5.3, L1+L5 GPS', water: 'WR50', weight: '36g (42mm)', material: 'Alum\u00ednio/Tit\u00e2nio', highlight: 'Mais fino, tela maior, detec\u00e7\u00e3o apneia do sono' },
            { name: 'Apple Watch SE 3a', cat: 'Watch', year: '2025', screen: '40/44mm OLED LTPO', refresh: 'Always-On', chip: 'S10 SiP', ram: '-', mainCam: '-', frontCam: '-', battery: 'At\u00e9 18h', charging: 'MagSafe', storage: '32GB', connectivity: 'LTE opc., Wi-Fi, Bluetooth 5.3, L1 GPS', water: 'WR50', weight: '27g (40mm)', material: 'Alum\u00ednio', highlight: 'Modelo mais acess\u00edvel, detec\u00e7\u00e3o de queda' },
            // ── AirPods ──
            { name: 'AirPods Pro 3', cat: 'AirPods', year: '2025', screen: '-', refresh: '-', chip: 'H3', ram: '-', mainCam: '-', frontCam: '-', battery: 'At\u00e9 6h (30h c/ case)', charging: 'USB-C / MagSafe / Qi', storage: '-', connectivity: 'Bluetooth 5.4', water: 'IPX4', weight: '5.3g cada', material: 'Pl\u00e1stico', highlight: 'ANC adaptativo, \u00c1udio Espacial, Audi\u00e7\u00e3o assistida' },
            { name: 'AirPods 4 ANC', cat: 'AirPods', year: '2024', screen: '-', refresh: '-', chip: 'H2', ram: '-', mainCam: '-', frontCam: '-', battery: 'At\u00e9 5h (30h c/ case)', charging: 'USB-C / MagSafe', storage: '-', connectivity: 'Bluetooth 5.3', water: 'IP54', weight: '4.3g cada', material: 'Pl\u00e1stico', highlight: 'ANC, \u00c1udio Espacial, sem ponteiras de silicone' },
            { name: 'AirPods 4', cat: 'AirPods', year: '2024', screen: '-', refresh: '-', chip: 'H2', ram: '-', mainCam: '-', frontCam: '-', battery: 'At\u00e9 5h (30h c/ case)', charging: 'USB-C', storage: '-', connectivity: 'Bluetooth 5.3', water: 'IP54', weight: '4.3g cada', material: 'Pl\u00e1stico', highlight: 'Novo design aberto, \u00c1udio Espacial' },
            { name: 'AirPods Max', cat: 'AirPods', year: '2024', screen: '-', refresh: '-', chip: 'H2', ram: '-', mainCam: '-', frontCam: '-', battery: 'At\u00e9 20h', charging: 'USB-C', storage: '-', connectivity: 'Bluetooth 5.3', water: '-', weight: '384.8g', material: 'Alum\u00ednio + A\u00e7o', highlight: 'Over-ear, ANC, \u00c1udio Espacial, Alta fidelidade' },
        ];
    }
    </script>
    @endpush
</x-app-layout>
