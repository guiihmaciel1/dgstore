@extends('layouts.supplier')

@section('title', 'Meu Estoque')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Meu Estoque</h1>
            <p class="mt-1 text-sm text-gray-500">Gerencie seus produtos consignados</p>
        </div>
        <a href="{{ route('supplier.stock.batch-create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nova Entrada
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-4">
                    <a href="{{ route('supplier.stock.index', ['status' => 'available']) }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $status === 'available' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        Disponível
                    </a>
                    <a href="{{ route('supplier.stock.index', ['status' => 'sold']) }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $status === 'sold' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        Vendido
                    </a>
                    <a href="{{ route('supplier.stock.index', ['status' => 'returned']) }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $status === 'returned' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        Devolvido
                    </a>
                </div>
                
                <form method="GET" class="flex items-center space-x-2">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}"
                        placeholder="Buscar por IMEI, nome..." 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    >
                    <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                        Buscar
                    </button>
                </form>
            </div>
        </div>
        
        @if($items->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 mb-2">Nenhum item encontrado</p>
                @if($status === 'available')
                    <a href="{{ route('supplier.stock.batch-create') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        Cadastrar primeiro item
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IMEI/Serial</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Custo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Sugerido</th>
                            @if($status === 'sold')
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendido em</th>
                            @endif
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($item->storage) {{ $item->storage }} @endif
                                    @if($item->color) - {{ $item->color }} @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                {{ $item->imei ?? $item->serial_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $item->condition->value === 'new' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->condition->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                                R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($item->suggested_price)
                                    R$ {{ number_format($item->suggested_price, 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            @if($status === 'sold')
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $item->sold_at ? $item->sold_at->format('d/m/Y') : '-' }}
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                <a href="{{ route('supplier.stock.show', $item) }}" class="text-blue-600 hover:text-blue-900">
                                    Ver
                                </a>
                                @if($status === 'available')
                                    <a href="{{ route('supplier.stock.edit', $item) }}" class="text-gray-600 hover:text-gray-900">
                                        Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
