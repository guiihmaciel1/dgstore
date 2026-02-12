<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" x-data="specsApp()">

            <!-- Header -->
            <div style="margin-bottom: 1.25rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Ficha Tecnica Apple</h1>
                <p style="font-size: 0.875rem; color: #6b7280;">Especificacoes rapidas para consulta e comparacao</p>
            </div>

            <!-- Seletores -->
            <div style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 4px;">Modelo 1</label>
                    <select x-model="selected1" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
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
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 4px;">Modelo 2 (comparar)</label>
                    <select x-model="selected2" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
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

            <!-- Nenhum selecionado -->
            <div x-show="!model1" style="text-align: center; padding: 3rem; color: #9ca3af; font-size: 0.875rem;">
                Selecione um modelo para ver as especificacoes
            </div>

            <!-- Cards de specs -->
            <div x-show="model1" style="display: grid; gap: 1rem;" :style="model2 ? 'grid-template-columns: 1fr 1fr;' : 'grid-template-columns: 1fr;'">
                <!-- Card 1 -->
                <template x-if="model1">
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 1rem 1.25rem; background: #111827; color: white;">
                            <div style="font-size: 1.125rem; font-weight: 700;" x-text="model1.name"></div>
                            <div style="font-size: 0.75rem; opacity: 0.7;" x-text="model1.year"></div>
                        </div>
                        <div style="padding: 0;">
                            <template x-for="(section, sIdx) in specSections" :key="sIdx">
                                <div>
                                    <div style="padding: 0.5rem 1.25rem; background: #f9fafb; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #f3f4f6;" x-text="section.label"></div>
                                    <template x-for="field in section.fields" :key="field.key">
                                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 1.25rem; border-bottom: 1px solid #f9fafb; font-size: 0.8rem;"
                                             :style="model2 && model1[field.key] !== model2[field.key] ? 'background:#fefce8;' : ''">
                                            <span style="color: #6b7280;" x-text="field.label"></span>
                                            <span style="font-weight: 500; color: #111827; text-align: right; max-width: 60%;" x-text="model1[field.key] || '-'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Card 2 -->
                <template x-if="model2">
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 1rem 1.25rem; background: #374151; color: white;">
                            <div style="font-size: 1.125rem; font-weight: 700;" x-text="model2.name"></div>
                            <div style="font-size: 0.75rem; opacity: 0.7;" x-text="model2.year"></div>
                        </div>
                        <div style="padding: 0;">
                            <template x-for="(section, sIdx) in specSections" :key="sIdx">
                                <div>
                                    <div style="padding: 0.5rem 1.25rem; background: #f9fafb; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #f3f4f6;" x-text="section.label"></div>
                                    <template x-for="field in section.fields" :key="field.key">
                                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 1.25rem; border-bottom: 1px solid #f9fafb; font-size: 0.8rem;"
                                             :style="model1 && model1[field.key] !== model2[field.key] ? 'background:#fefce8;' : ''">
                                            <span style="color: #6b7280;" x-text="field.label"></span>
                                            <span style="font-weight: 500; color: #111827; text-align: right; max-width: 60%;" x-text="model2[field.key] || '-'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Hint comparacao -->
            <div x-show="model1 && model2" style="margin-top: 0.75rem; text-align: center; font-size: 0.75rem; color: #9ca3af;">
                Campos com fundo amarelo indicam diferencas entre os modelos
            </div>
        </div>
    </div>

    <script>
    function specsApp() {
        const models = [
            // ── iPhone 17 Series ──
            { name: 'iPhone 17 Pro Max', cat: 'iPhone', year: '2025', screen: '6.9" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A19 Pro', ram: '12GB', mainCam: '48MP + 48MP UW + 12MP Tele 5x', frontCam: '24MP TrueDepth', battery: 'Ate 33h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB, 2TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.4, UWB', water: 'IP68 (6m)', weight: '227g', material: 'Titanio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control' },
            { name: 'iPhone 17 Pro', cat: 'iPhone', year: '2025', screen: '6.3" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A19 Pro', ram: '12GB', mainCam: '48MP + 48MP UW + 12MP Tele 5x', frontCam: '24MP TrueDepth', battery: 'Ate 27h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.4, UWB', water: 'IP68 (6m)', weight: '199g', material: 'Titanio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control' },
            { name: 'iPhone 17', cat: 'iPhone', year: '2025', screen: '6.1" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A19', ram: '8GB', mainCam: '48MP + 24MP UW', frontCam: '24MP TrueDepth', battery: 'Ate 22h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.4', water: 'IP68', weight: '170g', material: 'Aluminio', highlight: 'Dynamic Island, Camera Control, Apple Intelligence' },
            { name: 'iPhone Air', cat: 'iPhone', year: '2025', screen: '6.6" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A19', ram: '8GB', mainCam: '48MP + 24MP UW', frontCam: '24MP TrueDepth', battery: 'Ate 24h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.4', water: 'IP68', weight: '163g', material: 'Aluminio', highlight: 'Mais fino da linha, Dynamic Island, Apple Intelligence' },
            { name: 'iPhone 16e', cat: 'iPhone', year: '2025', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A18 + Apple C1', ram: '8GB', mainCam: '48MP', frontCam: '12MP TrueDepth', battery: 'Ate 26h video', charging: 'USB-C, MagSafe', storage: '128GB, 256GB, 512GB', connectivity: '5G (C1), Wi-Fi 7, Bluetooth 5.3', water: 'IP68', weight: '170g', material: 'Aluminio', highlight: 'Modem Apple C1, Apple Intelligence, Face ID' },

            // ── iPhone 16 Series ──
            { name: 'iPhone 16 Pro Max', cat: 'iPhone', year: '2024', screen: '6.9" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A18 Pro', ram: '8GB', mainCam: '48MP + 48MP UW + 12MP Tele 5x', frontCam: '12MP TrueDepth', battery: 'Ate 33h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.3, UWB', water: 'IP68 (6m)', weight: '227g', material: 'Titanio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control' },
            { name: 'iPhone 16 Pro', cat: 'iPhone', year: '2024', screen: '6.3" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A18 Pro', ram: '8GB', mainCam: '48MP + 48MP UW + 12MP Tele 5x', frontCam: '12MP TrueDepth', battery: 'Ate 27h video', charging: 'USB-C, MagSafe 25W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.3, UWB', water: 'IP68 (6m)', weight: '199g', material: 'Titanio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control' },
            { name: 'iPhone 16', cat: 'iPhone', year: '2024', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A18', ram: '8GB', mainCam: '48MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'Ate 22h video', charging: 'USB-C, MagSafe', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.3', water: 'IP68', weight: '170g', material: 'Aluminio', highlight: 'Dynamic Island, Camera Control, Action Button' },
            { name: 'iPhone 16 Plus', cat: 'iPhone', year: '2024', screen: '6.7" Super Retina XDR OLED', refresh: '60Hz', chip: 'A18', ram: '8GB', mainCam: '48MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'Ate 27h video', charging: 'USB-C, MagSafe', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 7, Bluetooth 5.3', water: 'IP68', weight: '199g', material: 'Aluminio', highlight: 'Dynamic Island, Camera Control, Action Button' },

            // ── iPhone 15 Series ──
            { name: 'iPhone 15 Pro Max', cat: 'iPhone', year: '2023', screen: '6.7" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A17 Pro', ram: '8GB', mainCam: '48MP + 12MP UW + 12MP Tele 5x', frontCam: '12MP TrueDepth', battery: 'Ate 29h video', charging: 'USB-C, MagSafe 15W', storage: '256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6E, Bluetooth 5.3, UWB', water: 'IP68 (6m)', weight: '221g', material: 'Titanio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button' },
            { name: 'iPhone 15 Pro', cat: 'iPhone', year: '2023', screen: '6.1" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A17 Pro', ram: '8GB', mainCam: '48MP + 12MP UW + 12MP Tele 3x', frontCam: '12MP TrueDepth', battery: 'Ate 23h video', charging: 'USB-C, MagSafe 15W', storage: '128GB, 256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6E, Bluetooth 5.3, UWB', water: 'IP68 (6m)', weight: '187g', material: 'Titanio', highlight: 'Tela sempre ativa, Dynamic Island, Action Button' },
            { name: 'iPhone 15', cat: 'iPhone', year: '2023', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A16 Bionic', ram: '6GB', mainCam: '48MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'Ate 20h video', charging: 'USB-C, MagSafe 15W', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68', weight: '171g', material: 'Aluminio', highlight: 'Dynamic Island, USB-C' },
            { name: 'iPhone 15 Plus', cat: 'iPhone', year: '2023', screen: '6.7" Super Retina XDR OLED', refresh: '60Hz', chip: 'A16 Bionic', ram: '6GB', mainCam: '48MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'Ate 26h video', charging: 'USB-C, MagSafe 15W', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68', weight: '201g', material: 'Aluminio', highlight: 'Dynamic Island, USB-C' },

            // ── iPhone 14 Series ──
            { name: 'iPhone 14 Pro Max', cat: 'iPhone', year: '2022', screen: '6.7" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A16 Bionic', ram: '6GB', mainCam: '48MP + 12MP UW + 12MP Tele 3x', frontCam: '12MP TrueDepth', battery: 'Ate 29h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68 (6m)', weight: '240g', material: 'Aco inoxidavel', highlight: 'Dynamic Island, Tela sempre ativa' },
            { name: 'iPhone 14 Pro', cat: 'iPhone', year: '2022', screen: '6.1" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A16 Bionic', ram: '6GB', mainCam: '48MP + 12MP UW + 12MP Tele 3x', frontCam: '12MP TrueDepth', battery: 'Ate 23h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68 (6m)', weight: '206g', material: 'Aco inoxidavel', highlight: 'Dynamic Island, Tela sempre ativa' },
            { name: 'iPhone 14', cat: 'iPhone', year: '2022', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A15 Bionic', ram: '6GB', mainCam: '12MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'Ate 20h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.3', water: 'IP68', weight: '172g', material: 'Aluminio', highlight: 'SOS Emergencia via Satelite' },

            // ── iPhone 13 Series ──
            { name: 'iPhone 13 Pro Max', cat: 'iPhone', year: '2021', screen: '6.7" Super Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'A15 Bionic', ram: '6GB', mainCam: '12MP + 12MP UW + 12MP Tele 3x', frontCam: '12MP TrueDepth', battery: 'Ate 28h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB, 1TB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.0', water: 'IP68 (6m)', weight: '238g', material: 'Aco inoxidavel', highlight: 'ProMotion 120Hz, Macro' },
            { name: 'iPhone 13', cat: 'iPhone', year: '2021', screen: '6.1" Super Retina XDR OLED', refresh: '60Hz', chip: 'A15 Bionic', ram: '4GB', mainCam: '12MP + 12MP UW', frontCam: '12MP TrueDepth', battery: 'Ate 19h video', charging: 'Lightning, MagSafe 15W', storage: '128GB, 256GB, 512GB', connectivity: '5G, Wi-Fi 6, Bluetooth 5.0', water: 'IP68', weight: '173g', material: 'Aluminio', highlight: 'Modo Cinema' },

            // ── iPads ──
            { name: 'iPad Pro M4 13"', cat: 'iPad', year: '2024', screen: '13" Ultra Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'Apple M4', ram: '8/16GB', mainCam: '12MP + 10MP UW + LiDAR', frontCam: '12MP Paisagem', battery: 'Ate 10h nav web', charging: 'USB-C Thunderbolt', storage: '256GB a 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, 5G opc.', water: '-', weight: '579g (Wi-Fi)', material: 'Aluminio', highlight: 'Tela Tandem OLED, Apple Pencil Pro, Magic Keyboard' },
            { name: 'iPad Pro M4 11"', cat: 'iPad', year: '2024', screen: '11" Ultra Retina XDR OLED', refresh: '120Hz ProMotion', chip: 'Apple M4', ram: '8/16GB', mainCam: '12MP + 10MP UW + LiDAR', frontCam: '12MP Paisagem', battery: 'Ate 10h nav web', charging: 'USB-C Thunderbolt', storage: '256GB a 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, 5G opc.', water: '-', weight: '444g (Wi-Fi)', material: 'Aluminio', highlight: 'Tela Tandem OLED, Apple Pencil Pro, Magic Keyboard' },
            { name: 'iPad Air M3 13"', cat: 'iPad', year: '2025', screen: '13" Liquid Retina', refresh: '60Hz', chip: 'Apple M3', ram: '8GB', mainCam: '12MP', frontCam: '12MP Paisagem', battery: 'Ate 10h nav web', charging: 'USB-C', storage: '128GB a 1TB', connectivity: 'Wi-Fi 7, Bluetooth 5.3, 5G opc.', water: '-', weight: '617g (Wi-Fi)', material: 'Aluminio', highlight: 'Apple Pencil Pro, Magic Keyboard' },
            { name: 'iPad Air M3 11"', cat: 'iPad', year: '2025', screen: '11" Liquid Retina', refresh: '60Hz', chip: 'Apple M3', ram: '8GB', mainCam: '12MP', frontCam: '12MP Paisagem', battery: 'Ate 10h nav web', charging: 'USB-C', storage: '128GB a 1TB', connectivity: 'Wi-Fi 7, Bluetooth 5.3, 5G opc.', water: '-', weight: '462g (Wi-Fi)', material: 'Aluminio', highlight: 'Apple Pencil Pro, Magic Keyboard' },
            { name: 'iPad Mini 7a Ger.', cat: 'iPad', year: '2024', screen: '8.3" Liquid Retina', refresh: '60Hz', chip: 'A17 Pro', ram: '8GB', mainCam: '12MP', frontCam: '12MP Paisagem', battery: 'Ate 10h nav web', charging: 'USB-C', storage: '128GB, 256GB, 512GB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, 5G opc.', water: '-', weight: '293g (Wi-Fi)', material: 'Aluminio', highlight: 'Apple Pencil Pro, compacto' },

            // ── Macs ──
            { name: 'MacBook Pro 16" M4 Pro', cat: 'Mac', year: '2025', screen: '16.2" Liquid Retina XDR', refresh: '120Hz ProMotion', chip: 'Apple M4 Pro', ram: '24/48GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'Ate 24h video', charging: 'MagSafe 3 / USB-C', storage: '512GB, 1TB, 2TB, 4TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, HDMI 2.1', water: '-', weight: '2.14kg', material: 'Aluminio', highlight: 'Thunderbolt 5, 3x externo, Bateria enorme' },
            { name: 'MacBook Pro 14" M4 Pro', cat: 'Mac', year: '2025', screen: '14.2" Liquid Retina XDR', refresh: '120Hz ProMotion', chip: 'Apple M4 Pro', ram: '24/48GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'Ate 17h video', charging: 'MagSafe 3 / USB-C', storage: '512GB, 1TB, 2TB, 4TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, HDMI 2.1', water: '-', weight: '1.55kg', material: 'Aluminio', highlight: 'Thunderbolt 5, 3x externo' },
            { name: 'MacBook Pro 14" M4', cat: 'Mac', year: '2025', screen: '14.2" Liquid Retina XDR', refresh: '120Hz ProMotion', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'Ate 17h video', charging: 'MagSafe 3 / USB-C', storage: '512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3, HDMI 2.1', water: '-', weight: '1.55kg', material: 'Aluminio', highlight: 'Thunderbolt 4, 2x externo' },
            { name: 'MacBook Air 15" M4', cat: 'Mac', year: '2025', screen: '15.3" Liquid Retina', refresh: '60Hz', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'Ate 18h video', charging: 'MagSafe / USB-C', storage: '256GB, 512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3', water: '-', weight: '1.51kg', material: 'Aluminio', highlight: '2x externo, MagSafe, silencioso (fanless)' },
            { name: 'MacBook Air 13" M4', cat: 'Mac', year: '2025', screen: '13.6" Liquid Retina', refresh: '60Hz', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '12MP Center Stage', battery: 'Ate 18h video', charging: 'MagSafe / USB-C', storage: '256GB, 512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3', water: '-', weight: '1.24kg', material: 'Aluminio', highlight: '2x externo, MagSafe, silencioso (fanless)' },
            { name: 'iMac 24" M4', cat: 'Mac', year: '2024', screen: '24" Retina 4.5K', refresh: '60Hz', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '12MP Center Stage', battery: '-', charging: 'Fonte externa', storage: '256GB, 512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3', water: '-', weight: '4.48kg', material: 'Aluminio', highlight: 'Tudo-em-um, 7 cores, USB-C/Thunderbolt' },
            { name: 'Mac Mini M4', cat: 'Mac', year: '2024', screen: '-', refresh: '-', chip: 'Apple M4', ram: '16/24/32GB', mainCam: '-', frontCam: '-', battery: '-', charging: 'Fonte interna', storage: '256GB, 512GB, 1TB, 2TB', connectivity: 'Wi-Fi 6E, Bluetooth 5.3', water: '-', weight: '0.68kg', material: 'Aluminio', highlight: 'Compacto, 2x USB-C frontal, Thunderbolt 4' },

            // ── Apple Watch ──
            { name: 'Apple Watch Ultra 2', cat: 'Watch', year: '2024', screen: '49mm OLED Flat', refresh: 'Always-On', chip: 'S9 SiP', ram: '-', mainCam: '-', frontCam: '-', battery: 'Ate 36h', charging: 'MagSafe', storage: '64GB', connectivity: 'LTE, Wi-Fi, Bluetooth 5.3, L1+L5 GPS', water: 'WR100 / EN13319', weight: '61.4g', material: 'Titanio', highlight: 'Action Button, Profundimetro, Sirene 86dB' },
            { name: 'Apple Watch Series 10', cat: 'Watch', year: '2024', screen: '42/46mm OLED LTPO3', refresh: 'Always-On', chip: 'S10 SiP', ram: '-', mainCam: '-', frontCam: '-', battery: 'Ate 18h', charging: 'MagSafe', storage: '64GB', connectivity: 'LTE opc., Wi-Fi, Bluetooth 5.3, L1+L5 GPS', water: 'WR50', weight: '36g (42mm)', material: 'Aluminio/Titanio', highlight: 'Mais fino, tela maior, deteccao apneia do sono' },
            { name: 'Apple Watch SE 3a', cat: 'Watch', year: '2025', screen: '40/44mm OLED LTPO', refresh: 'Always-On', chip: 'S10 SiP', ram: '-', mainCam: '-', frontCam: '-', battery: 'Ate 18h', charging: 'MagSafe', storage: '32GB', connectivity: 'LTE opc., Wi-Fi, Bluetooth 5.3, L1 GPS', water: 'WR50', weight: '27g (40mm)', material: 'Aluminio', highlight: 'Modelo mais acessivel, deteccao de queda' },

            // ── AirPods ──
            { name: 'AirPods Pro 3', cat: 'AirPods', year: '2025', screen: '-', refresh: '-', chip: 'H3', ram: '-', mainCam: '-', frontCam: '-', battery: 'Ate 6h (30h c/ case)', charging: 'USB-C / MagSafe / Qi', storage: '-', connectivity: 'Bluetooth 5.4', water: 'IPX4', weight: '5.3g cada', material: 'Plastico', highlight: 'ANC adaptativo, Audio Espacial, Audicao assistida' },
            { name: 'AirPods 4 ANC', cat: 'AirPods', year: '2024', screen: '-', refresh: '-', chip: 'H2', ram: '-', mainCam: '-', frontCam: '-', battery: 'Ate 5h (30h c/ case)', charging: 'USB-C / MagSafe', storage: '-', connectivity: 'Bluetooth 5.3', water: 'IP54', weight: '4.3g cada', material: 'Plastico', highlight: 'ANC, Audio Espacial, sem ponteiras de silicone' },
            { name: 'AirPods 4', cat: 'AirPods', year: '2024', screen: '-', refresh: '-', chip: 'H2', ram: '-', mainCam: '-', frontCam: '-', battery: 'Ate 5h (30h c/ case)', charging: 'USB-C', storage: '-', connectivity: 'Bluetooth 5.3', water: 'IP54', weight: '4.3g cada', material: 'Plastico', highlight: 'Novo design aberto, Audio Espacial' },
            { name: 'AirPods Max', cat: 'AirPods', year: '2024', screen: '-', refresh: '-', chip: 'H2', ram: '-', mainCam: '-', frontCam: '-', battery: 'Ate 20h', charging: 'USB-C', storage: '-', connectivity: 'Bluetooth 5.3', water: '-', weight: '384.8g', material: 'Aluminio + Aco', highlight: 'Over-ear, ANC, Audio Espacial, Alta fidelidade' },
        ];

        return {
            models,
            selected1: '',
            selected2: '',

            get groupedModels() {
                const groups = {};
                this.models.forEach(m => {
                    if (!groups[m.cat]) groups[m.cat] = [];
                    groups[m.cat].push(m);
                });
                return groups;
            },

            get model1() {
                return this.models.find(m => m.name === this.selected1) || null;
            },
            get model2() {
                return this.models.find(m => m.name === this.selected2) || null;
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
                { label: 'Cameras', fields: [
                    { key: 'mainCam', label: 'Cam. traseira' },
                    { key: 'frontCam', label: 'Cam. frontal' },
                ]},
                { label: 'Bateria', fields: [
                    { key: 'battery', label: 'Duracao' },
                    { key: 'charging', label: 'Carregamento' },
                ]},
                { label: 'Conectividade', fields: [
                    { key: 'connectivity', label: 'Conexoes' },
                    { key: 'water', label: 'Resist. agua' },
                ]},
                { label: 'Fisico', fields: [
                    { key: 'weight', label: 'Peso' },
                    { key: 'material', label: 'Material' },
                ]},
                { label: 'Destaque', fields: [
                    { key: 'highlight', label: 'Recursos' },
                ]},
            ],
        };
    }
    </script>
</x-app-layout>
