@extends('layouts.supplier')

@section('title', 'Meu Estoque')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-5">
    <div>
        <h1 class="s-title">Meu Estoque</h1>
        <p class="s-subtitle">Aparelhos disponíveis no momento</p>
    </div>
    <a href="{{ route('supplier.stock.batch-create') }}" class="s-btn s-btn-primary w-full sm:w-auto shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Nova Entrada
    </a>
</div>

<div class="s-card mb-4">
    <div class="s-card-pad">
        <form method="GET" class="flex gap-2">
            <input type="search" name="search" value="{{ $search }}"
                   placeholder="Buscar IMEI, produto..."
                   class="s-input flex-1 text-sm py-2.5">
            <button type="submit" class="s-btn s-btn-secondary shrink-0 px-4">Buscar</button>
        </form>
    </div>
</div>

<div class="s-card">
    @if($items->isEmpty())
        <div class="s-card-pad text-center py-12">
            <svg class="mx-auto w-10 h-10 mb-3" style="color: var(--apple-text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p style="color: var(--apple-text-secondary);">Nenhum aparelho disponível</p>
            <a href="{{ route('supplier.stock.batch-create') }}" class="s-btn-ghost inline-block mt-3">Cadastrar entrada →</a>
        </div>
    @else
        <div class="lg:hidden">
            @foreach($items as $item)
            <a href="{{ route('supplier.stock.show', $item) }}" class="s-item-card block">
                <p class="font-semibold text-sm">{{ $item->name }}</p>
                <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">
                    {{ collect([$item->storage, $item->color])->filter()->join(' · ') }}
                </p>
                <p class="text-xs font-mono mt-1" style="color: var(--apple-text-tertiary);">
                    {{ $item->imei ?? $item->serial_number ?? '—' }}
                </p>
                <span class="s-badge {{ $item->condition->value === 'new' ? 's-badge-green' : 's-badge-yellow' }} mt-2 inline-flex">
                    {{ $item->condition->label() }}
                </span>
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
                    @foreach($items as $item)
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
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
