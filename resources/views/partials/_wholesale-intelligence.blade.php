@php
    $cs = $wholesaleData['channel_summary'];
    $repasse = $cs['repasse'];
    $cf = $cs['cliente_final'];
    $topClients = $wholesaleData['top_repasse_clients'];
    $inactiveClients = $wholesaleData['inactive_clients'];
    $accumulated = $wholesaleData['accumulated_ranking'];
    $evolution = $wholesaleData['monthly_evolution'];
@endphp

{{-- Inteligência Atacado x Cliente Final --}}
@if(auth()->user()->canViewFinancials())
<div class="mt-4 sm:mt-6">
    <h3 class="text-base sm:text-lg font-semibold text-dg-100 mb-3">Inteligência Atacado x Cliente Final</h3>

    {{-- Linha 1: Cards de resumo por canal (4 cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Faturamento Repasse --}}
        <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-dg-500 uppercase tracking-wide">Fat. Repasse</span>
            </div>
            <p class="text-2xl font-extrabold text-dg-100 leading-none">
                <span x-show="showValues">R$ {{ number_format($repasse['revenue'], 0, ',', '.') }}</span>
                <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;</span>
            </p>
            <div class="mt-2 space-y-0.5">
                <p class="text-xs text-dg-500">{{ $repasse['count'] }} {{ $repasse['count'] === 1 ? 'venda' : 'vendas' }} · {{ $repasse['items'] }} itens</p>
                <p class="text-xs text-purple-400 font-medium">
                    <span x-show="showValues">Margem {{ number_format($repasse['margin'], 1, ',', '.') }}%</span>
                    <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                </p>
            </div>
        </div>

        {{-- Lucro Repasse --}}
        <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-dg-500 uppercase tracking-wide">Lucro Repasse</span>
            </div>
            <p class="text-2xl font-extrabold leading-none {{ $repasse['profit'] >= 0 ? 'text-purple-400' : 'text-red-500' }}">
                <span x-show="showValues">R$ {{ number_format($repasse['profit'], 0, ',', '.') }}</span>
                <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;</span>
            </p>
            <div class="mt-2 space-y-0.5">
                <p class="text-xs text-dg-500">Ticket médio:
                    <span x-show="showValues" class="font-semibold text-dg-300">R$ {{ number_format($repasse['ticket'], 0, ',', '.') }}</span>
                    <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                </p>
                <p class="text-xs font-medium {{ $cs['repasse_profit_pct'] >= 50 ? 'text-purple-400' : 'text-dg-500' }}">
                    <span x-show="showValues">{{ number_format($cs['repasse_profit_pct'], 1, ',', '.') }}% do lucro total</span>
                    <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                </p>
            </div>
        </div>

        {{-- Faturamento Cliente Final --}}
        <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-dg-500 uppercase tracking-wide">Fat. Cliente Final</span>
            </div>
            <p class="text-2xl font-extrabold text-dg-100 leading-none">
                <span x-show="showValues">R$ {{ number_format($cf['revenue'], 0, ',', '.') }}</span>
                <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;</span>
            </p>
            <div class="mt-2 space-y-0.5">
                <p class="text-xs text-dg-500">{{ $cf['count'] }} {{ $cf['count'] === 1 ? 'venda' : 'vendas' }} · {{ $cf['items'] }} itens</p>
                <p class="text-xs text-blue-400 font-medium">
                    <span x-show="showValues">Margem {{ number_format($cf['margin'], 1, ',', '.') }}%</span>
                    <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                </p>
            </div>
        </div>

        {{-- Lucro Cliente Final --}}
        <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-dg-500 uppercase tracking-wide">Lucro Cl. Final</span>
            </div>
            <p class="text-2xl font-extrabold leading-none {{ $cf['profit'] >= 0 ? 'text-blue-400' : 'text-red-500' }}">
                <span x-show="showValues">R$ {{ number_format($cf['profit'], 0, ',', '.') }}</span>
                <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;</span>
            </p>
            <div class="mt-2 space-y-0.5">
                <p class="text-xs text-dg-500">Ticket médio:
                    <span x-show="showValues" class="font-semibold text-dg-300">R$ {{ number_format($cf['ticket'], 0, ',', '.') }}</span>
                    <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                </p>
                <p class="text-xs font-medium {{ (100 - $cs['repasse_profit_pct']) >= 50 ? 'text-blue-400' : 'text-dg-500' }}">
                    <span x-show="showValues">{{ number_format(100 - $cs['repasse_profit_pct'], 1, ',', '.') }}% do lucro total</span>
                    <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Linha 2: Comparativo visual (2 colunas) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mt-4">
        {{-- Barra comparativa Faturamento e Lucro --}}
        <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6">
            <h4 class="text-sm font-semibold text-dg-100 mb-4">Comparativo Faturamento & Lucro</h4>

            @if($cs['total_revenue'] > 0)
            {{-- Faturamento --}}
            <div class="mb-4">
                <p class="text-xs text-dg-500 font-medium mb-1.5">Faturamento</p>
                <div class="flex h-6 rounded-lg overflow-hidden bg-surface-overlay">
                    @php $rPct = $cs['repasse_revenue_pct']; @endphp
                    @if($rPct > 0)
                    <div class="h-full bg-purple-500 flex items-center justify-center transition-all duration-700" style="width: {{ max(8, $rPct) }}%">
                        <span class="text-[0.6rem] font-bold text-white px-1" x-show="showValues">{{ number_format($rPct, 0) }}%</span>
                    </div>
                    @endif
                    @if((100 - $rPct) > 0)
                    <div class="h-full bg-blue-500 flex items-center justify-center transition-all duration-700" style="width: {{ max(8, 100 - $rPct) }}%">
                        <span class="text-[0.6rem] font-bold text-white px-1" x-show="showValues">{{ number_format(100 - $rPct, 0) }}%</span>
                    </div>
                    @endif
                </div>
                <div class="flex justify-between mt-1.5 text-xs">
                    <span class="text-purple-400 font-semibold">
                        <span x-show="showValues">R$ {{ number_format($repasse['revenue'], 0, ',', '.') }}</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                    </span>
                    <span class="text-blue-400 font-semibold">
                        <span x-show="showValues">R$ {{ number_format($cf['revenue'], 0, ',', '.') }}</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                    </span>
                </div>
            </div>

            {{-- Lucro --}}
            <div>
                <p class="text-xs text-dg-500 font-medium mb-1.5">Lucro Bruto</p>
                @php $lpPct = $cs['repasse_profit_pct']; @endphp
                <div class="flex h-6 rounded-lg overflow-hidden bg-surface-overlay">
                    @if($lpPct > 0)
                    <div class="h-full bg-purple-400 flex items-center justify-center transition-all duration-700" style="width: {{ max(8, $lpPct) }}%">
                        <span class="text-[0.6rem] font-bold text-white px-1" x-show="showValues">{{ number_format($lpPct, 0) }}%</span>
                    </div>
                    @endif
                    @if((100 - $lpPct) > 0)
                    <div class="h-full bg-blue-400 flex items-center justify-center transition-all duration-700" style="width: {{ max(8, 100 - $lpPct) }}%">
                        <span class="text-[0.6rem] font-bold text-white px-1" x-show="showValues">{{ number_format(100 - $lpPct, 0) }}%</span>
                    </div>
                    @endif
                </div>
                <div class="flex justify-between mt-1.5 text-xs">
                    <span class="text-purple-400 font-semibold">
                        <span x-show="showValues">R$ {{ number_format($repasse['profit'], 0, ',', '.') }}</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                    </span>
                    <span class="text-blue-400 font-semibold">
                        <span x-show="showValues">R$ {{ number_format($cf['profit'], 0, ',', '.') }}</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                    </span>
                </div>
            </div>
            @else
            <p class="text-dg-500 text-center py-8 text-sm">Nenhuma venda registrada neste mês.</p>
            @endif

            <div class="flex items-center gap-4 mt-4 pt-3 border-t border-border">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-2 bg-purple-500 rounded-sm"></div>
                    <span class="text-xs text-dg-500">Repasse</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-2 bg-blue-500 rounded-sm"></div>
                    <span class="text-xs text-dg-500">Cliente Final</span>
                </div>
            </div>
        </div>

        {{-- Participação por canal --}}
        <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6">
            <h4 class="text-sm font-semibold text-dg-100 mb-4">Participação por Canal</h4>

            @if($cs['total_revenue'] > 0)
            <div class="grid grid-cols-2 gap-4">
                {{-- Repasse --}}
                <div class="text-center">
                    <div class="relative w-24 h-24 mx-auto mb-3">
                        <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="currentColor" class="text-surface-overlay" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="currentColor" class="text-purple-500" stroke-width="3"
                                    stroke-dasharray="{{ $cs['repasse_revenue_pct'] }}, 100"
                                    stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-lg font-extrabold text-purple-400" x-show="showValues">{{ number_format($cs['repasse_revenue_pct'], 0) }}%</span>
                            <span class="text-lg font-extrabold text-dg-500" x-show="!showValues" x-cloak>&bull;&bull;</span>
                        </div>
                    </div>
                    <p class="text-xs font-semibold text-purple-400">Repasse</p>
                    <p class="text-xs text-dg-500 mt-0.5">{{ $repasse['count'] }} vendas</p>
                    <p class="text-sm font-bold text-dg-300 mt-1">
                        <span x-show="showValues">R$ {{ number_format($repasse['revenue'], 0, ',', '.') }}</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                    </p>
                </div>

                {{-- Cliente Final --}}
                <div class="text-center">
                    <div class="relative w-24 h-24 mx-auto mb-3">
                        <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="currentColor" class="text-surface-overlay" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="currentColor" class="text-blue-500" stroke-width="3"
                                    stroke-dasharray="{{ 100 - $cs['repasse_revenue_pct'] }}, 100"
                                    stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-lg font-extrabold text-blue-400" x-show="showValues">{{ number_format(100 - $cs['repasse_revenue_pct'], 0) }}%</span>
                            <span class="text-lg font-extrabold text-dg-500" x-show="!showValues" x-cloak>&bull;&bull;</span>
                        </div>
                    </div>
                    <p class="text-xs font-semibold text-blue-400">Cliente Final</p>
                    <p class="text-xs text-dg-500 mt-0.5">{{ $cf['count'] }} vendas</p>
                    <p class="text-sm font-bold text-dg-300 mt-1">
                        <span x-show="showValues">R$ {{ number_format($cf['revenue'], 0, ',', '.') }}</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                    </p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-border space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-dg-500">Margem Repasse</span>
                    <span class="font-bold {{ $repasse['margin'] >= 10 ? 'text-purple-400' : 'text-amber-400' }}">
                        <span x-show="showValues">{{ number_format($repasse['margin'], 1, ',', '.') }}%</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-dg-500">Margem Cliente Final</span>
                    <span class="font-bold {{ $cf['margin'] >= 10 ? 'text-blue-400' : 'text-amber-400' }}">
                        <span x-show="showValues">{{ number_format($cf['margin'], 1, ',', '.') }}%</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                    </span>
                </div>
            </div>
            @else
            <p class="text-dg-500 text-center py-8 text-sm">Nenhuma venda registrada neste mês.</p>
            @endif
        </div>
    </div>

    {{-- Clientes Repasse Inativos --}}
    @if(count($inactiveClients) > 0)
    <div class="mt-4 bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm sm:text-base font-semibold text-dg-100">Clientes Repasse Inativos</h4>
                    <p class="text-xs text-dg-500">Sem compra há mais de 30 dias</p>
                </div>
            </div>
            <span class="text-xs font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded-full">{{ count($inactiveClients) }}</span>
        </div>

        <div class="space-y-2">
            @foreach($inactiveClients as $ic)
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface transition-colors group">
                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 {{ $ic['days_since'] >= 60 ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-dg-100 truncate">{{ $ic['name'] }}</p>
                    <div class="flex items-center gap-2 text-xs text-dg-500">
                        <span class="font-medium {{ $ic['days_since'] >= 60 ? 'text-red-400' : 'text-amber-400' }}">
                            há {{ $ic['days_since'] }} dias
                        </span>
                        <span>·</span>
                        <span>Última: {{ $ic['last_purchase'] }}</span>
                        <span>·</span>
                        <span>{{ $ic['total_purchases'] }} {{ $ic['total_purchases'] === 1 ? 'compra' : 'compras' }}</span>
                    </div>
                </div>

                <div class="text-right flex-shrink-0 hidden sm:block">
                    <p class="text-xs text-dg-500">Total histórico</p>
                    <p class="text-sm font-bold text-dg-300">
                        <span x-show="showValues">R$ {{ number_format($ic['total_spent'], 0, ',', '.') }}</span>
                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                    </p>
                </div>

                @if($ic['has_phone'])
                <a href="{{ $ic['whatsapp_url'] }}" target="_blank"
                   class="flex-shrink-0 w-9 h-9 rounded-lg bg-emerald-600 hover:bg-emerald-700 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>
                @else
                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-surface-overlay flex items-center justify-center" title="Sem telefone">
                    <svg class="w-4 h-4 text-dg-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Linha 3: Top Clientes de Repasse do mês --}}
    @if(count($topClients) > 0)
    <div class="mt-4 bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-5 bg-purple-500 rounded-full"></span>
                <h4 class="text-sm sm:text-base font-semibold text-dg-100">Top Clientes Repasse do Mês</h4>
            </div>
            <span class="text-xs text-dg-500 font-medium" style="text-transform: capitalize;">{{ $referenceDate->translatedFormat('M/Y') }}</span>
        </div>

        @php $maxClientProfit = collect($topClients)->max('profit'); @endphp

        <div class="space-y-2">
            @foreach($topClients as $idx => $client)
            <div class="relative p-3 rounded-lg {{ $idx === 0 ? 'bg-purple-50 border border-purple-100' : 'hover:bg-surface' }}" x-data="{ expanded: false }">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 {{ $idx === 0 ? 'bg-purple-600' : 'bg-surface' }} text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                        {{ $idx + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-sm text-dg-100 truncate">{{ $client['name'] }}</p>
                                <p class="text-xs text-dg-500">{{ $client['count'] }} {{ $client['count'] === 1 ? 'compra' : 'compras' }} no mês</p>
                            </div>
                            <div class="flex items-center gap-4 flex-shrink-0 ml-3">
                                <div class="text-right hidden sm:block">
                                    <p class="text-xs text-dg-500">Faturamento</p>
                                    <p class="text-sm font-bold text-dg-200">
                                        <span x-show="showValues">R$ {{ number_format($client['revenue'], 0, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-dg-500">Lucro</p>
                                    <p class="text-sm font-bold {{ $client['profit'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                        <span x-show="showValues">R$ {{ number_format($client['profit'], 0, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </p>
                                </div>
                                <div class="text-right hidden sm:block">
                                    <p class="text-xs text-dg-500">Margem</p>
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $client['margin'] >= 15 ? 'bg-emerald-100 text-emerald-700' : ($client['margin'] >= 5 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                        <span x-show="showValues">{{ number_format($client['margin'], 1, ',', '.') }}%</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($maxClientProfit > 0)
                <div class="mt-2 ml-9 h-1.5 bg-surface-overlay rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-purple-400 transition-all duration-700" style="width: {{ max(3, ($client['profit'] / $maxClientProfit) * 100) }}%"></div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Linha 4: Ranking acumulado + Evolução mensal (2 colunas) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mt-4">
        {{-- Top 5 Clientes Repasse (acumulado 6 meses) --}}
        @if(count($accumulated['clients']) > 0)
        <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-5 bg-purple-500 rounded-full"></span>
                    <h4 class="text-sm font-semibold text-dg-100">Top 5 Repasse (6 meses)</h4>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($accumulated['clients'] as $idx => $ac)
                <div class="p-3 rounded-lg {{ $idx === 0 ? 'bg-purple-50/50 border border-purple-100/50' : 'hover:bg-surface' }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <span class="w-5 h-5 {{ $idx === 0 ? 'bg-purple-600' : 'bg-surface' }} text-white rounded-full flex items-center justify-center text-[0.6rem] font-bold flex-shrink-0">{{ $idx + 1 }}</span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-dg-100 truncate">{{ $ac['name'] }}</p>
                                <p class="text-xs text-dg-500">{{ $ac['total_count'] }} compras</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <p class="text-sm font-bold text-dg-200">
                                <span x-show="showValues">R$ {{ number_format($ac['total_revenue'], 0, ',', '.') }}</span>
                                <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                            </p>
                            @if($ac['trend'] != 0)
                            <span class="text-[0.6rem] font-bold {{ $ac['trend'] > 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                {{ $ac['trend'] > 0 ? '+' : '' }}{{ number_format($ac['trend'], 0) }}%
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Mini barras por mês --}}
                    <div class="flex items-end gap-1 ml-7">
                        @php
                            $maxMonthRev = max(1, max(array_column($ac['per_month'], 'revenue')));
                        @endphp
                        @foreach($accumulated['months'] as $mi => $monthKey)
                        @php $mRev = $ac['per_month'][$monthKey]['revenue'] ?? 0; @endphp
                        <div class="flex-1 flex flex-col items-center gap-0.5">
                            <div class="w-full rounded-t-sm {{ $mRev > 0 ? 'bg-purple-400' : 'bg-surface-overlay' }}" style="height: {{ max(3, ($mRev / $maxMonthRev) * 28) }}px"></div>
                            <span class="text-[0.5rem] text-dg-500 leading-none">{{ $accumulated['month_labels'][$mi] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Evolução mensal: Lucro Repasse vs CF --}}
        <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-semibold text-dg-100">Evolução Lucro por Canal</h4>
                <span class="text-xs text-dg-500">Últimos 6 meses</span>
            </div>

            <div class="h-48">
                <canvas id="wholesaleEvolutionChart"></canvas>
            </div>

            <div class="flex items-center gap-4 mt-3 pt-3 border-t border-border">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-2 bg-purple-500 rounded-sm"></div>
                    <span class="text-xs text-dg-500">Repasse</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-2 bg-blue-500 rounded-sm"></div>
                    <span class="text-xs text-dg-500">Cliente Final</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const evolutionEl = document.getElementById('wholesaleEvolutionChart');
    if (!evolutionEl) return;

    const repasseRealData = @json($evolution['repasse']);
    const cfRealData = @json($evolution['cliente_final']);
    const hiddenRepasse = repasseRealData.map(() => 0);
    const hiddenCf = cfRealData.map(() => 0);
    const showVals = localStorage.getItem('dg_show_values') !== 'false';

    const evolutionChart = new Chart(evolutionEl.getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($evolution['labels']),
            datasets: [
                {
                    label: 'Repasse',
                    data: showVals ? repasseRealData : hiddenRepasse,
                    backgroundColor: 'rgba(168, 85, 247, 0.7)',
                    borderColor: '#a855f7',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Cliente Final',
                    data: showVals ? cfRealData : hiddenCf,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            if (localStorage.getItem('dg_show_values') === 'false') return ctx.dataset.label + ': R$ -----';
                            return ctx.dataset.label + ': R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (localStorage.getItem('dg_show_values') === 'false') return '';
                            return 'R$ ' + (value / 1000).toLocaleString('pt-BR', { maximumFractionDigits: 1 }) + 'k';
                        }
                    },
                    grid: { color: 'rgba(255,255,255,0.04)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    window.addEventListener('dg-show-values-changed', function(e) {
        const show = e.detail;
        evolutionChart.data.datasets[0].data = show ? repasseRealData : hiddenRepasse;
        evolutionChart.data.datasets[1].data = show ? cfRealData : hiddenCf;
        evolutionChart.update();
    });

    window.addEventListener('storage', function(e) {
        if (e.key === 'dg_show_values') {
            const show = e.newValue !== 'false';
            evolutionChart.data.datasets[0].data = show ? repasseRealData : hiddenRepasse;
            evolutionChart.data.datasets[1].data = show ? cfRealData : hiddenCf;
            evolutionChart.update();
        }
    });
});
</script>
@endif
