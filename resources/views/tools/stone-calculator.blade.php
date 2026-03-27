<x-app-layout>
    <div class="py-6" x-data="stoneCalculatorAdvanced()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Calculadora Stone</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Calcule taxas e gere mensagens para WhatsApp</p>
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
                    <!-- Valores Rápidos -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem 1.25rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Valores Rápidos</label>
                            <button @click="editingQuickValues = !editingQuickValues" type="button"
                                    style="font-size: 11px; color: #9ca3af; cursor: pointer; background: none; border: none; display: flex; align-items: center; gap: 3px;"
                                    onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#9ca3af'">
                                <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span x-text="editingQuickValues ? 'Fechar' : 'Editar'"></span>
                            </button>
                        </div>

                        <!-- Lista de aparelhos -->
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            <template x-for="(qv, idx) in quickValues" :key="idx">
                                <div style="display: inline-flex;">
                                    <!-- Aparelho sem variantes (valor único) -->
                                    <template x-if="!qv.variants || qv.variants.length === 0">
                                        <button @click="selectQuickValue(qv.name, qv.value)" type="button"
                                                :style="isQuickValueActive(qv.name, qv.value)
                                                    ? 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: 2px solid #111827; background: #111827; color: white; white-space: nowrap;'
                                                    : 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: 2px solid #e5e7eb; background: white; color: #374151; white-space: nowrap;'">
                                            <span x-text="qv.name"></span>
                                            <span style="opacity: 0.6; margin-left: 4px;" x-text="'R$ ' + formatNumber(qv.value)"></span>
                                        </button>
                                    </template>
                                    <!-- Aparelho com variantes (cores) -->
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
                                                        :title="v.label + ' - R$ ' + formatNumber(v.value)">
                                                    <span :style="'width: 14px; height: 14px; border-radius: 50%; border: 2px solid ' + (isQuickValueActive(qv.name + ' ' + v.label, v.value) ? 'white' : '#d1d5db') + '; background: ' + v.color + ';'"></span>
                                                    <span :style="'font-size: 11px; font-weight: 600;' + (isQuickValueActive(qv.name + ' ' + v.label, v.value) ? ' color: white;' : ' color: #6b7280;')"
                                                          x-text="formatNumber(v.value)"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Edição dos valores rápidos -->
                        <div x-show="editingQuickValues" x-transition style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                            <template x-for="(qv, idx) in quickValues" :key="'edit-'+idx">
                                <div style="margin-bottom: 8px; padding: 8px; background: #f9fafb; border-radius: 8px;">
                                    <div style="display: grid; grid-template-columns: 1fr auto 28px; gap: 6px; align-items: center;">
                                        <input type="text" x-model="qv.name" @change="saveQuickValues()" placeholder="Nome do aparelho"
                                               style="padding: 6px 10px; font-size: 13px; border: 1px solid #e5e7eb; border-radius: 6px; outline: none; background: white;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                        <template x-if="!qv.variants || qv.variants.length === 0">
                                            <div style="position: relative;">
                                                <span style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 11px;">R$</span>
                                                <input type="text" :value="formatNumber(qv.value)" @change="qv.value = parseNumber($event.target.value); saveQuickValues()" placeholder="0,00"
                                                       style="width: 120px; padding: 6px 8px 6px 28px; font-size: 13px; border: 1px solid #e5e7eb; border-radius: 6px; outline: none; text-align: right; background: white;"
                                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                            </div>
                                        </template>
                                        <template x-if="qv.variants && qv.variants.length > 0"><span></span></template>
                                        <button @click="removeQuickValue(idx)" type="button" title="Remover"
                                                style="width: 28px; height: 28px; border-radius: 6px; border: none; background: #fef2f2; color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                onmouseover="this.style.background='#ef4444'; this.style.color='white'" onmouseout="this.style.background='#fef2f2'; this.style.color='#ef4444'">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Variantes -->
                                    <template x-if="qv.variants && qv.variants.length > 0">
                                        <div style="margin-top: 6px; padding-left: 4px;">
                                            <template x-for="(v, vi) in qv.variants" :key="'var-'+vi">
                                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                                    <span :style="'width: 16px; height: 16px; border-radius: 50%; border: 2px solid #d1d5db; background: ' + v.color + '; flex-shrink: 0;'"></span>
                                                    <input type="text" x-model="v.label" @change="saveQuickValues()" placeholder="Cor"
                                                           style="padding: 4px 8px; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 5px; outline: none; width: 90px; background: white;">
                                                    <input type="text" x-model="v.color" @change="saveQuickValues()" placeholder="#hex"
                                                           style="padding: 4px 8px; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 5px; outline: none; width: 70px; background: white; font-family: monospace;">
                                                    <div style="position: relative; flex: 1;">
                                                        <span style="position: absolute; left: 6px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 10px;">R$</span>
                                                        <input type="text" :value="formatNumber(v.value)" @change="v.value = parseNumber($event.target.value); saveQuickValues()"
                                                               style="width: 100%; padding: 4px 6px 4px 24px; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 5px; outline: none; text-align: right; background: white;">
                                                    </div>
                                                    <button @click="qv.variants.splice(vi, 1); saveQuickValues()" type="button"
                                                            style="width: 22px; height: 22px; border-radius: 4px; border: none; background: #fef2f2; color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <button @click="qv.variants.push({ label: '', color: '#cccccc', value: 0 }); saveQuickValues()" type="button"
                                                    style="padding: 3px 8px; font-size: 11px; color: #9ca3af; background: none; border: 1px dashed #d1d5db; border-radius: 4px; cursor: pointer;"
                                                    onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#9ca3af'">
                                                + Cor
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <div style="display: flex; gap: 6px;">
                                <button @click="addQuickValue(false)" type="button"
                                        style="flex: 1; padding: 6px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 6px; cursor: pointer;"
                                        onmouseover="this.style.borderColor='#111827'; this.style.color='#111827'" onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#6b7280'">
                                    + Aparelho
                                </button>
                                <button @click="addQuickValue(true)" type="button"
                                        style="flex: 1; padding: 6px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 6px; cursor: pointer;"
                                        onmouseover="this.style.borderColor='#111827'; this.style.color='#111827'" onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#6b7280'">
                                    + Aparelho com cores
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Compra -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Tipo de Compra</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <button @click="deliveryType = 'pronta'" type="button"
                                    :style="deliveryType === 'pronta'
                                        ? 'flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 600; border: 2px solid #111827; background: #111827; color: white; cursor: pointer;'
                                        : 'flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 500; border: 2px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer;'">
                                Pronta Entrega
                            </button>
                            <button @click="deliveryType = 'programada'" type="button"
                                    :style="deliveryType === 'programada'
                                        ? 'flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 600; border: 2px solid #111827; background: #111827; color: white; cursor: pointer;'
                                        : 'flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 500; border: 2px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer;'">
                                Compra Programada
                            </button>
                        </div>
                    </div>

                    <!-- Descrição + Valor -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem;">
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem;">Descrição do Aparelho (opcional)</label>
                            <input type="text"
                                   x-model="deviceDescription"
                                   x-ref="deviceField"
                                   @keydown.enter="$refs.amountField.focus()"
                                   placeholder="Ex: iPhone 16 Pro Max 256GB Black"
                                   style="width: 100%; padding: 12px 16px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 15px; color: #111827; outline: none;"
                                   onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                                   onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #111827; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem;">Valor que desejo receber *</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px; font-weight: 500;">R$</span>
                                <input type="text"
                                       x-model="amountInput"
                                       x-ref="amountField"
                                       @input.debounce.300ms="calculate()"
                                       @keydown.enter="$refs.downPaymentField.focus()"
                                       placeholder="0,00"
                                       style="width: 100%; padding: 16px 16px 16px 44px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 24px; font-weight: 700; color: #111827; outline: none; text-align: right;"
                                       onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                                       onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                            </div>
                        </div>
                    </div>

                    <!-- Entrada + Trade-in -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #059669; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem;">Entrada (Pix)</label>
                                <div style="position: relative;">
                                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #6ee7b7; font-size: 14px; font-weight: 500;">R$</span>
                                    <input type="text"
                                           x-model="downPaymentInput"
                                           x-ref="downPaymentField"
                                           @input.debounce.300ms="calculate()"
                                           @keydown.enter="$refs.tradeInField.focus()"
                                           placeholder="0,00"
                                           style="width: 100%; padding: 12px 14px 12px 40px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; font-size: 18px; font-weight: 600; color: #059669; outline: none; text-align: right;"
                                           onfocus="this.style.borderColor='#059669'; this.style.background='white'"
                                           onblur="this.style.borderColor='#bbf7d0'; this.style.background='#f0fdf4'">
                                </div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #d97706; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem;">Trade-in</label>
                                <div style="position: relative;">
                                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #fcd34d; font-size: 14px; font-weight: 500;">R$</span>
                                    <input type="text"
                                           x-model="tradeInInput"
                                           x-ref="tradeInField"
                                           @input.debounce.300ms="calculate()"
                                           placeholder="0,00"
                                           style="width: 100%; padding: 12px 14px 12px 40px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; font-size: 18px; font-weight: 600; color: #d97706; outline: none; text-align: right;"
                                           onfocus="this.style.borderColor='#d97706'; this.style.background='white'"
                                           onblur="this.style.borderColor='#fde68a'; this.style.background='#fef3c7'">
                                </div>
                            </div>
                        </div>
                        <!-- Exibir trade-in na mensagem -->
                        <div x-show="tradeInValue > 0" style="margin-top: 0.75rem; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" x-model="showTradeInInMessage" id="showTradeInMsg"
                                   style="width: 16px; height: 16px; accent-color: #111827; cursor: pointer;">
                            <label for="showTradeInMsg" style="font-size: 13px; color: #6b7280; cursor: pointer; user-select: none;">
                                Exibir valor trade-in na simulação
                            </label>
                        </div>

                        <!-- Resumo -->
                        <div x-show="finalAmount > 0" style="margin-top: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; display: flex; justify-content: space-between; font-size: 13px;">
                            <span x-show="downPayment > 0 || tradeInValue > 0" style="color: #6b7280;">Restante no cartão:</span>
                            <span x-show="downPayment <= 0 && tradeInValue <= 0" style="color: #6b7280;">Valor no cartão:</span>
                            <span style="font-weight: 700; color: #111827;" x-text="'R$ ' + formatNumber(finalAmount)"></span>
                        </div>
                    </div>

                    <!-- Preview da mensagem -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Preview da mensagem</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <button @click="copyAll()" type="button"
                                        :style="copiedAll
                                            ? 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: #059669; color: white; display: flex; align-items: center; gap: 4px;'
                                            : 'padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: #111827; color: white; display: flex; align-items: center; gap: 4px;'">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="copiedAll ? 'Copiado!' : 'Copiar'"></span>
                                </button>
                                <a :href="'https://wa.me/?text=' + encodeURIComponent(buildAllMessage())"
                                   target="_blank"
                                   style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: #16a34a; color: white; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                                    <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; font-size: 13px; color: #374151; white-space: pre-wrap; font-family: monospace; line-height: 1.6; min-height: 120px; max-height: 300px; overflow-y: auto;"
                             x-text="results.length > 0 ? buildAllMessage() : 'Preencha o valor para visualizar a mensagem...'"></div>
                    </div>
                </div>

                <!-- COLUNA DIREITA: Resultados -->
                <div>
                    <!-- Loading -->
                    <div x-show="loading" style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 3rem; text-align: center;">
                        <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top-color: #111827; border-radius: 50%; animation: stoneCalcSpin 1s linear infinite;"></div>
                    </div>

                    <!-- Erro -->
                    <div x-show="error && !loading" x-transition style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: start; gap: 10px;">
                            <svg style="width: 20px; height: 20px; color: #dc2626; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <div style="font-size: 14px; font-weight: 600; color: #dc2626; margin-bottom: 4px;">Erro</div>
                                <div style="font-size: 13px; color: #991b1b;" x-text="error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado vazio -->
                    <div x-show="!loading && results.length === 0 && !error" style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 3rem; text-align: center;">
                        <svg style="width: 48px; height: 48px; color: #d1d5db; margin: 0 auto 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <p style="font-size: 14px; color: #9ca3af;">Preencha o valor desejado para calcular as parcelas</p>
                    </div>

                    <!-- Resultados -->
                    <div x-show="!loading && results.length > 0" x-transition>
                        <!-- Pix -->
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.75rem; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <div>
                                <div style="font-size: 15px; font-weight: 700; color: #059669;">Pix</div>
                                <div style="font-size: 12px; color: #6b7280;">Melhor preço - sem taxa</div>
                            </div>
                            <div style="font-size: 22px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(finalAmount)"></div>
                        </div>

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

                        <!-- Tabela -->
                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                            <div style="display: grid; grid-template-columns: 32px 1fr auto 52px; background: #111827; color: white; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                <div style="padding: 10px 0 10px 10px; display: flex; align-items: center;">
                                    <input type="checkbox" :checked="results.every(r => r.selected)" @change="results.forEach(r => r.selected = $event.target.checked); activePreset = $event.target.checked ? 'all' : null"
                                           style="width: 15px; height: 15px; accent-color: white; cursor: pointer;">
                                </div>
                                <div style="padding: 10px 14px;">Forma</div>
                                <div style="padding: 10px 14px; text-align: right;">Cliente Paga</div>
                                <div></div>
                            </div>
                            <template x-for="(row, idx) in results" :key="idx">
                                <div :style="'display: grid; grid-template-columns: 32px 1fr auto 52px; align-items: center; border-top: 1px solid #f3f4f6; transition: opacity 0.15s;' + (row.selected ? (idx % 2 === 0 ? ' background: white;' : ' background: #f9fafb;') : ' background: #f3f4f6; opacity: 0.4;')">
                                    <div style="padding: 10px 0 10px 10px; display: flex; align-items: center;">
                                        <input type="checkbox" x-model="row.selected" @change="activePreset = null" style="width: 15px; height: 15px; accent-color: #111827; cursor: pointer;">
                                    </div>
                                    <div style="padding: 10px 14px;">
                                        <div style="font-size: 14px; font-weight: 600; color: #111827;" x-text="row.label"></div>
                                        <div x-show="row.installments > 1" style="font-size: 12px; color: #6b7280;" x-text="row.installments + 'x de R$ ' + formatNumber(row.installment_value)"></div>
                                        <div style="font-size: 11px; color: #9ca3af;" x-text="'Taxa ' + row.mdr_rate.toString().replace('.', ',') + '%'"></div>
                                    </div>
                                    <div style="padding: 10px 14px; text-align: right;">
                                        <div style="font-size: 16px; font-weight: 700; color: #111827;" x-text="'R$ ' + formatNumber(row.gross_amount)"></div>
                                        <div style="font-size: 11px; color: #ef4444;" x-text="'taxa R$ ' + formatNumber(row.fee_amount)"></div>
                                    </div>
                                    <div style="padding: 6px 8px; display: flex; align-items: center; justify-content: center;">
                                        <button @click.stop="copyRow(row)" type="button"
                                                :style="row.copied
                                                    ? 'background: #059669; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; color: white; display: flex; align-items: center; justify-content: center;'
                                                    : 'background: #e5e7eb; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; color: #374151; display: flex; align-items: center; justify-content: center;'"
                                                :title="row.copied ? 'Copiado!' : 'Copiar mensagem'">
                                            <svg x-show="!row.copied" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <svg x-show="row.copied" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Você recebe + Ações fixas -->
                        <div style="margin-top: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.75rem; padding: 1rem 1.25rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 14px; font-weight: 600; color: #059669;">VOCÊ RECEBE</span>
                                <span style="font-size: 22px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(finalAmount)"></span>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 0.75rem;">
                            <button @click="copyAll()" type="button"
                                    :style="copiedAll
                                        ? 'flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #059669; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'
                                        : 'flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #111827; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="copiedAll ? 'Copiado!' : 'Copiar todas'"></span>
                            </button>
                            <a :href="'https://wa.me/?text=' + encodeURIComponent(buildAllMessage())"
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
        @keyframes stoneCalcSpin { to { transform: rotate(360deg); } }
        @media (max-width: 768px) {
            [style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
        }
    </style>

    @push('scripts')
    <script>
    function stoneCalculatorAdvanced() {
        return {
            loading: false,
            copiedAll: false,
            toast: '',
            error: '',
            deliveryType: 'pronta',
            deviceDescription: '',
            activePreset: 'all',
            editingQuickValues: false,

            quickValues: [],
            defaultQuickValues: [
                { name: 'iPhone 15 128GB', value: 3999 },
                { name: 'iPhone 16 128GB', value: 4699 },
                { name: 'iPhone 16e 128GB', value: 3599 },
                { name: 'iPhone 17 256GB', value: 5699 },
                { name: 'iPhone 17e 256GB', value: 4199 },
                { name: 'iPhone 17 Pro 256GB', value: 7799 },
                { name: 'iPhone 17 Pro Max 256GB', value: 0, variants: [
                    { label: 'Laranja', color: '#f97316', value: 8299 },
                    { label: 'Azul', color: '#3b82f6', value: 8399 },
                    { label: 'Branco', color: '#f5f5f4', value: 8599 },
                ]},
            ],

            amountInput: '',
            amount: 0,
            downPaymentInput: '',
            downPayment: 0,
            tradeInInput: '',
            tradeInValue: 0,
            showTradeInInMessage: true,
            remaining: 0,
            finalAmount: 0,
            results: [],

            presets: [
                { key: 'all', label: 'Todas' },
                { key: 'up_to_12', label: 'Até 12x' },
                { key: 'above_6', label: 'Acima de 6x' },
                { key: 'above_8', label: 'Acima de 8x' },
                { key: 'above_10', label: 'Acima de 10x' },
            ],

            init() {
                const saved = localStorage.getItem('stoneCalcQuickValues');
                this.quickValues = saved ? JSON.parse(saved) : [...this.defaultQuickValues];
                this.$nextTick(() => {
                    if (this.$refs.amountField) this.$refs.amountField.focus();
                });
            },

            selectQuickValue(name, value) {
                this.deviceDescription = name;
                this.amountInput = this.formatNumber(value);
                this.calculate();
            },

            isQuickValueActive(name, value) {
                return this.deviceDescription === name && this.amountInput === this.formatNumber(value);
            },

            isQuickValueActiveByName(baseName) {
                return this.deviceDescription.startsWith(baseName);
            },

            saveQuickValues() {
                localStorage.setItem('stoneCalcQuickValues', JSON.stringify(this.quickValues));
            },

            addQuickValue(withVariants) {
                if (withVariants) {
                    this.quickValues.push({ name: '', value: 0, variants: [
                        { label: '', color: '#cccccc', value: 0 }
                    ]});
                } else {
                    this.quickValues.push({ name: '', value: 0 });
                }
                this.saveQuickValues();
            },

            removeQuickValue(idx) {
                this.quickValues.splice(idx, 1);
                this.saveQuickValues();
            },

            parseNumber(value) {
                if (value === null || value === undefined || value === '') return 0;
                let str = String(value).trim().replace(/\s/g, '');
                if (!str) return 0;
                str = str.replace(/\./g, '').replace(',', '.');
                const n = parseFloat(str);
                return isNaN(n) ? 0 : n;
            },

            formatNumber(value) {
                if (value === null || value === undefined || isNaN(value)) return '0,00';
                return Number(value).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2, maximumFractionDigits: 2
                });
            },

            clearAll() {
                this.amountInput = '';
                this.amount = 0;
                this.downPaymentInput = '';
                this.downPayment = 0;
                this.tradeInInput = '';
                this.tradeInValue = 0;
                this.remaining = 0;
                this.finalAmount = 0;
                this.results = [];
                this.deviceDescription = '';
                this.error = '';
                this.activePreset = 'all';
                this.$nextTick(() => {
                    if (this.$refs.amountField) this.$refs.amountField.focus();
                });
            },

            async calculate() {
                this.amount = this.parseNumber(this.amountInput);
                this.downPayment = this.parseNumber(this.downPaymentInput);
                this.tradeInValue = this.parseNumber(this.tradeInInput);

                let netAmount = this.amount;

                if (this.tradeInValue > 0) {
                    netAmount = Math.max(0, netAmount - this.tradeInValue);
                }

                if (this.downPayment > 0) {
                    netAmount = Math.max(0, netAmount - this.downPayment);
                }

                this.remaining = netAmount;
                this.finalAmount = netAmount;

                if (netAmount <= 0) {
                    this.results = [];
                    return;
                }

                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch('/api/card-fees/calculate-all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ net_amount: netAmount })
                    });

                    const data = await response.json();

                    if (data.success) {
                        const prevPreset = this.activePreset;
                        this.results = data.data.map(r => ({
                            ...r,
                            label: r.payment_type === 'debit' ? 'Débito' : (r.installments === 1 ? 'Crédito 1x' : `Crédito ${r.installments}x`),
                            copied: false,
                            selected: true
                        }));
                        if (prevPreset && prevPreset !== 'all') {
                            this.setSelection(prevPreset);
                        }
                        this.error = '';
                    } else {
                        this.error = data.message || 'Erro ao calcular taxas';
                        this.results = [];
                    }
                } catch (error) {
                    this.error = 'Erro ao conectar com o servidor. Verifique sua conexão.';
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },

            buildRowMessage(row) {
                const vlr = row.installment_value;
                const deliveryLabel = this.deliveryType === 'programada' ? 'COMPRA PROGRAMADA' : 'PRONTA ENTREGA';
                const header = this.deviceDescription
                    ? `*Condições de pagamento - DG Store (${deliveryLabel})* 💳 - ${this.deviceDescription}`
                    : `*Condições de pagamento - DG Store (${deliveryLabel})* 💳`;

                const linhas = [header, ''];

                const hasTradeInText = this.tradeInValue > 0 && this.showTradeInInMessage;
                if (this.downPayment > 0 || this.tradeInValue > 0) {
                    if (hasTradeInText) {
                        linhas.push('📱 *Aparelho novo:* R$ ' + this.formatNumber(this.amount));
                        linhas.push('⬇️ *Entrada seminovo:* - R$ ' + this.formatNumber(this.tradeInValue));
                        linhas.push('');
                    }
                    if (this.downPayment > 0) {
                        linhas.push('💵 *Entrada (Pix):*');
                        linhas.push('*R$ ' + this.formatNumber(this.downPayment) + '*');
                        linhas.push('');
                    }
                    linhas.push('💳 *Restante no cartão:*');
                    const parcLabel = row.payment_type === 'debit'
                        ? `Débito R$ ${this.formatNumber(vlr)}`
                        : `*${row.installments}x de R$ ${this.formatNumber(vlr)}*`;
                    linhas.push(parcLabel);
                } else {
                    linhas.push('💳 *No cartão:*');
                    const parcLabel = row.payment_type === 'debit'
                        ? `Débito R$ ${this.formatNumber(vlr)}`
                        : `*${row.installments}x de R$ ${this.formatNumber(vlr)}*`;
                    linhas.push(parcLabel);
                    linhas.push('');
                    linhas.push('✅ *Diferença à vista:*');
                    linhas.push(`*R$ ${this.formatNumber(this.finalAmount)}*`);
                }

                linhas.push('');
                linhas.push('🔒 *Garantia e procedência verificada*');
                linhas.push('🏢 _Atendimento DG Store_');

                return linhas.join('\n');
            },

            buildAllMessage() {
                const deliveryLabel = this.deliveryType === 'programada' ? 'COMPRA PROGRAMADA' : 'PRONTA ENTREGA';
                const header = this.deviceDescription
                    ? `*Condições de pagamento - DG Store (${deliveryLabel})* 💳 - ${this.deviceDescription}`
                    : `*Condições de pagamento - DG Store (${deliveryLabel})* 💳`;

                const linhas = [header, ''];

                const showTradeIn = this.tradeInValue > 0 && this.showTradeInInMessage;
                if (showTradeIn) {
                    linhas.push('📱 *Aparelho novo:* R$ ' + this.formatNumber(this.amount));
                    linhas.push('⬇️ *Entrada seminovo:* - R$ ' + this.formatNumber(this.tradeInValue));
                    linhas.push('');
                }

                if (this.downPayment > 0) {
                    linhas.push('💵 *Entrada (Pix):*');
                    linhas.push('*R$ ' + this.formatNumber(this.downPayment) + '*');
                    linhas.push('');
                    linhas.push('💳 *Restante no cartão:*');
                } else {
                    linhas.push('✅ *Diferença à vista:*');
                    linhas.push(`*R$ ${this.formatNumber(this.finalAmount)}*`);
                    linhas.push('');
                    linhas.push('💳 *No cartão:*');
                }

                this.results.forEach(r => {
                    if (!r.selected) return;
                    const vlr = r.installment_value;
                    if (r.payment_type === 'debit') {
                        linhas.push(`Débito R$ ${this.formatNumber(vlr)}`);
                    } else {
                        linhas.push(`${r.installments}x de R$ ${this.formatNumber(vlr)}`);
                    }
                });

                linhas.push('');
                linhas.push('🔒 *Garantia e procedência verificada*');
                linhas.push('🏢 _Atendimento DG Store_');

                return linhas.join('\n');
            },

            setSelection(preset) {
                this.activePreset = preset;
                this.results.forEach(r => {
                    if (preset === 'all') {
                        r.selected = true;
                    } else if (preset === 'up_to_12') {
                        r.selected = r.payment_type !== 'debit' && r.installments <= 12;
                    } else if (preset === 'above_6') {
                        r.selected = r.payment_type !== 'debit' && r.installments >= 6;
                    } else if (preset === 'above_8') {
                        r.selected = r.payment_type !== 'debit' && r.installments >= 8;
                    } else if (preset === 'above_10') {
                        r.selected = r.payment_type !== 'debit' && r.installments >= 10;
                    }
                });
            },

            showToast(message) {
                this.toast = message;
                setTimeout(() => { this.toast = ''; }, 2000);
            },

            async copyRow(row) {
                const message = this.buildRowMessage(row);
                try {
                    await navigator.clipboard.writeText(message);
                    row.copied = true;
                    this.showToast('Mensagem copiada!');
                    setTimeout(() => { row.copied = false; }, 2000);
                } catch (err) {
                    console.error('Erro ao copiar:', err);
                }
            },

            async copyAll() {
                const message = this.buildAllMessage();
                try {
                    await navigator.clipboard.writeText(message);
                    this.copiedAll = true;
                    this.showToast('Todas as condições copiadas!');
                    setTimeout(() => { this.copiedAll = false; }, 2000);
                } catch (err) {
                    console.error('Erro ao copiar:', err);
                }
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
