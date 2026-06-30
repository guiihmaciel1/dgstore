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

            {{-- Layout: mobile = coluna única / desktop = 2 colunas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 items-start">

                {{-- Inputs --}}
                <div class="order-1">
                    @include('tools.negotiation._quick-values')
                    @include('tools.negotiation._product-form')
                    @include('tools.negotiation._payment-inputs')

                    {{-- Estado vazio --}}
                    <div x-show="!cardLoading && cardResults.length === 0 && productPrice <= 0"
                         class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Preencha o valor do produto para montar a proposta</p>
                    </div>
                </div>

                {{-- Preview da mensagem (direita no desktop, sticky) --}}
                <div class="order-2 lg:sticky lg:top-6 space-y-3">
                    @include('tools.negotiation._message-preview')
                    @include('tools.negotiation._commission-preview')
                </div>

                {{-- Resultados (abaixo dos inputs na coluna esquerda) --}}
                <div class="order-3">
                    @include('tools.negotiation._results')
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
            quickValuesUsed: @json($quickValuesUsed ?? []),
            tradeInModels: @json($tradeInModels ?? []),
            toast: '',
            copied: false,
            cardLoading: false,
            activePreset: 'even',
            evalSearch: '',
            evalDropdownOpen: false,

            productCost: 0,
            product: { description: '', priceInput: '' },
            tradeIn: {
                showEval: false, model: '', storage: '', battery: 100,
                deviceState: 'original', noBox: false, noCable: false,
                result: null, error: null, loading: false, offeredInput: '',
            },
            downPaymentInput: '',
            numberGame: { enabled: false, boost: 0 },
            cardResults: [],

            saveModal: {
                open: false,
                search: '',
                results: [],
                selectedCustomer: null,
                notes: '',
                saving: false,
            },

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

                const params = new URLSearchParams(window.location.search);
                if (params.get('snap_product')) {
                    this.product.description = params.get('snap_product');
                    this.product.priceInput = this.fmt(parseFloat(params.get('snap_price')) || 0);
                    this.productCost = parseFloat(params.get('snap_cost')) || 0;
                    if (params.get('snap_tradein_model')) {
                        this.tradeIn.model = params.get('snap_tradein_model');
                        this.tradeIn.storage = params.get('snap_tradein_storage') || '';
                        this.tradeIn.battery = parseInt(params.get('snap_tradein_battery')) || 100;
                        this.tradeIn.offeredInput = this.fmt(parseFloat(params.get('snap_tradein_value')) || 0);
                        this.tradeIn.showEval = true;
                        const sysValue = parseFloat(params.get('snap_tradein_system_value')) || 0;
                        if (sysValue > 0) {
                            this.tradeIn.result = { resale_price: sysValue };
                        }
                    }
                    this.$nextTick(() => this.recalculate());
                }
            },

            get productPrice() { return this.parseNum(this.product.priceInput); },
            get tradeInValue() { return this.parseNum(this.tradeIn.offeredInput); },
            get downPayment() { return this.parseNum(this.downPaymentInput); },
            get cardBalance() {
                return Math.max(0, this.productPrice - this.tradeInValue - this.downPayment);
            },
            get activeBoost() {
                return this.numberGame.enabled ? this.numberGame.boost : 0;
            },
            get displayTradeInValue() {
                return this.tradeInValue + this.activeBoost;
            },
            get displayBalance() {
                return Math.max(0, (this.productPrice + this.activeBoost) - this.displayTradeInValue - this.downPayment);
            },
            get boostMax() {
                return Math.max(0, Math.floor(this.tradeInValue / 50) * 50) || 500;
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

            get commissionEstimate() {
                const RATE = 0.10;
                const TRADEIN_RATE = 0.02;
                const FLOOR = 0.0;
                const TRADEIN_CAP = 0.20;
                let profit = 0;
                let tradein = 0;

                if (this.productCost > 0 && this.productPrice > 0) {
                    const lucro = this.productPrice - this.productCost;
                    const minProfit = this.productCost * FLOOR;
                    if (lucro >= minProfit) {
                        profit = lucro * RATE;
                    }
                }

                if (this.tradeIn.result && this.tradeInValue > 0) {
                    const systemValue = this.tradeIn.result.resale_price || 0;
                    if (systemValue > 0 && this.tradeInValue < systemValue) {
                        let economy = systemValue - this.tradeInValue;
                        const maxDiscount = systemValue * TRADEIN_CAP;
                        economy = Math.min(economy, maxDiscount);
                        tradein = economy * TRADEIN_RATE;
                    }
                }

                return {
                    profit: Math.round(profit * 100) / 100,
                    tradein: Math.round(tradein * 100) / 100,
                    total: Math.round((profit + tradein) * 100) / 100,
                };
            },

            selectQuickValue(name, value, costPrice) {
                this.product.description = name;
                this.product.priceInput = this.fmt(value);
                this.productCost = costPrice || 0;
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
                this.numberGame = { enabled: false, boost: 0 };
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
                const balance = this.cardBalance;

                const desc = this.product.description || 'Produto';
                lines.push(`📱 *${desc}*`);

                if (this.tradeInValue > 0) {
                    const tiDesc = this.tradeIn.model
                        ? this.tradeIn.model + (this.tradeIn.storage ? ' ' + this.tradeIn.storage : '')
                        : 'Seu aparelho';
                    const tiDisplay = this.activeBoost > 0
                        ? `${tiDesc} *(R$ ${this.fmt(this.displayTradeInValue)})*`
                        : tiDesc;
                    lines.push(`🔄 *Seu seminovo:* ${tiDisplay}`);
                }

                if (this.downPayment > 0) {
                    lines.push(`💵 Entrada: *R$ ${this.fmt(this.downPayment)}*`);
                }

                if (balance > 0) {
                    if (this.tradeInValue > 0 || this.downPayment > 0) lines.push('');
                    lines.push(`━━━━━━━━━━━━━━━`);
                    const hasTradeInOrDown = this.tradeInValue > 0 || this.downPayment > 0;
                    const label = hasTradeInOrDown
                        ? `Diferença à vista com desconto: R$ ${this.fmt(balance)}`
                        : `Valor à vista com desconto R$ ${this.fmt(balance)}`;
                    lines.push(`✅ *${label}*`);

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
                lines.push(`⏳ _Simulação válida somente hoje (${expiry})_`);

                return lines.join('\n');
            },

            getExpiryDate() {
                return new Date().toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
            },

            async copyMessage() {
                try {
                    await navigator.clipboard.writeText(this.buildMessage());
                    this.copied = true;
                    this.showToast('Proposta copiada!');
                    setTimeout(() => { this.copied = false; }, 2000);
                } catch (e) {}
            },

            openSaveModal() {
                this.saveModal.open = true;
                this.saveModal.search = '';
                this.saveModal.results = [];
                this.saveModal.selectedCustomer = null;
                this.saveModal.notes = '';
                this.saveModal.saving = false;
            },

            async searchSaveCustomers() {
                if (this.saveModal.search.length < 2) {
                    this.saveModal.results = [];
                    return;
                }
                const res = await fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(this.saveModal.search)}`);
                this.saveModal.results = await res.json();
            },

            selectSaveCustomer(customer) {
                this.saveModal.selectedCustomer = customer;
                this.saveModal.results = [];
                this.saveModal.search = '';
            },

            async saveSnapshot() {
                if (!this.saveModal.selectedCustomer || this.saveModal.saving) return;
                this.saveModal.saving = true;

                const payload = {
                    customer_id: this.saveModal.selectedCustomer.id,
                    product_description: this.product.description,
                    product_price: this.productPrice,
                    product_cost: this.productCost || null,
                    trade_in_model: this.tradeIn.model || null,
                    trade_in_storage: this.tradeIn.storage || null,
                    trade_in_battery: this.tradeIn.battery || null,
                    trade_in_value: this.tradeInValue || null,
                    trade_in_system_value: this.tradeIn.result?.resale_price || null,
                    down_payment: this.downPayment || 0,
                    card_balance: this.cardBalance || 0,
                    commission_estimate: this.commissionEstimate.total || 0,
                    message_text: this.buildMessage(),
                    notes: this.saveModal.notes || null,
                };

                try {
                    const res = await fetch('{{ route("negotiation.save-snapshot") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.saveModal.open = false;
                        this.showToast('Simulação salva para ' + this.saveModal.selectedCustomer.name);
                    } else {
                        this.showToast('Erro ao salvar simulação');
                    }
                } catch (e) {
                    this.showToast('Erro ao salvar simulação');
                } finally {
                    this.saveModal.saving = false;
                }
            },

            clearAll() {
                this.product = { description: '', priceInput: '' };
                this.productCost = 0;
                this.tradeIn = {
                    showEval: false, model: '', storage: '', battery: 100,
                    deviceState: 'original', noBox: false, noCable: false,
                    result: null, error: null, loading: false, offeredInput: '',
                };
                this.downPaymentInput = '';
                this.numberGame = { enabled: false, boost: 0 };
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
