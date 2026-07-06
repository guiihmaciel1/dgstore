@php
    $dollarRate = \App\Domain\System\Models\SystemSetting::get('dollar_rate');
    $isAdmin = auth()->user()->isAdminGeral();
@endphp

@if($isAdmin && $dollarRate)
<div x-data="importCalculator({{ (float) $dollarRate }})" x-init="init()">
    {{-- FAB --}}
    <button @click="open = true"
            type="button"
            style="position: fixed; bottom: 24px; left: 24px; z-index: 40; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #2563eb); color: white; border: none; cursor: pointer; box-shadow: 0 8px 24px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)'"
            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.3)'"
            title="Calculadora de importação">
        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </button>

    {{-- Painel lateral --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-x-4"
         @keydown.escape.window="open = false"
         x-cloak
         style="position: fixed; bottom: 92px; left: 24px; z-index: 50; width: 380px; max-height: 85vh; overflow-y: auto; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25);">

        {{-- Header --}}
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 20px 0;">
            <button @click="open = false" type="button" style="background: none; border: none; cursor: pointer; padding: 4px; color: #6b7280;">
                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h2 style="font-size: 16px; font-weight: 700; color: #111827;">Calculadora de Importação</h2>
            <div style="width: 22px;"></div>
        </div>

        {{-- Cotação atual --}}
        <div style="padding: 6px 20px 0; text-align: center;">
            <span style="font-size: 12px; color: #6b7280;">Cotação do dia: US$ 1,00 = </span>
            <span style="font-size: 13px; font-weight: 700; color: #1e40af;">R$ <span x-text="formatNumber(dollarRate)"></span></span>
        </div>

        {{-- Body --}}
        <div style="padding: 14px 20px 20px;">
            {{-- Valor em dólar --}}
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 5px;">Valor do produto (US$)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px; font-weight: 500;">$</span>
                    <input type="text"
                           x-model="dollarValue"
                           @input="calculate()"
                           placeholder="0,00"
                           x-ref="dollarField"
                           style="width: 100%; padding: 12px 14px 12px 34px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 20px; font-weight: 600; color: #111827; outline: none; text-align: right;"
                           onfocus="this.style.borderColor='#2563eb'; this.style.background='white'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                </div>
            </div>

            {{-- Transporte % --}}
            <div style="margin-bottom: 14px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                    <label style="font-size: 13px; color: #6b7280;">Transporte (%)</label>
                    <span x-show="transportValue > 0" style="font-size: 11px; color: #f59e0b; font-weight: 600;">+ R$ <span x-text="formatNumber(transportValue)"></span></span>
                </div>
                <div style="position: relative;">
                    <input type="text"
                           x-model="transportRate"
                           @input="calculate()"
                           placeholder="0"
                           style="width: 100%; padding: 10px 36px 10px 14px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 15px; color: #111827; outline: none; text-align: center;"
                           onfocus="this.style.borderColor='#2563eb'; this.style.background='white'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                    <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px;">%</span>
                </div>
            </div>

            {{-- Resultados --}}
            <div x-show="valueInReais > 0" x-transition>
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px; margin-bottom: 10px;">
                    {{-- Linha de cálculo --}}
                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 12px; color: #6b7280; flex-wrap: wrap; margin-bottom: 14px;">
                        <span>US$ <strong x-text="dollarValue || '0'"></strong></span>
                        <span>&times;</span>
                        <span>R$ <strong x-text="formatNumber(dollarRate)"></strong></span>
                        <span>=</span>
                        <span style="font-weight: 600; color: #111827;">R$ <span x-text="formatNumber(valueInReais)"></span></span>
                    </div>

                    {{-- Cards de resultado --}}
                    <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                        <div style="flex: 1; text-align: center; padding: 10px 6px; background: white; border-radius: 10px; border: 1px solid #e5e7eb;">
                            <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px;">Convertido</div>
                            <div style="font-size: 15px; font-weight: 700; color: #111827;">R$ <span x-text="formatNumber(valueInReais)"></span></div>
                        </div>
                        <div style="flex: 1; text-align: center; padding: 10px 6px; background: white; border-radius: 10px; border: 1px solid #e5e7eb;">
                            <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px;">Transporte</div>
                            <div style="font-size: 15px; font-weight: 700; color: #f59e0b;">R$ <span x-text="formatNumber(transportValue)"></span></div>
                        </div>
                    </div>

                    {{-- Custo final --}}
                    <div style="background: #111827; border-radius: 10px; padding: 14px; text-align: center;">
                        <div style="font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px;">Custo Total</div>
                        <div style="font-size: 24px; font-weight: 800; color: #4ade80;">R$ <span x-text="formatNumber(totalCost)"></span></div>
                    </div>
                </div>

                {{-- Resumo detalhado --}}
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px 14px; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 12px; color: #1e40af; flex-wrap: wrap; text-align: center;">
                        <span>R$ <strong x-text="formatNumber(valueInReais)"></strong></span>
                        <span>+</span>
                        <span style="color: #f59e0b;"><strong x-text="(transportRate || '0') + '% transporte'"></strong></span>
                        <span>=</span>
                        <span style="color: #059669; font-weight: 800;">R$ <span x-text="formatNumber(totalCost)"></span></span>
                    </div>
                </div>

                {{-- Copiar --}}
                <button @click="copySummary()" type="button"
                        :style="copied ? 'width:100%;padding:8px;border-radius:8px;border:none;cursor:pointer;font-size:12px;font-weight:600;background:#059669;color:white;display:flex;align-items:center;justify-content:center;gap:5px;transition:all 0.2s;' : 'width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;cursor:pointer;font-size:12px;font-weight:600;background:white;color:#374151;display:flex;align-items:center;justify-content:center;gap:5px;transition:all 0.2s;'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span x-text="copied ? 'Copiado!' : 'Copiar resumo'"></span>
                </button>
            </div>

            {{-- Estado vazio --}}
            <div x-show="valueInReais <= 0" style="text-align: center; padding: 24px 14px; color: #9ca3af;">
                <svg style="width: 44px; height: 44px; margin: 0 auto 10px; opacity: 0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-size: 13px;">Informe o valor do produto em dólar</p>
            </div>
        </div>
    </div>
</div>

<script>
function importCalculator(serverRate) {
    return {
        open: false,
        copied: false,
        dollarValue: '',
        dollarRate: serverRate,
        transportRate: '',
        valueInReais: 0,
        transportValue: 0,
        totalCost: 0,

        init() {
            const saved = localStorage.getItem('dgstore_import_calc');
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    if (data.transportRate !== undefined) this.transportRate = data.transportRate;
                } catch (e) {}
            }
            this.$watch('open', (val) => {
                if (val) {
                    this.$nextTick(() => {
                        if (this.$refs.dollarField) this.$refs.dollarField.focus();
                    });
                }
            });
        },

        parseNumber(value) {
            if (value === null || value === undefined || value === '') return 0;
            let str = String(value).trim();
            if (str === '') return 0;
            if (str.includes(',')) {
                str = str.replace(/\./g, '').replace(',', '.');
            }
            const parsed = parseFloat(str);
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
            const transport = this.parseNumber(this.transportRate);

            this.valueInReais = dollar * this.dollarRate;
            this.transportValue = this.valueInReais * (transport / 100);
            this.totalCost = this.valueInReais + this.transportValue;

            localStorage.setItem('dgstore_import_calc', JSON.stringify({
                transportRate: this.transportRate
            }));
        },

        copySummary() {
            var self = this;
            var lines = [];
            lines.push('*Cálculo de Importação*');
            lines.push('');
            lines.push('Valor: US$ ' + (this.dollarValue || '0'));
            lines.push('Cotação: R$ ' + this.formatNumber(this.dollarRate));
            lines.push('Convertido: R$ ' + this.formatNumber(this.valueInReais));
            lines.push('');
            lines.push('Transporte: ' + (this.transportRate || '0') + '%');
            lines.push('Valor transporte: R$ ' + this.formatNumber(this.transportValue));
            lines.push('');
            lines.push('CUSTO TOTAL: R$ ' + this.formatNumber(this.totalCost));

            var text = lines.join('\n');
            navigator.clipboard.writeText(text).then(function() {
                self.copied = true;
                setTimeout(function() { self.copied = false; }, 2500);
            });
        }
    }
}
</script>
@endif
