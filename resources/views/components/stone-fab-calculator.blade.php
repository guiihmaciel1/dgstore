<div x-data="stoneFabCalc()" x-init="init()" @open-stone-calc.window="open = true">

    <!-- Overlay -->
    <div x-show="open" x-transition.opacity.duration.200ms @click="open = false"
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.25); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 45;" x-cloak></div>

    <!-- Panel -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         @keydown.escape.window="open = false"
         x-cloak
         :style="'position: fixed; z-index: 50; overflow: hidden; ' + panelPosition"
         class="calc-panel">

        <!-- Header -->
        <div class="calc-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="calc-header-icon">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25v-.008zm2.25-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm2.25-2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zM6.75 19.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm4.5-15v3.75"/>
                    </svg>
                </div>
                <div>
                    <span style="font-size: 14px; font-weight: 700; letter-spacing: -0.01em;">Calculadora</span>
                    <span style="font-size: 10px; font-weight: 500; opacity: 0.7; margin-left: 6px;">Stone</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 4px;">
                <button @click="clearAll()" type="button" class="calc-header-btn" title="Limpar tudo">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.992 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.185M21.036 4.356v4.992"/>
                    </svg>
                </button>
                <button @click="open = false" type="button" class="calc-header-btn" title="Fechar">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div :style="'padding: 16px; overflow-y: auto; max-height: ' + bodyMaxHeight + ';'" class="calc-body">

            <!-- Valor do Produto -->
            <div class="calc-input-group calc-input-main">
                <label class="calc-label">Valor do produto</label>
                <div class="calc-input-wrap">
                    <span class="calc-currency">R$</span>
                    <input type="text" x-model="amountInput" x-ref="amountField"
                           @input.debounce.300ms="calculate()"
                           placeholder="0,00" inputmode="decimal"
                           class="calc-input calc-input-primary">
                </div>
            </div>

            <!-- Trade-in / Entrada side by side -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;">
                <div class="calc-input-group">
                    <label class="calc-label" style="color: #f59e0b;">
                        <svg style="width: 12px; height: 12px; display: inline; vertical-align: -1px; margin-right: 2px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                        </svg>
                        Trade-in
                    </label>
                    <div class="calc-input-wrap calc-input-wrap-amber">
                        <span class="calc-currency" style="color: #f59e0b;">R$</span>
                        <input type="text" x-model="tradeInInput"
                               @input.debounce.300ms="calculate()"
                               placeholder="0,00" inputmode="decimal"
                               class="calc-input calc-input-amber">
                    </div>
                </div>
                <div class="calc-input-group">
                    <label class="calc-label" style="color: #10b981;">
                        <svg style="width: 12px; height: 12px; display: inline; vertical-align: -1px; margin-right: 2px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                        </svg>
                        Entrada
                    </label>
                    <div class="calc-input-wrap calc-input-wrap-green">
                        <span class="calc-currency" style="color: #10b981;">R$</span>
                        <input type="text" x-model="downPaymentInput"
                               @input.debounce.300ms="calculate()"
                               placeholder="0,00" inputmode="decimal"
                               class="calc-input calc-input-green">
                    </div>
                </div>
            </div>

            <!-- Saldo no Cartão -->
            <div x-show="finalAmount > 0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="calc-balance">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <svg style="width: 14px; height: 14px; color: #6366f1;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                    <span style="font-size: 12px; color: #64748b; font-weight: 500;">No cartão</span>
                </div>
                <span style="font-size: 16px; font-weight: 800; color: #1e293b; letter-spacing: -0.02em;" x-text="'R$ ' + fmt(finalAmount)"></span>
            </div>

            <!-- Filtros -->
            <div x-show="results.length > 0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="calc-filters">
                <div style="display: flex; flex-wrap: wrap; gap: 5px; flex: 1;">
                    <template x-for="p in presets" :key="p.key">
                        <button @click="setFilter(p.key)" type="button"
                                :class="filter === p.key ? 'calc-filter-btn calc-filter-active' : 'calc-filter-btn'"
                                x-text="p.label"></button>
                    </template>
                </div>
                <button @click="copyAll()" type="button"
                        :class="copiedAll ? 'calc-copy-all-btn calc-copy-all-done' : 'calc-copy-all-btn'">
                    <svg x-show="!copiedAll" style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <svg x-show="copiedAll" style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <span x-text="copiedAll ? 'Copiado' : 'Copiar'"></span>
                </button>
            </div>

            <!-- Loading -->
            <div x-show="loading" style="text-align: center; padding: 28px 0;">
                <div class="calc-spinner"></div>
            </div>

            <!-- Resultados -->
            <div x-show="!loading && results.length > 0"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100">
                <div class="calc-results">
                    <template x-for="(row, idx) in filteredResults" :key="idx">
                        <div class="calc-row" :class="{ 'calc-row-alt': idx % 2 !== 0 }">
                            <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                <div class="calc-installment-badge" x-text="row.installments + 'x'"></div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 700; color: #1e293b; letter-spacing: -0.01em;" x-text="'R$ ' + fmt(row.installment_value)">
                                    </div>
                                    <div style="font-size: 10px; color: #94a3b8; font-weight: 500;">/mês</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                <div style="text-align: right;">
                                    <div style="font-size: 14px; font-weight: 800; color: #1e293b; letter-spacing: -0.02em;" x-text="'R$ ' + fmt(row.gross_amount)"></div>
                                    <div style="font-size: 10px; color: #94a3b8; font-weight: 500;">total</div>
                                </div>
                                <button @click="copyRow(row)" type="button" class="calc-copy-btn"
                                        :class="{ 'calc-copy-btn-done': row.copied }"
                                        :title="row.copied ? 'Copiado!' : 'Copiar'">
                                    <svg x-show="!row.copied" style="width: 13px; height: 13px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <svg x-show="row.copied" style="width: 13px; height: 13px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- Toast -->
        <div x-show="toast" x-transition.opacity.duration.200ms x-cloak class="calc-toast">
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <span x-text="toast"></span>
        </div>
    </div>

    <style>
        @keyframes fabSpin {
            to { transform: rotate(360deg); }
        }

        @keyframes fabPulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(99, 102, 241, 0.4); }
            50% { box-shadow: 0 8px 40px rgba(99, 102, 241, 0.6); }
        }

        .fab-pulse { animation: fabPulse 3s ease-in-out infinite; }

        .calc-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .calc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }

        .calc-header-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .calc-header-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .calc-header-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .calc-body {
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }
        .calc-body::-webkit-scrollbar { width: 4px; }
        .calc-body::-webkit-scrollbar-track { background: transparent; }
        .calc-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .calc-input-group {
            margin-bottom: 0;
        }

        .calc-input-main {
            margin-bottom: 14px;
        }

        .calc-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 5px;
            display: block;
        }

        .calc-input-wrap {
            position: relative;
            margin-top: 4px;
        }

        .calc-currency {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            pointer-events: none;
        }

        .calc-input {
            width: 100%;
            border: none;
            outline: none;
            font-family: inherit;
            text-align: right;
            transition: all 0.2s ease;
        }

        .calc-input-primary {
            padding: 14px 14px 14px 36px;
            border-radius: 12px;
            font-size: 22px;
            font-weight: 800;
            color: #1e293b;
            background: #f8fafc;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.06);
            letter-spacing: -0.02em;
        }
        .calc-input-primary:focus {
            background: #141414;
            box-shadow: inset 0 0 0 2px #6366f1, 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .calc-input-amber {
            padding: 10px 10px 10px 32px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            color: #d97706;
            background: #141414beb;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
        }
        .calc-input-amber:focus {
            background: #141414;
            box-shadow: inset 0 0 0 2px #f59e0b, 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .calc-input-green {
            padding: 10px 10px 10px 32px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            color: #059669;
            background: rgba(16,185,129,0.1);
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
        }
        .calc-input-green:focus {
            background: #141414;
            box-shadow: inset 0 0 0 2px #10b981, 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .calc-input-wrap-amber {
            border-radius: 10px;
            background: #141414beb;
        }
        .calc-input-wrap-green {
            border-radius: 10px;
            background: rgba(16,185,129,0.1);
        }

        .calc-balance {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calc-filters {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .calc-filter-btn {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.06);
            background: #141414;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .calc-filter-btn:hover {
            border-color: #cbd5e1;
            color: #334155;
        }
        .calc-filter-active {
            background: #6366f1 !important;
            color: white !important;
            border-color: #6366f1 !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        .calc-copy-all-btn {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            border: none;
            background: #6366f1;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .calc-copy-all-btn:hover {
            background: #4f46e5;
        }
        .calc-copy-all-done {
            background: #10b981 !important;
        }

        .calc-spinner {
            display: inline-block;
            width: 28px;
            height: 28px;
            border: 3px solid #e2e8f0;
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: fabSpin 0.7s linear infinite;
        }

        .calc-results {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .calc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: #141414;
            transition: background 0.15s;
        }
        .calc-row:not(:last-child) {
            border-bottom: 1px solid #f1f5f9;
        }
        .calc-row-alt {
            background: #fafbfc;
        }
        .calc-row:hover {
            background: #f8fafc;
        }

        .calc-installment-badge {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            color: #4f46e5;
            font-size: 12px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .calc-copy-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.06);
            background: #141414;
            color: #94a3b8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .calc-copy-btn:hover {
            border-color: #6366f1;
            color: #6366f1;
            background: rgba(99,102,241,0.1);
        }
        .calc-copy-btn-done {
            background: #10b981 !important;
            border-color: #10b981 !important;
            color: white !important;
        }

        .calc-toast {
            position: absolute;
            bottom: 14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        @media (max-width: 639px) {
            .calc-panel {
                border-radius: 20px 20px 0 0 !important;
            }
        }
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
                return 'bottom: 0; left: 0; right: 0; max-height: 85dvh; box-shadow: 0 -10px 60px rgba(0,0,0,0.15);';
            }
            return 'bottom: 24px; right: 24px; width: 370px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.06); max-height: calc(100vh - 80px);';
        },

        get bodyMaxHeight() {
            const isMobile = window.innerWidth < 640;
            return isMobile ? 'calc(85dvh - 50px)' : 'calc(100vh - 160px)';
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
            if (Math.abs(currentX - this.dragStartX) > 30) {
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
                this.side = endX < window.innerWidth / 2 ? 'left' : 'right';
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
                this.results = data.success ? data.data.map(r => ({ ...r, copied: false })) : [];
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
