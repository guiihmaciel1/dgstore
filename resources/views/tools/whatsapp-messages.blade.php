<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="whatsappMessages()">

            <!-- Header -->
            <div style="margin-bottom: 1.25rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Mensagens WhatsApp</h1>
                <p style="font-size: 0.875rem; color: #6b7280;">Templates prontos para copiar ou enviar direto</p>
            </div>

            <!-- Telefone do cliente -->
            <div style="margin-bottom: 1rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.875rem 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <svg style="width: 20px; height: 20px; color: #16a34a; flex-shrink: 0;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <input type="text" x-model="phone" placeholder="Telefone do cliente (ex: 5511999999999)"
                           style="flex: 1; padding: 0.375rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none;"
                           onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
            </div>

            <!-- Filtro por categoria -->
            <div style="display: flex; gap: 0.375rem; margin-bottom: 1rem; flex-wrap: wrap;">
                <template x-for="cat in categories" :key="cat.key">
                    <button @click="filterCat = filterCat === cat.key ? '' : cat.key" type="button"
                            :style="filterCat === cat.key
                                ? 'padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; background: #111827; color: white;'
                                : 'padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; border: 1px solid #e5e7eb; cursor: pointer; background: white; color: #6b7280;'"
                            x-text="cat.label">
                    </button>
                </template>
            </div>

            <!-- Templates -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <template x-for="(tpl, idx) in filteredTemplates" :key="tpl.key">
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <!-- Header do template -->
                        <button @click="toggle(tpl.key)" type="button"
                                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1.25rem; background: white; border: none; cursor: pointer; text-align: left;">
                            <div style="display: flex; align-items: center; gap: 0.625rem;">
                                <span style="font-size: 1.125rem;" x-text="tpl.icon"></span>
                                <div>
                                    <span style="font-size: 0.9375rem; font-weight: 600; color: #111827;" x-text="tpl.title"></span>
                                    <div style="font-size: 0.6875rem; color: #9ca3af;" x-text="tpl.subtitle"></div>
                                </div>
                            </div>
                            <svg width="16" height="16" :style="openKey === tpl.key ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #9ca3af; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Conteudo -->
                        <div x-show="openKey === tpl.key" x-transition style="border-top: 1px solid #f3f4f6; padding: 1rem 1.25rem;">
                            <!-- Campos editaveis -->
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <template x-for="(field, fIdx) in tpl.fields" :key="fIdx">
                                    <div style="flex: 1; min-width: 140px;">
                                        <label style="font-size: 0.7rem; font-weight: 500; color: #6b7280; display: block; margin-bottom: 2px;" x-text="field.label"></label>
                                        <input type="text" x-model="field.value"
                                               style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                </template>
                            </div>

                            <!-- Preview balao WhatsApp -->
                            <div style="background: #dcf8c6; border-radius: 0 12px 12px 12px; padding: 0.75rem 1rem; margin-bottom: 0.75rem; font-size: 0.85rem; color: #111827; white-space: pre-wrap; line-height: 1.5; max-height: 300px; overflow-y: auto;"
                                 x-text="getMessage(tpl)"></div>

                            <!-- Botoes -->
                            <div style="display: flex; gap: 0.5rem;">
                                <button @click="copyMsg(tpl)" type="button"
                                        :style="copiedKey === tpl.key ? 'flex:1;padding:0.5rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;border:none;cursor:pointer;background:#059669;color:white;display:flex;align-items:center;justify-content:center;gap:6px;' : 'flex:1;padding:0.5rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;border:none;cursor:pointer;background:#111827;color:white;display:flex;align-items:center;justify-content:center;gap:6px;'">
                                    <span x-text="copiedKey === tpl.key ? 'Copiado!' : 'Copiar'"></span>
                                </button>
                                <a :href="waLink(tpl)" target="_blank"
                                   style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; background: #16a34a; color: white; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                                    <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    Enviar
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function whatsappMessages() {
        return {
            phone: '',
            openKey: null,
            copiedKey: null,
            filterCat: '',

            categories: [
                { key: 'atendimento', label: 'Atendimento' },
                { key: 'venda', label: 'Venda' },
                { key: 'posvenda', label: 'P\u00f3s-venda' },
                { key: 'followup', label: 'Follow-up' },
            ],

            templates: [
                // ── ATENDIMENTO ──
                {
                    icon: '\uD83D\uDC4B', title: 'Boas-vindas', subtitle: 'Primeiro contato com o cliente', key: 'welcome', cat: 'atendimento',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                    ],
                },
                {
                    icon: '\uD83D\uDCCB', title: 'Question\u00e1rio de Avalia\u00e7\u00e3o', subtitle: 'Coletar dados do aparelho usado', key: 'evaluation', cat: 'atendimento',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                    ],
                },
                {
                    icon: '\uD83D\uDCB0', title: 'Or\u00e7amento', subtitle: 'Enviar proposta com valores', key: 'quote', cat: 'atendimento',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro 256GB' },
                        { label: 'Cor', value: 'Tit\u00e2nio Natural' },
                        { label: 'Pre\u00e7o Pix', value: '8.499' },
                        { label: 'Parcelas (at\u00e9)', value: '12x' },
                        { label: 'Valor parcela', value: '849' },
                    ],
                },
                {
                    icon: '\uD83D\uDD04', title: 'Proposta de trade-in', subtitle: 'Upgrade com aparelho usado', key: 'tradein', cat: 'atendimento',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Aparelho atual', value: 'iPhone 14 Pro' },
                        { label: 'Valor trade-in', value: '3.800' },
                        { label: 'Produto novo', value: 'iPhone 16 Pro 256GB' },
                        { label: 'Pre\u00e7o novo', value: '8.499' },
                    ],
                },
                {
                    icon: '\u2753', title: 'Tire d\u00favidas (seminovo)', subtitle: 'Explicar sobre seminovos', key: 'seminovo', cat: 'atendimento',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 15 Pro 128GB' },
                        { label: 'Condi\u00e7\u00e3o', value: 'estado excelente, bateria 92%' },
                    ],
                },

                // ── VENDA ──
                {
                    icon: '\u2705', title: 'Confirma\u00e7\u00e3o de venda', subtitle: 'Compra confirmada com sucesso', key: 'confirm', cat: 'venda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro 256GB' },
                        { label: 'Valor total', value: '8.499' },
                        { label: 'Forma pagamento', value: 'Pix' },
                    ],
                },
                {
                    icon: '\uD83D\uDCE6', title: 'Pronto para retirada', subtitle: 'Aparelho dispon\u00edvel para buscar', key: 'pickup', cat: 'venda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro 256GB' },
                    ],
                },
                {
                    icon: '\u23F0', title: 'Lembrete de reserva', subtitle: 'Lembrar prazo da reserva', key: 'reservation', cat: 'venda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro 256GB' },
                        { label: 'Valor sinal', value: '500' },
                        { label: 'Data limite', value: '' },
                    ],
                },

                // ── P\u00d3S-VENDA ──
                {
                    icon: '\uD83D\uDEE1\uFE0F', title: 'Garantia', subtitle: 'Informa\u00e7\u00f5es da garantia', key: 'warranty', cat: 'posvenda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro 256GB' },
                        { label: 'Prazo garantia', value: '90 dias' },
                        { label: 'Data compra', value: '' },
                    ],
                },
                {
                    icon: '\uD83D\uDE4F', title: 'Agradecimento', subtitle: 'P\u00f3s-venda e fideliza\u00e7\u00e3o', key: 'thanks', cat: 'posvenda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro' },
                    ],
                },
                {
                    icon: '\u2B50', title: 'Pedir avalia\u00e7\u00e3o', subtitle: 'Solicitar review no Google', key: 'review', cat: 'posvenda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                    ],
                },

                // ── FOLLOW-UP ──
                {
                    icon: '\uD83D\uDCE2', title: 'Produto chegou', subtitle: 'Avisar que o produto est\u00e1 dispon\u00edvel', key: 'arrived', cat: 'followup',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro 256GB' },
                        { label: 'Cor', value: 'Tit\u00e2nio Natural' },
                    ],
                },
                {
                    icon: '\uD83D\uDCAC', title: 'Retomar contato', subtitle: 'Cliente que n\u00e3o respondeu', key: 'followup', cat: 'followup',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto de interesse', value: 'iPhone 16 Pro' },
                    ],
                },
                {
                    icon: '\uD83C\uDF81', title: 'Promo\u00e7\u00e3o / Oportunidade', subtitle: 'Condi\u00e7\u00e3o especial por tempo limitado', key: 'promo', cat: 'followup',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro 256GB' },
                        { label: 'De (pre\u00e7o anterior)', value: '9.199' },
                        { label: 'Por (pre\u00e7o atual)', value: '8.499' },
                    ],
                },
            ],

            get filteredTemplates() {
                if (!this.filterCat) return this.templates;
                return this.templates.filter(t => t.cat === this.filterCat);
            },

            toggle(key) {
                this.openKey = this.openKey === key ? null : key;
            },

            getMessage(tpl) {
                var f = tpl.fields;
                var nome = f[0].value || 'cliente';

                switch (tpl.key) {
                    case 'welcome':
                        return 'Ol\u00e1, ' + nome + '! \uD83D\uDC4B\n\nQue bom ter voc\u00ea aqui! Eu sou da *DG Store*, especialista em produtos Apple.\n\nTrabalhamos com *iPhones, iPads, MacBooks, Apple Watch e acess\u00f3rios* \u2014 todos com proced\u00eancia verificada e garantia.\n\nMe conta, o que voc\u00ea est\u00e1 procurando? Vou te ajudar a encontrar a melhor op\u00e7\u00e3o pro seu perfil \uD83D\uDE09';

                    case 'evaluation':
                        return 'Ol\u00e1, ' + nome + '! \uD83D\uDCCB\n\nPara avaliarmos seu aparelho da melhor forma, preciso de algumas informa\u00e7\u00f5es. \u00c9 r\u00e1pido! Basta responder abaixo:\n\n\uD83D\uDCF1 *Modelo:*\n\uD83C\uDFA8 *Cor:*\n\uD83D\uDCBE *Capacidade (GB):*\n\uD83D\uDD0B *Sa\u00fade da bateria (%):*\n_(Ajustes > Bateria > Sa\u00fade da Bateria)_\n\n\uD83D\uDD27 *J\u00e1 foi aberto ou teve pe\u00e7a trocada?*\n_(tela, bateria, c\u00e2mera, etc.)_\n\n\uD83D\uDCE6 *Possui caixa original?*\n\uD83D\uDCCE *Possui acess\u00f3rios?*\n_(carregador, cabo, fone, etc.)_\n\n\uD83D\uDCDD *Possui algum detalhe est\u00e9tico?*\n_(riscos, amassados, marcas de uso, etc.)_\n\n\u2139\uFE0F *Alguma informa\u00e7\u00e3o adicional?*\n\nCom esses dados consigo te passar uma avalia\u00e7\u00e3o precisa e justa \uD83D\uDE09\n\n_DG Store \u2014 Avalia\u00e7\u00e3o transparente e sem compromisso_';

                    case 'quote':
                        return 'Ol\u00e1, ' + nome + '! Preparei seu or\u00e7amento \uD83D\uDCCB\n\n\uD83D\uDCF1 *' + f[1].value + '*\n\uD83C\uDFA8 Cor: ' + f[2].value + '\n\n\u2705 *Pix: R$ ' + f[3].value + '* _(melhor pre\u00e7o)_\n\uD83D\uDCB3 Cart\u00e3o: at\u00e9 *' + f[4].value + ' de R$ ' + f[5].value + '*\n\n\uD83D\uDD12 *O que voc\u00ea recebe:*\n\u2022 Produto com proced\u00eancia verificada\n\u2022 Nota fiscal\n\u2022 Garantia DG Store\n\u2022 Suporte p\u00f3s-venda\n\nTem alguma d\u00favida? Pode perguntar, estou aqui pra te ajudar! \uD83D\uDE0A';

                    case 'tradein':
                        var tradeVal = parseFloat(String(f[2].value).replace(/\./g,'').replace(',','.')) || 0;
                        var novoVal = parseFloat(String(f[4].value).replace(/\./g,'').replace(',','.')) || 0;
                        var resta = Math.max(0, novoVal - tradeVal);
                        return 'Ol\u00e1, ' + nome + '! Fiz a avalia\u00e7\u00e3o do seu aparelho \uD83D\uDD0D\n\n\uD83D\uDCF1 *Seu aparelho:* ' + f[1].value + '\n\uD83D\uDCB0 *Valor avaliado:* R$ ' + f[2].value + '\n\n\u2728 *Aparelho novo:* ' + f[3].value + '\n\uD83C\uDFF7\uFE0F *Pre\u00e7o:* R$ ' + f[4].value + '\n\n\uD83D\uDE80 *Voc\u00ea paga apenas: R$ ' + resta.toLocaleString('pt-BR') + '*\n_(e o restante pode ser parcelado no cart\u00e3o)_\n\n\u00c9 uma \u00f3tima oportunidade de fazer o upgrade sem pesar no bolso.\n\nQuer seguir com a troca? \uD83D\uDE0A';

                    case 'seminovo':
                        return 'Ol\u00e1, ' + nome + '! Entendo sua d\u00favida sobre seminovos, vou te explicar direitinho \uD83D\uDE09\n\n\uD83D\uDCF1 *' + f[1].value + '*\nCondi\u00e7\u00e3o: *' + f[2].value + '*\n\n\uD83D\uDD0D *O que verificamos antes de vender:*\n\u2022 Teste completo de todas as fun\u00e7\u00f5es\n\u2022 Verifica\u00e7\u00e3o de IMEI (livre de bloqueios)\n\u2022 Sa\u00fade da bateria\n\u2022 Inspe\u00e7\u00e3o visual detalhada\n\u2022 Higieniza\u00e7\u00e3o completa\n\n\uD83D\uDEE1\uFE0F *Vem com garantia DG Store* \u2014 voc\u00ea compra com a mesma seguran\u00e7a de um novo.\n\nSe quiser, posso te enviar fotos e v\u00eddeos do aparelho! \uD83D\uDCF8';

                    case 'confirm':
                        return nome + ', tudo certo! Sua compra foi *confirmada* \u2705\n\n\uD83D\uDCF1 *' + f[1].value + '*\n\uD83D\uDCB0 Valor: *R$ ' + f[2].value + '*\n\uD83D\uDCB3 Pagamento: *' + f[3].value + '*\n\n\uD83D\uDD12 *Voc\u00ea pode ficar tranquilo(a):*\n\u2022 Produto com proced\u00eancia verificada\n\u2022 Garantia DG Store inclusa\n\u2022 Suporte sempre que precisar\n\nVou te avisar assim que estiver tudo pronto, combinado?\n\nObrigado pela confian\u00e7a! \uD83D\uDE4F';

                    case 'pickup':
                        return 'Ol\u00e1, ' + nome + '! \uD83C\uDF89\n\nSeu *' + f[1].value + '* j\u00e1 est\u00e1 pronto e te esperando!\n\n\uD83D\uDCCD *Retirada na loja:*\n\uD83D\uDD50 Seg a Sex: 9h \u00e0s 18h\n\uD83D\uDD50 S\u00e1b: 9h \u00e0s 13h\n\n\uD83D\uDCCB *Traga um documento com foto.*\n\nSe precisar de ajuda para configurar o aparelho, fazemos na hora, sem custo! \uD83D\uDE09\n\nTe esperamos!';

                    case 'reservation':
                        var dataLimite = f[3].value || 'consultar';
                        return 'Ol\u00e1, ' + nome + '! Passando para lembrar da sua reserva \uD83D\uDE09\n\n\uD83D\uDCF1 *' + f[1].value + '*\n\uD83D\uDCB0 Sinal: *R$ ' + f[2].value + '*\n\uD83D\uDCC5 V\u00e1lida at\u00e9: *' + dataLimite + '*\n\nSeu produto est\u00e1 separado e garantido! Para finalizar, \u00e9 s\u00f3 me chamar aqui.\n\nCaso precise de mais tempo ou tenha alguma d\u00favida, s\u00f3 avisar \u2014 estou \u00e0 disposi\u00e7\u00e3o \uD83D\uDE0A';

                    case 'warranty':
                        var data = f[3].value || new Date().toLocaleDateString('pt-BR');
                        return 'Ol\u00e1, ' + nome + '! Aqui est\u00e3o os detalhes da sua garantia \uD83D\uDEE1\uFE0F\n\n\uD83D\uDCF1 *' + f[1].value + '*\n\uD83D\uDCC5 Compra: *' + data + '*\n\u23F0 Garantia: *' + f[2].value + '*\n\n\u2705 *Cobre:*\n\u2022 Defeitos de fabrica\u00e7\u00e3o\n\u2022 Problemas de hardware\n\u2022 Mau funcionamento espont\u00e2neo\n\n\u274C *N\u00e3o cobre:*\n\u2022 Danos por queda ou l\u00edquido\n\u2022 Mau uso ou viola\u00e7\u00e3o\n\nSe notar qualquer coisa diferente no aparelho, me chama aqui. *A gente resolve.* \uD83D\uDCAA\n\nConte sempre com a DG Store!';

                    case 'thanks':
                        return 'Ol\u00e1, ' + nome + '! \uD83D\uDE4F\n\nObrigado por escolher a *DG Store*!\n\nEspero que esteja aproveitando seu *' + f[1].value + '* \uD83D\uDCF1\u2728\n\nLembrando que voc\u00ea tem:\n\u2705 Garantia ativa\n\u2705 Suporte direto por aqui\n\u2705 Condi\u00e7\u00f5es especiais para pr\u00f3ximas compras\n\nSe algum amigo ou familiar estiver procurando um Apple, pode indicar! Terei o prazer de atender com o mesmo cuidado \uD83D\uDE09\n\nUm abra\u00e7o!';

                    case 'review':
                        return 'Ol\u00e1, ' + nome + '! Tudo bem? \uD83D\uDE0A\n\nVoc\u00ea teria *1 minutinho* para deixar uma avalia\u00e7\u00e3o da DG Store no Google? \u2B50\n\nIsso nos ajuda muito a continuar oferecendo o melhor atendimento.\n\nSua opini\u00e3o faz toda a diferen\u00e7a para n\u00f3s! \uD83D\uDE4F\n\nMuito obrigado!';

                    case 'arrived':
                        return 'Ol\u00e1, ' + nome + '! Tenho uma boa not\u00edcia \uD83C\uDF89\n\nO *' + f[1].value + '* na cor *' + f[2].value + '* acabou de chegar!\n\n\uD83D\uDD12 Produto com proced\u00eancia verificada e garantia DG Store.\n\nComo voc\u00ea tinha demonstrado interesse, quis te avisar em primeira m\u00e3o \u2014 costuma sair r\u00e1pido \uD83D\uDE09\n\nQuer que eu reserve pra voc\u00ea?';

                    case 'followup':
                        return 'Ol\u00e1, ' + nome + '! Tudo bem? \uD83D\uDE0A\n\nVi que voc\u00ea se interessou pelo *' + f[1].value + '* e queria saber se posso te ajudar com alguma d\u00favida.\n\nEstou \u00e0 disposi\u00e7\u00e3o para:\n\u2022 Detalhar as condi\u00e7\u00f5es de pagamento\n\u2022 Enviar fotos/v\u00eddeos do produto\n\u2022 Explicar sobre garantia e proced\u00eancia\n\nSem compromisso! Fico feliz em ajudar \uD83D\uDE09';

                    case 'promo':
                        return 'Ol\u00e1, ' + nome + '! Quero compartilhar uma oportunidade com voc\u00ea \uD83C\uDF1F\n\n\uD83D\uDCF1 *' + f[1].value + '*\n\n\u274C ~De R$ ' + f[2].value + '~\n\u2705 *Por R$ ' + f[3].value + '* (condi\u00e7\u00e3o especial)\n\n\uD83D\uDD12 Produto com proced\u00eancia verificada, nota fiscal e garantia DG Store.\n\n\u23F0 *Condi\u00e7\u00e3o por tempo limitado* \u2014 sujeito a disponibilidade.\n\nTem interesse? Me chama aqui que te passo todos os detalhes! \uD83D\uDE09';

                    default:
                        return '';
                }
            },

            copyMsg(tpl) {
                var self = this;
                var text = this.getMessage(tpl);
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(function() {
                        self.copiedKey = tpl.key;
                        setTimeout(function() { self.copiedKey = null; }, 2500);
                    });
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.focus(); ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    self.copiedKey = tpl.key;
                    setTimeout(function() { self.copiedKey = null; }, 2500);
                }
            },

            waLink(tpl) {
                var num = this.phone.replace(/\D/g, '');
                return 'https://wa.me/' + (num || '') + '?text=' + encodeURIComponent(this.getMessage(tpl));
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
