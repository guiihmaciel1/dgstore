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
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-blue-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 lg:gap-4 items-end">
                
                <!-- Valor em Dólar -->
                <div>
                    <label class="block text-blue-300 text-xs font-medium mb-1">
                        Valor (US$)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">$</span>
                        <input type="text" 
                               x-model="dollarValue" 
                               @input="calculate()"
                               placeholder="1.400,00"
                               class="w-full pl-7 pr-3 py-2.5 border-2 border-blue-500 rounded-lg text-sm sm:text-base font-semibold bg-white focus:outline-none focus:border-blue-400">
                    </div>
                </div>

                <!-- Cotação do Dólar -->
                <div>
                    <label class="block text-blue-300 text-xs font-medium mb-1">
                        Cotação (R$)
                    </label>
                    <input type="text" 
                           x-model="exchangeRateInput" 
                           @input="updateExchangeRate()"
                           placeholder="5,45"
                           class="w-full px-3 py-2.5 border-2 border-blue-500 rounded-lg text-sm sm:text-base font-semibold bg-white focus:outline-none focus:border-blue-400">
                </div>

                <!-- Taxa Adicional -->
                <div>
                    <label class="block text-blue-300 text-xs font-medium mb-1">
                        Taxa (%)
                    </label>
                    <div class="relative">
                        <input type="text" 
                               x-model="taxRate" 
                               @input="updateTaxRate()"
                               placeholder="3,5"
                               class="w-full px-3 pr-8 py-2.5 border-2 border-blue-500 rounded-lg text-sm sm:text-base font-semibold bg-white focus:outline-none focus:border-blue-400">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">%</span>
                    </div>
                </div>

                <!-- Margem de Lucro -->
                <div>
                    <label class="block text-blue-300 text-xs font-medium mb-1">
                        Margem (%)
                    </label>
                    <div class="relative">
                        <input type="text" 
                               x-model="profitMargin" 
                               @input="updateProfitMargin()"
                               placeholder="12"
                               class="w-full px-3 pr-8 py-2.5 border-2 border-blue-500 rounded-lg text-sm sm:text-base font-semibold bg-white focus:outline-none focus:border-blue-400">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">%</span>
                    </div>
                </div>

                <!-- Resultados -->
                <div class="col-span-2">
                    <div class="bg-blue-800 rounded-lg p-3">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="text-blue-300 text-[10px] uppercase tracking-wide">Valor R$</div>
                                <div class="text-white text-sm sm:text-base font-bold" x-text="'R$ ' + formatNumber(valueInReais)"></div>
                            </div>
                            <div>
                                <div class="text-blue-300 text-[10px] uppercase tracking-wide">Com Taxa</div>
                                <div class="text-amber-400 text-sm sm:text-base font-bold" x-text="'R$ ' + formatNumber(valueWithTax)"></div>
                            </div>
                            <div>
                                <div class="text-blue-300 text-[10px] uppercase tracking-wide">Sugerido</div>
                                <div class="text-green-400 text-base sm:text-lg font-bold" x-text="'R$ ' + formatNumber(suggestedPrice)"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo detalhado -->
            <div x-show="dollarValue && parseFloat(dollarValue.replace(',', '.')) > 0" class="mt-3 p-3 bg-white/10 rounded-lg">
                <div class="flex flex-wrap gap-2 sm:gap-4 justify-center items-center text-white text-xs sm:text-sm">
                    <span class="flex items-center gap-1">
                        <span class="text-blue-300">US$</span>
                        <span class="font-semibold" x-text="dollarValue || '0'"></span>
                    </span>
                    <span class="text-blue-400">×</span>
                    <span class="flex items-center gap-1">
                        <span class="text-blue-300">R$</span>
                        <span class="font-semibold" x-text="exchangeRateInput || '0'"></span>
                    </span>
                    <span class="text-blue-400">=</span>
                    <span class="font-semibold">R$ <span x-text="formatNumber(valueInReais)"></span></span>
                    <span class="text-blue-400">+</span>
                    <span class="font-semibold text-amber-400" x-text="(taxRate || '0') + '%'"></span>
                    <span class="text-blue-400">=</span>
                    <span class="font-semibold text-amber-400">R$ <span x-text="formatNumber(valueWithTax)"></span></span>
                    <span class="text-blue-400">+</span>
                    <span class="font-semibold text-green-400" x-text="(profitMargin || '0') + '%'"></span>
                    <span class="text-blue-400">=</span>
                    <span class="font-bold text-green-400 text-sm sm:text-base">R$ <span x-text="formatNumber(suggestedPrice)"></span></span>
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
