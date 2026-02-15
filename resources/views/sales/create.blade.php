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

            @if(isset($reservation) && $reservation)
                <div style="background: #eff6ff; border: 1px solid #93c5fd; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <div>
                        <span style="font-weight: 600; color: #1d4ed8;">Convertendo Reserva #{{ $reservation->reservation_number }}</span>
                        <span style="font-size: 0.875rem; color: #6b7280; margin-left: 0.5rem;">
                            O sinal de {{ $reservation->formatted_deposit_paid }} será aplicado como desconto.
                        </span>
                    </div>
                </div>
            @endif

            <div x-data="saleForm()" @keydown.escape.window="if(!showCustomerModal && !showProductModal) window.location.href='{{ route('sales.index') }}'">
            <form method="POST" action="{{ route('sales.store') }}" @submit="handleSubmit($event)">
                @csrf
                @if(isset($reservation) && $reservation)
                    <input type="hidden" name="from_reservation" value="{{ $reservation->id }}">
                @endif
                
                <div class="sale-grid">
                    <!-- COLUNA PRINCIPAL - PRODUTOS -->
                    <div class="sale-main">
                        
                        <!-- PASSO 1: BUSCAR PRODUTO -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="background: #111827; color: white; padding: 1rem 1.5rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center;">
                                        <span style="width: 2rem; height: 2rem; background: white; color: #111827; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 0.75rem;">1</span>
                                        <span style="font-size: 1.125rem; font-weight: 600;">Adicionar Produtos</span>
                                    </div>
                                    <button type="button" @click="showProductModal = true; productForm = resetProductForm()"
                                            style="display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 500; cursor: pointer;"
                                            onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Cadastrar Produto
                                    </button>
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
                                    <div x-show="searchResults.length > 0 || (searchTerm.length >= 2 && searchResults.length === 0 && !searchLoading)" x-cloak 
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
                                                        <span style="font-size: 0.75rem;" :style="{ color: product.stock > 0 ? '#6b7280' : '#ea580c', fontWeight: product.stock <= 0 ? '600' : '400' }" x-text="product.stock > 0 ? product.stock + ' em estoque' : 'Sem estoque'"></span>
                                                    </div>
                                                </div>
                                                <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        </template>
                                        <!-- Opção de cadastrar quando não encontra -->
                                        <div x-show="searchTerm.length >= 2 && searchResults.length === 0 && !searchLoading"
                                             style="padding: 1rem; text-align: center; border-top: 1px solid #f3f4f6;">
                                            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem;">Nenhum produto encontrado para "<span x-text="searchTerm" style="font-weight: 600;"></span>"</p>
                                            <button type="button" 
                                                    @click="productForm = resetProductForm(); productForm.name = searchTerm; showProductModal = true; searchResults = [];"
                                                    style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;"
                                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Cadastrar Produto Agora
                                            </button>
                                        </div>
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
                                                {{-- Linha 1: Nome, Quantidade, Venda, Subtotal, Remover --}}
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
                                                            :max="Math.max(1, item.stock)"
                                                            style="width: 4rem; text-align: center; font-weight: 700; font-size: 1.125rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; padding: 0.25rem;"
                                                        >
                                                        <button type="button" @click="item.quantity = Math.min(Math.max(1, item.stock), item.quantity + 1); updateTotals()" 
                                                                style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;">
                                                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        </button>
                                                    </div>
                                                    
                                                    <div style="width: 7rem; text-align: right;">
                                                        <label style="display: block; font-size: 0.625rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.125rem;">Venda</label>
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

                                                {{-- Linha 2: Custo, Origem, Frete --}}
                                                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb; display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 0.75rem; align-items: end;">
                                                    <div>
                                                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                                            Custo (R$) <span style="color: #dc2626;">*</span>
                                                        </label>
                                                        <input type="number" 
                                                               :name="'items['+index+'][cost_price]'"
                                                               x-model.number="item.cost_price"
                                                               @input="updateTotals"
                                                               step="0.01" min="0" placeholder="0.00"
                                                               style="width: 100%; padding: 0.375rem 0.5rem; border: 2px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; outline: none;"
                                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                                    </div>
                                                    <div>
                                                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Origem</label>
                                                        <select :name="'items['+index+'][supplier_origin]'"
                                                                x-model="item.supplier_origin"
                                                                @change="updateTotals"
                                                                style="width: 100%; padding: 0.375rem 0.5rem; border: 2px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; background: white; outline: none;">
                                                            <option value="">-</option>
                                                            <option value="br">BR (Brasil)</option>
                                                            <option value="py">PY (Paraguai)</option>
                                                        </select>
                                                    </div>
                                                    <div x-show="item.supplier_origin">
                                                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Tipo Frete</label>
                                                        <select :name="'items['+index+'][freight_type]'"
                                                                x-model="item.freight_type"
                                                                @change="updateTotals"
                                                                style="width: 100%; padding: 0.375rem 0.5rem; border: 2px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; background: white; outline: none;">
                                                            <option value="">Sem frete</option>
                                                            <option value="percentage">% sobre custo</option>
                                                            <option value="fixed">Valor fixo (R$)</option>
                                                        </select>
                                                    </div>
                                                    <div x-show="item.supplier_origin && item.freight_type">
                                                        <label style="display: block; font-size: 0.6875rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;" 
                                                               x-text="item.freight_type === 'percentage' ? 'Frete (%)' : 'Frete (R$)'">Frete</label>
                                                        <input type="number" 
                                                               :name="'items['+index+'][freight_value]'"
                                                               x-model.number="item.freight_value"
                                                               @input="updateTotals"
                                                               step="0.01" min="0" placeholder="0.00"
                                                               style="width: 100%; padding: 0.375rem 0.5rem; border: 2px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; outline: none;"
                                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                                    </div>
                                                </div>

                                                {{-- Linha 3: Resumo de custo/lucro --}}
                                                <div x-show="item.cost_price > 0" 
                                                     style="margin-top: 0.5rem; display: flex; gap: 1rem; font-size: 0.75rem; color: #6b7280;">
                                                    <span x-show="getItemFreightAmount(item) > 0">
                                                        Frete: <span style="font-weight: 600;" x-text="formatMoney(getItemFreightAmount(item))"></span>
                                                    </span>
                                                    <span x-show="getItemFreightAmount(item) > 0">
                                                        Custo real: <span style="font-weight: 600;" x-text="formatMoney(getItemTotalCost(item))"></span>
                                                    </span>
                                                    <span :style="{ color: getItemProfit(item) >= 0 ? '#16a34a' : '#dc2626', fontWeight: '600' }">
                                                        Lucro: <span x-text="formatMoney(getItemProfit(item))"></span>
                                                    </span>
                                                </div>

                                                {{-- Alerta de sem estoque --}}
                                                <div x-show="item.stock <= 0"
                                                     style="margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 0.375rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                                    <div style="display: flex; align-items: center; gap: 0.375rem;">
                                                        <svg style="width: 1rem; height: 1rem; color: #ea580c; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                        </svg>
                                                        <span style="font-size: 0.75rem; color: #9a3412; font-weight: 600;">
                                                            Sem estoque disponível!
                                                        </span>
                                                    </div>
                                                    <button type="button" @click="openStockModal(index)"
                                                            style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; background: #ea580c; color: white; border: none; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 600; cursor: pointer; white-space: nowrap;"
                                                            onmouseover="this.style.background='#c2410c'" onmouseout="this.style.background='#ea580c'">
                                                        <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                        Adicionar Estoque
                                                    </button>
                                                </div>
                                                {{-- Alerta de preço abaixo do custo total --}}
                                                <div x-show="item.cost_price > 0 && item.price > 0 && item.price < getItemTotalCost(item)"
                                                     style="margin-top: 0.5rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem; display: flex; align-items: center; gap: 0.375rem;">
                                                    <svg style="width: 1rem; height: 1rem; color: #dc2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                    </svg>
                                                    <span style="font-size: 0.75rem; color: #dc2626; font-weight: 600;">
                                                        Preço abaixo do custo total! Custo: <span x-text="formatMoney(getItemTotalCost(item))"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- TRADE-IN (Aparelhos como entrada) -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="background: #7c3aed; color: white; padding: 0.75rem 1.5rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center;">
                                        <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span style="font-weight: 600;">Aparelhos como Entrada (Trade-in)</span>
                                    </div>
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" x-model="hasTradeIn" @change="toggleTradeIn()" style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;">
                                        <span style="font-size: 0.875rem;">Ativar</span>
                                    </label>
                                </div>
                            </div>
                            <div x-show="hasTradeIn" x-collapse style="padding: 1.5rem;">
                                <!-- Lista de aparelhos -->
                                <template x-for="(ti, tiIndex) in tradeIns" :key="tiIndex">
                                    <div style="background: #faf5ff; border: 1px solid #ddd6fe; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; position: relative;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                            <span style="font-size: 0.8125rem; font-weight: 600; color: #7c3aed;" x-text="'Aparelho ' + (tiIndex + 1)"></span>
                                            <button type="button" @click="removeTradeIn(tiIndex)" 
                                                    x-show="tradeIns.length > 1"
                                                    style="padding: 0.25rem; color: #ef4444; background: none; border: none; cursor: pointer;"
                                                    title="Remover aparelho">
                                                <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                                            <div style="grid-column: span 2;">
                                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">
                                                    Nome/Descrição <span style="color: #dc2626;">*</span>
                                                </label>
                                                <input type="text" :name="'trade_ins['+tiIndex+'][device_name]'" x-model="ti.device_name" 
                                                       placeholder="Ex: iPhone 13 Pro Max Azul"
                                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                                       onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Modelo</label>
                                                <input type="text" :name="'trade_ins['+tiIndex+'][device_model]'" x-model="ti.device_model" 
                                                       placeholder="Ex: A2643"
                                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                                       onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">IMEI</label>
                                                <input type="text" :name="'trade_ins['+tiIndex+'][imei]'" x-model="ti.imei" 
                                                       placeholder="Ex: 123456789012345"
                                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                                       onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Condição</label>
                                                <select :name="'trade_ins['+tiIndex+'][condition]'" x-model="ti.condition"
                                                        style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                                    @foreach($tradeInConditions as $condition)
                                                        <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">
                                                    Valor Negociado <span style="color: #dc2626;">*</span>
                                                </label>
                                                <div style="position: relative;">
                                                    <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;">R$</span>
                                                    <input type="number" :name="'trade_ins['+tiIndex+'][estimated_value]'" x-model.number="ti.estimated_value" 
                                                           @input="updateTotals" step="0.01" min="0"
                                                           style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.5rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                                           onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                                </div>
                                            </div>
                                            <div style="grid-column: span 2;">
                                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Observações</label>
                                                <textarea :name="'trade_ins['+tiIndex+'][notes]'" x-model="ti.notes" rows="2"
                                                          placeholder="Estado da tela, bateria, acessórios inclusos..."
                                                          style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                                                          onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Botão adicionar aparelho -->
                                <button type="button" @click="addTradeIn()"
                                        style="width: 100%; padding: 0.625rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: #7c3aed; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;"
                                        onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Adicionar outro aparelho
                                </button>

                                <!-- Total dos trade-ins -->
                                <div x-show="tradeIns.length > 1" style="margin-top: 0.75rem; padding: 0.75rem; background: #ede9fe; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.8125rem; font-weight: 600; color: #5b21b6;" x-text="tradeIns.length + ' aparelhos'"></span>
                                    <span style="font-size: 1rem; font-weight: 700; color: #5b21b6;" x-text="'Total: ' + formatMoney(totalTradeInValue)"></span>
                                </div>

                                <div style="margin-top: 0.75rem; padding: 0.75rem; background: #f5f3ff; border-radius: 0.5rem; border: 1px solid #ddd6fe;">
                                    <p style="font-size: 0.75rem; color: #5b21b6;">
                                        <strong>Nota:</strong> Os aparelhos ficarão pendentes para cadastro no estoque após a venda ser finalizada.
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
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center;">
                                        <span style="width: 1.5rem; height: 1.5rem; background: white; color: #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; margin-right: 0.5rem;">2</span>
                                        <span style="font-weight: 600;">Cliente (opcional)</span>
                                    </div>
                                    <button type="button" @click="showCustomerModal = true; customerForm = resetCustomerForm()"
                                            style="display: flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;"
                                            onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Novo
                                    </button>
                                </div>
                            </div>
                            <div style="padding: 1rem;">
                                <div x-show="!selectedCustomer.id" style="position: relative;">
                                    <input 
                                        type="text" 
                                        x-model="customerSearch"
                                        @input.debounce.300ms="searchCustomers"
                                        placeholder="Buscar cliente por nome ou telefone..."
                                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; outline: none;"
                                        onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                    >
                                    <input type="hidden" name="customer_id" x-model="selectedCustomer.id">
                                    
                                    <div x-show="customerResults.length > 0 || (customerSearch.length >= 2 && customerResults.length === 0 && !customerSearchLoading)" x-cloak 
                                         style="position: absolute; z-index: 50; margin-top: 0.5rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border-radius: 0.75rem; border: 1px solid #e5e7eb; max-height: 16rem; overflow: auto;">
                                        <template x-for="customer in customerResults" :key="customer.id">
                                            <button type="button" @click="selectCustomer(customer)"
                                                    style="width: 100%; padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #f3f4f6; cursor: pointer; background: white;"
                                                    onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                                <span style="font-weight: 500; color: #111827;" x-text="customer.name"></span>
                                                <span style="font-size: 0.875rem; color: #6b7280; display: block;" x-text="customer.phone"></span>
                                            </button>
                                        </template>
                                        <!-- Opção de cadastrar quando não encontra -->
                                        <div x-show="customerSearch.length >= 2 && customerResults.length === 0 && !customerSearchLoading"
                                             style="padding: 0.75rem 1rem; text-align: center; border-top: 1px solid #f3f4f6;">
                                            <p style="font-size: 0.8125rem; color: #6b7280; margin-bottom: 0.5rem;">Cliente não encontrado</p>
                                            <button type="button" 
                                                    @click="customerForm = resetCustomerForm(); customerForm.name = customerSearch; showCustomerModal = true; customerResults = [];"
                                                    style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; background: #111827; color: white; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 500; border: none; cursor: pointer;">
                                                <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Cadastrar Agora
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div x-show="selectedCustomer.id" style="margin-top: 0; padding: 0.75rem; background: #f0fdf4; border-radius: 0.75rem; border: 1px solid #bbf7d0; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                                        <div style="width: 2.25rem; height: 2.25rem; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <svg style="width: 1.125rem; height: 1.125rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p style="font-weight: 600; color: #111827; font-size: 0.9375rem;" x-text="selectedCustomer.name"></p>
                                            <p style="font-size: 0.8125rem; color: #6b7280;" x-text="selectedCustomer.phone"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="clearCustomer" style="padding: 0.375rem; color: #9ca3af; cursor: pointer; background: none; border: none; border-radius: 0.375rem;"
                                            onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='none'; this.style.color='#9ca3af'">
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
                                <input type="hidden" name="trade_in_value" :value="hasTradeIn ? totalTradeInValue : 0">
                                
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
                                    <div x-show="totalCost > 0" style="display: flex; justify-content: space-between; color: #9ca3af; font-size: 0.8125rem;">
                                        <dt>Custo total</dt>
                                        <dd x-text="formatMoney(totalCost)"></dd>
                                    </div>
                                    <div x-show="totalFreight > 0" style="display: flex; justify-content: space-between; color: #9ca3af; font-size: 0.8125rem;">
                                        <dt>Frete total</dt>
                                        <dd x-text="formatMoney(totalFreight)"></dd>
                                    </div>
                                    <div x-show="totalCost > 0" style="display: flex; justify-content: space-between; font-size: 0.8125rem;"
                                         :style="{ color: totalProfit >= 0 ? '#4ade80' : '#f87171' }">
                                        <dt>Lucro estimado</dt>
                                        <dd x-text="formatMoney(totalProfit)"></dd>
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
                                        <template x-for="(ti, tiIdx) in tradeIns" :key="tiIdx">
                                            <div x-show="hasTradeIn && ti.estimated_value > 0" style="display: flex; justify-content: space-between; color: #a78bfa; font-size: 0.875rem;">
                                                <dt x-text="'Trade-in ' + (tradeIns.length > 1 ? (tiIdx + 1) + ' ' : '') + '(' + (ti.device_name || 'aparelho') + ')'"></dt>
                                                <dd x-text="formatMoney(ti.estimated_value || 0)"></dd>
                                            </div>
                                        </template>
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
                                :disabled="!canSubmit || submitting"
                                class="sale-submit-btn"
                                :class="{ 'sale-submit-btn-disabled': !canSubmit || submitting, 'sale-submit-btn-active': canSubmit && !submitting }"
                            >
                                <span x-show="submitting" class="flex items-center justify-center gap-3">
                                    <svg class="animate-spin" style="width:1.5rem;height:1.5rem;" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span class="sale-btn-text">PROCESSANDO...</span>
                                </span>
                                <span x-show="canSubmit && !submitting" class="flex items-center justify-center gap-3">
                                    <span class="sale-btn-icon-wrapper">
                                        <svg class="sale-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span class="sale-btn-text">FINALIZAR VENDA</span>
                                </span>
                                <span x-show="!canSubmit && !submitting" class="flex items-center justify-center gap-2">
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

            <!-- MODAL: CADASTRO RÁPIDO DE CLIENTE -->
            <div x-show="showCustomerModal" x-cloak
                 style="position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem;"
                 @keydown.escape.window="showCustomerModal = false">
                <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5);" @click="showCustomerModal = false"></div>
                <div style="position: relative; background: white; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 100%; max-width: 32rem; max-height: 90vh; overflow-y: auto;"
                     @click.stop>
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; background: #f0fdf4; border-radius: 0.625rem; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 1.25rem; height: 1.25rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827;">Cadastro Rápido de Cliente</h3>
                                <p style="font-size: 0.8125rem; color: #6b7280;">O cliente será vinculado automaticamente à venda</p>
                            </div>
                        </div>
                        <button type="button" @click="showCustomerModal = false" style="padding: 0.375rem; color: #9ca3af; cursor: pointer; background: none; border: none; border-radius: 0.375rem;"
                                onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">
                            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div style="padding: 1.5rem;">
                        <div x-show="customerFormError" style="margin-bottom: 1rem; padding: 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem;">
                            <p style="font-size: 0.8125rem; color: #dc2626;" x-text="customerFormError"></p>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Nome <span style="color: #dc2626;">*</span></label>
                                <input type="text" x-model="customerForm.name" x-ref="customerNameInput"
                                       placeholder="Nome completo do cliente"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                       @keydown.enter.prevent="saveCustomer">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Telefone <span style="color: #dc2626;">*</span></label>
                                <input type="text" x-model="customerForm.phone"
                                       placeholder="(00) 00000-0000"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                       @keydown.enter.prevent="saveCustomer">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">CPF</label>
                                <input type="text" x-model="customerForm.cpf"
                                       placeholder="000.000.000-00"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">E-mail</label>
                                <input type="email" x-model="customerForm.email"
                                       placeholder="email@exemplo.com"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                        </div>
                    </div>
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.75rem; background: #f9fafb; border-radius: 0 0 1rem 1rem;">
                        <button type="button" @click="showCustomerModal = false"
                                style="padding: 0.625rem 1.25rem; background: white; color: #374151; font-weight: 500; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                            Cancelar
                        </button>
                        <button type="button" @click="saveCustomer" :disabled="customerFormSaving"
                                style="padding: 0.625rem 1.25rem; background: #111827; color: white; font-weight: 600; border: none; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            <svg x-show="customerFormSaving" style="width: 1rem; height: 1rem; animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="customerFormSaving ? 'Salvando...' : 'Salvar e Vincular'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL: CADASTRO RÁPIDO DE PRODUTO -->
            <div x-show="showProductModal" x-cloak
                 style="position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem;"
                 @keydown.escape.window="showProductModal = false">
                <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5);" @click="showProductModal = false"></div>
                <div style="position: relative; background: white; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 100%; max-width: 40rem; max-height: 90vh; overflow-y: auto;"
                     @click.stop>
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; background: #eff6ff; border-radius: 0.625rem; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 1.25rem; height: 1.25rem; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827;">Cadastro Rápido de Produto</h3>
                                <p style="font-size: 0.8125rem; color: #6b7280;">O produto será adicionado automaticamente à venda</p>
                            </div>
                        </div>
                        <button type="button" @click="showProductModal = false" style="padding: 0.375rem; color: #9ca3af; cursor: pointer; background: none; border: none; border-radius: 0.375rem;"
                                onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">
                            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div style="padding: 1.5rem;">
                        <div x-show="productFormError" style="margin-bottom: 1rem; padding: 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem;">
                            <p style="font-size: 0.8125rem; color: #dc2626;" x-text="productFormError"></p>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Nome <span style="color: #dc2626;">*</span></label>
                                <input type="text" x-model="productForm.name" x-ref="productNameInput"
                                       placeholder="Ex: iPhone 15 Pro Max 256GB"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Categoria <span style="color: #dc2626;">*</span></label>
                                <select x-model="productForm.category" @change="generateProductSku"
                                        style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white; outline: none;">
                                    <option value="">Selecione...</option>
                                    @foreach(\App\Domain\Product\Enums\ProductCategory::grouped() as $group => $items)
                                        <optgroup label="{{ $group }}">
                                            @foreach($items as $category)
                                                <option value="{{ $category->value }}">{{ $category->label() }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Condição <span style="color: #dc2626;">*</span></label>
                                <select x-model="productForm.condition"
                                        style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white; outline: none;">
                                    @foreach(\App\Domain\Product\Enums\ProductCondition::cases() as $condition)
                                        <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">SKU <span style="color: #dc2626;">*</span></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="text" x-model="productForm.sku"
                                           placeholder="Gerado automaticamente"
                                           style="flex: 1; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    <button type="button" @click="generateProductSku" title="Gerar SKU"
                                            style="padding: 0.5rem; background: #f3f4f6; border: 2px solid #e5e7eb; border-radius: 0.5rem; cursor: pointer;">
                                        <svg style="width: 1.125rem; height: 1.125rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">IMEI</label>
                                <input type="text" x-model="productForm.imei"
                                       placeholder="000000000000000"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Quantidade <span style="color: #dc2626;">*</span></label>
                                <input type="number" x-model.number="productForm.stock_quantity" min="1"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Cor</label>
                                <input type="text" x-model="productForm.color"
                                       placeholder="Ex: Preto"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                        </div>
                        <p style="margin-top: 1rem; padding: 0.75rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; font-size: 0.8125rem; color: #1e40af;">
                            Os valores de custo e venda serão informados na hora da venda.
                        </p>
                    </div>
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.75rem; background: #f9fafb; border-radius: 0 0 1rem 1rem;">
                        <button type="button" @click="showProductModal = false"
                                style="padding: 0.625rem 1.25rem; background: white; color: #374151; font-weight: 500; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                            Cancelar
                        </button>
                        <button type="button" @click="saveProduct" :disabled="productFormSaving"
                                style="padding: 0.625rem 1.25rem; background: #111827; color: white; font-weight: 600; border: none; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            <svg x-show="productFormSaving" style="width: 1rem; height: 1rem; animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="productFormSaving ? 'Salvando...' : 'Salvar e Adicionar à Venda'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL: ENTRADA RÁPIDA DE ESTOQUE -->
            <div x-show="showStockModal" x-cloak
                 style="position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem;"
                 @keydown.escape.window="showStockModal = false">
                <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5);" @click="showStockModal = false"></div>
                <div style="position: relative; background: white; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 100%; max-width: 28rem;"
                     @click.stop>
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; background: #fff7ed; border-radius: 0.625rem; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 1.25rem; height: 1.25rem; color: #ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827;">Entrada Rápida de Estoque</h3>
                                <p style="font-size: 0.8125rem; color: #6b7280;" x-text="stockItemName"></p>
                            </div>
                        </div>
                        <button type="button" @click="showStockModal = false" style="padding: 0.375rem; color: #9ca3af; cursor: pointer; background: none; border: none; border-radius: 0.375rem;"
                                onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">
                            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div style="padding: 1.5rem;">
                        <div x-show="stockFormError" style="margin-bottom: 1rem; padding: 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem;">
                            <p style="font-size: 0.8125rem; color: #dc2626;" x-text="stockFormError"></p>
                        </div>
                        <div x-show="stockFormSuccess" style="margin-bottom: 1rem; padding: 0.75rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem;">
                            <p style="font-size: 0.8125rem; color: #166534;" x-text="stockFormSuccess"></p>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Quantidade a adicionar <span style="color: #dc2626;">*</span></label>
                            <input type="number" x-model.number="stockForm.quantity" min="1" x-ref="stockQuantityInput"
                                   @keydown.enter.prevent="saveStock"
                                   style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1.125rem; font-weight: 700; text-align: center; outline: none;"
                                   onfocus="this.style.borderColor='#ea580c'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Motivo <span style="color: #9ca3af;">(opcional)</span></label>
                            <input type="text" x-model="stockForm.reason"
                                   placeholder="Ex: Reposição de estoque"
                                   style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#ea580c'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                        <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; color: #6b7280;">
                                <span>Estoque atual:</span>
                                <span style="font-weight: 600;" x-text="stockCurrentStock + ' un.'"></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; color: #111827; margin-top: 0.25rem;">
                                <span style="font-weight: 600;">Estoque após entrada:</span>
                                <span style="font-weight: 700; color: #16a34a;" x-text="(stockCurrentStock + (stockForm.quantity || 0)) + ' un.'"></span>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.75rem; background: #f9fafb; border-radius: 0 0 1rem 1rem;">
                        <button type="button" @click="showStockModal = false"
                                style="padding: 0.625rem 1.25rem; background: white; color: #374151; font-weight: 500; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                            Cancelar
                        </button>
                        <button type="button" @click="saveStock" :disabled="stockFormSaving"
                                style="padding: 0.625rem 1.25rem; background: #ea580c; color: white; font-weight: 600; border: none; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;"
                                onmouseover="this.style.background='#c2410c'" onmouseout="this.style.background='#ea580c'">
                            <svg x-show="stockFormSaving" style="width: 1rem; height: 1rem; animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="stockFormSaving ? 'Salvando...' : 'Confirmar Entrada'"></span>
                        </button>
                    </div>
                </div>
            </div>

            </div><!-- /x-data wrapper -->
        </div>
    </div>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
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
            const reservationData = {!! json_encode(isset($reservation) && $reservation ? [
                'customer' => $reservation->customer ? [
                    'id' => $reservation->customer->id,
                    'name' => $reservation->customer->name,
                    'phone' => $reservation->customer->formatted_phone ?? '',
                ] : null,
                'product' => $reservation->product ? [
                    'id' => $reservation->product->id,
                    'name' => $reservation->product->full_name ?? $reservation->product->name,
                    'price' => (float) $reservation->product_price,
                    'stock' => $reservation->product->stock_quantity ?? 1,
                ] : null,
                'deposit_paid' => (float) $reservation->deposit_paid,
                'product_price' => (float) $reservation->product_price,
            ] : null) !!};

            return {
                items: [],
                searchTerm: '',
                searchResults: [],
                searchLoading: false,
                customerSearch: '',
                customerResults: [],
                customerSearchLoading: false,
                selectedCustomer: reservationData?.customer ?? { id: '', name: '', phone: '' },
                discount: reservationData?.deposit_paid ?? 0,
                subtotal: 0,
                total: 0,
                
                // Pagamento
                cashPayment: 0,
                cashPaymentMethod: '',
                
                // Múltiplos cartões
                cardPayments: [],
                
                // Trade-ins (múltiplos aparelhos)
                hasTradeIn: false,
                tradeIns: [],

                // Modal de Cliente
                showCustomerModal: false,
                customerForm: { name: '', phone: '', cpf: '', email: '' },
                customerFormError: '',
                customerFormSaving: false,

                // Modal de Produto
                showProductModal: false,
                productForm: { name: '', sku: '', category: 'smartphone', condition: 'new', imei: '', stock_quantity: 1, color: '', min_stock_alert: 1 },
                productFormError: '',
                productFormSaving: false,

                // Submissão
                submitting: false,

                // Modal de Entrada de Estoque
                showStockModal: false,
                stockForm: { quantity: 1, reason: '' },
                stockFormError: '',
                stockFormSuccess: '',
                stockFormSaving: false,
                stockItemIndex: null,
                stockItemName: '',
                stockCurrentStock: 0,

                handleSubmit(event) {
                    if (this.submitting) {
                        event.preventDefault();
                        return;
                    }
                    if (!this.canSubmit) {
                        event.preventDefault();
                        return;
                    }
                    this.submitting = true;
                    // O form será submetido normalmente pelo browser
                },

                init() {
                    // Pré-preencher produto da reserva
                    if (reservationData?.product) {
                        this.items.push({
                            id: reservationData.product.id,
                            name: reservationData.product.name,
                            price: reservationData.product.price,
                            cost_price: 0,
                            supplier_origin: '',
                            freight_type: '',
                            freight_value: 0,
                            quantity: 1,
                            stock: reservationData.product.stock
                        });
                        this.updateTotals();
                    }

                    // Gerar SKU inicial para o form de produto
                    this.generateProductSku();
                },
                
                // Computed: total de pagamentos em cartão
                get totalCardPayments() {
                    return this.cardPayments.reduce((sum, card) => sum + (parseFloat(card.amount) || 0), 0);
                },

                // Computed: total de todos os trade-ins
                get totalTradeInValue() {
                    return this.tradeIns.reduce((sum, ti) => sum + (parseFloat(ti.estimated_value) || 0), 0);
                },
                
                // Computed: total de todos os pagamentos
                get totalPayments() {
                    let total = 0;
                    if (this.hasTradeIn) {
                        total += this.totalTradeInValue;
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
                    if (this.items.length === 0) return false;
                    if (this.total > 0) {
                        return Math.abs(this.paymentDifference) <= 0.01;
                    }
                    return true;
                },
                
                addCardPayment() {
                    this.cardPayments.push({ amount: 0, installments: 1 });
                },
                
                removeCardPayment(index) {
                    this.cardPayments.splice(index, 1);
                    this.updateTotals();
                },
                
                getMainPaymentMethod() {
                    if (this.totalCardPayments > 0) return 'credit_card';
                    if (this.cashPaymentMethod === 'pix') return 'pix';
                    if (this.cashPayment > 0) return 'cash';
                    return 'cash';
                },
                
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
                    
                    this.searchLoading = true;
                    const response = await fetch(`{{ route('products.search') }}?q=${encodeURIComponent(this.searchTerm)}`);
                    this.searchResults = await response.json();
                    this.searchLoading = false;
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
                            price: 0,
                            cost_price: 0,
                            supplier_origin: '',
                            freight_type: '',
                            freight_value: 0,
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
                    
                    this.customerSearchLoading = true;
                    const response = await fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(this.customerSearch)}`);
                    this.customerResults = await response.json();
                    this.customerSearchLoading = false;
                },
                
                selectCustomer(customer) {
                    this.selectedCustomer = customer;
                    this.customerSearch = '';
                    this.customerResults = [];
                },
                
                clearCustomer() {
                    this.selectedCustomer = { id: '', name: '', phone: '' };
                },
                
                toggleTradeIn() {
                    if (this.hasTradeIn && this.tradeIns.length === 0) {
                        this.addTradeIn();
                    }
                    if (!this.hasTradeIn) {
                        this.tradeIns = [];
                    }
                    this.updateTotals();
                },

                addTradeIn() {
                    this.tradeIns.push({
                        device_name: '',
                        device_model: '',
                        imei: '',
                        estimated_value: 0,
                        condition: 'good',
                        notes: ''
                    });
                },

                removeTradeIn(index) {
                    this.tradeIns.splice(index, 1);
                    if (this.tradeIns.length === 0) {
                        this.hasTradeIn = false;
                    }
                    this.updateTotals();
                },
                
                updateTotals() {
                    this.subtotal = this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                    this.total = Math.max(0, this.subtotal - this.discount);
                },

                getItemFreightAmount(item) {
                    if (!item.supplier_origin || !item.freight_type) return 0;
                    if (item.freight_type === 'percentage') {
                        return (parseFloat(item.cost_price) || 0) * ((parseFloat(item.freight_value) || 0) / 100);
                    }
                    if (item.freight_type === 'fixed') {
                        return parseFloat(item.freight_value) || 0;
                    }
                    return 0;
                },

                getItemTotalCost(item) {
                    return (parseFloat(item.cost_price) || 0) + this.getItemFreightAmount(item);
                },

                getItemProfit(item) {
                    return ((parseFloat(item.price) || 0) - this.getItemTotalCost(item)) * (item.quantity || 1);
                },

                get totalCost() {
                    return this.items.reduce((sum, item) => sum + (this.getItemTotalCost(item) * item.quantity), 0);
                },

                get totalFreight() {
                    return this.items.reduce((sum, item) => sum + (this.getItemFreightAmount(item) * item.quantity), 0);
                },

                get totalProfit() {
                    return this.items.reduce((sum, item) => sum + this.getItemProfit(item), 0);
                },
                
                formatMoney(value) {
                    return 'R$ ' + (parseFloat(value) || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                // ========== MODAL CLIENTE ==========
                resetCustomerForm() {
                    return { name: '', phone: '', cpf: '', email: '' };
                },

                async saveCustomer() {
                    this.customerFormError = '';
                    
                    if (!this.customerForm.name || !this.customerForm.phone) {
                        this.customerFormError = 'Nome e telefone são obrigatórios.';
                        return;
                    }

                    this.customerFormSaving = true;

                    try {
                        const response = await fetch('{{ route("customers.store-quick") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.customerForm)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            const errors = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Erro ao salvar cliente.');
                            this.customerFormError = errors;
                            this.customerFormSaving = false;
                            return;
                        }

                        // Selecionar o cliente criado
                        this.selectedCustomer = { id: data.id, name: data.name, phone: data.phone };
                        this.customerSearch = '';
                        this.customerResults = [];
                        this.showCustomerModal = false;
                    } catch (e) {
                        this.customerFormError = 'Erro de conexão. Tente novamente.';
                    }

                    this.customerFormSaving = false;
                },

                // ========== MODAL ESTOQUE ==========
                openStockModal(index) {
                    const item = this.items[index];
                    this.stockItemIndex = index;
                    this.stockItemName = item.name;
                    this.stockCurrentStock = item.stock;
                    this.stockForm = { quantity: 1, reason: '' };
                    this.stockFormError = '';
                    this.stockFormSuccess = '';
                    this.showStockModal = true;
                    this.$nextTick(() => {
                        if (this.$refs.stockQuantityInput) {
                            this.$refs.stockQuantityInput.focus();
                            this.$refs.stockQuantityInput.select();
                        }
                    });
                },

                async saveStock() {
                    this.stockFormError = '';
                    this.stockFormSuccess = '';

                    if (!this.stockForm.quantity || this.stockForm.quantity < 1) {
                        this.stockFormError = 'A quantidade deve ser pelo menos 1.';
                        return;
                    }

                    const item = this.items[this.stockItemIndex];
                    if (!item) {
                        this.stockFormError = 'Produto não encontrado na lista.';
                        return;
                    }

                    this.stockFormSaving = true;

                    try {
                        const response = await fetch('{{ route("stock.store-quick") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                product_id: item.id,
                                quantity: this.stockForm.quantity,
                                reason: this.stockForm.reason || null,
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            const errors = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Erro ao registrar entrada.');
                            this.stockFormError = errors;
                            this.stockFormSaving = false;
                            return;
                        }

                        // Atualizar estoque do item na lista
                        item.stock = data.new_stock;
                        this.stockCurrentStock = data.new_stock;

                        // Fechar modal após sucesso
                        this.showStockModal = false;
                    } catch (e) {
                        this.stockFormError = 'Erro de conexão. Tente novamente.';
                    }

                    this.stockFormSaving = false;
                },

                // ========== MODAL PRODUTO ==========
                resetProductForm() {
                    const form = { name: '', sku: '', category: 'smartphone', condition: 'new', imei: '', stock_quantity: 1, color: '', min_stock_alert: 1 };
                    // Gerar SKU ao resetar
                    this.generateProductSkuFor(form);
                    return form;
                },

                async generateProductSku() {
                    await this.generateProductSkuFor(this.productForm);
                },

                async generateProductSkuFor(form) {
                    try {
                        const response = await fetch(`{{ route('products.generate-sku') }}?category=${encodeURIComponent(form.category || 'smartphone')}&model=${encodeURIComponent(form.name || '')}`);
                        const data = await response.json();
                        form.sku = data.sku;
                    } catch (e) {
                        // Ignora erro de sku
                    }
                },

                async saveProduct() {
                    this.productFormError = '';
                    
                    if (!this.productForm.name || !this.productForm.sku || !this.productForm.category || !this.productForm.condition) {
                        this.productFormError = 'Preencha os campos obrigatórios: Nome, SKU, Categoria e Condição.';
                        return;
                    }

                    this.productFormSaving = true;

                    try {
                        const payload = {
                            ...this.productForm,
                            active: true,
                        };

                        const response = await fetch('{{ route("products.store-quick") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            const errors = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Erro ao salvar produto.');
                            this.productFormError = errors;
                            this.productFormSaving = false;
                            return;
                        }

                        // Adicionar produto à venda automaticamente
                        this.addItem(data);
                        this.showProductModal = false;
                        this.searchTerm = '';
                    } catch (e) {
                        this.productFormError = 'Erro de conexão. Tente novamente.';
                    }

                    this.productFormSaving = false;
                },
            }
        }
    </script>
    @endpush
</x-app-layout>
