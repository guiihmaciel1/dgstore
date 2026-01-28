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

            <form method="POST" action="{{ route('imports.store') }}" x-data="importOrderForm()">
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
                                        style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer;">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Adicionar Item
                                </button>
                            </div>
                            <div style="padding: 1.25rem;">
                                <template x-for="(item, index) in items" :key="index">
                                    <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem; margin-bottom: 0.75rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                            <span style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;" x-text="'Item ' + (index + 1)"></span>
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                                    style="padding: 0.25rem; color: #dc2626; border-radius: 0.25rem; border: none; background: none; cursor: pointer;">
                                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 0.75rem;">
                                            <div>
                                                <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Descrição *</label>
                                                <input type="text" x-model="item.description" :name="'items[' + index + '][description]'" required
                                                       placeholder="Ex: iPhone 15 Pro Max 256GB"
                                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Quantidade *</label>
                                                <input type="number" x-model.number="item.quantity" :name="'items[' + index + '][quantity]'" required min="1"
                                                       @input="calculateTotals()"
                                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Custo Unit. (USD) *</label>
                                                <input type="number" x-model.number="item.unit_cost" :name="'items[' + index + '][unit_cost]'" required min="0.01" step="0.01"
                                                       @input="calculateTotals()"
                                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                            </div>
                                        </div>
                                        <div style="margin-top: 0.5rem; text-align: right; font-size: 0.875rem; color: #6b7280;">
                                            Subtotal: <span style="font-weight: 600; color: #111827;" x-text="'$ ' + (item.quantity * item.unit_cost).toFixed(2).replace('.', ',')"></span>
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
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Cotação USD/BRL *</label>
                                    <input type="number" name="exchange_rate" x-model.number="exchangeRate" required min="0.01" step="0.0001"
                                           @input="calculateTotals()"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Frete (R$)</label>
                                    <input type="number" name="shipping_cost" x-model.number="shippingCost" min="0" step="0.01"
                                           @input="calculateTotals()"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Impostos/Taxas (R$)</label>
                                    <input type="number" name="taxes" x-model.number="taxes" min="0" step="0.01"
                                           @input="calculateTotals()"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>

                                <div style="padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                        <span style="color: #6b7280;">Subtotal (USD):</span>
                                        <span style="font-weight: 500;" x-text="'$ ' + itemsTotal.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                        <span style="color: #6b7280;">Convertido (BRL):</span>
                                        <span style="font-weight: 500;" x-text="'R$ ' + (itemsTotal * exchangeRate).toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                        <span style="color: #6b7280;">+ Frete:</span>
                                        <span style="font-weight: 500;" x-text="'R$ ' + shippingCost.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 0.875rem;">
                                        <span style="color: #6b7280;">+ Impostos:</span>
                                        <span style="font-weight: 500;" x-text="'R$ ' + taxes.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 2px solid #111827;">
                                        <span style="font-weight: 700; color: #111827;">Total Estimado:</span>
                                        <span style="font-weight: 700; color: #111827; font-size: 1.125rem;" x-text="'R$ ' + totalBrl.toFixed(2).replace('.', ',')"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div style="margin-top: 1rem;">
                            <button type="submit" :disabled="items.length === 0"
                                    style="width: 100%; padding: 0.75rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;"
                                    :style="items.length === 0 ? 'opacity: 0.5; cursor: not-allowed;' : ''"
                                    onmouseover="if(this.getAttribute(':disabled') !== 'true') this.style.background='#374151'" 
                                    onmouseout="this.style.background='#111827'">
                                Criar Pedido
                            </button>
                            <a href="{{ route('imports.index') }}" 
                               style="display: block; width: 100%; padding: 0.75rem; margin-top: 0.5rem; text-align: center; color: #6b7280; font-weight: 500; text-decoration: none;">
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
                items: [{ description: '', quantity: 1, unit_cost: 0 }],
                exchangeRate: {{ old('exchange_rate', 5.50) }},
                shippingCost: {{ old('shipping_cost', 0) }},
                taxes: {{ old('taxes', 0) }},
                itemsTotal: 0,
                totalBrl: 0,

                init() {
                    this.calculateTotals();
                },

                addItem() {
                    this.items.push({ description: '', quantity: 1, unit_cost: 0 });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                },

                calculateTotals() {
                    this.itemsTotal = this.items.reduce((sum, item) => {
                        return sum + (item.quantity * item.unit_cost);
                    }, 0);
                    
                    this.totalBrl = (this.itemsTotal * this.exchangeRate) + this.shippingCost + this.taxes;
                }
            };
        }
    </script>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: 2fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: 2fr 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
