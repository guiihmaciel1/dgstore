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
                        <button @click="tpl.open = !tpl.open" type="button"
                                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1.25rem; background: none; border: none; cursor: pointer; text-align: left;">
                            <div style="display: flex; align-items: center; gap: 0.625rem;">
                                <span style="font-size: 1.125rem;" x-text="tpl.icon"></span>
                                <span style="font-size: 0.9375rem; font-weight: 600; color: #111827;" x-text="tpl.title"></span>
                            </div>
                            <svg :style="tpl.open ? 'transform:rotate(180deg);' : ''" style="width: 16px; height: 16px; color: #9ca3af; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Conteudo -->
                        <div x-show="tpl.open" x-transition style="border-top: 1px solid #f3f4f6; padding: 1rem 1.25rem;">
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
                                 x-text="buildMessage(tpl)"></div>

                            <!-- Botoes -->
                            <div style="display: flex; gap: 0.5rem;">
                                <button @click="copyMessage(tpl)" type="button"
                                        :style="tpl.copied ? 'flex:1;padding:0.5rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;border:none;cursor:pointer;background:#059669;color:white;display:flex;align-items:center;justify-content:center;gap:6px;' : 'flex:1;padding:0.5rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;border:none;cursor:pointer;background:#111827;color:white;display:flex;align-items:center;justify-content:center;gap:6px;'">
                                    <span x-text="tpl.copied ? 'Copiado!' : 'Copiar'"></span>
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

    <script>
    function whatsappMessages() {
        return {
            phone: '',

            templates: [
                {
                    icon: '👋', title: 'Boas-vindas', open: false, copied: false,
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                    ],
                    build(f) {
                        const nome = f[0].value || 'cliente';
                        return `Ola ${nome}! 👋\n\nSeja bem-vindo(a) a *DG Store*!\nComo posso te ajudar hoje?\n\nTemos iPhones novos e seminovos, iPads, MacBooks, Apple Watch e acessorios.\n\nPode me dizer o que voce procura? 😊`;
                    }
                },
                {
                    icon: '💰', title: 'Orcamento', open: false, copied: false,
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Preco a vista', value: '9.999' },
                        { label: 'Parcelas (ate)', value: '12x' },
                        { label: 'Valor parcela', value: '999' },
                    ],
                    build(f) {
                        const nome = f[0].value || 'cliente';
                        return `Ola ${nome}! Segue o orcamento:\n\n📱 *${f[1].value}*\n\n💵 A vista: *R$ ${f[2].value}*\n💳 Parcelado: ate *${f[3].value} de R$ ${f[4].value}*\n\nAceito PIX, cartao de credito e debito.\nTambem fazemos *trade-in* do seu aparelho usado! 🔄\n\nAlguma duvida? Estou a disposicao!`;
                    }
                },
                {
                    icon: '✅', title: 'Confirmacao de venda', open: false, copied: false,
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Valor total', value: '9.999' },
                        { label: 'Forma pagamento', value: 'PIX' },
                    ],
                    build(f) {
                        const nome = f[0].value || 'cliente';
                        return `Ola ${nome}! ✅\n\nSua compra foi *confirmada*!\n\n📱 Produto: *${f[1].value}*\n💰 Valor: *R$ ${f[2].value}*\n💳 Pagamento: *${f[3].value}*\n\nSeu aparelho vem com garantia DG Store.\nQualquer duvida, estou a disposicao!\n\nObrigado pela preferencia! 🙏`;
                    }
                },
                {
                    icon: '📦', title: 'Produto pronto para retirada', open: false, copied: false,
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                    ],
                    build(f) {
                        const nome = f[0].value || 'cliente';
                        return `Ola ${nome}! 📦\n\nSeu *${f[1].value}* ja esta pronto para retirada!\n\nNosso horario de atendimento:\n🕐 Seg a Sex: 9h as 18h\n🕐 Sab: 9h as 13h\n\nTe aguardamos! 😊`;
                    }
                },
                {
                    icon: '🛡️', title: 'Informacoes de garantia', open: false, copied: false,
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Prazo garantia', value: '90 dias' },
                        { label: 'Data compra', value: '' },
                    ],
                    build(f) {
                        const nome = f[0].value || 'cliente';
                        const data = f[3].value || new Date().toLocaleDateString('pt-BR');
                        return `Ola ${nome}! 🛡️\n\nInformacoes da sua garantia DG Store:\n\n📱 Produto: *${f[1].value}*\n📅 Data da compra: *${data}*\n⏰ Prazo: *${f[2].value}*\n\n*O que cobre:*\n✅ Defeitos de fabricacao\n✅ Problemas de hardware\n\n*O que NAO cobre:*\n❌ Danos por queda ou liquido\n❌ Mau uso\n\nEm caso de problema, entre em contato comigo!`;
                    }
                },
                {
                    icon: '⏰', title: 'Lembrete de reserva', open: false, copied: false,
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Valor sinal', value: '500' },
                        { label: 'Data limite', value: '' },
                    ],
                    build(f) {
                        const nome = f[0].value || 'cliente';
                        return `Ola ${nome}! ⏰\n\nLembrando que voce tem uma *reserva* na DG Store:\n\n📱 Produto: *${f[1].value}*\n💰 Sinal pago: *R$ ${f[2].value}*\n📅 Valida ate: *${f[3].value || 'consultar'}*\n\nDeseja finalizar a compra?\nEstou a disposicao para ajudar! 😊`;
                    }
                },
                {
                    icon: '🔄', title: 'Proposta de trade-in', open: false, copied: false,
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Aparelho atual', value: 'iPhone 15 Pro' },
                        { label: 'Valor trade-in', value: '4.500' },
                        { label: 'Produto novo', value: 'iPhone 17 Pro 256GB' },
                        { label: 'Preco novo', value: '9.999' },
                    ],
                    build(f) {
                        const nome = f[0].value || 'cliente';
                        const tradeIn = parseFloat(f[2].value.replace(/\./g,'').replace(',','.')) || 0;
                        const novo = parseFloat(f[4].value.replace(/\./g,'').replace(',','.')) || 0;
                        const resta = Math.max(0, novo - tradeIn);
                        return `Ola ${nome}! 🔄\n\nFiz a avaliacao do seu aparelho:\n\n📱 Seu aparelho: *${f[1].value}*\n💰 Valor avaliado: *R$ ${f[2].value}*\n\n📱 Produto novo: *${f[3].value}*\n💰 Preco: *R$ ${f[4].value}*\n\n✨ *Restante a pagar: R$ ${resta.toLocaleString('pt-BR')}*\n\nO restante pode ser parcelado no cartao!\nTem interesse? 😊`;
                    }
                },
                {
                    icon: '🙏', title: 'Pos-venda / Agradecimento', open: false, copied: false,
                    fields: [
                        { label: 'Nome do cliente', value: '' },
                        { label: 'Produto', value: 'iPhone 17 Pro' },
                    ],
                    build(f) {
                        const nome = f[0].value || 'cliente';
                        return `Ola ${nome}! 🙏\n\nObrigado por comprar na *DG Store*!\n\nEspero que esteja curtindo seu *${f[1].value}*! 📱\n\nLembre-se:\n✅ Voce tem garantia conosco\n✅ Qualquer duvida, pode chamar\n✅ Indique para amigos e ganhe desconto na proxima compra!\n\nAvaliacoes no Google nos ajudam muito! ⭐\n\nAte a proxima! 😊`;
                    }
                },
            ],

            buildMessage(tpl) {
                return tpl.build(tpl.fields);
            },

            copyMessage(tpl) {
                navigator.clipboard.writeText(this.buildMessage(tpl)).then(() => {
                    tpl.copied = true;
                    setTimeout(() => tpl.copied = false, 2500);
                });
            },

            waLink(tpl) {
                const num = this.phone.replace(/\D/g, '');
                return 'https://wa.me/' + (num || '') + '?text=' + encodeURIComponent(this.buildMessage(tpl));
            },
        };
    }
    </script>
</x-app-layout>
