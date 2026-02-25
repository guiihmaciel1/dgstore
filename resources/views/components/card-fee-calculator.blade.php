<div x-data="cardFeeCalculator()" x-init="init()">
    <!-- Botão Flutuante -->
    <button @click="open = true"
            type="button"
            style="position: fixed; bottom: 24px; right: 24px; z-index: 40; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #111827, #374151); color: white; border: none; cursor: pointer; box-shadow: 0 8px 24px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)'"
            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.3)'"
            title="Calculadora de taxas Stone">
        <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
    </button>

    <!-- Painel lateral direito -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-4"
         @keydown.escape.window="open = false"
         x-cloak
         style="position: fixed; bottom: 96px; right: 24px; z-index: 50; width: 440px; max-height: 85vh; overflow-y: auto; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25);">

            <!-- Header -->
            <div style="padding: 16px 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <button @click="open = false" type="button" style="background: none; border: none; cursor: pointer; padding: 4px; color: #6b7280;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <h2 style="font-size: 17px; font-weight: 700; color: #111827;">Calculadora Stone</h2>
                    <div style="width: 24px;"></div>
                </div>
            </div>

            <!-- Conteúdo -->
            <div style="padding: 0 24px 24px;">
                
                <!-- Tipo de Compra -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Tipo de Compra</label>
                    <div style="display: flex; gap: 8px;">
                        <button @click="deliveryType = 'pronta'" type="button"
                                :style="deliveryType === 'pronta'
                                    ? 'flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600; border: 2px solid #111827; background: #111827; color: white; cursor: pointer;'
                                    : 'flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 500; border: 2px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer;'">
                            Pronta Entrega
                        </button>
                        <button @click="deliveryType = 'programada'" type="button"
                                :style="deliveryType === 'programada'
                                    ? 'flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600; border: 2px solid #111827; background: #111827; color: white; cursor: pointer;'
                                    : 'flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 500; border: 2px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer;'">
                            Compra Programada
                        </button>
                    </div>
                </div>

                <!-- Descrição do Aparelho (Opcional) -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Descrição do Aparelho (opcional)</label>
                    <input type="text"
                           x-model="deviceDescription"
                           placeholder="Ex: iPhone 15 Pro Max 256GB Blue"
                           style="width: 100%; padding: 12px 16px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 14px; color: #111827; outline: none;"
                           onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                </div>

                <!-- Valor que desejo receber -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Valor que desejo receber</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px; font-weight: 500;">R$</span>
                        <input type="text"
                               x-model="amountInput"
                               @input.debounce.500ms="calculate()"
                               placeholder="0,00"
                               x-ref="amountField"
                               style="width: 100%; padding: 14px 16px 14px 42px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 20px; font-weight: 600; color: #111827; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                    </div>
                </div>

                <!-- Entrada Pix (Opcional) -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Entrada (Pix) - Opcional</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px; font-weight: 500;">R$</span>
                        <input type="text"
                               x-model="downPaymentInput"
                               @input.debounce.500ms="calculate()"
                               placeholder="0,00"
                               style="width: 100%; padding: 12px 16px 12px 42px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; font-size: 16px; font-weight: 600; color: #059669; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#059669'; this.style.background='white'"
                               onblur="this.style.borderColor='#bbf7d0'; this.style.background='#f0fdf4'">
                    </div>
                    <div x-show="downPayment > 0" style="font-size: 11px; color: #059669; margin-top: 6px; font-weight: 600;">
                        Restante a parcelar: R$ <span x-text="formatNumber(remaining)"></span>
                    </div>
                </div>

                <!-- Trade-in (Opcional) -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Trade-in (Opcional)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px; font-weight: 500;">R$</span>
                        <input type="text"
                               x-model="tradeInInput"
                               @input.debounce.500ms="calculate()"
                               placeholder="0,00"
                               style="width: 100%; padding: 12px 16px 12px 42px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; font-size: 16px; font-weight: 600; color: #d97706; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#d97706'; this.style.background='white'"
                               onblur="this.style.borderColor='#fde68a'; this.style.background='#fef3c7'">
                    </div>
                    <div x-show="tradeInValue > 0" style="font-size: 11px; color: #d97706; margin-top: 6px; font-weight: 600;">
                        Restante após trade-in: R$ <span x-text="formatNumber(finalAmount)"></span>
                    </div>
                </div>

                <!-- Loading -->
                <div x-show="loading" style="text-align: center; padding: 20px;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top-color: #111827; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                </div>

                <!-- Erro -->
                <div x-show="error && !loading" x-transition style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 18px; margin-bottom: 16px;">
                    <div style="display: flex; align-items: start; gap: 10px;">
                        <svg style="width: 20px; height: 20px; color: #dc2626; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: #dc2626; margin-bottom: 4px;">Erro</div>
                            <div style="font-size: 12px; color: #991b1b;" x-text="error"></div>
                        </div>
                    </div>
                </div>

                <!-- Resultados -->
                <div x-show="!loading && results.length > 0" x-transition>
                    <!-- Pix (melhor preço) -->
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #059669;">Pix</div>
                            <div style="font-size: 11px; color: #6b7280;">Melhor preço - sem taxa</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 20px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(finalAmount)"></div>
                        </div>
                    </div>

                    <!-- Cartão -->
                    <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                        <div style="display: grid; grid-template-columns: 1fr auto 56px; background: #111827; color: white; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                            <div style="padding: 10px 14px;">Forma</div>
                            <div style="padding: 10px 14px; text-align: right;">Cliente Paga</div>
                            <div style="padding: 10px 10px; text-align: center;"></div>
                        </div>
                        <template x-for="(row, idx) in results" :key="idx">
                            <div :style="'display: grid; grid-template-columns: 1fr auto 56px; align-items: center; border-top: 1px solid #f3f4f6;' + (idx % 2 === 0 ? ' background: white;' : ' background: #f9fafb;')">
                                <div style="padding: 10px 14px;">
                                    <div style="font-size: 13px; font-weight: 600; color: #111827;" x-text="row.label"></div>
                                    <div x-show="row.installments > 1" style="font-size: 11px; color: #6b7280;" x-text="row.installments + 'x de R$ ' + formatNumber(row.installment_value)"></div>
                                    <div style="font-size: 11px; color: #9ca3af;" x-text="'Taxa ' + row.mdr_rate.toString().replace('.', ',') + '%'"></div>
                                </div>
                                <div style="padding: 10px 14px; text-align: right;">
                                    <div style="font-size: 15px; font-weight: 700; color: #111827;" x-text="'R$ ' + formatNumber(row.gross_amount)"></div>
                                    <div style="font-size: 10px; color: #ef4444;" x-text="'taxa R$ ' + formatNumber(row.fee_amount)"></div>
                                </div>
                                <div style="padding: 6px 10px; display: flex; align-items: center; justify-content: center;">
                                    <button @click.stop="copyRow(row)"
                                            type="button"
                                            :style="row.copied
                                                ? 'background: #059669; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; color: white; display: flex; align-items: center; justify-content: center;'
                                                : 'background: #e5e7eb; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; color: #374151; display: flex; align-items: center; justify-content: center;'"
                                            :title="row.copied ? 'Copiado!' : 'Copiar mensagem'">
                                        <svg x-show="!row.copied" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <svg x-show="row.copied" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Você recebe -->
                    <div style="margin-top: 16px;">
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 13px; font-weight: 600; color: #059669;">VOCÊ RECEBE</span>
                                <span style="font-size: 20px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(finalAmount)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Copiar todas + WhatsApp -->
                    <div style="display: flex; gap: 10px; margin-top: 12px;">
                        <button @click="copyAll()"
                                type="button"
                                :style="copiedAll
                                    ? 'flex: 1; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; background: #059669; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'
                                    : 'flex: 1; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; background: #111827; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'">
                            <svg x-show="!copiedAll" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="copiedAll" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="copiedAll ? 'Copiado!' : 'Copiar todas'"></span>
                        </button>
                        <a :href="'https://wa.me/?text=' + encodeURIComponent(buildAllMessage())"
                           target="_blank"
                           style="padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; background: #16a34a; color: white; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;"
                           onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
    </div>
