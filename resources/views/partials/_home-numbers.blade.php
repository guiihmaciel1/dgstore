{{--
    Números do mês na home.
    Espera: $todayTotal, $todayCount, $monthTotal, $monthCount, $profit, $prevMonthProfit,
    $forecast (null fora do mês atual), $todayAppointments, $nextAppointment, $isCurrentMonth.
    Precisa estar dentro do x-data que define showValues.
--}}
@php
    $canSeeFinancials = auth()->user()->canViewFinancials();
    $avgTicket = $monthCount > 0 ? $monthTotal / $monthCount : 0;
    $liquidMargin = $profit['month_revenue'] > 0 ? ($profit['real_profit'] / $profit['month_revenue']) * 100 : 0;
    $samePeriod = $prevMonthProfit['same_period'] ?? null;

    // Chip de margem no tema escuro
    $marginTone = fn (float $pct, float $good, float $ok) => $pct >= $good
        ? 'bg-emerald-500/15 text-emerald-300'
        : ($pct >= $ok ? 'bg-amber-500/15 text-amber-300' : 'bg-red-500/15 text-red-300');

    // Comparação: % quando a base é relevante, diferença em reais quando não é
    $compare = function (float $now, float $before): ?array {
        if ($before == 0.0 && $now == 0.0) {
            return null;
        }
        $diff = $now - $before;
        $usePct = abs($before) >= 1000;
        $text = $usePct
            ? ($diff >= 0 ? '+' : '−') . number_format(abs($diff / abs($before)) * 100, 0, ',', '.') . '%'
            : ($diff >= 0 ? '+ R$ ' : '− R$ ') . number_format(abs($diff), 0, ',', '.');
        return ['text' => $text, 'up' => $diff >= 0];
    };
    $revenueVsSame = $samePeriod ? $compare($profit['month_revenue'], $samePeriod['revenue']) : null;
    $profitVsSame = $samePeriod ? $compare($profit['month_profit'], $samePeriod['profit']) : null;

    $row = 'flex items-center justify-between gap-3 text-xs';
@endphp

