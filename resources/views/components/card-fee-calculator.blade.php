<div x-data="cardFeeCalculator()" x-init="init()">
    <!-- Botão Flutuante -->
    <button @click="open = true"
            type="button"
            style="position: fixed; bottom: 24px; right: 24px; z-index: 40; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #111827, #374151); color: white; border: none; cursor: pointer; box-shadow: 0 8px 24px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)'"
            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.3)'"
            title="Calculadora de taxas">
        <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
    </button>

    <!-- Overlay -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         x-cloak
         style="position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,0.5);"></div>

    <!-- Modal -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8"
         @keydown.escape.window="open = false"
         x-cloak
         style="position: fixed; inset: 0; z-index: 51; display: flex; align-items: center; justify-content: center; padding: 16px; pointer-events: none;">

        <div @click.stop style="pointer-events: auto; background: white; border-radius: 16px; width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">

            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0;">
                <button @click="open = false" type="button" style="background: none; border: none; cursor: pointer; padding: 4px; color: #6b7280;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <h2 style="font-size: 17px; font-weight: 700; color: #111827;">Calculadora de taxas</h2>
                <div style="width: 24px;"></div>
            </div>

            <!-- Body -->
            <div style="padding: 20px 24px 24px;">
                <!-- Valor que desejo receber -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Valor que desejo receber</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px; font-weight: 500;">R$</span>
                        <input type="text"
                               x-model="amountInput"
                               @input="calculate()"
                               placeholder="0,00"
                               x-ref="amountField"
                               style="width: 100%; padding: 14px 16px 14px 42px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 20px; font-weight: 600; color: #111827; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                    </div>
                </div>

                <!-- Checkbox taxa de saque -->
                <div style="margin-bottom: 20px;">
                    <label @click="addSaque = !addSaque; calculate()" style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 14px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; user-select: none;"
                           :style="addSaque ? 'background: #fffbeb; border-color: #fcd34d;' : ''">
                        <div :style="addSaque
                            ? 'width: 20px; height: 20px; border-radius: 6px; border: 2px solid #f59e0b; background: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;'
                            : 'width: 20px; height: 20px; border-radius: 6px; border: 2px solid #d1d5db; background: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;'">
                            <svg x-show="addSaque" style="width: 12px; height: 12px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <span style="font-size: 13px; font-weight: 600; color: #111827;">Incluir taxa de saque (+1%)</span>
                            <span style="font-size: 11px; color: #9ca3af; display: block;">Acrescenta 1% em cada taxa para cobrir o saque</span>
                        </div>
                    </label>
                </div>

                <!-- Tabela de todas as opções -->
                <div x-show="amount > 0" x-transition>
                    <!-- Pix e Débito -->
                    <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                        <!-- Pix -->
                        <div style="flex: 1; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-size: 13px; font-weight: 700; color: #059669;">Pix</div>
                                <div style="font-size: 10px; color: #6b7280;">Sem taxa</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 17px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(amount)"></div>
                            </div>
                        </div>
                        <!-- Débito -->
                        <div style="flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-size: 13px; font-weight: 700; color: #111827;">Débito</div>
                                <div style="font-size: 10px; color: #9ca3af;" x-text="'Taxa ' + (addSaque ? '1,8' : '0,8') + '%'"></div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 17px; font-weight: 800; color: #111827;" x-text="'R$ ' + formatNumber(debitoCobrar)"></div>
                                <div style="font-size: 10px; color: #ef4444;" x-text="'taxa R$ ' + formatNumber(debitoTaxa)"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Crédito -->
                    <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                        <!-- Cabeçalho -->
                        <div style="display: grid; grid-template-columns: 1fr auto 56px; background: #111827; color: white; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                            <div style="padding: 10px 14px;">Forma</div>
                            <div style="padding: 10px 14px; text-align: right;">Cobrar</div>
                            <div style="padding: 10px 10px; text-align: center;"></div>
                        </div>
                        <!-- Linhas -->
                        <template x-for="(row, idx) in results" :key="row.key">
                            <div :style="'display: grid; grid-template-columns: 1fr auto 56px; align-items: center; border-top: 1px solid #f3f4f6;' + (idx % 2 === 0 ? ' background: white;' : ' background: #f9fafb;')">
                                <div style="padding: 10px 14px;">
                                    <div style="font-size: 13px; font-weight: 600; color: #111827;" x-text="row.label"></div>
                                    <div style="font-size: 11px; color: #9ca3af;" x-text="'Taxa ' + (row.percentFinal ?? row.percent).toString().replace('.', ',') + '%'"></div>
                                    <div x-show="row.parcelas > 1" style="font-size: 11px; color: #6b7280;" x-text="row.parcelas + 'x de R$ ' + formatNumber(row.cobrar / row.parcelas)"></div>
                                </div>
                                <div style="padding: 10px 14px; text-align: right;">
                                    <div style="font-size: 15px; font-weight: 700; color: #111827;" x-text="'R$ ' + formatNumber(row.cobrar)"></div>
                                    <div style="font-size: 10px; color: #ef4444;" x-text="'taxa R$ ' + formatNumber(row.taxa)"></div>
                                </div>
                                <div style="padding: 6px 10px; display: flex; align-items: center; justify-content: center;">
                                    <button @click.stop="copyRow(row)"
                                            type="button"
                                            :style="row.copied
                                                ? 'background: #059669; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; color: white; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;'
                                                : 'background: #e5e7eb; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; color: #374151; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;'"
                                            :title="row.copied ? 'Copiado!' : 'Copiar mensagem'">
                                        <svg x-show="!row.copied" style="width: 16px; height: 16px; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <svg x-show="row.copied" style="width: 16px; height: 16px; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Você recebe (resumo) -->
                    <div style="margin-top: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 13px; font-weight: 600; color: #059669;">VOCÊ RECEBE</span>
                        <span style="font-size: 20px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(amount)"></span>
                    </div>

                    <!-- Copiar todas -->
                    <button @click="copyAll()"
                            type="button"
                            :style="copiedAll
                                ? 'width: 100%; margin-top: 12px; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #059669; color: white; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;'
                                : 'width: 100%; margin-top: 12px; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #111827; color: white; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;'">
                        <svg x-show="!copiedAll" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <svg x-show="copiedAll" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="copiedAll ? 'Copiado!' : 'Copiar todas as opções'"></span>
                    </button>
                </div>

                <!-- Estado vazio -->
                <div x-show="amount <= 0" style="text-align: center; padding: 32px 16px; color: #9ca3af;">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p style="font-size: 14px;">Digite o valor para simular todas as opções</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cardFeeCalculator() {
    return {
        open: false,
        copiedAll: false,

        rates: [
            { label: 'Crédito 1x', key: '1x', percent: 2.85, parcelas: 1 },
            { label: 'Crédito 2x', key: '2x', percent: 3.9, parcelas: 2 },
            { label: 'Crédito 3x', key: '3x', percent: 4.9, parcelas: 3 },
            { label: 'Crédito 4x', key: '4x', percent: 5.9, parcelas: 4 },
            { label: 'Crédito 5x', key: '5x', percent: 6.9, parcelas: 5 },
            { label: 'Crédito 6x', key: '6x', percent: 7.9, parcelas: 6 },
            { label: 'Crédito 7x', key: '7x', percent: 8.9, parcelas: 7 },
            { label: 'Crédito 8x', key: '8x', percent: 9.9, parcelas: 8 },
            { label: 'Crédito 9x', key: '9x', percent: 9.9, parcelas: 9 },
            { label: 'Crédito 10x', key: '10x', percent: 9.9, parcelas: 10 },
            { label: 'Crédito 11x', key: '11x', percent: 9.9, parcelas: 11 },
            { label: 'Crédito 12x', key: '12x', percent: 9.9, parcelas: 12 },
        ],

        addSaque: false,
        debitoBasePercent: 0.8,

        amountInput: '',
        amount: 0,
        debitoCobrar: 0,
        debitoTaxa: 0,
        results: [],

        init() {
            this.results = this.rates.map(r => ({
                ...r, cobrar: 0, taxa: 0, copied: false
            }));
            this.$watch('open', (val) => {
                if (val) {
                    this.$nextTick(() => {
                        if (this.$refs.amountField) this.$refs.amountField.focus();
                    });
                }
            });
        },

        parseNumber(value) {
            if (value === null || value === undefined || value === '') return 0;
            let str = String(value).trim().replace(/\s/g, '');
            if (!str) return 0;
            str = str.replace(/\./g, '').replace(',', '.');
            const n = parseFloat(str);
            return isNaN(n) ? 0 : n;
        },

        formatNumber(value) {
            if (value === null || value === undefined || isNaN(value)) return '0,00';
            return Number(value).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        calculate() {
            this.amount = this.parseNumber(this.amountInput);
            const extra = this.addSaque ? 1 : 0;

            // Débito
            const debitoPct = (this.debitoBasePercent + extra) / 100;
            this.debitoCobrar = this.amount > 0 ? this.amount / (1 - debitoPct) : 0;
            this.debitoTaxa = this.debitoCobrar - this.amount;

            // Crédito
            this.results = this.rates.map(r => {
                const pctFinal = r.percent + extra;
                const pct = pctFinal / 100;
                const cobrar = this.amount > 0 ? this.amount / (1 - pct) : 0;
                const taxa = cobrar - this.amount;
                return { ...r, percentFinal: pctFinal, cobrar, taxa, copied: false };
            });
        },

        buildRowMessage(row) {
            const linhas = [
                '💳 *Condições de pagamento – DG Store*',
                '',
                'Forma: *' + row.label + '*',
                'Valor: *R$ ' + this.formatNumber(row.cobrar) + '*',
            ];
            if (row.parcelas > 1) {
                const vlr = row.cobrar / row.parcelas;
                linhas.push('Parcelas: *' + row.parcelas + 'x de R$ ' + this.formatNumber(vlr) + '*');
            }
            linhas.push('', '📦 Produto com garantia e procedência');
            linhas.push('💳 Parcelamento facilitado');
            linhas.push('🤝 Atendimento DG Store');
            return linhas.join("\n");
        },

        buildAllMessage() {
            const credito1x = this.results.find(r => r.parcelas === 1);

            const linhas = [
                '💳 *Condições de pagamento – DG Store*',
                '',
                '*À vista:*',
                '✅ Pix: *R$ ' + this.formatNumber(this.amount) + '* _(melhor preço)_',
                '▪️ Débito: R$ ' + this.formatNumber(this.debitoCobrar),
                '▪️ Crédito: R$ ' + this.formatNumber(credito1x ? credito1x.cobrar : 0),
                '',
                '*Parcelado no cartão:*',
            ];

            this.results.filter(r => r.parcelas >= 2).forEach(r => {
                const vlrParcela = r.cobrar / r.parcelas;
                linhas.push('▪️ ' + r.parcelas + 'x de R$ ' + this.formatNumber(vlrParcela));
            });

            linhas.push('');
            linhas.push('📦 Garantia e procedência');
            linhas.push('🤝 Atendimento DG Store');

            return linhas.join("\n");
        },

        copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }
            // Fallback para HTTP
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            return Promise.resolve();
        },

        copyRow(row) {
            const msg = this.buildRowMessage(row);
            this.copyToClipboard(msg).then(() => {
                row.copied = true;
                setTimeout(() => { row.copied = false; }, 2000);
            });
        },

        copyAll() {
            const msg = this.buildAllMessage();
            this.copyToClipboard(msg).then(() => {
                this.copiedAll = true;
                setTimeout(() => { this.copiedAll = false; }, 2500);
            });
        }
    };
}
</script>