</div>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<script>
function cardFeeCalculator() {
    return {
        open: false,
        loading: false,
        copiedAll: false,
        error: '',
        deliveryType: 'pronta',
        deviceDescription: '',
        
        amountInput: '',
        amount: 0,
        downPaymentInput: '',
        downPayment: 0,
        tradeInInput: '',
        tradeInValue: 0,
        remaining: 0,
        finalAmount: 0,
        results: [],

        init() {
            this.$watch('open', (val) => {
                if (val) {
                    this.$nextTick(() => {
                        if (this.$refs.amountField) {
                            this.$refs.amountField.focus();
                        }
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
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });
        },

        async calculate() {
            this.amount = this.parseNumber(this.amountInput);
            this.downPayment = this.parseNumber(this.downPaymentInput);
            this.tradeInValue = this.parseNumber(this.tradeInInput);

            // Calcula o valor final considerando entrada e trade-in
            let netAmount = this.amount;
            
            if (this.tradeInValue > 0) {
                netAmount = Math.max(0, netAmount - this.tradeInValue);
            }
            
            if (this.downPayment > 0) {
                netAmount = Math.max(0, netAmount - this.downPayment);
            }

            this.remaining = netAmount;
            this.finalAmount = netAmount;

            if (netAmount <= 0) {
                this.results = [];
                return;
            }

            this.loading = true;
            this.error = '';

            try {
                const response = await fetch('/api/card-fees/calculate-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ net_amount: netAmount })
                });

                const data = await response.json();

                if (data.success) {
                    this.results = data.data.map(r => ({
                        ...r,
                        label: r.payment_type === 'debit' ? 'Débito' : (r.installments === 1 ? 'Crédito 1x' : `Crédito ${r.installments}x`),
                        copied: false
                    }));
                    this.error = '';
                } else {
                    console.error('Erro ao calcular taxas:', data.message);
                    this.error = data.message || 'Erro ao calcular taxas';
                    this.results = [];
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                this.error = 'Erro ao conectar com o servidor. Verifique sua conexão.';
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        buildRowMessage(row) {
            const vlr = row.installment_value;
            const deliveryLabel = this.deliveryType === 'programada' ? 'COMPRA PROGRAMADA' : 'PRONTA ENTREGA';
            const header = this.deviceDescription 
                ? `*Condições de pagamento - DG Store (${deliveryLabel})* 💳 - ${this.deviceDescription}`
                : `*Condições de pagamento - DG Store (${deliveryLabel})* 💳`;
            
            const linhas = [header, ''];

            if (this.downPayment > 0 || this.tradeInValue > 0) {
                if (this.tradeInValue > 0) {
                    linhas.push('📱 *Aparelho novo:* R$ ' + this.formatNumber(this.amount));
                    linhas.push('⬇️ *Trade-in:* - R$ ' + this.formatNumber(this.tradeInValue));
                    linhas.push('');
                }
                if (this.downPayment > 0) {
                    linhas.push('💵 *Entrada (Pix):*');
                    linhas.push('*R$ ' + this.formatNumber(this.downPayment) + '*');
                    linhas.push('');
                }
                linhas.push('💳 *Restante no cartão:*');
                linhas.push(`*${row.installments}x de R$ ${this.formatNumber(vlr)}*`);
            } else {
                linhas.push('💳 *No cartão:*');
                linhas.push(`*${row.installments}x de R$ ${this.formatNumber(vlr)}*`);
                linhas.push('');
                linhas.push('✅ *À vista (Pix):*');
                linhas.push(`*R$ ${this.formatNumber(this.finalAmount)}* _(melhor preço)_`);
            }
            
            linhas.push('');
            linhas.push('🔒 *Garantia e procedência verificada*');
            linhas.push('🏢 _Atendimento DG Store_');
            
            return linhas.join('\n');
        },

        buildAllMessage() {
            const deliveryLabel = this.deliveryType === 'programada' ? 'COMPRA PROGRAMADA' : 'PRONTA ENTREGA';
            const header = this.deviceDescription 
                ? `*Condições de pagamento - DG Store (${deliveryLabel})* 💳 - ${this.deviceDescription}`
                : `*Condições de pagamento - DG Store (${deliveryLabel})* 💳`;
            
            const linhas = [header, ''];

            if (this.tradeInValue > 0) {
                linhas.push('📱 *Aparelho novo:* R$ ' + this.formatNumber(this.amount));
                linhas.push('⬇️ *Trade-in:* - R$ ' + this.formatNumber(this.tradeInValue));
                linhas.push('');
            }

            if (this.downPayment > 0) {
                linhas.push('💵 *Entrada (Pix):*');
                linhas.push('*R$ ' + this.formatNumber(this.downPayment) + '*');
                linhas.push('');
                linhas.push('💳 *Restante no cartão:*');
            } else {
                linhas.push('✅ *À vista (Pix):*');
                linhas.push(`*R$ ${this.formatNumber(this.finalAmount)}* _(melhor preço)_`);
                linhas.push('');
                linhas.push('💳 *No cartão:*');
            }

            this.results.forEach(r => {
                const vlr = r.installment_value;
                linhas.push(`${r.installments}x de R$ ${this.formatNumber(vlr)}`);
            });
            
            linhas.push('');
            linhas.push('🔒 *Garantia e procedência verificada*');
            linhas.push('🏢 _Atendimento DG Store_');
            
            return linhas.join('\n');
        },

        async copyRow(row) {
            const message = this.buildRowMessage(row);
            try {
                await navigator.clipboard.writeText(message);
                row.copied = true;
                setTimeout(() => { row.copied = false; }, 2000);
            } catch (err) {
                console.error('Erro ao copiar:', err);
            }
        },

        async copyAll() {
            const message = this.buildAllMessage();
            try {
                await navigator.clipboard.writeText(message);
                this.copiedAll = true;
                setTimeout(() => { this.copiedAll = false; }, 2000);
            } catch (err) {
                console.error('Erro ao copiar:', err);
            }
        }
    };
}
</script>