<section class="mb-6 sm:mb-8" aria-label="Números do mês">
    {{-- Linha 1: movimento --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
            <p class="text-xs text-dg-400 sm:text-sm">Hoje</p>
            <p class="mt-1 truncate text-lg font-bold tabular-nums text-dg-100 sm:text-2xl"><x-money :value="$todayTotal" /></p>
            <p class="mt-1 text-xs text-dg-400">
                {{ $todayCount }} {{ $todayCount === 1 ? 'pedido' : 'pedidos' }}
                @if($canSeeFinancials && $todayCount > 0)
                    · lucro <x-money :value="$profit['today_profit']" class="{{ $profit['today_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}" />
                @endif
            </p>
        </div>

        <div class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
            <p class="text-xs text-dg-400 sm:text-sm">Vendas do mês</p>
            <p class="mt-1 truncate text-lg font-bold tabular-nums text-dg-100 sm:text-2xl"><x-money :value="$monthTotal" /></p>
            <p class="mt-1 text-xs text-dg-400">
                {{ $monthCount }} {{ $monthCount === 1 ? 'venda' : 'vendas' }}
                @if($revenueVsSame)
                    · <span class="{{ $revenueVsSame['up'] ? 'text-emerald-400' : 'text-red-400' }}" x-show="showValues">{{ $revenueVsSame['text'] }}</span><span x-show="!showValues" x-cloak class="dg-hidden-value">•••</span>
                    <span class="hidden sm:inline">vs. até {{ $samePeriod['until_label'] }}</span>
                @endif
            </p>
        </div>

        @if($forecast && $canSeeFinancials)
            <div class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
                <p class="text-xs text-dg-400 sm:text-sm">Previsão de vendas</p>
                <p class="mt-1 truncate text-lg font-bold tabular-nums text-dg-100 sm:text-2xl"><x-money :value="$forecast['revenue']" :decimals="0" /></p>
                <p class="mt-1 text-xs text-dg-400">no ritmo de {{ $forecast['days_elapsed'] }} de {{ $forecast['days_in_month'] }} dias</p>
            </div>
        @else
            <div class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
                <p class="text-xs text-dg-400 sm:text-sm">Ticket médio</p>
                <p class="mt-1 truncate text-lg font-bold tabular-nums text-dg-100 sm:text-2xl"><x-money :value="$avgTicket" /></p>
                <p class="mt-1 text-xs text-dg-400">por venda no mês</p>
            </div>
        @endif

        <a href="{{ route('schedule.index') }}"
           class="block rounded-xl border border-border bg-surface-raised p-4 transition-colors hover:border-border-strong hover:bg-surface-overlay focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400 sm:p-5">
            <p class="text-xs text-dg-400 sm:text-sm">Agenda de hoje</p>
            <p class="mt-1 text-lg font-bold text-dg-100 sm:text-2xl">
                {{ $todayAppointments->count() }}
                <span class="text-sm font-medium text-dg-400">{{ $todayAppointments->count() === 1 ? 'atendimento' : 'atendimentos' }}</span>
            </p>
            <p class="mt-1 truncate text-xs text-dg-400">
                @if($nextAppointment)
                    Próximo <span class="text-indigo-300">{{ substr($nextAppointment->start_time, 0, 5) }}</span> · {{ $nextAppointment->attendant_name }}
                @else
                    Nada pendente hoje
                @endif
            </p>
        </a>
    </div>

    @if($canSeeFinancials)
    {{-- Linha 2: resultado --}}
    <div class="mt-3 grid grid-cols-1 gap-3 sm:mt-4 sm:gap-4 md:grid-cols-3">
        {{-- Lucro bruto --}}
        <div class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wide text-dg-400">Lucro bruto</p>
                <span class="rounded-full px-2 py-0.5 text-[0.6875rem] font-medium {{ $marginTone($profit['month_margin'], 20, 10) }}">
                    <span x-show="showValues">{{ number_format($profit['month_margin'], 1, ',', '.') }}% margem</span><span x-show="!showValues" x-cloak>•••</span>
                </span>
            </div>
            <p class="mt-1 text-2xl font-bold sm:text-3xl {{ $profit['month_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}"><x-money :value="$profit['month_profit']" /></p>

            <dl class="mt-3 space-y-1.5 border-t border-border pt-3">
                <div class="{{ $row }}"><dt class="text-dg-400">Faturamento</dt><dd class="font-semibold text-dg-200"><x-money :value="$profit['month_revenue']" /></dd></div>
                <div class="{{ $row }}"><dt class="text-dg-400">Custo dos produtos</dt><dd class="font-semibold text-red-400"><x-money :value="-($profit['month_revenue'] - $profit['month_profit'])" /></dd></div>
                @if($profitVsSame)
                    <div class="{{ $row }}">
                        <dt class="text-dg-400">Vs. mesmo período</dt>
                        <dd class="font-semibold {{ $profitVsSame['up'] ? 'text-emerald-400' : 'text-red-400' }}"><span x-show="showValues">{{ $profitVsSame['text'] }}</span><span x-show="!showValues" x-cloak class="dg-hidden-value">•••</span></dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Lucro líquido (destaque) --}}
        <div class="rounded-xl border border-border-strong bg-surface-overlay p-4 sm:p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wide text-dg-300">Lucro líquido</p>
                <span class="rounded-full px-2 py-0.5 text-[0.6875rem] font-medium {{ $marginTone($liquidMargin, 10, 5) }}">
                    <span x-show="showValues">{{ number_format($liquidMargin, 1, ',', '.') }}% líquido</span><span x-show="!showValues" x-cloak>•••</span>
                </span>
            </div>
            <p class="mt-1 text-2xl font-bold sm:text-3xl {{ $profit['real_profit'] >= 0 ? 'text-dg-50' : 'text-red-400' }}"><x-money :value="$profit['real_profit']" /></p>
            @if($forecast)
                <p class="mt-0.5 text-xs text-dg-400">até hoje · despesas pagas até agora</p>
            @endif

            <dl class="mt-3 space-y-1.5 border-t border-border pt-3">
                <div class="{{ $row }}"><dt class="text-dg-400">Lucro bruto</dt><dd class="font-semibold text-emerald-400"><x-money :value="$profit['month_profit']" /></dd></div>
                @if($profit['salaries_paid'] > 0)
                    <div class="{{ $row }}"><dt class="text-dg-400">Salários</dt><dd class="font-semibold text-red-400"><x-money :value="-$profit['salaries_paid']" /></dd></div>
                    <div class="{{ $row }}"><dt class="text-dg-400">Despesas</dt><dd class="font-semibold text-red-400"><x-money :value="-$profit['expenses_without_salaries']" /></dd></div>
                @else
                    <div class="{{ $row }}"><dt class="text-dg-400">Despesas</dt><dd class="font-semibold text-red-400"><x-money :value="-$profit['month_expenses_paid']" /></dd></div>
                @endif
                <div class="{{ $row }}"><dt class="text-dg-400">Comissões</dt><dd class="font-semibold text-purple-300"><x-money :value="-($profit['month_commissions'] ?? 0)" /></dd></div>
            </dl>

            @if($forecast)
                <div class="mt-3 rounded-lg bg-surface-raised px-3 py-2.5">
                    <div class="{{ $row }}">
                        <span class="font-medium text-dg-200">Previsto no fim do mês</span>
                        <x-money :value="$forecast['net_profit']" :decimals="0" class="text-sm font-bold {{ $forecast['net_profit'] >= 0 ? 'text-dg-50' : 'text-red-400' }}" />
                    </div>
                    <details class="group mt-1">
                        <summary class="cursor-pointer list-none text-[0.6875rem] text-dg-400 underline-offset-4 hover:text-dg-200 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400 [&::-webkit-details-marker]:hidden">
                            <span class="group-open:hidden">Como é calculado</span><span class="hidden group-open:inline">Ocultar cálculo</span>
                        </summary>
                        <dl class="mt-2 space-y-1">
                            <div class="{{ $row }}"><dt class="text-dg-400">Lucro bruto no ritmo atual</dt><dd class="text-emerald-400"><x-money :value="$forecast['gross_profit']" :decimals="0" /></dd></div>
                            <div class="{{ $row }}"><dt class="text-dg-400">Despesas já pagas</dt><dd class="text-red-400"><x-money :value="-$forecast['expenses_paid']" :decimals="0" /></dd></div>
                            <div class="{{ $row }}"><dt class="text-dg-400">Contas a pagar até o fim do mês</dt><dd class="text-red-400"><x-money :value="-$forecast['expenses_pending']" :decimals="0" /></dd></div>
                            <div class="{{ $row }}"><dt class="text-dg-400">Comissões no ritmo atual</dt><dd class="text-purple-300"><x-money :value="-$forecast['commissions']" :decimals="0" /></dd></div>
                        </dl>
                        <p class="mt-2 text-[0.6875rem] leading-snug text-dg-400">Vendas e comissões projetadas pelos {{ $forecast['days_elapsed'] }} dias já passados. Contas a pagar vêm do Financeiro (vencimento neste mês, ainda não pagas).</p>
                    </details>
                </div>
            @endif

            @if(!empty($profit['salary_details']))
                <details class="group mt-2">
                    <summary class="cursor-pointer list-none text-[0.6875rem] text-dg-400 underline-offset-4 hover:text-dg-200 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400 [&::-webkit-details-marker]:hidden">
                        <span class="group-open:hidden">Ver salários pagos ({{ count($profit['salary_details']) }})</span><span class="hidden group-open:inline">Ocultar salários</span>
                    </summary>
                    <ul class="mt-2 space-y-1">
                        @foreach($profit['salary_details'] as $salary)
                            <li class="{{ $row }}">
                                <span class="truncate text-dg-400">{{ $salary['description'] }} @if($salary['paid_at'])<span class="text-dg-500">· {{ $salary['paid_at'] }}</span>@endif</span>
                                <x-money :value="$salary['amount']" class="text-dg-300" />
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>

        {{-- Mês anterior --}}
        <div class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wide text-dg-400">Mês anterior</p>
                <span class="rounded-full bg-surface-elevated px-2 py-0.5 text-[0.6875rem] font-medium capitalize text-dg-300">{{ $prevMonthProfit['month_label'] }}</span>
            </div>
            <p class="mt-1 text-2xl font-bold sm:text-3xl {{ $prevMonthProfit['real_profit'] >= 0 ? 'text-dg-200' : 'text-red-400' }}"><x-money :value="$prevMonthProfit['real_profit']" /></p>
            <p class="mt-0.5 text-xs text-dg-400">lucro líquido do mês fechado</p>

            <dl class="mt-3 space-y-1.5 border-t border-border pt-3">
                <div class="{{ $row }}"><dt class="text-dg-400">Faturamento</dt><dd class="font-semibold text-dg-300"><x-money :value="$prevMonthProfit['month_revenue']" /></dd></div>
                <div class="{{ $row }}"><dt class="text-dg-400">Lucro bruto</dt><dd class="font-semibold text-emerald-400"><x-money :value="$prevMonthProfit['month_profit']" /></dd></div>
                @if($prevMonthProfit['salaries_paid'] > 0)
                    <div class="{{ $row }}"><dt class="text-dg-400">Salários</dt><dd class="font-semibold text-red-400"><x-money :value="-$prevMonthProfit['salaries_paid']" /></dd></div>
                    <div class="{{ $row }}"><dt class="text-dg-400">Despesas</dt><dd class="font-semibold text-red-400"><x-money :value="-$prevMonthProfit['expenses_without_salaries']" /></dd></div>
                @else
                    <div class="{{ $row }}"><dt class="text-dg-400">Despesas</dt><dd class="font-semibold text-red-400"><x-money :value="-$prevMonthProfit['month_expenses']" /></dd></div>
                @endif
                <div class="{{ $row }}"><dt class="text-dg-400">Comissões</dt><dd class="font-semibold text-purple-300"><x-money :value="-($prevMonthProfit['month_commissions'] ?? 0)" /></dd></div>
            </dl>

            @if($samePeriod)
                <div class="mt-3 rounded-lg bg-surface-overlay px-3 py-2.5">
                    <p class="text-[0.6875rem] font-medium text-dg-300">Mesmo período (1 a {{ $samePeriod['until_label'] }})</p>
                    <dl class="mt-1.5 space-y-1">
                        <div class="{{ $row }}"><dt class="text-dg-400">Faturamento</dt><dd class="text-dg-300"><x-money :value="$samePeriod['revenue']" :decimals="0" /></dd></div>
                        <div class="{{ $row }}"><dt class="text-dg-400">Lucro bruto</dt><dd class="text-emerald-400"><x-money :value="$samePeriod['profit']" :decimals="0" /></dd></div>
                    </dl>
                </div>
            @endif
        </div>
    </div>
    @endif
</section>
