<div x-data="stoneFabCalc()" x-init="init()">
    <!-- Botão Flutuante Draggável -->
    <button x-ref="fab"
            @mousedown="startDrag($event)"
            @touchstart.passive="startDrag($event)"
            :style="'position: fixed; bottom: 24px; ' + side + ': 24px; z-index: 40; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #111827, #374151); color: white; border: none; cursor: grab; box-shadow: 0 6px 20px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; transition: ' + (dragging ? 'none' : 'all 0.3s ease') + '; user-select: none; -webkit-user-select: none; touch-action: none;'"
            title="Calculadora Stone">
        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
    </button>

    <!-- Overlay -->
    <div x-show="open" x-transition.opacity @click="open = false"
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 45;" x-cloak></div>

    <!-- Painel -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         @keydown.escape.window="open = false"
         x-cloak
         :style="'position: fixed; z-index: 50; background: white; overflow: hidden; ' + panelPosition">

        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; background: #111827; color: white;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span style="font-size: 14px; font-weight: 700;">Calculadora</span>
            </div>
            <button @click="open = false" type="button" style="background: none; border: none; color: white; cursor: pointer; padding: 4px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div style="padding: 14px 16px; overflow-y: auto; max-height: calc(100dvh - 100px);">

            <!-- Inputs -->
            <div style="margin-bottom: 12px;">
                <label style="font-size: 11px; font-weight: 700; color: #111827; text-transform: uppercase; letter-spacing: 0.05em;">Valor do produto *</label>
                <div style="position: relative; margin-top: 4px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 13px;">R$</span>
                    <input type="text" x-model="amountInput" x-ref="amountField"
                           @input.debounce.300ms="calculate()"
                           placeholder="0,00" inputmode="decimal"
                           style="width: 100%; padding: 12px 12px 12px 36px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 20px; font-weight: 700; color: #111827; outline: none; text-align: right; background: #f9fafb;"
                           onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 11px; font-weight: 600; color: #d97706; text-transform: uppercase;">Trade-in</label>
                    <div style="position: relative; margin-top: 4px;">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #fbbf24; font-size: 12px;">R$</span>
                        <input type="text" x-model="tradeInInput"
                               @input.debounce.300ms="calculate()"
                               placeholder="0,00" inputmode="decimal"
                               style="width: 100%; padding: 10px 10px 10px 32px; border: 1px solid #fde68a; border-radius: 8px; font-size: 15px; font-weight: 600; color: #d97706; outline: none; text-align: right; background: #fffbeb;"
                               onfocus="this.style.borderColor='#d97706'; this.style.background='white'"
                               onblur="this.style.borderColor='#fde68a'; this.style.background='#fffbeb'">
                    </div>
                </div>
                <div>
                    <label style="font-size: 11px; font-weight: 600; color: #059669; text-transform: uppercase;">Entrada</label>
                    <div style="position: relative; margin-top: 4px;">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #6ee7b7; font-size: 12px;">R$</span>
                        <input type="text" x-model="downPaymentInput"
                               @input.debounce.300ms="calculate()"
                               placeholder="0,00" inputmode="decimal"
                               style="width: 100%; padding: 10px 10px 10px 32px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 15px; font-weight: 600; color: #059669; outline: none; text-align: right; background: #f0fdf4;"
                               onfocus="this.style.borderColor='#059669'; this.style.background='white'"
                               onblur="this.style.borderColor='#bbf7d0'; this.style.background='#f0fdf4'">
                    </div>
                </div>
            </div>

            <!-- Saldo no cartão -->
            <div x-show="finalAmount > 0" style="background: #f3f4f6; border-radius: 8px; padding: 8px 12px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 12px; color: #6b7280;">No cartão:</span>
                <span style="font-size: 15px; font-weight: 700; color: #111827;" x-text="'R$ ' + fmt(finalAmount)"></span>
            </div>

            <!-- Filtros + Copiar todas -->
            <div x-show="results.length > 0" style="display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 10px; align-items: center;">
                <template x-for="p in presets" :key="p.key">
                    <button @click="setFilter(p.key)" type="button"
                            :style="filter === p.key
                                ? 'padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; border: none; background: #111827; color: white; cursor: pointer;'
                                : 'padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; border: 1px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer;'"
                            x-text="p.label"></button>
                </template>
                <button @click="copyAll()" type="button"
                        :style="copiedAll
                            ? 'padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; border: none; background: #059669; color: white; cursor: pointer; margin-left: auto; display: flex; align-items: center; gap: 3px;'
                            : 'padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; border: none; background: #3b82f6; color: white; cursor: pointer; margin-left: auto; display: flex; align-items: center; gap: 3px;'">
                    <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span x-text="copiedAll ? '✓' : 'Copiar'"></span>
                </button>
            </div>


            <!-- Loading -->
            <div x-show="loading" style="text-align: center; padding: 20px;">
                <div style="display: inline-block; width: 28px; height: 28px; border: 3px solid #f3f4f6; border-top-color: #111827; border-radius: 50%; animation: fabSpin 0.8s linear infinite;"></div>
            </div>

            <!-- Resultados -->
            <div x-show="!loading && results.length > 0">
                <div style="border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
                    <template x-for="(row, idx) in filteredResults" :key="idx">
                        <div :style="'display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-bottom: 1px solid #f3f4f6; ' + (idx % 2 === 0 ? 'background: white;' : 'background: #fafafa;')">
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 13px; font-weight: 700; color: #111827;" x-text="row.installments + 'x'"></div>
                                <div style="font-size: 12px; color: #374151;" x-text="'R$ ' + fmt(row.installment_value) + '/mês'"></div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                <span style="font-size: 14px; font-weight: 700; color: #111827;" x-text="'R$ ' + fmt(row.gross_amount)"></span>
                                <button @click="copyRow(row)" type="button"
                                        :style="row.copied
                                            ? 'width: 30px; height: 30px; border-radius: 6px; border: none; background: #059669; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center;'
                                            : 'width: 30px; height: 30px; border-radius: 6px; border: none; background: #f3f4f6; color: #374151; cursor: pointer; display: flex; align-items: center; justify-content: center;'"
                                        :title="row.copied ? 'Copiado!' : 'Copiar'">
                                    <svg x-show="!row.copied" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <svg x-show="row.copied" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>


        <!-- Toast -->
        <div x-show="toast" x-transition.opacity x-cloak
             style="position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%); background: #111827; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; white-space: nowrap; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
            <span x-text="toast"></span>
        </div>
    </div>

    <style>
        @keyframes fabSpin { to { transform: rotate(360deg); } }
    </style>
