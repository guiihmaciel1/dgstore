<x-app-layout>
    <x-slot name="title">Detalhes da Venda</x-slot>
    @php
        $canSeeFinancials = auth()->user()->canViewFinancials();
        $money = fn ($v) => 'R$ ' . number_format((float) $v, 2, ',', '.');
        $card = 'overflow-hidden rounded-2xl border border-border bg-surface-raised';
        $cardHead = 'border-b border-border bg-surface-overlay px-5 py-3.5 sm:px-6';
        $cardTitle = 'font-semibold text-dg-100';
        $dtClass = 'text-xs font-medium uppercase tracking-wide text-dg-400';
        $pill = 'inline-block whitespace-nowrap rounded-full px-1.5 py-px text-[0.5625rem] font-semibold uppercase';
        $btn = 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-dg-300';
        $statusTone = [
            'paid' => 'border-emerald-500/35 bg-emerald-500/15 text-emerald-400',
            'pending' => 'border-amber-500/35 bg-amber-500/15 text-amber-300',
            'partial' => 'border-sky-500/35 bg-sky-500/15 text-sky-300',
            'cancelled' => 'border-red-500/35 bg-red-500/15 text-red-400',
        ];
        $tradeInTone = [
            'pending' => 'border-amber-500/35 bg-amber-500/15 text-amber-300',
            'processed' => 'border-emerald-500/35 bg-emerald-500/15 text-emerald-400',
            'rejected' => 'border-red-500/35 bg-red-500/15 text-red-400',
        ];
        $conditionTone = [
            'excellent' => 'bg-emerald-500/15 text-emerald-400',
            'good' => 'bg-sky-500/15 text-sky-300',
            'fair' => 'bg-amber-500/15 text-amber-300',
            'poor' => 'bg-red-500/15 text-red-400',
        ];
    @endphp
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('sales.index') }}" class="rounded-lg p-2 text-dg-400 transition-colors hover:bg-surface-overlay hover:text-dg-100" aria-label="Voltar para vendas">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-dg-50 sm:text-2xl">Venda #{{ $sale->sale_number }}</h1>
                        <p class="text-sm text-dg-400">{{ $sale->sold_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                    @if(!$sale->isCancelled())
                        <a href="{{ route('sales.edit', $sale) }}" class="{{ $btn }} border border-border bg-surface-elevated text-dg-100 hover:bg-dg-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Editar
                        </a>
                    @endif
                    <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="{{ $btn }} border border-border bg-surface-elevated text-dg-100 hover:bg-dg-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Recibo
                    </a>
                    <a href="{{ route('sales.signed-receipt', $sale) }}" target="_blank" class="{{ $btn }} border border-border bg-surface-elevated text-dg-100 hover:bg-dg-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        NF
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
                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" class="{{ $btn }} bg-emerald-600 text-white hover:bg-emerald-500">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                    @endif
                    @if($sale->canBeCancelled())
                        <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('Tem certeza que deseja cancelar esta venda? O estoque será devolvido.')" class="contents">
                            @csrf
                            <button type="submit" class="{{ $btn }} border border-red-500/30 bg-red-500/10 text-red-300 hover:bg-red-500/20">Cancelar venda</button>
                        </form>
                    @endif
                    <a href="{{ route('sales.create') }}" class="{{ $btn }} bg-white font-semibold text-dg-950 hover:bg-dg-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nova venda
                    </a>
                </div>
            </div>

            <x-flash class="mb-4" />

            <div class="grid grid-cols-[minmax(0,1fr)] gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] lg:gap-6">
                <!-- Coluna Principal -->
                <div class="flex min-w-0 flex-col gap-6">
                    <!-- Itens da Venda -->
                    <section class="{{ $card }}" x-data="saleItemImages()" aria-labelledby="items-title">
                        <div class="{{ $cardHead }}"><h2 id="items-title" class="{{ $cardTitle }}">Itens da venda</h2></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-xs font-semibold uppercase tracking-wide text-dg-400">
                                    <tr class="border-b border-border">
                                        <th scope="col" class="px-5 py-3 text-left sm:px-6">Produto</th>
                                        <th scope="col" class="px-4 py-3 text-center">Qtd</th>
                                        @if($canSeeFinancials)
                                            <th scope="col" class="whitespace-nowrap px-4 py-3 text-right">Custo</th>
                                            <th scope="col" class="whitespace-nowrap px-4 py-3 text-right">C. financeiro</th>
                                            <th scope="col" class="whitespace-nowrap px-4 py-3 text-right">C. comissão</th>
                                            <th scope="col" class="px-4 py-3 text-right">Frete</th>
                                        @endif
                                        <th scope="col" class="px-4 py-3 text-right">Venda</th>
                                        <th scope="col" class="px-4 py-3 text-right">Subtotal</th>
                                        @if($canSeeFinancials)
                                            <th scope="col" class="px-5 py-3 text-right sm:px-6">Lucro</th>
                                        @endif
                                        <th scope="col" class="px-2 py-3 text-center">Foto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border-subtle">
                                    @foreach($sale->items as $item)
                                        @php
                                            $itemProfit = $item->item_profit;
                                            $freightAmt = (float) ($item->freight_amount ?? 0);
                                            $itemTradeIn = $item->product?->tradeIn;
                                            $snapshot = $item->product_snapshot ?? [];
                                            $itemImei = $snapshot['imei'] ?? null;
                                            $itemSerial = $snapshot['serial_number'] ?? null;
                                            $exchanges = $snapshot['exchanges'] ?? [];
                                        @endphp
                                        <tr>
                                            <td class="min-w-[15rem] px-5 py-4 align-top sm:px-6">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="font-medium text-dg-100">{{ $item->product_name }}</span>
                                                    @if($itemTradeIn)<span class="{{ $pill }} bg-sky-500/15 text-sky-300">Trade-in</span>@endif
                                                    @if($item->isConsignment())<span class="{{ $pill }} bg-amber-500/15 text-amber-300">Consignado</span>@endif
                                                    @if(!empty($snapshot['is_fragrance']))<span class="{{ $pill }} bg-[#c9a96e]/15 text-[#c9a96e]">Perfume</span>@endif
                                                    @if(!empty($exchanges))<span class="{{ $pill }} bg-pink-500/15 text-pink-300" title="Item já foi trocado com outro lojista">Trocado</span>@endif
                                                </div>
                                                <p class="text-xs text-dg-500">
                                                    SKU: {{ $item->product_sku }}
                                                    @if($item->supplier_origin)
                                                        <span class="ml-2 rounded px-1.5 py-0.5 text-[0.6875rem] font-semibold {{ $item->supplier_origin === 'py' ? 'bg-amber-500/15 text-amber-300' : 'bg-sky-500/15 text-sky-300' }}">{{ strtoupper($item->supplier_origin) }}</span>
                                                    @endif
                                                    @if($itemTradeIn && $itemTradeIn->sale)
                                                        <span class="ml-1 text-[0.6875rem] text-dg-400">
                                                            Recebido via <a href="{{ route('sales.show', $itemTradeIn->sale) }}" class="font-medium text-sky-300 hover:underline">#{{ $itemTradeIn->sale->sale_number }}</a>
                                                        </span>
                                                    @endif
                                                </p>
                                                @if($itemImei || $itemSerial)
                                                    <div class="mt-2 flex flex-wrap gap-3 text-xs">
                                                        @if($itemImei)<span><strong class="text-dg-300">IMEI:</strong> <span class="font-mono text-dg-100">{{ $itemImei }}</span></span>@endif
                                                        @if($itemSerial)<span><strong class="text-dg-300">Serial:</strong> <span class="font-mono text-dg-100">{{ $itemSerial }}</span></span>@endif
                                                    </div>
                                                @endif
                                                @if(!empty($exchanges))
                                                    <div class="mt-2 rounded-md border border-pink-500/30 bg-pink-500/10 px-2.5 py-2">
                                                        <p class="mb-1 text-[0.6875rem] font-bold uppercase tracking-wide text-pink-300">Histórico de trocas</p>
                                                        @foreach($exchanges as $exch)
                                                            <p class="mt-0.5 text-xs text-dg-400">
                                                                @if(!empty($exch['exchanged_at']))<span class="font-medium text-pink-300">{{ \Carbon\Carbon::parse($exch['exchanged_at'])->format('d/m/Y') }}</span>@endif
                                                                · {{ $exch['partner_name'] ?? 'Lojista' }}
                                                                @if(!empty($exch['old_imei']))
                                                                    · IMEI antigo: <span class="font-mono">{{ $exch['old_imei'] }}</span>
                                                                @elseif(!empty($exch['old_serial_number']))
                                                                    · Serial antigo: <span class="font-mono">{{ $exch['old_serial_number'] }}</span>
                                                                @endif
                                                            </p>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center tabular-nums text-dg-300">{{ $item->quantity }}</td>
                                            @if($canSeeFinancials)
                                                <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-dg-400">{{ $item->formatted_cost_price }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-dg-400">{{ $item->formatted_financial_cost }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-dg-400">{{ $item->formatted_commission_cost }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-dg-400">
                                                    @if($freightAmt > 0)
                                                        {{ $money($freightAmt) }}
                                                        @if($item->freight_type === 'percentage')
                                                            <span class="block text-[0.6875rem] text-dg-500">({{ number_format((float) $item->freight_value, 1) }}%)</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-dg-300">{{ $item->formatted_unit_price }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold tabular-nums text-dg-50">{{ $item->formatted_subtotal }}</td>
                                            @if($canSeeFinancials)
                                                <td class="whitespace-nowrap px-5 py-3 text-right font-semibold tabular-nums sm:px-6 {{ $itemProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $money($itemProfit) }}</td>
                                            @endif
                                            <td class="px-2 py-2 text-center align-middle">
                                                <div x-data="{ itemId: '{{ $item->id }}' }">
                                                    @if($item->images->isNotEmpty())
                                                        @php $img = $item->images->first(); @endphp
                                                        <div class="relative inline-block">
                                                            <a href="{{ $img->url }}" target="_blank">
                                                                <img src="{{ $img->url }}" alt="{{ $img->original_name }}" class="h-10 w-10 cursor-pointer rounded-md border-2 border-border-strong object-cover transition-colors hover:border-dg-100">
                                                            </a>
                                                            <button type="button" @click="deleteImage('{{ $img->id }}', $el.closest('td'))" title="Remover foto" aria-label="Remover foto"
                                                                    class="absolute -right-1.5 -top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold leading-none text-white hover:bg-red-500">&times;</button>
                                                        </div>
                                                    @else
                                                        <label title="Adicionar foto" class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-md border-2 border-dashed border-border-strong text-dg-500 transition-colors hover:border-dg-100 hover:text-dg-100">
                                                            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            <span class="sr-only">Adicionar foto</span>
                                                            <input type="file" accept="image/jpeg,image/png,image/webp" @change="uploadImage(itemId, $event, $el.closest('td'))" class="hidden">
                                                        </label>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Totais -->
                        <dl class="border-t border-border bg-surface-overlay px-5 py-4 text-sm sm:px-6">
                            <div class="mb-2 flex justify-between"><dt class="text-dg-400">Subtotal:</dt><dd class="tabular-nums text-dg-100">{{ $sale->formatted_subtotal }}</dd></div>
                            @if($sale->discount > 0)
                                <div class="mb-2 flex justify-between"><dt class="text-dg-400">Desconto:</dt><dd class="tabular-nums text-red-300">-{{ $sale->formatted_discount }}</dd></div>
                            @endif
                            <div class="flex justify-between border-t border-border pt-3 text-xl font-bold"><dt class="text-dg-100">Total:</dt><dd class="tabular-nums text-emerald-400">{{ $sale->formatted_total }}</dd></div>
                        </dl>
                    </section>

                    <!-- Resumo Financeiro -->
                    @php
                        $totalCost = $sale->total_cost;
                        $revenue = (float) $sale->total;
                        $tradeInVal = (float) $sale->trade_in_value;
                        $profit = $sale->profit;
                        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
                        $commissionTotal = $sale->commissions->sum('commission_amount');
                        $netProfit = $profit - $commissionTotal;
                        $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
                        $tile = 'rounded-xl p-3 text-center';
                        $tileLabel = 'mb-1 text-[0.6875rem] font-semibold uppercase tracking-wide text-dg-400';
                        $tileValue = 'text-lg font-bold tabular-nums';
                    @endphp
                    @if($canSeeFinancials)
                        <section class="{{ $card }}" aria-labelledby="fin-title">
                            <div class="border-b border-border bg-emerald-500/10 px-5 py-3.5 sm:px-6">
                                <h2 id="fin-title" class="flex items-center gap-2 font-semibold text-emerald-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Resumo financeiro
                                </h2>
                            </div>
                            <div class="p-5 sm:p-6">
                                <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    <div class="{{ $tile }} bg-emerald-500/10"><dt class="{{ $tileLabel }}">Receita</dt><dd class="{{ $tileValue }} text-emerald-400">{{ $money($revenue) }}</dd></div>
                                    <div class="{{ $tile }} bg-red-500/10"><dt class="{{ $tileLabel }}">Custo (CMV)</dt><dd class="{{ $tileValue }} text-red-300">{{ $money($totalCost) }}</dd></div>
                                    <div class="{{ $tile }} {{ $profit >= 0 ? 'bg-emerald-500/15' : 'bg-red-500/15' }}">
                                        <dt class="{{ $tileLabel }}">Lucro bruto</dt>
                                        <dd class="{{ $tileValue }} {{ $profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $money($profit) }}</dd>
                                        <dd class="mt-0.5 text-[0.625rem] text-dg-400">{{ number_format($margin, 1, ',', '.') }}% margem</dd>
                                    </div>
                                </dl>

                                @if($commissionTotal > 0)
                                    <dl class="mt-3 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                        <div class="{{ $tile }} bg-violet-500/10"><dt class="{{ $tileLabel }}">Comissão</dt><dd class="{{ $tileValue }} text-violet-300">− {{ $money($commissionTotal) }}</dd></div>
                                        <div class="{{ $tile }} border-2 {{ $netProfit >= 0 ? 'border-emerald-500/35 bg-emerald-500/15' : 'border-red-500/35 bg-red-500/15' }}">
                                            <dt class="{{ $tileLabel }}">Lucro líquido</dt>
                                            <dd class="{{ $tileValue }} {{ $netProfit >= 0 ? 'text-emerald-300' : 'text-red-400' }}">{{ $money($netProfit) }}</dd>
                                            <dd class="mt-0.5 text-[0.625rem] text-dg-400">{{ number_format($netMargin, 1, ',', '.') }}% líquido</dd>
                                        </div>
                                        <div class="{{ $tile }} bg-sky-500/10">
                                            <dt class="{{ $tileLabel }}">Impacto comissão</dt>
                                            <dd class="{{ $tileValue }} text-sky-300">{{ number_format($margin - $netMargin, 1, ',', '.') }}%</dd>
                                            <dd class="mt-0.5 text-[0.625rem] text-dg-400">da receita</dd>
                                        </div>
                                    </dl>
                                @endif

                                @if($tradeInVal > 0)
                                    <div class="mt-4 rounded-lg border border-violet-500/35 bg-violet-500/10 p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-[0.8125rem] font-medium text-violet-300">Trade-in recebido (abatido do total):</span>
                                            <span class="font-bold tabular-nums text-violet-300">{{ $money($tradeInVal) }}</span>
                                        </div>
                                        <p class="mt-1 text-[0.6875rem] text-dg-400">O trade-in foi recebido como parte do pagamento. O custo de aquisição do trade-in não está incluído no CMV acima, pois será contabilizado quando for revendido.</p>
                                    </div>
                                @endif

                                @if($profit < 0)
                                    <p class="mt-4 rounded-lg border border-red-500/35 bg-red-500/10 p-3 text-[0.8125rem] font-semibold text-red-300">
                                        Atenção: esta venda teve prejuízo de {{ $money(abs($profit)) }}. O preço de venda ficou abaixo do custo dos produtos.
                                    </p>
                                @endif
                            </div>
                        </section>
                    @endif

                    <!-- Detalhes do Pagamento -->
                    @if($sale->hasMixedPayment() || $sale->hasTradeIn())
                        <section class="{{ $card }}" aria-labelledby="pay-title">
                            <div class="{{ $cardHead }}"><h2 id="pay-title" class="{{ $cardTitle }}">Detalhes do pagamento</h2></div>
                            <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 sm:p-6 lg:grid-cols-3">
                                @foreach(array_filter([
                                    $sale->hasTradeIn() ? ['label' => 'Trade-in', 'value' => $sale->formatted_trade_in_value, 'tone' => 'border-violet-500/35 bg-violet-500/10 text-violet-300', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'] : null,
                                    $sale->cash_payment > 0 ? ['label' => 'Dinheiro', 'value' => $sale->formatted_cash_payment, 'tone' => 'border-emerald-500/35 bg-emerald-500/10 text-emerald-300', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'] : null,
                                    $sale->pix_payment > 0 ? ['label' => 'PIX', 'value' => $sale->formatted_pix_payment, 'tone' => 'border-teal-500/35 bg-teal-500/10 text-teal-300', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'] : null,
                                    $sale->card_payment > 0 ? ['label' => 'Cartão (' . $sale->installments . 'x)', 'value' => $sale->formatted_card_payment, 'tone' => 'border-sky-500/35 bg-sky-500/10 text-sky-300', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'card' => true] : null,
                                ]) as $part)
                                    <div class="rounded-xl border p-4 {{ $part['tone'] }}">
                                        <p class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $part['icon'] }}"/></svg>
                                            {{ $part['label'] }}
                                        </p>
                                        <p class="text-xl font-bold tabular-nums">{{ $part['value'] }}</p>
                                        @if(!empty($part['card']) && $sale->installments > 1)
                                            <p class="mt-1 text-xs text-dg-400">{{ $sale->installments }}x de {{ $money($sale->card_payment / $sale->installments) }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <!-- Trade-Ins -->
                    @if($sale->tradeIns->isNotEmpty())
                        <section class="overflow-hidden rounded-2xl border border-violet-500/35 bg-surface-raised" aria-labelledby="ti-title">
                            <div class="border-b border-violet-500/35 bg-violet-500/10 px-5 py-3.5 sm:px-6">
                                <h2 id="ti-title" class="flex items-center gap-2 font-semibold text-violet-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    {{ $sale->tradeIns->count() > 1 ? 'Aparelhos recebidos (trade-in)' : 'Aparelho recebido (trade-in)' }}
                                </h2>
                            </div>
                            <div class="divide-y divide-border p-5 sm:p-6">
                                @foreach($sale->tradeIns as $tradeInItem)
                                    <div class="{{ $loop->first ? '' : 'pt-6' }} {{ $loop->last ? '' : 'pb-6' }}">
                                        <div class="mb-3 flex items-center justify-between">
                                            <span class="text-[0.8125rem] font-semibold text-violet-300">{{ $sale->tradeIns->count() > 1 ? 'Aparelho ' . $loop->iteration : '' }}</span>
                                            <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $tradeInTone[$tradeInItem->status->value] ?? 'border-border bg-surface-overlay text-dg-400' }}">{{ $tradeInItem->status->label() }}</span>
                                        </div>
                                        <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                                            <div class="col-span-2">
                                                <dt class="{{ $dtClass }}">Aparelho</dt>
                                                <dd class="mt-1 font-semibold text-dg-100">
                                                    {{ $tradeInItem->full_name }}
                                                    @if($tradeInItem->storage || $tradeInItem->color)
                                                        <span class="mt-1 flex gap-1.5">
                                                            @if($tradeInItem->storage)<span class="rounded-full bg-surface-elevated px-2 py-0.5 text-[0.6875rem] font-normal text-dg-300">{{ $tradeInItem->storage }}</span>@endif
                                                            @if($tradeInItem->color)<span class="rounded-full bg-surface-elevated px-2 py-0.5 text-[0.6875rem] font-normal text-dg-300">{{ $tradeInItem->color }}</span>@endif
                                                        </span>
                                                    @endif
                                                </dd>
                                            </div>
                                            <div>
                                                <dt class="{{ $dtClass }}">Condição</dt>
                                                <dd class="mt-1"><span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $conditionTone[$tradeInItem->condition->value] ?? 'bg-surface-overlay text-dg-400' }}">{{ $tradeInItem->condition->label() }}</span></dd>
                                            </div>
                                            @if($tradeInItem->imei)
                                                <div><dt class="{{ $dtClass }}">IMEI</dt><dd class="mt-1 font-mono text-sm font-medium text-dg-100">{{ $tradeInItem->imei }}</dd></div>
                                            @endif
                                            <div><dt class="{{ $dtClass }}">Valor negociado</dt><dd class="mt-1 font-semibold tabular-nums text-violet-300">{{ $tradeInItem->formatted_value }}</dd></div>
                                            @if($tradeInItem->cost_price)
                                                <div><dt class="{{ $dtClass }}">Preço custo</dt><dd class="mt-1 text-sm font-semibold tabular-nums text-dg-300">{{ $money($tradeInItem->cost_price) }}</dd></div>
                                            @endif
                                            @if($tradeInItem->sale_price)
                                                <div><dt class="{{ $dtClass }}">Preço final</dt><dd class="mt-1 text-sm font-semibold tabular-nums text-dg-300">{{ $money($tradeInItem->sale_price) }}</dd></div>
                                            @endif
                                            @if($tradeInItem->resale_price)
                                                <div><dt class="{{ $dtClass }}">Preço repasse</dt><dd class="mt-1 text-sm font-semibold tabular-nums text-dg-300">{{ $money($tradeInItem->resale_price) }}</dd></div>
                                            @endif
                                        </dl>
                                        @if($tradeInItem->battery_health || $tradeInItem->has_box || $tradeInItem->has_cable)
                                            <div class="mt-4 flex flex-wrap gap-3 border-t border-border pt-4 text-xs font-semibold">
                                                @if($tradeInItem->battery_health)
                                                    <span class="rounded-full bg-surface-elevated px-2.5 py-1 text-dg-300">🔋 {{ $tradeInItem->battery_health }}%</span>
                                                @endif
                                                <span class="rounded-full px-2.5 py-1 {{ $tradeInItem->has_box ? 'bg-emerald-500/15 text-emerald-300' : 'bg-red-500/15 text-red-300' }}">{{ $tradeInItem->has_box ? '✓' : '✗' }} Caixa</span>
                                                <span class="rounded-full px-2.5 py-1 {{ $tradeInItem->has_cable ? 'bg-emerald-500/15 text-emerald-300' : 'bg-red-500/15 text-red-300' }}">{{ $tradeInItem->has_cable ? '✓' : '✗' }} Cabo</span>
                                            </div>
                                        @endif
                                        @if($tradeInItem->notes)
                                            <dl class="mt-4 border-t border-border pt-4">
                                                <dt class="{{ $dtClass }}">Observações</dt>
                                                <dd class="mt-1 text-sm text-dg-300">{{ $tradeInItem->notes }}</dd>
                                            </dl>
                                        @endif

                                        {{-- Rastreio: produto criado a partir do trade-in --}}
                                        @if($tradeInItem->product)
                                            @php $productSale = $tradeInItem->product->saleItems->first()?->sale; @endphp
                                            <div class="mt-4 rounded-lg border border-sky-500/35 bg-sky-500/10 p-3">
                                                <p class="mb-2 flex items-center gap-2 text-xs font-semibold text-sky-300">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                                    Produto cadastrado no estoque
                                                </p>
                                                <a href="{{ route('products.show', $tradeInItem->product) }}" class="text-sm font-medium text-sky-300 hover:underline">
                                                    {{ $tradeInItem->product->full_name }} ({{ $tradeInItem->product->sku }})
                                                </a>
                                                @if($productSale)
                                                    <p class="mt-2 flex items-center gap-2 border-t border-sky-500/35 pt-2 text-xs font-medium text-emerald-400">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        <span>Vendido na venda <a href="{{ route('sales.show', $productSale) }}" class="font-semibold text-sky-300 hover:underline">#{{ $productSale->sale_number }}</a>@if($productSale->customer) para {{ $productSale->customer->name }}@endif</span>
                                                    </p>
                                                @elseif($tradeInItem->product->stock_quantity > 0)
                                                    <p class="mt-2 text-xs text-amber-300">Em estoque ({{ $tradeInItem->product->stock_quantity }} unidade(s))</p>
                                                @endif
                                            </div>
                                        @endif

                                        @if($tradeInItem->isPending())
                                            <p class="mt-4 rounded-lg border border-amber-500/35 bg-amber-500/10 p-3 text-xs text-amber-300">
                                                <strong>Atenção:</strong> este aparelho ainda não foi cadastrado no estoque.
                                                <a href="{{ route('stock.trade-ins') }}" class="text-amber-200 underline">Processar trade-ins pendentes</a>
                                            </p>
                                        @endif

                                        {{-- Rastreabilidade: se trade-in processado, verificar se já foi revendido --}}
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
                                            <div class="mt-4 rounded-lg border p-3 text-[0.8125rem] {{ $resaleSaleItem ? 'border-emerald-500/35 bg-emerald-500/15' : 'border-sky-500/35 bg-sky-500/15' }}">
                                                @if($resaleSaleItem)
                                                    @php
                                                        $resalePrice = (float) $resaleSaleItem->unit_price * $resaleSaleItem->quantity;
                                                        $resaleProfit = $resalePrice - $tradeInCost;
                                                    @endphp
                                                    <p class="font-semibold {{ $resaleProfit >= 0 ? 'text-emerald-300' : 'text-red-400' }}">
                                                        Trade-in revendido na venda
                                                        <a href="{{ route('sales.show', $resaleSaleItem->sale) }}" class="underline">#{{ $resaleSaleItem->sale->sale_number }}</a>
                                                        por {{ $money($resalePrice) }} · Lucro: {{ $money($resaleProfit) }}
                                                    </p>
                                                @elseif($tradeInProduct)
                                                    <p class="text-sky-300">
                                                        Trade-in cadastrado como produto:
                                                        <a href="{{ route('products.show', $tradeInProduct) }}" class="font-semibold underline">{{ $tradeInProduct->name }}</a>
                                                        · Valor estimado: {{ $money($tradeInCost) }} | Estoque: {{ $tradeInProduct->stock_quantity }} un.
                                                    </p>
                                                @else
                                                    <p class="text-dg-400">Trade-in processado · Produto removido do estoque · Valor estimado: {{ $money($tradeInCost) }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($sale->notes)
                        <section class="{{ $card }}" aria-labelledby="notes-title">
                            <div class="{{ $cardHead }}"><h2 id="notes-title" class="{{ $cardTitle }}">Observações</h2></div>
                            <p class="whitespace-pre-line p-5 text-sm text-dg-300 sm:p-6">{{ $sale->notes }}</p>
                        </section>
                    @endif
                </div>

                <!-- Coluna Lateral -->
                <div class="flex flex-col gap-6">
                    <!-- Status -->
                    <section class="{{ $card }}" aria-labelledby="status-title">
                        <div class="{{ $cardHead }}"><h2 id="status-title" class="{{ $cardTitle }}">Status da venda</h2></div>
                        <div class="p-5 sm:p-6">
                            <div class="mb-4 flex justify-center">
                                <span class="inline-block rounded-full border px-6 py-2 text-sm font-semibold {{ $statusTone[$sale->payment_status->value] ?? 'border-border bg-surface-overlay text-dg-400' }}">{{ $sale->payment_status->label() }}</span>
                            </div>
                            @if(!$sale->isCancelled())
                                <form method="POST" action="{{ route('sales.update-status', $sale) }}" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label for="pay-status" class="sr-only">Status do pagamento</label>
                                    <select id="pay-status" name="payment_status" class="flex-1 rounded-lg border border-border-strong bg-surface-overlay px-3 py-2 text-sm text-dg-100 focus:border-dg-400 focus:ring-2 focus:ring-dg-400/20">
                                        <option value="pending" {{ $sale->payment_status->value === 'pending' ? 'selected' : '' }}>Pendente</option>
                                        <option value="paid" {{ $sale->payment_status->value === 'paid' ? 'selected' : '' }}>Pago</option>
                                        <option value="partial" {{ $sale->payment_status->value === 'partial' ? 'selected' : '' }}>Parcial</option>
                                    </select>
                                    <button type="submit" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-dg-950 transition-colors hover:bg-dg-100">Atualizar</button>
                                </form>
                            @endif
                        </div>
                    </section>

                    <!-- Informações -->
                    <section class="{{ $card }}" aria-labelledby="info-title">
                        <div class="{{ $cardHead }}"><h2 id="info-title" class="{{ $cardTitle }}">Informações</h2></div>
                        <dl class="flex flex-col gap-4 p-5 sm:p-6">
                            <div><dt class="{{ $dtClass }}">Data da venda</dt><dd class="mt-1 text-sm font-medium tabular-nums text-dg-100">{{ $sale->sold_at->format('d/m/Y H:i') }}</dd></div>
                            <div><dt class="{{ $dtClass }}">Vendedor</dt><dd class="mt-1 text-sm font-medium text-dg-100">{{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name }}</dd></div>
                            <div>
                                <dt class="{{ $dtClass }}">Tipo</dt>
                                <dd class="mt-1">
                                    @if($sale->sale_type)
                                        <span class="inline-block rounded-md px-2.5 py-1 text-[0.8125rem] font-semibold {{ $sale->sale_type->value === 'repasse' ? 'bg-violet-500/10 text-violet-300' : 'bg-sky-500/10 text-sky-300' }}">{{ $sale->sale_type->label() }}</span>
                                    @else
                                        <span class="text-sm text-dg-500">Não informado</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="{{ $dtClass }}">Forma de pagamento</dt>
                                <dd class="mt-1 text-sm font-medium text-dg-100">
                                    {{ $sale->payment_method->label() }}
                                    @if($sale->installments > 1 && !$sale->hasMixedPayment())
                                        <span class="text-dg-400">({{ $sale->installments }}x de {{ $sale->formatted_installment_value }})</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Cliente -->
                    <section class="{{ $card }}" aria-labelledby="customer-title">
                        <div class="{{ $cardHead }}"><h2 id="customer-title" class="{{ $cardTitle }}">Cliente</h2></div>
                        <div class="p-5 sm:p-6">
                            @if($sale->customer)
                                <div class="mb-4 flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-surface-elevated font-semibold text-dg-100 ring-1 ring-inset ring-white/10" aria-hidden="true">{{ strtoupper(substr($sale->customer->name, 0, 1)) }}</span>
                                    <a href="{{ route('customers.show', $sale->customer) }}" class="font-semibold text-dg-100 hover:underline">{{ $sale->customer->name }}</a>
                                </div>
                                <dl class="flex flex-col gap-2">
                                    <div><dt class="text-xs text-dg-400">Telefone</dt><dd class="text-sm text-dg-100">{{ $sale->customer->formatted_phone }}</dd></div>
                                    @if($sale->customer->instagram)
                                        <div><dt class="text-xs text-dg-400">Instagram</dt><dd class="text-sm text-dg-100">{{ $sale->customer->formatted_instagram }}</dd></div>
                                    @endif
                                </dl>
                            @else
                                <div class="text-center">
                                    <p class="mb-3 text-sm text-dg-400">Cliente não informado</p>
                                    @if(!$sale->isCancelled())
                                        <a href="{{ route('sales.edit', $sale) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-[0.8125rem] font-semibold text-dg-950 transition-colors hover:bg-dg-100">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Adicionar cliente
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script>
    function saleItemImages() {
        return {
            async uploadImage(saleItemId, event, tdEl) {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('sale_item_id', saleItemId);
                formData.append('image', file);

                try {
                    const res = await fetch('{{ route("sale-item-images.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });
                    const json = await res.json();
                    if (json.success) {
                        window.location.reload();
                    } else {
                        alert(json.message || 'Erro ao enviar imagem.');
                    }
                } catch (e) {
                    alert('Erro de conexão ao enviar imagem.');
                } finally {
                    event.target.value = '';
                }
            },

            async deleteImage(imageId, tdEl) {
                if (!confirm('Remover esta foto?')) return;

                try {
                    const res = await fetch('/sales/item-images/' + imageId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const json = await res.json();
                    if (json.success) {
                        window.location.reload();
                    }
                } catch (e) {
                    alert('Erro ao remover imagem.');
                }
            },
        };
    }
    </script>
</x-app-layout>
