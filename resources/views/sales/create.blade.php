<x-app-layout>
    <x-slot name="title">Nova Venda</x-slot>
    <div class="py-4">
        <div class="px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/35 rounded-xl p-4 mb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-semibold text-red-300">Corrija os erros abaixo:</span>
                    </div>
                    <ul class="list-disc pl-6 text-red-300 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a class="mr-4 p-2 text-dg-400 rounded-lg hover:bg-surface-elevated" href="{{ route('sales.index') }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-dg-100">Nova Venda</h1>
                        <p class="text-sm text-dg-400">Preencha os dados para registrar uma venda</p>
                    </div>
                </div>
                <span class="text-sm text-dg-500">ESC para cancelar</span>
            </div>

            @if(isset($reservation) && $reservation)
                <div class="bg-blue-500/10 border border-blue-300 rounded-xl p-4 mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <div>
                        <span class="font-semibold text-blue-300">Convertendo Reserva #{{ $reservation->reservation_number }}</span>
                        <span class="text-sm text-dg-400 ml-2">
                            O sinal de {{ $reservation->formatted_deposit_paid }} será aplicado como desconto.
                        </span>
                    </div>
                </div>
            @endif

            @if(isset($preSale) && $preSale)
                <div class="bg-[rgba(22,163,106,0.1)] border border-[rgba(22,163,106,0.3)] rounded-xl p-4 mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <div>
                        <span class="font-semibold text-green-400">Efetivando Pré-Venda #{{ $preSale->pre_sale_number }}</span>
                        <span class="text-sm text-dg-400 ml-2">
                            Cliente: {{ $preSale->customer?->name }} · Produto: {{ $preSale->product_name }} · Sinal: {{ $preSale->formatted_down_payment }}
                        </span>
                    </div>
                </div>
            @endif

            <div x-data="saleForm()" @keydown.escape.window="if(!showCustomerModal && !showProductModal) window.location.href='{{ route('sales.index') }}'">

            {{-- Pré-Vendas Pendentes --}}
            @if(isset($pendingPreSales) && $pendingPreSales->count() > 0 && !(isset($preSale) && $preSale))
                <div class="mb-6 bg-blue-500/[0.06] border border-blue-500/20 rounded-xl overflow-hidden">
                    @foreach($pendingPreSales as $ps)
                        <div class="py-2 px-4 flex items-center gap-3" style="{{ !$loop->last ? 'border-bottom: 1px solid rgba(59,130,246,0.1);' : '' }}">
                            <span class="text-[0.6875rem] font-semibold text-dg-400 whitespace-nowrap">{{ $ps->pre_sale_number }}</span>
                            <span class="inline-block py-0.5 px-2 rounded-full text-[0.5625rem] font-semibold" style="{{ $ps->status->color() }}">{{ $ps->status->label() }}</span>
                            <span class="flex-1 text-[0.8125rem] text-dg-300 overflow-hidden text-ellipsis whitespace-nowrap">{{ $ps->customer?->name ?? 'N/A' }} · {{ $ps->product_name }}</span>
                            <span class="text-[0.8125rem] font-semibold text-dg-100 whitespace-nowrap">{{ $ps->formatted_unit_price }}</span>
                            <a class="py-1 px-2.5 text-[0.6875rem] font-semibold rounded-md whitespace-nowrap" href="{{ route('pre-sales.show', $ps) }}"
                               style="{{ $ps->isReady() ? 'background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);' : 'background: rgba(255,255,255,0.04); color: #818181; border: 1px solid rgba(255,255,255,0.08);' }}">
                                {{ $ps->isReady() ? 'Efetivar' : 'Ver' }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('sales.store') }}" @submit="handleSubmit($event)">
                @csrf
                @if(isset($reservation) && $reservation)
                    <input type="hidden" name="from_reservation" value="{{ $reservation->id }}">
                @endif
                @if(isset($preSale) && $preSale)
                    <input type="hidden" name="from_presale" value="{{ $preSale->id }}">
                @endif
                
                <div class="sale-grid">
                    <!-- COLUNA PRINCIPAL - PRODUTOS -->
                    <div class="sale-main">
                        
                        <!-- PASSO 1: TIPO DE VENDA + CONFIGURAÇÕES -->
                        <div class="bg-surface-raised rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.3)] border border-border overflow-visible">
                            <div class="bg-[#111827] text-white py-4 px-6 rounded-t-2xl">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-surface-raised text-dg-100 rounded-full flex items-center justify-center font-bold mr-3">1</span>
                                    <span class="text-lg font-semibold">Primeiro Passo</span>
                                </div>
                            </div>
                            <div class="p-6">
                            <div class="flex flex-wrap gap-6">
                                <!-- Tipo de Venda -->
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block font-semibold text-[0.9375rem] text-dg-300 mb-3">
                                        Tipo de Venda <span class="text-red-300">*</span>
                                    </label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center cursor-pointer py-3 px-4 border-2 border-white/[0.08] rounded-lg transition-all"
                                               :style="{ borderColor: saleType === 'cliente_final' ? '#3b82f6' : 'rgba(255,255,255,0.08)', background: saleType === 'cliente_final' ? 'rgba(59,130,246,0.15)' : '#1a1a1a' }">
                                            <input class="w-[1.125rem] h-[1.125rem] mr-2.5 accent-blue-500" type="radio" name="sale_type" value="cliente_final"
                                                   x-model="saleType" required>
                                            <span class="text-[0.9375rem] font-medium">Cliente</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer py-3 px-4 border-2 border-white/[0.08] rounded-lg transition-all"
                                               :style="{ borderColor: saleType === 'repasse' ? '#8b5cf6' : 'rgba(255,255,255,0.08)', background: saleType === 'repasse' ? 'rgba(139,92,246,0.15)' : '#1a1a1a' }">
                                            <input class="w-[1.125rem] h-[1.125rem] mr-2.5 accent-violet-500" type="radio" name="sale_type" value="repasse"
                                                   x-model="saleType" required>
                                            <span class="text-[0.9375rem] font-medium">Repasse</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Vendedor Responsável -->
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block font-semibold text-[0.9375rem] text-dg-300 mb-3">
                                        Vendedor Responsável <span class="text-red-300">*</span>
                                    </label>
                                    <select class="w-full py-3 px-4 border-2 border-white/[0.08] rounded-lg text-[0.9375rem] bg-surface-overlay outline-none focus:border-dg-500" name="seller_name" x-model="sellerName" required


                                            @change="sellerId = $event.target.selectedOptions[0]?.dataset?.userId || ''">
                                        <option value="">Selecione...</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{ $seller->name }}" data-user-id="{{ $seller->id }}">{{ $seller->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="seller_id" :value="sellerId">
                                </div>

                                <!-- Tipo de Entrega -->
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block font-semibold text-[0.9375rem] text-dg-300 mb-3">
                                        Entrega <span class="text-red-300">*</span>
                                    </label>
                                    <select class="w-full py-3 px-4 border-2 border-white/[0.08] rounded-lg text-[0.9375rem] bg-surface-overlay outline-none focus:border-dg-500" name="delivery_type" x-model="deliveryType" required>
                                        <option value="">Selecione...</option>
                                        <option value="retirada">Retirada</option>
                                        <option value="entrega">Entrega</option>
                                        <option value="transferencia">Transferência</option>
                                    </select>
                                    <!-- Sub-opção de entrega -->
                                    <div class="mt-3" x-show="deliveryType === 'entrega'" x-collapse>
                                        <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1.5">
                                            Método de Entrega
                                        </label>
                                        <select class="w-full py-2 px-3 border-2 border-white/[0.08] rounded-lg text-sm bg-surface-overlay outline-none focus:border-dg-500" name="delivery_method" x-model="deliveryMethod">
                                            <option value="">Selecione...</option>
                                            <option value="motoboy_nosso">Motoboy Nosso</option>
                                            <option value="motoboy_cliente">Motoboy Cliente</option>
                                            <option value="entrega_dg">Entrega DG</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- PASSO 2: BUSCAR PRODUTO -->
                        <div class="bg-surface-raised rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.3)] border border-border overflow-visible">
                            <div class="bg-[#111827] text-white py-4 px-6 rounded-t-2xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="w-8 h-8 bg-surface-raised text-dg-100 rounded-full flex items-center justify-center font-bold mr-3">2</span>
                                        <span class="text-lg font-semibold">Adicionar Produtos</span>
                                    </div>
                                    <button class="flex items-center gap-1.5 py-1.5 px-3 bg-white/15 text-white border border-white/30 rounded-lg text-[0.8125rem] font-medium cursor-pointer hover:bg-white/25" type="button" @click="showProductModal = true; productForm = resetProductForm()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Cadastrar Produto
                                    </button>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="relative">
                                    <label class="block text-sm font-medium text-dg-300 mb-2">
                                        Buscar produto por nome, SKU ou IMEI
                                    </label>
                                    <div class="relative">
                                        <input class="w-full pt-4 pr-4 pb-4 pl-12 text-lg border-2 border-white/[0.08] rounded-xl outline-none focus:border-dg-500"
                                            type="text"
                                            x-model="searchTerm"
                                            @input.debounce.300ms="searchProducts"
                                            @keydown.enter.prevent="if(searchResults.length > 0) addItem(searchResults[0])"
                                            placeholder="Buscar por nome, SKU ou IMEI... (Enter para adicionar)"


                                            autofocus>
                                        <div class="absolute top-[50%] left-4 -translate-y-1/2 pointer-events-none">
                                            <svg class="h-6 w-6 text-dg-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Resultados da busca -->
                                    <div class="absolute z-[20] mt-2 w-full bg-surface-raised shadow-[0_25px_50px_-12px_rgba(0,0,0,0.6)] rounded-xl border border-border max-h-[20rem] overflow-auto" x-show="searchResults.length > 0 || (searchTerm.length >= 2 && searchResults.length === 0 && !searchLoading)" x-cloak>
                                        <template x-for="(product, pIdx) in searchResults" :key="(product.is_fragrance ? 'f_' : product.is_consignment ? 'c_' : 'p_') + product.id">
                                            <button class="w-full py-3 px-4 text-left flex justify-between items-center border-b border-border-subtle hover:bg-white/[0.06]"
                                                type="button"
                                                @click="addItem(product)">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold text-dg-100" x-text="product.name"></span>
                                                        <span class="text-[0.625rem] py-[0.0625rem] px-1.5 bg-blue-500/15 text-blue-300 rounded-full font-semibold uppercase tracking-wide" x-show="product.from_trade_in">Trade-in</span>
                                                        <span class="text-[0.625rem] py-[0.0625rem] px-1.5 bg-[rgba(251,191,36,0.15)] text-amber-400 rounded-full font-semibold uppercase tracking-wide" x-show="product.is_consignment">Consignado</span>
                                                        <span class="text-[0.625rem] py-[0.0625rem] px-1.5 bg-[rgba(201,169,110,0.15)] text-[#c9a96e] rounded-full font-semibold uppercase tracking-wide" x-show="product.is_fragrance">Perfume</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                        <span class="text-xs py-0.5 px-2 bg-surface-elevated text-dg-400 rounded" x-text="product.sku"></span>
                                                        <template x-if="product.imei && !product.is_consignment">
                                                            <span class="text-[0.7rem] py-0.5 px-2 bg-blue-500/10 text-blue-400 rounded font-mono" x-text="'IMEI: ' + product.imei"></span>
                                                        </template>
                                                        <span class="text-xs" :style="{ color: product.stock > 0 ? '#6b7280' : '#ea580c', fontWeight: product.stock <= 0 ? '600' : '400' }" x-text="product.stock > 0 ? product.stock + ' em estoque' : 'Sem estoque'"></span>
                                                        @if(auth()->user()->canViewFinancials())
                                                        <span class="text-xs text-emerald-600 font-medium" x-show="product.cost_price" x-text="product.cost_price ? 'Custo: R$ ' + product.cost_price.toFixed(2).replace('.', ',') : ''"></span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <svg class="w-5 h-5 text-dg-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        </template>
                                        <!-- Opção de cadastrar quando não encontra -->
                                        <div class="p-4 text-center border-t border-border" x-show="searchTerm.length >= 2 && searchResults.length === 0 && !searchLoading">
                                            <p class="text-sm text-dg-400 mb-3">Nenhum produto encontrado para "<span class="font-semibold" x-text="searchTerm"></span>"</p>
                                            <button class="inline-flex items-center gap-1.5 py-2 px-4 bg-[#111827] text-white rounded-lg text-sm font-medium border-0 cursor-pointer hover:bg-[#374151]" type="button"
                                                    @click="productForm = resetProductForm(); productForm.name = searchTerm; showProductModal = true; searchResults = [];">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Cadastrar Produto Agora
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Lista de itens adicionados -->
                                <div class="mt-6">
                                    <div class="text-center py-12 px-4 rounded-xl" x-show="items.length === 0" style="border: 2px dashed rgba(255,255,255,0.1);">
                                        <svg class="my-0 mx-auto h-12 w-12 text-dg-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <p class="mt-4 text-dg-400">Nenhum produto adicionado</p>
                                        <p class="text-sm text-dg-500">Use o campo acima para buscar e adicionar produtos</p>
                                    </div>
                                    
                                    <div x-show="items.length > 0">
                                        <template x-for="(item, index) in items" :key="index">
                                            <div class="p-4 bg-surface-overlay rounded-xl border border-border mb-3">
                                                {{-- Linha 1: Nome + badges + botão remover --}}
                                                <div class="flex items-start justify-between gap-2 mb-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-start gap-2 flex-wrap">
                                                            <p class="font-semibold text-dg-100 text-[0.9375rem] leading-[1.3] overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" x-text="item.name"></p>
                                                            <span class="text-[0.625rem] py-[0.0625rem] px-1.5 bg-blue-500/15 text-blue-300 rounded-full font-semibold shrink-0 whitespace-nowrap" x-show="item.from_trade_in">TRADE-IN</span>
                                                            <span class="text-[0.625rem] py-[0.0625rem] px-1.5 bg-[rgba(251,191,36,0.15)] text-amber-400 rounded-full font-semibold shrink-0 whitespace-nowrap" x-show="item.is_consignment">CONSIGNADO</span>
                                                            <span class="text-[0.625rem] py-[0.0625rem] px-1.5 bg-[rgba(201,169,110,0.15)] text-[#c9a96e] rounded-full font-semibold shrink-0 whitespace-nowrap" x-show="item.is_fragrance">PERFUME</span>
                                                        </div>
                                                        <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.is_consignment || item.is_fragrance ? '' : (item.id || '')">
                                                        <input type="hidden" :name="'items['+index+'][consignment_item_id]'" :value="item.consignment_item_id || ''">
                                                        <input type="hidden" :name="'items['+index+'][fragrance_id]'" :value="item.fragrance_id || ''">
                                                        <input type="hidden" :name="'items['+index+'][product_name]'" :value="item.name || ''">
                                                    </div>
                                                    <button class="p-1.5 text-red-500 rounded-lg cursor-pointer bg-transparent border-0 shrink-0 hover:bg-red-500/15" type="button" @click="removeItem(index)">
                                                        <svg class="h-[1.125rem] w-[1.125rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                
                                                {{-- Linha 2: Quantidade, Venda, Subtotal --}}
                                                <div class="flex items-center gap-3 flex-wrap">
                                                    <div class="flex items-center gap-2">
                                                        <button class="w-8 h-8 flex items-center justify-center bg-surface-raised border border-white/[0.08] rounded-lg cursor-pointer" type="button" @click="item.quantity = Math.max(1, item.quantity - 1); updateTotals()">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                        </button>
                                                        <input class="w-14 text-center font-bold text-base border-2 border-white/[0.08] rounded-lg p-1"
                                                            type="number"
                                                            :name="'items['+index+'][quantity]'"
                                                            x-model.number="item.quantity"
                                                            @input="updateTotals"
                                                            min="1"
                                                            :max="Math.max(1, item.stock)">
                                                        <button class="w-8 h-8 flex items-center justify-center bg-surface-raised border border-white/[0.08] rounded-lg cursor-pointer" type="button" @click="item.quantity = Math.min(Math.max(1, item.stock), item.quantity + 1); updateTotals()">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="w-28">
                                                        <label class="block text-[0.625rem] text-dg-400 uppercase mb-0.5">Venda</label>
                                                        <input type="hidden" :name="'items['+index+'][unit_price]'" :value="item.price">
                                                        <input class="w-full text-right font-semibold border-2 border-white/[0.08] rounded-lg py-1 px-2"
                                                            type="text"
                                                            inputmode="numeric"
                                                            :value="toMoneyMask(item.price)"
                                                            @input="applyMoneyMask($event); item.price = parseMoney($event.target.value); updateTotals()"
                                                            placeholder="0,00">
                                                    </div>
                                                    
                                                    <div class="flex-1 text-right">
                                                        <span class="text-lg font-bold text-dg-100" x-text="formatMoney(item.quantity * item.price)"></span>
                                                    </div>
                                                </div>

                                                {{-- Combos de capinha --}}
                                                <template x-if="item.category === 'case'">
                                                    <div class="mt-3"
                                                         x-data="{
                                                             basePrice() { return item.original_sale_price || item.price; },
                                                             combos() {
                                                                 const p = this.basePrice();
                                                                 const t2 = Math.round(p * 2 * 0.85 / 5) * 5;
                                                                 const t3 = Math.round(p * 3 * 0.75 / 5) * 5;
                                                                 return [
                                                                     { qty: 1, label: '1 capinha', total: p, unitPrice: p, discount: 0 },
                                                                     { qty: 2, label: '2 capinhas', total: t2, unitPrice: Math.round(t2 / 2 * 100) / 100, discount: Math.round((1 - t2 / (p * 2)) * 100) },
                                                                     { qty: 3, label: '3 capinhas', total: t3, unitPrice: Math.round(t3 / 3 * 100) / 100, discount: Math.round((1 - t3 / (p * 3)) * 100) },
                                                                 ];
                                                             },
                                                             selectCombo(combo) {
                                                                 item.quantity = combo.qty;
                                                                 item.price = combo.unitPrice;
                                                                 updateTotals();
                                                             }
                                                         }">
                                                        <div class="grid grid-cols-3 gap-2">
                                                            <template x-for="combo in combos()" :key="combo.qty">
                                                                <button class="flex flex-col items-center justify-center min-h-[4.5rem] p-2 rounded-xl cursor-pointer text-center transition-all" type="button"
                                                                        @click="selectCombo(combo)"
                                                                        style="border: 2px solid;"
                                                                        :style="item.quantity === combo.qty
                                                                            ? 'background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border-color: #c4b5fd; box-shadow: 0 2px 8px rgba(124,58,237,0.3);'
                                                                            : 'background: #141414; color: #a4a4a4; border-color: rgba(255,255,255,0.1);'">
                                                                    <span class="text-xs font-semibold leading-none" x-text="combo.label"></span>
                                                                    <span class="text-[1.0625rem] font-extrabold mt-1 leading-none"
                                                                          :style="item.quantity === combo.qty ? 'color: white;' : 'color: #e3e3e3;'"
                                                                          x-text="'R$ ' + combo.total.toFixed(0)"></span>
                                                                    <span class="text-[0.625rem] font-bold mt-1 leading-none min-h-[0.875rem]"
                                                                          :style="combo.discount > 0
                                                                              ? (item.quantity === combo.qty ? 'color: #fde68a;' : 'color: #d97706;')
                                                                              : (item.quantity === combo.qty ? 'color: rgba(255,255,255,0.5);' : 'color: #515151;')"
                                                                          x-text="combo.discount > 0 ? '-' + combo.discount + '%' : 'unitário'"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- Linha 2: Custo Financeiro, Custo Comissão, Origem, Frete --}}
                                                <input type="hidden" :name="'items['+index+'][cost_price]'" :value="item.cost_price">
                                                <input type="hidden" :name="'items['+index+'][financial_cost]'" :value="item.financial_cost">
                                                <input type="hidden" :name="'items['+index+'][commission_cost]'" :value="item.commission_cost">
                                                @if(auth()->user()->canViewFinancials())
                                                <div class="mt-3 pt-3 border-t border-border grid grid-cols-[1fr_1fr_1fr] gap-3 items-end">
                                                    <div>
                                                        <label class="block text-[0.6875rem] font-medium text-dg-400 mb-1">
                                                            Custo (R$) <span class="text-red-300">*</span>
                                                        </label>
                                                        <input class="w-full py-1.5 px-2 border-2 border-white/[0.08] rounded-md text-[0.8125rem] outline-none focus:border-dg-500" type="text"
                                                               inputmode="numeric"
                                                               :value="toMoneyMask(item.cost_price)"
                                                               @input="applyMoneyMask($event); item.cost_price = parseMoney($event.target.value); syncCosts(item); updateTotals()"
                                                               placeholder="0,00">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[0.6875rem] font-medium text-dg-400 mb-1">
                                                            Custo Financeiro (R$)
                                                        </label>
                                                        <input class="w-full py-1.5 px-2 border-2 border-[#d1fae5] rounded-md text-[0.8125rem] outline-none bg-emerald-500/10 focus:border-green-600" type="text"
                                                               inputmode="numeric"
                                                               :value="toMoneyMask(item.financial_cost)"
                                                               @input="applyMoneyMask($event); item.financial_cost = parseMoney($event.target.value); updateTotals()"
                                                               placeholder="0,00">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[0.6875rem] font-medium text-dg-400 mb-1">
                                                            Custo Comissão (R$)
                                                        </label>
                                                        <input class="w-full py-1.5 px-2 border-2 border-[rgba(251,191,36,0.3)] rounded-md text-[0.8125rem] outline-none bg-surface-raised focus:border-amber-600" type="text"
                                                               inputmode="numeric"
                                                               :value="toMoneyMask(item.commission_cost)"
                                                               @input="applyMoneyMask($event); item.commission_cost = parseMoney($event.target.value); updateTotals()"
                                                               placeholder="0,00">
                                                    </div>
                                                </div>
                                                <div class="mt-2 grid grid-cols-[1fr_1fr_1fr_1fr] gap-3 items-end">
                                                    <div>
                                                        <label class="block text-[0.6875rem] font-medium text-dg-400 mb-1">Origem</label>
                                                        <select class="w-full py-1.5 px-2 border-2 border-white/[0.08] rounded-md text-[0.8125rem] bg-surface-overlay outline-none" :name="'items['+index+'][supplier_origin]'"
                                                                x-model="item.supplier_origin"
                                                                @change="updateTotals">
                                                            <option value="">-</option>
                                                            <option value="br">BR (Brasil)</option>
                                                            <option value="py">PY (Paraguai)</option>
                                                        </select>
                                                    </div>
                                                    <div x-show="item.supplier_origin">
                                                        <label class="block text-[0.6875rem] font-medium text-dg-400 mb-1">Tipo Frete</label>
                                                        <select class="w-full py-1.5 px-2 border-2 border-white/[0.08] rounded-md text-[0.8125rem] bg-surface-overlay outline-none" :name="'items['+index+'][freight_type]'"
                                                                x-model="item.freight_type"
                                                                @change="updateTotals">
                                                            <option value="">Sem frete</option>
                                                            <option value="percentage">% sobre custo</option>
                                                            <option value="fixed">Valor fixo (R$)</option>
                                                        </select>
                                                    </div>
                                                    <div x-show="item.supplier_origin && item.freight_type">
                                                        <label class="block text-[0.6875rem] font-medium text-dg-400 mb-1"
                                                               x-text="item.freight_type === 'percentage' ? 'Frete (%)' : 'Frete (R$)'">Frete</label>
                                                        <input class="w-full py-1.5 px-2 border-2 border-white/[0.08] rounded-md text-[0.8125rem] outline-none focus:border-dg-500" type="number"
                                                               :name="'items['+index+'][freight_value]'"
                                                               x-model.number="item.freight_value"
                                                               @input="updateTotals"
                                                               step="0.01" min="0" placeholder="0.00">
                                                    </div>
                                                </div>

                                                {{-- Linha 3: Resumo de custo/lucro --}}
                                                <div class="mt-2 flex gap-4 text-xs text-dg-400" x-show="item.cost_price > 0">
                                                    <span x-show="getItemFreightAmount(item) > 0">
                                                        Frete: <span class="font-semibold" x-text="formatMoney(getItemFreightAmount(item))"></span>
                                                    </span>
                                                    <span x-show="getItemFreightAmount(item) > 0">
                                                        Custo real: <span class="font-semibold" x-text="formatMoney(getItemTotalCost(item))"></span>
                                                    </span>
                                                    <span :style="{ color: getItemProfit(item) >= 0 ? '#16a34a' : '#dc2626', fontWeight: '600' }">
                                                        Lucro: <span x-text="formatMoney(getItemProfit(item))"></span>
                                                    </span>
                                                </div>
                                                @endif

                                                {{-- Alerta de sem estoque --}}
                                                <div class="mt-2 py-2 px-3 bg-orange-500/[0.12] border border-orange-500/35 rounded-md flex items-center justify-between gap-2" x-show="item.stock <= 0">
                                                    <div class="flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                        </svg>
                                                        <span class="text-xs text-orange-300 font-semibold">
                                                            Sem estoque disponível!
                                                        </span>
                                                    </div>
                                                    <button class="inline-flex items-center gap-1 py-1 px-2.5 bg-orange-600 text-white border-0 rounded-md text-[0.6875rem] font-semibold cursor-pointer whitespace-nowrap hover:bg-orange-700" type="button" @click="openStockModal(index)">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                        Adicionar Estoque
                                                    </button>
                                                </div>
                                                {{-- Alerta de preço abaixo do custo total --}}
                                                @if(auth()->user()->canViewFinancials())
                                                <div class="mt-2 py-2 px-3 bg-red-500/10 border border-red-500/35 rounded-md flex items-center gap-1.5" x-show="item.cost_price > 0 && item.price > 0 && item.price < getItemTotalCost(item)">
                                                    <svg class="w-4 h-4 text-red-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                    </svg>
                                                    <span class="text-xs text-red-300 font-semibold">
                                                        Preço abaixo do custo total! Custo: <span x-text="formatMoney(getItemTotalCost(item))"></span>
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <!-- TRADE-IN (Aparelhos como entrada) -->
                        <div class="bg-surface-raised rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.3)] border border-border overflow-hidden" x-show="saleType === 'cliente_final' || saleType === null">
                            <div class="bg-violet-600 text-white py-3 px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-semibold">Aparelhos como Entrada (Trade-in)</span>
                                    </div>
                                    <label class="flex items-center cursor-pointer">
                                        <input class="w-5 h-5 mr-2" type="checkbox" x-model="hasTradeIn" @change="toggleTradeIn()">
                                        <span class="text-sm">Ativar</span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-6" x-show="hasTradeIn" x-collapse>
                                <!-- Lista de aparelhos -->
                                <template x-for="(ti, tiIndex) in tradeIns" :key="tiIndex">
                                    <div class="bg-[rgba(124,58,237,0.08)] border border-[rgba(196,181,253,0.2)] rounded-xl p-4 mb-4 relative">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-[0.8125rem] font-semibold text-violet-300" x-text="'Aparelho ' + (tiIndex + 1)"></span>
                                            <button class="p-1 text-red-500 bg-transparent border-0 cursor-pointer" type="button" @click="removeTradeIn(tiIndex)"
                                                    x-show="tradeIns.length > 1"

                                                    title="Remover aparelho">
                                                <svg class="w-[1.125rem] h-[1.125rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3">
                                            <!-- Nome/Descrição -->
                                            <div class="col-span-3">
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">
                                                    Nome/Descrição <span class="text-red-300">*</span>
                                                </label>
                                                <input class="w-full py-2 px-3 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="text" :name="'trade_ins['+tiIndex+'][device_name]'" x-model="ti.device_name"
                                                       placeholder="Ex: iPhone 13 Pro Max">
                                            </div>
                                            <!-- Categoria -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Categoria</label>
                                                <select class="w-full py-2 px-3 border-2 border-white/[0.08] rounded-lg text-sm bg-surface-raised" :name="'trade_ins['+tiIndex+'][category]'" x-model="ti.category">
                                                    <option value="">Selecione...</option>
                                                    @foreach(\App\Domain\Product\Enums\ProductCategory::grouped() as $group => $items)
                                                        <optgroup label="{{ $group }}">
                                                            @foreach($items as $cat)
                                                                <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Modelo -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Modelo</label>
                                                <input class="w-full py-2 px-3 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="text" :name="'trade_ins['+tiIndex+'][device_model]'" x-model="ti.device_model"
                                                       placeholder="Ex: 15 Pro Max">
                                            </div>
                                            <!-- Condição Cosmética -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Condição Cosmética</label>
                                                <select class="w-full py-2 px-3 border-2 border-white/[0.08] rounded-lg text-sm bg-surface-raised" :name="'trade_ins['+tiIndex+'][condition]'" x-model="ti.condition">
                                                    @foreach($tradeInConditions as $condition)
                                                        <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Armazenamento -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">
                                                    Armazenamento <span class="text-red-300">*</span>
                                                </label>
                                                <input class="w-full py-2 px-3 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="text" :name="'trade_ins['+tiIndex+'][storage]'" x-model="ti.storage" required
                                                       placeholder="Ex: 256GB">
                                            </div>
                                            <!-- Cor -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">
                                                    Cor <span class="text-red-300">*</span>
                                                </label>
                                                <input class="w-full py-2 px-3 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="text" :name="'trade_ins['+tiIndex+'][color]'" x-model="ti.color" required
                                                       placeholder="Ex: Preto">
                                            </div>
                                            <!-- IMEI -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">IMEI</label>
                                                <input class="w-full py-2 px-3 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="text" :name="'trade_ins['+tiIndex+'][imei]'" x-model="ti.imei"
                                                       placeholder="Ex: 123456789012345">
                                            </div>

                                            <!-- Separador Valores -->
                                            <div class="col-span-3 border-t border-border mt-1 pt-3">
                                                <span class="text-xs font-semibold text-violet-300 uppercase tracking-wider">Valores</span>
                                            </div>
                                            <!-- Valor Negociado (= Preço Custo) -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">
                                                    Valor Negociado <span class="text-red-300">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-[50%] -translate-y-1/2 text-dg-400 text-[0.8125rem]">R$</span>
                                                    <input class="w-full pt-2 pr-3 pb-2 pl-9 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="number" :name="'trade_ins['+tiIndex+'][estimated_value]'" x-model.number="ti.estimated_value"
                                                           @input="updateTotals" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                            <!-- Preço Final -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">
                                                    Preço Final <span class="text-red-300">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-[50%] -translate-y-1/2 text-dg-400 text-[0.8125rem]">R$</span>
                                                    <input class="w-full pt-2 pr-3 pb-2 pl-9 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="number" :name="'trade_ins['+tiIndex+'][sale_price]'" x-model.number="ti.sale_price"
                                                           step="0.01" min="0" placeholder="0,00" required>
                                                </div>
                                            </div>
                                            <!-- Preço Repasse -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">
                                                    Preço Repasse <span class="text-red-300">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-[50%] -translate-y-1/2 text-dg-400 text-[0.8125rem]">R$</span>
                                                    <input class="w-full pt-2 pr-3 pb-2 pl-9 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="number" :name="'trade_ins['+tiIndex+'][resale_price]'" x-model.number="ti.resale_price"
                                                           step="0.01" min="0" placeholder="0,00" required>
                                                </div>
                                            </div>

                                            <!-- Separador Seminovo -->
                                            <div class="col-span-3 border-t border-border mt-1 pt-3">
                                                <span class="text-xs font-semibold text-violet-300 uppercase tracking-wider">Informações do Seminovo</span>
                                            </div>
                                            <!-- Bateria -->
                                            <div>
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">
                                                    Bateria (%) <span class="text-red-300">*</span>
                                                </label>
                                                <input class="w-full py-2 px-3 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="number" :name="'trade_ins['+tiIndex+'][battery_health]'" x-model.number="ti.battery_health"
                                                       min="0" max="100" placeholder="Ex: 87" required>
                                            </div>
                                            <!-- Checkboxes -->
                                            <div class="flex gap-6 items-center pt-1 col-span-2">
                                                <label class="flex items-center gap-1.5 cursor-pointer text-[0.8125rem] text-dg-300">
                                                    <input type="hidden" :name="'trade_ins['+tiIndex+'][has_box]'" value="0">
                                                    <input class="w-[1.125rem] h-[1.125rem] accent-violet-300" type="checkbox" :name="'trade_ins['+tiIndex+'][has_box]'" x-model="ti.has_box" value="1">
                                                    <svg class="w-4 h-4 text-dg-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                    Com caixa
                                                </label>
                                                <label class="flex items-center gap-1.5 cursor-pointer text-[0.8125rem] text-dg-300">
                                                    <input type="hidden" :name="'trade_ins['+tiIndex+'][has_cable]'" value="0">
                                                    <input class="w-[1.125rem] h-[1.125rem] accent-violet-300" type="checkbox" :name="'trade_ins['+tiIndex+'][has_cable]'" x-model="ti.has_cable" value="1">
                                                    <svg class="w-4 h-4 text-dg-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                    Com cabo
                                                </label>
                                            </div>
                                            <!-- Vincular Checklist -->
                                            <div class="col-span-3" x-data="{ clSearch: '', clOpen: false, clResults: [], clDebounce: null }">
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Vincular Checklist</label>
                                                <input type="hidden" :name="'trade_ins['+tiIndex+'][checklist_id]'" :value="ti.checklist_id || ''">
                                                <div class="relative">
                                                    <input class="w-full py-2 px-3 bg-surface-overlay text-dg-100 border-2 border-white/[0.08] rounded-lg text-sm focus:border-violet-600" type="text" x-model="clSearch" @focus="clOpen = true"
                                                           @input="clearTimeout(clDebounce); clDebounce = setTimeout(() => { fetch('{{ route("checklists.search") }}?q=' + encodeURIComponent(clSearch)).then(r => r.json()).then(d => { clResults = d; clOpen = true; }); }, 250)"
                                                           @click.away="clOpen = false"
                                                           placeholder="Buscar checklist..." autocomplete="off"
                                                           x-show="!ti.checklist_id">
                                                    <div class="flex items-center py-2 px-3 border-2 border-white/[0.08] rounded-lg bg-emerald-500/10" x-show="ti.checklist_id">
                                                        <span class="flex-1 text-sm text-dg-100" x-text="ti.checklist_label || ''"></span>
                                                        <button class="bg-transparent border-0 cursor-pointer text-dg-500 p-[2px]" type="button" @click="ti.checklist_id = ''; ti.checklist_label = ''">
                                                            <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                    <div class="absolute z-[50] w-full mt-[4px] bg-surface-raised border border-border rounded-lg shadow-[0_10px_25px_rgba(0,0,0,0.1)] max-h-[200px] overflow-y-auto" x-show="clOpen && clResults.length > 0">
                                                        <template x-for="cl in clResults" :key="cl.id">
                                                            <button class="w-full text-left py-2 px-3 border-0 bg-surface-raised cursor-pointer text-[0.8rem] hover:bg-white/[0.03]" type="button" @click="ti.checklist_id = cl.id; ti.checklist_label = cl.name + ' (' + cl.summary + ')'; clOpen = false; clSearch = ''">
                                                                <div class="font-medium text-dg-100" x-text="cl.name"></div>
                                                                <div class="text-[0.7rem] text-dg-400" x-text="cl.summary + ' — ' + cl.status"></div>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Observações -->
                                            <div class="col-span-3">
                                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Observações</label>
                                                <textarea class="w-full py-2 px-3 border-2 border-white/[0.08] rounded-lg text-sm resize-y focus:border-violet-600" :name="'trade_ins['+tiIndex+'][notes]'" x-model="ti.notes" rows="2"
                                                          placeholder="Estado da tela, detalhes adicionais..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Botão adicionar aparelho -->
                                <button class="w-full p-2.5 flex items-center justify-center gap-2 bg-violet-600 text-white border-0 rounded-lg text-sm font-medium cursor-pointer hover:bg-violet-700" type="button" @click="addTradeIn()">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Adicionar outro aparelho
                                </button>

                                <!-- Total dos trade-ins -->
                                <div class="mt-3 p-3 bg-violet-500/15 rounded-lg flex justify-between items-center" x-show="tradeIns.length > 1">
                                    <span class="text-[0.8125rem] font-semibold text-violet-300" x-text="tradeIns.length + ' aparelhos'"></span>
                                    <span class="text-base font-bold text-violet-300" x-text="'Total: ' + formatMoney(totalTradeInValue)"></span>
                                </div>

                            </div>
                        </div>
                        
                        <!-- Aviso: Trade-in não disponível para Repasse -->
                        <div class="bg-[rgba(251,191,36,0.1)] border border-[rgba(251,191,36,0.3)] rounded-2xl p-5 mb-6" x-show="saleType === 'repasse'">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-amber-400 mb-1">Trade-in não disponível</p>
                                    <p class="text-sm text-dg-300">Vendas do tipo <strong>Repasse</strong> não permitem aparelhos como entrada (trade-in).</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Observações -->
                        <div class="bg-surface-raised rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.3)] border border-border p-6">
                            <label class="block text-sm font-medium text-dg-300 mb-2">Observações (opcional)</label>
                            <textarea class="w-full border-2 border-white/[0.08] rounded-xl p-3 outline-none resize-y focus:border-dg-500" name="notes" rows="2"
                                      placeholder="Anotações sobre a venda..."></textarea>
                        </div>
                    </div>
                    
                    <!-- COLUNA LATERAL - RESUMO -->
                    <div class="sale-sidebar">
                        
                        <!-- PASSO 3: CLIENTE -->
                        <div class="bg-surface-raised rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.3)] border border-border relative z-[30]">
                            <div class="bg-[#374151] text-white py-3 px-6 rounded-t-2xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 bg-surface-raised text-dg-300 rounded-full flex items-center justify-center font-bold text-sm mr-2">3</span>
                                        <span class="font-semibold">Cliente <span class="text-red-300">*</span></span>
                                    </div>
                                    <button class="flex items-center gap-1 py-1 px-2 bg-white/15 text-white border border-white/30 rounded-md text-xs font-medium cursor-pointer hover:bg-white/25" type="button" @click="showCustomerModal = true; customerForm = resetCustomerForm()">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Novo
                                    </button>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="relative" x-show="!selectedCustomer.id">
                                    <input class="w-full py-3 px-4 border-2 border-white/[0.08] rounded-xl outline-none focus:border-dg-500"
                                        type="text"
                                        x-model="customerSearch"
                                        @input.debounce.300ms="searchCustomers"
                                        placeholder="Buscar cliente por nome ou telefone...">
                                    <input type="hidden" name="customer_id" x-model="selectedCustomer.id">
                                    
                                    <div class="absolute z-[50] mt-2 w-full bg-surface-raised shadow-[0_25px_50px_-12px_rgba(0,0,0,0.6)] rounded-xl border border-border max-h-[16rem] overflow-auto" x-show="customerResults.length > 0 || (customerSearch.length >= 2 && customerResults.length === 0 && !customerSearchLoading)" x-cloak>
                                        <template x-for="customer in customerResults" :key="customer.id">
                                            <button class="w-full py-3 px-4 text-left border-b border-border-subtle cursor-pointer bg-surface-raised hover:bg-white/[0.06]" type="button" @click="selectCustomer(customer)">
                                                <span class="font-medium text-dg-100" x-text="customer.name"></span>
                                                <span class="text-sm text-dg-400 block" x-text="customer.phone"></span>
                                            </button>
                                        </template>
                                        <!-- Opção de cadastrar quando não encontra -->
                                        <div class="py-3 px-4 text-center border-t border-border" x-show="customerSearch.length >= 2 && customerResults.length === 0 && !customerSearchLoading">
                                            <p class="text-[0.8125rem] text-dg-400 mb-2">Cliente não encontrado</p>
                                            <button class="inline-flex items-center gap-1 py-1.5 px-3 bg-[#111827] text-white rounded-md text-[0.8125rem] font-medium border-0 cursor-pointer" type="button"
                                                    @click="customerForm = resetCustomerForm(); customerForm.name = customerSearch; showCustomerModal = true; customerResults = [];">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Cadastrar Agora
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="relative pt-2.5 pr-10 pb-2.5 pl-3 bg-emerald-500/10 rounded-xl border border-emerald-500/35 flex items-center gap-2.5" x-show="selectedCustomer.id">
                                    <div class="w-8 h-8 min-w-[2rem] bg-emerald-500/15 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-semibold text-dg-100 text-sm" x-text="selectedCustomer.name"></span>
                                        <span class="text-xs text-dg-400 ml-2" x-text="selectedCustomer.phone"></span>
                                    </div>
                                    <button class="absolute top-1.5 right-1.5 w-6 h-6 flex items-center justify-center text-dg-500 cursor-pointer bg-transparent border-0 rounded-md hover:bg-red-500/15 hover:text-red-600" type="button" @click="clearCustomer">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Simulações salvas do cliente --}}
                                <template x-if="customerSnapshots.length > 0">
                                    <div class="mt-1.5">
                                        <template x-for="snap in customerSnapshots" :key="snap.id">
                                            <button class="flex items-center gap-1.5 py-1 px-0 bg-transparent border-0 cursor-pointer text-[0.6875rem] text-dg-400 w-full text-left hover:text-dg-100" type="button" @click="useSnapshot(snap)">
                                                <span class="text-[0.625rem] opacity-[0.6]">&#128203;</span>
                                                <span class="overflow-hidden text-ellipsis whitespace-nowrap" x-text="snap.product_description + ' · R$ ' + Number(snap.product_price).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- PASSO 4: PAGAMENTO MISTO -->
                        <div class="bg-surface-raised rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.3)] border border-border overflow-hidden">
                            <div class="bg-[#374151] text-white py-3 px-6">
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-surface-raised text-dg-300 rounded-full flex items-center justify-center font-bold text-sm mr-2">4</span>
                                    <span class="font-semibold">Pagamento</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <!-- Dinheiro + PIX -->
                                <div class="grid grid-cols-[1fr_1fr] gap-3 mb-4">
                                    <!-- Dinheiro -->
                                    <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/35">
                                        <label class="flex items-center text-sm font-semibold text-emerald-300 mb-3">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Dinheiro
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-2 top-[50%] -translate-y-1/2 text-dg-400 text-xs">R$</span>
                                            <input class="w-full pt-2 pr-2 pb-2 pl-8 border border-green-300 rounded-lg text-sm bg-surface-raised" type="number" name="cash_payment" x-model.number="cashPayment" @input="updateTotals"
                                                   step="0.01" min="0" placeholder="0,00">
                                        </div>
                                    </div>
                                    <!-- PIX -->
                                    <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-300">
                                        <label class="flex items-center text-sm font-semibold text-emerald-300 mb-3">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            PIX
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-2 top-[50%] -translate-y-1/2 text-dg-400 text-xs">R$</span>
                                            <input class="w-full pt-2 pr-2 pb-2 pl-8 border border-emerald-300 rounded-lg text-sm bg-surface-raised" type="number" name="pix_payment" x-model.number="pixPayment" @input="updateTotals"
                                                   step="0.01" min="0" placeholder="0,00">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Parcelamentos em Cartões (Múltiplos) com Taxa Stone -->
                                <div class="mb-4 p-4 bg-blue-500/10 rounded-xl border border-blue-500/35" x-show="saleType === 'cliente_final' || saleType === null">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="flex items-center text-sm font-semibold text-blue-300">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Parcelamento em Cartão
                                            <span class="text-[0.6875rem] font-normal text-dg-400 ml-2">(Taxa Stone aplicada)</span>
                                        </label>
                                        <button class="flex items-center gap-1 py-1 px-2 bg-blue-500 text-white rounded-md text-xs border-0 cursor-pointer" type="button" @click="addCardPayment()">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Adicionar Cartão
                                        </button>
                                    </div>
                                    
                                    <!-- Lista de cartões -->
                                    <template x-for="(card, cardIndex) in cardPayments" :key="cardIndex">
                                        <div class="bg-surface-raised rounded-lg p-3 mb-2 border border-[#dbeafe]">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-semibold text-blue-500" x-text="'Cartão ' + (cardIndex + 1)"></span>
                                                <button class="p-0.5 text-red-500 bg-transparent border-0 cursor-pointer" type="button" @click="removeCardPayment(cardIndex)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-[1fr_1fr] gap-2">
                                                <div>
                                                    <label class="block text-[0.7rem] text-dg-400 mb-0.5">Valor Líquido (recebido)</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2 top-[50%] -translate-y-1/2 text-dg-400 text-[0.7rem]">R$</span>
                                                        <input class="w-full pt-1.5 pr-1.5 pb-1.5 pl-7 bg-surface-overlay text-dg-100 border border-white/[0.08] rounded-md text-sm" type="number" :name="'card_payments['+cardIndex+'][amount]'"
                                                               x-model.number="card.amount" @input="calculateCardFee(cardIndex)"
                                                               step="0.01" min="0" placeholder="0,00">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[0.7rem] text-dg-400 mb-0.5">Parcelas</label>
                                                    <select class="w-full p-1.5 border border-white/[0.08] rounded-md text-sm bg-surface-raised" :name="'card_payments['+cardIndex+'][installments]'"
                                                            x-model.number="card.installments"
                                                            @change="calculateCardFee(cardIndex)">
                                                        @for($i = 1; $i <= 21; $i++)
                                                            <option value="{{ $i }}">{{ $i }}x</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Info da taxa Stone -->
                                            <div class="mt-2 p-2 bg-white/[0.04] rounded-md border border-border" x-show="card.amount > 0 && card.installments >= 1">
                                                <div class="text-xs text-[#c4c4c4] font-medium">
                                                    <span x-text="card.installments + 'x'"></span> - 
                                                    Taxa: <strong x-text="(card.mdr_rate || 0).toFixed(2) + '%'"></strong> - 
                                                    Você recebe: <strong class="text-green-600" x-text="formatMoney(card.amount)"></strong>
                                                </div>
                                                <div class="flex justify-between items-center text-[0.7rem] text-dg-400 mt-1.5">
                                                    <span>Cliente paga: <strong x-text="formatMoney(card.gross_amount || card.amount || 0)"></strong></span>
                                                    <span class="text-red-300">Taxa: <strong x-text="formatMoney(card.fee_amount || 0)"></strong></span>
                                                </div>
                                                <div class="text-[0.7rem] text-dg-400 text-right mt-0.5" x-show="card.installments > 1">
                                                    <span x-text="card.installments + 'x de ' + formatMoney((card.gross_amount || card.amount || 0) / card.installments)"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <div class="text-center p-4 text-dg-400 text-xs" x-show="cardPayments.length === 0">
                                        Clique em "Adicionar Cartão" para parcelar
                                    </div>
                                </div>
                                
                                <!-- Aviso: Cartão não disponível para Repasse -->
                                <div class="mb-4 p-4 bg-[rgba(251,191,36,0.1)] border border-[rgba(251,191,36,0.3)] rounded-xl" x-show="saleType === 'repasse'">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm text-dg-300 font-medium">
                                            <strong class="text-amber-400">Pagamento com cartão não disponível</strong> para Repasse. Use dinheiro ou PIX.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Campos ocultos para compatibilidade -->
                                <input type="hidden" name="payment_method" :value="getMainPaymentMethod()">
                                <input type="hidden" name="card_payment" :value="totalCardPayments">
                                <input type="hidden" name="installments" :value="cardPayments.length > 0 ? cardPayments[0].installments : 1">
                                <input type="hidden" name="trade_in_value" :value="hasTradeIn ? totalTradeInValue : 0">
                                <input type="hidden" name="cash_payment_method" :value="cashPayment > 0 ? 'cash' : (pixPayment > 0 ? 'pix' : '')">
                                
                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-dg-300 mb-1">Status</label>
                                    <select class="w-full py-2 px-3 border-2 border-white/[0.08] rounded-lg outline-none bg-surface-raised" name="payment_status" x-model="paymentStatus" required>
                                        @foreach($paymentStatuses as $status)
                                            <option value="{{ $status->value }}" {{ $status->value === 'paid' ? 'selected' : '' }}>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- RESUMO FINAL -->
                        <div class="bg-[#111827] text-white rounded-2xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.25)] overflow-hidden">
                            <div class="p-6">
                                <h3 class="text-lg font-bold mb-4">Resumo da Venda</h3>
                                
                                <dl class="flex flex-col gap-2">
                                    <div class="flex justify-between text-dg-600">
                                        <dt>Itens</dt>
                                        <dd x-text="items.length + ' produto(s)'"></dd>
                                    </div>
                                    <div class="flex justify-between text-dg-600">
                                        <dt>Subtotal</dt>
                                        <dd x-text="formatMoney(subtotal)"></dd>
                                    </div>
                                    @if(auth()->user()->canViewFinancials())
                                    <div class="flex justify-between text-dg-500 text-[0.8125rem]" x-show="totalCost > 0">
                                        <dt>Custo total</dt>
                                        <dd x-text="formatMoney(totalCost)"></dd>
                                    </div>
                                    <div class="flex justify-between text-dg-500 text-[0.8125rem]" x-show="totalFreight > 0">
                                        <dt>Frete total</dt>
                                        <dd x-text="formatMoney(totalFreight)"></dd>
                                    </div>
                                    <div class="flex justify-between text-[0.8125rem]" x-show="totalCost > 0"
                                         :style="{ color: totalProfit >= 0 ? '#4ade80' : '#f87171' }">
                                        <dt>Lucro estimado</dt>
                                        <dd x-text="formatMoney(totalProfit)"></dd>
                                    </div>
                                    @endif

                                    {{-- Prévia de comissão do vendedor --}}
                                    <div class="mt-1 py-2 px-2.5 bg-[#1a1a2e] border border-[#7c3aed40] rounded-lg" x-show="sellerHasCommission && items.length > 0 && estimatedCommission > 0">
                                        <div class="flex justify-between items-center">
                                            <dt class="text-xs text-violet-400 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Comissão <span class="font-semibold" x-text="sellerName"></span>
                                            </dt>
                                            <dd class="text-[0.9375rem] font-bold text-violet-300" x-text="formatMoney(estimatedCommission)"></dd>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <dt class="text-dg-600">Desconto</dt>
                                        <dd>
                                            <input class="w-20 text-right py-1 px-2 bg-[#1f2937] border border-[#374151] rounded-lg text-white outline-none text-sm"
                                                type="number"
                                                name="discount"
                                                x-model.number="discount"
                                                @input="updateTotals"
                                                step="0.01"
                                                min="0">
                                        </dd>
                                    </div>
                                    
                                    <div class="pt-3 mt-2 border-t border-[#374151]">
                                        <div class="flex justify-between text-xl font-bold">
                                            <dt>TOTAL</dt>
                                            <dd x-text="formatMoney(total)"></dd>
                                        </div>
                                    </div>
                                    
                                    <!-- Breakdown do pagamento -->
                                    <div class="pt-3 mt-2 border-t border-[#374151]" x-show="hasTradeIn || cashPayment > 0 || pixPayment > 0 || totalCardPayments > 0">
                                        <div class="text-xs font-semibold text-dg-500 mb-2">FORMA DE PAGAMENTO</div>
                                        <template x-for="(ti, tiIdx) in tradeIns" :key="tiIdx">
                                            <div class="flex justify-between text-violet-400 text-sm" x-show="hasTradeIn && ti.estimated_value > 0">
                                                <dt x-text="'Trade-in ' + (tradeIns.length > 1 ? (tiIdx + 1) + ' ' : '') + '(' + (ti.device_name || 'aparelho') + ')'"></dt>
                                                <dd x-text="formatMoney(ti.estimated_value || 0)"></dd>
                                            </div>
                                        </template>
                                        <div class="flex justify-between text-green-300 text-sm" x-show="cashPayment > 0">
                                            <dt>Dinheiro</dt>
                                            <dd x-text="formatMoney(cashPayment)"></dd>
                                        </div>
                                        <div class="flex justify-between text-emerald-300 text-sm" x-show="pixPayment > 0">
                                            <dt>PIX</dt>
                                            <dd x-text="formatMoney(pixPayment)"></dd>
                                        </div>
                                        <template x-for="(card, idx) in cardPayments" :key="idx">
                                            <div class="flex justify-between text-blue-300 text-sm" x-show="card.amount > 0">
                                                <dt x-text="'Cartão ' + (idx + 1) + ' (' + card.installments + 'x)'"></dt>
                                                <dd x-text="formatMoney(card.amount)"></dd>
                                            </div>
                                        </template>
                                        <div class="flex justify-between text-sm mt-2 pt-2" style="border-top: 1px dashed #374151;"
                                             :style="{ color: paymentDifference === 0 ? '#4ade80' : '#fbbf24' }">
                                            <dt>Soma dos pagamentos</dt>
                                            <dd x-text="formatMoney(totalPayments)"></dd>
                                        </div>
                                        
                                        <!-- Alerta de diferença -->
                                        <div class="mt-2 p-2 rounded-md" x-show="items.length > 0 && Math.abs(paymentDifference) > 0.01"

                                             :style="{ background: paymentDifference > 0 ? '#7f1d1d' : '#713f12' }">
                                            <p class="text-xs" :style="{ color: paymentDifference > 0 ? '#fecaca' : '#fef08a' }">
                                                <span x-show="paymentDifference > 0">
                                                    <strong>Faltam:</strong> <span x-text="formatMoney(paymentDifference)"></span>
                                                </span>
                                                <span x-show="paymentDifference < 0">
                                                    <strong>Excedente:</strong> <span x-text="formatMoney(Math.abs(paymentDifference))"></span>
                                                </span>
                                            </p>
                                        </div>
                                        
                                        <!-- Indicador de pagamento correto -->
                                        <div class="mt-2 p-2 bg-[#14532d] rounded-md" x-show="items.length > 0 && Math.abs(paymentDifference) <= 0.01 && totalPayments > 0">
                                            <p class="text-xs text-green-300 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <svg class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
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
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showCustomerModal" x-cloak

                 @keydown.escape.window="showCustomerModal = false">
                <div class="fixed inset-0 bg-black/50" @click="showCustomerModal = false"></div>
                <div class="relative bg-surface-raised rounded-2xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.6)] w-full max-w-[32rem] max-h-[90vh] overflow-y-auto"
                     @click.stop>
                    <div class="py-5 px-6 border-b border-border flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-500/10 rounded-[0.625rem] flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-dg-100">Cadastro Rápido de Cliente</h3>
                                <p class="text-[0.8125rem] text-dg-400">O cliente será vinculado automaticamente à venda</p>
                            </div>
                        </div>
                        <button class="p-1.5 text-dg-500 cursor-pointer bg-transparent border-0 rounded-md hover:bg-white/[0.06]" type="button" @click="showCustomerModal = false">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="mb-4 p-3 bg-red-500/10 border border-red-500/35 rounded-lg" x-show="customerFormError">
                            <p class="text-[0.8125rem] text-red-300" x-text="customerFormError"></p>
                        </div>
                        <div class="grid grid-cols-[1fr_1fr] gap-4">
                            <div class="col-span-2">
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Nome <span class="text-red-300">*</span></label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="customerForm.name" x-ref="customerNameInput"
                                       placeholder="Nome completo do cliente"


                                       @keydown.enter.prevent="saveCustomer">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Telefone <span class="text-red-300">*</span></label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="customerForm.phone"
                                       placeholder="(00) 00000-0000"


                                       @keydown.enter.prevent="saveCustomer">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">CPF</label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="customerForm.cpf"
                                       placeholder="000.000.000-00">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Instagram</label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="customerForm.instagram"
                                       placeholder="@usuario">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Data de Nascimento</label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="date" x-model="customerForm.birth_date">
                            </div>
                        </div>
                    </div>
                    <div class="py-4 px-6 border-t border-border flex justify-end gap-3 bg-surface-overlay rounded-b-2xl">
                        <button class="py-2.5 px-5 bg-surface-raised text-dg-300 font-medium border border-white/[0.08] rounded-lg text-sm cursor-pointer" type="button" @click="showCustomerModal = false">
                            Cancelar
                        </button>
                        <button class="py-2.5 px-5 bg-[#111827] text-white font-semibold border-0 rounded-lg text-sm cursor-pointer flex items-center gap-2 hover:bg-[#374151]" type="button" @click="saveCustomer" :disabled="customerFormSaving">
                            <svg class="w-4 h-4 animate-spin" x-show="customerFormSaving" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="customerFormSaving ? 'Salvando...' : 'Salvar e Vincular'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL: CADASTRO RÁPIDO DE PRODUTO -->
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showProductModal" x-cloak

                 @keydown.escape.window="showProductModal = false">
                <div class="fixed inset-0 bg-black/50" @click="showProductModal = false"></div>
                <div class="relative bg-surface-raised rounded-2xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.6)] w-full max-w-[40rem] max-h-[90vh] overflow-y-auto"
                     @click.stop>
                    <div class="py-5 px-6 border-b border-border flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500/10 rounded-[0.625rem] flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-dg-100">Cadastro Rápido de Produto</h3>
                                <p class="text-[0.8125rem] text-dg-400">O produto será adicionado automaticamente à venda</p>
                            </div>
                        </div>
                        <button class="p-1.5 text-dg-500 cursor-pointer bg-transparent border-0 rounded-md hover:bg-white/[0.06]" type="button" @click="showProductModal = false">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="mb-4 p-3 bg-red-500/10 border border-red-500/35 rounded-lg" x-show="productFormError">
                            <p class="text-[0.8125rem] text-red-300" x-text="productFormError"></p>
                        </div>
                        <div class="grid grid-cols-[1fr_1fr] gap-4">
                            <div class="col-span-2">
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Nome <span class="text-red-300">*</span></label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="productForm.name" x-ref="productNameInput"
                                       placeholder="Ex: iPhone 15 Pro Max 256GB">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Categoria <span class="text-red-300">*</span></label>
                                <select class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm bg-surface-overlay outline-none" x-model="productForm.category" @change="generateProductSku">
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
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Condição <span class="text-red-300">*</span></label>
                                <select class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm bg-surface-overlay outline-none" x-model="productForm.condition">
                                    @foreach(\App\Domain\Product\Enums\ProductCondition::cases() as $condition)
                                        <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">SKU <span class="text-red-300">*</span></label>
                                <div class="flex gap-2">
                                    <input class="flex-1 py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="productForm.sku"
                                           placeholder="Gerado automaticamente">
                                    <button class="p-2 bg-surface-elevated border-2 border-white/[0.08] rounded-lg cursor-pointer" type="button" @click="generateProductSku" title="Gerar SKU">
                                        <svg class="w-[1.125rem] h-[1.125rem] text-dg-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">IMEI</label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="productForm.imei"
                                       placeholder="000000000000000">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Modelo <span class="text-red-300">*</span></label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="productForm.model"
                                       placeholder="Ex: 15 Pro Max">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Armazenamento <span class="text-red-300">*</span></label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="productForm.storage"
                                       placeholder="Ex: 256GB">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Cor <span class="text-red-300">*</span></label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="text" x-model="productForm.color"
                                       placeholder="Ex: Preto">
                            </div>
                            <div>
                                <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Quantidade <span class="text-red-300">*</span></label>
                                <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="number" x-model.number="productForm.stock_quantity" min="1">
                            </div>
                        </div>

                        <!-- Campos Seminovo -->
                        <div class="mt-4 p-4 bg-amber-500/10 border border-amber-500/35 rounded-lg" x-show="productForm.condition === 'used' || productForm.condition === 'refurbished'"
                             x-transition>
                            <p class="text-[0.8125rem] font-semibold text-amber-400 mb-3">Informações do Seminovo</p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="flex items-center cursor-pointer gap-2">
                                        <input class="w-4 h-4 rounded accent-[#e3e3e3]" type="checkbox" x-model="productForm.has_box">
                                        <span class="text-[0.8125rem] text-dg-300">Tem caixa</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center cursor-pointer gap-2">
                                        <input class="w-4 h-4 rounded accent-[#e3e3e3]" type="checkbox" x-model="productForm.has_cable">
                                        <span class="text-[0.8125rem] text-dg-300">Tem cabo</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-dg-400 mb-1">Saúde da Bateria (%)</label>
                                    <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-dg-500" type="number" x-model.number="productForm.battery_health" min="0" max="100" placeholder="Ex: 87">
                                </div>
                            </div>
                        </div>

                        <p class="mt-4 p-3 bg-blue-500/10 border border-blue-500/35 rounded-lg text-[0.8125rem] text-blue-300">
                            Os valores de custo e venda serão informados na hora da venda.
                        </p>
                    </div>
                    <div class="py-4 px-6 border-t border-border flex justify-end gap-3 bg-surface-overlay rounded-b-2xl">
                        <button class="py-2.5 px-5 bg-surface-raised text-dg-300 font-medium border border-white/[0.08] rounded-lg text-sm cursor-pointer" type="button" @click="showProductModal = false">
                            Cancelar
                        </button>
                        <button class="py-2.5 px-5 bg-[#111827] text-white font-semibold border-0 rounded-lg text-sm cursor-pointer flex items-center gap-2 hover:bg-[#374151]" type="button" @click="saveProduct" :disabled="productFormSaving">
                            <svg class="w-4 h-4 animate-spin" x-show="productFormSaving" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="productFormSaving ? 'Salvando...' : 'Salvar e Adicionar à Venda'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL: ENTRADA RÁPIDA DE ESTOQUE -->
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showStockModal" x-cloak

                 @keydown.escape.window="showStockModal = false">
                <div class="fixed inset-0 bg-black/50" @click="showStockModal = false"></div>
                <div class="relative bg-surface-raised rounded-2xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.6)] w-full max-w-[28rem]"
                     @click.stop>
                    <div class="py-5 px-6 border-b border-border flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-500/[0.12] rounded-[0.625rem] flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-dg-100">Entrada Rápida de Estoque</h3>
                                <p class="text-[0.8125rem] text-dg-400" x-text="stockItemName"></p>
                            </div>
                        </div>
                        <button class="p-1.5 text-dg-500 cursor-pointer bg-transparent border-0 rounded-md hover:bg-white/[0.06]" type="button" @click="showStockModal = false">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="mb-4 p-3 bg-red-500/10 border border-red-500/35 rounded-lg" x-show="stockFormError">
                            <p class="text-[0.8125rem] text-red-300" x-text="stockFormError"></p>
                        </div>
                        <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/35 rounded-lg" x-show="stockFormSuccess">
                            <p class="text-[0.8125rem] text-emerald-300" x-text="stockFormSuccess"></p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Quantidade a adicionar <span class="text-red-300">*</span></label>
                            <input class="w-full p-3 border-2 border-white/[0.08] rounded-lg text-lg font-bold text-center outline-none focus:border-orange-600" type="number" x-model.number="stockForm.quantity" min="1" x-ref="stockQuantityInput"
                                   @keydown.enter.prevent="saveStock">
                        </div>
                        <div class="mb-4">
                            <label class="block text-[0.8125rem] font-medium text-dg-300 mb-1">Motivo <span class="text-dg-500">(opcional)</span></label>
                            <input class="w-full py-2.5 px-3 border-2 border-white/[0.08] rounded-lg text-sm outline-none focus:border-orange-600" type="text" x-model="stockForm.reason"
                                   placeholder="Ex: Reposição de estoque">
                        </div>
                        <div class="p-3 bg-surface-overlay rounded-lg border border-border mb-4">
                            <div class="flex justify-between text-[0.8125rem] text-dg-400">
                                <span>Estoque atual:</span>
                                <span class="font-semibold" x-text="stockCurrentStock + ' un.'"></span>
                            </div>
                            <div class="flex justify-between text-[0.8125rem] text-dg-100 mt-1">
                                <span class="font-semibold">Estoque após entrada:</span>
                                <span class="font-bold text-green-600" x-text="(stockCurrentStock + (stockForm.quantity || 0)) + ' un.'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="py-4 px-6 border-t border-border flex justify-end gap-3 bg-surface-overlay rounded-b-2xl">
                        <button class="py-2.5 px-5 bg-surface-raised text-dg-300 font-medium border border-white/[0.08] rounded-lg text-sm cursor-pointer" type="button" @click="showStockModal = false">
                            Cancelar
                        </button>
                        <button class="py-2.5 px-5 bg-orange-600 text-white font-semibold border-0 rounded-lg text-sm cursor-pointer flex items-center gap-2 hover:bg-orange-700" type="button" @click="saveStock" :disabled="stockFormSaving">
                            <svg class="w-4 h-4 animate-spin" x-show="stockFormSaving" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            color: #666666;
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
                    'cost_price' => (float) ($reservation->cost_price ?? 0),
                    'stock' => $reservation->product->stock_quantity ?? 1,
                ] : [
                    'id' => null,
                    'name' => $reservation->product_description ?? 'Produto da reserva',
                    'price' => (float) $reservation->product_price,
                    'cost_price' => (float) ($reservation->cost_price ?? 0),
                    'stock' => 1,
                ],
                'deposit_paid' => (float) $reservation->deposit_paid,
                'product_price' => (float) $reservation->product_price,
            ] : null) !!};

            const snapshotData = {!! json_encode(isset($snapshot) && $snapshot ? [
                'id' => $snapshot->id,
                'customer' => $snapshot->customer ? [
                    'id' => $snapshot->customer->id,
                    'name' => $snapshot->customer->name,
                    'phone' => $snapshot->customer->formatted_phone ?? '',
                ] : null,
                'product_description' => $snapshot->product_description,
                'product_price' => (float) $snapshot->product_price,
                'product_cost' => (float) ($snapshot->product_cost ?? 0),
                'trade_in_model' => $snapshot->trade_in_model,
                'trade_in_value' => (float) ($snapshot->trade_in_value ?? 0),
                'trade_in_system_value' => (float) ($snapshot->trade_in_system_value ?? 0),
                'down_payment' => (float) ($snapshot->down_payment ?? 0),
            ] : null) !!};

            const preSaleData = {!! json_encode(isset($preSale) && $preSale ? [
                'customer' => $preSale->customer ? [
                    'id' => $preSale->customer->id,
                    'name' => $preSale->customer->name,
                    'phone' => $preSale->customer->formatted_phone ?? '',
                ] : null,
                'product' => [
                    'id' => $preSale->product_id,
                    'name' => $preSale->product_name,
                    'price' => (float) $preSale->unit_price,
                    'cost_price' => (float) $preSale->cost_price,
                    'stock' => 1,
                    'is_consignment' => $preSale->isFromConsignment(),
                    'consignment_item_id' => $preSale->consignment_item_id,
                ],
                'down_payment' => (float) $preSale->down_payment,
                'trade_in' => $preSale->trade_in_device,
                'trade_in_value' => (float) ($preSale->trade_in_value ?? 0),
            ] : null) !!};

            return {
                items: [],
                searchTerm: '',
                searchResults: [],
                searchLoading: false,
                customerSearch: '',
                customerResults: [],
                customerSearchLoading: false,
                selectedCustomer: reservationData?.customer ?? snapshotData?.customer ?? preSaleData?.customer ?? { id: '', name: '', phone: '' },
                discount: reservationData?.deposit_paid ?? preSaleData?.down_payment ?? 0,
                subtotal: 0,
                total: 0,
                
                // Pagamento
                paymentStatus: 'paid',
                cashPayment: 0,
                pixPayment: 0,
                cashPaymentMethod: '',
                
                // Múltiplos cartões
                cardPayments: [],
                
                // Trade-ins (múltiplos aparelhos)
                hasTradeIn: false,
                tradeIns: [],
                
                // Tipo de venda (padrão: Cliente Final)
                saleType: 'cliente_final',
                
                // Vendedor e entrega
                sellerName: (() => {
                    const userName = '{{ auth()->user()->name ?? '' }}';
                    const allSellers = @json($sellers->pluck('name')->toArray());
                    const match = allSellers.find(s => userName.toLowerCase().includes(s.toLowerCase()));
                    return match || '';
                })(),
                sellerId: (() => {
                    const userName = '{{ auth()->user()->name ?? '' }}';
                    const sellerMap = @json($sellers->mapWithKeys(fn($s) => [$s->name => $s->id])->toArray());
                    const matchName = Object.keys(sellerMap).find(s => userName.toLowerCase().includes(s.toLowerCase()));
                    return matchName ? sellerMap[matchName] : '';
                })(),
                sellerRoles: @json($sellers->mapWithKeys(fn($s) => [$s->id => $s->role->value])->toArray()),
                deliveryType: '',
                deliveryMethod: '',

                // Simulações do cliente
                customerSnapshots: [],

                // Modal de Cliente
                showCustomerModal: false,
                customerForm: { name: '', phone: '', cpf: '', instagram: '', birth_date: '' },
                customerFormError: '',
                customerFormSaving: false,

                // Modal de Produto
                showProductModal: false,
                productForm: { name: '', sku: '', category: 'smartphone', condition: 'new', model: '', storage: '', color: '', imei: '', stock_quantity: 1, min_stock_alert: 1, has_box: false, has_cable: false, battery_health: null },
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
                    if (reservationData?.product) {
                        this.items.push({
                            id: reservationData.product.id,
                            name: reservationData.product.name,
                            price: reservationData.product.price,
                            cost_price: reservationData.product.cost_price || 0,
                            supplier_origin: '',
                            freight_type: '',
                            freight_value: 0,
                            quantity: 1,
                            stock: reservationData.product.stock,
                            is_consignment: false,
                            consignment_item_id: null,
                        });
                        this.updateTotals();
                    }

                    if (snapshotData && !reservationData) {
                        this.items.push({
                            id: '',
                            name: snapshotData.product_description,
                            price: snapshotData.product_price,
                            cost_price: snapshotData.product_cost || 0,
                            supplier_origin: '',
                            freight_type: '',
                            freight_value: 0,
                            quantity: 1,
                            stock: 1,
                            is_consignment: false,
                            consignment_item_id: null,
                        });

                        if (snapshotData.trade_in_model && snapshotData.trade_in_value > 0) {
                            this.hasTradeIn = true;
                            this.$nextTick(() => {
                                this.tradeIns.push({
                                    device_name: snapshotData.trade_in_model,
                                    device_model: snapshotData.trade_in_model,
                                    category: 'smartphone',
                                    storage: '',
                                    color: '',
                                    imei: '',
                                    estimated_value: snapshotData.trade_in_value,
                                    sale_price: null,
                                    resale_price: snapshotData.trade_in_system_value || null,
                                    condition: 'good',
                                    battery_health: null,
                                    has_box: false,
                                    has_cable: false,
                                    notes: '',
                                    checklist_id: '',
                                    checklist_label: '',
                                });
                                this.updateTotals();
                            });
                        }

                        this.updateTotals();

                        if (snapshotData.customer?.id) {
                            this.fetchCustomerSnapshots(snapshotData.customer.id);
                        }
                    }

                    if (preSaleData && !reservationData && !snapshotData) {
                        this.items.push({
                            id: preSaleData.product.id || '',
                            name: preSaleData.product.name,
                            price: preSaleData.product.price,
                            cost_price: preSaleData.product.cost_price || 0,
                            supplier_origin: '',
                            freight_type: '',
                            freight_value: 0,
                            quantity: 1,
                            stock: preSaleData.product.stock || 1,
                            is_consignment: preSaleData.product.is_consignment || false,
                            consignment_item_id: preSaleData.product.consignment_item_id || null,
                        });

                        if (preSaleData.trade_in && preSaleData.trade_in_value > 0) {
                            this.hasTradeIn = true;
                            this.$nextTick(() => {
                                this.tradeIns.push({
                                    device_name: preSaleData.trade_in.model || '',
                                    device_model: preSaleData.trade_in.model || '',
                                    category: 'smartphone',
                                    storage: '',
                                    color: '',
                                    imei: '',
                                    estimated_value: preSaleData.trade_in_value,
                                    sale_price: null,
                                    resale_price: null,
                                    condition: preSaleData.trade_in.condition || 'good',
                                    battery_health: null,
                                    has_box: false,
                                    has_cable: false,
                                    notes: '',
                                    checklist_id: '',
                                    checklist_label: '',
                                });
                                this.updateTotals();
                            });
                        }

                        this.updateTotals();

                        if (preSaleData.customer?.id) {
                            this.fetchCustomerSnapshots(preSaleData.customer.id);
                        }
                    }

                    this.generateProductSku();
                    
                    // Watcher para o tipo de venda
                    this.$watch('saleType', (newType, oldType) => {
                        this.onSaleTypeChange(newType, oldType);
                    });
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
                    total += parseFloat(this.pixPayment) || 0;
                    total += this.totalCardPayments;
                    return total;
                },
                
                // Computed: diferença entre total e pagamentos
                get paymentDifference() {
                    return this.total - this.totalPayments;
                },
                
                // Computed: pode submeter o formulário
                get canSubmit() {
                    if (!this.saleType) return false;
                    if (!this.sellerName) return false;
                    if (!this.deliveryType) return false;
                    if (this.deliveryType === 'entrega' && !this.deliveryMethod) return false;
                    if (this.items.length === 0) return false;
                    if (!this.selectedCustomer.id) return false;
                    if (this.hasTradeIn && !this.tradeInsValid) return false;
                    if (this.total <= 0) return true;
                    const diff = this.paymentDifference;
                    if (Math.abs(diff) <= 0.01) return true;
                    const allowsOpenBalance =
                        this.paymentStatus === 'pending' || this.paymentStatus === 'partial';
                    if (allowsOpenBalance && diff > 0.01) return true;
                    return false;
                },

                get tradeInsValid() {
                    return this.tradeIns.every(ti =>
                        ti.device_name &&
                        ti.storage &&
                        ti.color &&
                        ti.estimated_value > 0 &&
                        ti.sale_price > 0 &&
                        ti.resale_price !== null && ti.resale_price !== '' &&
                        ti.battery_health !== null && ti.battery_health !== ''
                    );
                },
                
                addCardPayment() {
                    this.cardPayments.push({ amount: 0, installments: 1, gross_amount: 0, mdr_rate: 0, fee_amount: 0 });
                },
                
                removeCardPayment(index) {
                    this.cardPayments.splice(index, 1);
                    this.updateTotals();
                },

                async calculateCardFee(cardIndex) {
                    const card = this.cardPayments[cardIndex];
                    if (!card || !card.amount || card.amount <= 0) {
                        card.gross_amount = 0;
                        card.mdr_rate = 0;
                        card.fee_amount = 0;
                        this.updateTotals();
                        return;
                    }

                    try {
                        const response = await fetch('{{ route("card-fees.calculate") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                net_amount: parseFloat(card.amount),
                                payment_type: 'credit',
                                installments: parseInt(card.installments) || 1,
                            })
                        });

                        const data = await response.json();
                        if (data.success && data.data) {
                            card.gross_amount = data.data.gross_amount;
                            card.mdr_rate = data.data.mdr_rate;
                            card.fee_amount = data.data.fee_amount;
                        } else {
                            card.gross_amount = card.amount;
                            card.mdr_rate = 0;
                            card.fee_amount = 0;
                        }
                    } catch (e) {
                        card.gross_amount = card.amount;
                        card.mdr_rate = 0;
                        card.fee_amount = 0;
                    }
                    this.updateTotals();
                },
                
                getMainPaymentMethod() {
                    if (this.totalCardPayments > 0) return 'credit_card';
                    if (this.pixPayment > 0) return 'pix';
                    if (this.cashPayment > 0) return 'cash';
                    return 'pix';
                },
                
                getSubmitButtonText() {
                    if (!this.saleType) {
                        return 'Selecione o tipo de venda';
                    }
                    if (!this.sellerName) {
                        return 'Selecione o vendedor';
                    }
                    if (!this.deliveryType) {
                        return 'Selecione o tipo de entrega';
                    }
                    if (this.deliveryType === 'entrega' && !this.deliveryMethod) {
                        return 'Selecione o método de entrega';
                    }
                    if (this.items.length === 0) {
                        return 'Adicione produtos para continuar';
                    }
                    if (!this.selectedCustomer.id) {
                        return 'Selecione um cliente';
                    }
                    if (this.hasTradeIn && !this.tradeInsValid) {
                        return 'Preencha todos os campos do trade-in';
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
                    const isRepasse = this.saleType === 'repasse';
                    
                    if (product.is_consignment) {
                        const existingConsignment = this.items.find(i => i.consignment_item_id === product.consignment_item_id);
                        if (existingConsignment) {
                            if (existingConsignment.quantity < existingConsignment.stock) {
                                existingConsignment.quantity++;
                            }
                        } else {
                            const costPrice = parseFloat(product.cost_price) || 0;
                            const price = isRepasse ? costPrice + 30 : (parseFloat(product.suggested_price) || 0);
                            
                            this.items.push({
                                id: product.id,
                                name: product.name,
                                price: price,
                                original_sale_price: parseFloat(product.suggested_price) || 0,
                                original_cost_price: costPrice,
                                cost_price: isRepasse ? 0 : costPrice,
                                financial_cost: isRepasse ? 0 : costPrice,
                                commission_cost: isRepasse ? 0 : costPrice,
                                supplier_origin: '',
                                freight_type: '',
                                freight_value: 0,
                                quantity: 1,
                                stock: product.stock,
                                from_trade_in: false,
                                condition: product.condition || null,
                                is_consignment: true,
                                consignment_item_id: product.consignment_item_id,
                            });
                        }
                    } else if (product.is_fragrance) {
                        const existingFrag = this.items.find(i => i.fragrance_id === product.fragrance_id);
                        if (existingFrag) {
                            if (existingFrag.quantity < existingFrag.stock) {
                                existingFrag.quantity++;
                            }
                        } else {
                            const costPrice = parseFloat(product.cost_price) || 0;
                            const price = isRepasse ? costPrice + 30 : (parseFloat(product.sale_price) || 0);

                            this.items.push({
                                id: null,
                                name: product.name,
                                category: 'perfume',
                                price: price,
                                original_sale_price: parseFloat(product.sale_price) || 0,
                                original_cost_price: costPrice,
                                cost_price: isRepasse ? 0 : costPrice,
                                financial_cost: isRepasse ? 0 : costPrice,
                                commission_cost: isRepasse ? 0 : costPrice,
                                supplier_origin: '',
                                freight_type: '',
                                freight_value: 0,
                                quantity: 1,
                                stock: product.stock,
                                from_trade_in: false,
                                condition: 'new',
                                is_consignment: false,
                                consignment_item_id: null,
                                is_fragrance: true,
                                fragrance_id: product.fragrance_id,
                            });
                        }
                    } else {
                        const existing = this.items.find(i => i.id === product.id && !i.is_consignment);
                        if (existing) {
                            if (existing.quantity < existing.stock) {
                                existing.quantity++;
                            }
                        } else {
                            const costPrice = parseFloat(product.cost_price) || 0;
                            const price = isRepasse ? costPrice + 30 : (parseFloat(product.sale_price) || 0);
                            
                            this.items.push({
                                id: product.id,
                                name: product.name,
                                category: product.category || null,
                                price: price,
                                original_sale_price: parseFloat(product.sale_price) || 0,
                                original_cost_price: costPrice,
                                cost_price: isRepasse ? 0 : costPrice,
                                financial_cost: isRepasse ? 0 : costPrice,
                                commission_cost: isRepasse ? 0 : costPrice,
                                supplier_origin: '',
                                freight_type: '',
                                freight_value: 0,
                                quantity: 1,
                                stock: product.stock,
                                from_trade_in: product.from_trade_in || false,
                                condition: product.condition || null,
                                is_consignment: false,
                                consignment_item_id: null,
                                is_fragrance: false,
                                fragrance_id: null,
                            });
                        }
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
                    this.fetchCustomerSnapshots(customer.id);
                },

                async fetchCustomerSnapshots(customerId) {
                    this.customerSnapshots = [];
                    try {
                        const res = await fetch(`/api/simulations/customer/${customerId}`);
                        const data = await res.json();
                        this.customerSnapshots = data || [];
                    } catch (e) {
                        this.customerSnapshots = [];
                    }
                },

                useSnapshot(snap) {
                    const desc = snap.product_description || '';
                    const price = parseFloat(snap.product_price) || 0;

                    this.searchTerm = desc;
                    this.searchResults = [];

                    if (this.items.length === 0) {
                        this.items.push({
                            product_id: '',
                            product_name: desc,
                            quantity: 1,
                            unit_price: price,
                            serial_number: '',
                            notes: 'Via simulação salva',
                        });
                    } else {
                        this.items[0].product_name = desc;
                        this.items[0].unit_price = price;
                    }

                    if (snap.trade_in_model && parseFloat(snap.trade_in_value) > 0) {
                        this.hasTradeIn = true;
                        if (this.tradeIns.length === 0) {
                            this.tradeIns.push({
                                device_name: snap.trade_in_model,
                                device_model: snap.trade_in_model,
                                category: 'smartphone',
                                storage: '',
                                color: '',
                                imei: '',
                                estimated_value: parseFloat(snap.trade_in_value),
                                sale_price: null,
                                resale_price: parseFloat(snap.trade_in_system_value) || null,
                                condition: 'good',
                                battery_health: null,
                                has_box: false,
                                has_cable: false,
                                notes: '',
                                checklist_id: '',
                                checklist_label: '',
                            });
                        } else {
                            this.tradeIns[0].device_name = snap.trade_in_model;
                            this.tradeIns[0].device_model = snap.trade_in_model;
                            this.tradeIns[0].estimated_value = parseFloat(snap.trade_in_value);
                            this.tradeIns[0].resale_price = parseFloat(snap.trade_in_system_value) || null;
                        }
                    }

                    this.updateTotals();
                },
                
                clearCustomer() {
                    this.selectedCustomer = { id: '', name: '', phone: '' };
                    this.customerSnapshots = [];
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
                
                onSaleTypeChange(newType, oldType) {
                    if (!newType || !oldType || newType === oldType) {
                        return;
                    }
                    
                    if (newType === 'repasse') {
                        // Limpar trade-ins
                        this.hasTradeIn = false;
                        this.tradeIns = [];
                        
                        // Limpar pagamentos de cartão
                        this.cardPayments = [];
                        
                        // Recalcular preços (custo original + 30) e zerar custos
                        this.items.forEach(item => {
                            const originalCost = item.original_cost_price != null ? parseFloat(item.original_cost_price) : (parseFloat(item.cost_price) || 0);
                            item.original_cost_price = originalCost;
                            item.price = originalCost + 30;
                            item.cost_price = 0;
                            item.financial_cost = 0;
                            item.commission_cost = 0;
                        });
                    } else if (newType === 'cliente_final') {
                        // Restaurar preços e custos originais
                        this.items.forEach(item => {
                            if (item.original_sale_price) {
                                item.price = parseFloat(item.original_sale_price) || 0;
                            }
                            if (item.original_cost_price != null) {
                                const oc = parseFloat(item.original_cost_price) || 0;
                                item.cost_price = oc;
                                item.financial_cost = oc;
                                item.commission_cost = oc;
                            }
                        });
                    }
                    
                    this.updateTotals();
                },

                addTradeIn() {
                    this.tradeIns.push({
                        device_name: '',
                        device_model: '',
                        category: 'smartphone',
                        storage: '',
                        color: '',
                        imei: '',
                        estimated_value: 0,
                        sale_price: null,
                        resale_price: null,
                        condition: 'good',
                        battery_health: null,
                        has_box: false,
                        has_cable: false,
                        notes: '',
                        checklist_id: '',
                        checklist_label: '',
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

                syncCosts(item) {
                    item.financial_cost = item.cost_price;
                    item.commission_cost = item.cost_price;
                },

                getItemFreightAmount(item) {
                    if (!item.supplier_origin || !item.freight_type) return 0;
                    const baseCost = parseFloat(item.financial_cost) || 0;
                    if (item.freight_type === 'percentage') {
                        return baseCost * ((parseFloat(item.freight_value) || 0) / 100);
                    }
                    if (item.freight_type === 'fixed') {
                        return parseFloat(item.freight_value) || 0;
                    }
                    return 0;
                },

                getItemTotalCost(item) {
                    return (parseFloat(item.financial_cost) || 0) + this.getItemFreightAmount(item);
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

                get sellerHasCommission() {
                    if (!this.sellerId || this.saleType === 'repasse') return false;
                    const role = this.sellerRoles[this.sellerId];
                    return role === 'seller' || role === 'intern';
                },

                get estimatedCommission() {
                    if (!this.sellerHasCommission) return 0;

                    let profitComm = 0;
                    let accessoryComm = 0;
                    const deviceCats = ['smartphone', 'tablet', 'notebook', 'smartwatch'];
                    const accMinPrices = { case: 10, charger: 50, cable: 100 };
                    const accRates = { case: 0.50, charger: 0.50, cable: 0.20 };

                    for (const item of this.items) {
                        const cat = item.category || '';
                        const unitPrice = parseFloat(item.price) || 0;
                        const qty = parseInt(item.quantity) || 0;
                        const commCost = parseFloat(item.commission_cost) || 0;

                        if (deviceCats.includes(cat)) {
                            const profit = (unitPrice - commCost) * qty;
                            if (profit > 0) profitComm += profit * 0.10;
                        }

                        if (accMinPrices[cat] !== undefined) {
                            const surplus = (unitPrice - accMinPrices[cat]) * qty;
                            if (surplus > 0) accessoryComm += surplus * accRates[cat];
                        }
                    }

                    return Math.round((profitComm + accessoryComm) * 100) / 100;
                },
                
                formatMoney(value) {
                    return 'R$ ' + (parseFloat(value) || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                toMoneyMask(value) {
                    const num = Math.round((parseFloat(value) || 0) * 100);
                    if (num === 0) return '';
                    const str = String(num).padStart(3, '0');
                    const intPart = str.slice(0, -2);
                    const decPart = str.slice(-2);
                    const formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    return formatted + ',' + decPart;
                },

                parseMoney(masked) {
                    if (!masked) return 0;
                    const clean = String(masked).replace(/[^\d,]/g, '').replace(',', '.');
                    return parseFloat(clean) || 0;
                },

                applyMoneyMask(event) {
                    let val = event.target.value.replace(/\D/g, '');
                    if (!val) { event.target.value = ''; return; }
                    val = String(parseInt(val, 10));
                    val = val.padStart(3, '0');
                    const intPart = val.slice(0, -2);
                    const decPart = val.slice(-2);
                    const formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    event.target.value = formatted + ',' + decPart;
                },

                // ========== MODAL CLIENTE ==========
                resetCustomerForm() {
                    return { name: '', phone: '', cpf: '', instagram: '', birth_date: '' };
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
                    const form = { name: '', sku: '', category: 'smartphone', condition: 'new', model: '', storage: '', color: '', imei: '', stock_quantity: 1, min_stock_alert: 1 };
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
                    
                    if (!this.productForm.name || !this.productForm.sku || !this.productForm.category || !this.productForm.condition || !this.productForm.model || !this.productForm.storage || !this.productForm.color) {
                        this.productFormError = 'Preencha os campos obrigatórios: Nome, SKU, Categoria, Condição, Modelo, Armazenamento e Cor.';
                        return;
                    }

                    this.productFormSaving = true;

                    try {
                        const formData = { ...this.productForm, active: true };
                        if (formData.condition === 'used' || formData.condition === 'refurbished') {
                            formData.has_box = formData.has_box ? 1 : 0;
                            formData.has_cable = formData.has_cable ? 1 : 0;
                        } else {
                            delete formData.has_box;
                            delete formData.has_cable;
                            delete formData.battery_health;
                        }
                        const payload = formData;

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
