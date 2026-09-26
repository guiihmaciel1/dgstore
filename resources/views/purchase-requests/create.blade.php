<x-app-layout>
    <x-slot name="title">Nova Solicitação de Compra</x-slot>
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
                    <a href="{{ route('purchase-requests.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #818181; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#222222'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;">Nova Solicitação de Compra</h1>
                        <p style="font-size: 0.875rem; color: #818181;">Registre a solicitação antes de comprar no fornecedor</p>
                    </div>
                </div>
            </div>

            <!-- Aviso de Blindagem -->
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(202,138,4,0.08); border: 1px solid rgba(202,138,4,0.2); border-radius: 0.75rem; display: flex; align-items: center; gap: 0.75rem;">
                <svg style="width: 1.25rem; height: 1.25rem; color: #fbbf24; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div style="font-size: 0.8125rem; color: #fbbf24;">
                    <strong>Importante:</strong> Nenhuma compra no fornecedor deve ser feita sem esta solicitação aprovada. Sinal mínimo de R$ 100,00 obrigatório.
                </div>
            </div>

            <div x-data="purchaseRequestForm()" x-cloak>
                <form method="POST" action="{{ route('purchase-requests.store') }}" @submit="handleSubmit($event)">
                    @csrf

                    <!-- Progress Steps -->
                    <div style="display: flex; gap: 0.25rem; margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
                        <template x-for="(label, idx) in ['Cliente', 'Produto', 'Venda', 'Confirmação']" :key="idx">
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
                    <input type="hidden" name="customer_id" :value="customer.id || ''">
                    <input type="hidden" name="upgrade_difference" :value="upgradeDifference">

                    <!-- ============================================================ -->
                    <!-- PASSO 1 - CLIENTE -->
                    <!-- ============================================================ -->
                    <div x-show="currentStep === 1" x-transition>
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.5rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">
                                <span style="color: #60a5fa;">①</span> Dados do Cliente
                            </h2>

                            <!-- Toggle: Existente / Novo -->
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem;">
                                <button type="button" @click="customerMode = 'existing'; resetCustomer();"
                                        :style="customerMode === 'existing'
                                            ? 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);'
                                            : 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                    Cliente Existente
                                </button>
                                <button type="button" @click="customerMode = 'new'; resetCustomer();"
                                        :style="customerMode === 'new'
                                            ? 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3);'
                                            : 'padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                    Novo Cliente
                                </button>
                            </div>

                            <!-- Busca cliente existente -->
                            <div x-show="customerMode === 'existing'" style="max-width: 28rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Buscar por nome ou telefone</label>
                                <div style="position: relative;">
                                    <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                           placeholder="Digite nome ou telefone..."
                                           style="width: 100%; padding: 0.75rem; padding-left: 2.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                    <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #515151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>

                                <!-- Resultados -->
                                <div x-show="customerResults.length > 0" style="margin-top: 0.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; max-height: 12rem; overflow-y: auto;">
                                    <template x-for="c in customerResults" :key="c.id">
                                        <button type="button" @click="selectCustomer(c)"
                                                style="width: 100%; text-align: left; padding: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.04); cursor: pointer;"
                                                class="hover:bg-surface-elevated transition-colors">
                                            <div style="font-size: 0.875rem; color: #e3e3e3;" x-text="c.name"></div>
                                            <div style="font-size: 0.75rem; color: #818181;" x-text="c.phone"></div>
                                        </button>
                                    </template>
                                </div>

                                <!-- Cliente selecionado -->
                                <div x-show="customer.id" style="margin-top: 1rem; padding: 1rem; background: rgba(22,163,106,0.08); border: 1px solid rgba(22,163,106,0.2); border-radius: 0.75rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                        <div>
                                            <div style="font-size: 0.875rem; font-weight: 600; color: #4ade80;" x-text="customer.name"></div>
                                            <div style="font-size: 0.75rem; color: #818181; margin-top: 0.25rem;" x-text="customer.phone"></div>
                                            <div x-show="customer.address" style="font-size: 0.75rem; color: #818181; margin-top: 0.125rem;" x-text="customer.address"></div>
                                        </div>
                                        <button type="button" @click="resetCustomer()" style="color: #f87171; font-size: 0.75rem; cursor: pointer;">Trocar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Cadastro novo -->
                            <div x-show="customerMode === 'new'" style="max-width: 28rem;">
                                <div style="display: grid; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Nome *</label>
                                        <input type="text" name="customer_name" x-model="newCustomer.name"
                                               style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                               placeholder="Nome completo">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Telefone *</label>
                                        <input type="text" name="customer_phone" x-model="newCustomer.phone"
                                               style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                               placeholder="(99) 99999-9999" x-mask="(99) 99999-9999">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">CPF</label>
                                        <input type="text" name="customer_cpf" x-model="newCustomer.cpf"
                                               style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                               placeholder="000.000.000-00" x-mask="999.999.999-99">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Instagram</label>
                                        <input type="text" name="customer_instagram" x-model="newCustomer.instagram"
                                               style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                               placeholder="@usuario">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navegação -->
                        <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                            <button type="button" @click="nextStep()"
                                    :disabled="!isStep1Valid()"
                                    :style="isStep1Valid()
                                        ? 'padding: 0.75rem 2rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;'
                                        : 'padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #515151; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: not-allowed;'">
                                Próximo →
                            </button>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- PASSO 2 - PRODUTO DESEJADO -->
                    <!-- ============================================================ -->
                    <div x-show="currentStep === 2" x-transition>
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.5rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">
                                <span style="color: #60a5fa;">②</span> Produto Desejado
                            </h2>
                            <p style="font-size: 0.8125rem; color: #818181; margin-bottom: 1.25rem;">
                                Descreva o aparelho que será comprado no fornecedor
                            </p>

                            <div style="display: grid; gap: 1rem; max-width: 36rem;">
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Produto *</label>
                                    <input type="text" name="desired_product" x-model="product.name"
                                           style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                           placeholder="Ex: iPhone 15 Pro Max 256GB Titânio Natural">
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Modelo</label>
                                        <input type="text" name="desired_model" x-model="product.model"
                                               style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                               placeholder="Ex: iPhone 15 Pro Max">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Armazenamento</label>
                                        <select name="desired_storage" x-model="product.storage"
                                                style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                            <option value="">Selecione</option>
                                            <option value="64GB">64GB</option>
                                            <option value="128GB">128GB</option>
                                            <option value="256GB">256GB</option>
                                            <option value="512GB">512GB</option>
                                            <option value="1TB">1TB</option>
                                        </select>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Cor</label>
                                        <input type="text" name="desired_color" x-model="product.color"
                                               style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                               placeholder="Ex: Titânio Natural">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Condição *</label>
                                        <select name="desired_condition" x-model="product.condition"
                                                style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                            <option value="new">Novo (lacrado)</option>
                                            <option value="used">Seminovo</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Custo estimado no fornecedor</label>
                                    <div style="position: relative;">
                                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #515151; font-size: 0.875rem;">R$</span>
                                        <input type="number" name="estimated_cost" x-model="product.estimatedCost" step="0.01" min="0"
                                               style="width: 100%; padding: 0.75rem; padding-left: 2.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                               placeholder="0,00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navegação -->
                        <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                            <button type="button" @click="currentStep = 1"
                                    style="padding: 0.75rem 2rem; color: #818181; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                ← Voltar
                            </button>
                            <button type="button" @click="nextStep()"
                                    :disabled="!isStep2Valid()"
                                    :style="isStep2Valid()
                                        ? 'padding: 0.75rem 2rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;'
                                        : 'padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #515151; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: not-allowed;'">
                                Próximo →
                            </button>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- PASSO 3 - DADOS DA VENDA -->
                    <!-- ============================================================ -->
                    <div x-show="currentStep === 3" x-transition>
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.5rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">
                                <span style="color: #60a5fa;">③</span> Dados da Venda
                            </h2>

                            <!-- Tipo: Compra ou Upgrade -->
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.5rem;">Tipo da operação *</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="button" @click="sale.type = 'direct_purchase'"
                                            :style="sale.type === 'direct_purchase'
                                                ? 'flex: 1; padding: 0.75rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); cursor: pointer;'
                                                : 'flex: 1; padding: 0.75rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                        <div>Compra Direta</div>
                                        <div style="font-size: 0.6875rem; font-weight: 400; margin-top: 0.25rem;">Cliente compra aparelho novo</div>
                                    </button>
                                    <button type="button" @click="sale.type = 'upgrade'"
                                            :style="sale.type === 'upgrade'
                                                ? 'flex: 1; padding: 0.75rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(124,58,237,0.15); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); cursor: pointer;'
                                                : 'flex: 1; padding: 0.75rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                        <div>Upgrade</div>
                                        <div style="font-size: 0.6875rem; font-weight: 400; margin-top: 0.25rem;">Cliente troca aparelho + diferença</div>
                                    </button>
                                </div>
                                <input type="hidden" name="type" :value="sale.type">
                            </div>

                            <div style="display: grid; gap: 1rem; max-width: 36rem;">
                                <!-- Preço de venda -->
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Preço de venda ao cliente *</label>
                                    <div style="position: relative;">
                                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #515151; font-size: 0.875rem;">R$</span>
                                        <input type="number" name="sale_price" x-model="sale.price" step="0.01" min="0.01"
                                               style="width: 100%; padding: 0.75rem; padding-left: 2.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                               placeholder="0,00">
                                    </div>
                                </div>

                                <!-- Sinal -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Sinal (mín. R$ 100) *</label>
                                        <div style="position: relative;">
                                            <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #515151; font-size: 0.875rem;">R$</span>
                                            <input type="number" name="down_payment" x-model="sale.downPayment" step="0.01" min="100"
                                                   style="width: 100%; padding: 0.75rem; padding-left: 2.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                                   placeholder="100,00">
                                        </div>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Método do sinal *</label>
                                        <select name="down_payment_method" x-model="sale.downPaymentMethod"
                                                style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                            <option value="pix">PIX</option>
                                            <option value="cash">Dinheiro</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Pagamento do restante -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Pagamento do restante *</label>
                                        <select name="payment_method" x-model="sale.paymentMethod"
                                                style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                            <option value="pix">PIX</option>
                                            <option value="cash">Dinheiro</option>
                                            <option value="credit_card">Cartão de Crédito</option>
                                            <option value="debit_card">Cartão de Débito</option>
                                        </select>
                                    </div>
                                    <div x-show="sale.paymentMethod === 'credit_card'">
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Parcelas</label>
                                        <select name="installments" x-model="sale.installments"
                                                style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;">
                                            <template x-for="i in 21" :key="i">
                                                <option :value="i" x-text="i + 'x'"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <!-- Upgrade: Trade-in -->
                                <div x-show="sale.type === 'upgrade'" style="padding: 1rem; background: rgba(124,58,237,0.06); border: 1px solid rgba(124,58,237,0.15); border-radius: 0.75rem;">
                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #a78bfa; margin-bottom: 0.75rem;">Dados do Upgrade</div>
                                    <div style="display: grid; gap: 1rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Aparelho do cliente *</label>
                                            <input type="text" name="trade_in_device" x-model="sale.tradeInDevice"
                                                   style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                                   placeholder="Ex: iPhone 13 128GB Meia-noite">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Valor de avaliação *</label>
                                            <div style="position: relative;">
                                                <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #515151; font-size: 0.875rem;">R$</span>
                                                <input type="number" name="trade_in_value" x-model="sale.tradeInValue" step="0.01" min="0"
                                                       style="width: 100%; padding: 0.75rem; padding-left: 2.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                                       placeholder="0,00">
                                            </div>
                                        </div>
                                        <!-- Diferença calculada -->
                                        <div x-show="sale.tradeInValue > 0 && sale.price > 0"
                                             style="padding: 0.75rem; background: rgba(124,58,237,0.1); border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-size: 0.8125rem; color: #a78bfa;">Diferença para o cliente:</span>
                                            <span style="font-size: 1rem; font-weight: 700; color: #c4b5fd;"
                                                  x-text="'R$ ' + formatMoney(upgradeDifference)"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Entrega -->
                                <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1rem;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.5rem;">Entrega *</label>
                                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                                        <button type="button" @click="sale.deliveryType = 'pickup'"
                                                :style="sale.deliveryType === 'pickup'
                                                    ? 'flex: 1; padding: 0.625rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); cursor: pointer;'
                                                    : 'flex: 1; padding: 0.625rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                            Retira na Loja
                                        </button>
                                        <button type="button" @click="sale.deliveryType = 'delivery'"
                                                :style="sale.deliveryType === 'delivery'
                                                    ? 'flex: 1; padding: 0.625rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3); cursor: pointer;'
                                                    : 'flex: 1; padding: 0.625rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; background: rgba(255,255,255,0.03); color: #818181; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'">
                                            Entrega
                                        </button>
                                    </div>
                                    <input type="hidden" name="delivery_type" :value="sale.deliveryType">

                                    <!-- Campos de entrega -->
                                    <div x-show="sale.deliveryType === 'delivery'" style="display: grid; gap: 1rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Endereço de entrega *</label>
                                            <input type="text" name="delivery_address" x-model="sale.deliveryAddress"
                                                   style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                                   placeholder="Rua, número, bairro, cidade">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Horário preferido</label>
                                            <input type="text" name="delivery_time" x-model="sale.deliveryTime"
                                                   style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;"
                                                   placeholder="Ex: Entre 14h e 17h">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Observações da entrega</label>
                                            <textarea name="delivery_notes" x-model="sale.deliveryNotes" rows="2"
                                                      style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem; resize: none;"
                                                      placeholder="Referências, portaria, etc."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observações gerais -->
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Observações</label>
                                    <textarea name="notes" x-model="sale.notes" rows="2"
                                              style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem; resize: none;"
                                              placeholder="Informações adicionais sobre a venda..."></textarea>
                                </div>

                                <!-- Resumo de valores -->
                                <div style="padding: 1rem; background: rgba(22,163,106,0.06); border: 1px solid rgba(22,163,106,0.15); border-radius: 0.75rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.8125rem; color: #818181;">Preço de venda:</span>
                                        <span style="font-size: 0.875rem; color: #e3e3e3;" x-text="'R$ ' + formatMoney(sale.price || 0)"></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.8125rem; color: #818181;">Sinal:</span>
                                        <span style="font-size: 0.875rem; color: #60a5fa;" x-text="'- R$ ' + formatMoney(sale.downPayment || 0)"></span>
                                    </div>
                                    <div x-show="sale.type === 'upgrade' && sale.tradeInValue > 0" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.8125rem; color: #818181;">Trade-in:</span>
                                        <span style="font-size: 0.875rem; color: #a78bfa;" x-text="'- R$ ' + formatMoney(sale.tradeInValue || 0)"></span>
                                    </div>
                                    <div style="border-top: 1px solid rgba(22,163,106,0.2); padding-top: 0.5rem; display: flex; justify-content: space-between;">
                                        <span style="font-size: 0.875rem; font-weight: 600; color: #a4a4a4;">Saldo restante:</span>
                                        <span style="font-size: 1.125rem; font-weight: 700; color: #4ade80;" x-text="'R$ ' + formatMoney(remainingBalance)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navegação -->
                        <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                            <button type="button" @click="currentStep = 2"
                                    style="padding: 0.75rem 2rem; color: #818181; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                ← Voltar
                            </button>
                            <button type="button" @click="nextStep()"
                                    :disabled="!isStep3Valid()"
                                    :style="isStep3Valid()
                                        ? 'padding: 0.75rem 2rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;'
                                        : 'padding: 0.75rem 2rem; background: rgba(255,255,255,0.03); color: #515151; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: not-allowed;'">
                                Próximo →
                            </button>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- PASSO 4 - CONFIRMAÇÃO -->
                    <!-- ============================================================ -->
                    <div x-show="currentStep === 4" x-transition>
                        <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.5rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">
                                <span style="color: #60a5fa;">④</span> Confirmação
                            </h2>
                            <p style="font-size: 0.8125rem; color: #818181; margin-bottom: 1.25rem;">
                                Revise os dados antes de registrar. Após confirmar, a solicitação será enviada para aprovação do admin.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Cliente -->
                                <div style="padding: 1rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Cliente</div>
                                    <div style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3;" x-text="customerName"></div>
                                    <div style="font-size: 0.8125rem; color: #818181; margin-top: 0.25rem;" x-text="customerPhone"></div>
                                </div>

                                <!-- Produto -->
                                <div style="padding: 1rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Produto Desejado</div>
                                    <div style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3;" x-text="product.name"></div>
                                    <div style="font-size: 0.8125rem; color: #818181; margin-top: 0.25rem;">
                                        <span x-show="product.storage" x-text="product.storage + ' · '"></span>
                                        <span x-show="product.color" x-text="product.color + ' · '"></span>
                                        <span x-text="product.condition === 'new' ? 'Novo' : 'Seminovo'"></span>
                                    </div>
                                    <div x-show="product.estimatedCost > 0" style="font-size: 0.8125rem; color: #818181; margin-top: 0.25rem;">
                                        Custo estimado: <span style="color: #fbbf24;" x-text="'R$ ' + formatMoney(product.estimatedCost)"></span>
                                    </div>
                                </div>

                                <!-- Venda -->
                                <div style="padding: 1rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Dados da Venda</div>
                                    <div style="display: grid; gap: 0.5rem;">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="font-size: 0.8125rem; color: #818181;">Tipo:</span>
                                            <span style="font-size: 0.8125rem; font-weight: 600;" :style="sale.type === 'upgrade' ? 'color: #a78bfa;' : 'color: #60a5fa;'"
                                                  x-text="sale.type === 'upgrade' ? 'Upgrade' : 'Compra Direta'"></span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="font-size: 0.8125rem; color: #818181;">Preço de venda:</span>
                                            <span style="font-size: 0.8125rem; color: #e3e3e3;" x-text="'R$ ' + formatMoney(sale.price)"></span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="font-size: 0.8125rem; color: #818181;">Sinal:</span>
                                            <span style="font-size: 0.8125rem; color: #60a5fa;" x-text="'R$ ' + formatMoney(sale.downPayment) + ' (' + downPaymentMethodLabel + ')'"></span>
                                        </div>
                                        <div x-show="sale.type === 'upgrade'" style="display: flex; justify-content: space-between;">
                                            <span style="font-size: 0.8125rem; color: #818181;">Trade-in:</span>
                                            <span style="font-size: 0.8125rem; color: #a78bfa;" x-text="'R$ ' + formatMoney(sale.tradeInValue)"></span>
                                        </div>
                                        <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.5rem; display: flex; justify-content: space-between;">
                                            <span style="font-size: 0.875rem; font-weight: 600; color: #a4a4a4;">Saldo restante:</span>
                                            <span style="font-size: 0.875rem; font-weight: 700; color: #4ade80;" x-text="'R$ ' + formatMoney(remainingBalance)"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Entrega / Upgrade -->
                                <div style="padding: 1rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">
                                        <span x-text="sale.type === 'upgrade' ? 'Upgrade & Entrega' : 'Entrega'"></span>
                                    </div>
                                    <div x-show="sale.type === 'upgrade'" style="margin-bottom: 0.75rem;">
                                        <div style="font-size: 0.8125rem; color: #818181;">Aparelho:</div>
                                        <div style="font-size: 0.875rem; color: #a78bfa;" x-text="sale.tradeInDevice || '-'"></div>
                                        <div style="font-size: 0.8125rem; color: #818181; margin-top: 0.375rem;">Diferença:</div>
                                        <div style="font-size: 0.875rem; font-weight: 600; color: #c4b5fd;" x-text="'R$ ' + formatMoney(upgradeDifference)"></div>
                                    </div>
                                    <div style="font-size: 0.8125rem; color: #818181;">Tipo:</div>
                                    <div style="font-size: 0.875rem; color: #e3e3e3;" x-text="sale.deliveryType === 'delivery' ? 'Entrega no endereço' : 'Retira na loja'"></div>
                                    <div x-show="sale.deliveryType === 'delivery'" style="margin-top: 0.5rem;">
                                        <div style="font-size: 0.8125rem; color: #818181;">Endereço:</div>
                                        <div style="font-size: 0.875rem; color: #e3e3e3;" x-text="sale.deliveryAddress || '-'"></div>
                                        <div x-show="sale.deliveryTime" style="margin-top: 0.375rem;">
                                            <div style="font-size: 0.8125rem; color: #818181;">Horário:</div>
                                            <div style="font-size: 0.875rem; color: #e3e3e3;" x-text="sale.deliveryTime"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Observações -->
                            <div x-show="sale.notes" style="margin-top: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem;">
                                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.375rem;">Observações</div>
                                <div style="font-size: 0.8125rem; color: #a4a4a4; white-space: pre-line;" x-text="sale.notes"></div>
                            </div>
                        </div>

                        <!-- Navegação -->
                        <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                            <button type="button" @click="currentStep = 3"
                                    style="padding: 0.75rem 2rem; color: #818181; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                ← Voltar
                            </button>
                            <button type="submit" :disabled="submitting"
                                    style="padding: 0.75rem 2rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="submitting ? 'Registrando...' : 'Registrar Solicitação'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function purchaseRequestForm() {
            return {
                currentStep: 1,
                submitting: false,

                // Cliente
                customerMode: 'existing',
                customerSearch: '',
                customerResults: [],
                customer: { id: '', name: '', phone: '', address: '' },
                newCustomer: { name: '', phone: '', cpf: '', instagram: '' },

                // Produto
                product: {
                    name: '',
                    model: '',
                    storage: '',
                    color: '',
                    condition: 'new',
                    estimatedCost: '',
                },

                // Venda
                sale: {
                    type: 'direct_purchase',
                    price: '',
                    downPayment: '',
                    downPaymentMethod: 'pix',
                    paymentMethod: 'pix',
                    installments: 1,
                    tradeInDevice: '',
                    tradeInValue: '',
                    deliveryType: 'pickup',
                    deliveryAddress: '',
                    deliveryTime: '',
                    deliveryNotes: '',
                    notes: '',
                },

                get customerName() {
                    if (this.customerMode === 'existing') return this.customer.name;
                    return this.newCustomer.name;
                },

                get customerPhone() {
                    if (this.customerMode === 'existing') return this.customer.phone;
                    return this.newCustomer.phone;
                },

                get upgradeDifference() {
                    if (this.sale.type !== 'upgrade') return 0;
                    const diff = parseFloat(this.sale.price || 0) - parseFloat(this.sale.tradeInValue || 0);
                    return Math.max(0, diff);
                },

                get remainingBalance() {
                    let balance = parseFloat(this.sale.price || 0) - parseFloat(this.sale.downPayment || 0);
                    if (this.sale.type === 'upgrade' && this.sale.tradeInValue) {
                        balance -= parseFloat(this.sale.tradeInValue || 0);
                    }
                    return Math.max(0, balance);
                },

                get downPaymentMethodLabel() {
                    return this.sale.downPaymentMethod === 'pix' ? 'PIX' : 'Dinheiro';
                },

                formatMoney(value) {
                    return parseFloat(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                isStep1Valid() {
                    if (this.customerMode === 'existing') return !!this.customer.id;
                    return this.newCustomer.name.trim().length >= 2 && this.newCustomer.phone.replace(/\D/g, '').length >= 10;
                },

                isStep2Valid() {
                    return this.product.name.trim().length >= 3;
                },

                isStep3Valid() {
                    if (!this.sale.price || parseFloat(this.sale.price) <= 0) return false;
                    if (!this.sale.downPayment || parseFloat(this.sale.downPayment) < 100) return false;
                    if (parseFloat(this.sale.downPayment) > parseFloat(this.sale.price)) return false;
                    if (this.sale.type === 'upgrade') {
                        if (!this.sale.tradeInDevice || this.sale.tradeInDevice.trim().length < 2) return false;
                        if (!this.sale.tradeInValue || parseFloat(this.sale.tradeInValue) <= 0) return false;
                    }
                    if (this.sale.deliveryType === 'delivery' && (!this.sale.deliveryAddress || this.sale.deliveryAddress.trim().length < 5)) return false;
                    return true;
                },

                canGoToStep(step) {
                    if (step <= this.currentStep) return true;
                    if (step === 2) return this.isStep1Valid();
                    if (step === 3) return this.isStep1Valid() && this.isStep2Valid();
                    if (step === 4) return this.isStep1Valid() && this.isStep2Valid() && this.isStep3Valid();
                    return false;
                },

                nextStep() {
                    if (this.currentStep < 4) this.currentStep++;
                },

                resetCustomer() {
                    this.customer = { id: '', name: '', phone: '', address: '' };
                    this.customerSearch = '';
                    this.customerResults = [];
                    this.newCustomer = { name: '', phone: '', cpf: '', instagram: '' };
                },

                selectCustomer(c) {
                    this.customer = {
                        id: c.id,
                        name: c.name,
                        phone: c.phone,
                        address: c.address || '',
                    };
                    this.customerResults = [];
                    this.customerSearch = '';

                    if (c.address && this.sale.deliveryType === 'delivery' && !this.sale.deliveryAddress) {
                        this.sale.deliveryAddress = c.address;
                    }
                },

                async searchCustomers() {
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }
                    try {
                        const res = await fetch(`{{ route('purchase-requests.search-customers') }}?q=${encodeURIComponent(this.customerSearch)}`);
                        this.customerResults = await res.json();
                    } catch (e) {
                        this.customerResults = [];
                    }
                },

                handleSubmit(event) {
                    if (this.submitting) {
                        event.preventDefault();
                        return;
                    }
                    this.submitting = true;
                },
            }
        }
    </script>
</x-app-layout>
