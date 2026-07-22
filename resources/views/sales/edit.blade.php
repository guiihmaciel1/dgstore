<x-app-layout>
    <x-slot name="title">Editar Venda</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('sales.show', $sale) }}" class="p-2 text-dg-500 rounded-lg hover:bg-surface-overlay transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-lg sm:text-2xl font-bold text-dg-100">Editar Venda #{{ $sale->sale_number }}</h1>
                    <p class="text-sm text-dg-500">{{ $sale->sold_at->format('d/m/Y \à\s H:i') }} &middot; Itens não podem ser alterados</p>
                </div>
            </div>

            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid #fecaca; border-radius: 0.5rem; color: #fca5a5;">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid #fecaca; border-radius: 0.5rem; color: #fca5a5;">
                    <ul style="list-style: disc; padding-left: 1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sales.update', $sale) }}" x-data="editSaleForm()">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    {{-- COLUNA ESQUERDA --}}
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                        {{-- Itens (somente leitura) --}}
                        <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                            <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                                <h3 style="font-weight: 600; color: #e3e3e3; font-size: 0.875rem;">Itens da Venda (somente leitura)</h3>
                            </div>
                            <div style="padding: 0;">
                                @foreach($sale->items as $item)
                                    <div style="padding: 0.75rem 1.25rem; display: flex; justify-content: space-between; align-items: center; {{ !$loop->last ? 'border-bottom: 1px solid rgba(255,255,255,0.04);' : '' }}">
                                        <div>
                                            <span style="font-weight: 500; color: #e3e3e3; font-size: 0.875rem;">{{ $item->product_name }}</span>
                                            <span style="color: #818181; font-size: 0.75rem; margin-left: 0.5rem;">x{{ $item->quantity }}</span>
                                        </div>
                                        <span style="font-weight: 600; color: #e3e3e3; font-size: 0.875rem;">{{ $item->formatted_subtotal }}</span>
                                    </div>
                                @endforeach
                                <div style="padding: 0.75rem 1.25rem; background: #1a1a1a; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between;">
                                    <span style="font-weight: 600; color: #e3e3e3;">Subtotal</span>
                                    <span style="font-weight: 700; color: #e3e3e3;">{{ $sale->formatted_subtotal }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Cliente --}}
                        <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                            <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #111827;">
                                <h3 style="font-weight: 600; color: white; font-size: 0.875rem;">Cliente</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <input type="hidden" name="customer_id" x-model="selectedCustomer.id">

                                {{-- Cliente selecionado --}}
                                <div x-show="selectedCustomer.id" style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(16,185,129,0.1); border: 1px solid #bbf7d0; border-radius: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 2rem; height: 2rem; background: #111827; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <span style="color: white; font-size: 0.75rem; font-weight: 600;" x-text="selectedCustomer.name ? selectedCustomer.name.charAt(0).toUpperCase() : ''"></span>
                                        </div>
                                        <div>
                                            <p style="font-weight: 600; color: #e3e3e3; font-size: 0.875rem;" x-text="selectedCustomer.name"></p>
                                            <p style="font-size: 0.75rem; color: #818181;" x-text="selectedCustomer.phone"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="clearCustomer()" style="padding: 0.25rem; color: #fca5a5; background: none; border: none; cursor: pointer;">
                                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Busca de cliente --}}
                                <div x-show="!selectedCustomer.id">
                                    <div style="position: relative;">
                                        <input type="text" x-model="customerSearch"
                                               @input.debounce.300ms="searchCustomers"
                                               placeholder="Buscar cliente por nome ou telefone..."
                                               style="width: 100%; padding: 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;"
                                               onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">

                                        <div x-show="customerResults.length > 0" x-cloak
                                             style="position: absolute; z-index: 20; margin-top: 0.25rem; width: 100%; background: #141414; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.06); max-height: 12rem; overflow: auto;">
                                            <template x-for="customer in customerResults" :key="customer.id">
                                                <button type="button" @click="selectCustomer(customer)"
                                                        style="width: 100%; padding: 0.5rem 0.75rem; text-align: left; display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.04); cursor: pointer; background: #141414;"
                                                        onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                                    <span style="font-weight: 500; color: #e3e3e3; font-size: 0.875rem;" x-text="customer.name"></span>
                                                    <span style="font-size: 0.75rem; color: #818181;" x-text="customer.phone"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #666666;">Deixe vazio para venda sem cliente</p>
                                </div>
                            </div>
                        </div>

                        {{-- Tipo de Venda --}}
                        <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #a4a4a4; margin-bottom: 0.75rem;">Tipo de Venda</label>
                            
                            @php
                                $hasBlockers = $sale->warranties()->exists() || $sale->commissions()->exists() || $sale->tradeIns()->exists();
                            @endphp
                            
                            @if($hasBlockers)
                                {{-- Somente leitura (bloqueado) --}}
                                <div style="padding: 0.75rem; background: #222222; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.08);">
                                    <span style="font-weight: 500; color: #e3e3e3; font-size: 0.875rem;">
                                        @if($sale->sale_type)
                                            {{ $sale->sale_type->label() }}
                                        @else
                                            Não informado
                                        @endif
                                    </span>
                                </div>
                                <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #818181;">
                                    Não é possível alterar o tipo pois a venda possui garantias, comissões ou trade-ins registrados.
                                </p>
                            @else
                                {{-- Editável (radio buttons) --}}
                                <div style="display: flex; gap: 1rem;">
                                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.625rem 0.875rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; transition: all 0.2s; {{ old('sale_type', $sale->sale_type?->value) === 'cliente_final' ? 'border-color: #3b82f6; background: rgba(59,130,246,0.1);' : '' }}">
                                        <input type="radio" name="sale_type" value="cliente_final" 
                                               {{ old('sale_type', $sale->sale_type?->value) === 'cliente_final' ? 'checked' : '' }}
                                               style="width: 1rem; height: 1rem; margin-right: 0.5rem; accent-color: #3b82f6;">
                                        <span style="font-size: 0.875rem; font-weight: 500;">Cliente Final</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.625rem 0.875rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; transition: all 0.2s; {{ old('sale_type', $sale->sale_type?->value) === 'repasse' ? 'border-color: #8b5cf6; background: rgba(168,85,247,0.1);' : '' }}">
                                        <input type="radio" name="sale_type" value="repasse" 
                                               {{ old('sale_type', $sale->sale_type?->value) === 'repasse' ? 'checked' : '' }}
                                               style="width: 1rem; height: 1rem; margin-right: 0.5rem; accent-color: #8b5cf6;">
                                        <span style="font-size: 0.875rem; font-weight: 500;">Repasse</span>
                                    </label>
                                </div>
                            @endif
                        </div>

                        {{-- Observações --}}
                        <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.5rem;">Observações</label>
                            <textarea name="notes" rows="3" placeholder="Anotações sobre a venda..."
                                      style="width: 100%; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; padding: 0.75rem; font-size: 0.875rem; outline: none; resize: vertical;"
                                      onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">{{ old('notes', $sale->notes) }}</textarea>
                        </div>
                    </div>

                    {{-- COLUNA DIREITA --}}
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                        {{-- Pagamento --}}
                        <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                            <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #111827;">
                                <h3 style="font-weight: 600; color: white; font-size: 0.875rem;">Pagamento</h3>
                            </div>
                            <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">

                                {{-- Forma de pagamento --}}
                                <div>
                                    <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.25rem;">Forma de Pagamento</label>
                                    <select name="payment_method" style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; background: #141414;">
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->value }}" {{ old('payment_method', $sale->payment_method->value) === $method->value ? 'selected' : '' }}>
                                                {{ $method->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status --}}
                                <div>
                                    <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.25rem;">Status</label>
                                    <select name="payment_status" style="width: 100%; padding: 0.5rem 0.75rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; background: #141414;">
                                        <option value="pending" {{ old('payment_status', $sale->payment_status->value) === 'pending' ? 'selected' : '' }}>Pendente</option>
                                        <option value="paid" {{ old('payment_status', $sale->payment_status->value) === 'paid' ? 'selected' : '' }}>Pago</option>
                                        <option value="partial" {{ old('payment_status', $sale->payment_status->value) === 'partial' ? 'selected' : '' }}>Parcial</option>
                                    </select>
                                </div>

                                {{-- Parcelas --}}
                                <div>
                                    <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.25rem;">Parcelas</label>
                                    <input type="number" name="installments" min="1" max="24"
                                           value="{{ old('installments', $sale->installments) }}"
                                           style="width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>

                                <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1rem;">
                                    <p style="font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase; margin-bottom: 0.75rem;">Pagamento Misto (opcional)</p>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                                        {{-- Dinheiro --}}
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; color: #818181; margin-bottom: 0.25rem;">Dinheiro (R$)</label>
                                            <input type="number" name="cash_payment" step="0.01" min="0"
                                                   value="{{ old('cash_payment', $sale->cash_payment) }}"
                                                   style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem;">
                                        </div>
                                        {{-- PIX --}}
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; color: #818181; margin-bottom: 0.25rem;">PIX (R$)</label>
                                            <input type="number" name="pix_payment" step="0.01" min="0"
                                                   value="{{ old('pix_payment', $sale->pix_payment) }}"
                                                   style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem;">
                                        </div>
                                        {{-- Cartão --}}
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; color: #818181; margin-bottom: 0.25rem;">Cartão (R$)</label>
                                            <input type="number" name="card_payment" step="0.01" min="0"
                                                   value="{{ old('card_payment', $sale->card_payment) }}"
                                                   style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem;">
                                        </div>
                                    </div>
                                </div>

                                {{-- Desconto --}}
                                <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1rem;">
                                    <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.25rem;">Desconto (R$)</label>
                                    <input type="number" name="discount" step="0.01" min="0"
                                           value="{{ old('discount', $sale->discount) }}"
                                           style="width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                            </div>
                        </div>

                        {{-- Trade-ins (somente leitura) --}}
                        @if($sale->tradeIns->isNotEmpty())
                        <div style="background: rgba(168,85,247,0.1); border-radius: 1rem; border: 1px solid #ddd6fe; padding: 1.25rem;">
                            <p style="font-size: 0.8125rem; font-weight: 600; color: #5b21b6; margin-bottom: 0.5rem;">Trade-in (não editável)</p>
                            @foreach($sale->tradeIns as $ti)
                                <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #a4a4a4; {{ !$loop->last ? 'margin-bottom: 0.25rem;' : '' }}">
                                    <span>{{ $ti->full_name }}</span>
                                    <span style="font-weight: 600;">{{ $ti->formatted_value }}</span>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Botões --}}
                        <div style="display: flex; gap: 0.75rem;">
                            <a href="{{ route('sales.show', $sale) }}"
                               style="flex: 1; text-align: center; padding: 0.75rem; background: #141414; color: #a4a4a4; font-weight: 600; font-size: 0.875rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.75rem; text-decoration: none;"
                               onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                Cancelar
                            </a>
                            <button type="submit"
                                    style="flex: 2; padding: 0.75rem; background: #111827; color: white; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 0.75rem; cursor: pointer;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function editSaleForm() {
            return {
                selectedCustomer: {
                    id: '{{ $sale->customer_id ?? '' }}',
                    name: '{{ $sale->customer?->name ?? '' }}',
                    phone: '{{ $sale->customer?->formatted_phone ?? '' }}'
                },
                customerSearch: '',
                customerResults: [],

                async searchCustomers() {
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }
                    const response = await fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(this.customerSearch)}`);
                    this.customerResults = await response.json();
                },

                selectCustomer(customer) {
                    this.selectedCustomer = { id: customer.id, name: customer.name, phone: customer.phone || '' };
                    this.customerSearch = '';
                    this.customerResults = [];
                },

                clearCustomer() {
                    this.selectedCustomer = { id: '', name: '', phone: '' };
                }
            }
        }
    </script>
    @endpush

    <style>
        @media (max-width: 768px) {
            [style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
