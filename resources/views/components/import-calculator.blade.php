@php
    $dollarRate = \App\Domain\System\Models\SystemSetting::get('dollar_rate');
    $isAdmin = auth()->user()->isAdminGeral();
@endphp

@if($isAdmin && $dollarRate)
<div x-data="importCalc({{ (float) $dollarRate }})" x-init="init()" @open-import-calc.window="open = true">

    {{-- Overlay --}}
    <div x-show="open" x-transition.opacity.duration.200ms @click="open = false"
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.25); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 45;" x-cloak></div>

    {{-- Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         @keydown.escape.window="open = false"
         x-cloak
         class="imp-panel">

        {{-- Header --}}
        <div class="imp-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="imp-header-icon">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span style="font-size: 14px; font-weight: 700; letter-spacing: -0.01em;">Importação</span>
                    <span style="font-size: 10px; font-weight: 500; opacity: 0.7; margin-left: 6px;">USD → BRL</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 4px;">
                <button @click="clearAll()" type="button" class="imp-header-btn" title="Limpar tudo">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.992 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.185M21.036 4.356v4.992"/>
                    </svg>
                </button>
                <button @click="open = false" type="button" class="imp-header-btn" title="Fechar">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="imp-body" style="padding: 16px; overflow-y: auto; max-height: calc(100vh - 160px);">

            {{-- Cotação do dia --}}
            <div class="imp-rate-badge">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <svg style="width: 14px; height: 14px; color: #2563eb;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span style="font-size: 12px; color: #64748b; font-weight: 500;">Cotação do dia</span>
                </div>
                <span style="font-size: 16px; font-weight: 800; color: #1e293b; letter-spacing: -0.02em;">R$ <span x-text="fmt(dollarRate)"></span></span>
            </div>

            {{-- Valor em dólar --}}
            <div class="imp-input-group" style="margin-bottom: 14px;">
                <label class="imp-label">Valor do produto (US$)</label>
                <div class="imp-input-wrap">
                    <span class="imp-currency">$</span>
                    <input type="text" x-model="dollarValue" x-ref="dollarField"
                           @input="calculate()"
                           placeholder="0,00" inputmode="decimal"
                           class="imp-input imp-input-primary">
                </div>
            </div>

            {{-- Transporte --}}
            <div class="imp-input-group" style="margin-bottom: 14px;">
                <label class="imp-label" style="color: #f59e0b;">
                    <svg style="width: 12px; height: 12px; display: inline; vertical-align: -1px; margin-right: 2px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                    Transporte
                </label>
                <div class="imp-input-wrap imp-input-wrap-amber">
                    <span class="imp-currency" style="color: #f59e0b;">%</span>
                    <input type="text" x-model="transportRate"
                           @input="calculate()"
                           placeholder="0" inputmode="decimal"
                           class="imp-input imp-input-amber">
                </div>
            </div>

            {{-- Resultado convertido --}}
            <div x-show="valueInReais > 0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">

                {{-- Fórmula visual --}}
                <div class="imp-formula">
                    <div class="imp-formula-item">
                        <span class="imp-formula-label">USD</span>
                        <span class="imp-formula-value" x-text="'$ ' + (dollarValue || '0')"></span>
                    </div>
                    <span class="imp-formula-op">&times;</span>
                    <div class="imp-formula-item">
                        <span class="imp-formula-label">Câmbio</span>
                        <span class="imp-formula-value" x-text="'R$ ' + fmt(dollarRate)"></span>
                    </div>
                    <span class="imp-formula-op">=</span>
                    <div class="imp-formula-item imp-formula-highlight">
                        <span class="imp-formula-label">BRL</span>
                        <span class="imp-formula-value" style="color: #1e293b; font-weight: 800;" x-text="'R$ ' + fmt(valueInReais)"></span>
                    </div>
                </div>

                {{-- Cards de resultado --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                    <div class="imp-result-card">
                        <div class="imp-result-icon" style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: #4f46e5;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="imp-result-label">Convertido</div>
                            <div class="imp-result-value" x-text="'R$ ' + fmt(valueInReais)"></div>
                        </div>
                    </div>
                    <div class="imp-result-card">
                        <div class="imp-result-icon" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); color: #d97706;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="imp-result-label">Transporte <span x-text="'(' + (transportRate || '0') + '%)'"></span></div>
                            <div class="imp-result-value" style="color: #d97706;" x-text="'R$ ' + fmt(transportValue)"></div>
                        </div>
                    </div>
                </div>

                {{-- Custo total --}}
                <div class="imp-total">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="imp-total-icon">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.6); font-weight: 600;">Custo Total</div>
                            <div style="font-size: 26px; font-weight: 900; color: white; letter-spacing: -0.03em; line-height: 1.1;" x-text="'R$ ' + fmt(totalCost)"></div>
                        </div>
                    </div>
                    <button @click="copySummary()" type="button" class="imp-copy-total-btn"
                            :class="{ 'imp-copy-total-done': copied }">
                        <svg x-show="!copied" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <svg x-show="copied" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </button>
                </div>

                {{-- Detalhamento --}}
                <div class="imp-breakdown">
                    <div class="imp-breakdown-row">
                        <span>Valor convertido</span>
                        <span x-text="'R$ ' + fmt(valueInReais)"></span>
                    </div>
                    <div class="imp-breakdown-row" style="color: #d97706;">
                        <span x-text="'+ Transporte (' + (transportRate || '0') + '%)'"></span>
                        <span x-text="'R$ ' + fmt(transportValue)"></span>
                    </div>
                    <div class="imp-breakdown-divider"></div>
                    <div class="imp-breakdown-row imp-breakdown-total">
                        <span>Custo total</span>
                        <span x-text="'R$ ' + fmt(totalCost)"></span>
                    </div>
                </div>
            </div>

            {{-- Estado vazio --}}
            <div x-show="valueInReais <= 0" style="text-align: center; padding: 32px 16px;">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #eef2ff, #e0e7ff); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                    <svg style="width: 24px; height: 24px; color: #6366f1;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p style="font-size: 13px; color: #94a3b8; font-weight: 500;">Informe o valor do produto em dólar</p>
            </div>
        </div>

        {{-- Toast --}}
        <div x-show="toast" x-transition.opacity.duration.200ms x-cloak class="imp-toast">
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <span x-text="toast"></span>
        </div>
    </div>

    <style>
        @keyframes impPulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(37, 99, 235, 0.4); }
            50% { box-shadow: 0 8px 40px rgba(37, 99, 235, 0.6); }
        }
        .imp-fab-pulse { animation: impPulse 3s ease-in-out infinite; }

        .imp-panel {
            position: fixed;
            z-index: 50;
            bottom: 90px;
            left: 24px;
            width: 370px;
            border-radius: 20px;
            max-height: calc(100vh - 110px);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
        }

        .imp-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
        }

        .imp-header-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .imp-header-btn {
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
        .imp-header-btn:hover { background: rgba(255, 255, 255, 0.3); }

        .imp-body {
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }
        .imp-body::-webkit-scrollbar { width: 4px; }
        .imp-body::-webkit-scrollbar-track { background: transparent; }
        .imp-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .imp-rate-badge {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .imp-input-group { margin-bottom: 0; }

        .imp-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 5px;
            display: block;
        }

        .imp-input-wrap {
            position: relative;
            margin-top: 4px;
        }

        .imp-currency {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            pointer-events: none;
        }

        .imp-input {
            width: 100%;
            border: none;
            outline: none;
            font-family: inherit;
            text-align: right;
            transition: all 0.2s ease;
        }

        .imp-input-primary {
            padding: 14px 14px 14px 36px;
            border-radius: 12px;
            font-size: 22px;
            font-weight: 800;
            color: #1e293b;
            background: #f8fafc;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.06);
            letter-spacing: -0.02em;
        }
        .imp-input-primary:focus {
            background: #141414;
            box-shadow: inset 0 0 0 2px #2563eb, 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .imp-input-amber {
            padding: 10px 10px 10px 32px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            color: #d97706;
            background: #141414beb;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
        }
        .imp-input-amber:focus {
            background: #141414;
            box-shadow: inset 0 0 0 2px #f59e0b, 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .imp-input-wrap-amber {
            border-radius: 10px;
            background: #141414beb;
        }

        .imp-formula {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 12px;
            margin-bottom: 12px;
            background: #f8fafc;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            flex-wrap: wrap;
        }

        .imp-formula-item { text-align: center; }
        .imp-formula-label {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 600;
        }
        .imp-formula-value {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
        }
        .imp-formula-highlight {
            background: rgba(99,102,241,0.1);
            padding: 4px 10px;
            border-radius: 8px;
        }
        .imp-formula-op {
            font-size: 14px;
            font-weight: 600;
            color: #cbd5e1;
        }

        .imp-result-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #141414;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            transition: all 0.15s;
        }

        .imp-result-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .imp-result-label {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .imp-result-value {
            font-size: 15px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.02em;
        }

        .imp-total {
            background: linear-gradient(135deg, #1e293b, #334155);
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .imp-total-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(74, 222, 128, 0.15);
            color: #4ade80;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .imp-copy-total-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            flex-shrink: 0;
        }
        .imp-copy-total-btn:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .imp-copy-total-done {
            background: #10b981 !important;
            border-color: #10b981 !important;
            color: white !important;
        }

        .imp-breakdown {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 12px;
        }

        .imp-breakdown-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            color: #64748b;
            font-weight: 500;
        }

        .imp-breakdown-divider {
            border-top: 1px dashed #e2e8f0;
            margin: 6px 0;
        }

        .imp-breakdown-total {
            color: #1e293b !important;
            font-weight: 800 !important;
            font-size: 13px !important;
        }

        .imp-toast {
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
            .imp-panel {
                bottom: 0 !important;
                left: 0 !important;
                right: 0;
                width: auto !important;
                border-radius: 20px 20px 0 0 !important;
                max-height: 85dvh !important;
                box-shadow: 0 -10px 60px rgba(0,0,0,0.15) !important;
            }
        }
    </style>
</div>

@pushOnce('scripts')
<script>
function importCalc(serverRate) {
    return {
        open: false,
        copied: false,
        toast: '',
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

        clearAll() {
            this.dollarValue = '';
            this.transportRate = '';
            this.valueInReais = 0;
            this.transportValue = 0;
            this.totalCost = 0;
        },

        calculate() {
            const dollar = this.parseNum(this.dollarValue);
            const transport = this.parseNum(this.transportRate);

            this.valueInReais = dollar * this.dollarRate;
            this.transportValue = this.valueInReais * (transport / 100);
            this.totalCost = this.valueInReais + this.transportValue;

            localStorage.setItem('dgstore_import_calc', JSON.stringify({
                transportRate: this.transportRate
            }));
        },

        copySummary() {
            var self = this;
            var lines = [
                '*Cálculo de Importação*',
                '',
                'Valor: US$ ' + (this.dollarValue || '0'),
                'Cotação: R$ ' + this.fmt(this.dollarRate),
                'Convertido: R$ ' + this.fmt(this.valueInReais),
                '',
                'Transporte: ' + (this.transportRate || '0') + '%',
                'Valor transporte: R$ ' + this.fmt(this.transportValue),
                '',
                'CUSTO TOTAL: R$ ' + this.fmt(this.totalCost)
            ];
            navigator.clipboard.writeText(lines.join('\n')).then(function() {
                self.copied = true;
                self.showToast('Resumo copiado!');
                setTimeout(function() { self.copied = false; }, 2500);
            }).catch(function() {
                self.fallbackCopy(lines.join('\n'));
            });
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
            this.showToast('Resumo copiado!');
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 2500);
        },

        showToast(msg) {
            this.toast = msg;
            setTimeout(() => { this.toast = ''; }, 1500);
        }
    };
}
</script>
@endPushOnce
@endif
