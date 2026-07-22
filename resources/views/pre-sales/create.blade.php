<x-app-layout>
    <x-slot name="title">Nova Pré-Venda</x-slot>
    <div class="py-4">
        <div class="px-6 lg:px-8">
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 0.5rem; color: #fca5a5;">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #fca5a5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span style="font-weight: 600; color: #fca5a5;">Corrija os erros abaixo:</span>
                    </div>
                    <ul style="list-style: disc; padding-left: 1.5rem; color: #fca5a5; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a href="{{ route('pre-sales.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #818181; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#222222'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;">Nova Pré-Venda</h1>
                        <p style="font-size: 0.875rem; color: #818181;">Registre a proposta fechada com o cliente</p>
                    </div>
                </div>
            </div>

            <div x-data="preSaleForm()" x-cloak>
                <form method="POST" action="{{ route('pre-sales.store') }}" @submit="handleSubmit($event)">
                    @csrf

                    <!-- Progress Steps -->
                    <div style="display: flex; gap: 0.25rem; margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
                        <template x-for="(label, idx) in ['Produto', 'Cliente', 'Proposta', 'Confirmação']" :key="idx">
                            <button type="button" @click="if(canGoToStep(idx + 1)) currentStep = idx + 1"
                                    :style="currentStep === idx + 1
                                        ? 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);'
                                        : (currentStep > idx + 1
                                            ? 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; background: rgba(22,163,106,0.1); color: #4ade80; border: 1px solid rgba(22,163,106,0.2); cursor: pointer;'
                                            : 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; background: rgba(255,255,255,0.03); color: #515151; border: 1px solid rgba(255,255,255,0.06);')">
                                <span x-text="(idx + 1) + '. ' + label"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="product_id" :value="product.product_id || ''">
                    <input type="hidden" name="consignment_item_id" :value="product.consignment_item_id || ''">
                    <input type="hidden" name="product_imei" :value="product.imei || ''">
                    <input type="hidden" name="product_source" :value="product.source || ''">
                    <input type="hidden" name="cost_price" :value="product.cost_price || 0">
                    <input type="hidden" name="condition" :value="product.condition || 'new'">
                    <input type="hidden" name="customer_id" :value="customer.id || ''">
                    <input type="hidden" name="final_balance" :value="finalBalance">
                    <input type="hidden" name="card_gross_amount" :value="cardGrossAmount">
                    <input type="hidden" name="card_net_amount" :value="cardNetAmount">
                    <input type="hidden" name="card_fee_rate" :value="cardFeeRate">

                    <!-- ============================================================ -->
                    <!-- PASSO 1 - PRODUTO (por IMEI) -->
                    <!-- ============================================================ -->
                    <div x-show="currentStep === 1" x-transition>
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.5rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">
                                <span style="color: #60a5fa;">①</span> Buscar Produto por IMEI
                            </h2>

                            <div style="max-width: 28rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">IMEI do Produto *</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="text" x-model="imeiSearch" @input.debounce.600ms="searchImei()"
                                           maxlength="15" inputmode="numeric"
                                           placeholder="Digite o IMEI (15 dígitos)"
                                           style="flex: 1; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.9375rem; font-family: monospace; letter-spacing: 0.05em;">
                                    <button type="button" @click="searchImei()" :disabled="searchingImei"
                                            style="padding: 0.75rem 1.25rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                        <span x-show="!searchingImei">Buscar</span>
                                        <span x-show="searchingImei">...</span>
                                    </button>
                                </div>

                                <!-- Mensagem de erro -->
                                <div x-show="imeiError" x-transition
                                     style="margin-top: 0.75rem; padding: 0.75rem; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); border-radius: 0.5rem; color: #fca5a5; font-size: 0.8125rem;">
                                    <span x-text="imeiError"></span>
                                </div>
                            </div>

                            <!-- Card do produto encontrado -->
                            <div x-show="product.imei" x-transition style="margin-top: 1.25rem;">
                                <div :style="product.reserved
                                    ? 'background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2); border-radius: 0.75rem; padding: 1.25rem;'
                                    : 'background: rgba(22,163,106,0.06); border: 1px solid rgba(22,163,106,0.2); border-radius: 0.75rem; padding: 1.25rem;'">

                                    <!-- Alerta de reservado -->
                                    <div x-show="product.reserved"
                                         style="margin-bottom: 1rem; padding: 0.75rem; background: rgba(239,68,68,0.15); border-radius: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <svg style="width: 1.25rem; height: 1.25rem; color: #f87171; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        <span style="font-size: 0.8125rem; font-weight: 600; color: #f87171;">Este produto já está reservado! Não é possível criar uma pré-venda.</span>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr)); gap: 0.75rem;">
                                        <div>
                                            <span style="font-size: 0.6875rem; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Produto</span>
                                            <div style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3; margin-top: 0.125rem;" x-text="product.name"></div>
                                        </div>
                                        <div>
                                            <span style="font-size: 0.6875rem; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Armazenamento</span>
                                            <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;" x-text="product.storage || '-'"></div>
                                        </div>
                                        <div>
                                            <span style="font-size: 0.6875rem; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Cor</span>
                                            <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;" x-text="product.color || '-'"></div>
                                        </div>
                                        <div>
                                            <span style="font-size: 0.6875rem; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Condição</span>
                                            <div style="font-size: 0.875rem; margin-top: 0.125rem;"
                                                 :style="product.condition === 'new' ? 'color: #60a5fa;' : 'color: #fbbf24;'"
                                                 x-text="product.condition === 'new' ? 'Novo' : 'Seminovo'"></div>
                                        </div>
                                        <div>
                                            <span style="font-size: 0.6875rem; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Preço Venda</span>
                                            <div style="font-size: 0.9375rem; font-weight: 600; color: #4ade80; margin-top: 0.125rem;" x-text="formatMoney(product.sale_price)"></div>
                                        </div>
                                        <div>
                                            <span style="font-size: 0.6875rem; color: #818181; text-transform: uppercase; letter-spacing: 0.05em;">Origem</span>
                                            <div style="font-size: 0.875rem; margin-top: 0.125rem;"
                                                 :style="product.source === 'own_stock' ? 'color: #60a5fa;' : 'color: #c084fc;'"
                                                 x-text="product.source === 'own_stock' ? 'Nosso Estoque' : 'Consignado (' + (product.supplier_name || '') + ')'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Navegação -->
                            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                                <button type="button" @click="goToStep2()" :disabled="!canAdvanceStep1()"
                                        :style="canAdvanceStep1()
                                            ? 'padding: 0.75rem 2rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;'
                                            : 'padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #515151; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: not-allowed;'">
                                    Próximo →
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- PASSO 2 - CLIENTE -->
                    <!-- ============================================================ -->
                    <div x-show="currentStep === 2" x-transition>
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.5rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">
                                <span style="color: #60a5fa;">②</span> Cliente
                            </h2>

                            <!-- Toggle: Existente / Novo -->
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem;">
                                <button type="button" @click="customerMode = 'search'"
                                        :style="customerMode === 'search'
                                            ? 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);'
                                            : 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                    Cliente existente
                                </button>
                                <button type="button" @click="customerMode = 'new'; customer = {id: '', name: '', phone: '', cpf: '', instagram: ''};"
                                        :style="customerMode === 'new'
                                            ? 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3);'
                                            : 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                    Novo cliente
                                </button>
                            </div>

                            <!-- Busca cliente existente -->
                            <div x-show="customerMode === 'search'" style="max-width: 28rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Buscar por nome ou telefone</label>
                                <div style="position: relative;">
                                    <input type="text" x-model="customerSearch" @input.debounce.400ms="searchCustomer()" @focus="showCustomerResults = customerResults.length > 0"
                                           placeholder="Nome ou telefone..."
                                           style="width: 100%; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">

                                    <!-- Resultados da busca -->
                                    <div x-show="showCustomerResults && customerResults.length > 0" @click.outside="showCustomerResults = false"
                                         style="position: absolute; top: 100%; left: 0; right: 0; margin-top: 0.25rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; max-height: 15rem; overflow-y: auto; z-index: 50;">
                                        <template x-for="c in customerResults" :key="c.id">
                                            <button type="button" @click="selectCustomer(c)"
                                                    style="display: block; width: 100%; padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.04); cursor: pointer;"
                                                    class="hover:bg-surface-overlay transition-colors">
                                                <div style="font-size: 0.875rem; color: #e3e3e3;" x-text="c.name"></div>
                                                <div style="font-size: 0.75rem; color: #818181;" x-text="c.phone"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Cliente selecionado -->
                                <div x-show="customer.id" x-transition style="margin-top: 0.75rem;">
                                    <div style="padding: 0.75rem 1rem; background: rgba(22,163,106,0.06); border: 1px solid rgba(22,163,106,0.2); border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <div style="font-size: 0.875rem; font-weight: 600; color: #4ade80;" x-text="customer.name"></div>
                                            <div style="font-size: 0.75rem; color: #818181;" x-text="customer.phone"></div>
                                        </div>
                                        <button type="button" @click="customer = {id: '', name: '', phone: '', cpf: '', instagram: ''}; customerSearch = '';"
                                                style="padding: 0.25rem 0.5rem; color: #f87171; font-size: 0.75rem; cursor: pointer;">
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Cadastro rápido de cliente -->
                            <div x-show="customerMode === 'new'" style="max-width: 28rem;">
                                <div style="display: grid; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Nome *</label>
                                        <input type="text" name="customer_name" x-model="newCustomer.name"
                                               style="width: 100%; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Telefone *</label>
                                        <input type="text" name="customer_phone" x-model="newCustomer.phone" maxlength="15" inputmode="tel"
                                               placeholder="(00) 00000-0000"
                                               @input="newCustomer.phone = formatPhone($event.target.value)"
                                               style="width: 100%; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">CPF</label>
                                            <input type="text" name="customer_cpf" x-model="newCustomer.cpf" maxlength="14" inputmode="numeric"
                                                   placeholder="000.000.000-00"
                                                   @input="newCustomer.cpf = formatCpf($event.target.value)"
                                                   style="width: 100%; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Instagram</label>
                                            <input type="text" name="customer_instagram" x-model="newCustomer.instagram"
                                                   placeholder="@usuario"
                                                   style="width: 100%; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Navegação -->
                            <div style="margin-top: 1.5rem; display: flex; justify-content: space-between;">
                                <button type="button" @click="currentStep = 1"
                                        style="padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                    ← Voltar
                                </button>
                                <button type="button" @click="goToStep3()" :disabled="!canAdvanceStep2()"
                                        :style="canAdvanceStep2()
                                            ? 'padding: 0.75rem 2rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;'
                                            : 'padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #515151; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: not-allowed;'">
                                    Próximo →
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- PASSO 3 - PROPOSTA -->
                    <!-- ============================================================ -->
                    <div x-show="currentStep === 3" x-transition>
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.5rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">
                                <span style="color: #60a5fa;">③</span> Proposta
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Coluna Esquerda: Valores -->
                                <div style="display: grid; gap: 1rem;">
                                    <!-- Preço de venda -->
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Preço de Venda *</label>
                                        <div style="position: relative;">
                                            <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #818181; font-size: 0.875rem;">R$</span>
                                            <input type="number" name="unit_price" x-model="unitPrice" step="0.01" min="0.01"
                                                   @input="calculateBalance()"
                                                   style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.9375rem;">
                                        </div>
                                        <div x-show="unitPrice > 0 && unitPrice < product.cost_price"
                                             style="margin-top: 0.375rem; font-size: 0.75rem; color: #f87171;">
                                            ⚠ Preço abaixo do custo (R$ <span x-text="formatNumber(product.cost_price)"></span>)
                                        </div>
                                    </div>

                                    <!-- Sinal -->
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">
                                            Sinal * <span style="font-size: 0.6875rem; color: #515151;">(mín R$ 50,00)</span>
                                        </label>
                                        <div style="position: relative;">
                                            <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #818181; font-size: 0.875rem;">R$</span>
                                            <input type="number" name="down_payment" x-model="downPayment" step="0.01" min="50"
                                                   @input="calculateBalance()"
                                                   style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.9375rem;">
                                        </div>
                                    </div>

                                    <!-- Método do sinal -->
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Método do Sinal *</label>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" @click="downPaymentMethod = 'pix'"
                                                    :style="downPaymentMethod === 'pix'
                                                        ? 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3);'
                                                        : 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                                PIX
                                            </button>
                                            <button type="button" @click="downPaymentMethod = 'cash'"
                                                    :style="downPaymentMethod === 'cash'
                                                        ? 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3);'
                                                        : 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                                Dinheiro
                                            </button>
                                        </div>
                                        <input type="hidden" name="down_payment_method" :value="downPaymentMethod">
                                    </div>

                                    <!-- Trade-in -->
                                    <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                            <input type="checkbox" x-model="hasTradeIn" id="hasTradeIn"
                                                   style="accent-color: #60a5fa;">
                                            <label for="hasTradeIn" style="font-size: 0.8125rem; color: #a4a4a4; cursor: pointer;">Tem aparelho de troca (trade-in)?</label>
                                        </div>
                                        <div x-show="hasTradeIn" x-transition style="display: grid; gap: 0.75rem;">
                                            <div>
                                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Modelo do aparelho</label>
                                                <input type="text" name="trade_in_model" x-model="tradeInModel"
                                                       placeholder="Ex: iPhone 12 128GB"
                                                       style="width: 100%; padding: 0.625rem 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Valor do trade-in</label>
                                                    <div style="position: relative;">
                                                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #818181; font-size: 0.75rem;">R$</span>
                                                        <input type="number" name="trade_in_value" x-model="tradeInValue" step="0.01" min="0"
                                                               @input="calculateBalance()"
                                                               style="width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.25rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Condição</label>
                                                    <select name="trade_in_condition" x-model="tradeInCondition"
                                                            style="width: 100%; padding: 0.625rem 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                                        <option value="">Selecione</option>
                                                        <option value="excellent">Excelente</option>
                                                        <option value="good">Bom</option>
                                                        <option value="fair">Regular</option>
                                                        <option value="poor">Ruim</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coluna Direita: Pagamento do Saldo -->
                                <div style="display: grid; gap: 1rem; align-content: start;">
                                    <!-- Forma de pagamento do saldo -->
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Forma de Pagamento do Saldo *</label>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" @click="paymentMethod = 'pix'; calculateBalance();"
                                                    :style="paymentMethod === 'pix'
                                                        ? 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3);'
                                                        : 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                                PIX
                                            </button>
                                            <button type="button" @click="paymentMethod = 'cash'; calculateBalance();"
                                                    :style="paymentMethod === 'cash'
                                                        ? 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3);'
                                                        : 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                                Dinheiro
                                            </button>
                                            <button type="button" @click="paymentMethod = 'credit_card'; calculateBalance();"
                                                    :style="paymentMethod === 'credit_card'
                                                        ? 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);'
                                                        : 'flex: 1; padding: 0.625rem; text-align: center; font-size: 0.8125rem; font-weight: 600; border-radius: 0.5rem; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                                Cartão
                                            </button>
                                        </div>
                                        <input type="hidden" name="payment_method" :value="paymentMethod">
                                    </div>

                                    <!-- Cartão: parcelas -->
                                    <div x-show="paymentMethod === 'credit_card'" x-transition>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Parcelas *</label>
                                        <select name="installments" x-model="installments" @change="calculateCardFee()"
                                                style="width: 100%; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                            <option value="">Selecione</option>
                                            <template x-for="i in 18" :key="i">
                                                <option :value="i" x-text="i + 'x'"></option>
                                            </template>
                                        </select>

                                        <!-- Detalhes da taxa -->
                                        <div x-show="cardGrossAmount > 0" x-transition
                                             style="margin-top: 0.75rem; padding: 0.75rem; background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem;">
                                            <div style="display: grid; gap: 0.375rem; font-size: 0.8125rem;">
                                                <div style="display: flex; justify-content: space-between;">
                                                    <span style="color: #818181;">Valor bruto (cobrado):</span>
                                                    <span style="color: #60a5fa; font-weight: 600;" x-text="formatMoney(cardGrossAmount)"></span>
                                                </div>
                                                <div style="display: flex; justify-content: space-between;">
                                                    <span style="color: #818181;">Taxa Stone (MDR):</span>
                                                    <span style="color: #fbbf24;" x-text="cardFeeRate + '%'"></span>
                                                </div>
                                                <div style="display: flex; justify-content: space-between;">
                                                    <span style="color: #818181;">Valor líquido (recebido):</span>
                                                    <span style="color: #4ade80; font-weight: 600;" x-text="formatMoney(cardNetAmount)"></span>
                                                </div>
                                                <div style="display: flex; justify-content: space-between;">
                                                    <span style="color: #818181;">Parcela do cliente:</span>
                                                    <span style="color: #a4a4a4;" x-text="installments > 0 ? installments + 'x de ' + formatMoney(cardGrossAmount / installments) : '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Observações -->
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Observações</label>
                                        <textarea name="notes" x-model="notes" rows="3" maxlength="2000"
                                                  placeholder="Informações adicionais sobre a proposta..."
                                                  style="width: 100%; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem; resize: vertical;"></textarea>
                                    </div>

                                    <!-- Resumo ao vivo -->
                                    <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                                        <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Resumo</div>
                                        <div style="display: grid; gap: 0.5rem; font-size: 0.8125rem;">
                                            <div style="display: flex; justify-content: space-between;">
                                                <span style="color: #818181;">Preço de venda:</span>
                                                <span style="color: #e3e3e3;" x-text="formatMoney(unitPrice)"></span>
                                            </div>
                                            <div style="display: flex; justify-content: space-between;">
                                                <span style="color: #818181;">(-) Sinal:</span>
                                                <span style="color: #60a5fa;" x-text="formatMoney(downPayment)"></span>
                                            </div>
                                            <div x-show="hasTradeIn && tradeInValue > 0" style="display: flex; justify-content: space-between;">
                                                <span style="color: #818181;">(-) Trade-in:</span>
                                                <span style="color: #c084fc;" x-text="formatMoney(tradeInValue)"></span>
                                            </div>
                                            <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.5rem; display: flex; justify-content: space-between;">
                                                <span style="color: #a4a4a4; font-weight: 600;">= Saldo restante:</span>
                                                <span style="font-weight: 700; font-size: 1rem;"
                                                      :style="finalBalance >= 0 ? 'color: #4ade80;' : 'color: #f87171;'"
                                                      x-text="formatMoney(finalBalance)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Navegação -->
                            <div style="margin-top: 1.5rem; display: flex; justify-content: space-between;">
                                <button type="button" @click="currentStep = 2"
                                        style="padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                    ← Voltar
                                </button>
                                <button type="button" @click="goToStep4()" :disabled="!canAdvanceStep3()"
                                        :style="canAdvanceStep3()
                                            ? 'padding: 0.75rem 2rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;'
                                            : 'padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #515151; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: not-allowed;'">
                                    Próximo →
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- PASSO 4 - CONFIRMAÇÃO -->
                    <!-- ============================================================ -->
                    <div x-show="currentStep === 4" x-transition>
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.5rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1.25rem;">
                                <span style="color: #60a5fa;">④</span> Confirmar Pré-Venda
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Produto -->
                                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Produto</div>
                                    <div style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3;" x-text="product.name"></div>
                                    <div style="font-size: 0.8125rem; color: #818181; margin-top: 0.25rem;">IMEI: <span style="font-family: monospace; color: #a4a4a4;" x-text="product.imei"></span></div>
                                    <div style="font-size: 0.8125rem; color: #818181; margin-top: 0.125rem;">
                                        <span x-text="product.storage || ''"></span>
                                        <span x-show="product.storage && product.color"> · </span>
                                        <span x-text="product.color || ''"></span>
                                        <span> · </span>
                                        <span :style="product.condition === 'new' ? 'color: #60a5fa;' : 'color: #fbbf24;'"
                                              x-text="product.condition === 'new' ? 'Novo' : 'Seminovo'"></span>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #515151; margin-top: 0.25rem;"
                                         x-text="product.source === 'own_stock' ? 'Nosso Estoque' : 'Consignado (' + (product.supplier_name || '') + ')'"></div>
                                </div>

                                <!-- Cliente -->
                                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Cliente</div>
                                    <div style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3;" x-text="getCustomerName()"></div>
                                    <div style="font-size: 0.8125rem; color: #818181; margin-top: 0.25rem;" x-text="getCustomerPhone()"></div>
                                </div>
                            </div>

                            <!-- Proposta Financeira -->
                            <div style="margin-top: 1rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Proposta Financeira</div>

                                <div style="display: grid; gap: 0.5rem; font-size: 0.875rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #818181;">Preço de venda:</span>
                                        <span style="color: #e3e3e3; font-weight: 600;" x-text="formatMoney(unitPrice)"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #818181;">Sinal (<span x-text="downPaymentMethod === 'pix' ? 'PIX' : 'Dinheiro'"></span>):</span>
                                        <span style="color: #60a5fa; font-weight: 600;" x-text="'- ' + formatMoney(downPayment)"></span>
                                    </div>

                                    <div x-show="hasTradeIn && tradeInValue > 0" style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #818181;">Trade-in (<span x-text="tradeInModel"></span>):</span>
                                        <span style="color: #c084fc; font-weight: 600;" x-text="'- ' + formatMoney(tradeInValue)"></span>
                                    </div>

                                    <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #a4a4a4; font-weight: 600;">Saldo restante:</span>
                                        <span style="font-weight: 700; font-size: 1.125rem; color: #4ade80;" x-text="formatMoney(finalBalance)"></span>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #818181;">Pagamento do saldo:</span>
                                        <span style="color: #a4a4a4;" x-text="paymentMethodLabel()"></span>
                                    </div>

                                    <div x-show="paymentMethod === 'credit_card' && cardGrossAmount > 0"
                                         style="padding: 0.75rem; background: rgba(59,130,246,0.06); border-radius: 0.5rem; margin-top: 0.25rem;">
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8125rem;">
                                            <span style="color: #818181;">Parcela:</span>
                                            <span style="color: #60a5fa;" x-text="installments + 'x de ' + formatMoney(cardGrossAmount / installments)"></span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-top: 0.25rem;">
                                            <span style="color: #818181;">Valor bruto (cobrado):</span>
                                            <span style="color: #a4a4a4;" x-text="formatMoney(cardGrossAmount)"></span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-top: 0.25rem;">
                                            <span style="color: #818181;">Taxa Stone:</span>
                                            <span style="color: #fbbf24;" x-text="cardFeeRate + '%'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Observações -->
                            <div x-show="notes" style="margin-top: 1rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.5rem;">Observações</div>
                                <div style="font-size: 0.875rem; color: #a4a4a4; white-space: pre-line;" x-text="notes"></div>
                            </div>

                            <!-- Navegação -->
                            <div style="margin-top: 1.5rem; display: flex; justify-content: space-between;">
                                <button type="button" @click="currentStep = 3"
                                        style="padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                    ← Voltar
                                </button>
                                <button type="submit" :disabled="submitting"
                                        style="padding: 0.75rem 2rem; background: rgba(22,163,106,0.2); color: #4ade80; border: 1px solid rgba(22,163,106,0.4); border-radius: 0.5rem; font-size: 0.9375rem; font-weight: 700; cursor: pointer;">
                                    <span x-show="!submitting">✓ Registrar Pré-Venda</span>
                                    <span x-show="submitting">Registrando...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function preSaleForm() {
            return {
                currentStep: 1,
                submitting: false,

                // Step 1 - Produto
                imeiSearch: '',
                searchingImei: false,
                imeiError: '',
                product: {
                    product_id: null,
                    consignment_item_id: null,
                    source: '',
                    name: '',
                    model: '',
                    storage: '',
                    color: '',
                    condition: 'new',
                    imei: '',
                    cost_price: 0,
                    sale_price: 0,
                    reserved: false,
                    reserved_by: null,
                    supplier_name: '',
                },

                // Step 2 - Cliente
                customerMode: 'search',
                customerSearch: '',
                customerResults: [],
                showCustomerResults: false,
                customer: { id: '', name: '', phone: '', cpf: '', instagram: '' },
                newCustomer: { name: '', phone: '', cpf: '', instagram: '' },

                // Step 3 - Proposta
                unitPrice: 0,
                downPayment: 0,
                downPaymentMethod: 'pix',
                paymentMethod: 'pix',
                installments: '',
                hasTradeIn: false,
                tradeInModel: '',
                tradeInValue: 0,
                tradeInCondition: '',
                notes: '',

                // Card fee
                cardGrossAmount: 0,
                cardNetAmount: 0,
                cardFeeRate: 0,

                // Computed
                get finalBalance() {
                    let balance = parseFloat(this.unitPrice || 0)
                        - parseFloat(this.downPayment || 0)
                        - (this.hasTradeIn ? parseFloat(this.tradeInValue || 0) : 0);
                    return Math.max(0, Math.round(balance * 100) / 100);
                },

                // Step navigation
                canGoToStep(step) {
                    if (step === 1) return true;
                    if (step === 2) return this.canAdvanceStep1();
                    if (step === 3) return this.canAdvanceStep1() && this.canAdvanceStep2();
                    if (step === 4) return this.canAdvanceStep1() && this.canAdvanceStep2() && this.canAdvanceStep3();
                    return false;
                },

                canAdvanceStep1() {
                    return this.product.imei && !this.product.reserved;
                },

                canAdvanceStep2() {
                    if (this.customerMode === 'search') {
                        return !!this.customer.id;
                    }
                    return !!(this.newCustomer.name && this.newCustomer.phone);
                },

                canAdvanceStep3() {
                    if (!this.unitPrice || this.unitPrice <= 0) return false;
                    if (!this.downPayment || this.downPayment < 50) return false;
                    if (this.paymentMethod === 'credit_card' && (!this.installments || this.installments < 1)) return false;
                    return true;
                },

                goToStep2() {
                    if (this.canAdvanceStep1()) {
                        this.unitPrice = this.product.sale_price || 0;
                        this.downPayment = this.product.condition === 'new' ? 100 : 50;
                        this.currentStep = 2;
                    }
                },

                goToStep3() {
                    if (this.canAdvanceStep2()) {
                        this.calculateBalance();
                        this.currentStep = 3;
                    }
                },

                goToStep4() {
                    if (this.canAdvanceStep3()) {
                        this.currentStep = 4;
                    }
                },

                // IMEI search
                async searchImei() {
                    const imei = this.imeiSearch.replace(/\D/g, '');
                    if (imei.length < 8) {
                        this.imeiError = 'Informe pelo menos 8 dígitos.';
                        return;
                    }

                    this.searchingImei = true;
                    this.imeiError = '';

                    try {
                        const response = await fetch(`{{ route('pre-sales.search-imei') }}?imei=${encodeURIComponent(imei)}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();

                        if (data.success && data.data) {
                            this.product = { ...data.data };
                            this.imeiError = '';
                        } else {
                            this.product = { product_id: null, consignment_item_id: null, source: '', name: '', model: '', storage: '', color: '', condition: 'new', imei: '', cost_price: 0, sale_price: 0, reserved: false, reserved_by: null, supplier_name: '' };
                            this.imeiError = data.message || 'Nenhum produto encontrado.';
                        }
                    } catch (e) {
                        this.imeiError = 'Erro ao buscar produto. Tente novamente.';
                    } finally {
                        this.searchingImei = false;
                    }
                },

                // Customer search
                async searchCustomer() {
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route('pre-sales.search-customers') }}?q=${encodeURIComponent(this.customerSearch)}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        this.customerResults = await response.json();
                        this.showCustomerResults = this.customerResults.length > 0;
                    } catch (e) {
                        this.customerResults = [];
                    }
                },

                selectCustomer(c) {
                    this.customer = { id: c.id, name: c.name, phone: c.phone, cpf: c.cpf || '', instagram: c.instagram || '' };
                    this.showCustomerResults = false;
                    this.customerSearch = c.name;
                },

                // Card fee
                async calculateCardFee() {
                    if (this.paymentMethod !== 'credit_card' || !this.installments || this.finalBalance <= 0) {
                        this.cardGrossAmount = 0;
                        this.cardNetAmount = 0;
                        this.cardFeeRate = 0;
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
                                net_amount: this.finalBalance,
                                payment_type: 'credit',
                                installments: parseInt(this.installments),
                            })
                        });

                        const data = await response.json();
                        if (data.success && data.data) {
                            this.cardGrossAmount = data.data.gross_amount;
                            this.cardNetAmount = data.data.net_amount || this.finalBalance;
                            this.cardFeeRate = data.data.mdr_rate;
                        }
                    } catch (e) {
                        this.cardGrossAmount = this.finalBalance;
                        this.cardNetAmount = this.finalBalance;
                        this.cardFeeRate = 0;
                    }
                },

                calculateBalance() {
                    if (this.paymentMethod === 'credit_card' && this.installments) {
                        this.calculateCardFee();
                    } else {
                        this.cardGrossAmount = 0;
                        this.cardNetAmount = 0;
                        this.cardFeeRate = 0;
                    }
                },

                // Helpers
                formatMoney(value) {
                    const num = parseFloat(value) || 0;
                    return 'R$ ' + num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                formatNumber(value) {
                    const num = parseFloat(value) || 0;
                    return num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                formatPhone(value) {
                    const digits = value.replace(/\D/g, '');
                    if (digits.length <= 2) return `(${digits}`;
                    if (digits.length <= 7) return `(${digits.slice(0,2)}) ${digits.slice(2)}`;
                    return `(${digits.slice(0,2)}) ${digits.slice(2,7)}-${digits.slice(7,11)}`;
                },

                formatCpf(value) {
                    const digits = value.replace(/\D/g, '');
                    if (digits.length <= 3) return digits;
                    if (digits.length <= 6) return `${digits.slice(0,3)}.${digits.slice(3)}`;
                    if (digits.length <= 9) return `${digits.slice(0,3)}.${digits.slice(3,6)}.${digits.slice(6)}`;
                    return `${digits.slice(0,3)}.${digits.slice(3,6)}.${digits.slice(6,9)}-${digits.slice(9,11)}`;
                },

                getCustomerName() {
                    return this.customerMode === 'search' ? this.customer.name : this.newCustomer.name;
                },

                getCustomerPhone() {
                    return this.customerMode === 'search' ? this.customer.phone : this.newCustomer.phone;
                },

                paymentMethodLabel() {
                    const labels = { pix: 'PIX', cash: 'Dinheiro', credit_card: 'Cartão de Crédito' };
                    let label = labels[this.paymentMethod] || this.paymentMethod;
                    if (this.paymentMethod === 'credit_card' && this.installments) {
                        label += ` ${this.installments}x`;
                    }
                    return label;
                },

                handleSubmit(event) {
                    if (this.submitting) {
                        event.preventDefault();
                        return;
                    }
                    this.submitting = true;
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
