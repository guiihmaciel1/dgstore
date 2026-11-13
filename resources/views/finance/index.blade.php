<x-app-layout>
    <x-slot name="title">Financeiro</x-slot>
    @php
        $prevMonth = $referenceDate->copy()->subMonth();
        $nextMonth = $referenceDate->copy()->addMonth();
        $canGoNext = !$isCurrentMonth;
        $money = fn ($v) => 'R$ ' . number_format((float) $v, 2, ',', '.');
        $signTone = fn ($v) => $v >= 0 ? 'text-emerald-400' : 'text-red-400';
        $kicker = 'text-[0.6875rem] font-semibold uppercase tracking-wide text-dg-400';
        $miniLabel = 'text-[0.625rem] font-semibold uppercase tracking-wide text-dg-400';
        $arrow = 'flex h-9 w-9 items-center justify-center rounded-lg bg-surface-elevated text-dg-300 transition-colors hover:bg-dg-800 hover:text-dg-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-dg-300';
    @endphp
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            <x-page-header title="Painel financeiro" subtitle="Visão geral das finanças da empresa">
                <x-slot name="actions">
                    <a href="{{ route('finance.payables') }}" class="dg-btn-secondary">Contas a pagar</a>
                    <a href="{{ route('finance.receivables') }}" class="dg-btn-secondary">Contas a receber</a>
                </x-slot>
            </x-page-header>

            <!-- Navegação de Mês -->
            <nav class="mb-6 flex items-center justify-center gap-4 rounded-xl border border-border bg-surface-raised px-5 py-2.5" aria-label="Mês">
                <a href="{{ route('finance.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="{{ $arrow }}" aria-label="Mês anterior">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="min-w-40 text-center">
                    <p class="text-base font-bold capitalize text-dg-50">{{ $referenceDate->translatedFormat('F Y') }}</p>
                    @if(!$isCurrentMonth)
                        <a href="{{ route('finance.index') }}" class="text-[0.6875rem] font-medium text-sky-300 hover:text-sky-200">Voltar ao mês atual</a>
                    @endif
                </div>
                @if($canGoNext)
                    <a href="{{ route('finance.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="{{ $arrow }}" aria-label="Próximo mês">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-overlay text-dg-600" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </nav>

            <!-- Cards Principais -->
            <dl class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="col-span-2 rounded-xl border border-border-strong bg-surface-elevated p-5 lg:col-span-1">
                    <dt class="{{ $kicker }}">Saldo total</dt>
                    <dd class="mt-1 text-xl font-extrabold tabular-nums sm:text-2xl text-dg-50">{{ $money($totalBalance) }}</dd>
                    <dd class="mt-1 text-xs text-dg-500">{{ $accounts->count() }} {{ $accounts->count() === 1 ? 'carteira' : 'carteiras' }}</dd>
                </div>
                <div class="dg-card-flat p-5">
                    <dt class="{{ $kicker }}">Receitas do mês</dt>
                    <dd class="mt-1 text-xl font-extrabold tabular-nums sm:text-2xl text-emerald-400">{{ $money($monthIncome) }}</dd>
                </div>
                <div class="dg-card-flat p-5">
                    <dt class="{{ $kicker }}">Despesas pagas</dt>
                    <dd class="mt-1 text-xl font-extrabold tabular-nums sm:text-2xl text-red-300">{{ $money($monthExpensePaid) }}</dd>
                    <dd class="mt-1 text-[0.6875rem] text-dg-500">Saídas de caixa no mês</dd>
                </div>
                <div class="rounded-xl border bg-surface-raised p-5 {{ $monthProfit >= 0 ? 'border-emerald-500/35' : 'border-red-500/35' }}">
                    <dt class="{{ $kicker }}">Lucro do mês</dt>
                    <dd class="mt-1 text-xl font-extrabold tabular-nums sm:text-2xl {{ $signTone($monthProfit) }}">{{ $money($monthProfit) }}</dd>
                </div>
            </dl>

            <!-- Detalhamento de Despesas -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="dg-card-flat flex items-center gap-3.5 px-5 py-4">
                    <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-red-500/10 text-red-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </span>
                    <div>
                        <p class="{{ $kicker }}">Despesas totais do mês</p>
                        <p class="text-xl font-extrabold tabular-nums text-red-300">{{ $money($monthExpenseTotal) }}</p>
                        @if($monthExpensePending > 0)
                            <p class="text-[0.6875rem] font-medium text-amber-400">{{ $money($monthExpensePending) }} pendente</p>
                        @else
                            <p class="text-[0.6875rem] font-medium text-emerald-400">Tudo pago</p>
                        @endif
                    </div>
                </div>

                <div class="dg-card-flat flex items-center gap-3.5 px-5 py-4">
                    <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-orange-500/10 text-orange-400" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <div>
                        <p class="{{ $kicker }}">Despesas próx. mês</p>
                        <p class="text-xl font-extrabold tabular-nums text-orange-400">{{ $money($nextMonthExpenseTotal) }}</p>
                        @if($nextMonthExpensePending > 0)
                            <p class="text-[0.6875rem] font-medium text-amber-400">{{ $money($nextMonthExpensePending) }} pendente</p>
                        @elseif($nextMonthExpenseTotal > 0)
                            <p class="text-[0.6875rem] font-medium text-emerald-400">Tudo pago</p>
                        @else
                            <p class="text-[0.6875rem] text-dg-500">Nenhuma despesa lançada</p>
                        @endif
                    </div>
                </div>

                <a href="{{ route('finance.payables') }}" class="flex items-center gap-3.5 rounded-xl border border-border bg-surface-raised px-5 py-4 transition-colors hover:border-border-strong hover:bg-surface-overlay">
                    <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                    <div>
                        <p class="{{ $kicker }}">Resumo</p>
                        <p class="text-[0.8125rem] font-semibold text-dg-100">
                            @if($monthExpensePending > 0)
                                {{ number_format(($monthExpensePaid / max($monthExpenseTotal, 1)) * 100, 0) }}% das despesas pagas
                            @else
                                100% das despesas pagas
                            @endif
                        </p>
                        <p class="text-[0.6875rem] font-medium text-sky-300">Ver contas a pagar →</p>
                    </div>
                </a>
            </div>

            <!-- Vendas do Mês -->
            @if($salesData['salesCount'] > 0)
                <section class="dg-card-flat mb-6 p-5" aria-labelledby="sales-title">
                    <div class="mb-3 flex items-center gap-2">
                        <h2 id="sales-title" class="text-[0.9375rem] font-semibold text-dg-100">Vendas do mês</h2>
                        <span class="rounded-full bg-surface-elevated px-2 py-0.5 text-[0.6875rem] font-semibold text-dg-400">{{ $salesData['salesCount'] }} {{ $salesData['salesCount'] === 1 ? 'venda' : 'vendas' }}</span>
                    </div>
                    <dl class="grid grid-cols-2 gap-4 sm:grid-cols-5">
                        <div>
                            <dt class="{{ $miniLabel }}">Faturamento</dt>
                            <dd class="text-lg font-extrabold tabular-nums text-dg-100">{{ $money($salesData['salesRevenue']) }}</dd>
                        </div>
                        <div>
                            <dt class="{{ $miniLabel }}">Custo (CMV)</dt>
                            <dd class="text-lg font-extrabold tabular-nums text-red-300">{{ $money($salesData['salesCost']) }}</dd>
                        </div>
                        <div>
                            <dt class="{{ $miniLabel }}">Lucro das vendas</dt>
                            <dd class="text-lg font-extrabold tabular-nums {{ $signTone($salesData['salesProfit']) }}">{{ $money($salesData['salesProfit']) }}</dd>
                        </div>
                        <div>
                            <dt class="{{ $miniLabel }}">Margem</dt>
                            <dd class="text-lg font-extrabold tabular-nums {{ $signTone($salesData['salesMargin']) }}">{{ $salesData['salesMargin'] }}%</dd>
                        </div>
                        @if(($salesData['tradeInTotal'] ?? 0) > 0)
                            <div>
                                <dt class="{{ $miniLabel }}">Trade-ins recebidos</dt>
                                <dd class="text-lg font-extrabold tabular-nums text-violet-300">{{ $money($salesData['tradeInTotal']) }}</dd>
                                <dd class="text-[0.6875rem] text-dg-400">Abatidos nas vendas</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            @endif

            <!-- Inventário de Seminovos -->
            @if($inventoryData['totalItems'] > 0)
                <section class="dg-card-flat mb-6 p-5" aria-labelledby="inv-title">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <h2 id="inv-title" class="text-[0.9375rem] font-semibold text-dg-100">Inventário de seminovos</h2>
                            <span class="rounded-full bg-surface-elevated px-2 py-0.5 text-[0.6875rem] font-semibold text-dg-400">{{ $inventoryData['totalItems'] }} {{ $inventoryData['totalItems'] === 1 ? 'item' : 'itens' }}</span>
                        </div>
                        @if($inventoryData['withoutPrice'] > 0)
                            <div x-data="{ showMissing: false }" class="relative">
                                <button type="button" @click="showMissing = !showMissing" :aria-expanded="showMissing"
                                        class="inline-flex items-center gap-1 rounded-full border border-amber-500/40 bg-amber-500/15 px-2 py-0.5 text-[0.6875rem] font-semibold text-amber-300 transition-colors hover:bg-amber-500/25">
                                    {{ $inventoryData['withoutPrice'] }} sem preço de venda
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="showMissing" x-cloak @click.away="showMissing = false" x-transition
                                     class="absolute right-0 top-full z-50 mt-1.5 min-w-[280px] overflow-hidden rounded-lg border border-border-strong bg-surface-overlay shadow-xl shadow-black/50">
                                    <p class="border-b border-border bg-amber-500/10 px-3 py-2.5 text-[0.6875rem] font-semibold text-amber-300">Produtos sem preço de venda</p>
                                    @foreach($inventoryData['itemsWithoutPrice'] as $missingItem)
                                        <a href="{{ route('products.edit', $missingItem['id']) }}" class="flex items-center justify-between gap-3 border-b border-border-subtle px-3 py-2 transition-colors last:border-0 hover:bg-surface-elevated">
                                            <span>
                                                <span class="block text-[0.8125rem] font-medium text-dg-100">{{ $missingItem['name'] }}</span>
                                                <span class="block text-[0.6875rem] text-dg-400">Custo: {{ $money($missingItem['cost']) }}</span>
                                            </span>
                                            <svg class="h-4 w-4 flex-shrink-0 text-dg-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <dl class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-5">
                        <div>
                            <dt class="{{ $miniLabel }}">Valor em estoque</dt>
                            <dd class="text-lg font-extrabold tabular-nums text-dg-100">{{ $money($inventoryData['totalCost']) }}</dd>
                            <dd class="text-[0.6875rem] text-dg-500">Custo total investido</dd>
                        </div>
                        <div>
                            <dt class="{{ $miniLabel }}">Valor potencial</dt>
                            <dd class="text-lg font-extrabold tabular-nums text-sky-300">{{ $money($inventoryData['totalSaleValue']) }}</dd>
                            <dd class="text-[0.6875rem] text-dg-500">Se vender tudo</dd>
                        </div>
                        <div>
                            <dt class="{{ $miniLabel }}">Lucro possível</dt>
                            <dd class="text-lg font-extrabold tabular-nums {{ $signTone($inventoryData['potentialProfit']) }}">{{ $money($inventoryData['potentialProfit']) }}</dd>
                        </div>
                        <div>
                            <dt class="{{ $miniLabel }}">Margem potencial</dt>
                            <dd class="text-lg font-extrabold tabular-nums {{ $signTone($inventoryData['potentialMargin']) }}">{{ $inventoryData['potentialMargin'] }}%</dd>
                        </div>
                        <div>
                            @php $days = $inventoryData['avgDaysInStock']; @endphp
                            <dt class="{{ $miniLabel }}">Tempo médio</dt>
                            <dd class="text-lg font-extrabold tabular-nums {{ $days > 30 ? 'text-red-400' : ($days > 15 ? 'text-amber-400' : 'text-dg-100') }}">{{ $days }} dias</dd>
                            <dd class="text-[0.6875rem] {{ $days > 30 ? 'text-red-400' : 'text-dg-500' }}">{{ $days > 30 ? 'Atenção: estoque parado' : 'Em estoque' }}</dd>
                        </div>
                    </dl>

                    {{-- Breakdown: Próprios vs Consignação --}}
                    @if($inventoryData['ownData']['itemCount'] > 0 || $inventoryData['consignmentData']['itemCount'] > 0)
                        <div class="border-t border-border pt-3">
                            <p class="mb-2 text-[0.6875rem] font-semibold uppercase tracking-wide text-dg-500">Detalhamento por origem</p>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach([
                                    ['data' => $inventoryData['ownData'], 'label' => 'Próprios', 'badge' => 'bg-indigo-500/15 text-indigo-300', 'cost' => 'Custo', 'sale' => 'Venda'],
                                    ['data' => $inventoryData['consignmentData'], 'label' => 'Consignação', 'badge' => 'bg-amber-500/15 text-amber-300', 'cost' => 'Custo fornecedor', 'sale' => 'Venda sugerida'],
                                ] as $origin)
                                    @continue($origin['data']['itemCount'] <= 0)
                                    <div class="rounded-lg border border-border bg-surface-overlay px-4 py-3">
                                        <div class="mb-1.5 flex items-center justify-between">
                                            <span class="text-[0.8125rem] font-semibold text-dg-100">{{ $origin['label'] }}</span>
                                            <span class="rounded-full px-1.5 py-px text-[0.6875rem] font-semibold {{ $origin['badge'] }}">{{ $origin['data']['itemCount'] }} {{ $origin['data']['itemCount'] === 1 ? 'item' : 'itens' }}</span>
                                        </div>
                                        <dl class="flex flex-wrap gap-4">
                                            <div><dt class="text-[0.625rem] text-dg-400">{{ $origin['cost'] }}</dt><dd class="text-sm font-bold tabular-nums text-dg-100">{{ $money($origin['data']['totalCost']) }}</dd></div>
                                            <div><dt class="text-[0.625rem] text-dg-400">{{ $origin['sale'] }}</dt><dd class="text-sm font-bold tabular-nums text-sky-300">{{ $money($origin['data']['totalSaleValue']) }}</dd></div>
                                            <div><dt class="text-[0.625rem] text-dg-400">Lucro</dt><dd class="text-sm font-bold tabular-nums {{ $signTone($origin['data']['profit']) }}">{{ $money($origin['data']['profit']) }}</dd></div>
                                            <div><dt class="text-[0.625rem] text-dg-400">Margem</dt><dd class="text-sm font-bold tabular-nums {{ $signTone($origin['data']['margin']) }}">{{ $origin['data']['margin'] }}%</dd></div>
                                        </dl>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            <!-- Lucro Líquido -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach([
                    ['value' => $netProfitCurrent, 'label' => 'Lucro líquido atual', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'formula' => 'Despesas pagas', 'expenses' => $monthExpensePaid, 'projected' => false],
                    ['value' => $netProfitProjected, 'label' => 'Lucro líquido projetado', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'formula' => 'Despesas totais', 'expenses' => $monthExpenseTotal, 'projected' => true],
                ] as $np)
                    @php $positive = $np['value'] >= 0; @endphp
                    <div class="relative overflow-hidden rounded-xl border p-5 {{ $positive ? 'border-emerald-500/30 bg-emerald-500/[0.07]' : 'border-red-500/30 bg-red-500/[0.07]' }}">
                        @if($np['projected'] && ! $positive)
                            <span class="absolute right-2.5 top-2.5 rounded-full bg-red-600 px-2 py-0.5 text-[0.625rem] font-bold uppercase tracking-wide text-white">Alerta</span>
                        @endif
                        <div class="mb-2 flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $positive ? 'bg-emerald-600' : 'bg-red-600' }}" aria-hidden="true">
                                <svg class="h-[1.1rem] w-[1.1rem] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $np['icon'] }}"/></svg>
                            </span>
                            <p class="text-xs font-bold uppercase tracking-wide {{ $positive ? 'text-emerald-300' : 'text-red-300' }}">{{ $np['label'] }}</p>
                        </div>
                        <p class="text-[1.75rem] font-extrabold tabular-nums {{ $positive ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $money(abs($np['value'])) }}
                            @unless($positive)<span class="text-sm font-semibold">negativo</span>@endunless
                        </p>
                        <p class="mt-1 text-[0.6875rem] {{ $positive ? 'text-emerald-300/80' : 'text-red-300/80' }}">
                            Lucro de vendas ({{ $money($salesData['salesProfit']) }}) − {{ $np['formula'] }} ({{ $money($np['expenses']) }})
                        </p>
                        @if($np['projected'] && $monthExpensePending > 0 && $netProfitProjected < $netProfitCurrent)
                            <p class="mt-1.5 text-[0.6875rem] font-semibold text-amber-400">⚠ Ainda há {{ $money($monthExpensePending) }} em despesas pendentes</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Carteiras -->
            @if($accounts->count() > 0)
                <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach($accounts as $account)
                        <a href="{{ route('finance.accounts', ['account_id' => $account->id]) }}"
                           class="flex items-center gap-3 rounded-xl border border-border bg-surface-raised p-4 transition-colors hover:border-border-strong hover:bg-surface-overlay">
                            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg ring-1 ring-inset ring-white/10" style="background: {{ $account->color }};" aria-hidden="true">
                                <svg class="h-5 w-5 {{ $account->isLightColor() ? 'text-dg-950' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $account->type->icon() }}"/></svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-xs text-dg-400">{{ $account->name }}</span>
                                <span class="block text-base font-bold tabular-nums {{ (float) $account->current_balance >= 0 ? 'text-dg-100' : 'text-red-400' }}">{{ $account->formatted_balance }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Gráfico Receitas vs Despesas -->
                <section class="dg-card-flat overflow-hidden" aria-labelledby="chart-title">
                    <h2 id="chart-title" class="border-b border-border px-5 py-4 text-[0.9375rem] font-semibold text-dg-100">
                        Receitas vs despesas · <span class="capitalize">{{ $referenceDate->translatedFormat('F/Y') }}</span>
                    </h2>
                    <div class="h-64 p-5">
                        <canvas id="financeChart" aria-label="Gráfico de receitas e despesas do mês" role="img"></canvas>
                    </div>
                </section>

                <!-- Contas Vencendo -->
                <section class="dg-card-flat overflow-hidden" aria-labelledby="due-title">
                    <div class="flex items-center justify-between border-b border-border px-5 py-4">
                        <h2 id="due-title" class="text-[0.9375rem] font-semibold text-dg-100">Próximos vencimentos</h2>
                        @if($dueSoon->count() > 0)
                            <span class="rounded-full bg-red-500/10 px-2 py-0.5 text-[0.6875rem] font-semibold text-red-300">{{ $dueSoon->count() }}</span>
                        @endif
                    </div>
                    <ul class="max-h-[300px] divide-y divide-border-subtle overflow-y-auto">
                        @forelse($dueSoon as $item)
                            <li class="flex items-center justify-between gap-3 px-5 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-[0.8125rem] font-medium text-dg-100">{{ $item->description }}</p>
                                    <p class="text-[0.6875rem] tabular-nums {{ $item->due_date?->isPast() ? 'text-red-400' : 'text-amber-400' }}">
                                        {{ $item->due_date?->format('d/m/Y') ?? '-' }}@if($item->due_date?->isPast()) · Vencido @endif
                                    </p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-sm font-bold tabular-nums {{ $item->type->value === 'expense' ? 'text-red-400' : 'text-emerald-400' }}">{{ $item->formatted_amount_plain }}</p>
                                    <span class="inline-block rounded-full px-1.5 py-px text-[0.625rem]" style="background: {{ $item->category->color ?? '#6b7280' }}26; color: {{ $item->category->color ?? '#a4a4a4' }};">
                                        {{ $item->category->name ?? '-' }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center text-sm text-dg-500">Nenhuma conta pendente nos próximos 7 dias.</li>
                        @endforelse
                    </ul>
                </section>
            </div>

            <!-- Últimas Movimentações -->
            <section class="dg-card-flat mt-6 overflow-hidden" aria-labelledby="recent-title">
                <h2 id="recent-title" class="border-b border-border px-5 py-4 text-[0.9375rem] font-semibold text-dg-100">Últimas movimentações</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="dg-table-header">
                            <tr>
                                <th scope="col" class="px-5 py-2.5 text-left">Data</th>
                                <th scope="col" class="px-4 py-2.5 text-left">Descrição</th>
                                <th scope="col" class="px-4 py-2.5 text-left">Categoria</th>
                                <th scope="col" class="px-4 py-2.5 text-left">Carteira</th>
                                <th scope="col" class="px-4 py-2.5 text-center">Status</th>
                                <th scope="col" class="px-5 py-2.5 text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @forelse($recentTransactions as $tx)
                                <tr class="transition-colors hover:bg-surface-overlay/60">
                                    <td class="whitespace-nowrap px-5 py-2.5 text-[0.8125rem] tabular-nums text-dg-400">
                                        {{ $tx->paid_at ? $tx->paid_at->format('d/m H:i') : $tx->due_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-[0.8125rem] font-medium text-dg-100">{{ Str::limit($tx->description, 40) }}</td>
                                    <td class="px-4 py-2.5">
                                        <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[0.6875rem] font-medium" style="background: {{ $tx->category->color ?? '#6b7280' }}26; color: {{ $tx->category->color ?? '#a4a4a4' }};">
                                            {{ $tx->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-xs text-dg-400">{{ $tx->account->name ?? '-' }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[0.6875rem] font-semibold" style="background: {{ $tx->status->bgColor() }}; color: {{ $tx->status->color() }};">
                                            {{ $tx->status->label() }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-2.5 text-right font-bold tabular-nums {{ $tx->type->value === 'expense' ? 'text-red-400' : 'text-emerald-400' }}">
                                        {{ $tx->formatted_amount }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-dg-500">Nenhuma movimentação registrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('financeChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Receitas',
                            data: @json($chartData['incomes']),
                            backgroundColor: 'rgba(52, 211, 153, 0.75)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Despesas',
                            data: @json($chartData['expenses']),
                            backgroundColor: 'rgba(248, 113, 113, 0.75)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16, color: '#a4a4a4', font: { size: 11 } } }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(v) { return 'R$ ' + v.toLocaleString('pt-BR'); },
                                color: '#818181',
                                font: { size: 10 }
                            },
                            grid: { color: 'rgba(255, 255, 255, 0.06)' }
                        },
                        x: {
                            ticks: { color: '#818181', font: { size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
