{{--
    Painel "Equipe" da home (admin geral).
    Espera: $teamPanel (DashboardController::buildTeamPanel), $referenceDate, $isCurrentMonth.
    Precisa estar dentro do x-data que define showValues.
--}}
@php
    $members = $teamPanel['members'] ?? [];
    $totals = $teamPanel['totals'] ?? [];
    $monthlyGoals = $teamPanel['goals']['monthly'] ?? [];
    $weeklyGoal = $teamPanel['goals']['weekly'] ?? null;

    $nextGoal = collect($monthlyGoals)->first(fn ($g) => ! $g['reached']) ?? collect($monthlyGoals)->last();
    $reachedGoals = collect($monthlyGoals)->where('reached', true)->count();
    $goalCeiling = collect($monthlyGoals)->max('target') ?: 1;
    $goalCurrent = $nextGoal['current'] ?? 0;

    $fmtMinutes = function (int $minutes): string {
        $sign = $minutes < 0 ? '−' : '';
        $minutes = abs($minutes);
        return $sign . intdiv($minutes, 60) . 'h' . str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT);
    };

    $isWorkHours = now()->isWeekday() && now()->hour >= 8 && now()->hour < 17;

    $clockLabel = function (array $clock) use ($fmtMinutes, $isWorkHours): array {
        return match ($clock['status']) {
            'working' => ['Trabalhando', 'desde ' . $clock['clock_in'] . ' · ' . $fmtMinutes($clock['today_minutes']) . ' hoje', 'bg-emerald-400', 'text-emerald-300'],
            'lunch' => ['No almoço', 'chegou ' . $clock['clock_in'], 'bg-amber-400', 'text-amber-300'],
            'done' => ['Encerrou', $fmtMinutes($clock['today_minutes']) . ' hoje', 'bg-dg-500', 'text-dg-300'],
            default => $isWorkHours
                ? ['Ainda não chegou', 'sem batida hoje', 'bg-red-400', 'text-red-300']
                : ['Sem ponto hoje', '', 'bg-dg-600', 'text-dg-400'],
        };
    };
@endphp

