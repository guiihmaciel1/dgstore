<x-app-layout>
    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('reservations.show', $reservation) }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Editar Reserva</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">{{ $reservation->reservation_number }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('reservations.update', $reservation) }}" x-data="editReservationForm()">
                @csrf
                @method('PUT')
                <input type="hidden" name="customer_id" x-model="customerId">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Coluna Principal -->
                    <div>
                        <!-- Cliente -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; margin-bottom: 1.5rem; position: relative;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; border-radius: 0.75rem 0.75rem 0 0;">
                                <h3 style="font-weight: 600; color: #111827;">
                                    <svg style="width: 1.25rem; height: 1.25rem; display: inline; vertical-align: text-bottom; margin-right: 0.375rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Cliente
                                </h3>
                            </div>
                            <div style="padding: 1.25rem; position: relative;">
                                <template x-if="!selectedCustomer">
                                    <div>
                                        <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                               @focus="showCustomerDropdown = true"
                                               placeholder="Digite o nome ou telefone do cliente..."
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; transition: border-color 0.2s;"
                                               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                        <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.375rem;">Mínimo 2 caracteres para buscar</p>
                                    </div>
                                </template>

                                <!-- Dropdown de resultados -->
                                <div x-show="customerResults.length > 0 && !selectedCustomer" x-cloak
                                     @click.outside="customerResults = []"
                                     style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 8px 24px -4px rgba(0,0,0,0.2); max-height: 220px; overflow-y: auto; z-index: 100;">
                                    <template x-for="customer in customerResults" :key="customer.id">
                                        <div @click="selectCustomer(customer)"
                                             style="padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f3f4f6; transition: background 0.15s;"
                                             onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='white'">
                                            <div style="font-weight: 500; color: #111827;" x-text="customer.name"></div>
                                            <div style="font-size: 0.75rem; color: #6b7280;" x-text="customer.phone || 'Sem telefone'"></div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Cliente selecionado -->
                                <div x-show="selectedCustomer" x-cloak style="padding: 0.625rem 0.75rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; min-width: 1.5rem; background: #16a34a; border-radius: 50%; color: white;">
                                            <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                        <div style="min-width: 0;">
                                            <div style="font-weight: 600; font-size: 0.875rem; color: #15803d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="selectedCustomer?.name"></div>
                                            <div style="font-size: 0.75rem; color: #6b7280;" x-text="selectedCustomer?.phone"></div>
                                        </div>
                                    </div>
                                    <button type="button" @click="clearCustomer()" style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; min-width: 1.5rem; color: #dc2626; background: none; border: none; cursor: pointer; border-radius: 0.375rem;"
                                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'" title="Trocar cliente">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Produto -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; border-radius: 0.75rem 0.75rem 0 0;">
                                <h3 style="font-weight: 600; color: #111827;">
                                    <svg style="width: 1.25rem; height: 1.25rem; display: inline; vertical-align: text-bottom; margin-right: 0.375rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Produto Reservado
                                </h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Descrição do Produto <span style="color: #dc2626;">*</span></label>
                                <input type="text" name="product_description" value="{{ old('product_description', $reservation->product_description) }}"
                                       required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; transition: border-color 0.2s;"
                                       onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                @if($reservation->product)
                                    <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.375rem;">
                                        Produto vinculado: {{ $reservation->product->sku }}
                                        @if($reservation->product->imei) | IMEI: {{ $reservation->product->imei }} @endif
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Observações -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; border-radius: 0.75rem 0.75rem 0 0;">
                                <h3 style="font-weight: 600; color: #111827;">Observações</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <textarea name="notes" rows="3" placeholder="Anotações sobre a reserva..."
                                          style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                                          onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">{{ old('notes', $reservation->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Lateral -->
                    <div>
                        <!-- Valores e Prazo -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">
                                    <svg style="width: 1.25rem; height: 1.25rem; display: inline; vertical-align: text-bottom; margin-right: 0.375rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Valores e Prazo
                                </h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor de Custo (R$) <span style="color: #dc2626;">*</span></label>
                                        <input type="number" name="cost_price" value="{{ old('cost_price', $reservation->cost_price) }}" required min="0" step="0.01"
                                               x-model.number="costPrice"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor de Venda (R$) <span style="color: #dc2626;">*</span></label>
                                        <input type="number" name="product_price" value="{{ old('product_price', $reservation->product_price) }}" required min="0.01" step="0.01"
                                               x-model.number="productPrice"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                </div>

                                <!-- Lucro estimado -->
                                <div x-show="costPrice > 0 && productPrice > 0" x-cloak
                                     style="padding: 0.5rem 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;"
                                     :style="(productPrice - costPrice) > 0 ? 'background: #f0fdf4; color: #16a34a;' : 'background: #fef2f2; color: #dc2626;'">
                                    <span style="font-weight: 500;">Lucro estimado:</span>
                                    <span style="font-weight: 700;" x-text="'R$ ' + (productPrice - costPrice).toFixed(2).replace('.', ',')"></span>
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor do Sinal (R$) <span style="color: #dc2626;">*</span></label>
                                    <input type="number" name="deposit_amount" value="{{ old('deposit_amount', $reservation->deposit_amount) }}" required min="{{ $reservation->deposit_paid }}" step="0.01"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                    @if($reservation->deposit_paid > 0)
                                        <p style="font-size: 0.7rem; color: #d97706; margin-top: 0.25rem;">
                                            Mínimo: R$ {{ number_format((float) $reservation->deposit_paid, 2, ',', '.') }} (já pago)
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Data Limite <span style="color: #dc2626;">*</span></label>
                                    <input type="date" name="expires_at" value="{{ old('expires_at', $reservation->expires_at->format('Y-m-d')) }}" required
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                    <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.25rem;">Até quando o cliente pode finalizar</p>
                                </div>
                            </div>
                        </div>

                        <!-- Resumo -->
                        <div style="background: #111827; border-radius: 0.75rem; padding: 1.25rem; color: white; margin-bottom: 1.5rem;">
                            <h3 style="font-weight: 600; margin-bottom: 1rem;">Resumo da Reserva</h3>

                            <div style="font-size: 0.875rem; margin-bottom: 0.75rem;">
                                <div style="opacity: 0.7; margin-bottom: 0.25rem;">Cliente:</div>
                                <div style="font-weight: 500;" x-text="selectedCustomer?.name || '—'"></div>
                            </div>

                            <div style="border-top: 1px solid rgba(255,255,255,0.15); padding-top: 0.75rem; margin-top: 0.5rem;">
                                <div x-show="costPrice > 0" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8rem; opacity: 0.7;">
                                    <span>Custo:</span>
                                    <span x-text="'R$ ' + (costPrice || 0).toFixed(2).replace('.', ',')"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                    <span style="opacity: 0.8;">Venda:</span>
                                    <span style="font-weight: 500;" x-text="'R$ ' + (productPrice || 0).toFixed(2).replace('.', ',')"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                    <span style="opacity: 0.8;">Sinal pago:</span>
                                    <span style="font-weight: 500; color: #86efac;">{{ $reservation->formatted_deposit_paid }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div style="margin-top: 1.25rem;">
                            <button type="submit"
                                    style="width: 100%; padding: 0.875rem 1.5rem; background: #111827; color: white; font-weight: 700; border-radius: 0.75rem; border: none; cursor: pointer; font-size: 1rem; transition: all 0.2s;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                Salvar Alterações
                            </button>
                            <a href="{{ route('reservations.show', $reservation) }}"
                               style="display: block; width: 100%; padding: 0.75rem; margin-top: 0.75rem; text-align: center; color: #9ca3af; font-weight: 500; text-decoration: none; font-size: 0.875rem; transition: color 0.2s;"
                               onmouseover="this.style.color='#374151';"
                               onmouseout="this.style.color='#9ca3af';">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editReservationForm() {
            return {
                // Cliente
                customerId: '{{ old('customer_id', $reservation->customer_id) }}',
                customerSearch: '',
                customerResults: [],
                selectedCustomer: {!! $reservation->customer ? json_encode([
                    'id' => $reservation->customer->id,
                    'name' => $reservation->customer->name,
                    'phone' => $reservation->customer->formatted_phone ?? '',
                ]) : 'null' !!},

                // Valores
                costPrice: {{ old('cost_price', $reservation->cost_price) ?: 0 }},
                productPrice: {{ old('product_price', $reservation->product_price) ?: 0 }},

                // === CLIENTES ===
                async searchCustomers() {
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }
                    try {
                        const response = await fetch(`/reservations/search-customers?q=${encodeURIComponent(this.customerSearch)}`);
                        this.customerResults = await response.json();
                    } catch (e) {
                        console.error('Erro ao buscar clientes:', e);
                    }
                },

                selectCustomer(customer) {
                    this.customerId = customer.id;
                    this.selectedCustomer = customer;
                    this.customerSearch = '';
                    this.customerResults = [];
                },

                clearCustomer() {
                    this.customerId = '';
                    this.selectedCustomer = null;
                    this.customerSearch = '';
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        @media (max-width: 768px) {
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
