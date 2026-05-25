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

{{-- Status + price hero --}}
<div class="s-card s-card-pad mb-4">
    <div class="flex items-center justify-between mb-4">
        @if($item->status->value === 'available')
            <span class="s-badge s-badge-green">Disponível</span>
        @elseif($item->status->value === 'sold')
            <span class="s-badge s-badge-gray">Vendido</span>
        @else
            <span class="s-badge s-badge-red">Devolvido</span>
        @endif
        <span class="s-badge {{ $item->condition->value === 'new' ? 's-badge-green' : 's-badge-yellow' }}">{{ $item->condition->label() }}</span>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs font-medium" style="color: var(--apple-text-secondary);">Custo / Repasse</p>
            <p class="text-2xl font-bold mt-0.5" style="letter-spacing: -0.03em;">R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}</p>
        </div>
        @if($item->suggested_price)
        <div>
            <p class="text-xs font-medium" style="color: var(--apple-text-secondary);">Preço Sugerido</p>
            <p class="text-2xl font-bold mt-0.5" style="letter-spacing: -0.03em;">R$ {{ number_format($item->suggested_price, 2, ',', '.') }}</p>
        </div>
        @endif
    </div>

    @if($item->status->value === 'available')
    <a href="{{ route('supplier.stock.edit', $item) }}" class="s-btn s-btn-primary w-full mt-4 text-center">
        Editar Preços
    </a>
    @endif
</div>

{{-- Details --}}
<div class="s-card s-card-pad mb-4">
    <h2 class="text-base font-semibold mb-4" style="letter-spacing: -0.01em;">Informações</h2>
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
        @if($item->sold_at)
        <div class="flex justify-between gap-4">
            <dt class="text-sm" style="color: var(--apple-text-secondary);">Vendido em</dt>
            <dd class="text-sm font-medium">{{ $item->sold_at->format('d/m/Y H:i') }}</dd>
        </div>
        @endif
    </dl>
</div>

{{-- Timeline --}}
@if($item->movements->isNotEmpty())
<div class="s-card s-card-pad">
    <h2 class="text-base font-semibold mb-4" style="letter-spacing: -0.01em;">Histórico</h2>
    <div class="space-y-0">
        @foreach($item->movements as $movement)
        <div class="flex gap-3 py-3 {{ !$loop->last ? 'border-b' : '' }}" style="border-color: var(--apple-separator);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                {{ $movement->type->value === 'in' ? 'bg-blue-50 text-blue-600' : ($movement->type->value === 'out' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600') }}">
                @if($movement->type->value === 'in')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                @elseif($movement->type->value === 'out')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium">{{ $movement->type->label() }}</p>
                @if($movement->notes)
                    <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">{{ $movement->notes }}</p>
                @endif
                <p class="text-xs mt-1" style="color: var(--apple-text-tertiary);">{{ $movement->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
