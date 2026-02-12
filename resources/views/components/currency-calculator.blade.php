<div x-data="currencyCalculator()" x-init="init()">
    <!-- Botão Flutuante (lado esquerdo) -->
    <button @click="open = true"
            type="button"
            style="position: fixed; bottom: 24px; left: 24px; z-index: 40; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #2563eb); color: white; border: none; cursor: pointer; box-shadow: 0 8px 24px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)'"
            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.3)'"
            title="Calculadora de importação">
        <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </button>

    <!-- Painel lateral esquerdo -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-x-4"
         @keydown.escape.window="open = false"
         x-cloak
         style="position: fixed; bottom: 96px; left: 24px; z-index: 50; width: 400px; max-height: 80vh; overflow-y: auto; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25);">

            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0;">
                <button @click="open = false" type="button" style="background: none; border: none; cursor: pointer; padding: 4px; color: #6b7280;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <h2 style="font-size: 17px; font-weight: 700; color: #111827;">Calculadora de importação</h2>
                <div style="width: 24px;"></div>
            </div>

            <!-- Cotação atual -->
            <div style="padding: 8px 24px 0;">
                <div style="font-size: 12px; color: #6b7280; text-align: center;">
                    Cotação: US$ 1,00 = R$ <span x-text="formatNumber(exchangeRate)"></span>
                </div>
            </div>

            <!-- Body -->
            <div style="padding: 16px 24px 24px;">
                <!-- Valor em Dólar -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Valor em Dólar (US$)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px; font-weight: 500;">$</span>
                        <input type="text"
                               x-model="dollarValue"
                               @input="calculate()"
                               placeholder="0,00"
                               x-ref="dollarField"
                               style="width: 100%; padding: 14px 16px 14px 36px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 20px; font-weight: 600; color: #111827; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#2563eb'; this.style.background='white'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                    </div>
                </div>

                <!-- Cotação e Taxa lado a lado -->
                <div style="display: flex; gap: 10px; margin-bottom: 16px;">
                    <!-- Cotação -->
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Cotação (R$)</label>
                        <input type="text"
                               x-model="exchangeRateInput"
                               @input="updateExchangeRate()"
                               placeholder="5,45"
                               style="width: 100%; padding: 12px 14px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 15px; color: #111827; outline: none; text-align: center;"
                               onfocus="this.style.borderColor='#2563eb'; this.style.background='white'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                    </div>
                    <!-- Taxa Adicional -->
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label style="font-size: 13px; color: #6b7280;">Taxa (%)</label>
                            <span x-show="taxValue > 0" style="font-size: 11px; color: #f59e0b; font-weight: 600;">R$ <span x-text="formatNumber(taxValue)"></span></span>
                        </div>
                        <div style="position: relative;">
                            <input type="text"
                                   x-model="taxRate"
                                   @input="calculate()"
                                   placeholder="3,5"
                                   style="width: 100%; padding: 12px 32px 12px 14px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 15px; color: #111827; outline: none; text-align: center;"
                                   onfocus="this.style.borderColor='#2563eb'; this.style.background='white'"
                                   onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                            <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px;">%</span>
                        </div>
                    </div>
                </div>

                <!-- Margem de Lucro -->
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <label style="font-size: 13px; color: #6b7280;" x-text="marginMode === 'percent' ? 'Margem de lucro (%)' : 'Preço final (R$)'"></label>
                            <button @click="toggleMarginMode()"
                                    type="button"
                                    style="background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; padding: 3px 8px; cursor: pointer; font-size: 11px; color: #6b7280; display: flex; align-items: center; gap: 4px;"
                                    :title="marginMode === 'percent' ? 'Mudar para Preço Final' : 'Mudar para Margem %'">
                                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <span x-text="marginMode === 'percent' ? 'R$' : '%'"></span>
                            </button>
                        </div>
                        <span x-show="marginMode === 'percent' && marginValue > 0" style="font-size: 11px; color: #059669; font-weight: 600;">+ R$ <span x-text="formatNumber(marginValue)"></span></span>
                        <span x-show="marginMode === 'price' && profitMargin > 0" style="font-size: 11px; color: #059669; font-weight: 600;"><span x-text="formatNumber(parseNumber(profitMargin))"></span>%</span>
                    </div>
                    <!-- Input Margem % -->
                    <div x-show="marginMode === 'percent'" style="position: relative;">
                        <input type="text"
                               x-model="profitMargin"
                               @input="calculate()"
                               placeholder="12"
                               style="width: 100%; padding: 12px 32px 12px 14px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 15px; color: #111827; outline: none; text-align: center;"
                               onfocus="this.style.borderColor='#2563eb'; this.style.background='white'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px;">%</span>
                    </div>
                    <!-- Input Preço Final -->
                    <div x-show="marginMode === 'price'" style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px;">R$</span>
                        <input type="text"
                               x-model="targetPrice"
                               @input="calculateFromPrice()"
                               placeholder="10000"
                               style="width: 100%; padding: 12px 14px 12px 38px; background: #f9fafb; border: 1px solid #059669; border-radius: 12px; font-size: 15px; color: #111827; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#059669'; this.style.background='white'"
                               onblur="this.style.borderColor='#059669'; this.style.background='#f9fafb'">
                    </div>
                </div>

                <!-- Resultados -->
                <div x-show="valueInReais > 0" x-transition>
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; margin-bottom: 12px;">
                        <!-- Linha de cálculo -->
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; color: #6b7280; flex-wrap: wrap; margin-bottom: 16px;">
                            <span>US$ <strong x-text="dollarValue || '0'"></strong></span>
                            <span>×</span>
                            <span>R$ <strong x-text="exchangeRateInput || '0'"></strong></span>
                            <span>=</span>
                            <span style="font-weight: 600; color: #111827;">R$ <span x-text="formatNumber(valueInReais)"></span></span>
                        </div>

                        <!-- Cards de resultado -->
                        <div style="display: flex; gap: 8px;">
                            <!-- Valor em R$ -->
                            <div style="flex: 1; text-align: center; padding: 12px 8px; background: white; border-radius: 12px; border: 1px solid #e5e7eb;">
                                <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Valor em R$</div>
                                <div style="font-size: 16px; font-weight: 700; color: #111827;">R$ <span x-text="formatNumber(valueInReais)"></span></div>
                            </div>
                            <!-- Com Taxa -->
                            <div style="flex: 1; text-align: center; padding: 12px 8px; background: white; border-radius: 12px; border: 1px solid #e5e7eb;">
                                <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Com Taxa</div>
                                <div style="font-size: 16px; font-weight: 700; color: #f59e0b;">R$ <span x-text="formatNumber(valueWithTax)"></span></div>
                            </div>
                        </div>

                        <!-- Preço Sugerido (destaque) -->
                        <div style="margin-top: 12px; background: #111827; border-radius: 12px; padding: 16px; text-align: center;">
                            <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Preço Sugerido</div>
                            <div style="font-size: 26px; font-weight: 800; color: #4ade80;">R$ <span x-text="formatNumber(suggestedPrice)"></span></div>
                        </div>
                    </div>

                    <!-- Resumo detalhado -->
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 14px 16px;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 12px; color: #1e40af; flex-wrap: wrap; text-align: center;">
                            <span>R$ <strong x-text="formatNumber(valueInReais)"></strong></span>
                            <span>+</span>
                            <span style="color: #f59e0b;"><strong x-text="(taxRate || '0') + '%'"></strong></span>
                            <span>=</span>
                            <span style="color: #ea580c;"><strong>R$ <span x-text="formatNumber(valueWithTax)"></span></strong></span>
                            <span>+</span>
                            <span style="color: #059669;"><strong x-text="formatNumber(parseNumber(profitMargin)) + '%'"></strong></span>
                            <span>=</span>
                            <span style="color: #059669; font-weight: 800;">R$ <span x-text="formatNumber(suggestedPrice)"></span></span>
                        </div>
                    </div>

                    <!-- Botão copiar resumo para funcionário -->
                    <button @click="copySummary()" type="button"
                            :style="copied ? 'width:100%;margin-top:10px;padding:10px;border-radius:10px;border:none;cursor:pointer;font-size:13px;font-weight:600;background:#059669;color:white;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s;' : 'width:100%;margin-top:10px;padding:10px;border-radius:10px;border:1px solid #e5e7eb;cursor:pointer;font-size:13px;font-weight:600;background:white;color:#374151;display:flex;align-items:center;justify-content:center;gap:6px;transition:all 0.2s;'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span x-text="copied ? 'Copiado!' : 'Copiar resumo (funcion\u00e1rio)'"></span>
                    </button>
                </div>

                <!-- Estado vazio -->
                <div x-show="valueInReais <= 0" style="text-align: center; padding: 32px 16px; color: #9ca3af;">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p style="font-size: 14px;">Informe o valor em dólar para calcular</p>
                </div>
            </div>
    </div>
