/**
 * Base de dados de especificações de produtos Apple.
 * Usado pela Ficha Técnica (/tools/specs).
 *
 * Campos por modelo:
 *   cat, name, year, screen, brightness, refresh, chip, ram, storage,
 *   mainCam, frontCam, video, batteryCap, battery, charging,
 *   sim, connectivity, water, dimensions, weight, material, biometrics, highlight
 */
function specsModels() {
    return [

        // ═══════════════════════════════════════
        //  iPHONE 17 SERIES (2025)
        // ═══════════════════════════════════════
        {
            name: 'iPhone 17 Pro Max', cat: 'iPhone', year: '2025',
            screen: '6.9" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A19 Pro', ram: '12GB', storage: '256GB, 512GB, 1TB, 2TB',
            mainCam: '48MP f/1.78 + 48MP UW f/2.2 + 12MP Tele 5x f/2.8', frontCam: '24MP f/1.9 TrueDepth',
            video: '4K 120fps Dolby Vision, ProRes, Vídeo Espacial',
            batteryCap: '4685 mAh', battery: 'Até 33h vídeo', charging: 'USB-C, MagSafe 25W, Qi2 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 7, BT 5.4, UWB, NFC',
            water: 'IP68 (6m)', dimensions: '163.0 × 77.6 × 8.25 mm', weight: '227g',
            material: 'Titânio', biometrics: 'Face ID',
            highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control, Apple Intelligence'
        },
        {
            name: 'iPhone 17 Pro', cat: 'iPhone', year: '2025',
            screen: '6.3" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A19 Pro', ram: '12GB', storage: '256GB, 512GB, 1TB',
            mainCam: '48MP f/1.78 + 48MP UW f/2.2 + 12MP Tele 5x f/2.8', frontCam: '24MP f/1.9 TrueDepth',
            video: '4K 120fps Dolby Vision, ProRes, Vídeo Espacial',
            batteryCap: '3577 mAh', battery: 'Até 27h vídeo', charging: 'USB-C, MagSafe 25W, Qi2 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 7, BT 5.4, UWB, NFC',
            water: 'IP68 (6m)', dimensions: '149.6 × 71.5 × 8.25 mm', weight: '199g',
            material: 'Titânio', biometrics: 'Face ID',
            highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control, Apple Intelligence'
        },
        {
            name: 'iPhone 17', cat: 'iPhone', year: '2025',
            screen: '6.1" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A19', ram: '8GB', storage: '256GB, 512GB',
            mainCam: '48MP f/1.6 + 24MP UW f/2.2', frontCam: '24MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision',
            batteryCap: '3561 mAh', battery: 'Até 22h vídeo', charging: 'USB-C, MagSafe 25W, Qi2',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 7, BT 5.4, NFC',
            water: 'IP68', dimensions: '147.7 × 71.5 × 7.25 mm', weight: '170g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Dynamic Island, Camera Control, Apple Intelligence'
        },
        {
            name: 'iPhone Air', cat: 'iPhone', year: '2025',
            screen: '6.6" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A19', ram: '8GB', storage: '256GB, 512GB, 1TB',
            mainCam: '48MP f/1.6 + 24MP UW f/2.2', frontCam: '24MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision',
            batteryCap: '3635 mAh', battery: 'Até 24h vídeo', charging: 'USB-C, MagSafe 25W, Qi2',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 7, BT 5.4, NFC',
            water: 'IP68', dimensions: '155.0 × 73.0 × 5.5 mm', weight: '163g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Mais fino (5.5mm), Dynamic Island, Apple Intelligence'
        },
        {
            name: 'iPhone 16e', cat: 'iPhone', year: '2025',
            screen: '6.1" Super Retina XDR OLED', brightness: '1600 nits', refresh: '60Hz',
            chip: 'A18 + Apple C1', ram: '8GB', storage: '128GB, 256GB, 512GB',
            mainCam: '48MP f/1.6', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision',
            batteryCap: '3561 mAh', battery: 'Até 26h vídeo', charging: 'USB-C, MagSafe, Qi2',
            sim: 'eSIM + nano-SIM', connectivity: '5G (C1), Wi-Fi 7, BT 5.3, NFC',
            water: 'IP68', dimensions: '147.7 × 71.5 × 7.8 mm', weight: '170g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Modem Apple C1, Apple Intelligence, modelo acessível'
        },

        // ═══════════════════════════════════════
        //  iPHONE 16 SERIES (2024)
        // ═══════════════════════════════════════
        {
            name: 'iPhone 16 Pro Max', cat: 'iPhone', year: '2024',
            screen: '6.9" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A18 Pro', ram: '8GB', storage: '256GB, 512GB, 1TB',
            mainCam: '48MP f/1.78 + 48MP UW f/2.2 + 12MP Tele 5x f/2.8', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 120fps Dolby Vision, ProRes, Vídeo Espacial',
            batteryCap: '4685 mAh', battery: 'Até 33h vídeo', charging: 'USB-C, MagSafe 25W, Qi2 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 7, BT 5.3, UWB, NFC',
            water: 'IP68 (6m)', dimensions: '163.0 × 77.6 × 8.25 mm', weight: '227g',
            material: 'Titânio', biometrics: 'Face ID',
            highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control'
        },
        {
            name: 'iPhone 16 Pro', cat: 'iPhone', year: '2024',
            screen: '6.3" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A18 Pro', ram: '8GB', storage: '256GB, 512GB, 1TB',
            mainCam: '48MP f/1.78 + 48MP UW f/2.2 + 12MP Tele 5x f/2.8', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 120fps Dolby Vision, ProRes, Vídeo Espacial',
            batteryCap: '3582 mAh', battery: 'Até 27h vídeo', charging: 'USB-C, MagSafe 25W, Qi2 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 7, BT 5.3, UWB, NFC',
            water: 'IP68 (6m)', dimensions: '149.6 × 71.5 × 8.25 mm', weight: '199g',
            material: 'Titânio', biometrics: 'Face ID',
            highlight: 'Tela sempre ativa, Dynamic Island, Action Button, Camera Control'
        },
        {
            name: 'iPhone 16 Plus', cat: 'iPhone', year: '2024',
            screen: '6.7" Super Retina XDR OLED', brightness: '2000 nits', refresh: '60Hz',
            chip: 'A18', ram: '8GB', storage: '128GB, 256GB, 512GB',
            mainCam: '48MP f/1.6 + 12MP UW f/2.2', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision',
            batteryCap: '4006 mAh', battery: 'Até 27h vídeo', charging: 'USB-C, MagSafe 15W, Qi2',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 7, BT 5.3, NFC',
            water: 'IP68', dimensions: '160.9 × 77.8 × 7.8 mm', weight: '199g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Dynamic Island, Camera Control, Action Button'
        },
        {
            name: 'iPhone 16', cat: 'iPhone', year: '2024',
            screen: '6.1" Super Retina XDR OLED', brightness: '2000 nits', refresh: '60Hz',
            chip: 'A18', ram: '8GB', storage: '128GB, 256GB, 512GB',
            mainCam: '48MP f/1.6 + 12MP UW f/2.2', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision',
            batteryCap: '3561 mAh', battery: 'Até 22h vídeo', charging: 'USB-C, MagSafe 15W, Qi2',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 7, BT 5.3, NFC',
            water: 'IP68', dimensions: '147.6 × 71.6 × 7.8 mm', weight: '170g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Dynamic Island, Camera Control, Action Button'
        },

        // ═══════════════════════════════════════
        //  iPHONE 15 SERIES (2023)
        // ═══════════════════════════════════════
        {
            name: 'iPhone 15 Pro Max', cat: 'iPhone', year: '2023',
            screen: '6.7" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A17 Pro', ram: '8GB', storage: '256GB, 512GB, 1TB',
            mainCam: '48MP f/1.78 + 12MP UW f/2.2 + 12MP Tele 5x f/2.8', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps ProRes, Dolby Vision',
            batteryCap: '4422 mAh', battery: 'Até 29h vídeo', charging: 'USB-C, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6E, BT 5.3, UWB, NFC',
            water: 'IP68 (6m)', dimensions: '159.9 × 76.7 × 8.25 mm', weight: '221g',
            material: 'Titânio', biometrics: 'Face ID',
            highlight: 'Tela sempre ativa, Dynamic Island, Action Button, USB-C'
        },
        {
            name: 'iPhone 15 Pro', cat: 'iPhone', year: '2023',
            screen: '6.1" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A17 Pro', ram: '8GB', storage: '128GB, 256GB, 512GB, 1TB',
            mainCam: '48MP f/1.78 + 12MP UW f/2.2 + 12MP Tele 3x f/2.8', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps ProRes, Dolby Vision',
            batteryCap: '3274 mAh', battery: 'Até 23h vídeo', charging: 'USB-C, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6E, BT 5.3, UWB, NFC',
            water: 'IP68 (6m)', dimensions: '146.6 × 70.6 × 8.25 mm', weight: '187g',
            material: 'Titânio', biometrics: 'Face ID',
            highlight: 'Tela sempre ativa, Dynamic Island, Action Button, USB-C'
        },
        {
            name: 'iPhone 15 Plus', cat: 'iPhone', year: '2023',
            screen: '6.7" Super Retina XDR OLED', brightness: '2000 nits', refresh: '60Hz',
            chip: 'A16 Bionic', ram: '6GB', storage: '128GB, 256GB, 512GB',
            mainCam: '48MP f/1.6 + 12MP UW f/2.4', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision',
            batteryCap: '4383 mAh', battery: 'Até 26h vídeo', charging: 'USB-C, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.3, NFC',
            water: 'IP68', dimensions: '160.9 × 77.8 × 7.8 mm', weight: '201g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Dynamic Island, USB-C'
        },
        {
            name: 'iPhone 15', cat: 'iPhone', year: '2023',
            screen: '6.1" Super Retina XDR OLED', brightness: '2000 nits', refresh: '60Hz',
            chip: 'A16 Bionic', ram: '6GB', storage: '128GB, 256GB, 512GB',
            mainCam: '48MP f/1.6 + 12MP UW f/2.4', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision',
            batteryCap: '3349 mAh', battery: 'Até 20h vídeo', charging: 'USB-C, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.3, NFC',
            water: 'IP68', dimensions: '147.6 × 71.6 × 7.8 mm', weight: '171g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Dynamic Island, USB-C'
        },

        // ═══════════════════════════════════════
        //  iPHONE 14 SERIES (2022)
        // ═══════════════════════════════════════
        {
            name: 'iPhone 14 Pro Max', cat: 'iPhone', year: '2022',
            screen: '6.7" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A16 Bionic', ram: '6GB', storage: '128GB, 256GB, 512GB, 1TB',
            mainCam: '48MP f/1.78 + 12MP UW f/2.2 + 12MP Tele 3x f/2.8', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps ProRes, Dolby Vision, Modo Ação',
            batteryCap: '4323 mAh', battery: 'Até 29h vídeo', charging: 'Lightning, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.3, NFC',
            water: 'IP68 (6m)', dimensions: '160.7 × 77.6 × 7.85 mm', weight: '240g',
            material: 'Aço inoxidável', biometrics: 'Face ID',
            highlight: 'Dynamic Island, Tela sempre ativa, SOS via Satélite'
        },
        {
            name: 'iPhone 14 Pro', cat: 'iPhone', year: '2022',
            screen: '6.1" Super Retina XDR OLED', brightness: '2000 nits', refresh: '120Hz ProMotion',
            chip: 'A16 Bionic', ram: '6GB', storage: '128GB, 256GB, 512GB, 1TB',
            mainCam: '48MP f/1.78 + 12MP UW f/2.2 + 12MP Tele 3x f/2.8', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps ProRes, Dolby Vision, Modo Ação',
            batteryCap: '3200 mAh', battery: 'Até 23h vídeo', charging: 'Lightning, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.3, NFC',
            water: 'IP68 (6m)', dimensions: '147.5 × 71.5 × 7.85 mm', weight: '206g',
            material: 'Aço inoxidável', biometrics: 'Face ID',
            highlight: 'Dynamic Island, Tela sempre ativa, SOS via Satélite'
        },
        {
            name: 'iPhone 14 Plus', cat: 'iPhone', year: '2022',
            screen: '6.7" Super Retina XDR OLED', brightness: '1200 nits', refresh: '60Hz',
            chip: 'A15 Bionic', ram: '6GB', storage: '128GB, 256GB, 512GB',
            mainCam: '12MP f/1.5 + 12MP UW f/2.4', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision, Modo Ação',
            batteryCap: '4325 mAh', battery: 'Até 26h vídeo', charging: 'Lightning, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.3, NFC',
            water: 'IP68', dimensions: '160.8 × 78.1 × 7.8 mm', weight: '203g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'SOS via Satélite, Detecção de acidente'
        },
        {
            name: 'iPhone 14', cat: 'iPhone', year: '2022',
            screen: '6.1" Super Retina XDR OLED', brightness: '1200 nits', refresh: '60Hz',
            chip: 'A15 Bionic', ram: '6GB', storage: '128GB, 256GB, 512GB',
            mainCam: '12MP f/1.5 + 12MP UW f/2.4', frontCam: '12MP f/1.9 TrueDepth',
            video: '4K 60fps Dolby Vision, Modo Ação',
            batteryCap: '3279 mAh', battery: 'Até 20h vídeo', charging: 'Lightning, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.3, NFC',
            water: 'IP68', dimensions: '146.7 × 71.5 × 7.8 mm', weight: '172g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'SOS via Satélite, Detecção de acidente'
        },

        // ═══════════════════════════════════════
        //  iPHONE 13 SERIES (2021)
        // ═══════════════════════════════════════
        {
            name: 'iPhone 13 Pro Max', cat: 'iPhone', year: '2021',
            screen: '6.7" Super Retina XDR OLED', brightness: '1200 nits', refresh: '120Hz ProMotion',
            chip: 'A15 Bionic', ram: '6GB', storage: '128GB, 256GB, 512GB, 1TB',
            mainCam: '12MP f/1.5 + 12MP UW f/1.8 + 12MP Tele 3x f/2.8', frontCam: '12MP f/2.2 TrueDepth',
            video: '4K 60fps ProRes, Dolby Vision, Modo Macro',
            batteryCap: '4352 mAh', battery: 'Até 28h vídeo', charging: 'Lightning, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.0, NFC',
            water: 'IP68 (6m)', dimensions: '160.8 × 78.1 × 7.65 mm', weight: '238g',
            material: 'Aço inoxidável', biometrics: 'Face ID',
            highlight: 'ProMotion 120Hz, Modo Macro, Modo Cinemático'
        },
        {
            name: 'iPhone 13 Pro', cat: 'iPhone', year: '2021',
            screen: '6.1" Super Retina XDR OLED', brightness: '1200 nits', refresh: '120Hz ProMotion',
            chip: 'A15 Bionic', ram: '6GB', storage: '128GB, 256GB, 512GB, 1TB',
            mainCam: '12MP f/1.5 + 12MP UW f/1.8 + 12MP Tele 3x f/2.8', frontCam: '12MP f/2.2 TrueDepth',
            video: '4K 60fps ProRes, Dolby Vision, Modo Macro',
            batteryCap: '3095 mAh', battery: 'Até 22h vídeo', charging: 'Lightning, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.0, NFC',
            water: 'IP68 (6m)', dimensions: '146.7 × 71.5 × 7.65 mm', weight: '203g',
            material: 'Aço inoxidável', biometrics: 'Face ID',
            highlight: 'ProMotion 120Hz, Modo Macro, Modo Cinemático'
        },
        {
            name: 'iPhone 13', cat: 'iPhone', year: '2021',
            screen: '6.1" Super Retina XDR OLED', brightness: '800 nits', refresh: '60Hz',
            chip: 'A15 Bionic', ram: '4GB', storage: '128GB, 256GB, 512GB',
            mainCam: '12MP f/1.6 + 12MP UW f/2.4', frontCam: '12MP f/2.2 TrueDepth',
            video: '4K 60fps Dolby Vision, Modo Cinemático',
            batteryCap: '3227 mAh', battery: 'Até 19h vídeo', charging: 'Lightning, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.0, NFC',
            water: 'IP68', dimensions: '146.7 × 71.5 × 7.65 mm', weight: '173g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Modo Cinemático, Notch menor'
        },
        {
            name: 'iPhone 13 Mini', cat: 'iPhone', year: '2021',
            screen: '5.4" Super Retina XDR OLED', brightness: '800 nits', refresh: '60Hz',
            chip: 'A15 Bionic', ram: '4GB', storage: '128GB, 256GB, 512GB',
            mainCam: '12MP f/1.6 + 12MP UW f/2.4', frontCam: '12MP f/2.2 TrueDepth',
            video: '4K 60fps Dolby Vision, Modo Cinemático',
            batteryCap: '2406 mAh', battery: 'Até 17h vídeo', charging: 'Lightning, MagSafe 15W',
            sim: 'eSIM + nano-SIM', connectivity: '5G, Wi-Fi 6, BT 5.0, NFC',
            water: 'IP68', dimensions: '131.5 × 64.2 × 7.65 mm', weight: '140g',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Mais compacto, Modo Cinemático'
        },

        // ═══════════════════════════════════════
        //  iPAD
        // ═══════════════════════════════════════
        {
            name: 'iPad Pro M4 13"', cat: 'iPad', year: '2024',
            screen: '13" Ultra Retina XDR OLED', brightness: '1600 nits HDR', refresh: '120Hz ProMotion',
            chip: 'Apple M4', ram: '8/16GB', storage: '256GB, 512GB, 1TB, 2TB',
            mainCam: '12MP + 10MP UW + LiDAR', frontCam: '12MP Paisagem',
            video: '4K 60fps ProRes',
            batteryCap: '38.99 Wh', battery: 'Até 10h nav web', charging: 'USB-C Thunderbolt',
            sim: 'eSIM / Wi-Fi', connectivity: 'Wi-Fi 6E, BT 5.3, 5G opc.',
            water: '-', dimensions: '281.6 × 215.5 × 5.1 mm', weight: '579g (Wi-Fi)',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Tela Tandem OLED, Apple Pencil Pro, Magic Keyboard, Thunderbolt'
        },
        {
            name: 'iPad Pro M4 11"', cat: 'iPad', year: '2024',
            screen: '11" Ultra Retina XDR OLED', brightness: '1600 nits HDR', refresh: '120Hz ProMotion',
            chip: 'Apple M4', ram: '8/16GB', storage: '256GB, 512GB, 1TB, 2TB',
            mainCam: '12MP + 10MP UW + LiDAR', frontCam: '12MP Paisagem',
            video: '4K 60fps ProRes',
            batteryCap: '31.29 Wh', battery: 'Até 10h nav web', charging: 'USB-C Thunderbolt',
            sim: 'eSIM / Wi-Fi', connectivity: 'Wi-Fi 6E, BT 5.3, 5G opc.',
            water: '-', dimensions: '249.7 × 177.5 × 5.3 mm', weight: '444g (Wi-Fi)',
            material: 'Alumínio', biometrics: 'Face ID',
            highlight: 'Tela Tandem OLED, Apple Pencil Pro, Magic Keyboard, Thunderbolt'
        },
        {
            name: 'iPad Air M3 13"', cat: 'iPad', year: '2025',
            screen: '13" Liquid Retina IPS', brightness: '600 nits', refresh: '60Hz',
            chip: 'Apple M3', ram: '8GB', storage: '128GB, 256GB, 512GB, 1TB',
            mainCam: '12MP', frontCam: '12MP Paisagem',
            video: '4K 60fps',
            batteryCap: '36.59 Wh', battery: 'Até 10h nav web', charging: 'USB-C',
            sim: 'eSIM / Wi-Fi', connectivity: 'Wi-Fi 7, BT 5.3, 5G opc.',
            water: '-', dimensions: '281.6 × 214.9 × 6.1 mm', weight: '617g (Wi-Fi)',
            material: 'Alumínio', biometrics: 'Touch ID (botão superior)',
            highlight: 'Apple Pencil Pro, Magic Keyboard, Apple Intelligence'
        },
        {
            name: 'iPad Air M3 11"', cat: 'iPad', year: '2025',
            screen: '11" Liquid Retina IPS', brightness: '600 nits', refresh: '60Hz',
            chip: 'Apple M3', ram: '8GB', storage: '128GB, 256GB, 512GB, 1TB',
            mainCam: '12MP', frontCam: '12MP Paisagem',
            video: '4K 60fps',
            batteryCap: '28.93 Wh', battery: 'Até 10h nav web', charging: 'USB-C',
            sim: 'eSIM / Wi-Fi', connectivity: 'Wi-Fi 7, BT 5.3, 5G opc.',
            water: '-', dimensions: '247.6 × 178.5 × 6.1 mm', weight: '462g (Wi-Fi)',
            material: 'Alumínio', biometrics: 'Touch ID (botão superior)',
            highlight: 'Apple Pencil Pro, Magic Keyboard, Apple Intelligence'
        },
        {
            name: 'iPad Mini 7a Ger.', cat: 'iPad', year: '2024',
            screen: '8.3" Liquid Retina IPS', brightness: '500 nits', refresh: '60Hz',
            chip: 'A17 Pro', ram: '8GB', storage: '128GB, 256GB, 512GB',
            mainCam: '12MP', frontCam: '12MP Paisagem',
            video: '4K 60fps',
            batteryCap: '19.3 Wh', battery: 'Até 10h nav web', charging: 'USB-C',
            sim: 'eSIM / Wi-Fi', connectivity: 'Wi-Fi 6E, BT 5.3, 5G opc.',
            water: '-', dimensions: '195.4 × 134.8 × 6.3 mm', weight: '293g (Wi-Fi)',
            material: 'Alumínio', biometrics: 'Touch ID (botão superior)',
            highlight: 'Apple Pencil Pro, compacto, Apple Intelligence'
        },
        {
            name: 'iPad 11a Ger.', cat: 'iPad', year: '2025',
            screen: '10.9" Liquid Retina IPS', brightness: '500 nits', refresh: '60Hz',
            chip: 'A16 Bionic', ram: '8GB', storage: '128GB, 256GB, 512GB',
            mainCam: '12MP', frontCam: '12MP Paisagem',
            video: '4K 60fps',
            batteryCap: '28.6 Wh', battery: 'Até 10h nav web', charging: 'USB-C',
            sim: 'eSIM / Wi-Fi', connectivity: 'Wi-Fi 6, BT 5.2, 5G opc.',
            water: '-', dimensions: '248.6 × 179.5 × 7.0 mm', weight: '477g (Wi-Fi)',
            material: 'Alumínio', biometrics: 'Touch ID (botão superior)',
            highlight: 'Apple Pencil (USB-C), Apple Intelligence, modelo acessível'
        },

        // ═══════════════════════════════════════
        //  MAC
        // ═══════════════════════════════════════
        {
            name: 'MacBook Pro 16" M4 Pro', cat: 'Mac', year: '2025',
            screen: '16.2" Liquid Retina XDR', brightness: '1600 nits HDR', refresh: '120Hz ProMotion',
            chip: 'Apple M4 Pro', ram: '24/48GB', storage: '512GB, 1TB, 2TB, 4TB',
            mainCam: '-', frontCam: '12MP Center Stage',
            video: '-',
            batteryCap: '100 Wh', battery: 'Até 24h vídeo', charging: 'MagSafe 3 / USB-C 140W',
            sim: '-', connectivity: 'Wi-Fi 6E, BT 5.3, HDMI 2.1, SD',
            water: '-', dimensions: '355.7 × 248.1 × 16.8 mm', weight: '2.14 kg',
            material: 'Alumínio', biometrics: 'Touch ID',
            highlight: 'Thunderbolt 5, até 3 telas externas, bateria recorde'
        },
        {
            name: 'MacBook Pro 14" M4 Pro', cat: 'Mac', year: '2025',
            screen: '14.2" Liquid Retina XDR', brightness: '1600 nits HDR', refresh: '120Hz ProMotion',
            chip: 'Apple M4 Pro', ram: '24/48GB', storage: '512GB, 1TB, 2TB, 4TB',
            mainCam: '-', frontCam: '12MP Center Stage',
            video: '-',
            batteryCap: '72.4 Wh', battery: 'Até 17h vídeo', charging: 'MagSafe 3 / USB-C 96W',
            sim: '-', connectivity: 'Wi-Fi 6E, BT 5.3, HDMI 2.1, SD',
            water: '-', dimensions: '312.6 × 221.2 × 15.5 mm', weight: '1.55 kg',
            material: 'Alumínio', biometrics: 'Touch ID',
            highlight: 'Thunderbolt 5, até 3 telas externas'
        },
        {
            name: 'MacBook Pro 14" M4', cat: 'Mac', year: '2025',
            screen: '14.2" Liquid Retina XDR', brightness: '1600 nits HDR', refresh: '120Hz ProMotion',
            chip: 'Apple M4', ram: '16/24/32GB', storage: '512GB, 1TB, 2TB',
            mainCam: '-', frontCam: '12MP Center Stage',
            video: '-',
            batteryCap: '72.4 Wh', battery: 'Até 17h vídeo', charging: 'MagSafe 3 / USB-C 70W',
            sim: '-', connectivity: 'Wi-Fi 6E, BT 5.3, HDMI 2.1, SD',
            water: '-', dimensions: '312.6 × 221.2 × 15.5 mm', weight: '1.55 kg',
            material: 'Alumínio', biometrics: 'Touch ID',
            highlight: 'Thunderbolt 4, até 2 telas externas'
        },
        {
            name: 'MacBook Air 15" M4', cat: 'Mac', year: '2025',
            screen: '15.3" Liquid Retina', brightness: '500 nits', refresh: '60Hz',
            chip: 'Apple M4', ram: '16/24/32GB', storage: '256GB, 512GB, 1TB, 2TB',
            mainCam: '-', frontCam: '12MP Center Stage',
            video: '-',
            batteryCap: '66.5 Wh', battery: 'Até 18h vídeo', charging: 'MagSafe / USB-C 70W',
            sim: '-', connectivity: 'Wi-Fi 6E, BT 5.3',
            water: '-', dimensions: '340.4 × 237.6 × 11.5 mm', weight: '1.51 kg',
            material: 'Alumínio', biometrics: 'Touch ID',
            highlight: 'Fanless (silencioso), 2 telas ext., MagSafe'
        },
        {
            name: 'MacBook Air 13" M4', cat: 'Mac', year: '2025',
            screen: '13.6" Liquid Retina', brightness: '500 nits', refresh: '60Hz',
            chip: 'Apple M4', ram: '16/24/32GB', storage: '256GB, 512GB, 1TB, 2TB',
            mainCam: '-', frontCam: '12MP Center Stage',
            video: '-',
            batteryCap: '52.6 Wh', battery: 'Até 18h vídeo', charging: 'MagSafe / USB-C 70W',
            sim: '-', connectivity: 'Wi-Fi 6E, BT 5.3',
            water: '-', dimensions: '304.1 × 215.0 × 11.3 mm', weight: '1.24 kg',
            material: 'Alumínio', biometrics: 'Touch ID',
            highlight: 'Fanless (silencioso), 2 telas ext., MagSafe'
        },
        {
            name: 'iMac 24" M4', cat: 'Mac', year: '2024',
            screen: '24" Retina 4.5K', brightness: '500 nits', refresh: '60Hz',
            chip: 'Apple M4', ram: '16/24/32GB', storage: '256GB, 512GB, 1TB, 2TB',
            mainCam: '-', frontCam: '12MP Center Stage',
            video: '-',
            batteryCap: '-', battery: '-', charging: 'Fonte externa 143W',
            sim: '-', connectivity: 'Wi-Fi 6E, BT 5.3, Ethernet opc.',
            water: '-', dimensions: '547 × 461 × 147 mm', weight: '4.48 kg',
            material: 'Alumínio', biometrics: 'Touch ID',
            highlight: 'Tudo-em-um, 7 cores, USB-C/Thunderbolt'
        },
        {
            name: 'Mac Mini M4', cat: 'Mac', year: '2024',
            screen: '-', brightness: '-', refresh: '-',
            chip: 'Apple M4', ram: '16/24/32GB', storage: '256GB, 512GB, 1TB, 2TB',
            mainCam: '-', frontCam: '-',
            video: '-',
            batteryCap: '-', battery: '-', charging: 'Fonte interna 155W',
            sim: '-', connectivity: 'Wi-Fi 6E, BT 5.3, Ethernet 10Gb opc.',
            water: '-', dimensions: '127 × 127 × 50 mm', weight: '0.68 kg',
            material: 'Alumínio', biometrics: '-',
            highlight: 'Ultra compacto, 2x USB-C frontal, Thunderbolt 4, até 3 telas'
        },

        // ═══════════════════════════════════════
        //  APPLE WATCH
        // ═══════════════════════════════════════
        {
            name: 'Apple Watch Ultra 2', cat: 'Watch', year: '2024',
            screen: '49mm OLED Flat LTPO2', brightness: '3000 nits', refresh: 'Always-On',
            chip: 'S9 SiP', ram: '-', storage: '64GB',
            mainCam: '-', frontCam: '-',
            video: '-',
            batteryCap: '564 mAh', battery: 'Até 36h (72h eco)', charging: 'MagSafe magnético',
            sim: 'eSIM (LTE)', connectivity: 'LTE, Wi-Fi, BT 5.3, L1+L5 GPS, UWB',
            water: 'WR100 / EN13319', dimensions: '49 × 44 × 14.4 mm', weight: '61.4g',
            material: 'Titânio', biometrics: '-',
            highlight: 'Action Button, Profundímetro, Sirene 86dB, GPS dual-freq'
        },
        {
            name: 'Apple Watch Series 10', cat: 'Watch', year: '2024',
            screen: '42/46mm OLED LTPO3', brightness: '2000 nits', refresh: 'Always-On',
            chip: 'S10 SiP', ram: '-', storage: '64GB',
            mainCam: '-', frontCam: '-',
            video: '-',
            batteryCap: '~310 mAh (46mm)', battery: 'Até 18h (36h eco)', charging: 'MagSafe magnético',
            sim: 'eSIM (LTE opc.)', connectivity: 'LTE opc., Wi-Fi, BT 5.3, L1+L5 GPS',
            water: 'WR50', dimensions: '46 × 39 × 9.7 mm (46mm)', weight: '36.4g (46mm Al)',
            material: 'Alumínio / Titânio', biometrics: '-',
            highlight: 'Mais fino, tela maior, detecção apneia, carregamento rápido'
        },
        {
            name: 'Apple Watch SE 3a', cat: 'Watch', year: '2025',
            screen: '40/44mm OLED LTPO', brightness: '1000 nits', refresh: 'Always-On',
            chip: 'S10 SiP', ram: '-', storage: '32GB',
            mainCam: '-', frontCam: '-',
            video: '-',
            batteryCap: '~245 mAh (44mm)', battery: 'Até 18h', charging: 'MagSafe magnético',
            sim: 'eSIM (LTE opc.)', connectivity: 'LTE opc., Wi-Fi, BT 5.3, L1 GPS',
            water: 'WR50', dimensions: '44 × 38 × 10.7 mm (44mm)', weight: '33g (44mm)',
            material: 'Alumínio / Plástico', biometrics: '-',
            highlight: 'Modelo acessível, detecção de queda/acidente, caixa plástica'
        },

        // ═══════════════════════════════════════
        //  AIRPODS
        // ═══════════════════════════════════════
        {
            name: 'AirPods Pro 3', cat: 'AirPods', year: '2025',
            screen: '-', brightness: '-', refresh: '-',
            chip: 'H3', ram: '-', storage: '-',
            mainCam: '-', frontCam: '-',
            video: '-',
            batteryCap: '-', battery: 'Até 6h (30h c/ case)', charging: 'USB-C / MagSafe / Qi2',
            sim: '-', connectivity: 'BT 5.4',
            water: 'IP54', dimensions: '-', weight: '5.3g cada',
            material: 'Plástico', biometrics: '-',
            highlight: 'ANC adaptativo, Áudio Espacial, sensor cardíaco, audição assistida'
        },
        {
            name: 'AirPods 4 ANC', cat: 'AirPods', year: '2024',
            screen: '-', brightness: '-', refresh: '-',
            chip: 'H2', ram: '-', storage: '-',
            mainCam: '-', frontCam: '-',
            video: '-',
            batteryCap: '-', battery: 'Até 5h (30h c/ case)', charging: 'USB-C / MagSafe',
            sim: '-', connectivity: 'BT 5.3',
            water: 'IP54', dimensions: '-', weight: '4.3g cada',
            material: 'Plástico', biometrics: '-',
            highlight: 'ANC, Áudio Espacial, sem ponteiras silicone, design aberto'
        },
        {
            name: 'AirPods 4', cat: 'AirPods', year: '2024',
            screen: '-', brightness: '-', refresh: '-',
            chip: 'H2', ram: '-', storage: '-',
            mainCam: '-', frontCam: '-',
            video: '-',
            batteryCap: '-', battery: 'Até 5h (30h c/ case)', charging: 'USB-C',
            sim: '-', connectivity: 'BT 5.3',
            water: 'IP54', dimensions: '-', weight: '4.3g cada',
            material: 'Plástico', biometrics: '-',
            highlight: 'Áudio Espacial, design aberto, case compacto'
        },
        {
            name: 'AirPods Max (USB-C)', cat: 'AirPods', year: '2024',
            screen: '-', brightness: '-', refresh: '-',
            chip: 'H2', ram: '-', storage: '-',
            mainCam: '-', frontCam: '-',
            video: '-',
            batteryCap: '-', battery: 'Até 20h', charging: 'USB-C',
            sim: '-', connectivity: 'BT 5.3',
            water: '-', dimensions: '-', weight: '384.8g',
            material: 'Alumínio + Aço', biometrics: '-',
            highlight: 'Over-ear, ANC, Áudio Espacial, Alta fidelidade, 9 cores'
        },
    ];
}
