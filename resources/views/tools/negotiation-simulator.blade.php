<x-app-layout>
    <x-slot name="title">Simulador de Negociação</x-slot>
    <div class="py-6" x-data="negotiationSimulator()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Simulador de Negociação</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Monte propostas completas com trade-in e parcelamento</p>
                </div>
                <button @click="clearAll()" type="button"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 500; color: #6b7280; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;"
                        onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444';"
                        onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#6b7280';">
                    Limpar tudo
                </button>
            </div>

            <!-- Layout 2 colunas -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">

                <!-- COLUNA ESQUERDA: Inputs -->
                <div>
                    <!-- 1. Produto Desejado -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                            1. Produto Desejado
                        </label>

                        <!-- Quick Values -->
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.375rem;">
                                <span style="font-size: 11px; color: #9ca3af; font-weight: 500;">VALORES RÁPIDOS</span>
                                <a href="{{ route('marketing.index', ['tab' => 'prices']) }}"
                                   style="font-size: 11px; color: #9ca3af; text-decoration: none; display: flex; align-items: center; gap: 3px;"
                                   onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#9ca3af'">
                                    <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                <template x-for="(qv, idx) in quickValues" :key="idx">
                                    <div style="display: inline-flex;">
                                        <template x-if="!qv.variants || qv.variants.length === 0">
                                            <button @click="selectQuickValue(qv.name, qv.value)" type="button"
                                                    :style="isQuickValueActive(qv.name, qv.value)
                                                        ? 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: 2px solid #111827; background: #111827; color: white; white-space: nowrap;'
                                                        : 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: 2px solid #e5e7eb; background: white; color: #374151; white-space: nowrap;'">
                                                <span x-text="qv.name"></span>
                                                <span style="opacity: 0.6; margin-left: 4px;" x-text="'R$ ' + fmt(qv.value)"></span>
                                            </button>
                                        </template>
                                        <template x-if="qv.variants && qv.variants.length > 0">
                                            <div :style="isQuickValueActiveByName(qv.name)
                                                    ? 'display: inline-flex; align-items: center; gap: 0; border-radius: 8px; border: 2px solid #111827; overflow: hidden;'
                                                    : 'display: inline-flex; align-items: center; gap: 0; border-radius: 8px; border: 2px solid #e5e7eb; overflow: hidden;'">
                                                <span style="padding: 6px 8px 6px 12px; font-size: 12px; font-weight: 600; color: #374151; white-space: nowrap;" x-text="qv.name"></span>
                                                <template x-for="(v, vi) in qv.variants" :key="vi">
                                                    <button @click="selectQuickValue(qv.name + ' ' + v.label, v.value)" type="button"
                                                            :style="isQuickValueActive(qv.name + ' ' + v.label, v.value)
                                                                ? 'padding: 4px 8px; cursor: pointer; border: none; background: #111827; display: flex; align-items: center; gap: 4px;'
                                                                : 'padding: 4px 8px; cursor: pointer; border: none; background: transparent; display: flex; align-items: center; gap: 4px;'"
                                                            :title="v.label + ' - R$ ' + fmt(v.value)">
                                                        <span :style="'width: 14px; height: 14px; border-radius: 50%; border: 2px solid ' + (isQuickValueActive(qv.name + ' ' + v.label, v.value) ? 'white' : '#d1d5db') + '; background: ' + v.color + ';'"></span>
                                                        <span :style="'font-size: 11px; font-weight: 600;' + (isQuickValueActive(qv.name + ' ' + v.label, v.value) ? ' color: white;' : ' color: #6b7280;')"
                                                              x-text="fmt(v.value)"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div style="margin-bottom: 0.75rem;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Descrição</label>
                            <input type="text" x-model="product.description" placeholder="Ex: iPhone 16 Pro Max 256GB"
                                   style="width: 100%; padding: 10px 14px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; color: #111827; outline: none;"
                                   onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                                   onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Preço de Venda *</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px;">R$</span>
                                <input type="text" x-model="product.priceInput"
                                       @input.debounce.300ms="recalculate()"
                                       placeholder="0,00"
                                       style="width: 100%; padding: 14px 14px 14px 40px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 22px; font-weight: 700; color: #111827; outline: none; text-align: right;"
                                       onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                                       onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                            </div>
                        </div>

                        <div style="margin-top: 0.75rem;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Custo de Aquisição (interno)</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px;">R$</span>
                                <input type="text" x-model="product.costInput"
                                       @input.debounce.300ms="recalculate()"
                                       placeholder="0,00"
                                       style="width: 100%; padding: 10px 14px 10px 40px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 16px; font-weight: 600; color: #6b7280; outline: none; text-align: right;"
                                       onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                                       onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Trade-in -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <label style="font-size: 0.75rem; font-weight: 600; color: #d97706; text-transform: uppercase; letter-spacing: 0.05em;">
                                2. Trade-in (Aparelho do Cliente)
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                <span style="font-size: 12px; color: #6b7280;" x-text="tradeIn.enabled ? 'Ativo' : 'Inativo'"></span>
                                <div @click="tradeIn.enabled = !tradeIn.enabled; if(!tradeIn.enabled) clearTradeIn();"
                                     :style="tradeIn.enabled
                                         ? 'width: 40px; height: 22px; background: #d97706; border-radius: 11px; position: relative; cursor: pointer; transition: background 0.2s;'
                                         : 'width: 40px; height: 22px; background: #d1d5db; border-radius: 11px; position: relative; cursor: pointer; transition: background 0.2s;'">
                                    <div :style="tradeIn.enabled
                                             ? 'width: 18px; height: 18px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 20px; transition: left 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);'
                                             : 'width: 18px; height: 18px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: left 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);'"></div>
                                </div>
                            </label>
                        </div>

                        <div x-show="tradeIn.enabled" x-transition>
                            <!-- Modelo -->
                            <div style="margin-bottom: 0.75rem;" x-data="{ open: false, search: '' }">
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Modelo</label>
                                <div style="position: relative;">
                                    <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                           :placeholder="tradeIn.model || 'Buscar modelo...'"
                                           style="width: 100%; padding: 10px 14px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; font-size: 14px; color: #92400e; outline: none;"
                                           onfocus="this.style.borderColor='#d97706'"
                                           onblur="this.style.borderColor='#fde68a'">
                                    <div x-show="open" x-transition
                                         style="position: absolute; z-index: 20; margin-top: 4px; width: 100%; background: white; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-height: 240px; overflow-y: auto; border: 1px solid #e5e7eb;">
                                        <template x-for="(storages, name) in filteredTradeInModels(search)" :key="name">
                                            <button type="button" @click="selectTradeInModel(name); open = false; search = ''"
                                                    style="width: 100%; padding: 10px 14px; text-align: left; font-size: 13px; border: none; background: none; cursor: pointer;"
                                                    :style="tradeIn.model === name ? 'color: #d97706; font-weight: 600;' : 'color: #374151;'"
                                                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='none'"
                                                    x-text="name"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Storage -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Armazenamento</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                    <template x-for="s in tradeInStorages" :key="s">
                                        <button type="button" @click="tradeIn.storage = s; debouncedEvaluate()"
                                                :style="tradeIn.storage === s
                                                    ? 'padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: 2px solid #d97706; background: #d97706; color: white;'
                                                    : 'padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: 2px solid #e5e7eb; background: white; color: #374151;'"
                                                x-text="s"></button>
                                    </template>
                                </div>
                            </div>

                            <!-- Bateria -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">
                                    Bateria: <span x-text="tradeIn.battery + '%'" style="color: #111827;"></span>
                                    <span style="margin-left: 6px; font-size: 11px; padding: 2px 8px; border-radius: 9999px;"
                                          :style="tradeIn.battery >= 90 ? 'background: #dcfce7; color: #166534;' : (tradeIn.battery >= 80 ? 'background: #fef9c3; color: #854d0e;' : (tradeIn.battery >= 70 ? 'background: #ffedd5; color: #9a3412;' : 'background: #fee2e2; color: #991b1b;'))"
                                          x-text="tradeIn.battery >= 90 ? 'Excelente' : (tradeIn.battery >= 80 ? 'Bom' : (tradeIn.battery >= 70 ? 'Regular' : 'Ruim'))"></span>
                                </label>
                                <input type="range" x-model.number="tradeIn.battery" min="0" max="100" step="1"
                                       @input.debounce.500ms="debouncedEvaluate()"
                                       style="width: 100%; height: 8px; border-radius: 4px; appearance: none; cursor: pointer; accent-color: #d97706; background: #fef3c7;">
                                <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; margin-top: 2px;">
                                    <span>0%</span><span>100%</span>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Estado</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="button" @click="tradeIn.deviceState = 'original'; debouncedEvaluate()"
                                            :style="tradeIn.deviceState === 'original'
                                                ? 'flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600; border: 2px solid #d97706; background: #d97706; color: white; cursor: pointer;'
                                                : 'flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 500; border: 2px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer;'">
                                        Original
                                    </button>
                                    <button type="button" @click="tradeIn.deviceState = 'repaired'; debouncedEvaluate()"
                                            :style="tradeIn.deviceState === 'repaired'
                                                ? 'flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600; border: 2px solid #d97706; background: #d97706; color: white; cursor: pointer;'
                                                : 'flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 500; border: 2px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer;'">
                                        Reparado
                                    </button>
                                </div>
                            </div>

                            <!-- Acessórios -->
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Acessórios</label>
                                <div style="display: flex; gap: 0.75rem;">
                                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; color: #374151;">
                                        <input type="checkbox" x-model="tradeIn.noBox" @change="debouncedEvaluate()" style="accent-color: #d97706; width: 16px; height: 16px;">
                                        Sem caixa
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; color: #374151;">
                                        <input type="checkbox" x-model="tradeIn.noCable" @change="debouncedEvaluate()" style="accent-color: #d97706; width: 16px; height: 16px;">
                                        Sem cabo
                                    </label>
                                </div>
                            </div>

                            <!-- Resultado da avaliação -->
                            <div x-show="tradeIn.loading" style="text-align: center; padding: 1rem; color: #9ca3af; font-size: 13px;">
                                Avaliando...
                            </div>

                            <div x-show="tradeIn.result && !tradeIn.loading" x-transition
                                 style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; padding: 1rem; margin-top: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <div>
                                        <div style="font-size: 11px; color: #92400e; font-weight: 600; text-transform: uppercase;">Sugestão de Compra</div>
                                        <div style="font-size: 22px; font-weight: 800; color: #92400e;" x-text="'R$ ' + fmt(tradeIn.result?.suggested_price)"></div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 11px; color: #92400e;">Revenda: <span x-text="'R$ ' + fmt(tradeIn.result?.resale_price)" style="font-weight: 700;"></span></div>
                                        <div style="font-size: 11px; color: #92400e;">
                                            Mercado: <span x-text="'R$ ' + fmt(tradeIn.result?.market_average)" style="font-weight: 600;"></span>
                                            · <span x-text="tradeIn.result?.listings_count + ' anúncios'"></span>
                                        </div>
                                        <div style="font-size: 11px; margin-top: 2px;">
                                            <span style="padding: 1px 6px; border-radius: 9999px; font-weight: 600;"
                                                  :style="tradeIn.result?.confidence === 'high' ? 'background: #dcfce7; color: #166534;' : (tradeIn.result?.confidence === 'medium' ? 'background: #fef9c3; color: #854d0e;' : 'background: #fee2e2; color: #991b1b;')"
                                                  x-text="tradeIn.result?.confidence === 'high' ? 'Alta' : (tradeIn.result?.confidence === 'medium' ? 'Média' : 'Baixa')"></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 600; color: #92400e; margin-bottom: 0.25rem;">Valor oferecido ao cliente</label>
                                    <div style="position: relative;">
                                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #b45309; font-size: 14px;">R$</span>
                                        <input type="text" x-model="tradeIn.offeredInput"
                                               @input.debounce.300ms="recalculate()"
                                               style="width: 100%; padding: 10px 14px 10px 36px; background: white; border: 2px solid #d97706; border-radius: 10px; font-size: 18px; font-weight: 700; color: #92400e; outline: none; text-align: right;">
                                    </div>
                                </div>
                            </div>

                            <div x-show="tradeIn.error && !tradeIn.loading" x-transition
                                 style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 0.75rem; margin-top: 0.5rem; font-size: 13px; color: #991b1b;">
                                <span x-text="tradeIn.error"></span>
                            </div>

                            <!-- Valor manual (sem avaliação) -->
                            <div x-show="!tradeIn.result && !tradeIn.loading && !tradeIn.error" style="margin-top: 0.5rem;">
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #92400e; margin-bottom: 0.25rem;">Valor do trade-in (manual)</label>
                                <div style="position: relative;">
                                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #b45309; font-size: 14px;">R$</span>
                                    <input type="text" x-model="tradeIn.offeredInput"
                                           @input.debounce.300ms="recalculate()"
                                           placeholder="0,00"
                                           style="width: 100%; padding: 10px 14px 10px 36px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; font-size: 18px; font-weight: 700; color: #92400e; outline: none; text-align: right;"
                                           onfocus="this.style.borderColor='#d97706'"
                                           onblur="this.style.borderColor='#fde68a'">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Pagamento -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #059669; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                            3. Pagamento
                        </label>

                        <div style="margin-bottom: 0.75rem;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #059669; margin-bottom: 0.25rem;">Entrada (Pix/Dinheiro)</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #6ee7b7; font-size: 14px;">R$</span>
                                <input type="text" x-model="downPaymentInput"
                                       @input.debounce.300ms="recalculate()"
                                       placeholder="0,00"
                                       style="width: 100%; padding: 10px 14px 10px 40px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; font-size: 18px; font-weight: 600; color: #059669; outline: none; text-align: right;"
                                       onfocus="this.style.borderColor='#059669'; this.style.background='white'"
                                       onblur="this.style.borderColor='#bbf7d0'; this.style.background='#f0fdf4'">
                            </div>
                        </div>

                        <!-- Saldo no cartão -->
                        <div x-show="cardBalance > 0" style="padding: 0.75rem; background: #f9fafb; border-radius: 8px; display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: #6b7280;">Saldo no cartão:</span>
                            <span style="font-weight: 700; color: #111827;" x-text="'R$ ' + fmt(cardBalance)"></span>
                        </div>
                    </div>

                    <!-- Preview da mensagem -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Preview</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <button @click="copyMessage()" type="button"
                                        :style="copied
                                            ? 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: #059669; color: white; display: flex; align-items: center; gap: 4px;'
                                            : 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: #111827; color: white; display: flex; align-items: center; gap: 4px;'">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                                </button>
                                <a :href="'https://wa.me/?text=' + encodeURIComponent(buildMessage())"
                                   target="_blank"
                                   style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: #16a34a; color: white; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                                    <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; font-size: 13px; color: #374151; white-space: pre-wrap; font-family: monospace; line-height: 1.6; min-height: 80px; max-height: 250px; overflow-y: auto;"
                             x-text="productPrice > 0 ? buildMessage() : 'Preencha o valor do produto para visualizar...'"></div>
                    </div>
                </div>

                <!-- COLUNA DIREITA: Resumo + Parcelas -->
                <div>
                    <!-- Resumo da Negociação -->
                    <div x-show="productPrice > 0" x-transition style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Resumo da Negociação</div>

                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                            <span style="font-size: 14px; color: #374151;" x-text="product.description || 'Produto'"></span>
                            <span style="font-size: 14px; font-weight: 700; color: #111827;" x-text="'R$ ' + fmt(productPrice)"></span>
                        </div>

                        <template x-if="tradeInValue > 0">
                            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                                <span style="font-size: 14px; color: #d97706;" x-text="'Trade-in: ' + (tradeIn.model ? tradeIn.model + ' ' + (tradeIn.storage || '') : 'Seminovo')"></span>
                                <span style="font-size: 14px; font-weight: 700; color: #d97706;" x-text="'- R$ ' + fmt(tradeInValue)"></span>
                            </div>
                        </template>

                        <template x-if="downPayment > 0">
                            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                                <span style="font-size: 14px; color: #059669;">Entrada (Pix)</span>
                                <span style="font-size: 14px; font-weight: 700; color: #059669;" x-text="'- R$ ' + fmt(downPayment)"></span>
                            </div>
                        </template>

                        <div style="display: flex; justify-content: space-between; padding: 12px 0; margin-top: 4px;">
                            <span style="font-size: 16px; font-weight: 700; color: #111827;">Saldo Restante</span>
                            <span style="font-size: 20px; font-weight: 800; color: #111827;" x-text="'R$ ' + fmt(cardBalance)"></span>
                        </div>
                    </div>

                    <!-- Visão do Vendedor (margem) -->
                    <div x-show="productPrice > 0 && productCost > 0" x-transition
                         style="background: #f9fafb; border-radius: 0.75rem; border: 1px dashed #d1d5db; padding: 1rem 1.25rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 0.75rem;">
                            <svg style="width: 14px; height: 14px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Visão do Vendedor</span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <div style="font-size: 11px; color: #9ca3af;">Receita total</div>
                                <div style="font-size: 16px; font-weight: 700; color: #111827;" x-text="'R$ ' + fmt(revenueTotal)"></div>
                            </div>
                            <div>
                                <div style="font-size: 11px; color: #9ca3af;">Custo total</div>
                                <div style="font-size: 16px; font-weight: 700; color: #111827;" x-text="'R$ ' + fmt(costTotal)"></div>
                            </div>
                            <div>
                                <div style="font-size: 11px; color: #9ca3af;">Taxa Stone</div>
                                <div style="font-size: 16px; font-weight: 700; color: #ef4444;" x-text="'- R$ ' + fmt(stoneFee)"></div>
                            </div>
                            <div>
                                <div style="font-size: 11px; color: #9ca3af;">Margem líquida</div>
                                <div style="font-size: 16px; font-weight: 800;"
                                     :style="netMargin >= 0 ? 'color: #059669;' : 'color: #ef4444;'"
                                     x-text="'R$ ' + fmt(netMargin)"></div>
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 13px; font-weight: 600; color: #6b7280;">Margem %</span>
                            <span style="font-size: 18px; font-weight: 800; padding: 2px 10px; border-radius: 8px;"
                                  :style="marginPercent >= 15 ? 'background: #dcfce7; color: #166534;' : (marginPercent >= 10 ? 'background: #fef9c3; color: #854d0e;' : (marginPercent >= 0 ? 'background: #ffedd5; color: #9a3412;' : 'background: #fee2e2; color: #991b1b;'))"
                                  x-text="marginPercent.toFixed(1) + '%'"></span>
                        </div>
                    </div>

                    <!-- Pix destaque -->
                    <div x-show="cardBalance > 0" x-transition
                         style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.75rem; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div>
                            <div style="font-size: 15px; font-weight: 700; color: #059669;">Pix / Dinheiro</div>
                            <div style="font-size: 12px; color: #6b7280;">Melhor preço - sem taxa</div>
                        </div>
                        <div style="font-size: 22px; font-weight: 800; color: #059669;" x-text="'R$ ' + fmt(cardBalance)"></div>
                    </div>

                    <!-- Loading -->
                    <div x-show="cardLoading" style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 3rem; text-align: center;">
                        <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top-color: #111827; border-radius: 50%; animation: ngSpin 1s linear infinite;"></div>
                    </div>

                    <!-- Tabela de parcelas -->
                    <div x-show="!cardLoading && cardResults.length > 0" x-transition>
                        <!-- Presets -->
                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 0.75rem;">
                            <template x-for="p in presets" :key="p.key">
                                <button @click="setSelection(p.key)" type="button"
                                        :style="activePreset === p.key
                                            ? 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: 2px solid #111827; background: #111827; color: white;'
                                            : 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: 2px solid #e5e7eb; background: white; color: #374151;'"
                                        x-text="p.label"></button>
                            </template>
                        </div>

                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                            <div style="display: grid; grid-template-columns: 32px 1fr auto; background: #111827; color: white; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                <div style="padding: 10px 0 10px 10px; display: flex; align-items: center;">
                                    <input type="checkbox" :checked="cardResults.every(r => r.selected)" @change="cardResults.forEach(r => r.selected = $event.target.checked)"
                                           style="width: 15px; height: 15px; accent-color: white; cursor: pointer;">
                                </div>
                                <div style="padding: 10px 14px;">Forma</div>
                                <div style="padding: 10px 14px; text-align: right;">Cliente Paga</div>
                            </div>
                            <template x-for="(row, idx) in cardResults" :key="idx">
                                <div :style="'display: grid; grid-template-columns: 32px 1fr auto; align-items: center; border-top: 1px solid #f3f4f6; transition: opacity 0.15s;' + (row.selected ? (idx % 2 === 0 ? ' background: white;' : ' background: #f9fafb;') : ' background: #f3f4f6; opacity: 0.4;')">
                                    <div style="padding: 10px 0 10px 10px; display: flex; align-items: center;">
                                        <input type="checkbox" x-model="row.selected" @change="activePreset = null" style="width: 15px; height: 15px; accent-color: #111827; cursor: pointer;">
                                    </div>
                                    <div style="padding: 10px 14px;">
                                        <div style="font-size: 14px; font-weight: 600; color: #111827;" x-text="row.label"></div>
                                        <div x-show="row.installments > 1" style="font-size: 12px; color: #6b7280;" x-text="row.installments + 'x de R$ ' + fmt(row.installment_value)"></div>
                                        <div style="font-size: 11px; color: #9ca3af;" x-text="'Taxa ' + row.mdr_rate.toString().replace('.', ',') + '%'"></div>
                                    </div>
                                    <div style="padding: 10px 14px; text-align: right;">
                                        <div style="font-size: 16px; font-weight: 700; color: #111827;" x-text="'R$ ' + fmt(row.gross_amount)"></div>
                                        <div style="font-size: 11px; color: #ef4444;" x-text="'taxa R$ ' + fmt(row.fee_amount)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Ações finais -->
                        <div style="display: flex; gap: 10px; margin-top: 0.75rem;">
                            <button @click="copyMessage()" type="button"
                                    :style="copied
                                        ? 'flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #059669; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'
                                        : 'flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #111827; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="copied ? 'Copiado!' : 'Copiar proposta'"></span>
                            </button>
                            <a :href="'https://wa.me/?text=' + encodeURIComponent(buildMessage())"
                               target="_blank"
                               style="padding: 14px 24px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #16a34a; color: white; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;"
                               onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                                <svg style="width: 18px; height: 18px;" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                WhatsApp
                            </a>
                        </div>
                    </div>

                    <!-- Estado vazio -->
                    <div x-show="!cardLoading && cardResults.length === 0 && productPrice <= 0" style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 3rem; text-align: center;">
                        <svg style="width: 48px; height: 48px; color: #d1d5db; margin: 0 auto 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <p style="font-size: 14px; color: #9ca3af;">Preencha o valor do produto para montar a proposta</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div x-show="toast" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             x-cloak
             style="position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: #111827; color: white; padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,0.3); z-index: 50; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 18px; height: 18px; color: #34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="toast"></span>
        </div>
    </div>

    <style>
        @keyframes ngSpin { to { transform: rotate(360deg); } }
        @media (max-width: 768px) {
            [style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
        }
    </style>

    @push('scripts')
    <script>
    function negotiationSimulator() {
        return {
            quickValues: @json($quickValuesFromMarketing ?? []),
            tradeInModels: @json($tradeInModels ?? []),
            toast: '',
            copied: false,
            cardLoading: false,
            activePreset: 'all',

            product: { description: '', priceInput: '', costInput: '' },
            tradeIn: {
                enabled: false, model: '', storage: '', battery: 100,
                deviceState: 'original', noBox: false, noCable: false,
                result: null, error: null, loading: false, offeredInput: '',
            },
            downPaymentInput: '',
            cardResults: [],

            presets: [
                { key: 'all', label: 'Todas' },
                { key: 'up_to_12', label: 'Até 12x' },
                { key: 'above_6', label: 'Acima de 6x' },
                { key: 'above_10', label: 'Acima de 10x' },
            ],

            _evalTimer: null,

            init() {},

            // --- Computed ---
            get productPrice() { return this.parseNum(this.product.priceInput); },
            get productCost() { return this.parseNum(this.product.costInput); },
            get tradeInValue() {
                if (!this.tradeIn.enabled) return 0;
                return this.parseNum(this.tradeIn.offeredInput);
            },
            get downPayment() { return this.parseNum(this.downPaymentInput); },
            get cardBalance() {
                return Math.max(0, this.productPrice - this.tradeInValue - this.downPayment);
            },
            get tradeInStorages() {
                return this.tradeInModels[this.tradeIn.model] || [];
            },

            get selectedCardRow() {
                return this.cardResults.find(r => r.selected && r.installments > 1) || this.cardResults.find(r => r.selected) || null;
            },
            get stoneFee() {
                if (!this.selectedCardRow || this.cardBalance <= 0) return 0;
                return this.selectedCardRow.fee_amount || 0;
            },
            get revenueTotal() {
                let rev = this.productPrice;
                if (this.tradeIn.enabled && this.tradeIn.result) {
                    rev += (this.tradeIn.result.resale_price || 0) - this.tradeInValue;
                }
                return rev;
            },
            get costTotal() {
                return this.productCost + this.tradeInValue;
            },
            get netMargin() {
                return this.revenueTotal - this.costTotal - this.stoneFee;
            },
            get marginPercent() {
                if (this.productPrice <= 0) return 0;
                return (this.netMargin / this.productPrice) * 100;
            },

            // --- Quick Values ---
            selectQuickValue(name, value) {
                this.product.description = name;
                this.product.priceInput = this.fmt(value);
                this.recalculate();
            },
            isQuickValueActive(name, value) {
                return this.product.description === name && this.product.priceInput === this.fmt(value);
            },
            isQuickValueActiveByName(baseName) {
                return this.product.description.startsWith(baseName);
            },

            // --- Trade-in ---
            filteredTradeInModels(search) {
                if (!search) return this.tradeInModels;
                const s = search.toLowerCase();
                return Object.fromEntries(
                    Object.entries(this.tradeInModels).filter(([k]) => k.toLowerCase().includes(s))
                );
            },
            selectTradeInModel(name) {
                this.tradeIn.model = name;
                const storages = this.tradeInModels[name] || [];
                if (!storages.includes(this.tradeIn.storage)) {
                    this.tradeIn.storage = storages[0] || '';
                }
                this.debouncedEvaluate();
            },
            clearTradeIn() {
                this.tradeIn.model = '';
                this.tradeIn.storage = '';
                this.tradeIn.battery = 100;
                this.tradeIn.deviceState = 'original';
                this.tradeIn.noBox = false;
                this.tradeIn.noCable = false;
                this.tradeIn.result = null;
                this.tradeIn.error = null;
                this.tradeIn.offeredInput = '';
                this.recalculate();
            },

            debouncedEvaluate() {
                clearTimeout(this._evalTimer);
                this._evalTimer = setTimeout(() => this.evaluateTradeIn(), 600);
            },

            async evaluateTradeIn() {
                if (!this.tradeIn.model || !this.tradeIn.storage) return;

                this.tradeIn.loading = true;
                this.tradeIn.error = null;

                try {
                    const res = await fetch('{{ route("negotiation.evaluate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            model: this.tradeIn.model,
                            storage: this.tradeIn.storage,
                            battery_health: this.tradeIn.battery,
                            device_state: this.tradeIn.deviceState,
                            no_box: this.tradeIn.noBox,
                            no_cable: this.tradeIn.noCable,
                        }),
                    });

                    const json = await res.json();

                    if (!res.ok || !json.success) {
                        this.tradeIn.error = json.message || 'Erro ao avaliar.';
                        this.tradeIn.result = null;
                        return;
                    }

                    const data = json.data;
                    if (data.listings_count === 0) {
                        this.tradeIn.error = 'Nenhum anúncio encontrado para este modelo.';
                        this.tradeIn.result = null;
                        return;
                    }

                    this.tradeIn.result = data;
                    this.tradeIn.offeredInput = this.fmt(data.suggested_price);
                    this.recalculate();
                } catch (e) {
                    this.tradeIn.error = 'Erro de conexão com o avaliador.';
                    this.tradeIn.result = null;
                } finally {
                    this.tradeIn.loading = false;
                }
            },

            // --- Payment ---
            async recalculate() {
                const balance = this.cardBalance;
                if (balance <= 0) {
                    this.cardResults = [];
                    return;
                }

                this.cardLoading = true;
                try {
                    const res = await fetch('/api/card-fees/calculate-all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ net_amount: balance }),
                    });
                    const json = await res.json();

                    if (json.success) {
                        const prev = this.activePreset;
                        this.cardResults = json.data.map(r => ({
                            ...r,
                            label: r.payment_type === 'debit' ? 'Débito' : (r.installments === 1 ? 'Crédito 1x' : `Crédito ${r.installments}x`),
                            selected: true,
                        }));
                        if (prev && prev !== 'all') this.setSelection(prev);
                    } else {
                        this.cardResults = [];
                    }
                } catch (e) {
                    this.cardResults = [];
                } finally {
                    this.cardLoading = false;
                }
            },

            setSelection(preset) {
                this.activePreset = preset;
                this.cardResults.forEach(r => {
                    if (preset === 'all') r.selected = true;
                    else if (preset === 'up_to_12') r.selected = r.payment_type !== 'debit' && r.installments <= 12;
                    else if (preset === 'above_6') r.selected = r.payment_type !== 'debit' && r.installments >= 6;
                    else if (preset === 'above_10') r.selected = r.payment_type !== 'debit' && r.installments >= 10;
                });
            },

            // --- Message ---
            buildMessage() {
                const lines = [];
                lines.push(`*Proposta DG Store* 📱`);
                lines.push('');

                if (this.product.description) {
                    lines.push(`*${this.product.description}*`);
                }
                lines.push(`Preço: *R$ ${this.fmt(this.productPrice)}*`);

                if (this.tradeInValue > 0) {
                    lines.push('');
                    const tiDesc = this.tradeIn.model ? this.tradeIn.model + ' ' + (this.tradeIn.storage || '') : 'Seminovo';
                    lines.push(`📱 *Trade-in:* ${tiDesc}`);
                    lines.push(`Valor: *- R$ ${this.fmt(this.tradeInValue)}*`);
                }

                if (this.downPayment > 0) {
                    lines.push('');
                    lines.push(`💵 *Entrada (Pix):* R$ ${this.fmt(this.downPayment)}`);
                }

                if (this.cardBalance > 0) {
                    lines.push('');
                    lines.push(`✅ *À vista (Pix):* R$ ${this.fmt(this.cardBalance)}`);
                    lines.push('');
                    lines.push('💳 *Parcelamento:*');
                    this.cardResults.forEach(r => {
                        if (!r.selected) return;
                        if (r.payment_type === 'debit') {
                            lines.push(`Débito R$ ${this.fmt(r.installment_value)}`);
                        } else {
                            lines.push(`${r.installments}x de R$ ${this.fmt(r.installment_value)}`);
                        }
                    });
                }

                lines.push('');
                lines.push('🔒 *Garantia e procedência verificada*');
                lines.push('🏢 _Atendimento DG Store_');

                return lines.join('\n');
            },

            async copyMessage() {
                try {
                    await navigator.clipboard.writeText(this.buildMessage());
                    this.copied = true;
                    this.showToast('Proposta copiada!');
                    setTimeout(() => { this.copied = false; }, 2000);
                } catch (e) {}
            },

            // --- Helpers ---
            clearAll() {
                this.product = { description: '', priceInput: '', costInput: '' };
                this.tradeIn = {
                    enabled: false, model: '', storage: '', battery: 100,
                    deviceState: 'original', noBox: false, noCable: false,
                    result: null, error: null, loading: false, offeredInput: '',
                };
                this.downPaymentInput = '';
                this.cardResults = [];
                this.activePreset = 'all';
            },

            parseNum(value) {
                if (!value) return 0;
                let str = String(value).trim().replace(/\s/g, '');
                str = str.replace(/\./g, '').replace(',', '.');
                const n = parseFloat(str);
                return isNaN(n) ? 0 : n;
            },

            fmt(value) {
                if (value === null || value === undefined || isNaN(value)) return '0,00';
                return Number(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            showToast(msg) {
                this.toast = msg;
                setTimeout(() => { this.toast = ''; }, 2000);
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
