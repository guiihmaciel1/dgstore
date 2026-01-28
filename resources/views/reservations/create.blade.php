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
                <a href="{{ route('reservations.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Nova Reserva</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Reserve um produto para um cliente</p>
                </div>
            </div>

            <form method="POST" action="{{ route('reservations.store') }}" x-data="reservationForm()">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Coluna Principal -->
                    <div>
                        <!-- Cliente -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Cliente</h3>
                            </div>
                            <div style="padding: 1.25rem; position: relative;">
                                <input type="hidden" name="customer_id" x-model="customerId">
                                <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                       placeholder="Buscar cliente por nome ou telefone..."
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                
                                <!-- Dropdown de resultados -->
                                <div x-show="customerResults.length > 0" x-cloak
                                     style="position: absolute; top: 100%; left: 1.25rem; right: 1.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; z-index: 50;">
                                    <template x-for="customer in customerResults" :key="customer.id">
                                        <div @click="selectCustomer(customer)" 
                                             style="padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f3f4f6;"
                                             onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                                            <div style="font-weight: 500;" x-text="customer.name"></div>
                                            <div style="font-size: 0.75rem; color: #6b7280;" x-text="customer.phone"></div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Cliente selecionado -->
                                <div x-show="selectedCustomer" x-cloak style="margin-top: 1rem; padding: 0.75rem; background: #f0fdf4; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <span style="font-weight: 600; color: #16a34a;" x-text="selectedCustomer?.name"></span>
                                        <span style="font-size: 0.75rem; color: #6b7280; margin-left: 0.5rem;" x-text="selectedCustomer?.phone"></span>
                                    </div>
                                    <button type="button" @click="clearCustomer()" style="padding: 0.25rem; color: #dc2626; background: none; border: none; cursor: pointer;">
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Produto -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Produto</h3>
                            </div>
                            <div style="padding: 1.25rem; position: relative;">
                                <input type="hidden" name="product_id" x-model="productId">
                                <input type="text" x-model="productSearch" @input.debounce.300ms="searchProducts()"
                                       placeholder="Buscar produto por nome ou SKU..."
                                       :disabled="selectedProduct !== null"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                
                                <!-- Dropdown de resultados -->
                                <div x-show="productResults.length > 0" x-cloak
                                     style="position: absolute; top: 100%; left: 1.25rem; right: 1.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; z-index: 50;">
                                    <template x-for="product in productResults" :key="product.id">
                                        <div @click="selectProduct(product)" 
                                             style="padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f3f4f6;"
                                             onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                                            <div style="font-weight: 500;" x-text="product.name"></div>
                                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #6b7280;">
                                                <span x-text="product.sku"></span>
                                                <span style="font-weight: 600; color: #16a34a;" x-text="product.formatted_price"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Produto selecionado -->
                                <div x-show="selectedProduct" x-cloak style="margin-top: 1rem; padding: 0.75rem; background: #eff6ff; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <span style="font-weight: 600; color: #2563eb;" x-text="selectedProduct?.name"></span>
                                        <div style="font-size: 0.75rem; color: #6b7280;">
                                            <span x-text="selectedProduct?.sku"></span> - 
                                            <span style="font-weight: 600; color: #16a34a;" x-text="selectedProduct?.formatted_price"></span>
                                        </div>
                                    </div>
                                    <button type="button" @click="clearProduct()" style="padding: 0.25rem; color: #dc2626; background: none; border: none; cursor: pointer;">
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Lateral -->
                    <div>
                        <!-- Valores e Prazo -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Valores e Prazo</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Preço do Produto (R$) *</label>
                                    <input type="number" name="product_price" x-model.number="productPrice" required min="0" step="0.01"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor do Sinal (R$) *</label>
                                    <input type="number" name="deposit_amount" x-model.number="depositAmount" required min="0" step="0.01"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Valor combinado como sinal</p>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Data Limite *</label>
                                    <input type="date" name="expires_at" x-model="expiresAt" required
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Até quando o cliente pode finalizar</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pagamento Inicial -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #fef3c7; border-bottom: 1px solid #fde68a;">
                                <h3 style="font-weight: 600; color: #92400e;">Pagamento Inicial (Opcional)</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor Pago Agora (R$)</label>
                                    <input type="number" name="initial_payment" x-model.number="initialPayment" min="0" step="0.01"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div x-show="initialPayment > 0">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Forma de Pagamento *</label>
                                    <select name="payment_method" style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Observações</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <textarea name="notes" rows="3" placeholder="Anotações sobre a reserva..."
                                          style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"></textarea>
                            </div>
                        </div>

                        <!-- Resumo -->
                        <div style="background: #111827; border-radius: 0.75rem; padding: 1.25rem; color: white;">
                            <h3 style="font-weight: 600; margin-bottom: 1rem;">Resumo da Reserva</h3>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                <span style="opacity: 0.8;">Valor do Produto:</span>
                                <span style="font-weight: 500;" x-text="'R$ ' + productPrice.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                <span style="opacity: 0.8;">Sinal Combinado:</span>
                                <span style="font-weight: 500;" x-text="'R$ ' + depositAmount.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div x-show="initialPayment > 0" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem; color: #86efac;">
                                <span>Pago Agora:</span>
                                <span style="font-weight: 500;" x-text="'- R$ ' + initialPayment.toFixed(2).replace('.', ',')"></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.2); font-size: 1rem;">
                                <span style="font-weight: 600;">Restante:</span>
                                <span style="font-weight: 700;" x-text="'R$ ' + Math.max(0, productPrice - initialPayment).toFixed(2).replace('.', ',')"></span>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div style="margin-top: 1rem;">
                            <button type="submit" :disabled="!customerId || !productId"
                                    style="width: 100%; padding: 0.75rem; background: #16a34a; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;"
                                    :style="(!customerId || !productId) ? 'opacity: 0.5; cursor: not-allowed;' : ''"
                                    onmouseover="if(!this.disabled) this.style.background='#15803d'" 
                                    onmouseout="this.style.background='#16a34a'">
                                Criar Reserva
                            </button>
                            <a href="{{ route('reservations.index') }}" 
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
        function reservationForm() {
            return {
                // Cliente
                customerId: '',
                customerSearch: '',
                customerResults: [],
                selectedCustomer: null,

                // Produto
                productId: '{{ $selectedProduct?->id ?? '' }}',
                productSearch: '',
                productResults: [],
                selectedProduct: {!! $selectedProduct ? json_encode([
                    'id' => $selectedProduct->id,
                    'name' => $selectedProduct->full_name,
                    'sku' => $selectedProduct->sku,
                    'price' => $selectedProduct->sale_price,
                    'formatted_price' => $selectedProduct->formatted_sale_price,
                ]) : 'null' !!},

                // Valores
                productPrice: {{ $selectedProduct?->sale_price ?? 0 }},
                depositAmount: 0,
                initialPayment: 0,
                expiresAt: '{{ date('Y-m-d', strtotime('+7 days')) }}',

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
                },

                async searchProducts() {
                    if (this.productSearch.length < 2) {
                        this.productResults = [];
                        return;
                    }

                    try {
                        const response = await fetch(`/reservations/search-products?q=${encodeURIComponent(this.productSearch)}`);
                        this.productResults = await response.json();
                    } catch (e) {
                        console.error('Erro ao buscar produtos:', e);
                    }
                },

                selectProduct(product) {
                    this.productId = product.id;
                    this.selectedProduct = product;
                    this.productPrice = product.price;
                    this.productSearch = '';
                    this.productResults = [];
                },

                clearProduct() {
                    this.productId = '';
                    this.selectedProduct = null;
                    this.productPrice = 0;
                }
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
