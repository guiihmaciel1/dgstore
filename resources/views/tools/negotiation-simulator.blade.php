<x-app-layout>
    <x-slot name="title">Simulador de Negociação</x-slot>
    <div class="py-4 sm:py-6" x-data="negotiationSimulator()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-4 sm:mb-6 flex-wrap gap-3">
                <div>
                    <h1 class="text-lg sm:text-2xl font-bold text-gray-900">Simulador de Negociação</h1>
                    <p class="text-sm text-gray-500">Monte propostas completas com trade-in e parcelamento</p>
                </div>
                <button @click="clearAll()" type="button"
                        class="apple-btn-dark text-[0.8rem]">
                    + Nova Simulação
                </button>
            </div>

            {{-- Layout: mobile = coluna única reordenada / desktop = 2 colunas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 items-start">

                {{-- Inputs (sempre primeiro) --}}
                <div class="order-1">
                    @include('tools.negotiation._quick-values')
                    @include('tools.negotiation._product-form')
                    @include('tools.negotiation._payment-inputs')
                </div>

                {{-- Resultados (sobe para 2o no mobile, fica na coluna direita no desktop) --}}
                <div class="order-2 lg:order-none">
                    @include('tools.negotiation._results')
                </div>

                {{-- Preview da mensagem (desce para 3o no mobile, fica abaixo dos inputs no desktop) --}}
                <div class="order-3 lg:order-none">
                    @include('tools.negotiation._message-preview')
                </div>
            </div>
        </div>

        {{-- Toast --}}
        <div x-show="toast"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-[0_8px_24px_rgba(0,0,0,0.3)] z-50 flex items-center gap-2">
            <svg class="w-[18px] h-[18px] text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="toast"></span>
        </div>
    </div>

    @push('scripts')
    <script>
    function negotiationSimulator() {
        return {
            quickValues: @json($quickValuesFromMarketing ?? []),
            tradeInModels: @json($tradeInModels ?? []),
            toast: '',
            copied: false,
            cardLoading: false,
            activePreset: 'even',
            evalSearch: '',
            evalDropdownOpen: false,

            product: { description: '', priceInput: '' },
            tradeIn: {
                showEval: false, model: '', storage: '', battery: 100,
                deviceState: 'original', noBox: false, noCable: false,
                result: null, error: null, loading: false, offeredInput: '',
            },
            downPaymentInput: '',
            pixDiscountPercent: '',
            cardResults: [],

            presets: [
                { key: 'all', label: 'Todas' },
                { key: 'even', label: 'Pares' },
                { key: 'up_to_12', label: 'Até 12x' },
                { key: 'above_6', label: '6x+' },
                { key: 'above_10', label: '10x+' },
            ],

            _evalTimer: null,
            _recalcTimer: null,

            init() {
                const debouncedRecalc = () => {
                    clearTimeout(this._recalcTimer);
                    this._recalcTimer = setTimeout(() => this.recalculate(), 300);
                };
                this.$watch('product.priceInput', debouncedRecalc);
                this.$watch('downPaymentInput', debouncedRecalc);
                this.$watch('tradeIn.offeredInput', debouncedRecalc);
            },

            get productPrice() { return this.parseNum(this.product.priceInput); },
            get tradeInValue() { return this.parseNum(this.tradeIn.offeredInput); },
            get downPayment() { return this.parseNum(this.downPaymentInput); },
            get cardBalance() {
                return Math.max(0, this.productPrice - this.tradeInValue - this.downPayment);
            },
            get pixDiscount() {
                return parseFloat(this.pixDiscountPercent) || 0;
            },
            get pixPrice() {
                const disc = this.pixDiscount;
                if (disc <= 0 || disc > 100) return this.cardBalance;
                return this.cardBalance * (1 - disc / 100);
            },
            get tradeInStorages() {
                return this.tradeInModels[this.tradeIn.model] || [];
            },

            get selectedCardRow() {
                return this.cardResults.find(r => r.selected && r.installments > 1) || this.cardResults.find(r => r.selected) || null;
            },
            get stoneFee() {
                if (!this.selectedCardRow || this.cardBalance <= 0) return 0;
                return this.selectedCardRow.fee_amount || 0;
            },
            get revenueTotal() {
                let rev = this.productPrice;
                if (this.tradeIn.result) {
                    rev += (this.tradeIn.result.resale_price || 0) - this.tradeInValue;
                }
                return rev;
            },

            selectQuickValue(name, value) {
                this.product.description = name;
                this.product.priceInput = this.fmt(value);
                this.recalculate();
            },
            isQuickValueActive(name, value) {
                return this.product.description === name && this.product.priceInput === this.fmt(value);
            },
            isQuickValueActiveByName(baseName) {
                return this.product.description.startsWith(baseName);
            },
            chipLabel(name) {
                return name
                    .replace(/iPhone\s+(\d+)\s+Pro\s+Max/i, '$1PM')
                    .replace(/iPhone\s+(\d+)\s+Pro/i, '$1P')
                    .replace(/iPhone\s+(\d+)/i, '$1');
            },

            filteredTradeInModels(search) {
                if (!search) return this.tradeInModels;
                const s = search.toLowerCase();
                return Object.fromEntries(
                    Object.entries(this.tradeInModels).filter(([k]) => k.toLowerCase().includes(s))
                );
            },
            groupedTradeInModels(search) {
                const filtered = this.filteredTradeInModels(search);
                const groups = {};
                for (const name of Object.keys(filtered)) {
                    const match = name.match(/iPhone (\d+)/);
                    const gen = match ? `iPhone ${match[1]}` : 'Outros';
                    if (!groups[gen]) groups[gen] = [];
                    groups[gen].push(name);
                }
                return Object.entries(groups).map(([generation, models]) => ({ generation, models }));
            },
            selectTradeInModel(name) {
                this.tradeIn.model = name;
                const storages = this.tradeInModels[name] || [];
                if (!storages.includes(this.tradeIn.storage)) {
                    this.tradeIn.storage = storages[0] || '';
                }
                this.debouncedEvaluate();
            },
            clearTradeIn() {
                this.tradeIn.model = '';
                this.tradeIn.storage = '';
                this.tradeIn.battery = 100;
                this.tradeIn.deviceState = 'original';
                this.tradeIn.noBox = false;
                this.tradeIn.noCable = false;
                this.tradeIn.result = null;
                this.tradeIn.error = null;
                this.evalSearch = '';
                this.evalDropdownOpen = false;
                this.tradeIn.offeredInput = '';
                this.recalculate();
            },

            debouncedEvaluate() {
                clearTimeout(this._evalTimer);
                this._evalTimer = setTimeout(() => this.evaluateTradeIn(), 600);
            },

            async evaluateTradeIn() {
                if (!this.tradeIn.model || !this.tradeIn.storage) return;

                this.tradeIn.loading = true;
                this.tradeIn.error = null;

                try {
                    const res = await fetch('{{ route("negotiation.evaluate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            model: this.tradeIn.model,
                            storage: this.tradeIn.storage,
                            battery_health: this.tradeIn.battery,
                            device_state: this.tradeIn.deviceState,
                            no_box: this.tradeIn.noBox,
                            no_cable: this.tradeIn.noCable,
                        }),
                    });

                    const json = await res.json();

                    if (!res.ok || !json.success) {
                        this.tradeIn.error = json.message || 'Erro ao avaliar.';
                        this.tradeIn.result = null;
                        return;
                    }

                    const data = json.data;
                    if (data.listings_count === 0) {
                        this.tradeIn.error = 'Nenhum anúncio encontrado para este modelo.';
                        this.tradeIn.result = null;
                        return;
                    }

                    this.tradeIn.result = data;
                } catch (e) {
                    this.tradeIn.error = 'Erro de conexão com o avaliador.';
                    this.tradeIn.result = null;
                } finally {
                    this.tradeIn.loading = false;
                }
            },

            async recalculate() {
                const balance = this.cardBalance;
                if (balance <= 0) {
                    this.cardResults = [];
                    return;
                }

                this.cardLoading = true;
                try {
                    const res = await fetch('/api/card-fees/calculate-all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ net_amount: balance }),
                    });
                    const json = await res.json();

                    if (json.success) {
                        const prev = this.activePreset || 'even';
                        this.cardResults = json.data.map(r => ({
                            ...r,
                            label: r.installments === 1 ? 'Crédito 1x' : `Crédito ${r.installments}x`,
                            selected: true,
                        }));
                        this.setSelection(prev);
                    } else {
                        this.cardResults = [];
                    }
                } catch (e) {
                    this.cardResults = [];
                } finally {
                    this.cardLoading = false;
                }
            },

            setSelection(preset) {
                this.activePreset = preset;
                this.cardResults.forEach(r => {
                    if (preset === 'all') r.selected = true;
                    else if (preset === 'even') r.selected = r.installments % 2 === 0;
                    else if (preset === 'up_to_12') r.selected = r.installments >= 2 && r.installments <= 12 && r.installments % 2 === 0;
                    else if (preset === 'above_6') r.selected = r.installments >= 6 && r.installments % 2 === 0;
                    else if (preset === 'above_10') r.selected = r.installments >= 10 && r.installments % 2 === 0;
                });
            },

            buildMessage() {
                const lines = [];
                const desc = this.product.description || 'Produto';

                lines.push(`📱 *${desc}*`);
                lines.push(`💰 *R$ ${this.fmt(this.productPrice)}*`);

                if (this.tradeInValue > 0) {
                    const tiDesc = this.tradeIn.model
                        ? this.tradeIn.model + (this.tradeIn.storage ? ' ' + this.tradeIn.storage : '')
                        : 'Seu aparelho';
                    lines.push('');
                    lines.push(`🔄 *Seu seminovo:* ${tiDesc}`);
                    lines.push(`Desconto: *- R$ ${this.fmt(this.tradeInValue)}*`);
                }

                if (this.downPayment > 0) {
                    lines.push(`💵 Entrada: *R$ ${this.fmt(this.downPayment)}*`);
                }

                const balance = this.cardBalance;
                if (balance > 0) {
                    lines.push('');
                    lines.push(`━━━━━━━━━━━━━━━`);
                    if (this.pixDiscount > 0) {
                        lines.push(`✅ *Pix/À vista: R$ ${this.fmt(this.pixPrice)}* _(${this.pixDiscount}% off)_`);
                    } else {
                        lines.push(`✅ *Pix/À vista: R$ ${this.fmt(balance)}*`);
                    }

                    const selected = this.cardResults.filter(r => r.selected);
                    if (selected.length > 0) {
                        lines.push('');
                        lines.push(`💳 *Parcele em até ${selected[selected.length - 1].installments}x:*`);
                        selected.forEach(r => {
                            lines.push(`  ${r.installments}x › R$ ${this.fmt(r.installment_value)}`);
                        });
                    }
                }

                lines.push('');
                const expiry = this.getExpiryDate();
                lines.push(`⏳ _Simulação válida até ${expiry}_`);

                return lines.join('\n');
            },

            getExpiryDate() {
                const d = new Date();
                let added = 0;
                while (added < 2) {
                    d.setDate(d.getDate() + 1);
                    const dow = d.getDay();
                    if (dow !== 0 && dow !== 6) added++;
                }
                return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
            },

            async copyMessage() {
                try {
                    await navigator.clipboard.writeText(this.buildMessage());
                    this.copied = true;
                    this.showToast('Proposta copiada!');
                    setTimeout(() => { this.copied = false; }, 2000);
                } catch (e) {}
            },

            clearAll() {
                this.product = { description: '', priceInput: '' };
                this.tradeIn = {
                    showEval: false, model: '', storage: '', battery: 100,
                    deviceState: 'original', noBox: false, noCable: false,
                    result: null, error: null, loading: false, offeredInput: '',
                };
                this.downPaymentInput = '';
                this.pixDiscountPercent = '';
                this.cardResults = [];
                this.activePreset = 'even';
                this.evalSearch = '';
                this.evalDropdownOpen = false;
            },

            parseNum(value) {
                if (!value) return 0;
                let str = String(value).trim().replace(/\s/g, '');
                str = str.replace(/\./g, '').replace(',', '.');
                const n = parseFloat(str);
                return isNaN(n) ? 0 : n;
            },

            fmt(value) {
                if (value === null || value === undefined || isNaN(value)) return '0,00';
                return Number(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            showToast(msg) {
                this.toast = msg;
                setTimeout(() => { this.toast = ''; }, 2000);
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
