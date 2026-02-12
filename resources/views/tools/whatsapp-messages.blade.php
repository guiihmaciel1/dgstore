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

            <!-- Templates -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <template x-for="(tpl, idx) in templates" :key="idx">
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <!-- Header do template -->
                        <button @click="toggle(idx)" type="button"
                                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1.25rem; background: white; border: none; cursor: pointer; text-align: left;">
                            <div style="display: flex; align-items: center; gap: 0.625rem;">
                                <span style="font-size: 1.125rem;" x-text="tpl.icon"></span>
                                <span style="font-size: 0.9375rem; font-weight: 600; color: #111827;" x-text="tpl.title"></span>
                            </div>
                            <svg width="16" height="16" :style="openIdx === idx ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #9ca3af; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Conteudo -->
                        <div x-show="openIdx === idx" x-transition style="border-top: 1px solid #f3f4f6; padding: 1rem 1.25rem;">
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
                            <div style="background: #dcf8c6; border-radius: 0 12px 12px 12px; padding: 0.75rem 1rem; margin-bottom: 0.75rem; font-size: 0.85rem; color: #111827; white-space: pre-wrap; line-height: 1.5; max-height: 250px; overflow-y: auto;"
                                 x-text="getMessage(idx)"></div>

                            <!-- Botoes -->
                            <div style="display: flex; gap: 0.5rem;">
                                <button @click="copyMsg(idx)" type="button"
                                        :style="copiedIdx === idx ? 'flex:1;padding:0.5rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;border:none;cursor:pointer;background:#059669;color:white;display:flex;align-items:center;justify-content:center;gap:6px;' : 'flex:1;padding:0.5rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;border:none;cursor:pointer;background:#111827;color:white;display:flex;align-items:center;justify-content:center;gap:6px;'">
                                    <span x-text="copiedIdx === idx ? 'Copiado!' : 'Copiar'"></span>
                                </button>
                                <a :href="waLink(idx)" target="_blank"
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

    <script>
    function whatsappMessages() {
        return {
            phone: '',
            openIdx: null,
            copiedIdx: null,

            templates: [
                {
                    icon: '\uD83D\uDC4B', title: 'Boas-vindas', key: 'welcome',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                    ],
                },
                {
                    icon: '\uD83D\uDCB0', title: 'Or\u00e7amento', key: 'quote',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Pre\u00e7o \u00e0 vista', value: '9.999' },
                        { label: 'Parcelas (at\u00e9)', value: '12x' },
                        { label: 'Valor parcela', value: '999' },
                    ],
                },
                {
                    icon: '\u2705', title: 'Confirma\u00e7\u00e3o de venda', key: 'confirm',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Valor total', value: '9.999' },
                        { label: 'Forma pagamento', value: 'PIX' },
                    ],
                },
                {
                    icon: '\uD83D\uDCE6', title: 'Produto pronto para retirada', key: 'pickup',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                    ],
                },
                {
                    icon: '\uD83D\uDEE1\uFE0F', title: 'Informa\u00e7\u00f5es de garantia', key: 'warranty',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Prazo garantia', value: '90 dias' },
                        { label: 'Data compra', value: '' },
                    ],
                },
                {
                    icon: '\u23F0', title: 'Lembrete de reserva', key: 'reservation',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Valor sinal', value: '500' },
                        { label: 'Data limite', value: '' },
                    ],
                },
                {
                    icon: '\uD83D\uDD04', title: 'Proposta de trade-in', key: 'tradein',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Aparelho atual', value: 'iPhone 15 Pro' },
                        { label: 'Valor trade-in', value: '4.500' },
                        { label: 'Produto novo', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Pre\u00e7o novo', value: '9.999' },
                    ],
                },
                {
                    icon: '\uD83D\uDE4F', title: 'P\u00f3s-venda / Agradecimento', key: 'thanks',
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro' },
                    ],
                },
            ],

            toggle(idx) {
                this.openIdx = this.openIdx === idx ? null : idx;
            },

            getMessage(idx) {
                var t = this.templates[idx];
                var f = t.fields;
                var nome = f[0].value || 'cliente';

                switch (t.key) {
                    case 'welcome':
                        return 'Ol\u00e1 ' + nome + '! \uD83D\uDC4B\n\nSeja bem-vindo(a) \u00e0 *DG Store*!\nComo posso te ajudar hoje?\n\nTemos iPhones novos e seminovos, iPads, MacBooks, Apple Watch e acess\u00f3rios.\n\nPode me dizer o que voc\u00ea procura? \uD83D\uDE0A';

                    case 'quote':
                        return 'Ol\u00e1 ' + nome + '! Segue o or\u00e7amento:\n\n\uD83D\uDCF1 *' + f[1].value + '*\n\n\uD83D\uDCB5 \u00C0 vista: *R$ ' + f[2].value + '*\n\uD83D\uDCB3 Parcelado: at\u00e9 *' + f[3].value + ' de R$ ' + f[4].value + '*\n\nAceito PIX, cart\u00e3o de cr\u00e9dito e d\u00e9bito.\nTamb\u00e9m fazemos *trade-in* do seu aparelho usado! \uD83D\uDD04\n\nAlguma d\u00favida? Estou \u00e0 disposi\u00e7\u00e3o!';

                    case 'confirm':
                        return 'Ol\u00e1 ' + nome + '! \u2705\n\nSua compra foi *confirmada*!\n\n\uD83D\uDCF1 Produto: *' + f[1].value + '*\n\uD83D\uDCB0 Valor: *R$ ' + f[2].value + '*\n\uD83D\uDCB3 Pagamento: *' + f[3].value + '*\n\nSeu aparelho vem com garantia DG Store.\nQualquer d\u00favida, estou \u00e0 disposi\u00e7\u00e3o!\n\nObrigado pela prefer\u00eancia! \uD83D\uDE4F';

                    case 'pickup':
                        return 'Ol\u00e1 ' + nome + '! \uD83D\uDCE6\n\nSeu *' + f[1].value + '* j\u00e1 est\u00e1 pronto para retirada!\n\nNosso hor\u00e1rio de atendimento:\n\uD83D\uDD50 Seg a Sex: 9h \u00e0s 18h\n\uD83D\uDD50 S\u00e1b: 9h \u00e0s 13h\n\nTe aguardamos! \uD83D\uDE0A';

                    case 'warranty':
                        var data = f[3].value || new Date().toLocaleDateString('pt-BR');
                        return 'Ol\u00e1 ' + nome + '! \uD83D\uDEE1\uFE0F\n\nInforma\u00e7\u00f5es da sua garantia DG Store:\n\n\uD83D\uDCF1 Produto: *' + f[1].value + '*\n\uD83D\uDCC5 Data da compra: *' + data + '*\n\u23F0 Prazo: *' + f[2].value + '*\n\n*O que cobre:*\n\u2705 Defeitos de fabrica\u00e7\u00e3o\n\u2705 Problemas de hardware\n\n*O que N\u00C3O cobre:*\n\u274C Danos por queda ou l\u00edquido\n\u274C Mau uso\n\nEm caso de problema, entre em contato comigo!';

                    case 'reservation':
                        return 'Ol\u00e1 ' + nome + '! \u23F0\n\nLembrando que voc\u00ea tem uma *reserva* na DG Store:\n\n\uD83D\uDCF1 Produto: *' + f[1].value + '*\n\uD83D\uDCB0 Sinal pago: *R$ ' + f[2].value + '*\n\uD83D\uDCC5 V\u00e1lida at\u00e9: *' + (f[3].value || 'consultar') + '*\n\nDeseja finalizar a compra?\nEstou \u00e0 disposi\u00e7\u00e3o para ajudar! \uD83D\uDE0A';

                    case 'tradein':
                        var tradeVal = parseFloat(String(f[2].value).replace(/\./g,'').replace(',','.')) || 0;
                        var novoVal = parseFloat(String(f[4].value).replace(/\./g,'').replace(',','.')) || 0;
                        var resta = Math.max(0, novoVal - tradeVal);
                        return 'Ol\u00e1 ' + nome + '! \uD83D\uDD04\n\nFiz a avalia\u00e7\u00e3o do seu aparelho:\n\n\uD83D\uDCF1 Seu aparelho: *' + f[1].value + '*\n\uD83D\uDCB0 Valor avaliado: *R$ ' + f[2].value + '*\n\n\uD83D\uDCF1 Produto novo: *' + f[3].value + '*\n\uD83D\uDCB0 Pre\u00e7o: *R$ ' + f[4].value + '*\n\n\u2728 *Restante a pagar: R$ ' + resta.toLocaleString('pt-BR') + '*\n\nO restante pode ser parcelado no cart\u00e3o!\nTem interesse? \uD83D\uDE0A';

                    case 'thanks':
                        return 'Ol\u00e1 ' + nome + '! \uD83D\uDE4F\n\nObrigado por comprar na *DG Store*!\n\nEspero que esteja curtindo seu *' + f[1].value + '*! \uD83D\uDCF1\n\nLembre-se:\n\u2705 Voc\u00ea tem garantia conosco\n\u2705 Qualquer d\u00favida, pode chamar\n\u2705 Indique para amigos e ganhe desconto na pr\u00f3xima compra!\n\nAvalia\u00e7\u00f5es no Google nos ajudam muito! \u2B50\n\nAt\u00e9 a pr\u00f3xima! \uD83D\uDE0A';

                    default:
                        return '';
                }
            },

            copyMsg(idx) {
                var self = this;
                var text = this.getMessage(idx);
                navigator.clipboard.writeText(text).then(function() {
                    self.copiedIdx = idx;
                    setTimeout(function() { self.copiedIdx = null; }, 2500);
                });
            },

            waLink(idx) {
                var num = this.phone.replace(/\D/g, '');
                return 'https://wa.me/' + (num || '') + '?text=' + encodeURIComponent(this.getMessage(idx));
            },
        };
    }
    </script>
</x-app-layout>