</div>

<script>
function currencyCalculator() {
    return {
        open: false,
        copied: false,
        dollarValue: '',
        exchangeRateInput: '5,45',
        exchangeRate: 5.45,
        taxRate: '3,5',
        profitMargin: '12',
        targetPrice: '',
        marginMode: 'percent',
        valueInReais: 0,
        valueWithTax: 0,
        suggestedPrice: 0,
        taxValue: 0,
        marginValue: 0,

        init() {
            this.loadFromStorage();
            this.$nextTick(() => {
                this.calculate();
            });
            this.$watch('open', (val) => {
                if (val) {
                    this.$nextTick(() => {
                        if (this.$refs.dollarField) this.$refs.dollarField.focus();
                    });
                }
            });
        },

        loadFromStorage() {
            const saved = localStorage.getItem('dgstore_calculator');
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    if (data.exchangeRateInput) {
                        this.exchangeRateInput = data.exchangeRateInput;
                        this.exchangeRate = this.parseNumber(data.exchangeRateInput);
                    }
                    if (data.taxRate !== undefined && data.taxRate !== '') {
                        this.taxRate = data.taxRate;
                    }
                    if (data.profitMargin !== undefined && data.profitMargin !== '') {
                        this.profitMargin = data.profitMargin;
                    }
                } catch (e) {
                    console.warn('Erro ao carregar configurações da calculadora:', e);
                }
            }
        },

        saveToStorage() {
            const data = {
                exchangeRateInput: this.exchangeRateInput,
                taxRate: this.taxRate,
                profitMargin: this.profitMargin
            };
            localStorage.setItem('dgstore_calculator', JSON.stringify(data));
        },

        updateExchangeRate() {
            this.exchangeRate = this.parseNumber(this.exchangeRateInput);
            this.saveToStorage();
            this.calculate();
        },

        parseNumber(value) {
            if (value === null || value === undefined || value === '') return 0;
            let strValue = String(value).trim();
            if (strValue === '') return 0;
            if (strValue.includes(',')) {
                strValue = strValue.replace(/\./g, '').replace(',', '.');
            }
            const parsed = parseFloat(strValue);
            return isNaN(parsed) ? 0 : parsed;
        },

        formatNumber(value) {
            if (value === null || value === undefined || isNaN(value)) return '0,00';
            return Number(value).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        toggleMarginMode() {
            if (this.marginMode === 'percent') {
                this.marginMode = 'price';
                if (this.suggestedPrice > 0) {
                    this.targetPrice = this.formatNumber(this.suggestedPrice);
                }
            } else {
                this.marginMode = 'percent';
            }
        },

        calculateFromPrice() {
            const dollar = this.parseNumber(this.dollarValue);
            const rate = this.parseNumber(this.exchangeRateInput);
            const tax = this.parseNumber(this.taxRate);
            const target = this.parseNumber(this.targetPrice);

            this.valueInReais = dollar * rate;
            this.taxValue = this.valueInReais * (tax / 100);
            this.valueWithTax = this.valueInReais + this.taxValue;

            if (this.valueWithTax > 0 && target > 0) {
                const marginPercent = ((target - this.valueWithTax) / this.valueWithTax) * 100;
                this.profitMargin = marginPercent.toFixed(2).replace('.', ',');
                this.marginValue = target - this.valueWithTax;
                this.suggestedPrice = target;
            } else {
                this.profitMargin = '0';
                this.marginValue = 0;
                this.suggestedPrice = this.valueWithTax;
            }

            this.exchangeRate = rate;
            this.saveToStorage();
        },

        calculate() {
            const dollar = this.parseNumber(this.dollarValue);
            const rate = this.parseNumber(this.exchangeRateInput);
            const tax = this.parseNumber(this.taxRate);
            const margin = this.parseNumber(this.profitMargin);

            this.valueInReais = dollar * rate;
            this.taxValue = this.valueInReais * (tax / 100);
            this.valueWithTax = this.valueInReais + this.taxValue;
            this.marginValue = this.valueWithTax * (margin / 100);
            this.suggestedPrice = this.valueWithTax + this.marginValue;
            this.exchangeRate = rate;

            if (this.marginMode === 'price' && this.suggestedPrice > 0) {
                this.targetPrice = this.formatNumber(this.suggestedPrice);
            }

            this.saveToStorage();
        },

        copySummary() {
            var self = this;
            var lines = [];
            lines.push('*Resumo Importa\u00e7\u00e3o*');
            lines.push('');
            lines.push('\uD83D\uDCB5 Valor: US$ ' + (this.dollarValue || '0'));
            lines.push('\uD83D\uDCB1 Cota\u00e7\u00e3o: R$ ' + this.formatNumber(this.exchangeRate));
            lines.push('\uD83D\uDCB0 Valor em R$: R$ ' + this.formatNumber(this.valueInReais));
            lines.push('');
            lines.push('\uD83D\uDCC8 Taxa: ' + (this.taxRate || '0') + '%');
            lines.push('\uD83D\uDCB2 Valor da taxa: R$ ' + this.formatNumber(this.taxValue));

            var text = lines.join('\n');
            navigator.clipboard.writeText(text).then(function() {
                self.copied = true;
                setTimeout(function() { self.copied = false; }, 2500);
            });
        }
    }
}
</script>
