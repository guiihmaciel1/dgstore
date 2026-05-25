@extends('layouts.supplier')

@section('title', 'Editar Item')

@section('content')
<a href="{{ route('supplier.stock.show', $item) }}" class="s-back">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Voltar
</a>

<div class="mb-5">
    <h1 class="s-title">Editar Preços</h1>
    <p class="s-subtitle">Atualize o custo e preço sugerido</p>
</div>

<div class="s-card s-card-pad max-w-lg">
    <div class="rounded-xl p-4 mb-5" style="background: var(--apple-bg);">
        <p class="font-semibold text-sm">{{ $item->name }}</p>
        <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">
            {{ collect([$item->storage, $item->color])->filter()->join(' · ') }}
        </p>
        <p class="text-xs font-mono mt-1" style="color: var(--apple-text-tertiary);">{{ $item->imei ?? $item->serial_number }}</p>
    </div>

    <form method="POST" action="{{ route('supplier.stock.update', $item) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="s-label">Custo / Repasse <span style="color: var(--apple-red);">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm" style="color: var(--apple-text-secondary);">R$</span>
                    <input type="number" name="supplier_cost" value="{{ old('supplier_cost', $item->supplier_cost) }}"
                           step="0.01" min="0" required inputmode="decimal"
                           class="s-input pl-10">
                </div>
                <p class="text-xs mt-1.5" style="color: var(--apple-text-tertiary);">Valor que você recebe quando vendido</p>
            </div>

            <div>
                <label class="s-label">Preço Sugerido</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm" style="color: var(--apple-text-secondary);">R$</span>
                    <input type="number" name="suggested_price" value="{{ old('suggested_price', $item->suggested_price) }}"
                           step="0.01" min="0" inputmode="decimal"
                           class="s-input pl-10">
                </div>
                <p class="text-xs mt-1.5" style="color: var(--apple-text-tertiary);">Sugestão para venda ao cliente final</p>
            </div>

            <div>
                <label class="s-label">Observações</label>
                <textarea name="notes" rows="3" class="s-input resize-none">{{ old('notes', $item->notes) }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6">
            <a href="{{ route('supplier.stock.show', $item) }}" class="s-btn s-btn-secondary w-full sm:w-auto text-center">Cancelar</a>
            <button type="submit" class="s-btn s-btn-primary w-full sm:w-auto">Salvar</button>
        </div>
    </form>
</div>
@endsection
