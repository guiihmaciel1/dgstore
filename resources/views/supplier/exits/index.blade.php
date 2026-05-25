@extends('layouts.supplier')

@section('title', 'Saídas')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-5">
    <div>
        <h1 class="s-title">Saídas</h1>
        <p class="s-subtitle">Registre a saída e acompanhe repasses</p>
    </div>
    <a href="{{ route('supplier.exits.create') }}" class="s-btn s-btn-primary w-full sm:w-auto shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
        Registrar Saída
    </a>
</div>

{{-- Filtro período --}}
<div class="s-card s-card-pad mb-4">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="s-label">De</label>
            <input type="date" name="from" value="{{ $from }}" class="s-input py-2.5">
        </div>
        <div>
            <label class="s-label">Até</label>
            <input type="date" name="to" value="{{ $to }}" class="s-input py-2.5">
        </div>
        <div class="flex items-end">
            <button type="submit" class="s-btn s-btn-secondary w-full">Filtrar</button>
        </div>
    </form>
</div>

{{-- Resumo --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="s-stat">
        <p class="s-stat-label">Saídas no período</p>
        <p class="s-stat-value">{{ $periodCount }}</p>
        <p class="s-stat-meta">aparelhos</p>
    </div>
    <div class="s-stat">
        <p class="s-stat-label">Total repasse</p>
        <p class="s-stat-value text-lg sm:text-2xl">R$ {{ number_format($periodTotal, 0, ',', '.') }}</p>
        <p class="s-stat-meta">no período</p>
    </div>
</div>

{{-- Lista --}}
<div class="s-card">
    @if($exits->isEmpty())
        <div class="s-card-pad text-center py-12" style="color: var(--apple-text-secondary);">
            <p>Nenhuma saída no período</p>
            <a href="{{ route('supplier.exits.create') }}" class="s-btn-ghost inline-block mt-3">Registrar primeira saída →</a>
        </div>
    @else
        <div class="lg:hidden">
            @foreach($exits as $item)
            <div class="s-item-card">
                <div class="flex justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold">{{ $item->name }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">
                            {{ collect([$item->storage, $item->color])->filter()->join(' · ') }}
                        </p>
                        <p class="text-xs font-mono mt-1" style="color: var(--apple-text-tertiary);">
                            {{ $item->imei ?? $item->serial_number ?? '—' }}
                        </p>
                        <p class="text-xs mt-1" style="color: var(--apple-text-tertiary);">
                            {{ $item->sold_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <p class="text-sm font-bold shrink-0" style="color: var(--apple-green);">
                        R$ {{ number_format($item->supplier_cost, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Produto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">IMEI/Serial</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Data</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Repasse</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exits as $item)
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <td class="px-5 py-3">
                            <p class="text-sm font-medium">{{ $item->name }}</p>
                            <p class="text-xs" style="color: var(--apple-text-secondary);">{{ collect([$item->storage, $item->color])->filter()->join(' · ') }}</p>
                        </td>
                        <td class="px-5 py-3 text-sm font-mono" style="color: var(--apple-text-secondary);">{{ $item->imei ?? $item->serial_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm" style="color: var(--apple-text-secondary);">{{ $item->sold_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold" style="color: var(--apple-green);">R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="s-card-pad border-t" style="border-color: var(--apple-separator);">
            {{ $exits->links() }}
        </div>
    @endif
</div>
@endsection
