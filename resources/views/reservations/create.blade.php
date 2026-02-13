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
                <input type="hidden" name="customer_id" x-model="customerId">
                <input type="hidden" name="product_id" x-model="productId">
                <input type="hidden" name="product_description" x-model="productDescription">
                <input type="hidden" name="source" x-model="source">

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

                                <!-- Nenhum resultado -->
                                <div x-show="customerSearch.length >= 2 && customerResults.length === 0 && searchedCustomer && !selectedCustomer" x-cloak
                                     style="margin-top: 0.75rem; padding: 0.75rem; background: #fef3c7; border-radius: 0.5rem; text-align: center;">
                                    <p style="font-size: 0.875rem; color: #92400e;">Nenhum cliente encontrado para "<span x-text="customerSearch"></span>"</p>
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
                                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'" title="Remover cliente">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Produto -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; position: relative;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; border-radius: 0.75rem 0.75rem 0 0;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h3 style="font-weight: 600; color: #111827;">
                                        <svg style="width: 1.25rem; height: 1.25rem; display: inline; vertical-align: text-bottom; margin-right: 0.375rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        Produto
                                    </h3>
                                    <!-- Toggle modo manual -->
                                    <button type="button" @click="toggleManualMode()"
                                            style="font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 1rem; border: 1px solid; cursor: pointer; transition: all 0.2s;"
                                            :style="manualMode
                                                ? 'background: #dbeafe; color: #2563eb; border-color: #93c5fd;'
                                                : 'background: #f3f4f6; color: #6b7280; border-color: #d1d5db;'">
                                        <span x-text="manualMode ? '← Buscar produto' : '+ Digitar manualmente'"></span>
                                    </button>
                                </div>
                            </div>
                            <div style="padding: 1.25rem; position: relative;">
                                <!-- Modo Busca -->
                                <template x-if="!manualMode && !selectedProduct">
                                    <div>
                                        <input type="text" x-model="productSearch" @input.debounce.300ms="searchProducts()"
                                               placeholder="Buscar no estoque ou cotações de fornecedores..."
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; transition: border-color 0.2s;"
                                               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                        <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.375rem;">Busca em estoque e cotações de fornecedores</p>
                                    </div>
                                </template>

                                <!-- Dropdown de resultados de produto -->
                                <div x-show="productResults.length > 0 && !selectedProduct && !manualMode" x-cloak
                                     @click.outside="productResults = []"
                                     style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 8px 24px -4px rgba(0,0,0,0.2); max-height: 280px; overflow-y: auto; z-index: 100;">
                                    <template x-for="(product, idx) in productResults" :key="idx">
                                        <div @click="selectProduct(product)"
                                             style="padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f3f4f6; transition: background 0.15s;"
                                             onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='white'">
                                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                                <div style="flex: 1;">
                                                    <div style="font-weight: 500; color: #111827;" x-text="product.name"></div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;" x-text="product.sku"></div>
                                                </div>
                                                <div style="text-align: right; white-space: nowrap;">
                                                    <div style="font-weight: 600; color: #16a34a; font-size: 0.875rem;" x-text="product.formatted_price"></div>
                                                    <template x-if="product.source === 'quotation' && product.formatted_base_price">
                                                        <div style="font-size: 0.625rem; color: #9ca3af;" x-text="product.formatted_base_price + ' +4%'"></div>
                                                    </template>
                                                    <span style="font-size: 0.625rem; padding: 0.125rem 0.5rem; border-radius: 1rem; font-weight: 500;"
                                                          :style="product.source === 'stock'
                                                              ? (product.stock > 0 ? 'background: #dcfce7; color: #16a34a;' : 'background: #fef3c7; color: #d97706;')
                                                              : 'background: #dbeafe; color: #2563eb;'"
                                                          x-text="product.source_label"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Modo Manual -->
                                <template x-if="manualMode && !selectedProduct">
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Descrição do Produto *</label>
                                        <input type="text" x-model="manualProductName"
                                               placeholder="Ex: iPhone 15 Pro Max 256GB - Preto"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 0.75rem; transition: border-color 0.2s;"
                                               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">

                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Preço Estimado (R$)</label>
                                        <input type="number" x-model.number="manualProductPrice" step="0.01" min="0"
                                               placeholder="0,00"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 1rem; transition: border-color 0.2s;"
                                               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">

                                        <button type="button" @click="confirmManualProduct()"
                                                :disabled="!manualProductName"
                                                style="width: 100%; padding: 0.5rem; background: #2563eb; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;"
                                                :style="!manualProductName ? 'opacity: 0.5; cursor: not-allowed;' : ''"
                                                onmouseover="if(!this.disabled) this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                                            Confirmar Produto
                                        </button>
                                    </div>
                                </template>

                                <!-- Nenhum resultado -->
                                <div x-show="productSearch.length >= 2 && productResults.length === 0 && searchedProduct && !selectedProduct && !manualMode" x-cloak
                                     style="margin-top: 0.75rem; padding: 0.75rem; background: #fef3c7; border-radius: 0.5rem; text-align: center;">
                                    <p style="font-size: 0.875rem; color: #92400e;">Nenhum produto encontrado.</p>
                                    <button type="button" @click="manualMode = true; manualProductName = productSearch; productSearch = '';"
                                            style="font-size: 0.75rem; color: #2563eb; background: none; border: none; cursor: pointer; text-decoration: underline; margin-top: 0.25rem;">
                                        Digitar manualmente
                                    </button>
                                </div>

                                <!-- Produto selecionado -->
                                <div x-show="selectedProduct" x-cloak style="padding: 0.625rem 0.75rem; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;"
                                     :style="source === 'stock'
                                         ? 'background: #f0fdf4; border: 1px solid #bbf7d0;'
                                         : (source === 'quotation' ? 'background: #eff6ff; border: 1px solid #bfdbfe;' : 'background: #fefce8; border: 1px solid #fde68a;')">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0; flex: 1;">
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; min-width: 1.5rem; border-radius: 50%; color: white;"
                                              :style="source === 'stock' ? 'background: #16a34a;' : (source === 'quotation' ? 'background: #2563eb;' : 'background: #d97706;')">
                                            <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                        <div style="min-width: 0; flex: 1;">
                                            <div style="font-weight: 600; font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                 :style="source === 'stock' ? 'color: #15803d;' : (source === 'quotation' ? 'color: #1d4ed8;' : 'color: #b45309;')"
                                                 x-text="selectedProduct?.name"></div>
                                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                                                <span x-text="selectedProduct?.sku"></span>
                                                <span style="font-weight: 600;"
                                                      :style="source === 'stock' ? 'color: #16a34a;' : (source === 'quotation' ? 'color: #2563eb;' : 'color: #d97706;')"
                                                      x-text="selectedProduct?.formatted_price"></span>
                                                <span style="font-size: 0.625rem; padding: 0.0625rem 0.375rem; border-radius: 1rem; font-weight: 500;"
                                                      :style="source === 'stock'
                                                          ? 'background: #dcfce7; color: #16a34a;'
                                                          : (source === 'quotation' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;')"
                                                      x-text="source === 'stock' ? 'Estoque' : (source === 'quotation' ? 'Cotação' : 'Manual')"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" @click="clearProduct()" style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; min-width: 1.5rem; color: #dc2626; background: none; border: none; cursor: pointer; border-radius: 0.375rem; margin-left: 0.5rem;"
                                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'" title="Remover produto">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
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
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor de Compra (R$) *</label>
                                        <input type="number" name="cost_price" x-model.number="costPrice" required min="0" step="0.01"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                        <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.25rem;">Custo do produto</p>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor de Venda (R$) *</label>
                                        <input type="number" name="product_price" x-model.number="productPrice" required min="0" step="0.01"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                        <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.25rem;">Preço para o cliente</p>
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
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor do Sinal (R$) *</label>
                                    <input type="number" name="deposit_amount" x-model.number="depositAmount" required min="0" step="0.01"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                    <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.25rem;">Valor combinado como sinal</p>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Data Limite *</label>
                                    <input type="date" name="expires_at" x-model="expiresAt" required
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                                    <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.25rem;">Até quando o cliente pode finalizar</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pagamento Inicial -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #fef3c7; border-bottom: 1px solid #fde68a;">
                                <h3 style="font-weight: 600; color: #92400e;">
                                    <svg style="width: 1.25rem; height: 1.25rem; display: inline; vertical-align: text-bottom; margin-right: 0.375rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Pagamento Inicial (Opcional)
                                </h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Valor Pago Agora (R$)</label>
                                    <input type="number" name="initial_payment" x-model.number="initialPayment" min="0" step="0.01"
                                           style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
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
                                          style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                                          onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                            </div>
                        </div>

                        <!-- Resumo -->
                        <div style="background: #111827; border-radius: 0.75rem; padding: 1.25rem; color: white;">
                            <h3 style="font-weight: 600; margin-bottom: 1rem;">
                                <svg style="width: 1.25rem; height: 1.25rem; display: inline; vertical-align: text-bottom; margin-right: 0.375rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Resumo da Reserva
                            </h3>
                            <div style="font-size: 0.875rem; margin-bottom: 0.75rem;">
                                <div style="opacity: 0.7; margin-bottom: 0.25rem;">Cliente:</div>
                                <div style="font-weight: 500;" x-text="selectedCustomer?.name || '—'"></div>
                            </div>
                            <div style="font-size: 0.875rem; margin-bottom: 0.75rem;">
                                <div style="opacity: 0.7; margin-bottom: 0.25rem;">Produto:</div>
                                <div style="font-weight: 500;" x-text="productDescription || '—'"></div>
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
                                <div x-show="costPrice > 0 && productPrice > 0" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8rem; color: #86efac;">
                                    <span>Lucro:</span>
                                    <span style="font-weight: 600;" x-text="'R$ ' + ((productPrice || 0) - (costPrice || 0)).toFixed(2).replace('.', ',')"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                    <span style="opacity: 0.8;">Sinal Combinado:</span>
                                    <span style="font-weight: 500;" x-text="'R$ ' + (depositAmount || 0).toFixed(2).replace('.', ',')"></span>
                                </div>
                                <div x-show="initialPayment > 0" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem; color: #86efac;">
                                    <span>Pago Agora:</span>
                                    <span style="font-weight: 500;" x-text="'- R$ ' + (initialPayment || 0).toFixed(2).replace('.', ',')"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.2); font-size: 1rem;">
                                    <span style="font-weight: 600;">Restante:</span>
                                    <span style="font-weight: 700;" x-text="'R$ ' + Math.max(0, (productPrice || 0) - (initialPayment || 0)).toFixed(2).replace('.', ',')"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 0.625rem;">
                            <button type="submit" :disabled="!canSubmit"
                                    style="width: 100%; padding: 0.875rem; background: #16a34a; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s; box-shadow: 0 1px 3px rgba(22,163,106,0.3);"
                                    :style="!canSubmit ? 'opacity: 0.5; cursor: not-allowed; box-shadow: none;' : ''"
                                    onmouseover="if(!this.disabled) { this.style.background='#15803d'; this.style.boxShadow='0 4px 12px rgba(22,163,106,0.4)'; this.style.transform='translateY(-1px)'; }"
                                    onmouseout="this.style.background='#16a34a'; this.style.boxShadow='0 1px 3px rgba(22,163,106,0.3)'; this.style.transform='none';">
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                                Criar Reserva
                            </button>
                            <a href="{{ route('reservations.index') }}"
                               style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.75rem; color: #6b7280; font-weight: 500; text-decoration: none; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;"
                               onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'; this.style.color='#374151';"
                               onmouseout="this.style.background='transparent'; this.style.borderColor='#e5e7eb'; this.style.color='#6b7280';">
                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
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
        function reservationForm() {
            return {
                // Cliente
                customerId: '',
                customerSearch: '',
                customerResults: [],
                selectedCustomer: null,
                searchedCustomer: false,

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
                    'source' => 'stock',
                    'source_label' => 'Estoque',
                ]) : 'null' !!},
                searchedProduct: false,
                manualMode: false,
                manualProductName: '',
                manualProductPrice: 0,

                // Campos hidden
                productDescription: '{{ $selectedProduct ? $selectedProduct->full_name : '' }}',
                source: '{{ $selectedProduct ? 'stock' : 'manual' }}',

                // Valores
                costPrice: 0,
                productPrice: {{ $selectedProduct?->sale_price ?? 0 }},
                depositAmount: 0,
                initialPayment: 0,
                expiresAt: '{{ date('Y-m-d', strtotime('+7 days')) }}',

                get canSubmit() {
                    return this.customerId && this.productDescription && this.productPrice > 0;
                },

                // === CLIENTES ===
                async searchCustomers() {
                    this.searchedCustomer = false;
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }
                    try {
                        const response = await fetch(`/reservations/search-customers?q=${encodeURIComponent(this.customerSearch)}`);
                        this.customerResults = await response.json();
                        this.searchedCustomer = true;
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
                    this.searchedCustomer = false;
                },

                // === PRODUTOS ===
                async searchProducts() {
                    this.searchedProduct = false;
                    if (this.productSearch.length < 2) {
                        this.productResults = [];
                        return;
                    }
                    try {
                        const response = await fetch(`/reservations/search-products?q=${encodeURIComponent(this.productSearch)}`);
                        this.productResults = await response.json();
                        this.searchedProduct = true;
                    } catch (e) {
                        console.error('Erro ao buscar produtos:', e);
                    }
                },

                selectProduct(product) {
                    this.productId = product.id || '';
                    this.selectedProduct = product;
                    this.productDescription = product.name;
                    this.source = product.source || 'stock';

                    // Cotação: custo = valor final (+4% frete); Estoque: preço = venda
                    if (product.source === 'quotation') {
                        this.costPrice = product.final_price || product.price || 0;
                        this.productPrice = 0;
                    } else {
                        this.costPrice = 0;
                        this.productPrice = product.price || 0;
                    }

                    this.productSearch = '';
                    this.productResults = [];
                },

                clearProduct() {
                    this.productId = '';
                    this.selectedProduct = null;
                    this.productDescription = '';
                    this.source = 'manual';
                    this.costPrice = 0;
                    this.productPrice = 0;
                    this.productSearch = '';
                    this.searchedProduct = false;
                    this.manualMode = false;
                    this.manualProductName = '';
                    this.manualProductPrice = 0;
                },

                toggleManualMode() {
                    this.manualMode = !this.manualMode;
                    this.productSearch = '';
                    this.productResults = [];
                },

                confirmManualProduct() {
                    if (!this.manualProductName) return;

                    this.selectedProduct = {
                        id: null,
                        name: this.manualProductName,
                        sku: 'Produto manual',
                        price: this.manualProductPrice || 0,
                        formatted_price: 'R$ ' + (this.manualProductPrice || 0).toFixed(2).replace('.', ','),
                        source: 'manual',
                        source_label: 'Inserido Manualmente',
                    };
                    this.productId = '';
                    this.productDescription = this.manualProductName;
                    this.source = 'manual';
                    this.productPrice = this.manualProductPrice || 0;
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
