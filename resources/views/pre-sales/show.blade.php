<x-app-layout>
    <x-slot name="title">Pré-Venda #{{ $preSale->pre_sale_number }}</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 0.5rem; color: #6ee7b7;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 0.5rem; color: #fca5a5;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
                <div class="flex items-center">
                    <a href="{{ route('pre-sales.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #818181; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#222222'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;">{{ $preSale->pre_sale_number }}</h1>
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; {{ $preSale->status->color() }}">
                                {{ $preSale->status->label() }}
                            </span>
                        </div>
                        <p style="font-size: 0.875rem; color: #818181; margin-top: 0.25rem;">
                            Criada em {{ $preSale->created_at->format('d/m/Y \à\s H:i') }} por {{ $preSale->seller_name }}
                        </p>
                    </div>
                </div>

                @php
                    $isOwnerOrAdmin = auth()->user()->isAdmin() || auth()->id() === $preSale->seller_id;
                @endphp

                <!-- Ação: Marcar como Pronta (vendedoras que NÃO são donas nem admin) -->
                @if(!$isOwnerOrAdmin && $preSale->isPending())
                    <form method="POST" action="{{ route('pre-sales.mark-ready', $preSale) }}">
                        @csrf
                        <button type="submit"
                                style="padding: 0.625rem 1.25rem; background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Concluída — Pronta p/ Lançar
                        </button>
                    </form>
                @endif

                <!-- Ações: Efetivar / Cancelar (admin OU dona da venda) -->
                @if($isOwnerOrAdmin && $preSale->isActionable())
                    <div class="flex gap-2" x-data="{ showCancelModal: false }">
                        <form method="POST" action="{{ route('pre-sales.convert', $preSale) }}">
                            @csrf
                            <button type="submit"
                                    style="padding: 0.625rem 1.25rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Efetivar
                            </button>
                        </form>
                        <button type="button" @click="showCancelModal = true"
                                style="padding: 0.625rem 1.25rem; background: rgba(220,38,38,0.1); color: #f87171; border: 1px solid rgba(220,38,38,0.2); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar
                        </button>

                        <!-- Modal Cancelamento -->
                        <div x-show="showCancelModal" x-cloak style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);">
                            <div @click.outside="showCancelModal = false" style="background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 1rem; padding: 1.5rem; width: 100%; max-width: 28rem;">
                                <h3 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">Cancelar Pré-Venda</h3>
                                <p style="font-size: 0.875rem; color: #818181; margin-bottom: 1rem;">O produto será liberado para novas operações.</p>
                                <form method="POST" action="{{ route('pre-sales.cancel', $preSale) }}">
                                    @csrf
                                    <div style="margin-bottom: 1rem;">
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Motivo (opcional)</label>
                                        <textarea name="reason" rows="3"
                                                  style="width: 100%; padding: 0.75rem; background: #141414; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem; resize: none;"
                                                  placeholder="Informe o motivo do cancelamento..."></textarea>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <button type="button" @click="showCancelModal = false"
                                                style="padding: 0.625rem 1.25rem; color: #818181; font-size: 0.875rem; cursor: pointer; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem;">
                                            Voltar
                                        </button>
                                        <button type="submit"
                                                style="padding: 0.625rem 1.25rem; background: rgba(220,38,38,0.15); color: #f87171; border: 1px solid rgba(220,38,38,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                            Confirmar Cancelamento
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Venda convertida -->
            @if($preSale->isConverted() && $preSale->convertedSale)
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(22,163,106,0.08); border: 1px solid rgba(22,163,106,0.2); border-radius: 0.75rem; display: flex; align-items: center; gap: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #4ade80; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div>
                        <span style="font-weight: 600; color: #4ade80;">Efetivada como venda</span>
                        <a href="{{ route('sales.show', $preSale->convertedSale) }}" style="margin-left: 0.5rem; font-size: 0.875rem; color: #60a5fa; text-decoration: underline;">
                            #{{ $preSale->convertedSale->sale_number }}
                        </a>
                        <span style="font-size: 0.8125rem; color: #818181; margin-left: 0.5rem;">
                            em {{ $preSale->converted_at?->format('d/m/Y \à\s H:i') }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- Cancelamento -->
            @if($preSale->isCancelled())
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2); border-radius: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #f87171; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span style="font-weight: 600; color: #f87171;">Cancelada</span>
                        <span style="font-size: 0.8125rem; color: #818181;">
                            em {{ $preSale->cancelled_at?->format('d/m/Y \à\s H:i') }}
                        </span>
                    </div>
                    @if($preSale->cancelled_reason)
                        <div style="margin-top: 0.5rem; font-size: 0.875rem; color: #a4a4a4; padding-left: 1.75rem;">
                            Motivo: {{ $preSale->cancelled_reason }}
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Produto -->
                <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 1rem;">Produto</div>

                    @php $snapshot = $preSale->product_snapshot ?? []; @endphp

                    <div style="font-size: 1rem; font-weight: 600; color: #e3e3e3;">{{ $preSale->product_name }}</div>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-top: 1rem;">
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">IMEI</span>
                            <div style="font-size: 0.8125rem; color: #a4a4a4; font-family: monospace;">{{ $preSale->product_imei }}</div>
                        </div>
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Condição</span>
                            <div style="font-size: 0.8125rem; {{ $preSale->condition === 'new' ? 'color: #60a5fa;' : 'color: #fbbf24;' }}">
                                {{ $preSale->condition === 'new' ? 'Novo' : 'Seminovo' }}
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Armazenamento</span>
                            <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $snapshot['storage'] ?? '-' }}</div>
                        </div>
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Cor</span>
                            <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $snapshot['color'] ?? '-' }}</div>
                        </div>
                        @if(auth()->user()->isAdmin())
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Origem</span>
                                <div style="font-size: 0.8125rem; {{ ($snapshot['source'] ?? '') === 'own_stock' ? 'color: #60a5fa;' : 'color: #c084fc;' }}">
                                    {{ ($snapshot['source'] ?? '') === 'own_stock' ? 'Nosso Estoque' : 'Consignado' }}
                                    @if(!empty($snapshot['supplier_name']))
                                        <span style="color: #818181;">({{ $snapshot['supplier_name'] }})</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Custo</span>
                                <div style="font-size: 0.8125rem; color: #818181;">R$ {{ number_format((float) $preSale->cost_price, 2, ',', '.') }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Cliente -->
                <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 1rem;">Cliente</div>

                    @if($preSale->customer)
                        <div style="font-size: 1rem; font-weight: 600; color: #e3e3e3;">{{ $preSale->customer->name }}</div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-top: 1rem;">
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Telefone</span>
                                <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $preSale->customer->formatted_phone ?? $preSale->customer->phone }}</div>
                            </div>
                            @if($preSale->customer->cpf)
                                <div>
                                    <span style="font-size: 0.6875rem; color: #515151;">CPF</span>
                                    <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $preSale->customer->cpf }}</div>
                                </div>
                            @endif
                            @if($preSale->customer->instagram)
                                <div>
                                    <span style="font-size: 0.6875rem; color: #515151;">Instagram</span>
                                    <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $preSale->customer->instagram }}</div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div style="font-size: 0.875rem; color: #515151;">Cliente removido</div>
                    @endif
                </div>
            </div>

            <!-- Proposta Financeira -->
            <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; margin-top: 1.5rem;">
                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 1rem;">Proposta Financeira</div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Coluna 1: Valores -->
                    <div style="display: grid; gap: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8125rem; color: #818181;">Preço de venda:</span>
                            <span style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3;">{{ $preSale->formatted_unit_price }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8125rem; color: #818181;">Sinal ({{ $preSale->down_payment_method_label }}):</span>
                            <span style="font-size: 0.9375rem; font-weight: 600; color: #60a5fa;">- {{ $preSale->formatted_down_payment }}</span>
                        </div>
                        @if($preSale->trade_in_value && $preSale->trade_in_value > 0)
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.8125rem; color: #818181;">Trade-in:</span>
                                <span style="font-size: 0.9375rem; font-weight: 600; color: #c084fc;">- {{ $preSale->formatted_trade_in_value }}</span>
                            </div>
                        @endif
                        <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.875rem; font-weight: 600; color: #a4a4a4;">Saldo restante:</span>
                            <span style="font-size: 1.25rem; font-weight: 700; color: #4ade80;">{{ $preSale->formatted_final_balance }}</span>
                        </div>
                        @php
                            $costPrice = (float) $preSale->cost_price;
                            $netReceived = $preSale->payment_method === 'credit_card' && $preSale->card_net_amount > 0
                                ? (float) $preSale->card_net_amount
                                : (float) $preSale->unit_price;
                            $profit = $costPrice > 0 ? ($netReceived - $costPrice) : 0;
                            $commission = ($costPrice > 0 && $profit > 0) ? round($profit * 0.10, 2) : 0;
                        @endphp
                        @if($commission > 0)
                            <div style="margin-top: 0.375rem; padding: 0.5rem 0.625rem; background: rgba(124,58,237,0.08); border: 1px solid rgba(124,58,237,0.15); border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.8125rem; color: #a78bfa; display: flex; align-items: center; gap: 0.375rem;">
                                    <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Sua comissão aproximada
                                </span>
                                <span style="font-size: 0.9375rem; font-weight: 700; color: #c4b5fd;">R$ {{ number_format($commission, 2, ',', '.') }}</span>
                            </div>
                        @elseif($costPrice <= 0)
                            <div style="margin-top: 0.375rem; font-size: 0.75rem; color: #666666; font-style: italic;">
                                Comissão indisponível — custo do produto não informado.
                            </div>
                        @endif
                    </div>

                    <!-- Coluna 2: Pagamento -->
                    <div style="display: grid; gap: 0.75rem; align-content: start;">
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Forma de Pagamento</span>
                            <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;">{{ $preSale->payment_method_label }}</div>
                        </div>
                        @if($preSale->payment_method === 'credit_card' && $preSale->installments)
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Parcelas</span>
                                <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;">{{ $preSale->installments }}x</div>
                            </div>
                        @endif
                        @if($preSale->card_gross_amount)
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Valor bruto (cobrado)</span>
                                <div style="font-size: 0.875rem; color: #60a5fa; margin-top: 0.125rem;">R$ {{ number_format((float) $preSale->card_gross_amount, 2, ',', '.') }}</div>
                            </div>
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Taxa Stone (MDR)</span>
                                <div style="font-size: 0.875rem; color: #fbbf24; margin-top: 0.125rem;">{{ number_format((float) $preSale->card_fee_rate, 2, ',', '.') }}%</div>
                            </div>
                        @endif
                    </div>

                    <!-- Coluna 3: Trade-in -->
                    <div style="display: grid; gap: 0.75rem; align-content: start;">
                        @if($preSale->trade_in_device)
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Aparelho de Troca</span>
                                <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;">{{ $preSale->trade_in_device['model'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Valor do Trade-in</span>
                                <div style="font-size: 0.875rem; color: #c084fc; margin-top: 0.125rem;">{{ $preSale->formatted_trade_in_value }}</div>
                            </div>
                            @if(!empty($preSale->trade_in_device['condition']))
                                <div>
                                    <span style="font-size: 0.6875rem; color: #515151;">Condição do Trade-in</span>
                                    @php
                                        $condLabels = ['excellent' => 'Excelente', 'good' => 'Bom', 'fair' => 'Regular', 'poor' => 'Ruim'];
                                    @endphp
                                    <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;">{{ $condLabels[$preSale->trade_in_device['condition']] ?? $preSale->trade_in_device['condition'] }}</div>
                                </div>
                            @endif
                        @else
                            <div style="font-size: 0.8125rem; color: #515151;">Sem trade-in</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Simulação de Parcelamento -->
            @if(isset($installmentOptions) && count($installmentOptions) > 0)
                <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; margin-top: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">
                            Simulação de Parcelamento
                        </div>
                        <span style="font-size: 0.75rem; color: #515151;">
                            Base: <span style="color: #4ade80; font-weight: 600;">R$ {{ number_format((float) $preSale->final_balance, 2, ',', '.') }}</span>
                            (saldo restante)
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(9rem, 1fr)); gap: 0.5rem;">
                        @foreach($installmentOptions as $option)
                            @php
                                $isHighlighted = in_array($option->installments, [1, 3, 6, 10, 12]);
                            @endphp
                            <div style="padding: 0.625rem 0.75rem; border-radius: 0.5rem; {{ $isHighlighted ? 'background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2);' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);' }}">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.25rem;">
                                    <span style="font-size: 0.8125rem; font-weight: 700; {{ $isHighlighted ? 'color: #60a5fa;' : 'color: #a4a4a4;' }}">
                                        {{ $option->installments }}x
                                    </span>
                                    <span style="font-size: 0.6875rem; color: #666;">
                                        {{ number_format($option->mdrRate, 1, ',', '') }}%
                                    </span>
                                </div>
                                <div style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3;">
                                    R$ {{ number_format($option->installmentValue, 2, ',', '.') }}
                                </div>
                                <div style="font-size: 0.6875rem; color: #515151; margin-top: 0.125rem;">
                                    Total: R$ {{ number_format($option->grossAmount, 2, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top: 0.75rem; font-size: 0.6875rem; color: #515151; font-style: italic;">
                        * Valores simulados com taxas Stone. O cliente paga o valor bruto (total) e o lojista recebe R$ {{ number_format((float) $preSale->final_balance, 2, ',', '.') }} líquido.
                    </div>
                </div>
            @endif

            <!-- Observações -->
            @if($preSale->notes)
                <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; margin-top: 1.5rem;">
                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.5rem;">Observações</div>
                    <div style="font-size: 0.875rem; color: #a4a4a4; white-space: pre-line;">{{ $preSale->notes }}</div>
                </div>
            @endif

            <!-- Info da vendedora -->
            <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; margin-top: 1.5rem;">
                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Informações</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr)); gap: 0.75rem;">
                    <div>
                        <span style="font-size: 0.6875rem; color: #515151;">Vendedora</span>
                        <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $preSale->seller_name }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.6875rem; color: #515151;">Criada em</span>
                        <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $preSale->created_at->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.6875rem; color: #515151;">Última atualização</span>
                        <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $preSale->updated_at->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
