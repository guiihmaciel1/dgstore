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
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="color: #bfdbfe; font-size: 12px;">Taxa Adicional (%)</label>
                        <span x-show="taxValue > 0" style="color: #fbbf24; font-size: 12px; font-weight: 600;">R$ <span x-text="formatNumber(taxValue)"></span></span>
                    </div>
                    <div style="position: relative;">
                        <input type="text" 
                               x-model="taxRate" 
                               @input="calculate()"
                               placeholder="3,5"
                               style="width: 100%; padding: 12px 36px 12px 12px; background-color: white; border: 2px solid #60a5fa; border-radius: 8px; font-size: 16px; color: #1f2937; outline: none;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px; font-weight: 500;">%</span>
                    </div>
                </div>

                <!-- Margem de Lucro / Preço Final -->
                <div style="flex: 1; max-width: 220px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <label style="color: #bfdbfe; font-size: 12px;" x-text="marginMode === 'percent' ? 'Margem de Lucro (%)' : 'Preço Final (R$)'"></label>
                            <button @click="toggleMarginMode()" 
                                    type="button"
                                    style="background: rgba(255,255,255,0.2); border: none; border-radius: 4px; padding: 2px 6px; cursor: pointer; display: flex; align-items: center; gap: 4px;"
                                    :title="marginMode === 'percent' ? 'Mudar para Preço Final' : 'Mudar para Margem %'">
                                <svg style="width: 14px; height: 14px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </button>
                        </div>
                        <span x-show="marginMode === 'percent' && marginValue > 0" style="color: #4ade80; font-size: 12px; font-weight: 600;">R$ <span x-text="formatNumber(marginValue)"></span></span>
                        <span x-show="marginMode === 'price' && profitMargin > 0" style="color: #4ade80; font-size: 12px; font-weight: 600;"><span x-text="formatNumber(parseNumber(profitMargin))"></span>%</span>
                    </div>
                    <!-- Input Margem % -->
                    <div x-show="marginMode === 'percent'" style="position: relative;">
                        <input type="text" 
                               x-model="profitMargin" 
                               @input="calculate()"
                               placeholder="12"
                               style="width: 100%; padding: 12px 36px 12px 12px; background-color: white; border: 2px solid #60a5fa; border-radius: 8px; font-size: 16px; color: #1f2937; outline: none;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px; font-weight: 500;">%</span>
                    </div>
                    <!-- Input Preço Final -->
                    <div x-show="marginMode === 'price'" style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px; font-weight: 500;">R$</span>
                        <input type="text" 
                               x-model="targetPrice" 
                               @input="calculateFromPrice()"
                               placeholder="10000"
                               style="width: 100%; padding: 12px 12px 12px 40px; background-color: white; border: 2px solid #4ade80; border-radius: 8px; font-size: 16px; color: #1f2937; outline: none;">
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
                    <span style="color: #4ade80;" x-text="formatNumber(parseNumber(profitMargin)) + '%'"></span>
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
            targetPrice: '',
            marginMode: 'percent', // 'percent' ou 'price'
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
                    // Preenche o preço alvo com o preço sugerido atual
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
                
                // Calcula a margem necessária para atingir o preço alvo
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
                
                // Atualiza o preço alvo se estiver no modo price
                if (this.marginMode === 'price' && this.suggestedPrice > 0) {
                    this.targetPrice = this.formatNumber(this.suggestedPrice);
                }
                
                this.saveToStorage();
            }
        }
    }
</script>
