@extends('layouts.supplier')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="s-title">Olá, {{ explode(' ', auth('supplier')->user()->supplier->name)[0] }}</h1>
    <p class="s-subtitle">Visão geral do seu estoque</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="s-stat">
        <p class="s-stat-label">Disponível</p>
        <p class="s-stat-value">{{ $stats['available_count'] }}</p>
        <p class="s-stat-meta">unidades</p>
    </div>
    <div class="s-stat">
        <p class="s-stat-label">Valor em estoque</p>
        <p class="s-stat-value text-lg sm:text-2xl">R$ {{ number_format($stats['available_value'], 0, ',', '.') }}</p>
        <p class="s-stat-meta">disponível</p>
    </div>
    <div class="s-stat">
        <p class="s-stat-label">Vendidos</p>
        <p class="s-stat-value">{{ $stats['sold_count'] }}</p>
        <p class="s-stat-meta">este mês</p>
    </div>
    <div class="s-stat">
        <p class="s-stat-label">Repasse</p>
        <p class="s-stat-value text-lg sm:text-2xl">R$ {{ number_format($stats['sold_value'], 0, ',', '.') }}</p>
        <p class="s-stat-meta">este mês</p>
    </div>
</div>

{{-- Quick action --}}
<div class="mb-6">
    <a href="{{ route('supplier.stock.batch-create') }}" class="s-btn s-btn-primary w-full sm:w-auto">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Nova Entrada
    </a>
</div>

{{-- Recent items --}}
<div class="s-card">
    <div class="s-card-pad border-b" style="border-color: var(--apple-separator);">
        <h2 class="text-base font-semibold" style="letter-spacing: -0.01em;">Últimas Entradas</h2>
    </div>

    @if($recentItems->isEmpty())
        <div class="s-card-pad text-center py-10">
            <p style="color: var(--apple-text-secondary);">Nenhum item cadastrado ainda.</p>
            <a href="{{ route('supplier.stock.batch-create') }}" class="s-btn-ghost inline-block mt-3">
                Cadastrar primeira entrada →
            </a>
        </div>
    @else
        {{-- Mobile cards --}}
        <div class="lg:hidden">
            @foreach($recentItems as $item)
            <a href="{{ route('supplier.stock.show', $item) }}" class="s-item-card">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-sm truncate">{{ $item->name }}</p>
                        <p class="text-xs mt-0.5 truncate" style="color: var(--apple-text-secondary);">
                            {{ collect([$item->storage, $item->color])->filter()->join(' · ') }}
                        </p>
                        <p class="text-xs font-mono mt-1" style="color: var(--apple-text-tertiary);">
                            {{ $item->imei ?? $item->serial_number ?? '—' }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        @if($item->status->value === 'available')
                            <span class="s-badge s-badge-green">Disponível</span>
                        @elseif($item->status->value === 'sold')
                            <span class="s-badge s-badge-gray">Vendido</span>
                        @else
                            <span class="s-badge s-badge-red">Devolvido</span>
                        @endif
                        <p class="text-sm font-semibold mt-1.5">R$ {{ number_format($item->supplier_cost, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">Produto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">IMEI/Serial</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">Custo</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">Data</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentItems as $item)
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-medium">{{ $item->name }}</p>
                            <p class="text-xs" style="color: var(--apple-text-secondary);">{{ collect([$item->storage, $item->color])->filter()->join(' · ') }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-sm font-mono" style="color: var(--apple-text-secondary);">{{ $item->imei ?? $item->serial_number ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if($item->status->value === 'available')
                                <span class="s-badge s-badge-green">Disponível</span>
                            @elseif($item->status->value === 'sold')
                                <span class="s-badge s-badge-gray">Vendido</span>
                            @else
                                <span class="s-badge s-badge-red">Devolvido</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-sm font-semibold">R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-sm" style="color: var(--apple-text-secondary);">{{ $item->batch->received_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('supplier.stock.show', $item) }}" class="s-btn-ghost">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="s-card-pad border-t" style="border-color: var(--apple-separator);">
            <a href="{{ route('supplier.stock.index') }}" class="s-btn-ghost">Ver todo o estoque →</a>
        </div>
    @endif
</div>
@endsection
