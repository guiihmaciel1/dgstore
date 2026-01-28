<div x-data="currencyCalculator()">
    <!-- Botão Toggle -->
    <div style="background: #1e40af; padding: 0.5rem 0; cursor: pointer;" @click="open = !open">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <span style="display: flex; align-items: center; gap: 0.5rem; color: white; font-weight: 600; font-size: 0.875rem;">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        CALCULADORA DE IMPORTAÇÃO
                    </span>
                    <span style="color: #93c5fd; font-size: 0.75rem;">
                        Cotação: US$ 1,00 = R$ <span x-text="exchangeRate.toFixed(2).replace('.', ',')"></span>
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: white; font-size: 0.875rem;">
                    <span x-show="!open">Clique para abrir</span>
                    <span x-show="open">Clique para fechar</span>
                    <svg :style="open ? 'transform: rotate(180deg)' : ''" style="width: 1.25rem; height: 1.25rem; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculadora Expandida -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="background: #1e3a8a;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 1rem; align-items: end;">
                
                <!-- Valor em Dólar -->
                <div>
                    <label style="display: block; color: #93c5fd; font-size: 0.75rem; font-weight: 500; margin-bottom: 0.25rem;">
                        Valor em Dólar (US$)
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-weight: 600;">$</span>
                        <input type="text" 
                               x-model="dollarValue" 
                               @input="calculate()"
                               placeholder="1.400,00"
                               style="width: 100%; padding: 0.625rem 0.75rem 0.625rem 1.75rem; border: 2px solid #3b82f6; border-radius: 0.5rem; font-size: 1rem; font-weight: 600; background: white;">
                    </div>
                </div>

                <!-- Cotação do Dólar -->
                <div>
                    <label style="display: block; color: #93c5fd; font-size: 0.75rem; font-weight: 500; margin-bottom: 0.25rem;">
                        Cotação (R$)
                    </label>
                    <input type="text" 
                           x-model="exchangeRateInput" 
                           @input="updateExchangeRate()"
                           placeholder="5,45"
                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #3b82f6; border-radius: 0.5rem; font-size: 1rem; font-weight: 600; background: white;">
                </div>

                <!-- Taxa Adicional -->
                <div>
                    <label style="display: block; color: #93c5fd; font-size: 0.75rem; font-weight: 500; margin-bottom: 0.25rem;">
                        Taxa Adicional (%)
                    </label>
                    <div style="position: relative;">
                        <input type="text" 
                               x-model="taxRate" 
                               @input="updateTaxRate()"
                               placeholder="3,5"
                               style="width: 100%; padding: 0.625rem 0.75rem; padding-right: 2rem; border: 2px solid #3b82f6; border-radius: 0.5rem; font-size: 1rem; font-weight: 600; background: white;">
                        <span style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-weight: 600;">%</span>
                    </div>
                </div>

                <!-- Margem de Lucro -->
                <div>
                    <label style="display: block; color: #93c5fd; font-size: 0.75rem; font-weight: 500; margin-bottom: 0.25rem;">
                        Margem de Lucro (%)
                    </label>
                    <div style="position: relative;">
                        <input type="text" 
                               x-model="profitMargin" 
                               @input="updateProfitMargin()"
                               placeholder="12"
                               style="width: 100%; padding: 0.625rem 0.75rem; padding-right: 2rem; border: 2px solid #3b82f6; border-radius: 0.5rem; font-size: 1rem; font-weight: 600; background: white;">
                        <span style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-weight: 600;">%</span>
                    </div>
                </div>

                <!-- Resultados -->
                <div style="grid-column: span 2;">
                    <div style="background: #1e40af; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; text-align: center;">
                            <div>
                                <div style="color: #93c5fd; font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.05em;">Valor em R$</div>
                                <div style="color: white; font-size: 1rem; font-weight: 700;" x-text="'R$ ' + formatNumber(valueInReais)"></div>
                            </div>
                            <div>
                                <div style="color: #93c5fd; font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.05em;">Com Taxa</div>
                                <div style="color: #fbbf24; font-size: 1rem; font-weight: 700;" x-text="'R$ ' + formatNumber(valueWithTax)"></div>
                            </div>
                            <div>
                                <div style="color: #93c5fd; font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.05em;">Preço Sugerido</div>
                                <div style="color: #4ade80; font-size: 1.125rem; font-weight: 700;" x-text="'R$ ' + formatNumber(suggestedPrice)"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo detalhado -->
            <div x-show="dollarValue && parseFloat(dollarValue.replace(',', '.')) > 0" style="margin-top: 1rem; padding: 0.75rem 1rem; background: rgba(255,255,255,0.1); border-radius: 0.5rem;">
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; align-items: center; color: white; font-size: 0.8rem;">
                    <span style="display: flex; align-items: center; gap: 0.25rem;">
                        <span style="color: #93c5fd;">US$</span>
                        <span style="font-weight: 600;" x-text="dollarValue || '0'"></span>
                    </span>
                    <span style="color: #60a5fa;">×</span>
                    <span style="display: flex; align-items: center; gap: 0.25rem;">
                        <span style="color: #93c5fd;">R$</span>
                        <span style="font-weight: 600;" x-text="exchangeRateInput || '0'"></span>
                    </span>
                    <span style="color: #60a5fa;">=</span>
                    <span style="font-weight: 600;">R$ <span x-text="formatNumber(valueInReais)"></span></span>
                    <span style="color: #60a5fa;">+</span>
                    <span style="display: flex; align-items: center; gap: 0.25rem;">
                        <span style="font-weight: 600; color: #fbbf24;" x-text="(taxRate || '0') + '%'"></span>
                    </span>
                    <span style="color: #60a5fa;">=</span>
                    <span style="font-weight: 600; color: #fbbf24;">R$ <span x-text="formatNumber(valueWithTax)"></span></span>
                    <span style="color: #60a5fa;">+</span>
                    <span style="display: flex; align-items: center; gap: 0.25rem;">
                        <span style="font-weight: 600; color: #4ade80;" x-text="(profitMargin || '0') + '%'"></span>
                    </span>
                    <span style="color: #60a5fa;">=</span>
                    <span style="font-weight: 700; color: #4ade80; font-size: 1rem;">R$ <span x-text="formatNumber(suggestedPrice)"></span></span>
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
                // Carrega configurações salvas do localStorage
                this.loadFromStorage();
                this.calculate();
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
                        if (data.taxRate) this.taxRate = data.taxRate;
                        if (data.profitMargin) this.profitMargin = data.profitMargin;
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

            updateTaxRate() {
                this.saveToStorage();
                this.calculate();
            },

            updateProfitMargin() {
                this.saveToStorage();
                this.calculate();
            },

            parseNumber(value) {
                if (!value) return 0;
                // Remove pontos de milhar e converte vírgula para ponto
                return parseFloat(value.toString().replace(/\./g, '').replace(',', '.')) || 0;
            },

            formatNumber(value) {
                if (!value || isNaN(value)) return '0,00';
                return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            calculate() {
                const dollar = this.parseNumber(this.dollarValue);
                const rate = this.parseNumber(this.exchangeRateInput);
                const tax = this.parseNumber(this.taxRate);
                const margin = this.parseNumber(this.profitMargin);

                // Valor em reais
                this.valueInReais = dollar * rate;

                // Valor com taxa
                this.valueWithTax = this.valueInReais * (1 + (tax / 100));

                // Preço sugerido (com margem de lucro)
                this.suggestedPrice = this.valueWithTax * (1 + (margin / 100));
            }
        }
    }
</script>
