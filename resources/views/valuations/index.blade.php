<x-app-layout>
    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="valuationApp()" x-init="init()">

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Avaliador de Seminovos</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Avalie iPhones usados com base nos preços de mercado</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">

                <!-- Card: Checklist de Avaliação -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <h2 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Checklist do Aparelho
                    </h2>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <!-- Modelo -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Modelo</label>
                            <select x-model="form.model_id" @change="onModelChange()"
                                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; color: #111827; background: white;">
                                <option value="">Selecione o modelo</option>
                                <template x-for="model in models" :key="model.id">
                                    <option :value="model.id" x-text="model.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Capacidade -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Capacidade</label>
                            <select x-model="form.storage" @change="onStorageChange()"
                                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; color: #111827; background: white;"
                                    :disabled="!form.model_id">
                                <option value="">Selecione</option>
                                <template x-for="s in currentStorages" :key="s">
                                    <option :value="s" x-text="s"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Cor -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Cor</label>
                            <select x-model="form.color"
                                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; color: #111827; background: white;"
                                    :disabled="!form.model_id">
                                <option value="">Selecione</option>
                                <template x-for="c in currentColors" :key="c">
                                    <option :value="c" x-text="c"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Saúde da Bateria -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                Saúde da Bateria
                                <span x-show="form.battery_percentage" x-text="'(' + batteryLabel + ')'"
                                      :style="'font-size: 0.75rem; color: ' + batteryColor"></span>
                            </label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="number" x-model.number="form.battery_percentage"
                                       min="0" max="100" placeholder="Ex: 87"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; color: #111827;">
                                <span style="font-size: 0.875rem; color: #6b7280;">%</span>
                            </div>
                        </div>

                        <!-- Estado do aparelho -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Já foi aberto/trocou peça?</label>
                            <select x-model="form.device_state"
                                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; color: #111827; background: white;">
                                <option value="original">Não — Original</option>
                                <option value="repaired">Sim — Já foi reparado</option>
                            </select>
                        </div>

                        <!-- Acessórios -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Possui caixa/cabo?</label>
                            <select x-model="form.accessory_state"
                                    style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; color: #111827; background: white;">
                                <option value="complete">Caixa e cabo</option>
                                <option value="partial">Só caixa ou só cabo</option>
                                <option value="none">Nenhum</option>
                            </select>
                        </div>
                    </div>

                    <!-- Info adicional -->
                    <div style="margin-top: 1rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Informação Adicional</label>
                        <textarea x-model="form.notes" rows="2" placeholder="Riscos, detalhes, observações..."
                                  style="width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; color: #111827; resize: vertical;"></textarea>
                    </div>

                    <!-- Botão Avaliar -->
                    <div style="margin-top: 1.25rem;">
                        <button @click="evaluate()"
                                :disabled="!canEvaluate || loading"
                                :style="'width: 100%; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; border: none; transition: all 0.2s;'
                                    + (canEvaluate && !loading
                                        ? 'background: #111827; color: white; cursor: pointer;'
                                        : 'background: #9ca3af; color: white; cursor: not-allowed; opacity: 0.7;')">
                            <span x-show="!loading">Avaliar Seminovo</span>
                            <span x-show="loading">Avaliando...</span>
                        </button>
                    </div>
                </div>

                <!-- Card: Resultado da Avaliação -->
                <div x-show="result" x-transition
                     style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">

                    <!-- Header do resultado -->
                    <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #111827, #1f2937); color: white;">
                        <h2 style="font-size: 1.125rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Resultado da Avaliação
                        </h2>
                        <p style="font-size: 0.75rem; margin-top: 0.25rem; color: #9ca3af;">
                            <span x-text="result?.model_name"></span> — <span x-text="result?.storage"></span>
                            <template x-if="result?.color"> · <span x-text="result.color"></span></template>
                        </p>
                    </div>

                    <div style="padding: 1.5rem;">
                        <!-- Preço de Mercado -->
                        <div style="margin-bottom: 1.5rem;">
                            <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Preço de Mercado — Novo (Mercado Livre)</h3>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                                <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; text-align: center;">
                                    <p style="font-size: 0.75rem; color: #6b7280;">Mínimo</p>
                                    <p style="font-size: 1rem; font-weight: 700; color: #111827;" x-text="formatCurrency(result?.market_min)"></p>
                                </div>
                                <div style="padding: 0.75rem; background: #eff6ff; border-radius: 0.5rem; text-align: center; border: 1px solid #bfdbfe;">
                                    <p style="font-size: 0.75rem; color: #3b82f6;">Média</p>
                                    <p style="font-size: 1.125rem; font-weight: 700; color: #1d4ed8;" x-text="formatCurrency(result?.market_avg)"></p>
                                </div>
                                <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; text-align: center;">
                                    <p style="font-size: 0.75rem; color: #6b7280;">Máximo</p>
                                    <p style="font-size: 1rem; font-weight: 700; color: #111827;" x-text="formatCurrency(result?.market_max)"></p>
                                </div>
                            </div>
                            <p style="font-size: 0.75rem; color: #9ca3af; text-align: center; margin-top: 0.5rem;">
                                Baseado em <span x-text="result?.sample_count"></span> anúncios
                                <template x-if="result?.data_age_days > 0">
                                    <span> · Dados de <span x-text="result?.data_age_days"></span> dia(s) atrás</span>
                                </template>
                                <template x-if="result?.data_age_days === 0">
                                    <span> · Atualizado hoje</span>
                                </template>
                            </p>
                        </div>

                        <!-- Modificadores -->
                        <div style="margin-bottom: 1.5rem;">
                            <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Ajustes Aplicados</h3>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: #f9fafb; border-radius: 0.375rem;">
                                    <span style="font-size: 0.875rem; color: #374151;">
                                        Bateria <span x-text="result?.battery_percentage + '%'"></span>
                                        (<span x-text="result?.battery_health_label"></span>)
                                    </span>
                                    <span style="font-size: 0.875rem; font-weight: 600;"
                                          :style="'color: ' + modifierColor(result?.modifiers?.battery)"
                                          x-text="formatModifier(result?.modifiers?.battery)"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: #f9fafb; border-radius: 0.375rem;">
                                    <span style="font-size: 0.875rem; color: #374151;" x-text="result?.device_state_label"></span>
                                    <span style="font-size: 0.875rem; font-weight: 600;"
                                          :style="'color: ' + modifierColor(result?.modifiers?.device_state)"
                                          x-text="formatModifier(result?.modifiers?.device_state)"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: #f9fafb; border-radius: 0.375rem;">
                                    <span style="font-size: 0.875rem; color: #374151;" x-text="result?.accessory_state_label"></span>
                                    <span style="font-size: 0.875rem; font-weight: 600;"
                                          :style="'color: ' + modifierColor(result?.modifiers?.accessories)"
                                          x-text="formatModifier(result?.modifiers?.accessories)"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: #fef2f2; border-radius: 0.375rem; border: 1px solid #fecaca;">
                                    <span style="font-size: 0.875rem; color: #991b1b; font-weight: 500;">Margem DG Store</span>
                                    <span style="font-size: 0.875rem; font-weight: 600; color: #dc2626;">-30%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Valor Sugerido -->
                        <div style="padding: 1.25rem; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-radius: 0.75rem; border: 1px solid #a7f3d0; text-align: center; margin-bottom: 1.25rem;">
                            <p style="font-size: 0.75rem; font-weight: 600; color: #065f46; text-transform: uppercase; letter-spacing: 0.05em;">Valor Sugerido de Compra</p>
                            <p style="font-size: 2rem; font-weight: 800; color: #047857; margin-top: 0.25rem;" x-text="formatCurrency(result?.suggested_buy_price)"></p>
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                                Desconto total: <span x-text="Math.round((result?.total_discount || 0) * 100) + '%'"></span> sobre a média de mercado
                            </p>
                        </div>

                        <!-- Botão Copiar -->
                        <button @click="copyEvaluation()"
                                style="width: 100%; padding: 0.75rem 1.5rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            <svg style="width: 1rem; height: 1rem; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span x-text="copied ? 'Copiado!' : 'Copiar Avaliação'"></span>
                        </button>
                    </div>
                </div>

                <!-- Card: Sem dados -->
                <div x-show="noData" x-transition
                     style="background: #fffbeb; border-radius: 0.75rem; border: 1px solid #fde68a; padding: 1.5rem; text-align: center;">
                    <svg style="width: 2.5rem; height: 2.5rem; color: #d97706; margin: 0 auto 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 style="font-size: 1rem; font-weight: 600; color: #92400e;">Sem dados de mercado</h3>
                    <p style="font-size: 0.875rem; color: #a16207; margin-top: 0.5rem;">
                        Ainda não temos dados de preço para este modelo/armazenamento.<br>
                        Adicione preços manualmente abaixo ou aguarde a coleta automática (06:00 diário).
                    </p>
                </div>

                <!-- Card: Entrada Manual de Preços -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <h2 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Adicionar Preço Manual
                    </h2>
                    <p style="font-size: 0.75rem; color: #9ca3af; margin-bottom: 1rem;">Pesquise no Mercado Livre pelo navegador e insira os preços aqui</p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr auto auto; gap: 0.75rem; align-items: end;">
                        <!-- Modelo -->
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Modelo</label>
                            <select x-model="manual.model_id" @change="onManualModelChange()"
                                    style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem; color: #111827; background: white;">
                                <option value="">Selecione</option>
                                <template x-for="model in models" :key="model.id">
                                    <option :value="model.id" x-text="model.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Storage -->
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Capacidade</label>
                            <select x-model="manual.storage"
                                    :disabled="!manual.model_id"
                                    style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem; color: #111827; background: white;">
                                <option value="">Selecione</option>
                                <template x-for="s in manualStorages" :key="s">
                                    <option :value="s" x-text="s"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Preço -->
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Preço (R$)</label>
                            <input type="number" x-model.number="manual.price" min="100" step="50" placeholder="5000"
                                   style="width: 120px; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem; color: #111827;">
                        </div>

                        <!-- Botão -->
                        <div>
                            <button @click="addManualPrice()"
                                    :disabled="!canAddManual || manualLoading"
                                    :style="'padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; border: none; white-space: nowrap; transition: all 0.2s;'
                                        + (canAddManual && !manualLoading
                                            ? 'background: #059669; color: white; cursor: pointer;'
                                            : 'background: #d1d5db; color: #9ca3af; cursor: not-allowed;')">
                                <span x-text="manualLoading ? 'Salvando...' : 'Adicionar'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Feedback -->
                    <div x-show="manualSuccess" x-transition
                         style="margin-top: 0.75rem; padding: 0.5rem 0.75rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.375rem;">
                        <p style="font-size: 0.8125rem; color: #065f46;" x-text="manualSuccess"></p>
                    </div>
                    <div x-show="manualError" x-transition
                         style="margin-top: 0.75rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;">
                        <p style="font-size: 0.8125rem; color: #991b1b;" x-text="manualError"></p>
                    </div>
                </div>

                <!-- Card: Erro -->
                <div x-show="error" x-transition
                     style="background: #fef2f2; border-radius: 0.75rem; border: 1px solid #fecaca; padding: 1rem; text-align: center;">
                    <p style="font-size: 0.875rem; color: #991b1b;" x-text="error"></p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function valuationApp() {
            return {
                models: @json($models),
                form: {
                    model_id: '',
                    storage: '',
                    color: '',
                    battery_percentage: null,
                    device_state: 'original',
                    accessory_state: 'complete',
                    notes: '',
                },
                currentStorages: [],
                currentColors: [],
                result: null,
                noData: false,
                error: null,
                loading: false,
                copied: false,

                // Entrada manual
                manual: { model_id: '', storage: '', price: null },
                manualStorages: [],
                manualLoading: false,
                manualSuccess: null,
                manualError: null,

                init() {},

                get canEvaluate() {
                    return this.form.model_id
                        && this.form.storage
                        && this.form.battery_percentage !== null
                        && this.form.battery_percentage !== ''
                        && this.form.battery_percentage >= 0
                        && this.form.battery_percentage <= 100;
                },

                get batteryLabel() {
                    const pct = this.form.battery_percentage;
                    if (pct === null || pct === '') return '';
                    if (pct >= 90) return 'Excelente';
                    if (pct >= 80) return 'Bom';
                    return 'Regular';
                },

                get batteryColor() {
                    const pct = this.form.battery_percentage;
                    if (pct === null || pct === '') return '#6b7280';
                    if (pct >= 90) return '#059669';
                    if (pct >= 80) return '#d97706';
                    return '#dc2626';
                },

                onModelChange() {
                    const model = this.models.find(m => m.id === this.form.model_id);
                    this.currentStorages = model ? model.storages : [];
                    this.currentColors = model ? model.colors : [];
                    this.form.storage = '';
                    this.form.color = '';
                    this.result = null;
                    this.noData = false;
                    this.error = null;
                },

                onStorageChange() {
                    this.result = null;
                    this.noData = false;
                    this.error = null;
                },

                async evaluate() {
                    if (!this.canEvaluate) return;

                    this.loading = true;
                    this.result = null;
                    this.noData = false;
                    this.error = null;

                    try {
                        const response = await fetch('{{ route("valuations.evaluate") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                iphone_model_id: this.form.model_id,
                                storage: this.form.storage,
                                battery_percentage: this.form.battery_percentage,
                                device_state: this.form.device_state,
                                accessory_state: this.form.accessory_state,
                                color: this.form.color || null,
                                notes: this.form.notes || null,
                            }),
                        });

                        const json = await response.json();

                        if (response.ok && json.success) {
                            this.result = json.data;
                        } else if (response.status === 404) {
                            this.noData = true;
                        } else {
                            this.error = json.message || 'Erro ao avaliar. Tente novamente.';
                        }
                    } catch (e) {
                        this.error = 'Erro de conexão. Tente novamente.';
                    } finally {
                        this.loading = false;
                    }
                },

                // --- Entrada manual ---

                get canAddManual() {
                    return this.manual.model_id && this.manual.storage && this.manual.price >= 100;
                },

                onManualModelChange() {
                    const model = this.models.find(m => m.id === this.manual.model_id);
                    this.manualStorages = model ? model.storages : [];
                    this.manual.storage = '';
                },

                async addManualPrice() {
                    if (!this.canAddManual) return;

                    this.manualLoading = true;
                    this.manualSuccess = null;
                    this.manualError = null;

                    try {
                        const response = await fetch('{{ route("valuations.manual-price") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                iphone_model_id: this.manual.model_id,
                                storage: this.manual.storage,
                                price: this.manual.price,
                            }),
                        });

                        const json = await response.json();

                        if (response.ok && json.success) {
                            const model = this.models.find(m => m.id === this.manual.model_id);
                            this.manualSuccess = `R$ ${this.manual.price.toLocaleString('pt-BR')} adicionado para ${model?.name} ${this.manual.storage}. Médias recalculadas.`;
                            this.manual.price = null;
                            setTimeout(() => { this.manualSuccess = null; }, 4000);
                        } else {
                            this.manualError = json.message || 'Erro ao salvar preço.';
                        }
                    } catch (e) {
                        this.manualError = 'Erro de conexão. Tente novamente.';
                    } finally {
                        this.manualLoading = false;
                    }
                },

                // --- Formatação ---

                formatCurrency(value) {
                    if (!value && value !== 0) return 'R$ 0,00';
                    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                formatModifier(value) {
                    if (!value && value !== 0) return '0%';
                    const pct = Math.round(value * 100);
                    if (pct > 0) return '+' + pct + '%';
                    if (pct < 0) return pct + '%';
                    return '0%';
                },

                modifierColor(value) {
                    if (!value) return '#6b7280';
                    if (value > 0) return '#059669';
                    if (value < 0) return '#dc2626';
                    return '#6b7280';
                },

                async copyEvaluation() {
                    if (!this.result?.message) return;

                    try {
                        await navigator.clipboard.writeText(this.result.message);
                    } catch {
                        // Fallback para ambientes HTTP
                        const textarea = document.createElement('textarea');
                        textarea.value = this.result.message;
                        textarea.style.position = 'fixed';
                        textarea.style.left = '-9999px';
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                    }

                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
