<div x-data="cardFeeCalculator()" x-init="init()">
    <!-- Botão Flutuante -->
    <button @click="open = true"
            type="button"
            style="position: fixed; bottom: 24px; right: 24px; z-index: 40; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #111827, #374151); color: white; border: none; cursor: pointer; box-shadow: 0 8px 24px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)'"
            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.3)'"
            title="Calculadora de taxas">
        <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
    </button>

    <!-- Painel lateral direito -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-4"
         @keydown.escape.window="open = false"
         x-cloak
         style="position: fixed; bottom: 96px; right: 24px; z-index: 50; width: 440px; max-height: 85vh; overflow-y: auto; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25);">

            <!-- Header com tabs -->
            <div style="padding: 16px 24px 0;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <button @click="open = false" type="button" style="background: none; border: none; cursor: pointer; padding: 4px; color: #6b7280;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <h2 style="font-size: 17px; font-weight: 700; color: #111827;" x-text="activeTab === 'fees' ? 'Calculadora de Taxas' : 'Simulador Trade-in'"></h2>
                    <div style="width: 24px;"></div>
                </div>

                <!-- Tabs -->
                <div style="display: flex; background: #f3f4f6; border-radius: 10px; padding: 3px; margin-bottom: 4px;">
                    <button @click="activeTab = 'fees'" type="button"
                            :style="activeTab === 'fees'
                                ? 'flex: 1; padding: 8px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; background: white; color: #111827; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'
                                : 'flex: 1; padding: 8px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #6b7280;'">
                        Taxas
                    </button>
                    <button @click="activeTab = 'tradein'" type="button"
                            :style="activeTab === 'tradein'
                                ? 'flex: 1; padding: 8px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; background: white; color: #111827; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'
                                : 'flex: 1; padding: 8px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #6b7280;'">
                        Trade-in
                    </button>
                </div>
            </div>

            <!-- ═══════ ABA: TAXAS ═══════ -->
            <div x-show="activeTab === 'fees'" style="padding: 16px 24px 24px;">
                <!-- Valor que desejo receber -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Valor que desejo receber</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px; font-weight: 500;">R$</span>
                        <input type="text"
                               x-model="amountInput"
                               @input="calculate()"
                               placeholder="0,00"
                               x-ref="amountField"
                               style="width: 100%; padding: 14px 16px 14px 42px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 20px; font-weight: 600; color: #111827; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                    </div>
                </div>

                <!-- Tabela de todas as opções -->
                <div x-show="amount > 0" x-transition>
                    <!-- Pix e Débito -->
                    <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                        <div style="flex: 1; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-size: 13px; font-weight: 700; color: #059669;">Pix</div>
                                <div style="font-size: 10px; color: #6b7280;">Sem taxa</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 17px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(amount)"></div>
                            </div>
                        </div>
                        <div style="flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-size: 13px; font-weight: 700; color: #111827;">Debito</div>
                                <div style="font-size: 10px; color: #9ca3af;">Taxa 0,8%</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 17px; font-weight: 800; color: #111827;" x-text="'R$ ' + formatNumber(debitoCobrar)"></div>
                                <div style="font-size: 10px; color: #ef4444;" x-text="'taxa R$ ' + formatNumber(debitoTaxa)"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Crédito -->
                    <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                        <div style="display: grid; grid-template-columns: 1fr auto 56px; background: #111827; color: white; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                            <div style="padding: 10px 14px;">Forma</div>
                            <div style="padding: 10px 14px; text-align: right;">Cobrar</div>
                            <div style="padding: 10px 10px; text-align: center;"></div>
                        </div>
                        <template x-for="(row, idx) in results" :key="row.key">
                            <div :style="'display: grid; grid-template-columns: 1fr auto 56px; align-items: center; border-top: 1px solid #f3f4f6;' + (idx % 2 === 0 ? ' background: white;' : ' background: #f9fafb;')">
                                <div style="padding: 10px 14px;">
                                    <div style="font-size: 13px; font-weight: 600; color: #111827;" x-text="row.label"></div>
                                    <div style="font-size: 11px; color: #9ca3af;" x-text="'Taxa ' + row.percent.toString().replace('.', ',') + '%'"></div>
                                    <div x-show="row.parcelas > 1" style="font-size: 11px; color: #6b7280;" x-text="row.parcelas + 'x de R$ ' + formatNumber(row.cobrar / row.parcelas)"></div>
                                </div>
                                <div style="padding: 10px 14px; text-align: right;">
                                    <div style="font-size: 15px; font-weight: 700; color: #111827;" x-text="'R$ ' + formatNumber(row.cobrar)"></div>
                                    <div style="font-size: 10px; color: #ef4444;" x-text="'taxa R$ ' + formatNumber(row.taxa)"></div>
                                </div>
                                <div style="padding: 6px 10px; display: flex; align-items: center; justify-content: center;">
                                    <button @click.stop="copyRow(row)"
                                            type="button"
                                            :style="row.copied
                                                ? 'background: #059669; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; color: white; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;'
                                                : 'background: #e5e7eb; border: none; border-radius: 8px; width: 36px; height: 36px; cursor: pointer; color: #374151; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;'"
                                            :title="row.copied ? 'Copiado!' : 'Copiar mensagem'">
                                        <svg x-show="!row.copied" style="width: 16px; height: 16px; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <svg x-show="row.copied" style="width: 16px; height: 16px; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Você recebe -->
                    <div style="margin-top: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 13px; font-weight: 600; color: #059669;">VOCE RECEBE</span>
                        <span style="font-size: 20px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(amount)"></span>
                    </div>

                    <!-- Copiar todas + WhatsApp -->
                    <div style="display: flex; gap: 10px; margin-top: 12px;">
                        <button @click="copyAll()"
                                type="button"
                                :style="copiedAll
                                    ? 'flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #059669; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'
                                    : 'flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #111827; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'">
                            <svg x-show="!copiedAll" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="copiedAll" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="copiedAll ? 'Copiado!' : 'Copiar tudo'"></span>
                        </button>
                        <a :href="'https://wa.me/?text=' + encodeURIComponent(buildAllMessage())"
                           target="_blank"
                           style="padding: 14px 18px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; background: #16a34a; color: white; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;"
                           onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Estado vazio -->
                <div x-show="amount <= 0" style="text-align: center; padding: 32px 16px; color: #9ca3af;">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p style="font-size: 14px;">Digite o valor para simular todas as opcoes</p>
                </div>
            </div>

            <!-- ═══════ ABA: TRADE-IN ═══════ -->
            <div x-show="activeTab === 'tradein'" style="padding: 16px 24px 24px;">
                <!-- Valor do aparelho (preço de venda) -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Preco do aparelho</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px; font-weight: 500;">R$</span>
                        <input type="text"
                               x-model="devicePriceInput"
                               @input="calculateTradeIn()"
                               placeholder="0,00"
                               x-ref="devicePriceField"
                               style="width: 100%; padding: 14px 16px 14px 42px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 20px; font-weight: 600; color: #111827; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#111827'; this.style.background='white'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'">
                    </div>
                </div>

                <!-- Valor do trade-in -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">Valor do trade-in (aparelho usado)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #7c3aed; font-size: 15px; font-weight: 500;">R$</span>
                        <input type="text"
                               x-model="tradeInValueInput"
                               @input="calculateTradeIn()"
                               placeholder="0,00"
                               style="width: 100%; padding: 14px 16px 14px 42px; background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 12px; font-size: 20px; font-weight: 600; color: #5b21b6; outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#7c3aed'; this.style.background='white'"
                               onblur="this.style.borderColor='#ddd6fe'; this.style.background='#f5f3ff'">
                    </div>
                </div>

                <!-- Resultado: Restante a pagar -->
                <div x-show="devicePrice > 0" x-transition>
                    <!-- Breakdown visual -->
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb;">
                            <span style="font-size: 13px; color: #6b7280;">Aparelho</span>
                            <span style="font-size: 16px; font-weight: 700; color: #111827;" x-text="'R$ ' + formatNumber(devicePrice)"></span>
                        </div>
                        <div x-show="tradeInValue > 0" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb;">
                            <span style="font-size: 13px; color: #7c3aed;">Trade-in</span>
                            <span style="font-size: 16px; font-weight: 700; color: #7c3aed;" x-text="'- R$ ' + formatNumber(tradeInValue)"></span>
                        </div>

                        <!-- Restante -->
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 15px; font-weight: 700; color: #111827;">Restante a pagar</span>
                            <span style="font-size: 24px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(remaining)"></span>
                        </div>
                    </div>

                    <!-- Formas de pagamento do restante -->
                    <div x-show="remaining > 0" x-transition>
                        <!-- Pix e Debito -->
                        <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                            <div style="flex: 1; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 10px 12px; display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 12px; font-weight: 700; color: #059669;">Pix</div>
                                    <div style="font-size: 9px; color: #6b7280;">Sem taxa</div>
                                </div>
                                <div style="font-size: 15px; font-weight: 800; color: #059669;" x-text="'R$ ' + formatNumber(remaining)"></div>
                            </div>
                            <div style="flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 10px 12px; display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 12px; font-weight: 700; color: #111827;">Debito</div>
                                    <div style="font-size: 9px; color: #9ca3af;">Taxa 0,8%</div>
                                </div>
                                <div style="font-size: 15px; font-weight: 800; color: #111827;" x-text="'R$ ' + formatNumber(tiDebitoCobrar)"></div>
                            </div>
                        </div>

                        <!-- Parcelas do restante -->
                        <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                            <div style="display: grid; grid-template-columns: 1fr auto; background: #111827; color: white; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                <div style="padding: 8px 14px;">Parcelas</div>
                                <div style="padding: 8px 14px; text-align: right;">Valor</div>
                            </div>
                            <template x-for="(row, idx) in tiResults" :key="row.key">
                                <div :style="'display: grid; grid-template-columns: 1fr auto; align-items: center; border-top: 1px solid #f3f4f6;' + (idx % 2 === 0 ? ' background: white;' : ' background: #f9fafb;')">
                                    <div style="padding: 8px 14px;">
                                        <div style="font-size: 13px; font-weight: 600; color: #111827;" x-text="row.label"></div>
                                        <div x-show="row.parcelas > 1" style="font-size: 11px; color: #6b7280;" x-text="row.parcelas + 'x de R$ ' + formatNumber(row.cobrar / row.parcelas)"></div>
                                    </div>
                                    <div style="padding: 8px 14px; text-align: right;">
                                        <div style="font-size: 15px; font-weight: 700; color: #111827;" x-text="'R$ ' + formatNumber(row.cobrar)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Botão usar na aba de taxas -->
                    <button @click="useRemainingInFees()"
                            type="button"
                            style="width: 100%; margin-top: 16px; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid #e5e7eb; background: white; color: #374151; display: flex; align-items: center; justify-content: center; gap: 8px;"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Usar restante na calculadora de taxas
                    </button>

                    <!-- Copiar resumo + WhatsApp -->
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <button @click="copyTradeInSummary()"
                                type="button"
                                :style="tiCopied
                                    ? 'flex: 1; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; background: #059669; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'
                                    : 'flex: 1; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; background: #111827; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;'">
                            <svg x-show="!tiCopied" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="tiCopied" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="tiCopied ? 'Copiado!' : 'Copiar proposta'"></span>
                        </button>
                        <a :href="'https://wa.me/?text=' + encodeURIComponent(buildTradeInMessage())"
                           target="_blank"
                           style="padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; background: #16a34a; color: white; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;"
                           onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Estado vazio -->
                <div x-show="devicePrice <= 0" style="text-align: center; padding: 32px 16px; color: #9ca3af;">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p style="font-size: 14px;">Informe o preco do aparelho e o valor do trade-in</p>
                </div>
            </div>
    </div>
