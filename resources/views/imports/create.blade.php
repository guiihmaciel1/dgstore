<x-app-layout>
    <div class="py-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('imports.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Novo Pedido de Importação</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Registre um novo pedido de produtos importados</p>
                </div>
            </div>

            <!-- Aviso financeiro -->
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.625rem; margin-bottom: 1.25rem; font-size: 0.8rem; color: #1e40af;">
                <svg style="width: 1.1rem; height: 1.1rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Ao criar o pedido, o valor total será <strong>debitado automaticamente</strong> da carteira padrão no financeiro.</span>
            </div>

            <form method="POST" action="{{ route('imports.store') }}" x-data="importOrderForm()" @submit="submitting = true">
                @csrf

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                    <!-- Coluna Principal -->
                    <div>
                        <!-- Informações do Pedido -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Informações do Pedido</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Fornecedor</label>
                                        <select name="supplier_id" style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                            <option value="">Selecione (opcional)</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Data do Pedido *</label>
                                        <input type="date" name="ordered_at" value="{{ old('ordered_at', date('Y-m-d')) }}" required
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Código de Rastreio</label>
                                        <input type="text" name="tracking_code" value="{{ old('tracking_code') }}" placeholder="Ex: LX123456789CN"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Previsão de Chegada</label>
                                        <input type="date" name="estimated_arrival" value="{{ old('estimated_arrival') }}"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                    </div>
                                </div>

                                <div style="margin-top: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Observações</label>
                                    <textarea name="notes" rows="2" placeholder="Notas adicionais sobre o pedido..."
                                              style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Itens do Pedido -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="font-weight: 600; color: #111827;">Itens do Pedido</h3>
                                <button type="button" @click="addItem()"
                                        style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; background: #111827; color: white; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; border: none; cursor: pointer;">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Adicionar Item
                                </button>
                            </div>
                            <div style="padding: 1.25rem;">
                                <template x-for="(item, index) in items" :key="index">
                                    <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem; margin-bottom: 0.75rem; border: 1px solid #e5e7eb;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                            <span style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;" x-text="'Item ' + (index + 1)"></span>
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                                    style="padding: 0.25rem; color: #dc2626; border-radius: 0.25rem; border: none; background: none; cursor: pointer;"
                                                    title="Remover item">
                                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Descrição com autocomplete -->
                                        <div style="margin-bottom: 0.75rem; position: relative;">
                                            <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Produto / Descrição *</label>
                                            <input type="text" x-model="item.description" :name="'items[' + index + '][description]'" required
                                                   placeholder="Digite para buscar em cotações e produtos..."
                                                   @input.debounce.300ms="searchItem(index)"
                                                   @focus="item.showSuggestions && item.suggestions.length > 0 ? item.showSuggestions = true : null"
                                                   @click.away="item.showSuggestions = false"
                                                   autocomplete="off"
                                                   style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">

                                            <!-- Dropdown de sugestões -->
                                            <div x-show="item.showSuggestions && item.suggestions.length > 0" x-cloak
                                                 style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e5e7eb; border-radius: 0.375rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 50; max-height: 200px; overflow-y: auto; margin-top: 2px;">
                                                <template x-for="(sug, si) in item.suggestions" :key="si">
                                                    <button type="button" @click="selectSuggestion(index, sug)"
                                                            style="width: 100%; padding: 0.5rem 0.75rem; text-align: left; border: none; background: none; cursor: pointer; border-bottom: 1px solid #f3f4f6; font-size: 0.8125rem;"
                                                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='none'">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <span x-text="sug.name" style="font-weight: 500; color: #111827;"></span>
                                                            <span x-show="sug.source === 'quotation'" style="font-size: 0.6875rem; padding: 0.0625rem 0.375rem; background: #dbeafe; color: #1d4ed8; border-radius: 9999px; font-weight: 600;">Cotação</span>
                                                            <span x-show="sug.source === 'product'" style="font-size: 0.6875rem; padding: 0.0625rem 0.375rem; background: #f3f4f6; color: #6b7280; border-radius: 9999px; font-weight: 600;">Produto</span>
                                                        </div>
                                                        <div x-show="sug.price_usd" style="font-size: 0.75rem; color: #16a34a; margin-top: 0.125rem;">
                                                            <span x-text="'US$ ' + (sug.price_usd ? sug.price_usd.toFixed(2).replace('.', ',') : '')"></span>
                                                            <span x-show="sug.supplier" x-text="' — ' + sug.supplier" style="color: #6b7280;"></span>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Grid de valores -->
                                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                            <div>
                                                <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Quantidade *</label>
                                                <input type="number" x-model.number="item.quantity" :name="'items[' + index + '][quantity]'" required min="1"
                                                       @input="calculateTotals()"
                                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                                                    Cotação (USD)
                                                    <span style="font-size: 0.625rem; color: #9ca3af;" title="Preço da última cotação cadastrada">?</span>
                                                </label>
                                                <input type="text" :value="item.quoted_price ? ('$ ' + item.quoted_price.toFixed(2).replace('.', ',')) : '—'" readonly
                                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; background: #f9fafb; color: #6b7280;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                                                    Custo Pago (USD) *
                                                    <span style="font-size: 0.625rem; color: #9ca3af;" title="Valor que você realmente pagou">?</span>
                                                </label>
                                                <input type="number" x-model.number="item.unit_cost" :name="'items[' + index + '][unit_cost]'" required min="0.01" step="0.01"
                                                       @input="calculateTotals()"
                                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                            </div>
                                        </div>

                                        <!-- Subtotal do item -->
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid #e5e7eb;">
                                            <!-- Desconto badge -->
                                            <div>
                                                <template x-if="item.quoted_price && item.unit_cost > 0 && item.unit_cost < item.quoted_price">
                                                    <span style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; background: #dcfce7; color: #16a34a; border-radius: 9999px; font-weight: 600;"
                                                          x-text="'-' + (((item.quoted_price - item.unit_cost) / item.quoted_price) * 100).toFixed(0) + '% desconto'"></span>
                                                </template>
                                                <template x-if="item.quoted_price && item.unit_cost > item.quoted_price">
                                                    <span style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; background: #fef2f2; color: #dc2626; border-radius: 9999px; font-weight: 600;"
                                                          x-text="'+' + (((item.unit_cost - item.quoted_price) / item.quoted_price) * 100).toFixed(0) + '% acima'"></span>
                                                </template>
                                            </div>
                                            <div style="text-align: right; font-size: 0.8125rem; color: #6b7280;">
                                                Subtotal: <span style="font-weight: 600; color: #111827;" x-text="'$ ' + (item.quantity * item.unit_cost).toFixed(2).replace('.', ',')"></span>
                                                <span style="color: #9ca3af; margin-left: 0.375rem;" x-text="'(R$ ' + (item.quantity * item.unit_cost * exchangeRate).toFixed(2).replace('.', ',') + ')'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="items.length === 0" style="padding: 2rem; text-align: center; color: #6b7280;">
                                    Adicione pelo menos um item ao pedido.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Lateral - Custos -->
                    <div>
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; position: sticky; top: 1rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Custos e Conversão</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <!-- Cotação USD/BRL -->
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Cotação USD/BRL *</label>
                                    <input type="number" name="exchange_rate" x-model.number="exchangeRate" required min="0.01" step="0.0001"
                                           @input="calculateTotals()"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                    <div style="margin-top: 0.25rem; font-size: 0.6875rem; color: #6b7280;" x-show="dailyRate > 0">
                                        Cotação do dia: R$ <span x-text="dailyRate.toFixed(4).replace('.', ',')"></span>
                                        <button type="button" @click="exchangeRate = dailyRate; calculateTotals()" style="color: #2563eb; text-decoration: underline; border: none; background: none; cursor: pointer; font-size: 0.6875rem;">usar</button>
                                    </div>
                                </div>

                                <!-- Frete com toggle % / R$ -->
                                <div style="margin-bottom: 1rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                                        <label style="font-size: 0.875rem; font-weight: 500; color: #374151;">Frete</label>
                                        <div style="display: flex; background: #f3f4f6; border-radius: 0.375rem; overflow: hidden; border: 1px solid #e5e7eb;">
                                            <button type="button" @click="shippingMode = 'value'; calculateTotals()"
                                                    :style="shippingMode === 'value' ? 'background: #111827; color: white;' : 'background: transparent; color: #6b7280;'"
                                                    style="padding: 0.1875rem 0.5rem; font-size: 0.6875rem; font-weight: 600; border: none; cursor: pointer;">R$</button>
                                            <button type="button" @click="shippingMode = 'percent'; calculateTotals()"
                                                    :style="shippingMode === 'percent' ? 'background: #111827; color: white;' : 'background: transparent; color: #6b7280;'"
                                                    style="padding: 0.1875rem 0.5rem; font-size: 0.6875rem; font-weight: 600; border: none; cursor: pointer;">%</button>
                                        </div>
                                    </div>
                                    <div style="position: relative;">
                                        <input type="number" x-model.number="shippingInput" min="0" step="0.01"
                                               @input="calculateTotals()"
                                               :placeholder="shippingMode === 'value' ? '0,00' : '0'"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                        <span x-show="shippingMode === 'percent'" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #9ca3af;">% do subtotal</span>
                                    </div>
                                    <input type="hidden" name="shipping_cost" :value="shippingCostFinal.toFixed(2)">
                                    <div x-show="shippingMode === 'percent' && shippingCostFinal > 0" style="margin-top: 0.25rem; font-size: 0.6875rem; color: #6b7280;">
                                        = R$ <span x-text="shippingCostFinal.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                </div>

                                <!-- Impostos/Taxas/TAXI -->
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Impostos / Taxas / TAXI (R$)
                                        <span style="font-size: 0.625rem; color: #9ca3af;" title="Inclua impostos de importação, taxas alfandegárias e custos de transporte local (TAXI)">?</span>
                                    </label>
                                    <input type="number" name="taxes" x-model.number="taxes" min="0" step="0.01"
                                           @input="calculateTotals()"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>

                                <!-- Resumo -->
                                <div style="padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                                        <span style="color: #6b7280;">Subtotal (USD):</span>
                                        <span style="font-weight: 500;" x-text="'$ ' + itemsTotal.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                                        <span style="color: #6b7280;">Convertido (BRL):</span>
                                        <span style="font-weight: 500;" x-text="'R$ ' + (itemsTotal * exchangeRate).toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                                        <span style="color: #6b7280;">+ Frete:</span>
                                        <span style="font-weight: 500;" x-text="'R$ ' + shippingCostFinal.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 0.8125rem;">
                                        <span style="color: #6b7280;">+ Impostos/Taxas/TAXI:</span>
                                        <span style="font-weight: 500;" x-text="'R$ ' + taxes.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 2px solid #111827;">
                                        <span style="font-weight: 700; color: #111827;">Total Estimado:</span>
                                        <span style="font-weight: 700; color: #111827; font-size: 1.125rem;" x-text="'R$ ' + totalBrl.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="text-align: right; font-size: 0.6875rem; color: #6b7280; margin-top: 0.25rem;" x-show="items.length > 0 && totalBrl > 0">
                                        Custo médio/unid: <span x-text="'R$ ' + (totalBrl / items.reduce((s,i) => s + (i.quantity || 0), 0) || 0).toFixed(2).replace('.', ',')"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            <button type="submit" :disabled="items.length === 0 || submitting"
                                    class="btn-criar-pedido"
                                    :class="{ 'btn-disabled': items.length === 0 || submitting }">
                                <template x-if="!submitting">
                                    <svg style="width: 1.125rem; height: 1.125rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                                <template x-if="submitting">
                                    <svg class="btn-spinner" style="width: 1.125rem; height: 1.125rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </template>
                                <span x-text="submitting ? 'Criando pedido...' : 'Criar Pedido e Lançar no Financeiro'"></span>
                            </button>
                            <a href="{{ route('imports.index') }}" class="btn-cancelar">
                                <svg style="width: 0.875rem; height: 0.875rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function importOrderForm() {
            return {
                items: [{ description: '', quantity: 1, unit_cost: 0, quoted_price: null, suggestions: [], showSuggestions: false }],
                exchangeRate: {{ old('exchange_rate', 5.50) }},
                dailyRate: 0,
                shippingMode: 'value',
                shippingInput: {{ old('shipping_cost', 0) }},
                shippingCostFinal: 0,
                taxes: {{ old('taxes', 0) }},
                itemsTotal: 0,
                totalBrl: 0,
                submitting: false,
                searchTimeout: null,

                init() {
                    this.fetchDailyRate();
                    this.calculateTotals();
                },

                async fetchDailyRate() {
                    try {
                        const res = await fetch('https://economia.awesomeapi.com.br/last/USD-BRL');
                        const data = await res.json();
                        if (data.USDBRL) {
                            this.dailyRate = parseFloat(data.USDBRL.bid);
                            if (this.exchangeRate === 5.50) {
                                this.exchangeRate = Math.round(this.dailyRate * 100) / 100;
                                this.calculateTotals();
                            }
                        }
                    } catch (e) {
                        // Falha silenciosa - usuário pode digitar manualmente
                    }
                },

                addItem() {
                    this.items.push({ description: '', quantity: 1, unit_cost: 0, quoted_price: null, suggestions: [], showSuggestions: false });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                },

                async searchItem(index) {
                    const item = this.items[index];
                    const q = item.description.trim();

                    if (q.length < 2) {
                        item.suggestions = [];
                        item.showSuggestions = false;
                        return;
                    }

                    try {
                        const res = await fetch(`{{ route('imports.items.search') }}?q=${encodeURIComponent(q)}`);
                        const data = await res.json();
                        item.suggestions = data;
                        item.showSuggestions = data.length > 0;
                    } catch (e) {
                        item.suggestions = [];
                    }
                },

                selectSuggestion(index, sug) {
                    const item = this.items[index];
                    item.description = sug.name;
                    item.showSuggestions = false;
                    item.suggestions = [];

                    if (sug.price_usd) {
                        item.quoted_price = sug.price_usd;
                        if (item.unit_cost === 0) {
                            item.unit_cost = sug.price_usd;
                        }
                    }

                    this.calculateTotals();
                },

                calculateTotals() {
                    this.itemsTotal = this.items.reduce((sum, item) => {
                        return sum + ((item.quantity || 0) * (item.unit_cost || 0));
                    }, 0);

                    const subtotalBrl = this.itemsTotal * this.exchangeRate;

                    if (this.shippingMode === 'percent') {
                        this.shippingCostFinal = subtotalBrl * ((this.shippingInput || 0) / 100);
                    } else {
                        this.shippingCostFinal = this.shippingInput || 0;
                    }

                    this.totalBrl = subtotalBrl + this.shippingCostFinal + (this.taxes || 0);
                }
            };
        }
    </script>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }

        .btn-criar-pedido {
            width: 100%;
            padding: 0.8rem 1rem;
            background: #16a34a;
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 0.625rem;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.15s;
            line-height: 1.4;
        }
        .btn-criar-pedido:hover:not(.btn-disabled) {
            background: #15803d;
        }
        .btn-criar-pedido.btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .btn-spinner {
            animation: spin 1s linear infinite;
        }

        .btn-cancelar {
            width: 100%;
            padding: 0.7rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            color: #6b7280;
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
            border: 1px solid #e5e7eb;
            border-radius: 0.625rem;
            transition: all 0.15s;
            background: transparent;
            line-height: 1.4;
        }
        .btn-cancelar:hover {
            border-color: #d1d5db;
            background: #f9fafb;
            color: #374151;
        }

        @media (max-width: 768px) {
            div[style*="grid-template-columns: 2fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: 1fr 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
