@php
    $dollarRate = \App\Domain\System\Models\SystemSetting::get('dollar_rate');
    $isAdmin = auth()->user()->isAdminGeral();
    $cpRates = \App\Domain\System\Models\DollarCpRate::lastTen();
    $cpLatest = $cpRates->first();
    $cpPrevious = $cpRates->skip(1)->first();
@endphp

@if($isAdmin)
<div class="flex items-center gap-2">
    @if(!$dollarRate)
        <button @click="$dispatch('open-dollar-modal')" class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold hover:bg-amber-500/20 transition" title="Cotação não informada – clique para configurar">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <span>US$</span>
        </button>
    @else
        <div class="flex items-center gap-1.5">
            <button @click="$dispatch('open-dollar-modal')" class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold text-emerald-400 hover:bg-emerald-500/10 transition" title="Acompanhamento do dólar – clique para detalhes">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>R$ {{ number_format((float) $dollarRate, 2, ',', '.') }}</span>
            </button>
            @if($cpLatest)
                <span class="text-[10px] text-dg-500 hidden lg:inline" title="Compras Paraguai">CP R$ {{ number_format((float) $cpLatest->rate, 2, ',', '.') }}</span>
                @if($cpPrevious)
                    @php
                        $cpDiff = (float) $cpLatest->rate - (float) $cpPrevious->rate;
                        $cpPct = (float) $cpPrevious->rate > 0 ? ($cpDiff / (float) $cpPrevious->rate) * 100 : 0;
                    @endphp
                    <span class="text-[10px] font-semibold hidden lg:inline {{ $cpDiff > 0 ? 'text-red-400' : ($cpDiff < 0 ? 'text-emerald-400' : 'text-dg-500') }}">
                        {{ ($cpDiff > 0 ? '▲' : ($cpDiff < 0 ? '▼' : '–')) }}{{ number_format(abs($cpPct), 1, ',', '.') }}%
                    </span>
                @endif
            @endif
        </div>
    @endif
</div>
@endif