</div>

<script>
function cardFeeCalculator() {
    return {
        open: false,
        activeTab: 'fees',
        copiedAll: false,

        // ── Taxas ──
        rates: [
            { label: 'Credito 1x', key: '1x', percent: 2.85, parcelas: 1 },
            { label: 'Credito 2x', key: '2x', percent: 3.9, parcelas: 2 },
            { label: 'Credito 3x', key: '3x', percent: 4.9, parcelas: 3 },
            { label: 'Credito 4x', key: '4x', percent: 5.9, parcelas: 4 },
            { label: 'Credito 5x', key: '5x', percent: 6.9, parcelas: 5 },
            { label: 'Credito 6x', key: '6x', percent: 7.9, parcelas: 6 },
            { label: 'Credito 7x', key: '7x', percent: 8.9, parcelas: 7 },
            { label: 'Credito 8x', key: '8x', percent: 9.9, parcelas: 8 },
            { label: 'Credito 9x', key: '9x', percent: 9.9, parcelas: 9 },
            { label: 'Credito 10x', key: '10x', percent: 9.9, parcelas: 10 },
            { label: 'Credito 11x', key: '11x', percent: 9.9, parcelas: 11 },
            { label: 'Credito 12x', key: '12x', percent: 9.9, parcelas: 12 },
        ],
        debitoBasePercent: 0.8,
        amountInput: '',
        amount: 0,
        debitoCobrar: 0,
        debitoTaxa: 0,
        results: [],

        // ── Trade-in ──
        devicePriceInput: '',
        tradeInValueInput: '',
        devicePrice: 0,
        tradeInValue: 0,
        remaining: 0,
        tiDebitoCobrar: 0,
        tiResults: [],
        tiCopied: false,

        init() {
            this.results = this.rates.map(r => ({
                ...r, cobrar: 0, taxa: 0, copied: false
            }));
            this.tiResults = this.rates.map(r => ({
                ...r, cobrar: 0, taxa: 0
            }));
            this.$watch('open', (val) => {
                if (val) {
                    this.$nextTick(() => {
                        if (this.activeTab === 'fees' && this.$refs.amountField) {
                            this.$refs.amountField.focus();
                        } else if (this.activeTab === 'tradein' && this.$refs.devicePriceField) {
                            this.$refs.devicePriceField.focus();
                        }
                    });
                }
            });
            this.$watch('activeTab', (val) => {
                this.$nextTick(() => {
                    if (val === 'fees' && this.$refs.amountField) this.$refs.amountField.focus();
                    if (val === 'tradein' && this.$refs.devicePriceField) this.$refs.devicePriceField.focus();
                });
            });
        },

        // ── Utilitários ──

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

        // ── Calculadora de Taxas ──

        calculate() {
            this.amount = this.parseNumber(this.amountInput);
            const debitoPct = this.debitoBasePercent / 100;
            this.debitoCobrar = this.amount > 0 ? this.amount / (1 - debitoPct) : 0;
            this.debitoTaxa = this.debitoCobrar - this.amount;
            this.results = this.rates.map(r => {
                const pct = r.percent / 100;
                const cobrar = this.amount > 0 ? this.amount / (1 - pct) : 0;
                const taxa = cobrar - this.amount;
                return { ...r, cobrar, taxa, copied: false };
            });
        },

        // ── Trade-in ──

        calculateTradeIn() {
            this.devicePrice = this.parseNumber(this.devicePriceInput);
            this.tradeInValue = this.parseNumber(this.tradeInValueInput);
            this.remaining = Math.max(0, this.devicePrice - this.tradeInValue);

            // Debito do restante
            const debitoPct = this.debitoBasePercent / 100;
            this.tiDebitoCobrar = this.remaining > 0 ? this.remaining / (1 - debitoPct) : 0;

            // Parcelas do restante
            this.tiResults = this.rates.map(r => {
                const pct = r.percent / 100;
                const cobrar = this.remaining > 0 ? this.remaining / (1 - pct) : 0;
                return { ...r, cobrar, taxa: cobrar - this.remaining };
            });
        },

        useRemainingInFees() {
            this.amountInput = this.formatNumber(this.remaining);
            this.calculate();
            this.activeTab = 'fees';
        },

        // ── Mensagens ──

        buildRowMessage(row) {
            const linhas = [
                '*Condicoes de pagamento - DG Store*',
                '',
                'Forma: *' + row.label + '*',
                'Valor: *R$ ' + this.formatNumber(row.cobrar) + '*',
            ];
            if (row.parcelas > 1) {
                const vlr = row.cobrar / row.parcelas;
                linhas.push('Parcelas: *' + row.parcelas + 'x de R$ ' + this.formatNumber(vlr) + '*');
            }
            linhas.push('', 'Produto com garantia e procedencia');
            linhas.push('Atendimento DG Store');
            return linhas.join("\n");
        },

        buildAllMessage() {
            const credito1x = this.results.find(r => r.parcelas === 1);
            const linhas = [
                '*Condicoes de pagamento - DG Store*',
                '',
                '*A vista:*',
                'Pix: *R$ ' + this.formatNumber(this.amount) + '* _(melhor preco)_',
                'Debito: R$ ' + this.formatNumber(this.debitoCobrar),
                'Credito: R$ ' + this.formatNumber(credito1x ? credito1x.cobrar : 0),
                '',
                '*Parcelado no cartao:*',
            ];
            this.results.filter(r => r.parcelas >= 2).forEach(r => {
                const vlr = r.cobrar / r.parcelas;
                linhas.push(r.parcelas + 'x de R$ ' + this.formatNumber(vlr));
            });
            linhas.push('', 'Garantia e procedencia', 'Atendimento DG Store');
            return linhas.join("\n");
        },

        buildTradeInMessage() {
            const linhas = [
                '*Proposta de troca - DG Store*',
                '',
                'Aparelho novo: *R$ ' + this.formatNumber(this.devicePrice) + '*',
            ];
            if (this.tradeInValue > 0) {
                linhas.push('Seu aparelho (trade-in): *- R$ ' + this.formatNumber(this.tradeInValue) + '*');
            }
            linhas.push('');
            linhas.push('*Restante a pagar: R$ ' + this.formatNumber(this.remaining) + '*');
            linhas.push('');
            linhas.push('*Formas de pagamento do restante:*');
            linhas.push('Pix: *R$ ' + this.formatNumber(this.remaining) + '* _(melhor preco)_');
            linhas.push('Debito: R$ ' + this.formatNumber(this.tiDebitoCobrar));

            const cred1x = this.tiResults.find(r => r.parcelas === 1);
            if (cred1x) linhas.push('Credito 1x: R$ ' + this.formatNumber(cred1x.cobrar));

            linhas.push('');
            linhas.push('*Parcelado no cartao:*');
            this.tiResults.filter(r => r.parcelas >= 2 && r.parcelas <= 12).forEach(r => {
                const vlr = r.cobrar / r.parcelas;
                linhas.push(r.parcelas + 'x de R$ ' + this.formatNumber(vlr));
            });

            linhas.push('', 'Garantia e procedencia', 'Atendimento DG Store');
            return linhas.join("\n");
        },

        // ── Clipboard ──

        copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.focus(); ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            return Promise.resolve();
        },

        copyRow(row) {
            this.copyToClipboard(this.buildRowMessage(row)).then(() => {
                row.copied = true;
                setTimeout(() => { row.copied = false; }, 2000);
            });
        },

        copyAll() {
            this.copyToClipboard(this.buildAllMessage()).then(() => {
                this.copiedAll = true;
                setTimeout(() => { this.copiedAll = false; }, 2500);
            });
        },

        copyTradeInSummary() {
            this.copyToClipboard(this.buildTradeInMessage()).then(() => {
                this.tiCopied = true;
                setTimeout(() => { this.tiCopied = false; }, 2500);
            });
        }
    };
}
</script>
