<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="importQuotation()">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('quotations.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Importar Cotações</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Cole a lista do fornecedor e importe automaticamente</p>
                </div>
            </div>

            @if($errors->any())
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b;">
                    <ul style="list-style: disc; padding-left: 1.5rem; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Etapa 1: Configuração -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f0f9ff;">
                    <h2 style="font-size: 1rem; font-weight: 600; color: #0c4a6e; display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        1. Configurar Importação
                    </h2>
                </div>
                <div style="padding: 1.5rem;">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Fornecedor -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                Fornecedor <span style="color: #dc2626;">*</span>
                            </label>
                            <select x-model="supplierId" required
                                    style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white; outline: none;"
                                    onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                <option value="">Selecione um fornecedor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Data -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                Data da Cotação <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="date" x-model="quotedAt" required
                                   style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>

                        <!-- Taxa de Câmbio -->
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                Cotação do Dólar (R$) <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" x-model="exchangeRateInput" @input="updateExchangeRate()" required
                                   placeholder="5,45"
                                   style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Etapa 2: Colar Texto -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f0f9ff;">
                    <h2 style="font-size: 1rem; font-weight: 600; color: #0c4a6e; display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        2. Colar Lista do Fornecedor
                    </h2>
                </div>
                <div style="padding: 1.5rem;">
                    <textarea x-model="rawText" rows="12"
                              placeholder="Cole aqui o texto da cotação do fornecedor (formato WhatsApp)...

Exemplo:
🍏🍎*IPHONE LACRADO*🍏🍎

*15 128GB IN*
BLACK *$610*
PINK *$610*
GREEN *$605* 1pc"
                              style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.8125rem; font-family: monospace; outline: none; resize: vertical; line-height: 1.6;"
                              onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"></textarea>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                        <!-- Toggle IA -->
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none;">
                            <input type="checkbox" x-model="forceAi"
                                   style="width: 0.875rem; height: 0.875rem; accent-color: #111827; cursor: pointer;">
                            <span style="font-size: 0.8125rem; color: #6b7280;">
                                Forçar análise via IA
                            </span>
                        </label>

                        <button type="button" @click="analyzeText()"
                                :disabled="loading || !rawText || !supplierId || !exchangeRate"
                                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; transition: all 0.15s;"
                                :style="loading || !rawText || !supplierId || !exchangeRate
                                    ? 'background: #e5e7eb; color: #9ca3af; cursor: not-allowed;'
                                    : 'background: #111827; color: white;'"
                                onmouseover="if(!this.disabled) this.style.background='#374151'"
                                onmouseout="if(!this.disabled) this.style.background='#111827'">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="loading ? 'animation: spin 1s linear infinite' : ''">
                                <template x-if="!loading">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </template>
                                <template x-if="loading">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </template>
                            </svg>
                            <span x-text="loading ? 'Analisando...' : 'Analisar Texto'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mensagem de erro/info -->
            <template x-if="message">
                <div :style="'margin-bottom: 1rem; padding: 1rem; border-radius: 0.5rem; font-size: 0.875rem; border: 1px solid; ' +
                    (messageType === 'error' ? 'background: #fef2f2; border-color: #fecaca; color: #991b1b;' : 'background: #ecfdf5; border-color: #a7f3d0; color: #065f46;')">
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span x-text="message"></span>
                        <template x-if="parserUsed && messageType !== 'error'">
                            <span style="padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;"
                                  x-text="parserUsed === 'ai' ? 'via IA' : 'via Regex'"></span>
                        </template>
                    </div>
                    <template x-if="isFallback && parserUsed === 'ai' && messageType !== 'error'">
                        <div style="margin-top: 0.5rem; font-size: 0.8125rem; color: #6b7280;">
                            Formato não reconhecido automaticamente. A IA extraiu os dados — revise com atenção.
                        </div>
                    </template>
                </div>
            </template>

            <!-- Etapa 3: Preview e Confirmação -->
            <template x-if="items.length > 0">
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #ecfdf5;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h2 style="font-size: 1rem; font-weight: 600; color: #065f46; display: flex; align-items: center; gap: 0.5rem;">
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                3. Revisar e Importar
                                <span style="font-weight: 400; color: #6b7280;" x-text="'(' + selectedCount + ' de ' + items.length + ' selecionados)'"></span>
                            </h2>
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="button" @click="selectAll()"
                                        style="padding: 0.375rem 0.75rem; font-size: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: white; cursor: pointer; color: #374151;">
                                    Selecionar Todos
                                </button>
                                <button type="button" @click="deselectAll()"
                                        style="padding: 0.375rem 0.75rem; font-size: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: white; cursor: pointer; color: #374151;">
                                    Desmarcar Todos
                                </button>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('quotations.import-store') }}">
                        @csrf
                        <input type="hidden" name="supplier_id" :value="supplierId">
                        <input type="hidden" name="quoted_at" :value="quotedAt">
                        <input type="hidden" name="exchange_rate" :value="exchangeRate">

                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 3rem;"></th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Categoria</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">USD</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">BRL</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #16a34a; text-transform: uppercase;">Final (+4%)</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr style="border-bottom: 1px solid #f3f4f6;"
                                            :style="item.selected ? '' : 'opacity: 0.4; background: #f9fafb;'">
                                            <td style="padding: 0.5rem 1rem; text-align: center;">
                                                <input type="checkbox" :checked="item.selected" @change="item.selected = $event.target.checked"
                                                       style="width: 1rem; height: 1rem; cursor: pointer; accent-color: #111827;">
                                                <input type="hidden" :name="'items[' + index + '][selected]'" :value="item.selected ? 1 : 0">
                                            </td>
                                            <td style="padding: 0.5rem 1rem;">
                                                <span style="font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #f3f4f6; color: #6b7280;" x-text="item.category || '-'"></span>
                                                <input type="hidden" :name="'items[' + index + '][category]'" :value="item.category">
                                            </td>
                                            <td style="padding: 0.5rem 1rem;">
                                                <div style="display: flex; align-items: center; gap: 0.375rem;">
                                                    <span x-html="getFlag(item.product_name)" style="flex-shrink: 0; line-height: 0;"></span>
                                                    <input type="text" x-model="item.product_name"
                                                           :name="'items[' + index + '][product_name]'"
                                                           style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; outline: none; min-width: 200px;"
                                                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                                </div>
                                            </td>
                                            <td style="padding: 0.5rem 1rem; text-align: right;">
                                                <div style="display: flex; align-items: center; gap: 0.25rem; justify-content: flex-end;">
                                                    <span style="font-size: 0.8125rem; color: #6b7280;">US$</span>
                                                    <input type="number" x-model.number="item.price_usd" step="0.01" min="0.01"
                                                           :name="'items[' + index + '][price_usd]'"
                                                           style="width: 5rem; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; text-align: right; outline: none;"
                                                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                                </div>
                                            </td>
                                            <td style="padding: 0.5rem 1rem; text-align: right; font-weight: 600; color: #16a34a; font-size: 0.875rem;">
                                                <span x-text="formatBrl(item.price_usd * exchangeRate)"></span>
                                            </td>
                                            <td style="padding: 0.5rem 1rem; text-align: right; font-size: 0.875rem;">
                                                <div style="font-weight: 700; color: #16a34a;" x-text="formatBrl(item.price_usd * exchangeRate * 1.04)"></div>
                                                <div style="font-size: 0.6875rem; color: #ca8a04;" x-text="'+' + formatBrl(item.price_usd * exchangeRate * 0.04)"></div>
                                            </td>
                                            <td style="padding: 0.5rem 1rem; text-align: center;">
                                                <input type="number" x-model.number="item.quantity" min="1" step="1"
                                                       :name="'items[' + index + '][quantity]'"
                                                       style="width: 3.5rem; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; text-align: center; outline: none;"
                                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f9fafb; border-top: 2px solid #e5e7eb;">
                                        <td colspan="3" style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; color: #374151;">
                                            Total selecionados:
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: #111827; font-size: 0.875rem;">
                                            <span x-text="'US$ ' + formatNumber(totalUsd)"></span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: #16a34a; font-size: 0.875rem;">
                                            <span x-text="formatBrl(totalBrl)"></span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem;">
                                            <div style="font-weight: 700; color: #16a34a;" x-text="formatBrl(totalBrl * 1.04)"></div>
                                            <div style="font-size: 0.6875rem; color: #ca8a04;" x-text="'+' + formatBrl(totalBrl * 0.04)"></div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: #374151; font-size: 0.875rem;">
                                            <span x-text="totalQty"></span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Botão Salvar -->
                        <div style="padding: 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <p style="font-size: 0.8125rem; color: #6b7280;">
                                Taxa: <strong x-text="'R$ ' + exchangeRateInput"></strong> |
                                <span x-text="selectedCount"></span> itens selecionados
                            </p>
                            <button type="submit" :disabled="selectedCount === 0"
                                    style="padding: 0.75rem 2rem; border-radius: 0.75rem; font-size: 0.9375rem; font-weight: 700; border: none; cursor: pointer; letter-spacing: 0.025em; transition: all 0.2s;"
                                    :style="selectedCount === 0
                                        ? 'background: #e5e7eb; color: #9ca3af; cursor: not-allowed;'
                                        : 'background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; box-shadow: 0 2px 8px rgba(22,163,106,0.35);'"
                                    onmouseover="if(!this.disabled) { this.style.boxShadow='0 6px 20px rgba(22,163,106,0.45)'; this.style.transform='translateY(-2px)'; }"
                                    onmouseout="this.style.boxShadow='0 2px 8px rgba(22,163,106,0.35)'; this.style.transform='none';">
                                Importar Cotações
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </div>

    @push('scripts')
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
    <script>
        function importQuotation() {
            return {
                supplierId: '',
                quotedAt: new Date().toISOString().split('T')[0],
                exchangeRateInput: '5,45',
                exchangeRate: 5.45,
                rawText: '',
                items: [],
                loading: false,
                message: '',
                messageType: '',
                forceAi: false,
                parserUsed: '',
                isFallback: false,

                updateExchangeRate() {
                    const parsed = parseFloat(this.exchangeRateInput.replace(',', '.'));
                    this.exchangeRate = isNaN(parsed) ? 0 : parsed;
                    // Recalcular BRL de todos os itens quando taxa muda
                },

                async analyzeText() {
                    if (!this.rawText || !this.supplierId || !this.exchangeRate) {
                        this.message = 'Preencha fornecedor, data e cotação do dólar antes de analisar.';
                        this.messageType = 'error';
                        return;
                    }

                    this.loading = true;
                    this.message = '';
                    this.items = [];
                    this.parserUsed = '';
                    this.isFallback = false;

                    try {
                        const payload = { raw_text: this.rawText };
                        if (this.forceAi) {
                            payload.force_ai = true;
                        }

                        const response = await fetch('{{ route("quotations.import-preview") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        this.parserUsed = data.parser_used || '';
                        this.isFallback = data.is_fallback || false;

                        if (data.success) {
                            this.items = data.items.map(item => ({
                                ...item,
                                selected: true,
                            }));
                            this.message = data.message;
                            this.messageType = 'success';
                        } else {
                            this.message = data.message || 'Nenhum item encontrado no texto.';
                            this.messageType = 'error';
                        }
                    } catch (error) {
                        this.message = 'Erro ao analisar o texto. Tente novamente.';
                        this.messageType = 'error';
                        console.error(error);
                    } finally {
                        this.loading = false;
                    }
                },

                get selectedCount() {
                    return this.items.filter(i => i.selected).length;
                },

                get totalUsd() {
                    return this.items.filter(i => i.selected).reduce((sum, i) => sum + (i.price_usd * i.quantity), 0);
                },

                get totalBrl() {
                    return this.totalUsd * this.exchangeRate;
                },

                get totalQty() {
                    return this.items.filter(i => i.selected).reduce((sum, i) => sum + i.quantity, 0);
                },

                selectAll() {
                    this.items.forEach(i => i.selected = true);
                },

                deselectAll() {
                    this.items.forEach(i => i.selected = false);
                },

                getFlag(productName) {
                    if (!productName) return '';
                    const name = productName.toUpperCase();
                    const usFlag = '<svg style="width:1.125rem;height:0.75rem;display:inline-block;vertical-align:middle;border-radius:2px" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg"><rect width="36" height="24" fill="#B22234"/><rect y="1.85" width="36" height="1.85" fill="#fff"/><rect y="5.54" width="36" height="1.85" fill="#fff"/><rect y="9.23" width="36" height="1.85" fill="#fff"/><rect y="12.92" width="36" height="1.85" fill="#fff"/><rect y="16.62" width="36" height="1.85" fill="#fff"/><rect y="20.31" width="36" height="1.85" fill="#fff"/><rect width="14.4" height="12.92" fill="#3C3B6E"/><text x="7.2" y="8" text-anchor="middle" fill="#fff" font-size="5" font-family="sans-serif">★</text></svg>';
                    const jpFlag = '<svg style="width:1.125rem;height:0.75rem;display:inline-block;vertical-align:middle;border-radius:2px" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg"><rect width="36" height="24" fill="#fff" stroke="#e5e7eb" stroke-width="0.5"/><circle cx="18" cy="12" r="7.2" fill="#BC002D"/></svg>';
                    if (/\bUSA\b/.test(name) || /\bLL\b/.test(name) || /[A-Z0-9]LL\b/.test(name)) {
                        return usFlag;
                    }
                    if (/\bJP\b/.test(name) || /\s+J\s*$/.test(name) || /\s+J\s+-/.test(name)) {
                        return jpFlag;
                    }
                    return '';
                },

                formatBrl(value) {
                    return 'R$ ' + value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },

                formatNumber(value) {
                    return value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
