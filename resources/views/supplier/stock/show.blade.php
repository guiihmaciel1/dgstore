@extends('layouts.supplier')

@section('title', 'Detalhes')

@section('content')
<a href="{{ route('supplier.stock.index') }}" class="s-back">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Voltar
</a>

<div class="mb-5">
    <h1 class="s-title">{{ $item->name }}</h1>
    <p class="s-subtitle">{{ collect([$item->storage, $item->color])->filter()->join(' · ') }}</p>
</div>

<div class="s-card s-card-pad mb-4">
    <div class="flex items-center gap-2 mb-4">
        <span class="s-badge s-badge-green">Disponível</span>
        <span class="s-badge {{ $item->condition->value === 'new' ? 's-badge-green' : 's-badge-yellow' }}">{{ $item->condition->label() }}</span>
    </div>

    <dl class="space-y-3.5">
        @if($item->imei)
        <div class="flex justify-between gap-4">
            <dt class="text-sm shrink-0" style="color: var(--apple-text-secondary);">IMEI</dt>
            <dd class="text-sm font-mono font-medium text-right break-all">{{ $item->imei }}</dd>
        </div>
        @endif
        @if($item->serial_number)
        <div class="flex justify-between gap-4">
            <dt class="text-sm shrink-0" style="color: var(--apple-text-secondary);">Serial</dt>
            <dd class="text-sm font-mono font-medium text-right break-all">{{ $item->serial_number }}</dd>
        </div>
        @endif
        @if($item->storage)
        <div class="flex justify-between gap-4">
            <dt class="text-sm" style="color: var(--apple-text-secondary);">Armazenamento</dt>
            <dd class="text-sm font-medium">{{ $item->storage }}</dd>
        </div>
        @endif
        @if($item->color)
        <div class="flex justify-between gap-4">
            <dt class="text-sm" style="color: var(--apple-text-secondary);">Cor</dt>
            <dd class="text-sm font-medium">{{ $item->color }}</dd>
        </div>
        @endif
        @if($item->battery_health)
        <div class="flex justify-between gap-4">
            <dt class="text-sm" style="color: var(--apple-text-secondary);">Bateria</dt>
            <dd class="text-sm font-medium">{{ $item->battery_health }}%</dd>
        </div>
        @endif
        @if($item->condition->value === 'used')
        <div class="flex justify-between gap-4">
            <dt class="text-sm" style="color: var(--apple-text-secondary);">Acessórios</dt>
            <dd class="text-sm font-medium">{{ $item->has_box ? 'Caixa ✓' : 'Caixa ✗' }} · {{ $item->has_cable ? 'Cabo ✓' : 'Cabo ✗' }}</dd>
        </div>
        @endif
        <div class="flex justify-between gap-4">
            <dt class="text-sm" style="color: var(--apple-text-secondary);">Lote</dt>
            <dd class="text-sm font-medium">{{ $item->batch->batch_code }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-sm" style="color: var(--apple-text-secondary);">Entrada</dt>
            <dd class="text-sm font-medium">{{ $item->batch->received_at->format('d/m/Y') }}</dd>
        </div>
    </dl>
</div>

@if($item->movements->isNotEmpty())
<div class="s-card s-card-pad">
    <h2 class="text-base font-semibold mb-4" style="letter-spacing: -0.01em;">Histórico</h2>
    <div class="space-y-0">
        @foreach($item->movements as $movement)
        <div class="flex gap-3 py-3 {{ !$loop->last ? 'border-b' : '' }}" style="border-color: var(--apple-separator);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-blue-50 text-blue-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium">{{ $movement->type->label() }}</p>
                @if($movement->reason)
                    <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">{{ $movement->reason }}</p>
                @endif
                <p class="text-xs mt-1" style="color: var(--apple-text-tertiary);">{{ $movement->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