<section class="mb-6 sm:mb-8 rounded-xl border border-border bg-surface-raised" aria-labelledby="team-panel-title">
    {{-- Cabeçalho: título, metas e acesso ao resumo --}}
    <div class="flex flex-col gap-4 border-b border-border px-4 py-4 sm:px-5 lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0">
            <h2 id="team-panel-title" class="text-base font-semibold text-dg-100">Equipe</h2>
            <p class="mt-0.5 text-xs text-dg-400">
                Vendas, lucro e comissões de <span class="capitalize">{{ $referenceDate->translatedFormat('F') }}</span>
                · ponto da semana atual
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-5">
            @if($nextGoal)
                <div class="min-w-[13rem]">
                    <div class="flex items-baseline justify-between gap-3 text-xs">
                        <span class="text-dg-400">Meta do mês <span class="text-dg-400">(celulares)</span></span>
                        <span class="font-semibold tabular-nums text-dg-100">{{ $goalCurrent }}<span class="font-normal text-dg-400"> / {{ $nextGoal['target'] }}</span></span>
                    </div>
                    <div class="relative mt-1.5 h-1.5 rounded-full bg-surface-elevated">
                        <div class="absolute inset-y-0 left-0 rounded-full {{ $nextGoal['reached'] ? 'bg-emerald-400' : 'bg-indigo-400' }}"
                             style="width: {{ min(100, round($goalCurrent / $goalCeiling * 100)) }}%"></div>
                        @foreach($monthlyGoals as $goal)
                            <span class="absolute top-1/2 h-2.5 w-px -translate-y-1/2 {{ $goal['reached'] ? 'bg-emerald-200' : 'bg-dg-400' }}"
                                  style="left: calc({{ round($goal['target'] / $goalCeiling * 100) }}% - 1px)"
                                  title="{{ $goal['target'] }} celulares · {{ $goal['reward'] }}"></span>
                        @endforeach
                    </div>
                    <p class="mt-1 text-[0.6875rem] text-dg-400">
                        @if($nextGoal['reached'])
                            Todas as {{ count($monthlyGoals) }} faixas batidas
                        @else
                            Faltam {{ $nextGoal['target'] - $goalCurrent }} para {{ $nextGoal['reward'] }}@if($reachedGoals) · {{ $reachedGoals }} {{ $reachedGoals === 1 ? 'faixa batida' : 'faixas batidas' }}@endif
                        @endif
                    </p>
                </div>
            @endif

            @if($weeklyGoal && $isCurrentMonth)
                <div class="min-w-[10rem]">
                    <div class="flex items-baseline justify-between gap-3 text-xs">
                        <span class="text-dg-400">Meta da semana</span>
                        <span class="font-semibold tabular-nums text-dg-100">{{ $weeklyGoal['current'] }}<span class="font-normal text-dg-400"> / {{ $weeklyGoal['target'] }}</span></span>
                    </div>
                    <div class="mt-1.5 h-1.5 rounded-full bg-surface-elevated">
                        <div class="h-full rounded-full {{ $weeklyGoal['reached'] ? 'bg-emerald-400' : ($weeklyGoal['past_deadline'] ? 'bg-dg-500' : 'bg-amber-400') }}"
                             style="width: {{ $weeklyGoal['progress'] }}%"></div>
                    </div>
                    <p class="mt-1 text-[0.6875rem] text-dg-400">
                        @if($weeklyGoal['reached'])
                            Batida
                        @elseif($weeklyGoal['past_deadline'])
                            Prazo encerrado
                        @else
                            Faltam {{ $weeklyGoal['remaining'] }} até {{ mb_strtolower($weeklyGoal['deadline_label']) }}
                        @endif
                    </p>
                </div>
            @endif

            <button type="button"
                    @click="$dispatch('open-month-summary')"
                    class="inline-flex items-center justify-center gap-1.5 self-start rounded-lg border border-border-strong px-3 py-2 text-xs font-medium text-dg-200 transition-colors hover:bg-surface-elevated hover:text-dg-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400 sm:self-center">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Resumo Executivo
            </button>
        </div>
    </div>

    @if(empty($members))
        <div class="px-5 py-10 text-center">
            <p class="text-sm text-dg-300">Nenhuma vendedora ativa.</p>
            <p class="mt-1 text-xs text-dg-400">Cadastre usuários com o perfil de vendedora para acompanhar vendas, ponto e comissões aqui.</p>
        </div>
    @else
        {{-- Celular: um cartão por vendedora --}}
        <ul class="divide-y divide-border md:hidden">
            @foreach($members as $member)
                @php
                    [$clockTitle, $clockDetail, $clockDot, $clockText] = $clockLabel($member['clock']);
                    $balanceMinutes = $member['clock']['week_balance_minutes'];
                @endphp
                <li>
                    <button type="button"
                            @click="$dispatch('open-month-summary', { tab: 'individual', intern: @js($member['id']) })"
                            class="block w-full px-4 py-3.5 text-left transition-colors hover:bg-surface-overlay focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-indigo-400">
                        <span class="flex items-start justify-between gap-3">
                            <span class="min-w-0">
                                <span class="block truncate font-medium text-dg-100">{{ $member['name'] }}</span>
                                <span class="mt-0.5 flex items-center gap-1.5 text-xs">
                                    <span class="h-2 w-2 flex-shrink-0 rounded-full {{ $clockDot }}" aria-hidden="true"></span>
                                    <span class="{{ $clockText }}">{{ $clockTitle }}</span>
                                    @if($clockDetail)<span class="truncate text-dg-400">· {{ $clockDetail }}</span>@endif
                                </span>
                            </span>
                            <span class="flex-shrink-0 text-right">
                                <span class="block text-lg font-semibold leading-tight tabular-nums text-dg-50">{{ $member['sales_count'] }}</span>
                                <span class="block text-[0.6875rem] text-dg-400">{{ $member['sales_count'] === 1 ? 'venda' : 'vendas' }}</span>
                            </span>
                        </span>
                        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
                            <div><dt class="text-dg-400">Faturamento</dt><dd class="text-dg-200"><x-money :value="$member['revenue']" /></dd></div>
                            <div><dt class="text-dg-400">Lucro p/ loja</dt><dd class="font-semibold {{ $member['net_profit'] >= 0 ? 'text-dg-50' : 'text-red-400' }}"><x-money :value="$member['net_profit']" /></dd></div>
                            <div><dt class="text-dg-400">Comissão do mês</dt><dd class="text-purple-300"><x-money :value="$member['commission']" /></dd></div>
                            <div>
                                <dt class="text-dg-400">Saldo comissão</dt>
                                <dd class="{{ $member['commission_balance'] > 0 ? 'text-amber-300' : 'text-dg-400' }}"><x-money :value="$member['commission_balance']" />@if($member['pending_withdrawals'] > 0)<span class="text-amber-400/80"> · {{ $member['pending_withdrawals'] }} saque{{ $member['pending_withdrawals'] > 1 ? 's' : '' }}</span>@endif</dd>
                            </div>
                            <div><dt class="text-dg-400">Lucro bruto</dt><dd class="{{ $member['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}"><x-money :value="$member['profit']" /></dd></div>
                            <div>
                                <dt class="text-dg-400">Horas na semana</dt>
                                <dd class="tabular-nums text-dg-200">{{ $fmtMinutes($member['clock']['week_minutes']) }}@if($member['clock']['week_minutes'] > 0)<span class="{{ $balanceMinutes >= 0 ? 'text-emerald-400/80' : 'text-red-400/80' }}"> {{ $balanceMinutes >= 0 ? '+' : '' }}{{ $fmtMinutes($balanceMinutes) }}</span>@endif</dd>
                            </div>
                        </dl>
                    </button>
                </li>
            @endforeach
            @if(count($members) > 1)
                <li class="flex items-center justify-between gap-3 px-4 py-3 text-xs">
                    <span class="font-medium text-dg-300">Total · {{ $totals['sales_count'] }} vendas</span>
                    <span class="text-right">
                        <span class="block text-dg-400">Lucro p/ loja <x-money :value="$totals['net_profit']" class="font-semibold text-dg-50" /></span>
                        <span class="block text-dg-400">Saldo comissão <x-money :value="$totals['commission_balance']" class="text-amber-300" /></span>
                    </span>
                </li>
            @endif
        </ul>

        {{-- Tablet e desktop: tabela --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full min-w-[60rem] text-sm">
                <thead>
                    <tr class="text-left text-[0.6875rem] font-medium uppercase tracking-wide text-dg-400">
                        <th scope="col" class="px-4 py-2.5 font-medium sm:px-5">Vendedora</th>
                        <th scope="col" class="px-3 py-2.5 font-medium">Ponto hoje</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium">Vendas</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium">Faturamento</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium">Lucro bruto</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium">Comissão</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium" title="Lucro bruto menos a comissão da vendedora">Lucro p/ loja</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium" title="Comissões aprovadas menos saques aprovados (todo o período)">Saldo comissão</th>
                        <th scope="col" class="px-4 py-2.5 text-right font-medium sm:px-5" title="Horas trabalhadas na semana e saldo sobre 6h por dia trabalhado">Semana</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($members as $member)
                        @php
                            [$clockTitle, $clockDetail, $clockDot, $clockText] = $clockLabel($member['clock']);
                            $initials = collect(explode(' ', trim($member['name'])))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
                            $balanceMinutes = $member['clock']['week_balance_minutes'];
                        @endphp
                        <tr class="group cursor-pointer transition-colors hover:bg-surface-overlay"
                            @click="$dispatch('open-month-summary', { tab: 'individual', intern: @js($member['id']) })">
                            <td class="px-4 py-3 sm:px-5">
                                <button type="button"
                                        @click.stop="$dispatch('open-month-summary', { tab: 'individual', intern: @js($member['id']) })"
                                        class="flex items-center gap-3 rounded-md text-left focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                                    <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-surface-elevated text-xs font-semibold uppercase text-dg-200" aria-hidden="true">{{ $initials }}</span>
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium text-dg-100 group-hover:text-white">{{ $member['name'] }}</span>
                                        <span class="block text-[0.6875rem] text-dg-400">Ticket médio <x-money :value="$member['avg_ticket']" :decimals="0" /></span>
                                    </span>
                                </button>
                            </td>
                            <td class="px-3 py-3">
                                <span class="flex items-center gap-2">
                                    <span class="h-2 w-2 flex-shrink-0 rounded-full {{ $clockDot }}" aria-hidden="true"></span>
                                    <span class="min-w-0">
                                        <span class="block text-xs font-medium {{ $clockText }}">{{ $clockTitle }}</span>
                                        @if($clockDetail)
                                            <span class="block text-[0.6875rem] tabular-nums text-dg-400">{{ $clockDetail }}</span>
                                        @endif
                                    </span>
                                </span>
                            </td>
                            <td class="px-3 py-3 text-right tabular-nums">
                                <span class="font-semibold text-dg-100">{{ $member['sales_count'] }}</span>
                                <span class="block text-[0.6875rem] text-dg-400">{{ $member['phone_sales_count'] }} {{ $member['phone_sales_count'] === 1 ? 'celular' : 'celulares' }}</span>
                            </td>
                            <td class="px-3 py-3 text-right text-dg-200"><x-money :value="$member['revenue']" /></td>
                            <td class="px-3 py-3 text-right {{ $member['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}"><x-money :value="$member['profit']" /></td>
                            <td class="px-3 py-3 text-right text-purple-300"><x-money :value="$member['commission']" /></td>
                            <td class="px-3 py-3 text-right font-semibold {{ $member['net_profit'] >= 0 ? 'text-dg-50' : 'text-red-400' }}"><x-money :value="$member['net_profit']" /></td>
                            <td class="px-3 py-3 text-right">
                                <x-money :value="$member['commission_balance']" class="{{ $member['commission_balance'] > 0 ? 'text-amber-300' : 'text-dg-400' }}" />
                                @if($member['pending_withdrawals'] > 0)
                                    <span class="block text-[0.6875rem] text-amber-400/80">{{ $member['pending_withdrawals'] }} {{ $member['pending_withdrawals'] === 1 ? 'saque pendente' : 'saques pendentes' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums sm:px-5">
                                <span class="text-dg-200">{{ $fmtMinutes($member['clock']['week_minutes']) }}</span>
                                @if($member['clock']['week_minutes'] > 0)
                                    <span class="block text-[0.6875rem] {{ $balanceMinutes >= 0 ? 'text-emerald-400/80' : 'text-red-400/80' }}">{{ $balanceMinutes >= 0 ? '+' : '' }}{{ $fmtMinutes($balanceMinutes) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                @if(count($members) > 1)
                    <tfoot>
                        <tr class="border-t border-border-strong text-dg-300">
                            <th scope="row" colspan="2" class="px-4 py-3 text-left text-xs font-medium sm:px-5">Total da equipe</th>
                            <td class="px-3 py-3 text-right font-semibold tabular-nums text-dg-100">{{ $totals['sales_count'] }}</td>
                            <td class="px-3 py-3 text-right"><x-money :value="$totals['revenue']" /></td>
                            <td class="px-3 py-3 text-right text-emerald-400"><x-money :value="$totals['profit']" /></td>
                            <td class="px-3 py-3 text-right text-purple-300"><x-money :value="$totals['commission']" /></td>
                            <td class="px-3 py-3 text-right font-semibold text-dg-50"><x-money :value="$totals['net_profit']" /></td>
                            <td class="px-3 py-3 text-right text-amber-300"><x-money :value="$totals['commission_balance']" /></td>
                            <td class="px-4 py-3 sm:px-5"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-border px-4 py-3 text-xs sm:px-5">
            <span class="text-dg-400">Clique em uma vendedora para ver o detalhe no Resumo Executivo.</span>
            <span class="flex gap-4 sm:ml-auto">
                <a href="{{ route('admin.time-clock.index') }}" class="font-medium text-dg-300 underline-offset-4 hover:text-dg-100 hover:underline">Ponto completo</a>
                <a href="{{ route('admin.commissions.index') }}" class="font-medium text-dg-300 underline-offset-4 hover:text-dg-100 hover:underline">Comissões e saques</a>
            </span>
        </div>
    @endif
</section>
