/**
 * Dados do módulo de Treinamento de Vendas.
 * Usado por resources/views/tools/sales-training.blade.php
 */
function salesTrainingData() {
    return {
        lineup2025: [
            {
                name: 'iPhone 17 Pro Max',
                img: 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-17-pro-max-desert-gold-select?wid=400&hei=400&fmt=png-alpha',
                gradient: ['#1a1a2e', '#2d1b69'],
                chip: 'A19 Pro — o mais potente já feito',
                camera: '48MP + 48MP UW + 12MP Tele 5x',
                screen: '6.9" OLED 120Hz ProMotion',
                battery: 'Até 33h de vídeo 🔥',
                tag: 'TOP DE LINHA',
                tagColor: '#7c3aed',
                sellTip: 'Pra quem quer o MELHOR. Foto, vídeo, tela gigante e bateria absurda.'
            },
            {
                name: 'iPhone 17 Pro',
                img: 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-17-pro-desert-gold-select?wid=400&hei=400&fmt=png-alpha',
                gradient: ['#1a1a2e', '#1e3a5f'],
                chip: 'A19 Pro — mesma potência do Max',
                camera: '48MP + 48MP UW + 12MP Tele 5x',
                screen: '6.3" OLED 120Hz ProMotion',
                battery: 'Até 27h de vídeo',
                tag: 'COMPACTO PREMIUM',
                tagColor: '#2563eb',
                sellTip: 'Mesmo poder do Pro Max, mas em tamanho que cabe fácil no bolso.'
            },
            {
                name: 'iPhone 17',
                img: 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-17-white-select?wid=400&hei=400&fmt=png-alpha',
                gradient: ['#f5f5f7', '#e8e8ed'],
                chip: 'A19 — super rápido',
                camera: '48MP + 24MP UW',
                screen: '6.1" OLED 120Hz ProMotion',
                battery: 'Até 22h de vídeo',
                tag: 'EQUILÍBRIO',
                tagColor: '#059669',
                sellTip: 'Ótimo pra quem quer iPhone novo sem pagar Pro. Ganhou 120Hz esse ano!'
            },
            {
                name: 'iPhone Air',
                img: 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-air-white-select?wid=400&hei=400&fmt=png-alpha',
                gradient: ['#e0f2fe', '#bae6fd'],
                chip: 'A19 — mesmo chip do 17',
                camera: '48MP + 24MP UW',
                screen: '6.6" OLED 120Hz ProMotion',
                battery: 'Até 24h de vídeo',
                tag: 'MAIS FINO DO MUNDO',
                tagColor: '#0891b2',
                sellTip: 'Apenas 5.5mm! Tela grande e levíssimo. Argumento visual FORTE na loja.'
            },
            {
                name: 'iPhone 16e',
                img: 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16e-white-select?wid=400&hei=400&fmt=png-alpha',
                gradient: ['#fef3c7', '#fde68a'],
                chip: 'A18 + Modem Apple C1',
                camera: '48MP',
                screen: '6.1" OLED 60Hz',
                battery: 'Até 26h de vídeo',
                tag: 'ENTRADA APPLE',
                tagColor: '#d97706',
                sellTip: 'Mais barato com Apple Intelligence. Primeiro com modem próprio da Apple!'
            }
        ],

        seminovos: [
            { name: 'iPhone 15 / 15 Plus', year: '2023', tags: ['USB-C', 'Dynamic Island', 'A16'], whyBuy: 'Primeiro com USB-C e Dynamic Island no modelo base. Ainda super atual, câmera de 48MP e design premium.' },
            { name: 'iPhone 15 Pro / Pro Max', year: '2023', tags: ['Titânio', 'A17 Pro', 'Action Button', 'USB-C 3'], whyBuy: 'Titânio, Action Button, chip A17 Pro com ray tracing. Roda Apple Intelligence! Seminovo top.' },
            { name: 'iPhone 14 / 14 Plus', year: '2022', tags: ['Detecção de acidente', 'A15', 'Bom custo'], whyBuy: 'Super estável, bateria excelente no Plus. Ótima opção custo-benefício com design moderno.' },
            { name: 'iPhone 14 Pro / Pro Max', year: '2022', tags: ['Dynamic Island', '48MP', 'Always-On', 'A16'], whyBuy: 'Foi o primeiro com Dynamic Island e câmera de 48MP. Tela always-on. Ainda impressiona muito.' },
            { name: 'iPhone 13 / 13 Mini', year: '2021', tags: ['A15', 'Modo Cinema', 'Bateria boa'], whyBuy: 'Design que marcou época. Modo Cinema na câmera, bateria que dura o dia inteiro, preço acessível.' },
            { name: 'iPhone 12 / 12 Mini', year: '2020', tags: ['5G', 'MagSafe', 'A14', 'OLED'], whyBuy: 'Primeiro com 5G e MagSafe. Tela OLED em todos os modelos. Design flat que voltou pra ficar.' },
            { name: 'iPhone 11', year: '2019', tags: ['A13', 'Ultra Wide', 'Resistente'], whyBuy: 'Câmera ultra-wide foi revolução na época. Chip A13 ainda roda tudo. Imbatível no preço pra entrada.' },
        ],

        battles: [
            {
                title: 'iPhone 17 Pro vs iPhone 16 Pro',
                sides: [
                    { name: 'iPhone 17 Pro', highlights: ['A19 Pro', '48MP UW', 'Wi-Fi 7', 'USB-C 3.2'] },
                    { name: 'iPhone 16 Pro', highlights: ['A18 Pro', '48MP UW', 'Wi-Fi 6E', 'Camera Control'] }
                ],
                script: '"O 17 Pro evoluiu no chip e conectividade. Mas se o cliente quer economizar, o 16 Pro já tem Camera Control e é absurdamente bom."'
            },
            {
                title: 'iPhone 16 vs iPhone 15',
                sides: [
                    { name: 'iPhone 16', highlights: ['A18', 'Camera Control', '48MP + 12MP UW', 'Apple Intelligence'] },
                    { name: 'iPhone 15', highlights: ['A16', 'Dynamic Island', '48MP + 12MP UW', 'USB-C'] }
                ],
                script: '"A grande diferença é o chip A18 com Apple Intelligence. Se o cliente usa muito IA e quer as novidades, vale o upgrade. Se não, o 15 ainda tá ótimo."'
            },
            {
                title: 'iPhone 15 Pro vs iPhone 14 Pro',
                sides: [
                    { name: '15 Pro', highlights: ['Titânio', 'A17 Pro', 'Action Button', 'USB-C', 'Apple Intelligence'] },
                    { name: '14 Pro', highlights: ['Aço inox', 'A16', 'Chave silencioso', 'Lightning', 'Sem IA'] }
                ],
                script: '"O 15 Pro é muito mais leve pelo titânio, tem USB-C universal e roda Apple Intelligence. O 14 Pro é ótimo, mas fica pra trás nessas novidades."'
            },
            {
                title: 'iPhone Air vs iPhone 17',
                sides: [
                    { name: 'iPhone Air', highlights: ['5.5mm fino', '6.6" tela', '163g', '24h bateria'] },
                    { name: 'iPhone 17', highlights: ['7.25mm', '6.1" tela', '170g', '22h bateria'] }
                ],
                script: '"O Air é perfeito pra quem quer tela grande e aparelho fino e leve. O 17 é pra quem prefere algo mais compacto. Mesmo chip, mesma câmera."'
            }
        ],

        aiCompatible: ['iPhone 17 Pro Max', 'iPhone 17 Pro', 'iPhone 17', 'iPhone Air', 'iPhone 16e', 'iPhone 16 (todos)', 'iPhone 15 Pro', 'iPhone 15 Pro Max'],

        aiFeatures: [
            { icon: '✍️', name: 'Ferramentas de Escrita', desc: 'Reescreve, resume e revisa qualquer texto. Em e-mail, WhatsApp, Notes... qualquer app.' },
            { icon: '🎨', name: 'Image Playground', desc: 'Cria imagens e ilustrações a partir de descrições. Tipo um mini DALL-E dentro do iPhone.' },
            { icon: '😜', name: 'Genmoji', desc: 'Cria emojis personalizados na hora. Quer um emoji seu com chapéu de cowboy? Pronto!' },
            { icon: '🔔', name: 'Resumo de Notificações', desc: 'A Siri resume todas as notificações pra você. Nada de ler 50 mensagens de grupo.' },
            { icon: '🗣️', name: 'Siri Turbinada', desc: 'Siri agora entende contexto, lembra de conversas e controla apps de verdade.' },
            { icon: '🔍', name: 'Visual Intelligence', desc: 'Aponta a câmera pra qualquer coisa e o iPhone identifica, traduz ou busca informações.' }
        ],

        salesTips: [
            {
                icon: '⚓', title: 'Ancoragem de Preço',
                desc: 'Sempre mostre o modelo mais caro primeiro. Quando o cliente vê o Pro Max, o Pro parece "barato".',
                example: '"Esse é o Pro Max, nosso top. Mas olha, o Pro tem quase tudo igual e custa bem menos..."'
            },
            {
                icon: '📈', title: 'Upsell Inteligente',
                desc: 'Quando o cliente já decidiu, sugira o modelo um nível acima mostrando o custo-benefício.',
                example: '"Por mais X reais você leva o de 256GB. Nunca vai se preocupar com espaço."'
            },
            {
                icon: '📉', title: 'Downsell que Salva a Venda',
                desc: 'Se o cliente desanimou com o preço, ofereça uma alternativa que caiba no bolso. Melhor vender do que perder.',
                example: '"Entendo! Olha, o iPhone 16e tem Apple Intelligence também e cabe super no orçamento."'
            },
            {
                icon: '🎧', title: 'Venda Cruzada (Cross-sell)',
                desc: 'Após fechar o iPhone, sugira acessórios. Capinha, película, carregador, AirPods...',
                example: '"Pra proteger seu iPhone novo, a gente tem essa capinha que é lindíssima. E que tal um AirPods pra completar?"'
            },
            {
                icon: '🤝', title: 'Conexão Pessoal',
                desc: 'Pergunte o que a pessoa faz, do que gosta. Vender é resolver problemas, não empurrar produto.',
                example: '"Você usa mais pra fotos, redes sociais ou trabalho? Assim eu te indico o modelo perfeito."'
            }
        ],

        objections: [
            { q: '"Tá muito caro..."', a: '"Entendo! Mas olha: dividido fica X por mês. E um iPhone dura fácil 5 anos com atualizações. Na ponta do lápis, é o celular com melhor custo por ano."' },
            { q: '"Não vem carregador?"', a: '"Pois é, a Apple tirou pensando no meio ambiente. Mas qualquer carregador USB-C funciona! E a gente tem uns ótimos aqui por um precinho especial."' },
            { q: '"Android faz a mesma coisa e é mais barato"', a: '"Entendo o ponto! Mas o iPhone tem atualizações por 6-7 anos, segurança muito superior e o ecossistema todo funciona junto. Na revenda, ele vale muito mais também."' },
            { q: '"Vou pensar..."', a: '"Claro! Posso te mandar as specs por WhatsApp pra você comparar em casa? Assim se decidir, é só me chamar."' },
            { q: '"Meu amigo disse que iPhone trava"', a: '"Pode ser um modelo muito antigo ou com armazenamento lotado. Os iPhones atuais com chip A18/A19 são os smartphones mais rápidos do mundo nos benchmarks."' }
        ],

        marketStats: [
            { value: '1.46bi', label: 'iPhones ativos no mundo' },
            { value: '98%', label: 'satisfação do cliente Apple' },
            { value: '6-7 anos', label: 'de atualizações iOS' },
            { value: '#1', label: 'em revenda de smartphones' },
            { value: '27%', label: 'market share global' },
            { value: '5.5mm', label: 'iPhone Air — mais fino já feito' }
        ],

        ecosystem: [
            { icon: '🎧', name: 'AirPods', hook: 'Conecta instantâneo, áudio espacial' },
            { icon: '⌚', name: 'Apple Watch', hook: 'Saúde, notificações, desbloquear iPhone' },
            { icon: '💻', name: 'Mac', hook: 'Copiar no iPhone, colar no Mac' },
            { icon: '📱', name: 'iPad', hook: 'Sidecar, Handoff, mesmos apps' },
            { icon: '📺', name: 'Apple TV', hook: 'AirPlay, SharePlay, controle remoto' },
            { icon: '☁️', name: 'iCloud', hook: 'Fotos, backup e senhas em tudo' }
        ],

        funFacts: [
            'O primeiro iPhone (2007) tinha 128MB de RAM. O iPhone 17 Pro Max tem 12GB — 96x mais!',
            'A Apple vende mais de 230 milhões de iPhones por ano — são 7 por segundo.',
            'O iPhone 17 Pro Max grava vídeo em 4K 120fps — qualidade de cinema de verdade.',
            'O titânio do iPhone 17 Pro é o mesmo usado em naves espaciais e implantes médicos.',
            'Face ID analisa mais de 30.000 pontos invisíveis no seu rosto. É mais seguro que impressão digital.',
            'O iPhone Air com 5.5mm é mais fino que um lápis comum (7mm).',
            'A Apple tem mais de $160 bilhões em caixa — mais que o PIB de muitos países.'
        ],

        quizQuestions: [
            {
                q: 'Qual chip equipa o iPhone 17 Pro Max?',
                opts: ['A18 Pro', 'A19', 'A19 Pro', 'M3'],
                correct: 2,
                explanation: 'O iPhone 17 Pro e Pro Max usam o A19 Pro, o chip mais potente da Apple para iPhones.'
            },
            {
                q: 'Qual é o iPhone mais fino já feito pela Apple?',
                opts: ['iPhone 17', 'iPhone 17 Pro', 'iPhone Air', 'iPhone 16e'],
                correct: 2,
                explanation: 'O iPhone Air tem apenas 5.5mm de espessura — mais fino que um lápis!'
            },
            {
                q: 'A partir de qual modelo o iPhone ganhou USB-C?',
                opts: ['iPhone 14', 'iPhone 15', 'iPhone 16', 'iPhone 13'],
                correct: 1,
                explanation: 'O iPhone 15 (2023) foi o primeiro com USB-C, substituindo o Lightning.'
            },
            {
                q: 'O que é Apple Intelligence?',
                opts: ['Um app de estudos', 'IA integrada no iPhone com privacidade', 'Assistente de voz antiga', 'Nome do novo iOS'],
                correct: 1,
                explanation: 'Apple Intelligence é o sistema de IA da Apple que roda localmente no dispositivo, garantindo privacidade total.'
            },
            {
                q: 'Qual técnica de venda consiste em mostrar o modelo mais caro primeiro?',
                opts: ['Upsell', 'Cross-sell', 'Ancoragem de Preço', 'Downsell'],
                correct: 2,
                explanation: 'Ancoragem de preço: ao ver o mais caro primeiro, o modelo intermediário parece uma pechincha.'
            },
            {
                q: 'Qual foi o primeiro iPhone com Dynamic Island?',
                opts: ['iPhone 13 Pro', 'iPhone 14 Pro', 'iPhone 15', 'iPhone 14'],
                correct: 1,
                explanation: 'O iPhone 14 Pro (2022) estreou a Dynamic Island. No ano seguinte, todos os iPhone 15 ganharam.'
            },
            {
                q: 'O que o Camera Control faz?',
                opts: ['Tira print da tela', 'Controla zoom, foco e abre a câmera com toque', 'Liga a lanterna', 'Conecta com AirPods'],
                correct: 1,
                explanation: 'O Camera Control é um botão na lateral que abre a câmera, ajusta zoom deslizando e foca com toque leve.'
            },
            {
                q: 'Quantos anos de atualização iOS um iPhone recebe em média?',
                opts: ['2-3 anos', '4-5 anos', '6-7 anos', '10 anos'],
                correct: 2,
                explanation: 'iPhones recebem 6-7 anos de atualizações — muito mais que a maioria dos Androids.'
            },
            {
                q: 'Se o cliente diz "tá caro", qual a melhor abordagem?',
                opts: ['Dar desconto imediato', 'Mostrar o custo por mês e a durabilidade', 'Ignorar e mudar de assunto', 'Falar mal do Android'],
                correct: 1,
                explanation: 'Mostrar o valor dividido e a durabilidade do produto transforma "caro" em "investimento inteligente".'
            },
            {
                q: 'Qual material é usado no corpo do iPhone 17 Pro?',
                opts: ['Alumínio', 'Aço inox', 'Titânio', 'Plástico reciclado'],
                correct: 2,
                explanation: 'Desde o iPhone 15 Pro, a Apple usa titânio — mais leve e resistente que o aço inox.'
            },
            {
                q: 'O iPhone 16e tem qual grande novidade em conectividade?',
                opts: ['Wi-Fi 7', 'Modem Apple C1 próprio', 'Bluetooth 6.0', 'Satelite Starlink'],
                correct: 1,
                explanation: 'O iPhone 16e é o primeiro com o modem C1 desenvolvido pela própria Apple, substituindo o da Qualcomm.'
            },
            {
                q: 'O que é Genmoji?',
                opts: ['Um jogo da Apple', 'Emoji gerado por IA personalizado', 'Novo formato de GIF', 'App de figurinhas'],
                correct: 1,
                explanation: 'Genmoji usa Apple Intelligence pra criar emojis únicos a partir de descrições ou fotos.'
            }
        ]
    };
}
