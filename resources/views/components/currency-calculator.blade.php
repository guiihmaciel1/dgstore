<div x-data="currencyCalculator()" x-init="init()">
    <!-- Barra Header -->
    <div style="background: linear-gradient(to right, #1e3a8a, #2563eb); cursor: pointer;" @click="open = !open">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                <div style="display: flex; align-items: center; gap: 24px;">
                    <span style="display: flex; align-items: center; gap: 8px; color: white; font-weight: 600; font-size: 14px;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        CALCULADORA DE IMPORTAÇÃO
                    </span>
                    <span style="color: #bfdbfe; font-size: 14px;">
                        Cotação: US$ 1,00 = R$ <span x-text="formatNumber(exchangeRate)"></span>
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px; color: white; font-size: 14px;">
                    <span style="text-align: right; line-height: 1.3;">Clique<br>para<br><span x-text="open ? 'fechar' : 'abrir'"></span></span>
                    <svg style="width: 28px; height: 28px; transition: transform 0.2s;" :style="open ? '' : 'transform: rotate(180deg)'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculadora Expandida -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-cloak
         style="background: linear-gradient(to right, #1e3a8a, #2563eb);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-bottom: 16px;">
            <!-- Linha dos Inputs e Resultados -->
            <div style="display: flex; align-items: flex-end; gap: 16px;">
                <!-- Valor em Dólar -->
                <div style="flex: 1; max-width: 200px;">
                    <label style="display: block; color: #bfdbfe; font-size: 12px; margin-bottom: 6px;">Valor em Dólar (US$)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px; font-weight: 500;">$</span>
                        <input type="text" 
                               x-model="dollarValue" 
                               @input="calculate()"
                               placeholder="1400"
                               style="width: 100%; padding: 12px 12px 12px 32px; background-color: white; border: 2px solid #60a5fa; border-radius: 8px; font-size: 16px; color: #1f2937; outline: none;">
                    </div>
                </div>

                <!-- Cotação -->
                <div style="flex: 1; max-width: 160px;">
                    <label style="display: block; color: #bfdbfe; font-size: 12px; margin-bottom: 6px;">Cotação (R$)</label>
                    <input type="text" 
                           x-model="exchangeRateInput" 
                           @input="updateExchangeRate()"
                           placeholder="5,45"
                           style="width: 100%; padding: 12px; background-color: white; border: 2px solid #60a5fa; border-radius: 8px; font-size: 16px; color: #1f2937; outline: none;">
                </div>

                <!-- Taxa Adicional -->
                <div style="flex: 1; max-width: 180px;">
                    <label style="display: block; color: #bfdbfe; font-size: 12px; margin-bottom: 6px;">Taxa Adicional (%)</label>
                    <div style="position: relative;">
                        <input type="text" 
                               x-model="taxRate" 
                               @input="calculate()"
                               placeholder="3,5"
                               style="width: 100%; padding: 12px 36px 12px 12px; background-color: white; border: 2px solid #60a5fa; border-radius: 8px; font-size: 16px; color: #1f2937; outline: none;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px; font-weight: 500;">%</span>
                    </div>
                </div>

                <!-- Margem de Lucro -->
                <div style="flex: 1; max-width: 200px;">
                    <label style="display: block; color: #bfdbfe; font-size: 12px; margin-bottom: 6px;">Margem de Lucro (%)</label>
                    <div style="position: relative;">
                        <input type="text" 
                               x-model="profitMargin" 
                               @input="calculate()"
                               placeholder="12"
                               style="width: 100%; padding: 12px 36px 12px 12px; background-color: white; border: 2px solid #60a5fa; border-radius: 8px; font-size: 16px; color: #1f2937; outline: none;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px; font-weight: 500;">%</span>
                    </div>
                </div>

                <!-- Resultados -->
                <div style="flex: 0 0 auto; margin-left: auto;">
                    <div style="display: flex; align-items: center; gap: 32px; background-color: rgba(30, 58, 138, 0.7); border-radius: 8px; padding: 10px 24px;">
                        <div style="text-align: center;">
                            <div style="color: #93c5fd; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px;">Valor em R$</div>
                            <div style="color: white; font-size: 18px; font-weight: 700;">R$ <span x-text="formatNumber(valueInReais)"></span></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="color: #93c5fd; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px;">Com Taxa</div>
                            <div style="color: #fb923c; font-size: 18px; font-weight: 700;">R$ <span x-text="formatNumber(valueWithTax)"></span></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="color: #93c5fd; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px;">Preço Sugerido</div>
                            <div style="color: #4ade80; font-size: 20px; font-weight: 700;">R$ <span x-text="formatNumber(suggestedPrice)"></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de Resumo -->
            <div x-show="valueInReais > 0" 
                 x-transition
                 style="margin-top: 16px; background-color: #3b82f6; border-radius: 8px; padding: 12px 20px;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 16px; color: white; font-size: 15px; flex-wrap: wrap;">
                    <span>US$ <span style="font-weight: 600;" x-text="dollarValue || '0'"></span></span>
                    <span style="color: #bfdbfe;">×</span>
                    <span>R$ <span style="font-weight: 600;" x-text="exchangeRateInput || '0'"></span></span>
                    <span style="color: #bfdbfe;">=</span>
                    <span style="font-weight: 600;">R$ <span x-text="formatNumber(valueInReais)"></span></span>
                    <span style="color: #bfdbfe;">+</span>
                    <span style="color: #fbbf24;" x-text="(taxRate || '0') + '%'"></span>
                    <span style="color: #bfdbfe;">=</span>
                    <span style="color: #fb923c; font-weight: 600;">R$ <span x-text="formatNumber(valueWithTax)"></span></span>
                    <span style="color: #bfdbfe;">+</span>
                    <span style="color: #4ade80;" x-text="(profitMargin || '0') + '%'"></span>
                    <span style="color: #bfdbfe;">=</span>
                    <span style="color: #4ade80; font-weight: 700; font-size: 16px;">R$ <span x-text="formatNumber(suggestedPrice)"></span></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function currencyCalculator() {
        return {
            open: false,
            dollarValue: '',
            exchangeRateInput: '5,45',
            exchangeRate: 5.45,
            taxRate: '3,5',
            profitMargin: '12',
            valueInReais: 0,
            valueWithTax: 0,
            suggestedPrice: 0,

            init() {
                this.loadFromStorage();
                this.$nextTick(() => {
                    this.calculate();
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

            calculate() {
                const dollar = this.parseNumber(this.dollarValue);
                const rate = this.parseNumber(this.exchangeRateInput);
                const tax = this.parseNumber(this.taxRate);
                const margin = this.parseNumber(this.profitMargin);

                this.valueInReais = dollar * rate;
                this.valueWithTax = this.valueInReais * (1 + (tax / 100));
                this.suggestedPrice = this.valueWithTax * (1 + (margin / 100));
                this.exchangeRate = rate;
                this.saveToStorage();
            }
        }
    }
</script>
