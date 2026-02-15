<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div class="flex items-center">
                    <a href="{{ route('sales.index') }}" class="mr-3 sm:mr-4 p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-900">Venda #{{ $sale->sale_number }}</h1>
                        <p class="text-sm text-gray-500">{{ $sale->sold_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('sales.receipt', $sale) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Imprimir</span>
                    </a>
                    @if($sale->customer?->phone)
                        @php
                            $waPhone = preg_replace('/\D/', '', $sale->customer->phone);
                            if (strlen($waPhone) <= 11) $waPhone = '55' . $waPhone;
                            $waItems = $sale->items->map(fn($i) => "- {$i->product_name} ({$i->quantity}x) {$i->formatted_subtotal}")->implode("\n");
                            $waText = urlencode(
                                "Ola {$sale->customer->name}! Segue o comprovante da sua compra na DG Store:\n\n"
                                . "Venda: #{$sale->sale_number}\n"
                                . "Data: {$sale->sold_at->format('d/m/Y H:i')}\n\n"
                                . "Itens:\n{$waItems}\n\n"
                                . "Total: {$sale->formatted_total}\n"
                                . "Pagamento: {$sale->payment_method->label()}\n\n"
                                . "Obrigado pela preferencia! DG Store"
                            );
                        @endphp
                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank"
                           class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <span>WhatsApp</span>
                        </a>
                    @endif
                    @if($sale->canBeCancelled())
                        <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('Tem certeza que deseja cancelar esta venda? O estoque será devolvido.')">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 sm:px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                Cancelar Venda
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #16a34a;">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #dc2626;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-4 lg:gap-6">
                <!-- Coluna Principal -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Itens da Venda -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Itens da Venda</h3>
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Custo</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Frete</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Venda</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Subtotal</th>
                                        <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Lucro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $item)
                                        @php
                                            $itemProfit = $item->item_profit;
                                            $profitColor = $itemProfit >= 0 ? '#16a34a' : '#dc2626';
                                            $freightAmt = (float) ($item->freight_amount ?? 0);
                                        @endphp
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 1rem 1.5rem;">
                                                <div style="font-weight: 500; color: #111827;">{{ $item->product_name }}</div>
                                                <div style="font-size: 0.75rem; color: #9ca3af;">
                                                    SKU: {{ $item->product_sku }}
                                                    @if($item->supplier_origin)
                                                        <span style="margin-left: 0.5rem; padding: 0.125rem 0.375rem; background: {{ $item->supplier_origin === 'py' ? '#fef3c7' : '#dbeafe' }}; color: {{ $item->supplier_origin === 'py' ? '#92400e' : '#1e40af' }}; font-size: 0.6875rem; font-weight: 600; border-radius: 0.25rem;">
                                                            {{ strtoupper($item->supplier_origin) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: center; color: #6b7280;">
                                                {{ $item->quantity }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: right; color: #6b7280; font-size: 0.875rem;">
                                                {{ $item->formatted_cost_price }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: right; color: #6b7280; font-size: 0.875rem;">
                                                @if($freightAmt > 0)
                                                    R$ {{ number_format($freightAmt, 2, ',', '.') }}
                                                    @if($item->freight_type === 'percentage')
                                                        <div style="font-size: 0.6875rem; color: #9ca3af;">({{ number_format((float) $item->freight_value, 1) }}%)</div>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: right; color: #6b7280;">
                                                {{ $item->formatted_unit_price }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #111827;">
                                                {{ $item->formatted_subtotal }}
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right; font-weight: 600; color: {{ $profitColor }};">
                                                R$ {{ number_format($itemProfit, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Totais -->
                        <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #f9fafb;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                <span style="color: #6b7280;">Subtotal:</span>
                                <span style="color: #111827;">{{ $sale->formatted_subtotal }}</span>
                            </div>
                            @if($sale->discount > 0)
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                <span style="color: #6b7280;">Desconto:</span>
                                <span style="color: #dc2626;">-{{ $sale->formatted_discount }}</span>
                            </div>
                            @endif
                            <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                                <span style="color: #111827;">Total:</span>
                                <span style="color: #16a34a;">{{ $sale->formatted_total }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resumo Financeiro -->
                    @php
                        $totalCost = $sale->total_cost;
                        $revenue = (float) $sale->total;
                        $tradeInVal = (float) $sale->trade_in_value;
                        $profit = $sale->profit;
                        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
                    @endphp
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f0fdf4;">
                            <h3 style="font-weight: 600; color: #166534; display: flex; align-items: center; gap: 0.5rem;">
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Resumo Financeiro
                            </h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <!-- Receita -->
                                <div style="text-align: center; padding: 0.75rem; background: #f0fdf4; border-radius: 0.75rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Receita</div>
                                    <div style="font-size: 1.125rem; font-weight: 700; color: #16a34a;">R$ {{ number_format($revenue, 2, ',', '.') }}</div>
                                </div>
                                <!-- Custo (CMV) -->
                                <div style="text-align: center; padding: 0.75rem; background: #fef2f2; border-radius: 0.75rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Custo (CMV)</div>
                                    <div style="font-size: 1.125rem; font-weight: 700; color: #dc2626;">R$ {{ number_format($totalCost, 2, ',', '.') }}</div>
                                </div>
                                <!-- Lucro -->
                                <div style="text-align: center; padding: 0.75rem; background: {{ $profit >= 0 ? '#f0fdf4' : '#fef2f2' }}; border-radius: 0.75rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Lucro Bruto</div>
                                    <div style="font-size: 1.125rem; font-weight: 700; color: {{ $profit >= 0 ? '#16a34a' : '#dc2626' }};">R$ {{ number_format($profit, 2, ',', '.') }}</div>
                                </div>
                                <!-- Margem -->
                                <div style="text-align: center; padding: 0.75rem; background: #eff6ff; border-radius: 0.75rem;">
                                    <div style="font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Margem</div>
                                    <div style="font-size: 1.125rem; font-weight: 700; color: #2563eb;">{{ number_format($margin, 1, ',', '.') }}%</div>
                                </div>
                            </div>

                            @if($tradeInVal > 0)
                            <div style="margin-top: 1rem; padding: 0.75rem; background: #f5f3ff; border-radius: 0.5rem; border: 1px solid #ddd6fe;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.8125rem; color: #5b21b6; font-weight: 500;">Trade-in recebido (abatido do total):</span>
                                    <span style="font-size: 1rem; font-weight: 700; color: #7c3aed;">R$ {{ number_format($tradeInVal, 2, ',', '.') }}</span>
                                </div>
                                <p style="font-size: 0.6875rem; color: #6b7280; margin-top: 0.25rem;">
                                    O trade-in foi recebido como parte do pagamento. O custo de aquisicao do trade-in nao esta incluido no CMV acima, pois sera contabilizado quando for revendido.
                                </p>
                            </div>
                            @endif

                            @if($profit < 0)
                            <div style="margin-top: 1rem; padding: 0.75rem; background: #fef2f2; border-radius: 0.5rem; border: 1px solid #fecaca;">
                                <p style="font-size: 0.8125rem; color: #dc2626; font-weight: 600;">
                                    Atencao: Esta venda teve prejuizo de R$ {{ number_format(abs($profit), 2, ',', '.') }}. O preco de venda ficou abaixo do custo dos produtos.
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Detalhes do Pagamento (quando há pagamento misto) -->
                    @if($sale->hasMixedPayment() || $sale->hasTradeIn())
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Detalhes do Pagamento</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @if($sale->hasTradeIn())
                                <div style="padding: 1rem; background: #f5f3ff; border-radius: 0.75rem; border: 1px solid #ddd6fe;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <svg style="width: 1.25rem; height: 1.25rem; color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: #7c3aed; text-transform: uppercase;">Trade-in</span>
                                    </div>
                                    <p style="font-size: 1.25rem; font-weight: 700; color: #5b21b6;">{{ $sale->formatted_trade_in_value }}</p>
                                </div>
                                @endif
                                
                                @if($sale->cash_payment > 0)
                                <div style="padding: 1rem; background: #f0fdf4; border-radius: 0.75rem; border: 1px solid #bbf7d0;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <svg style="width: 1.25rem; height: 1.25rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: #16a34a; text-transform: uppercase;">
                                            Entrada ({{ $sale->cash_payment_method_label ?? 'À vista' }})
                                        </span>
                                    </div>
                                    <p style="font-size: 1.25rem; font-weight: 700; color: #166534;">{{ $sale->formatted_cash_payment }}</p>
                                </div>
                                @endif
                                
                                @if($sale->card_payment > 0)
                                <div style="padding: 1rem; background: #eff6ff; border-radius: 0.75rem; border: 1px solid #bfdbfe;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <svg style="width: 1.25rem; height: 1.25rem; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: #2563eb; text-transform: uppercase;">
                                            Cartão ({{ $sale->installments }}x)
                                        </span>
                                    </div>
                                    <p style="font-size: 1.25rem; font-weight: 700; color: #1d4ed8;">{{ $sale->formatted_card_payment }}</p>
                                    @if($sale->installments > 1)
                                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                                        {{ $sale->installments }}x de R$ {{ number_format($sale->card_payment / $sale->installments, 2, ',', '.') }}
                                    </p>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Trade-Ins (detalhes dos aparelhos) -->
                    @if($sale->tradeIns->isNotEmpty())
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #ddd6fe; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #ddd6fe; background: #f5f3ff;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <svg style="width: 1.25rem; height: 1.25rem; color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <h3 style="font-weight: 600; color: #5b21b6;">
                                    {{ $sale->tradeIns->count() > 1 ? 'Aparelhos Recebidos (Trade-in)' : 'Aparelho Recebido (Trade-in)' }}
                                </h3>
                            </div>
                        </div>
                        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                            @foreach($sale->tradeIns as $tradeInItem)
                            <div style="{{ !$loop->last ? 'padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;' : '' }}">
                                @php
                                    $tradeInStatusColors = [
                                        'pending' => ['bg' => '#fefce8', 'color' => '#ca8a04', 'border' => '#fde68a'],
                                        'processed' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
                                        'rejected' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
                                    ];
                                    $tisc = $tradeInStatusColors[$tradeInItem->status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280', 'border' => '#e5e7eb'];
                                @endphp
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    @if($sale->tradeIns->count() > 1)
                                        <span style="font-size: 0.8125rem; font-weight: 600; color: #7c3aed;">Aparelho {{ $loop->iteration }}</span>
                                    @else
                                        <span></span>
                                    @endif
                                    <span style="padding: 0.25rem 0.75rem; background: {{ $tisc['bg'] }}; color: {{ $tisc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; border: 1px solid {{ $tisc['border'] }};">
                                        {{ $tradeInItem->status->label() }}
                                    </span>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                    <div>
                                        <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Aparelho</dt>
                                        <dd style="margin-top: 0.25rem; font-size: 1rem; font-weight: 600; color: #111827;">{{ $tradeInItem->full_name }}</dd>
                                    </div>
                                    <div>
                                        <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Valor</dt>
                                        <dd style="margin-top: 0.25rem; font-size: 1rem; font-weight: 600; color: #7c3aed;">{{ $tradeInItem->formatted_value }}</dd>
                                    </div>
                                    @if($tradeInItem->imei)
                                    <div>
                                        <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">IMEI</dt>
                                        <dd style="margin-top: 0.25rem; font-size: 0.875rem; font-weight: 500; color: #111827; font-family: monospace;">{{ $tradeInItem->imei }}</dd>
                                    </div>
                                    @endif
                                    <div>
                                        <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Condição</dt>
                                        <dd style="margin-top: 0.25rem;">
                                            @php
                                                $conditionColors = [
                                                    'excellent' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                    'good' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                    'fair' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                                    'poor' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                                ];
                                                $cc = $conditionColors[$tradeInItem->condition->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                            @endphp
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $cc['bg'] }}; color: {{ $cc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                                {{ $tradeInItem->condition->label() }}
                                            </span>
                                        </dd>
                                    </div>
                                </div>
                                @if($tradeInItem->notes)
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Observações</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #374151;">{{ $tradeInItem->notes }}</dd>
                                </div>
                                @endif
                                @if($tradeInItem->isPending())
                                <div style="margin-top: 1rem; padding: 0.75rem; background: #fefce8; border-radius: 0.5rem; border: 1px solid #fde68a;">
                                    <p style="font-size: 0.75rem; color: #92400e;">
                                        <strong>Atenção:</strong> Este aparelho ainda não foi cadastrado no estoque. 
                                        <a href="{{ route('stock.trade-ins') }}" style="color: #b45309; text-decoration: underline;">Processar trade-ins pendentes</a>
                                    </p>
                                </div>
                                @endif

                                {{-- Rastreabilidade: se trade-in processado, verificar se ja foi revendido --}}
                                @if($tradeInItem->isProcessed() && $tradeInItem->product_id)
                                    @php
                                        $tradeInProduct = $tradeInItem->product;
                                        $resaleSaleItem = $tradeInProduct
                                            ? \App\Domain\Sale\Models\SaleItem::where('product_id', $tradeInProduct->id)
                                                ->whereHas('sale', fn($q) => $q->where('payment_status', '!=', 'cancelled'))
                                                ->with('sale')
                                                ->first()
                                            : null;
                                        $tradeInCost = (float) $tradeInItem->estimated_value;
                                    @endphp
                                    <div style="margin-top: 1rem; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid {{ $resaleSaleItem ? '#bbf7d0' : '#bfdbfe' }}; background: {{ $resaleSaleItem ? '#f0fdf4' : '#eff6ff' }};">
                                        @if($resaleSaleItem)
                                            @php
                                                $resalePrice = (float) $resaleSaleItem->unit_price * $resaleSaleItem->quantity;
                                                $resaleProfit = $resalePrice - $tradeInCost;
                                            @endphp
                                            <p style="font-size: 0.8125rem; font-weight: 600; color: {{ $resaleProfit >= 0 ? '#166534' : '#dc2626' }};">
                                                Trade-in revendido na venda
                                                <a href="{{ route('sales.show', $resaleSaleItem->sale) }}" style="text-decoration: underline;">
                                                    #{{ $resaleSaleItem->sale->sale_number }}
                                                </a>
                                                por R$ {{ number_format($resalePrice, 2, ',', '.') }}
                                                — Lucro: R$ {{ number_format($resaleProfit, 2, ',', '.') }}
                                            </p>
                                        @else
                                            <p style="font-size: 0.8125rem; color: #1d4ed8;">
                                                Trade-in cadastrado como produto:
                                                <a href="{{ route('products.show', $tradeInProduct) }}" style="text-decoration: underline; font-weight: 600;">
                                                    {{ $tradeInProduct->name }}
                                                </a>
                                                — Valor estimado: R$ {{ number_format($tradeInCost, 2, ',', '.') }}
                                                | Estoque: {{ $tradeInProduct->stock_quantity }} un.
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if($sale->notes)
                    <!-- Observações -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Observações</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <p style="font-size: 0.875rem; color: #374151; white-space: pre-line;">{{ $sale->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Coluna Lateral -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Status -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Status da Venda</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            @php
                                $statusColors = [
                                    'paid' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
                                    'pending' => ['bg' => '#fefce8', 'color' => '#ca8a04', 'border' => '#fde68a'],
                                    'partial' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#bfdbfe'],
                                    'cancelled' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
                                ];
                                $sc = $statusColors[$sale->payment_status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280', 'border' => '#e5e7eb'];
                            @endphp
                            <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
                                <span style="display: inline-block; padding: 0.5rem 1.5rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.875rem; font-weight: 600; border-radius: 9999px; border: 1px solid {{ $sc['border'] }};">
                                    {{ $sale->payment_status->label() }}
                                </span>
                            </div>
                            
                            @if(!$sale->isCancelled())
                            <form method="POST" action="{{ route('sales.update-status', $sale) }}">
                                @csrf
                                @method('PATCH')
                                <div style="display: flex; gap: 0.5rem;">
                                    <select name="payment_status" 
                                            style="flex: 1; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                        <option value="pending" {{ $sale->payment_status->value === 'pending' ? 'selected' : '' }}>Pendente</option>
                                        <option value="paid" {{ $sale->payment_status->value === 'paid' ? 'selected' : '' }}>Pago</option>
                                        <option value="partial" {{ $sale->payment_status->value === 'partial' ? 'selected' : '' }}>Parcial</option>
                                    </select>
                                    <button type="submit" 
                                            style="padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                            onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                        Atualizar
                                    </button>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Informações -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Informações</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <dl style="display: flex; flex-direction: column; gap: 1rem;">
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Data da Venda</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $sale->sold_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Vendedor</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $sale->user?->name }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Forma de Pagamento</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; font-weight: 500; color: #111827;">
                                        {{ $sale->payment_method->label() }}
                                        @if($sale->installments > 1 && !$sale->hasMixedPayment())
                                            <span style="color: #6b7280;">({{ $sale->installments }}x de {{ $sale->formatted_installment_value }})</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <!-- Cliente -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Cliente</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            @if($sale->customer)
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                                    <div style="width: 2.5rem; height: 2.5rem; background: #111827; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: white; font-size: 1rem; font-weight: 600;">{{ strtoupper(substr($sale->customer->name, 0, 1)) }}</span>
                                    </div>
                                    <a href="{{ route('customers.show', $sale->customer) }}" 
                                       style="font-weight: 600; color: #111827; text-decoration: none;"
                                       onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        {{ $sale->customer->name }}
                                    </a>
                                </div>
                                <dl style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div>
                                        <dt style="font-size: 0.75rem; color: #6b7280;">Telefone</dt>
                                        <dd style="font-size: 0.875rem; color: #111827;">{{ $sale->customer->formatted_phone }}</dd>
                                    </div>
                                    @if($sale->customer->email)
                                    <div>
                                        <dt style="font-size: 0.75rem; color: #6b7280;">E-mail</dt>
                                        <dd style="font-size: 0.875rem; color: #111827;">{{ $sale->customer->email }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            @else
                                <p style="font-size: 0.875rem; color: #6b7280; text-align: center;">Cliente não informado</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
