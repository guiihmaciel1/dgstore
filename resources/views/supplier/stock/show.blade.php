@extends('layouts.supplier')

@section('title', 'Detalhes do Item')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('supplier.stock.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
        <h1 class="text-3xl font-semibold text-gray-900">Detalhes do Item</h1>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações do Produto</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Produto</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item->name }}</dd>
                    </div>
                    @if($item->storage)
                    <div>
                        <dt class="text-sm text-gray-500">Armazenamento</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item->storage }}</dd>
                    </div>
                    @endif
                    @if($item->color)
                    <div>
                        <dt class="text-sm text-gray-500">Cor</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item->color }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500">Condição</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item->condition->label() }}</dd>
                    </div>
                    @if($item->imei)
                    <div>
                        <dt class="text-sm text-gray-500">IMEI</dt>
                        <dd class="text-sm font-medium text-gray-900 font-mono">{{ $item->imei }}</dd>
                    </div>
                    @endif
                    @if($item->serial_number)
                    <div>
                        <dt class="text-sm text-gray-500">Serial Number</dt>
                        <dd class="text-sm font-medium text-gray-900 font-mono">{{ $item->serial_number }}</dd>
                    </div>
                    @endif
                    @if($item->battery_health)
                    <div>
                        <dt class="text-sm text-gray-500">Saúde da Bateria</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item->battery_health }}%</dd>
                    </div>
                    @endif
                    @if($item->condition->value === 'used')
                    <div>
                        <dt class="text-sm text-gray-500">Acessórios</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ $item->has_box ? '✓ Caixa' : '✗ Caixa' }}
                            {{ $item->has_cable ? '✓ Cabo' : '✗ Cabo' }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações Financeiras</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Custo / Repasse</dt>
                        <dd class="text-lg font-semibold text-gray-900">R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}</dd>
                    </div>
                    @if($item->suggested_price)
                    <div>
                        <dt class="text-sm text-gray-500">Preço Sugerido</dt>
                        <dd class="text-lg font-semibold text-gray-900">R$ {{ number_format($item->suggested_price, 2, ',', '.') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd>
                            @if($item->status->value === 'available')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Disponível
                                </span>
                            @elseif($item->status->value === 'sold')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Vendido
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    Devolvido
                                </span>
                            @endif
                        </dd>
                    </div>
                    @if($item->sold_at)
                    <div>
                        <dt class="text-sm text-gray-500">Vendido em</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item->sold_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500">Lote</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item->batch->batch_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Data de Entrada</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item->batch->received_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
                
                @if($item->status->value === 'available')
                <div class="mt-6">
                    <a href="{{ route('supplier.stock.edit', $item) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                        Editar Preços
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    @if($item->movements->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Histórico de Movimentações</h2>
        
        <div class="space-y-4">
            @foreach($item->movements as $movement)
            <div class="flex items-start space-x-3 pb-4 border-b border-gray-100 last:border-0">
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                    {{ $movement->type->value === 'in' ? 'bg-blue-100 text-blue-600' : ($movement->type->value === 'out' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') }}">
                    @if($movement->type->value === 'in')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    @elseif($movement->type->value === 'out')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $movement->type->label() }}</p>
                    @if($movement->notes)
                        <p class="text-sm text-gray-500 mt-1">{{ $movement->notes }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">{{ $movement->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
