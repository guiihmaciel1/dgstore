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

        // ── Módulo 5: Venda Consultiva ──
        consultiveQuestions: [
            { icon: '📱', question: 'Qual iPhone você usa hoje?', why: 'Entender o ponto de partida ajuda a sugerir upgrade certeiro e calcular trade-in.' },
            { icon: '📷', question: 'Você usa mais pra fotos, trabalho ou redes sociais?', why: 'Direciona a recomendação — câmera, bateria ou tela.' },
            { icon: '😤', question: 'O que te incomoda no celular atual?', why: 'Descobrir a dor principal: lentidão, bateria, espaço ou câmera fraca.' },
            { icon: '📏', question: 'Prefere tela grande ou algo mais compacto?', why: 'Elimina modelos logo de cara e mostra que você ouve o cliente.' },
            { icon: '💰', question: 'Tem um orçamento em mente ou quer ver as opções?', why: 'Evita mostrar algo fora do alcance e gera confiança.' },
            { icon: '🔄', question: 'Tem algum aparelho pra dar de entrada?', why: 'Abre a porta pro trade-in e reduz o preço final percebido.' }
        ],

        // ── Módulo 6: Gatilhos Mentais ──
        mentalTriggers: [
            { icon: '⏳', name: 'Escassez Real', desc: 'Mostre que o estoque é limitado — quando é verdade. Nunca minta sobre estoque.', example: 'Temos só 2 unidades dessa cor. Quando acaba, só na próxima remessa.', when: 'Quando realmente houver poucas unidades ou cor/modelo raro.' },
            { icon: '⏰', name: 'Urgência Ética', desc: 'Prazos reais de promoção ou condição especial.', example: 'Essa condição de parcelamento sem juros vale até sexta-feira.', when: 'Quando há promoção com prazo real ou o dólar pode subir.' },
            { icon: '👥', name: 'Prova Social', desc: 'Mostre que outros clientes já compraram e aprovaram.', example: 'Esse modelo é o mais vendido da loja esse mês. O pessoal ama a câmera.', when: 'Na apresentação do produto, pra gerar confiança.' },
            { icon: '🏅', name: 'Autoridade', desc: 'Posicione a DG Store como especialista Apple.', example: 'Somos especialistas Apple. Todo aparelho passa por checklist de 15 pontos.', when: 'Na abertura ou quando o cliente questiona procedência.' },
            { icon: '🎁', name: 'Reciprocidade', desc: 'Ofereça algo de valor primeiro — simulação, dica, atenção.', example: 'Preparei essa simulação especial pra você com trade-in e parcelamento.', when: 'Na abertura — gera obrigação inconsciente de retribuir.' },
            { icon: '⭐', name: 'Exclusividade', desc: 'Faça o cliente se sentir especial com condição única.', example: 'Consigo uma condição especial pra fechar hoje. Deixa eu ver com meu gerente.', when: 'No fechamento, quando o cliente está quase decidindo.' },
            { icon: '⚓', name: 'Ancoragem de Preço', desc: 'Mostre o modelo mais caro primeiro. O intermediário parecerá barato.', example: 'Esse é o Pro Max, R$ 12.999. Mas olha, o Pro tem quase tudo igual por R$ 9.999.', when: 'Sempre que for apresentar modelos — comece pelo topo.' },
            { icon: '🔮', name: 'Antecipação', desc: 'Use o futuro a favor da decisão presente.', example: 'O próximo lançamento Apple vai aumentar os preços dos modelos atuais.', when: 'Quando o cliente quer esperar — mostre que adiar custa mais.' }
        ],

        // ── Módulo 7: WhatsApp ──
        whatsappScripts: [
            {
                title: 'Primeiro Contato — Tráfego Pago',
                context: 'Lead clicou em anúncio e mandou mensagem.',
                messages: [
                    { role: 'cliente', text: 'Oi, vi o anúncio do iPhone 17 Pro.' },
                    { role: 'vendedor', text: 'Oi, [nome]! Que bom que se interessou! 😊 O 17 Pro é incrível. Você tá buscando um novo ou seminovo?' },
                    { role: 'cliente', text: 'Novo. Quanto custa?' },
                    { role: 'vendedor', text: 'O 17 Pro 256GB tá por R$ 9.999 à vista ou em até 12x sem juros. Ele tem câmera de 48MP, chip A19 Pro e 7 anos de atualização. Você tem algum aparelho pra dar de entrada? Posso fazer uma simulação personalizada!' }
                ]
            },
            {
                title: 'Primeiro Contato — Orgânico',
                context: 'Cliente achou a loja no Instagram ou indicação.',
                messages: [
                    { role: 'cliente', text: 'Boa tarde! Vocês têm iPhone 16?' },
                    { role: 'vendedor', text: 'Boa tarde! Temos sim! 😊 Temos o 16 de 128GB e 256GB. Posso te ajudar a escolher? Você usa mais pra fotos, trabalho ou redes sociais?' },
                    { role: 'cliente', text: 'Mais pra foto dos filhos e WhatsApp.' },
                    { role: 'vendedor', text: 'Pra fotos, o de 256GB é ideal — mais espaço pros vídeos em 4K e a câmera de 48MP é absurda. Quer que eu mande umas fotos feitas com ele? A qualidade impressiona!' }
                ]
            },
            {
                title: 'Follow-up — 1 hora depois',
                context: 'Cliente pediu pra pensar.',
                messages: [
                    { role: 'vendedor', text: 'Oi, [nome]! Só passando pra dizer que aquela simulação que fiz continua valendo. Se surgir qualquer dúvida, é só mandar! 😊' }
                ]
            },
            {
                title: 'Follow-up — 24 horas depois',
                context: 'Cliente não respondeu.',
                messages: [
                    { role: 'vendedor', text: 'Oi, [nome]! Tudo bem? Vi que você se interessou pelo iPhone [modelo] ontem. Surgiu alguma dúvida que eu possa ajudar? Aproveito pra avisar que temos só mais [X] unidades dessa cor 😉' }
                ]
            },
            {
                title: 'Nunca Responda Só o Preço',
                context: 'Cliente perguntou direto o preço.',
                messages: [
                    { role: 'cliente', text: 'Quanto custa o iPhone 17 Pro?' },
                    { role: 'vendedor', text: 'O iPhone 17 Pro 256GB tá por R$ 9.999 à vista ou 12x de R$ 833. Ele tem câmera de 48MP, chip A19 Pro e 7 anos de atualização. Você prefere o de 256GB ou precisa de mais espaço?' }
                ]
            }
        ],

        whatsappMistakes: [
            { icon: '🐌', mistake: 'Demorar mais de 5 minutos pra responder. O cliente já está falando com o concorrente.', fix: 'Responda em até 5 minutos. Configure notificações e reveze com a equipe.' },
            { icon: '🤖', mistake: 'Respostas secas e robóticas: "R$ 7.299." — sem contexto, sem valor.', fix: 'Sempre agregue valor: specs relevantes, parcelamento, diferenciais.' },
            { icon: '📋', mistake: 'Mandar tabela de preços genérica sem personalizar pro cliente.', fix: 'Faça simulação personalizada no sistema e envie screenshot.' },
            { icon: '🔇', mistake: 'Enviar áudio de 3+ minutos. Ninguém ouve.', fix: 'Áudios de no máximo 1 minuto. Informações importantes sempre em texto.' },
            { icon: '😴', mistake: 'Não fazer follow-up. Cliente disse "vou pensar" e você nunca mais mandou nada.', fix: 'Follow-up em 1h, 24h e 3 dias. Com educação, sem pressão.' },
            { icon: '📸', mistake: 'Enviar foto ruim do produto — escura, tremida, com bagunça atrás.', fix: 'Foto em fundo limpo, boa luz, mostrando o estado do aparelho.' }
        ],

        // ── Módulo 8: Fechamento ──
        closingTechniques: [
            { icon: '🔀', name: 'Fechamento por Alternativa', desc: 'Ofereça duas opções — ambas levam à compra.', example: 'Prefere o 256GB prata ou o 512GB preto?', tip: 'Funciona quando o cliente já demonstrou interesse. Nunca pergunte "quer ou não quer?".' },
            { icon: '📝', name: 'Fechamento por Resumo', desc: 'Resuma tudo e peça a confirmação.', example: 'Então ficamos com iPhone 17 Pro 256GB preto, com capinha e película. Vou preparar?', tip: 'Ideal após longa conversa. O resumo organiza a decisão na cabeça do cliente.' },
            { icon: '⏰', name: 'Fechamento por Urgência Real', desc: 'Use escassez ou prazo real a seu favor.', example: 'Esse é o último nessa cor. O próximo lote só daqui 2 semanas.', tip: 'Só funciona quando é verdade. Mentir destrói a confiança.' },
            { icon: '🎁', name: 'Fechamento por Concessão', desc: 'Ofereça algo extra pra selar o negócio.', example: 'Consigo incluir a película se fecharmos hoje. Que tal?', tip: 'A concessão deve parecer um esforço seu. "Vou ver com meu gerente se consigo..."' },
            { icon: '🤫', name: 'Fechamento Silencioso', desc: 'Apresente a proposta e aguarde. Quem falar primeiro, perde.', example: 'Aqui está a simulação completa. O que você acha?', tip: 'Difícil de dominar, mas poderosíssimo. Deixe o silêncio trabalhar.' },
            { icon: '📖', name: 'Fechamento por História', desc: 'Conte o caso de outro cliente parecido.', example: 'Uma cliente comprou esse mesmo modelo semana passada e mandou foto dizendo que amou a câmera.', tip: 'Pessoas se identificam com histórias. Use casos reais (sem expor nomes).' },
            { icon: '💳', name: 'Fechamento por Parcelamento', desc: 'Transforme o valor total em parcela digerível.', example: 'Dividido em 10x fica R$ 729 por mês. Menos de R$ 25 por dia pro melhor smartphone do mundo.', tip: 'Compare com algo pequeno: café, streaming, uber. Recontextualiza o valor.' }
        ],

        // ── Módulo 9: Objeções V2 ──
        objectionsV2: [
            { icon: '💸', objection: 'Tá caro', response: 'Entendo! Dividido fica R$ X por mês. E um iPhone dura fácil 5 anos com atualizações — na ponta do lápis, é o celular com menor custo por ano. E o valor de revenda é o mais alto do mercado.', tip: 'Nunca diga "não é caro". Reconheça e redirecione pra custo-benefício e parcelamento.' },
            { icon: '🤔', objection: 'Vou pensar', response: 'Claro! Posso perguntar o que te deixou em dúvida? Talvez eu consiga ajudar agora. E se quiser, posso reservar o aparelho enquanto você decide — assim ninguém leva antes.', tip: '"Vou pensar" geralmente esconde outra objeção. Descubra qual é.' },
            { icon: '🏷️', objection: 'Achei mais barato', response: 'Entendo! Posso perguntar onde você viu? Aqui na DG a gente garante: IMEI verificado, nota fiscal, garantia e suporte pós-venda. Muitas vezes o mais barato sai caro se não tem essa segurança.', tip: 'Nunca fale mal do concorrente. Destaque seus diferenciais.' },
            { icon: '🚫', objection: 'Não tenho dinheiro agora', response: 'Sem problema! A gente pode fazer uma pré-venda — você dá um sinal de R$ 50 ou R$ 100 e o aparelho fica reservado no seu nome. Quando tiver o restante, é só vir finalizar.', tip: 'A pré-venda é perfeita pra esse cenário. Use o sistema!' },
            { icon: '💑', objection: 'Preciso consultar meu marido/esposa', response: 'Claro! Posso te mandar um resumo pelo WhatsApp com a simulação completa? Assim vocês decidem juntos com todas as informações na mão.', tip: 'Facilite a decisão conjunta. Envie resumo profissional e acompanhe.' },
            { icon: '🤖', objection: 'Android faz a mesma coisa', response: 'Entendo o ponto! Mas o iPhone tem 6-7 anos de atualizações, segurança muito superior e na revenda vale muito mais. Um Galaxy perde 50% do valor em 1 ano — o iPhone mantém 70%.', tip: 'Use números reais: anos de atualização, porcentagem de revenda. Dados convencem.' },
            { icon: '🔌', objection: 'Não vem carregador', response: 'É verdade. A Apple tirou pensando no meio ambiente. Mas qualquer USB-C funciona — você provavelmente já tem um! E se precisar, a gente tem ótimas opções aqui.', tip: 'Normalize e ofereça solução. Se puder incluir no kit, use como concessão de fechamento.' },
            { icon: '😰', objection: 'Seminovo me dá medo', response: 'Entendo perfeitamente! Por isso aqui na DG é diferente: IMEI verificado, bateria com saúde informada, checklist de 15 pontos e 3 meses de garantia. Qualquer problema, é só voltar.', tip: 'Mostre o checklist ao cliente. O visual gera confiança imediata.' },
            { icon: '🐌', objection: 'iPhone trava', response: 'Pode ter sido um modelo antigo ou com armazenamento lotado. Os iPhones atuais com A18/A19 são os mais rápidos do mundo nos benchmarks. Quer testar aqui na loja?', tip: 'Ofereça a demonstração na mão. Experiência ao vivo desfaz mitos.' },
            { icon: '👀', objection: 'Tô só olhando', response: 'Fique à vontade! Me conta, o que chamou sua atenção? Se quiser, posso mostrar as diferenças entre os modelos pra facilitar sua escolha depois.', tip: 'Não pressione. Engaje com curiosidade genuína — muitos "só olhando" compram.' },
            { icon: '📱', objection: 'Meu celular ainda funciona', response: 'Ótimo! Mas sabia que quanto mais você espera, menos seu aparelho vale na troca? Hoje ele vale R$ X, em 6 meses pode cair pra R$ Y. O melhor momento pra trocar é quando ainda vale bastante.', tip: 'Use a depreciação a favor da urgência. Dados reais de desvalorização convencem.' },
            { icon: '✈️', objection: 'Vou comprar no Paraguai/importar', response: 'Entendo a tentação! Mas pensa: sem garantia no Brasil, risco de apreensão na alfândega, sem nota fiscal e sem suporte local. Se der problema, você gasta mais pra resolver do que economizou.', tip: 'Foque no risco real: alfândega, garantia internacional, suporte zero.' }
        ],

        // ── Módulo 10: Seminovo Confiança ──
        seminovoArguments: [
            { icon: '🔍', title: 'IMEI Verificado', script: 'Olha aqui: IMEI limpo, sem bloqueio, sem pendência. A gente verifica cada aparelho que entra na loja.' },
            { icon: '🔋', title: 'Saúde da Bateria', script: 'Esse aqui tá com 91% de saúde da bateria — praticamente novo. A Apple considera ideal acima de 80%.' },
            { icon: '🛡️', title: 'Garantia na Loja', script: 'Você leva 3 meses de garantia na loja. Se der qualquer problema, é só voltar aqui que a gente resolve.' },
            { icon: '📋', title: 'Checklist Completo', script: 'A gente testa tudo: tela, touch, câmeras, alto-falante, microfone, botões, Face ID, Wi-Fi, carregamento. Tá tudo OK.' },
            { icon: '🧾', title: 'Nota Fiscal', script: 'Leva com nota fiscal. Isso é garantia de procedência e te protege juridicamente.' },
            { icon: '💎', title: 'Custo-Benefício', script: 'Esse 14 Pro faz praticamente tudo que o 17 faz na câmera, e custa 40% menos. É o melhor custo-benefício da loja.' }
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
            // Produto (4 perguntas)
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
                q: 'Quantos anos de atualização iOS um iPhone recebe em média?',
                opts: ['2-3 anos', '4-5 anos', '6-7 anos', '10 anos'],
                correct: 2,
                explanation: 'iPhones recebem 6-7 anos de atualizações — muito mais que a maioria dos Androids.'
            },
            {
                q: 'Qual material é usado no corpo do iPhone 17 Pro?',
                opts: ['Alumínio', 'Aço inox', 'Titânio', 'Plástico reciclado'],
                correct: 2,
                explanation: 'Desde o iPhone 15 Pro, a Apple usa titânio — mais leve e resistente que o aço inox.'
            },
            // Cenários de Venda Consultiva (3 perguntas)
            {
                q: 'Cliente diz: "Quero um iPhone bom pra foto." O que você faz primeiro?',
                opts: ['Mostra o Pro Max', 'Pergunta o que ele fotografa e com que frequência', 'Diz que todos tiram boas fotos', 'Mostra a tabela de preços'],
                correct: 1,
                explanation: 'Venda consultiva: entenda a necessidade antes de recomendar. Pergunte mais pra indicar o modelo certo.'
            },
            {
                q: 'Qual a sequência correta da técnica SPIN?',
                opts: ['Situação, Problema, Implicação, Necessidade', 'Social, Preço, Interesse, Negociação', 'Solução, Proposta, Investimento, Nota', 'Saudação, Pergunta, Indicação, Negócio'],
                correct: 0,
                explanation: 'SPIN: Situação (entender o contexto), Problema (descobrir a dor), Implicação (ampliar a dor), Necessidade (mostrar a solução).'
            },
            {
                q: 'O que significa venda consultiva?',
                opts: ['Vender o produto mais caro', 'Entender o cliente antes de recomendar', 'Dar muitas opções pra ele escolher', 'Pressionar até fechar'],
                correct: 1,
                explanation: 'Venda consultiva resolve problemas. Você entende, recomenda e o cliente decide com confiança.'
            },
            // Cenários de Gatilhos Mentais (2 perguntas)
            {
                q: 'Qual gatilho mental é usar: "Esse modelo é o mais vendido da loja esse mês"?',
                opts: ['Escassez', 'Prova Social', 'Ancoragem', 'Reciprocidade'],
                correct: 1,
                explanation: 'Prova social: mostrar que outros já compraram gera confiança e reduz a sensação de risco.'
            },
            {
                q: 'Qual o erro FATAL ao usar gatilhos mentais?',
                opts: ['Usar mais de um por conversa', 'Mentir sobre estoque ou promoção', 'Mencionar outros clientes', 'Mostrar o modelo mais caro primeiro'],
                correct: 1,
                explanation: 'Escassez falsa destrói a confiança. Só use gatilhos quando forem verdadeiros.'
            },
            // Cenários WhatsApp (3 perguntas)
            {
                q: 'Cliente manda: "Quanto custa o iPhone 17 Pro?" O que você NÃO deve fazer?',
                opts: ['Responder só "R$ 9.999"', 'Perguntar pra que ele mais usa', 'Apresentar preço com valor agregado', 'Oferecer simulação personalizada'],
                correct: 0,
                explanation: 'Nunca responda só o preço! Agregue valor: specs relevantes, parcelamento, diferenciais.'
            },
            {
                q: 'Qual o tempo máximo ideal pra responder um lead no WhatsApp?',
                opts: ['30 minutos', '1 hora', '5 minutos', '15 minutos'],
                correct: 2,
                explanation: 'Regra dos 5 minutos: responder rápido aumenta a conversão em 21x comparado a 30 minutos.'
            },
            {
                q: 'Quando usar áudio no WhatsApp?',
                opts: ['Sempre, é mais pessoal', 'Pra criar conexão, no máximo 1 minuto', 'Nunca, só texto', 'Pra mandar tabela de preços'],
                correct: 1,
                explanation: 'Áudio cria conexão pessoal, mas deve ser curto (max 1 min). Informações importantes sempre em texto.'
            },
            // Cenários de Fechamento (3 perguntas)
            {
                q: 'Cliente já escolheu o modelo e pergunta sobre cores. O que isso indica?',
                opts: ['Que ele quer desconto', 'Que é um sinal de compra — hora de fechar', 'Que ele tá indeciso sobre o modelo', 'Que ele tá comparando com concorrente'],
                correct: 1,
                explanation: 'Perguntar sobre cor, armazenamento ou parcelamento são sinais de compra. Não perca o timing!'
            },
            {
                q: '"Prefere o 256GB prata ou o 512GB preto?" Qual técnica é essa?',
                opts: ['Fechamento por urgência', 'Fechamento por história', 'Fechamento por alternativa', 'Fechamento silencioso'],
                correct: 2,
                explanation: 'Fechamento por alternativa: ambas as opções levam à compra. Nunca pergunte "quer ou não quer?".'
            },
            {
                q: 'Depois que o cliente disse SIM, o que você deve fazer?',
                opts: ['Continuar argumentando pra ele não desistir', 'Parar de vender e finalizar a compra', 'Mostrar mais um modelo melhor', 'Perguntar se ele tem certeza'],
                correct: 1,
                explanation: 'Overselling desfaz vendas. Cliente disse sim? Finalize rápido, com sorriso e profissionalismo.'
            },
            // Cenários de Objeções (3 perguntas)
            {
                q: 'Cliente diz "Vou pensar". O que fazer?',
                opts: ['Dizer "ok, boa sorte"', 'Entender a dúvida real e oferecer reserva via pré-venda', 'Dar desconto imediato', 'Insistir que é a última unidade'],
                correct: 1,
                explanation: '"Vou pensar" esconde outra objeção. Descubra qual é e use a pré-venda pra segurar o cliente.'
            },
            {
                q: 'Qual é o método AER pra lidar com objeções?',
                opts: ['Argumentar, Explicar, Rebater', 'Acolher, Entender, Redirecionar', 'Atacar, Empurrar, Repetir', 'Anotar, Estudar, Responder'],
                correct: 1,
                explanation: 'AER: Acolha a preocupação, Entenda a objeção real, Redirecione com valor. Nunca rebata diretamente.'
            },
            {
                q: 'Cliente: "Achei mais barato no Paraguai." Melhor resposta?',
                opts: ['Falar que é falsificado', 'Dar o mesmo preço', 'Destacar garantia local, nota fiscal e risco de alfândega', 'Ignorar e mudar de assunto'],
                correct: 2,
                explanation: 'Foque nos riscos reais: sem garantia no Brasil, apreensão na alfândega, sem nota fiscal nem suporte.'
            },
            // Cenários Seminovo + Ferramentas (2 perguntas)
            {
                q: 'Cliente com medo de seminovo. O que gera mais confiança?',
                opts: ['Dizer que nunca dá problema', 'Mostrar o checklist, IMEI verificado e garantia na loja', 'Dar desconto extra', 'Comparar com o Mercado Livre'],
                correct: 1,
                explanation: 'Provas concretas (checklist, IMEI, garantia) destroem o medo. Mostre, não apenas fale.'
            },
            {
                q: 'Quando usar a pré-venda do sistema?',
                opts: ['Sempre, em toda venda', 'Quando o cliente quer mas precisa de tempo pra decidir', 'Só pra aparelhos usados', 'Nunca, é burocrático'],
                correct: 1,
                explanation: 'A pré-venda reserva o produto sem pressão. Perfeita pra "vou pensar" e "preciso consultar".'
            }
        ]
    };
}
