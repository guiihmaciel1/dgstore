@extends('layouts.supplier')

@section('title', 'Editar Item')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('supplier.stock.show', $item) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
        <h1 class="text-3xl font-semibold text-gray-900">Editar Item</h1>
        <p class="mt-1 text-sm text-gray-500">Atualize os preços do seu produto</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ $item->name }}</h3>
            <p class="text-sm text-gray-600">
                @if($item->storage) {{ $item->storage }} @endif
                @if($item->color) - {{ $item->color }} @endif
            </p>
            <p class="text-xs text-gray-500 mt-1 font-mono">{{ $item->imei ?? $item->serial_number }}</p>
        </div>
        
        <form method="POST" action="{{ route('supplier.stock.update', $item) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Custo / Repasse <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">R$</span>
                        <input 
                            type="number" 
                            name="supplier_cost" 
                            value="{{ old('supplier_cost', $item->supplier_cost) }}" 
                            step="0.01" 
                            min="0" 
                            required
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Valor que você receberá quando o item for vendido</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Preço Sugerido de Venda
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">R$</span>
                        <input 
                            type="number" 
                            name="suggested_price" 
                            value="{{ old('suggested_price', $item->suggested_price) }}" 
                            step="0.01" 
                            min="0"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Sugestão de preço para venda ao cliente final</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observações
                    </label>
                    <textarea 
                        name="notes" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >{{ old('notes', $item->notes) }}</textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('supplier.stock.show', $item) }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
