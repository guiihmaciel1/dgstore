@extends('layouts.supplier')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="s-title">Olá, {{ explode(' ', auth('supplier')->user()->supplier->name)[0] }}</h1>
    <p class="s-subtitle">Controle físico do seu estoque</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-6">
    <div class="s-stat">
        <p class="s-stat-label">Disponível</p>
        <p class="s-stat-value">{{ $stats['available_count'] }}</p>
        <p class="s-stat-meta">unidades em estoque</p>
    </div>
</div>

<div class="mb-6">
    <a href="{{ route('supplier.stock.batch-create') }}" class="s-btn s-btn-primary w-full sm:w-auto">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Nova Entrada
    </a>
</div>

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
        <div class="lg:hidden">
            @foreach($recentItems as $item)
            <a href="{{ route('supplier.stock.show', $item) }}" class="s-item-card block">
                <p class="font-semibold text-sm truncate">{{ $item->name }}</p>
                <p class="text-xs mt-0.5 truncate" style="color: var(--apple-text-secondary);">
                    {{ collect([$item->storage, $item->color])->filter()->join(' · ') }}
                </p>
                <p class="text-xs font-mono mt-1" style="color: var(--apple-text-tertiary);">
                    {{ $item->imei ?? $item->serial_number ?? '—' }}
                </p>
            </a>
            @endforeach
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">Produto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">IMEI/Serial</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">Condição</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--apple-text-secondary);">Entrada</th>
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
                            <span class="s-badge {{ $item->condition->value === 'new' ? 's-badge-green' : 's-badge-yellow' }}">{{ $item->condition->label() }}</span>
                        </td>
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
