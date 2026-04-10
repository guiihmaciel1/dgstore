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
                    <input type="text" x-model="phone" placeholder="Telefone do cliente (ex: 5517996498338)"
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
                                <a :href="phone.replace(/\D/g, '') ? waLink(tpl) : '#'" target="_blank"
                                   :style="phone.replace(/\D/g, '') ? 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; background: #16a34a; color: white; text-decoration: none; display: flex; align-items: center; gap: 6px;' : 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; background: #d1d5db; color: #9ca3af; text-decoration: none; display: flex; align-items: center; gap: 6px; pointer-events: none; cursor: not-allowed;'"
                                   @click.prevent="if(!phone.replace(/\D/g, '')) return false;">
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
                // ATENDIMENTO
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
                    icon: '\uD83D\uDD04', title: 'Proposta de Trade-in', subtitle: 'Upgrade com aparelho usado', key: 'tradein', cat: 'atendimento',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Aparelho atual', value: 'iPhone 14 Pro' },
                        { label: 'Valor trade-in', value: '3.800' },
                        { label: 'Produto novo', value: 'iPhone 16 Pro 256GB' },
                        { label: 'Pre\u00e7o novo', value: '8.499' },
                    ],
                },
                {
                    icon: '\uD83D\uDCF1', title: 'Seminovo', subtitle: 'Explicar qualidade dos seminovos', key: 'seminovo', cat: 'atendimento',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 15 Pro 128GB' },
                        { label: 'Condi\u00e7\u00e3o', value: 'estado excelente, bateria 92%' },
                    ],
                },
                {
                    icon: '\uD83D\uDCCA', title: 'Comparativo de modelos', subtitle: 'Ajudar na escolha entre modelos', key: 'compare', cat: 'atendimento',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Modelo 1', value: 'iPhone 15 Pro' },
                        { label: 'Pre\u00e7o modelo 1', value: '6.499' },
                        { label: 'Modelo 2', value: 'iPhone 16 Pro' },
                        { label: 'Pre\u00e7o modelo 2', value: '8.499' },
                    ],
                },

                // VENDA
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
                {
                    icon: '\uD83D\uDCB3', title: 'Dados para pagamento', subtitle: 'Enviar dados de Pix ou cart\u00e3o', key: 'payment', cat: 'venda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Valor', value: '8.499' },
                        { label: 'Chave Pix', value: '' },
                        { label: 'Titular', value: 'DG Store' },
                    ],
                },

                // P\u00d3S-VENDA
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
                    icon: '\u2B50', title: 'Avalia\u00e7\u00e3o no Google', subtitle: 'Solicitar avalia\u00e7\u00e3o do cliente', key: 'review', cat: 'posvenda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                    ],
                },
                {
                    icon: '\uD83D\uDCF2', title: 'Dicas de uso', subtitle: 'Orienta\u00e7\u00f5es para o aparelho novo', key: 'tips', cat: 'posvenda',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro' },
                    ],
                },

                // FOLLOW-UP
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
                    icon: '\uD83C\uDF1F', title: 'Condi\u00e7\u00e3o especial', subtitle: 'Oportunidade por tempo limitado', key: 'promo', cat: 'followup',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 16 Pro 256GB' },
                        { label: 'De (pre\u00e7o anterior)', value: '9.199' },
                        { label: 'Por (pre\u00e7o atual)', value: '8.499' },
                    ],
                },
                {
                    icon: '\uD83C\uDF82', title: 'Anivers\u00e1rio do cliente', subtitle: 'Parabenizar e oferecer condi\u00e7\u00e3o', key: 'birthday', cat: 'followup',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                    ],
                },
                {
                    icon: '\uD83D\uDD01', title: 'Upgrade', subtitle: 'Sugerir troca para modelo novo', key: 'upgrade', cat: 'followup',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Modelo atual do cliente', value: 'iPhone 13' },
                        { label: 'Modelo sugerido', value: 'iPhone 16 Pro' },
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
                var nome = f[0].value || '';
                var ola = nome ? ('Ol\u00e1, ' + nome + '!') : 'Ol\u00e1!';

                switch (tpl.key) {
                    case 'welcome':
                        return ola + ' Tudo bem?\n\nSeja muito bem-vinda(o) \u00e0 *DG Store*! Sou a Sophia, consultora especializada em produtos Apple.\n\nTrabalhamos com *iPhones novos e seminovos*, todos com proced\u00eancia verificada e garantia de loja.\n\nPosso ajudar a encontrar o modelo ideal para voc\u00ea. Qual aparelho est\u00e1 buscando?';

                    case 'evaluation':
                        return 'Para realizar a avalia\u00e7\u00e3o do seu aparelho, preciso de algumas informa\u00e7\u00f5es:\n\n*Modelo:*\n*Cor:*\n*Capacidade (GB):*\n*Sa\u00fade da bateria (%):*\n*J\u00e1 foi aberto ou trocou alguma pe\u00e7a?*\n*Possui caixa e acess\u00f3rios originais?*\n*Condi\u00e7\u00e3o est\u00e9tica (riscos, marcas):*\n*Alguma observa\u00e7\u00e3o adicional:*\n\nCom essas informa\u00e7\u00f5es, consigo apresentar a melhor proposta para o seu aparelho.\n\nAguardo o retorno!';

                    case 'quote':
                        return ola + ' Segue o or\u00e7amento conforme solicitado:\n\n*' + f[1].value + '*\nCor: ' + f[2].value + '\n\n*Pix/Dinheiro: R$ ' + f[3].value + '* (melhor condi\u00e7\u00e3o)\nCart\u00e3o: at\u00e9 *' + f[4].value + ' de R$ ' + f[5].value + '*\n\n*Incluso na compra:*\n\u2022 Proced\u00eancia verificada\n\u2022 Nota fiscal\n\u2022 Garantia DG Store\n\u2022 Suporte p\u00f3s-venda\n\nCaso tenha alguma d\u00favida sobre o produto ou condi\u00e7\u00f5es, fico \u00e0 disposi\u00e7\u00e3o.';

                    case 'tradein':
                        var tradeVal = parseFloat(String(f[2].value).replace(/\./g,'').replace(',','.')) || 0;
                        var novoVal = parseFloat(String(f[4].value).replace(/\./g,'').replace(',','.')) || 0;
                        var resta = Math.max(0, novoVal - tradeVal);
                        return ola + ' Conclu\u00ed a avalia\u00e7\u00e3o do seu aparelho.\n\n*Seu aparelho:* ' + f[1].value + '\n*Valor da avalia\u00e7\u00e3o:* R$ ' + f[2].value + '\n\n*Aparelho novo:* ' + f[3].value + '\n*Valor:* R$ ' + f[4].value + '\n\n*Valor com a troca: R$ ' + resta.toLocaleString('pt-BR') + '*\nO restante pode ser parcelado no cart\u00e3o.\n\n\u00c9 uma excelente oportunidade para fazer o upgrade com economia. Posso reservar o aparelho para voc\u00ea?';

                    case 'seminovo':
                        return ola + ' Entendo perfeitamente a sua d\u00favida. Vou explicar como funciona.\n\n*' + f[1].value + '*\nCondi\u00e7\u00e3o: *' + f[2].value + '*\n\n*Nosso processo de verifica\u00e7\u00e3o:*\n\u2022 Teste completo de todas as fun\u00e7\u00f5es\n\u2022 Verifica\u00e7\u00e3o de IMEI (livre de bloqueios)\n\u2022 Confer\u00eancia da sa\u00fade da bateria\n\u2022 Inspe\u00e7\u00e3o visual detalhada\n\u2022 Higieniza\u00e7\u00e3o profissional\n\nTodos os nossos seminovos acompanham *garantia DG Store*, oferecendo a mesma seguran\u00e7a de um aparelho novo.\n\nSe desejar, posso enviar fotos e v\u00eddeos do aparelho para a senhora conferir.';

                    case 'compare':
                        return ola + ' Preparei um comparativo para facilitar a sua escolha:\n\n*Op\u00e7\u00e3o 1: ' + f[1].value + '*\nValor: R$ ' + f[2].value + '\n\n*Op\u00e7\u00e3o 2: ' + f[3].value + '*\nValor: R$ ' + f[4].value + '\n\nAmbos os modelos s\u00e3o excelentes. A diferen\u00e7a principal est\u00e1 no processador, c\u00e2mera e recursos exclusivos da gera\u00e7\u00e3o mais recente.\n\nPosso detalhar as diferen\u00e7as conforme o que for mais importante para o seu uso. \u00c9 s\u00f3 me dizer!';

                    case 'confirm':
                        return ola + ' Sua compra foi *confirmada com sucesso*.\n\n*' + f[1].value + '*\nValor: *R$ ' + f[2].value + '*\nPagamento: *' + f[3].value + '*\n\n*O que est\u00e1 incluso:*\n\u2022 Produto com proced\u00eancia verificada\n\u2022 Garantia DG Store\n\u2022 Suporte p\u00f3s-venda\n\nAvisarei assim que tudo estiver pronto.\n\nAgradecemos a confian\u00e7a na DG Store!';

                    case 'pickup':
                        return ola + '\n\nSeu *' + f[1].value + '* j\u00e1 est\u00e1 pronto para retirada.\n\n*Hor\u00e1rio de funcionamento:*\nSegunda a Sexta: 9h \u00e0s 18h\nS\u00e1bado: 9h \u00e0s 13h\n\n*Importante:* traga um documento com foto.\n\nSe precisar de aux\u00edlio para configurar o aparelho, faremos na hora, sem custo adicional.\n\nEstamos aguardando a sua visita!';

                    case 'reservation':
                        var dataLimite = f[3].value || 'consultar';
                        return ola + ' Passando para lembrar da sua reserva.\n\n*' + f[1].value + '*\nSinal: *R$ ' + f[2].value + '*\nV\u00e1lida at\u00e9: *' + dataLimite + '*\n\nO produto est\u00e1 separado e garantido. Para finalizar, basta entrar em contato.\n\nCaso precise de um prazo maior ou tenha alguma d\u00favida, fico \u00e0 disposi\u00e7\u00e3o.';

                    case 'payment':
                        return ola + ' Seguem os dados para pagamento:\n\n*Valor: R$ ' + f[1].value + '*\n\n*Pix:*\nChave: ' + f[2].value + '\nTitular: ' + f[3].value + '\n\nAp\u00f3s a transfer\u00eancia, por gentileza, envie o comprovante por aqui para que eu possa dar andamento ao pedido.\n\nQualquer d\u00favida, estou \u00e0 disposi\u00e7\u00e3o.';

                    case 'warranty':
                        var data = f[3].value || new Date().toLocaleDateString('pt-BR');
                        return ola + ' Seguem os detalhes da sua garantia:\n\n*' + f[1].value + '*\nData da compra: *' + data + '*\nPer\u00edodo de garantia: *' + f[2].value + '*\n\n*A garantia cobre:*\n\u2022 Defeitos de fabrica\u00e7\u00e3o\n\u2022 Problemas de hardware\n\u2022 Mau funcionamento espont\u00e2neo\n\n*A garantia n\u00e3o cobre:*\n\u2022 Danos por queda ou contato com l\u00edquido\n\u2022 Mau uso ou viola\u00e7\u00e3o do aparelho\n\nCaso note qualquer comportamento diferente no aparelho, entre em contato por aqui que resolveremos.\n\nConte sempre com a DG Store!';

                    case 'thanks':
                        return ola + '\n\nMuito obrigada por escolher a *DG Store*!\n\nEspero que esteja aproveitando o seu *' + f[1].value + '*.\n\nLembrando que voc\u00ea conta com:\n\u2022 Garantia ativa\n\u2022 Suporte direto por aqui\n\u2022 Condi\u00e7\u00f5es especiais para pr\u00f3ximas compras\n\nSe algum familiar ou amigo estiver procurando um iPhone, ficarei muito feliz em atend\u00ea-los com o mesmo cuidado.\n\nUm abra\u00e7o!';

                    case 'review':
                        return ola + ' Tudo bem?\n\nGostaria de pedir, se poss\u00edvel, que deixe uma avalia\u00e7\u00e3o da *DG Store* no Google.\n\nSua opini\u00e3o \u00e9 muito importante para que possamos continuar oferecendo o melhor atendimento.\n\nLeva menos de um minuto e nos ajuda imensamente.\n\nDesde j\u00e1, muito obrigada!';

                    case 'tips':
                        return ola + ' Espero que esteja gostando do seu *' + f[1].value + '*!\n\nSeparei algumas dicas para aproveitar ao m\u00e1ximo o seu aparelho:\n\n*Bateria:* evite usar carregadores gen\u00e9ricos. O ideal \u00e9 manter entre 20% e 80% para preservar a sa\u00fade da bateria.\n\n*Pel\u00edcula e capinha:* s\u00e3o essenciais para proteger o investimento. Se precisar, temos acess\u00f3rios na loja.\n\n*iCloud:* ative o backup autom\u00e1tico para n\u00e3o perder fotos e dados.\n\n*Atualiza\u00e7\u00f5es:* mantenha o iOS atualizado para garantir seguran\u00e7a e melhor desempenho.\n\nSe tiver qualquer d\u00favida sobre o uso, estou \u00e0 disposi\u00e7\u00e3o!';

                    case 'arrived':
                        return ola + ' Tenho uma \u00f3tima not\u00edcia.\n\nO *' + f[1].value + '* na cor *' + f[2].value + '* acabou de chegar em nossa loja.\n\nProduto com proced\u00eancia verificada e garantia DG Store.\n\nComo a senhora havia demonstrado interesse, quis avis\u00e1-la em primeira m\u00e3o. Esse modelo costuma ter sa\u00edda r\u00e1pida.\n\nGostaria que eu reserve para voc\u00ea?';

                    case 'followup':
                        return ola + ' Tudo bem?\n\nNotei que a senhora demonstrou interesse pelo *' + f[1].value + '* e gostaria de saber se posso ajudar com alguma d\u00favida.\n\nEstou \u00e0 disposi\u00e7\u00e3o para:\n\u2022 Detalhar as condi\u00e7\u00f5es de pagamento\n\u2022 Enviar fotos e v\u00eddeos do produto\n\u2022 Explicar sobre garantia e proced\u00eancia\n\nSem compromisso algum. Ficarei feliz em ajudar!';

                    case 'promo':
                        return ola + ' Gostaria de compartilhar uma condi\u00e7\u00e3o especial.\n\n*' + f[1].value + '*\n\nDe ~R$ ' + f[2].value + '~\n*Por R$ ' + f[3].value + '*\n\nProduto com proced\u00eancia verificada, nota fiscal e garantia DG Store.\n\n*Condi\u00e7\u00e3o por tempo limitado*, sujeito \u00e0 disponibilidade.\n\nSe tiver interesse, entre em contato para que eu possa reservar o seu!';

                    case 'birthday':
                        return ola + '\n\nA equipe da *DG Store* gostaria de desejar um *feliz anivers\u00e1rio*!\n\nEsperamos que esse dia seja especial para voc\u00ea.\n\nE, como presente, preparamos uma *condi\u00e7\u00e3o exclusiva* para aniversariantes em qualquer produto da loja.\n\nSe quiser saber mais, \u00e9 s\u00f3 nos chamar. Ser\u00e1 um prazer atend\u00ea-la!\n\nParab\u00e9ns!';

                    case 'upgrade':
                        return ola + ' Tudo bem?\n\nVoc\u00ea est\u00e1 com o *' + f[1].value + '* h\u00e1 algum tempo, correto? Gostaria de apresentar uma oportunidade de upgrade.\n\nO *' + f[2].value + '* traz melhorias significativas em c\u00e2mera, desempenho e autonomia de bateria.\n\nAl\u00e9m disso, aceitamos o seu aparelho atual como parte do pagamento, o que reduz bastante o valor final.\n\nSe quiser, posso fazer uma avalia\u00e7\u00e3o do seu aparelho e apresentar a melhor proposta. Sem compromisso!';

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
