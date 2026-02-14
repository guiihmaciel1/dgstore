<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a href="{{ route('sales.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;" 
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Nova Venda</h1>
                        <p style="font-size: 0.875rem; color: #6b7280;">Preencha os dados para registrar uma venda</p>
                    </div>
                </div>
                <span style="font-size: 0.875rem; color: #9ca3af;">ESC para cancelar</span>
            </div>

            <form method="POST" action="{{ route('sales.store') }}" x-data="saleForm()" @keydown.escape.window="window.location.href='{{ route('sales.index') }}'">
                @csrf
                
                <div class="sale-grid">
                    <!-- COLUNA PRINCIPAL - PRODUTOS -->
                    <div class="sale-main">
                        
                        <!-- PASSO 1: BUSCAR PRODUTO -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="background: #111827; color: white; padding: 1rem 1.5rem;">
                                <div style="display: flex; align-items: center;">
                                    <span style="width: 2rem; height: 2rem; background: white; color: #111827; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 0.75rem;">1</span>
                                    <span style="font-size: 1.125rem; font-weight: 600;">Adicionar Produtos</span>
                                </div>
                            </div>
                            <div style="padding: 1.5rem;">
                                <div style="position: relative;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Buscar produto por nome, SKU ou código
                                    </label>
                                    <div style="position: relative;">
                                        <input 
                                            type="text" 
                                            x-model="searchTerm"
                                            @input.debounce.300ms="searchProducts"
                                            @keydown.enter.prevent="if(searchResults.length > 0) addItem(searchResults[0])"
                                            placeholder="Digite para buscar... (Enter para adicionar o primeiro)"
                                            style="width: 100%; padding: 1rem 1rem 1rem 3rem; font-size: 1.125rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; outline: none;"
                                            onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                            autofocus
                                        >
                                        <div style="position: absolute; top: 50%; left: 1rem; transform: translateY(-50%); pointer-events: none;">
                                            <svg style="height: 1.5rem; width: 1.5rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Resultados da busca -->
                                    <div x-show="searchResults.length > 0" x-cloak 
                                         style="position: absolute; z-index: 20; margin-top: 0.5rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border-radius: 0.75rem; border: 1px solid #e5e7eb; max-height: 20rem; overflow: auto;">
                                        <template x-for="product in searchResults" :key="product.id">
                                            <button 
                                                type="button"
                                                @click="addItem(product)"
                                                style="width: 100%; padding: 0.75rem 1rem; text-align: left; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f3f4f6;"
                                                onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'"
                                            >
                                                <div>
                                                    <span style="font-weight: 600; color: #111827;" x-text="product.name"></span>
                                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                                                        <span style="font-size: 0.75rem; padding: 0.125rem 0.5rem; background: #f3f4f6; color: #4b5563; border-radius: 0.25rem;" x-text="product.sku"></span>
                                                        <span style="font-size: 0.75rem; color: #6b7280;" x-text="product.stock + ' em estoque'"></span>
                                                    </div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <span style="font-size: 1.125rem; font-weight: 700; color: #111827;" x-text="product.formatted_price"></span>
                                                    <div x-show="product.cost_price > 0" style="font-size: 0.6875rem; color: #6b7280;">
                                                        Custo: <span x-text="formatMoney(product.cost_price)"></span>
                                                    </div>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Lista de itens adicionados -->
                                <div style="margin-top: 1.5rem;">
                                    <div x-show="items.length === 0" style="text-align: center; padding: 3rem 1rem; border: 2px dashed #e5e7eb; border-radius: 0.75rem;">
                                        <svg style="margin: 0 auto; height: 3rem; width: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <p style="margin-top: 1rem; color: #6b7280;">Nenhum produto adicionado</p>
                                        <p style="font-size: 0.875rem; color: #9ca3af;">Use o campo acima para buscar e adicionar produtos</p>
                                    </div>
                                    
                                    <div x-show="items.length > 0">
                                        <template x-for="(item, index) in items" :key="index">
                                            <div style="padding: 1rem; background: #f9fafb; border-radius: 0.75rem; border: 1px solid #e5e7eb; margin-bottom: 0.75rem;">
                                                <div style="display: flex; align-items: center; gap: 1rem;">
                                                <div style="flex: 1; min-width: 0;">
                                                    <p style="font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="item.name"></p>
                                                    <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.id">
                                                </div>
                                                
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <button type="button" @click="item.quantity = Math.max(1, item.quantity - 1); updateTotals()" 
                                                            style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;">
                                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                    </button>
                                                    <input 
                                                        type="number" 
                                                        :name="'items['+index+'][quantity]'"
                                                        x-model.number="item.quantity"
                                                        @input="updateTotals"
                                                        min="1"
                                                        :max="item.stock"
                                                        style="width: 4rem; text-align: center; font-weight: 700; font-size: 1.125rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; padding: 0.25rem;"
                                                    >
                                                    <button type="button" @click="item.quantity = Math.min(item.stock, item.quantity + 1); updateTotals()" 
                                                            style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;">
                                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    </button>
                                                </div>
                                                
                                                <div style="width: 7rem; text-align: right;">
                                                    <input 
                                                        type="number" 
                                                        :name="'items['+index+'][unit_price]'"
                                                        x-model.number="item.price"
                                                        @input="updateTotals"
                                                        step="0.01"
                                                        min="0"
                                                        style="width: 100%; text-align: right; font-weight: 600; border: 2px solid #e5e7eb; border-radius: 0.5rem; padding: 0.25rem 0.5rem;"
                                                    >
                                                </div>
                                                
                                                <div style="width: 7rem; text-align: right;">
                                                    <span style="font-size: 1.125rem; font-weight: 700; color: #111827;" x-text="formatMoney(item.quantity * item.price)"></span>
                                                </div>
                                                
                                                <button type="button" @click="removeItem(index)" 
                                                        style="padding: 0.5rem; color: #ef4444; border-radius: 0.5rem; cursor: pointer; background: none; border: none;"
                                                        onmouseover="this.style.backgroundColor='#fef2f2'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                                </div>
                                                {{-- Alerta de preço abaixo do custo --}}
                                                <div x-show="item.cost_price > 0 && item.price < item.cost_price"
                                                     style="margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem; display: flex; align-items: center; gap: 0.375rem;">
                                                    <svg style="width: 1rem; height: 1rem; color: #dc2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                    </svg>
                                                    <span style="font-size: 0.75rem; color: #dc2626; font-weight: 600;">
                                                        Preco abaixo do custo! Custo: <span x-text="formatMoney(item.cost_price)"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- TRADE-IN (Aparelho como entrada) -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="background: #7c3aed; color: white; padding: 0.75rem 1.5rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center;">
                                        <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span style="font-weight: 600;">Aparelho como Entrada (Trade-in)</span>
                                    </div>
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" x-model="hasTradeIn" @change="if(!hasTradeIn) clearTradeIn()" style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;">
                                        <span style="font-size: 0.875rem;">Ativar</span>
                                    </label>
                                </div>
                            </div>
                            <div x-show="hasTradeIn" x-collapse style="padding: 1.5rem;">
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                                    <div style="grid-column: span 2;">
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">
                                            Nome/Descrição do Aparelho <span style="color: #dc2626;">*</span>
                                        </label>
                                        <input type="text" name="trade_in[device_name]" x-model="tradeIn.device_name" 
                                               placeholder="Ex: iPhone 13 Pro Max Azul"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Modelo</label>
                                        <input type="text" name="trade_in[device_model]" x-model="tradeIn.device_model" 
                                               placeholder="Ex: A2643"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">IMEI</label>
                                        <input type="text" name="trade_in[imei]" x-model="tradeIn.imei" 
                                               placeholder="Ex: 123456789012345"
                                               style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Condição</label>
                                        <select name="trade_in[condition]" x-model="tradeIn.condition"
                                                style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                            @foreach($tradeInConditions as $condition)
                                                <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">
                                            Valor Negociado <span style="color: #dc2626;">*</span>
                                        </label>
                                        <div style="position: relative;">
                                            <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;">R$</span>
                                            <input type="number" name="trade_in[estimated_value]" x-model.number="tradeIn.estimated_value" 
                                                   @input="updateTotals" step="0.01" min="0"
                                                   style="width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                                   onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                        </div>
                                    </div>
                                    <div style="grid-column: span 2;">
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Observações sobre o aparelho</label>
                                        <textarea name="trade_in[notes]" x-model="tradeIn.notes" rows="2"
                                                  placeholder="Estado da tela, bateria, acessórios inclusos..."
                                                  style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                                                  onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                                    </div>
                                </div>
                                <div style="margin-top: 1rem; padding: 0.75rem; background: #f5f3ff; border-radius: 0.5rem; border: 1px solid #ddd6fe;">
                                    <p style="font-size: 0.75rem; color: #5b21b6;">
                                        <strong>Nota:</strong> O aparelho ficará pendente para cadastro no estoque após a venda ser finalizada.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Observações -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 1.5rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Observações (opcional)</label>
                            <textarea name="notes" rows="2" 
                                      placeholder="Anotações sobre a venda..."
                                      style="width: 100%; border: 2px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem; outline: none; resize: vertical;"
                                      onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                        </div>
                    </div>
                    
                    <!-- COLUNA LATERAL - RESUMO -->
                    <div class="sale-sidebar">
                        
                        <!-- PASSO 2: CLIENTE -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; position: relative; z-index: 30;">
                            <div style="background: #374151; color: white; padding: 0.75rem 1.5rem; border-radius: 1rem 1rem 0 0;">
                                <div style="display: flex; align-items: center;">
                                    <span style="width: 1.5rem; height: 1.5rem; background: white; color: #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; margin-right: 0.5rem;">2</span>
                                    <span style="font-weight: 600;">Cliente (opcional)</span>
                                </div>
                            </div>
                            <div style="padding: 1rem;">
                                <div style="position: relative;">
                                    <input 
                                        type="text" 
                                        x-model="customerSearch"
                                        @input.debounce.300ms="searchCustomers"
                                        placeholder="Buscar cliente por nome ou telefone..."
                                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; outline: none;"
                                        onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                    >
                                    <input type="hidden" name="customer_id" x-model="selectedCustomer.id">
                                    
                                    <div x-show="customerResults.length > 0" x-cloak 
                                         style="position: absolute; z-index: 50; margin-top: 0.5rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border-radius: 0.75rem; border: 1px solid #e5e7eb; max-height: 12rem; overflow: auto;">
                                        <template x-for="customer in customerResults" :key="customer.id">
                                            <button type="button" @click="selectCustomer(customer)"
                                                    style="width: 100%; padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #f3f4f6; cursor: pointer; background: white;"
                                                    onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                                <span style="font-weight: 500; color: #111827;" x-text="customer.name"></span>
                                                <span style="font-size: 0.875rem; color: #6b7280; display: block;" x-text="customer.phone"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                
                                <div x-show="selectedCustomer.id" style="margin-top: 0.75rem; padding: 0.75rem; background: #f3f4f6; border-radius: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <p style="font-weight: 500; color: #111827;" x-text="selectedCustomer.name"></p>
                                        <p style="font-size: 0.875rem; color: #6b7280;" x-text="selectedCustomer.phone"></p>
                                    </div>
                                    <button type="button" @click="clearCustomer" style="padding: 0.25rem; color: #9ca3af; cursor: pointer; background: none; border: none;">
                                        <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PASSO 3: PAGAMENTO MISTO -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="background: #374151; color: white; padding: 0.75rem 1.5rem;">
                                <div style="display: flex; align-items: center;">
                                    <span style="width: 1.5rem; height: 1.5rem; background: white; color: #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; margin-right: 0.5rem;">3</span>
                                    <span style="font-weight: 600;">Pagamento</span>
                                </div>
                            </div>
                            <div style="padding: 1rem;">
                                <!-- Entrada à Vista -->
                                <div style="margin-bottom: 1rem; padding: 1rem; background: #f0fdf4; border-radius: 0.75rem; border: 1px solid #bbf7d0;">
                                    <label style="display: flex; align-items: center; font-size: 0.875rem; font-weight: 600; color: #166534; margin-bottom: 0.75rem;">
                                        <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Entrada à Vista
                                    </label>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Valor</label>
                                            <div style="position: relative;">
                                                <span style="position: absolute; left: 0.5rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 0.75rem;">R$</span>
                                                <input type="number" name="cash_payment" x-model.number="cashPayment" @input="updateTotals"
                                                       step="0.01" min="0" placeholder="0,00"
                                                       style="width: 100%; padding: 0.5rem 0.5rem 0.5rem 2rem; border: 1px solid #86efac; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                            </div>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Forma</label>
                                            <select name="cash_payment_method" x-model="cashPaymentMethod"
                                                    style="width: 100%; padding: 0.5rem; border: 1px solid #86efac; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                                <option value="">-</option>
                                                <option value="cash">Dinheiro</option>
                                                <option value="pix">PIX</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Parcelamentos em Cartões (Múltiplos) -->
                                <div style="margin-bottom: 1rem; padding: 1rem; background: #eff6ff; border-radius: 0.75rem; border: 1px solid #bfdbfe;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                        <label style="display: flex; align-items: center; font-size: 0.875rem; font-weight: 600; color: #1e40af;">
                                            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Parcelamento em Cartão
                                        </label>
                                        <button type="button" @click="addCardPayment()" 
                                                style="display: flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: #3b82f6; color: white; border-radius: 0.375rem; font-size: 0.75rem; border: none; cursor: pointer;">
                                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Adicionar Cartão
                                        </button>
                                    </div>
                                    
                                    <!-- Lista de cartões -->
                                    <template x-for="(card, cardIndex) in cardPayments" :key="cardIndex">
                                        <div style="background: white; border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 0.5rem; border: 1px solid #dbeafe;">
                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                                                <span style="font-size: 0.75rem; font-weight: 600; color: #3b82f6;" x-text="'Cartão ' + (cardIndex + 1)"></span>
                                                <button type="button" @click="removeCardPayment(cardIndex)" 
                                                        style="padding: 0.125rem; color: #ef4444; background: none; border: none; cursor: pointer;">
                                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                                <div>
                                                    <label style="display: block; font-size: 0.7rem; color: #6b7280; margin-bottom: 0.125rem;">Valor</label>
                                                    <div style="position: relative;">
                                                        <span style="position: absolute; left: 0.5rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 0.7rem;">R$</span>
                                                        <input type="number" :name="'card_payments['+cardIndex+'][amount]'" 
                                                               x-model.number="card.amount" @input="updateTotals"
                                                               step="0.01" min="0" placeholder="0,00"
                                                               style="width: 100%; padding: 0.375rem 0.375rem 0.375rem 1.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label style="display: block; font-size: 0.7rem; color: #6b7280; margin-bottom: 0.125rem;">Parcelas</label>
                                                    <select :name="'card_payments['+cardIndex+'][installments]'" 
                                                            x-model.number="card.installments"
                                                            style="width: 100%; padding: 0.375rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                                        @for($i = 1; $i <= 12; $i++)
                                                            <option value="{{ $i }}">{{ $i }}x</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div x-show="card.amount > 0 && card.installments > 1" 
                                                 style="margin-top: 0.375rem; font-size: 0.7rem; color: #6b7280; text-align: right;">
                                                <span x-text="card.installments + 'x de ' + formatMoney(card.amount / card.installments)"></span>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <div x-show="cardPayments.length === 0" style="text-align: center; padding: 1rem; color: #6b7280; font-size: 0.75rem;">
                                        Clique em "Adicionar Cartão" para parcelar
                                    </div>
                                </div>
                                
                                <!-- Campos ocultos para compatibilidade -->
                                <input type="hidden" name="payment_method" :value="getMainPaymentMethod()">
                                <input type="hidden" name="card_payment" :value="totalCardPayments">
                                <input type="hidden" name="installments" :value="cardPayments.length > 0 ? cardPayments[0].installments : 1">
                                <input type="hidden" name="trade_in_value" :value="hasTradeIn ? tradeIn.estimated_value : 0">
                                
                                <!-- Status -->
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Status</label>
                                    <select name="payment_status" required style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; outline: none; background: white;">
                                        @foreach($paymentStatuses as $status)
                                            <option value="{{ $status->value }}" {{ $status->value === 'paid' ? 'selected' : '' }}>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- RESUMO FINAL -->
                        <div style="background: #111827; color: white; border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.25); overflow: hidden;">
                            <div style="padding: 1.5rem;">
                                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Resumo da Venda</h3>
                                
                                <dl style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; color: #d1d5db;">
                                        <dt>Itens</dt>
                                        <dd x-text="items.length + ' produto(s)'"></dd>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; color: #d1d5db;">
                                        <dt>Subtotal</dt>
                                        <dd x-text="formatMoney(subtotal)"></dd>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <dt style="color: #d1d5db;">Desconto</dt>
                                        <dd>
                                            <input 
                                                type="number" 
                                                name="discount"
                                                x-model.number="discount"
                                                @input="updateTotals"
                                                step="0.01"
                                                min="0"
                                                style="width: 5rem; text-align: right; padding: 0.25rem 0.5rem; background: #1f2937; border: 1px solid #374151; border-radius: 0.5rem; color: white; outline: none; font-size: 0.875rem;"
                                            >
                                        </dd>
                                    </div>
                                    
                                    <div style="padding-top: 0.75rem; margin-top: 0.5rem; border-top: 1px solid #374151;">
                                        <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700;">
                                            <dt>TOTAL</dt>
                                            <dd x-text="formatMoney(total)"></dd>
                                        </div>
                                    </div>
                                    
                                    <!-- Breakdown do pagamento -->
                                    <div x-show="hasTradeIn || cashPayment > 0 || totalCardPayments > 0" 
                                         style="padding-top: 0.75rem; margin-top: 0.5rem; border-top: 1px solid #374151;">
                                        <div style="font-size: 0.75rem; font-weight: 600; color: #9ca3af; margin-bottom: 0.5rem;">FORMA DE PAGAMENTO</div>
                                        <div x-show="hasTradeIn && tradeIn.estimated_value > 0" style="display: flex; justify-content: space-between; color: #a78bfa; font-size: 0.875rem;">
                                            <dt>Trade-in (aparelho)</dt>
                                            <dd x-text="formatMoney(tradeIn.estimated_value || 0)"></dd>
                                        </div>
                                        <div x-show="cashPayment > 0" style="display: flex; justify-content: space-between; color: #86efac; font-size: 0.875rem;">
                                            <dt x-text="'Entrada (' + (cashPaymentMethod === 'pix' ? 'PIX' : 'Dinheiro') + ')'"></dt>
                                            <dd x-text="formatMoney(cashPayment)"></dd>
                                        </div>
                                        <template x-for="(card, idx) in cardPayments" :key="idx">
                                            <div x-show="card.amount > 0" style="display: flex; justify-content: space-between; color: #93c5fd; font-size: 0.875rem;">
                                                <dt x-text="'Cartão ' + (idx + 1) + ' (' + card.installments + 'x)'"></dt>
                                                <dd x-text="formatMoney(card.amount)"></dd>
                                            </div>
                                        </template>
                                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #374151;"
                                             :style="{ color: paymentDifference === 0 ? '#4ade80' : '#fbbf24' }">
                                            <dt>Soma dos pagamentos</dt>
                                            <dd x-text="formatMoney(totalPayments)"></dd>
                                        </div>
                                        
                                        <!-- Alerta de diferença -->
                                        <div x-show="items.length > 0 && Math.abs(paymentDifference) > 0.01" 
                                             style="margin-top: 0.5rem; padding: 0.5rem; border-radius: 0.375rem;"
                                             :style="{ background: paymentDifference > 0 ? '#7f1d1d' : '#713f12' }">
                                            <p style="font-size: 0.75rem;" :style="{ color: paymentDifference > 0 ? '#fecaca' : '#fef08a' }">
                                                <span x-show="paymentDifference > 0">
                                                    <strong>Faltam:</strong> <span x-text="formatMoney(paymentDifference)"></span>
                                                </span>
                                                <span x-show="paymentDifference < 0">
                                                    <strong>Excedente:</strong> <span x-text="formatMoney(Math.abs(paymentDifference))"></span>
                                                </span>
                                            </p>
                                        </div>
                                        
                                        <!-- Indicador de pagamento correto -->
                                        <div x-show="items.length > 0 && Math.abs(paymentDifference) <= 0.01 && totalPayments > 0" 
                                             style="margin-top: 0.5rem; padding: 0.5rem; background: #14532d; border-radius: 0.375rem;">
                                            <p style="font-size: 0.75rem; color: #86efac; display: flex; align-items: center; gap: 0.25rem;">
                                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Pagamento correto!
                                            </p>
                                        </div>
                                    </div>
                                </dl>
                            </div>
                            
                            <button 
                                type="submit" 
                                :disabled="!canSubmit"
                                class="sale-submit-btn"
                                :class="{ 'sale-submit-btn-disabled': !canSubmit, 'sale-submit-btn-active': canSubmit }"
                            >
                                <span x-show="canSubmit" class="flex items-center justify-center gap-3">
                                    <span class="sale-btn-icon-wrapper">
                                        <svg class="sale-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span class="sale-btn-text">FINALIZAR VENDA</span>
                                </span>
                                <span x-show="!canSubmit" class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <style>
        .sale-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        .sale-main {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .sale-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .sale-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        [x-cloak] { display: none !important; }
        
        /* Botão Finalizar Venda */
        .sale-submit-btn {
            width: 100%;
            padding: 1.25rem 1.5rem;
            font-size: 1.125rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sale-submit-btn-disabled {
            background: #374151;
            color: #9ca3af;
            cursor: not-allowed;
        }
        
        .sale-submit-btn-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        
        .sale-submit-btn-active:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
            transform: translateY(-2px);
        }
        
        .sale-submit-btn-active:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.4);
        }
        
        .sale-btn-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: pulse-ring 2s ease-out infinite;
        }
        
        .sale-btn-icon {
            width: 1.25rem;
            height: 1.25rem;
        }
        
        .sale-btn-text {
            letter-spacing: 0.05em;
        }
        
        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }
        
        /* Efeito shimmer no botão */
        .sale-submit-btn-active::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }
    </style>

    @push('scripts')
    <script>
        function saleForm() {
            return {
                items: [],
                searchTerm: '',
                searchResults: [],
                customerSearch: '',
                customerResults: [],
                selectedCustomer: { id: '', name: '', phone: '' },
                discount: 0,
                subtotal: 0,
                total: 0,
                
                // Pagamento
                cashPayment: 0,
                cashPaymentMethod: '',
                
                // Múltiplos cartões
                cardPayments: [],
                
                // Trade-in
                hasTradeIn: false,
                tradeIn: {
                    device_name: '',
                    device_model: '',
                    imei: '',
                    estimated_value: 0,
                    condition: 'good',
                    notes: ''
                },
                
                // Computed: total de pagamentos em cartão
                get totalCardPayments() {
                    return this.cardPayments.reduce((sum, card) => sum + (parseFloat(card.amount) || 0), 0);
                },
                
                // Computed: total de todos os pagamentos
                get totalPayments() {
                    let total = 0;
                    if (this.hasTradeIn && this.tradeIn.estimated_value) {
                        total += parseFloat(this.tradeIn.estimated_value) || 0;
                    }
                    total += parseFloat(this.cashPayment) || 0;
                    total += this.totalCardPayments;
                    return total;
                },
                
                // Computed: diferença entre total e pagamentos
                get paymentDifference() {
                    return this.total - this.totalPayments;
                },
                
                // Computed: pode submeter o formulário
                get canSubmit() {
                    // Precisa ter itens
                    if (this.items.length === 0) return false;
                    
                    // Se tem total, precisa ter pagamentos que fechem
                    if (this.total > 0) {
                        // Verifica se a diferença é menor que 1 centavo
                        return Math.abs(this.paymentDifference) <= 0.01;
                    }
                    
                    return true;
                },
                
                // Adicionar novo cartão
                addCardPayment() {
                    this.cardPayments.push({
                        amount: 0,
                        installments: 1
                    });
                },
                
                // Remover cartão
                removeCardPayment(index) {
                    this.cardPayments.splice(index, 1);
                    this.updateTotals();
                },
                
                // Método de pagamento principal (para compatibilidade)
                getMainPaymentMethod() {
                    if (this.totalCardPayments > 0) return 'credit_card';
                    if (this.cashPaymentMethod === 'pix') return 'pix';
                    if (this.cashPayment > 0) return 'cash';
                    return 'cash';
                },
                
                // Texto do botão quando desabilitado
                getSubmitButtonText() {
                    if (this.items.length === 0) {
                        return 'Adicione produtos para continuar';
                    }
                    if (this.paymentDifference > 0.01) {
                        return 'Faltam ' + this.formatMoney(this.paymentDifference);
                    }
                    if (this.paymentDifference < -0.01) {
                        return 'Excedente de ' + this.formatMoney(Math.abs(this.paymentDifference));
                    }
                    return 'Verifique os dados';
                },
                
                async searchProducts() {
                    if (this.searchTerm.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    
                    const response = await fetch(`{{ route('products.search') }}?q=${encodeURIComponent(this.searchTerm)}`);
                    this.searchResults = await response.json();
                },
                
                addItem(product) {
                    const existing = this.items.find(i => i.id === product.id);
                    if (existing) {
                        if (existing.quantity < existing.stock) {
                            existing.quantity++;
                        }
                    } else {
                        this.items.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            cost_price: product.cost_price || 0,
                            quantity: 1,
                            stock: product.stock
                        });
                    }
                    this.searchTerm = '';
                    this.searchResults = [];
                    this.updateTotals();
                },
                
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.updateTotals();
                },
                
                async searchCustomers() {
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }
                    
                    const response = await fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(this.customerSearch)}`);
                    this.customerResults = await response.json();
                },
                
                selectCustomer(customer) {
                    this.selectedCustomer = customer;
                    this.customerSearch = '';
                    this.customerResults = [];
                },
                
                clearCustomer() {
                    this.selectedCustomer = { id: '', name: '', phone: '' };
                },
                
                clearTradeIn() {
                    this.tradeIn = {
                        device_name: '',
                        device_model: '',
                        imei: '',
                        estimated_value: 0,
                        condition: 'good',
                        notes: ''
                    };
                    this.updateTotals();
                },
                
                updateTotals() {
                    this.subtotal = this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                    this.total = Math.max(0, this.subtotal - this.discount);
                },
                
                formatMoney(value) {
                    return 'R$ ' + (parseFloat(value) || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
