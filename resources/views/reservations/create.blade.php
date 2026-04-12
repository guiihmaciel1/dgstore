<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            @if($errors->any())
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span style="font-weight: 600; color: #991b1b;">Corrija os erros abaixo:</span>
                    </div>
                    <ul style="list-style: disc; padding-left: 1.5rem; color: #dc2626; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a href="{{ route('reservations.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Nova Reserva</h1>
                        <p style="font-size: 0.875rem; color: #6b7280;">Selecione os produtos e o cliente para reservar</p>
                    </div>
                </div>
                <span style="font-size: 0.875rem; color: #9ca3af;">ESC para cancelar</span>
            </div>

            <div x-data="reservationForm()" @keydown.escape.window="window.location.href='{{ route('reservations.index') }}'">
            <form method="POST" action="{{ route('reservations.store') }}" @submit="handleSubmit($event)">
                @csrf

                <input type="hidden" name="customer_id" x-model="customerId">

                <template x-for="(item, idx) in items" :key="idx">
                    <div>
                        <input type="hidden" :name="'items['+idx+'][product_id]'" :value="item.id">
                        <input type="hidden" :name="'items['+idx+'][cost_price]'" :value="item.cost_price">
                        <input type="hidden" :name="'items['+idx+'][sale_price]'" :value="item.sale_price">
                    </div>
                </template>

                <div class="res-grid">
                    <!-- COLUNA PRINCIPAL -->
                    <div class="res-main">

                        <!-- PASSO 1: PRODUTOS -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: visible;">
                            <div style="background: #111827; color: white; padding: 1rem 1.5rem; border-radius: 1rem 1rem 0 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center;">
                                        <span style="width: 2rem; height: 2rem; background: white; color: #111827; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 0.75rem;">1</span>
                                        <span style="font-size: 1.125rem; font-weight: 600;">Adicionar Produtos</span>
                                    </div>
                                    <a href="{{ route('products.create') }}" target="_blank"
                                       style="display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 500; text-decoration: none;"
                                       onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Cadastrar Produto
                                    </a>
                                </div>
                            </div>
                            <div style="padding: 1.5rem;">
                                <div style="position: relative;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Buscar produto por nome, SKU ou código
                                    </label>
                                    <div style="position: relative;">
                                        <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input type="text" x-model="searchTerm" @input.debounce.300ms="searchProducts()"
                                               @keydown.enter.prevent="if(searchResults.length > 0) addProduct(searchResults[0])"
                                               placeholder="Digite para buscar... (Enter para o primeiro)"
                                               style="width: 100%; padding: 0.875rem 1rem 0.875rem 2.75rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-size: 0.9375rem; outline: none;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>

                                    <!-- Dropdown resultados -->
                                    <div x-show="searchResults.length > 0" x-cloak @click.outside="searchResults = []"
                                         style="position: absolute; z-index: 50; margin-top: 0.5rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border-radius: 0.75rem; border: 1px solid #e5e7eb; max-height: 20rem; overflow: auto;">
                                        <template x-for="(product, idx) in searchResults" :key="product.id">
                                            <button type="button" @click="addProduct(product)"
                                                    style="width: 100%; padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #f3f4f6; cursor: pointer; background: white; display: flex; justify-content: space-between; align-items: center;"
                                                    onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                                <div style="min-width: 0; flex: 1;">
                                                    <span style="font-weight: 500; color: #111827; display: block;" x-text="product.name"></span>
                                                    <span style="font-size: 0.75rem; color: #6b7280;" x-text="product.sku"></span>
                                                </div>
                                                <div style="text-align: right; white-space: nowrap; margin-left: 0.75rem;">
                                                    <div style="font-weight: 600; color: #16a34a; font-size: 0.875rem;" x-text="product.formatted_price"></div>
                                                    <span style="font-size: 0.625rem; padding: 0.125rem 0.5rem; border-radius: 1rem; font-weight: 500; background: #dcfce7; color: #16a34a;" x-text="'Estoque: ' + product.stock"></span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Nenhum resultado -->
                                    <div x-show="searchTerm.length >= 2 && searchResults.length === 0 && searched && !searching" x-cloak
                                         style="position: absolute; z-index: 50; margin-top: 0.5rem; width: 100%; background: white; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.5rem; text-align: center;">
                                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem;">Nenhum produto em estoque encontrado</p>
                                        <a href="{{ route('products.create') }}" target="_blank"
                                           style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 500; text-decoration: none;">
                                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Cadastrar Produto
                                        </a>
                                    </div>
                                </div>

                                <!-- Lista de produtos adicionados -->
                                <div x-show="items.length > 0" style="margin-top: 1.25rem;">
                                    <template x-for="(item, idx) in items" :key="idx">
                                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.875rem; border-radius: 0.75rem; margin-bottom: 0.5rem; border: 1px solid #e5e7eb; background: #fafafa;">
                                            <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0;">
                                                <div style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; background: #f0fdf4; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                                    <svg style="width: 1.125rem; height: 1.125rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                                <div style="min-width: 0; flex: 1;">
                                                    <div style="font-weight: 600; font-size: 0.875rem; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="item.name"></div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;" x-text="item.sku"></div>
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 1rem; flex-shrink: 0;">
                                                <div style="text-align: right;">
                                                    <div style="font-size: 0.6875rem; color: #9ca3af;">Custo</div>
                                                    <div style="font-size: 0.8125rem; color: #374151; font-weight: 500;" x-text="formatMoney(item.cost_price)"></div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <div style="font-size: 0.6875rem; color: #9ca3af;">Venda</div>
                                                    <input type="number" x-model.number="item.sale_price" step="0.01" min="0"
                                                           style="width: 7rem; padding: 0.375rem 0.5rem; border: 2px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; color: #16a34a; text-align: right;"
                                                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                                </div>
                                                <button type="button" @click="removeProduct(idx)"
                                                        style="width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; color: #9ca3af; cursor: pointer; background: none; border: none; border-radius: 0.375rem;"
                                                        onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='none'; this.style.color='#9ca3af'">
                                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Vazio -->
                                <div x-show="items.length === 0 && !searchTerm" style="padding: 2.5rem 1rem; text-align: center; color: #9ca3af;">
                                    <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.75rem; opacity: 0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p style="font-size: 0.875rem;">Nenhum produto adicionado</p>
                                    <p style="font-size: 0.75rem; margin-top: 0.25rem;">Use o campo acima para buscar e adicionar produtos</p>
                                </div>
                            </div>
                        </div>

                        <!-- PASSO 2: SINAL E PRAZO -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                            <div style="background: #374151; color: white; padding: 0.75rem 1.5rem; border-radius: 1rem 1rem 0 0;">
                                <div style="display: flex; align-items: center;">
                                    <span style="width: 1.5rem; height: 1.5rem; background: white; color: #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; margin-right: 0.5rem;">2</span>
                                    <span style="font-weight: 600;">Sinal e Prazo</span>
                                </div>
                            </div>
                            <div style="padding: 1.25rem;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                            Valor do Sinal (R$) <span style="color: #dc2626;">*</span>
                                        </label>
                                        <input type="number" name="deposit_amount" x-model.number="depositAmount" required min="0" step="0.01"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                        <p style="font-size: 0.6875rem; color: #9ca3af; margin-top: 0.25rem;">Valor combinado como sinal</p>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                            Data Limite <span style="color: #dc2626;">*</span>
                                        </label>
                                        <input type="date" name="expires_at" x-model="expiresAt" required
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                        <p style="font-size: 0.6875rem; color: #9ca3af; margin-top: 0.25rem;">Até quando o cliente pode finalizar</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PAGAMENTO INICIAL -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                            <div style="padding: 0.75rem 1.5rem; background: #fef3c7; border-radius: 1rem 1rem 0 0; border-bottom: 1px solid #fde68a;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <svg style="width: 1.125rem; height: 1.125rem; color: #92400e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span style="font-weight: 600; color: #92400e;">Pagamento Inicial (Opcional)</span>
                                </div>
                            </div>
                            <div style="padding: 1.25rem;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Valor Pago Agora (R$)</label>
                                        <input type="number" name="initial_payment" x-model.number="initialPayment" min="0" step="0.01"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div x-show="initialPayment > 0">
                                        <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Forma de Pagamento <span style="color: #dc2626;">*</span></label>
                                        <select name="payment_method" style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                            @foreach($paymentMethods as $method)
                                                <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div x-show="initialPayment > 0 && depositAmount > 0" x-cloak
                                     style="margin-top: 0.75rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem;"
                                     :style="initialPayment >= depositAmount ? 'background: #f0fdf4; color: #16a34a;' : 'background: #eff6ff; color: #2563eb;'">
                                    <span x-show="initialPayment >= depositAmount">Sinal pago integralmente</span>
                                    <span x-show="initialPayment < depositAmount" x-text="'Faltam ' + formatMoney(depositAmount - initialPayment) + ' para completar o sinal'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.25rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Observações (opcional)</label>
                            <textarea name="notes" rows="2"
                                      placeholder="Anotações sobre a reserva..."
                                      style="width: 100%; border: 2px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem; outline: none; resize: vertical; font-size: 0.875rem;"
                                      onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                        </div>
                    </div>

                    <!-- COLUNA LATERAL -->
                    <div class="res-sidebar">

                        <!-- CLIENTE -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; position: relative; z-index: 30;">
                            <div style="background: #374151; color: white; padding: 0.75rem 1.5rem; border-radius: 1rem 1rem 0 0;">
                                <div style="display: flex; align-items: center;">
                                    <span style="width: 1.5rem; height: 1.5rem; background: white; color: #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; margin-right: 0.5rem;">3</span>
                                    <span style="font-weight: 600;">Cliente <span style="color: #fca5a5;">*</span></span>
                                </div>
                            </div>
                            <div style="padding: 1rem;">
                                <div x-show="!selectedCustomer" style="position: relative;">
                                    <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                           placeholder="Buscar cliente por nome ou telefone..."
                                           style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; outline: none;"
                                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">

                                    <div x-show="customerResults.length > 0 || (customerSearch.length >= 2 && customerResults.length === 0 && searchedCustomer)" x-cloak
                                         @click.outside="customerResults = []"
                                         style="position: absolute; z-index: 50; margin-top: 0.5rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border-radius: 0.75rem; border: 1px solid #e5e7eb; max-height: 16rem; overflow: auto;">
                                        <template x-for="customer in customerResults" :key="customer.id">
                                            <button type="button" @click="selectCustomer(customer)"
                                                    style="width: 100%; padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #f3f4f6; cursor: pointer; background: white;"
                                                    onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                                <span style="font-weight: 500; color: #111827;" x-text="customer.name"></span>
                                                <span style="font-size: 0.875rem; color: #6b7280; display: block;" x-text="customer.phone"></span>
                                            </button>
                                        </template>
                                        <div x-show="customerSearch.length >= 2 && customerResults.length === 0 && searchedCustomer"
                                             style="padding: 0.75rem 1rem; text-align: center;">
                                            <p style="font-size: 0.8125rem; color: #6b7280;">Cliente não encontrado</p>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="selectedCustomer" x-cloak style="position: relative; padding: 0.625rem 2.5rem 0.625rem 0.75rem; background: #f0fdf4; border-radius: 0.75rem; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 0.625rem;">
                                    <div style="width: 2rem; height: 2rem; min-width: 2rem; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <svg style="width: 1rem; height: 1rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div style="min-width: 0;">
                                        <span style="font-weight: 600; color: #111827; font-size: 0.875rem;" x-text="selectedCustomer?.name"></span>
                                        <span style="font-size: 0.75rem; color: #6b7280; margin-left: 0.5rem;" x-text="selectedCustomer?.phone"></span>
                                    </div>
                                    <button type="button" @click="clearCustomer()" style="position: absolute; top: 0.375rem; right: 0.375rem; width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center; color: #9ca3af; cursor: pointer; background: none; border: none; border-radius: 0.375rem;"
                                            onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='none'; this.style.color='#9ca3af'">
                                        <svg style="height: 0.875rem; width: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- RESUMO -->
                        <div style="background: #111827; border-radius: 1rem; padding: 1.5rem; color: white;">
                            <h3 style="font-weight: 600; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                                Resumo da Reserva
                            </h3>

                            <div style="font-size: 0.875rem; margin-bottom: 0.75rem;">
                                <div style="opacity: 0.6; margin-bottom: 0.25rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Cliente</div>
                                <div style="font-weight: 500;" x-text="selectedCustomer?.name || '—'"></div>
                            </div>

                            <div style="font-size: 0.875rem; margin-bottom: 0.75rem;">
                                <div style="opacity: 0.6; margin-bottom: 0.375rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Produtos (<span x-text="items.length"></span>)
                                </div>
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-bottom: 0.25rem; opacity: 0.9;">
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 60%;" x-text="item.name"></span>
                                        <span style="font-weight: 500;" x-text="formatMoney(item.sale_price)"></span>
                                    </div>
                                </template>
                                <div x-show="items.length === 0" style="opacity: 0.5;">—</div>
                            </div>

                            <div style="font-size: 0.875rem; margin-bottom: 0.75rem;">
                                <div style="opacity: 0.6; margin-bottom: 0.25rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Validade</div>
                                <div style="font-weight: 500;" x-text="expiresAt ? new Date(expiresAt + 'T12:00:00').toLocaleDateString('pt-BR') : '—'"></div>
                            </div>

                            <div style="border-top: 1px solid rgba(255,255,255,0.15); padding-top: 1rem; margin-top: 0.75rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8125rem; opacity: 0.7;">
                                    <span>Custo total:</span>
                                    <span x-text="formatMoney(totalCost)"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9375rem;">
                                    <span style="opacity: 0.8;">Venda total:</span>
                                    <span style="font-weight: 600;" x-text="formatMoney(totalSale)"></span>
                                </div>
                                <div x-show="totalCost > 0 && totalSale > 0" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8125rem; color: #86efac;">
                                    <span>Lucro:</span>
                                    <span style="font-weight: 600;" x-text="formatMoney(totalSale - totalCost)"></span>
                                </div>

                                <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.75rem; margin-top: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
                                        <span style="opacity: 0.8;">Sinal combinado:</span>
                                        <span style="font-weight: 500;" x-text="formatMoney(depositAmount)"></span>
                                    </div>
                                    <div x-show="initialPayment > 0" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem; color: #86efac;">
                                        <span>Pago agora:</span>
                                        <span style="font-weight: 500;" x-text="'- ' + formatMoney(initialPayment)"></span>
                                    </div>
                                </div>

                                <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 0.75rem; margin-top: 0.5rem; display: flex; justify-content: space-between; font-size: 1.125rem;">
                                    <span style="font-weight: 600;">Restante:</span>
                                    <span style="font-weight: 700;" x-text="formatMoney(Math.max(0, totalSale - initialPayment))"></span>
                                </div>
                            </div>
                        </div>

                        <!-- BOTÃO -->
                        <div style="border-radius: 1rem; overflow: hidden;">
                            <button type="submit" :disabled="!canSubmit || submitting"
                                    class="res-submit-btn"
                                    :class="{ 'res-submit-btn-disabled': !canSubmit || submitting, 'res-submit-btn-active': canSubmit && !submitting }">
                                <span x-show="submitting" class="flex items-center justify-center gap-3">
                                    <svg class="animate-spin" style="width:1.5rem;height:1.5rem;" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span style="letter-spacing: 0.05em;">PROCESSANDO...</span>
                                </span>
                                <span x-show="canSubmit && !submitting" class="flex items-center justify-center gap-3">
                                    <span style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span style="letter-spacing: 0.05em;">CRIAR RESERVA</span>
                                </span>
                                <span x-show="!canSubmit && !submitting" class="flex items-center justify-center gap-2">
                                    <svg style="width: 1.25rem; height: 1.25rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span x-text="getSubmitButtonText()"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    <script>
        function reservationForm() {
            return {
                customerId: '',
                customerSearch: '',
                customerResults: [],
                selectedCustomer: null,
                searchedCustomer: false,

                searchTerm: '',
                searchResults: [],
                searched: false,
                searching: false,

                items: [],

                depositAmount: 0,
                initialPayment: 0,
                expiresAt: '{{ date("Y-m-d", strtotime("+7 days")) }}',
                submitting: false,

                get totalCost() {
                    return this.items.reduce((sum, i) => sum + (parseFloat(i.cost_price) || 0), 0);
                },

                get totalSale() {
                    return this.items.reduce((sum, i) => sum + (parseFloat(i.sale_price) || 0), 0);
                },

                get canSubmit() {
                    if (this.items.length === 0) return false;
                    if (!this.customerId) return false;
                    if (this.items.some(i => !i.sale_price || i.sale_price <= 0)) return false;
                    return true;
                },

                getSubmitButtonText() {
                    if (this.items.length === 0) return 'Adicione produtos para continuar';
                    if (!this.customerId) return 'Selecione um cliente';
                    if (this.items.some(i => !i.sale_price || i.sale_price <= 0)) return 'Informe o preço de venda';
                    return 'Verifique os dados';
                },

                handleSubmit(event) {
                    if (this.submitting) { event.preventDefault(); return; }
                    if (!this.canSubmit) { event.preventDefault(); return; }
                    this.submitting = true;
                },

                formatMoney(value) {
                    return 'R$ ' + (value || 0).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },

                // === CLIENTES ===
                async searchCustomers() {
                    this.searchedCustomer = false;
                    if (this.customerSearch.length < 2) { this.customerResults = []; return; }
                    try {
                        const response = await fetch(`/reservations/search-customers?q=${encodeURIComponent(this.customerSearch)}`);
                        this.customerResults = await response.json();
                        this.searchedCustomer = true;
                    } catch (e) { console.error('Erro ao buscar clientes:', e); }
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
                    this.searched = false;
                    this.searching = true;
                    if (this.searchTerm.length < 2) { this.searchResults = []; this.searching = false; return; }
                    try {
                        const excludeIds = this.items.map(i => i.id);
                        const params = new URLSearchParams({ q: this.searchTerm });
                        excludeIds.forEach(id => params.append('exclude[]', id));

                        const response = await fetch(`/reservations/search-products?${params.toString()}`);
                        this.searchResults = await response.json();
                        this.searched = true;
                    } catch (e) {
                        console.error('Erro ao buscar produtos:', e);
                    } finally {
                        this.searching = false;
                    }
                },

                addProduct(product) {
                    if (this.items.find(i => i.id === product.id)) return;

                    this.items.push({
                        id: product.id,
                        name: product.name,
                        sku: product.sku,
                        cost_price: product.cost_price || 0,
                        sale_price: product.sale_price || 0,
                        stock: product.stock,
                    });

                    this.searchTerm = '';
                    this.searchResults = [];
                },

                removeProduct(index) {
                    this.items.splice(index, 1);
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        .res-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        .res-main { display: flex; flex-direction: column; gap: 1.5rem; }
        .res-sidebar { display: flex; flex-direction: column; gap: 1.5rem; }

        @media (min-width: 1024px) {
            .res-grid { grid-template-columns: 2fr 1fr; }
        }

        .res-submit-btn {
            width: 100%;
            padding: 1.25rem 1.5rem;
            font-size: 1.125rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }
        .res-submit-btn-disabled { background: #374151; color: #9ca3af; cursor: not-allowed; }
        .res-submit-btn-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .res-submit-btn-active:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
            transform: translateY(-2px);
        }
        .res-submit-btn-active::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: res-shimmer 3s infinite;
        }
        @keyframes res-shimmer { 0% { left: -100%; } 100% { left: 100%; } }
    </style>
</x-app-layout>
