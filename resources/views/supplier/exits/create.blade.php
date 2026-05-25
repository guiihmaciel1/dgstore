@extends('layouts.supplier')

@section('title', 'Registrar Saída')

@section('content')
<a href="{{ route('supplier.exits.index') }}" class="s-back">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Voltar
</a>

<div class="mb-5">
    <h1 class="s-title">Registrar Saída</h1>
    <p class="s-subtitle">Selecione o aparelho que saiu do estoque</p>
</div>

<div class="s-card s-card-pad mb-4">
    <form method="GET" action="{{ route('supplier.exits.create') }}" class="flex gap-2">
        <input type="search" name="search" value="{{ $search }}"
               placeholder="Buscar por IMEI, produto..."
               class="s-input flex-1 text-sm py-2.5">
        <button type="submit" class="s-btn s-btn-secondary shrink-0 px-4">Buscar</button>
    </form>
</div>

@if($selected)
    {{-- Confirmação --}}
    <div class="s-card s-card-pad mb-4">
        <h2 class="text-base font-semibold mb-4" style="letter-spacing: -0.01em;">Confirmar saída</h2>

        <div class="rounded-xl p-4 mb-5" style="background: var(--apple-bg);">
            <p class="font-semibold">{{ $selected->name }}</p>
            <p class="text-sm mt-1" style="color: var(--apple-text-secondary);">
                {{ collect([$selected->storage, $selected->color, $selected->condition->label()])->filter()->join(' · ') }}
            </p>
            <p class="text-sm font-mono mt-2" style="color: var(--apple-text-tertiary);">
                {{ $selected->imei ?? $selected->serial_number ?? '—' }}
            </p>
            <div class="mt-4 pt-4 border-t" style="border-color: var(--apple-separator);">
                <p class="text-xs font-medium" style="color: var(--apple-text-secondary);">Valor de repasse</p>
                <p class="text-2xl font-bold mt-0.5" style="letter-spacing: -0.03em; color: var(--apple-green);">
                    R$ {{ number_format($selected->supplier_cost, 2, ',', '.') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('supplier.exits.store') }}">
            @csrf
            <input type="hidden" name="item_id" value="{{ $selected->id }}">

            <div class="mb-5">
                <label class="s-label">Observações (opcional)</label>
                <textarea name="notes" rows="2" placeholder="Ex: Entregue na loja"
                          class="s-input resize-none text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <a href="{{ route('supplier.exits.create', ['search' => $search]) }}" class="s-btn s-btn-secondary w-full sm:w-auto text-center">Cancelar</a>
                <button type="submit" class="s-btn s-btn-primary w-full sm:w-auto"
                        onclick="return confirm('Confirmar saída deste aparelho?')">
                    Confirmar Saída
                </button>
            </div>
        </form>
    </div>
@endif

{{-- Lista para seleção --}}
<div class="s-card">
    <div class="s-card-pad border-b" style="border-color: var(--apple-separator);">
        <h2 class="text-base font-semibold" style="letter-spacing: -0.01em;">
            Aparelhos disponíveis
            <span style="color: var(--apple-text-secondary); font-weight: 500;">({{ $available->count() }})</span>
        </h2>
    </div>

    @if($available->isEmpty())
        <div class="s-card-pad text-center py-10" style="color: var(--apple-text-secondary);">
            Nenhum aparelho disponível
        </div>
    @else
        <div>
            @foreach($available as $item)
            <a href="{{ route('supplier.exits.create', ['item' => $item->id, 'search' => $search]) }}"
               class="s-item-card block {{ $selected?->id === $item->id ? 'bg-blue-50' : '' }}">
                <div class="flex justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold">{{ $item->name }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">
                            {{ collect([$item->storage, $item->color])->filter()->join(' · ') }}
                        </p>
                        <p class="text-xs font-mono mt-1" style="color: var(--apple-text-tertiary);">
                            {{ $item->imei ?? $item->serial_number ?? '—' }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold">R$ {{ number_format($item->supplier_cost, 0, ',', '.') }}</p>
                        <p class="text-xs mt-1" style="color: var(--apple-blue);">Selecionar →</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
