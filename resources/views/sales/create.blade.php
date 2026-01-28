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
                                            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.75rem; border: 1px solid #e5e7eb; margin-bottom: 0.75rem;">
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
                                        </template>
                                    </div>
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
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="background: #374151; color: white; padding: 0.75rem 1.5rem;">
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
                                         style="position: absolute; z-index: 10; margin-top: 0.5rem; width: 100%; background: white; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border-radius: 0.75rem; border: 1px solid #e5e7eb; max-height: 10rem; overflow: auto;">
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
                        
                        <!-- PASSO 3: PAGAMENTO -->
                        <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="background: #374151; color: white; padding: 0.75rem 1.5rem;">
                                <div style="display: flex; align-items: center;">
                                    <span style="width: 1.5rem; height: 1.5rem; background: white; color: #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; margin-right: 0.5rem;">3</span>
                                    <span style="font-weight: 600;">Pagamento</span>
                                </div>
                            </div>
                            <div style="padding: 1rem;">
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Forma de Pagamento</label>
                                    <select name="payment_method" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; outline: none; background: white;">
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Status</label>
                                        <select name="payment_status" required style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; outline: none; background: white;">
                                            @foreach($paymentStatuses as $status)
                                                <option value="{{ $status->value }}" {{ $status->value === 'paid' ? 'selected' : '' }}>{{ $status->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Parcelas</label>
                                        <input type="number" name="installments" value="1" min="1" max="24" 
                                               style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; outline: none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- RESUMO FINAL -->
                        <div style="background: #111827; color: white; border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.25); overflow: hidden;">
                            <div style="padding: 1.5rem;">
                                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Resumo da Venda</h3>
                                
                                <dl style="display: flex; flex-direction: column; gap: 0.75rem;">
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
                                                style="width: 6rem; text-align: right; padding: 0.25rem 0.5rem; background: #1f2937; border: 1px solid #374151; border-radius: 0.5rem; color: white; outline: none;"
                                            >
                                        </dd>
                                    </div>
                                    <div style="padding-top: 1rem; border-top: 1px solid #374151;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <dt style="font-size: 1.25rem; font-weight: 700;">TOTAL</dt>
                                            <dd style="font-size: 1.875rem; font-weight: 700; color: white;" x-text="formatMoney(total)"></dd>
                                        </div>
                                    </div>
                                </dl>
                            </div>
                            
                            <button 
                                type="submit" 
                                :disabled="items.length === 0"
                                style="width: 100%; padding: 1rem; background: white; color: #111827; font-size: 1.125rem; font-weight: 700; border: none; cursor: pointer; transition: background 0.2s;"
                                onmouseover="if(!this.disabled) this.style.backgroundColor='#f3f4f6'" 
                                onmouseout="this.style.backgroundColor='white'"
                                :style="items.length === 0 ? 'opacity: 0.5; cursor: not-allowed;' : ''"
                            >
                                <span x-show="items.length > 0">✓ FINALIZAR VENDA</span>
                                <span x-show="items.length === 0">Adicione produtos para continuar</span>
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
                
                updateTotals() {
                    this.subtotal = this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                    this.total = Math.max(0, this.subtotal - this.discount);
                },
                
                formatMoney(value) {
                    return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
