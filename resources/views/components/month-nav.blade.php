@props([
    'route',
    'startDate',
])

{{-- Navegação mês anterior / próximo mantendo os outros filtros da URL. --}}
@php
    $ref = \Carbon\Carbon::parse($startDate)->startOfMonth();
    $prev = $ref->copy()->subMonth();
    $next = $ref->copy()->addMonth();
    $keep = request()->except(['start_date', 'end_date', 'page']);
    $urlFor = fn ($month) => route($route, array_merge($keep, [
        'start_date' => $month->copy()->startOfMonth()->format('Y-m-d'),
        'end_date' => $month->copy()->endOfMonth()->format('Y-m-d'),
    ]));
    $arrow = 'flex h-9 w-9 items-center justify-center rounded-lg bg-surface-elevated text-dg-300 transition-colors hover:bg-dg-800 hover:text-dg-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-dg-300';
@endphp

<nav {{ $attributes->merge(['class' => 'flex items-center justify-center gap-4 rounded-xl border border-border bg-surface-raised px-5 py-2.5']) }} aria-label="Mês">
    <a href="{{ $urlFor($prev) }}" class="{{ $arrow }}" aria-label="Mês anterior">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="min-w-40 text-center">
        <p class="text-base font-bold capitalize text-dg-50">{{ $ref->translatedFormat('F Y') }}</p>
        @unless($ref->isSameMonth(now()))
            <a href="{{ route($route) }}" class="text-[0.6875rem] font-medium text-sky-300 hover:text-sky-200">Voltar ao mês atual</a>
        @endunless
    </div>
    <a href="{{ $urlFor($next) }}" class="{{ $arrow }}" aria-label="Próximo mês">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
</nav>