</div>

@pushOnce('scripts')
<script>
function stoneFabCalc() {
    return {
        open: false,
        side: localStorage.getItem('stoneFabSide') || 'right',
        dragging: false,
        dragStartX: 0,
        wasDragged: false,
        loading: false,
        toast: '',
        filter: 'all',
        copiedAll: false,

        amountInput: '',
        tradeInInput: '',
        downPaymentInput: '',
        finalAmount: 0,
        results: [],

        presets: [
            { key: 'all', label: 'Todas' },
            { key: 'even', label: 'Pares' },
            { key: 'up_to_12', label: 'Até 12x' },
            { key: 'above_6', label: '6x+' },
            { key: 'above_10', label: '10x+' },
        ],

        get panelPosition() {
            const isMobile = window.innerWidth < 640;
            if (isMobile) {
                return 'bottom: 0; left: 0; right: 0; border-radius: 16px 16px 0 0; max-height: 85dvh; box-shadow: 0 -10px 40px rgba(0,0,0,0.2);';
            }
            return this.side === 'right'
                ? 'bottom: 90px; right: 24px; width: 360px; border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.2); max-height: 80vh;'
                : 'bottom: 90px; left: 24px; width: 360px; border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.2); max-height: 80vh;';
        },

        get filteredResults() {
            return this.results.filter(r => {
                if (this.filter === 'all') return true;
                if (this.filter === 'even') return r.installments % 2 === 0;
                if (this.filter === 'up_to_12') return r.installments <= 12;
                if (this.filter === 'above_6') return r.installments >= 6;
                if (this.filter === 'above_10') return r.installments >= 10;
                return true;
            });
        },

        init() {
            document.addEventListener('mousemove', (e) => this.onDrag(e));
            document.addEventListener('mouseup', (e) => this.endDrag(e));
            document.addEventListener('touchmove', (e) => this.onDrag(e), { passive: true });
            document.addEventListener('touchend', (e) => this.endDrag(e));
        },

        startDrag(e) {
            this.dragging = true;
            this.wasDragged = false;
            this.dragStartX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
        },

        onDrag(e) {
            if (!this.dragging) return;
            const currentX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
            const diff = currentX - this.dragStartX;
            if (Math.abs(diff) > 30) {
                this.wasDragged = true;
            }
        },

        endDrag(e) {
            if (!this.dragging) return;
            this.dragging = false;

            if (this.wasDragged) {
                const endX = e.type === 'touchend'
                    ? (e.changedTouches ? e.changedTouches[0].clientX : this.dragStartX)
                    : e.clientX;
                const screenMid = window.innerWidth / 2;
                this.side = endX < screenMid ? 'left' : 'right';
                localStorage.setItem('stoneFabSide', this.side);
            } else {
                this.open = true;
                this.$nextTick(() => {
                    if (this.$refs.amountField) this.$refs.amountField.focus();
                });
            }
        },

        parseNum(v) {
            if (!v) return 0;
            const str = String(v).replace(/\s/g, '').replace(/\./g, '').replace(',', '.');
            const n = parseFloat(str);
            return isNaN(n) ? 0 : n;
        },

        fmt(v) {
            if (v === null || v === undefined || isNaN(v)) return '0,00';
            return Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        setFilter(key) {
            this.filter = key;
        },

        clearAll() {
            this.amountInput = '';
            this.tradeInInput = '';
            this.downPaymentInput = '';
            this.finalAmount = 0;
            this.results = [];
            this.filter = 'all';
        },

        async calculate() {
            const amount = this.parseNum(this.amountInput);
            const tradeIn = this.parseNum(this.tradeInInput);
            const downPayment = this.parseNum(this.downPaymentInput);

            const net = Math.max(0, amount - tradeIn - downPayment);
            this.finalAmount = net;

            if (net <= 0) {
                this.results = [];
                return;
            }

            this.loading = true;
            try {
                const resp = await fetch('/api/card-fees/calculate-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ net_amount: net })
                });
                const data = await resp.json();
                if (data.success) {
                    this.results = data.data.map(r => ({ ...r, copied: false }));
                } else {
                    this.results = [];
                }
            } catch (err) {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        buildRowMessage(row) {
            return row.installments + 'x de R$ ' + this.fmt(row.installment_value);
        },

        buildAllMessage() {
            return this.filteredResults.map(r =>
                r.installments + 'x de R$ ' + this.fmt(r.installment_value)
            ).join('\n');
        },

        async copyAll() {
            const msg = this.buildAllMessage();
            try {
                await navigator.clipboard.writeText(msg);
            } catch (err) {
                this.fallbackCopy(msg);
                this.copiedAll = true;
                setTimeout(() => { this.copiedAll = false; }, 2000);
                return;
            }
            this.copiedAll = true;
            this.showToast('Todas copiadas!');
            setTimeout(() => { this.copiedAll = false; }, 2000);
        },

        async copyRow(row) {
            try {
                await navigator.clipboard.writeText(this.buildRowMessage(row));
                row.copied = true;
                this.showToast('Copiado!');
                setTimeout(() => { row.copied = false; }, 2000);
            } catch (err) {
                this.fallbackCopy(this.buildRowMessage(row));
                row.copied = true;
                setTimeout(() => { row.copied = false; }, 2000);
            }
        },

        fallbackCopy(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            this.showToast('Copiado!');
        },

        showToast(msg) {
            this.toast = msg;
            setTimeout(() => { this.toast = ''; }, 1500);
        }
    };
}
</script>
@endPushOnce
