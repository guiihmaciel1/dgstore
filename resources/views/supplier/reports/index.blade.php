@extends('layouts.supplier')

@section('title', 'Relatórios')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-semibold text-gray-900">Relatórios</h1>
        <p class="mt-1 text-sm text-gray-500">Acompanhe seu estoque e vendas</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
        
        <form method="GET" class="flex items-end space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                <input type="date" name="from" value="{{ $from }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                <input type="date" name="to" value="{{ $to }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                Filtrar
            </button>
        </form>
    </div>
    
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-900">Estoque Disponível</h3>
                <span class="text-2xl font-bold text-green-600">{{ $available->count() }}</span>
            </div>
            <p class="text-sm text-gray-500">Valor Total:</p>
            <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($availableTotal, 2, ',', '.') }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-900">Vendidos no Período</h3>
                <span class="text-2xl font-bold text-blue-600">{{ $sold->count() }}</span>
            </div>
            <p class="text-sm text-gray-500">Repasse Total:</p>
            <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($soldTotal, 2, ',', '.') }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Estoque Disponível</h2>
        </div>
        
        @if($available->isEmpty())
            <div class="p-8 text-center text-gray-500">
                Nenhum item disponível
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IMEI/Serial</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condição</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Custo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($available as $item)
                        <tr class="hover:bg-gray-50">
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
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $item->condition->value === 'new' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->condition->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Vendidos no Período</h2>
        </div>
        
        @if($sold->isEmpty())
            <div class="p-8 text-center text-gray-500">
                Nenhuma venda no período selecionado
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IMEI/Serial</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Venda</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Repasse</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sold as $item)
                        <tr class="hover:bg-gray-50">
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
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $item->sold_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-green-700">
                                R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Relatório para WhatsApp</h2>
        <p class="text-sm text-gray-600 mb-4">Copie o relatório abaixo para enviar via WhatsApp:</p>
        
        <div class="relative">
            <textarea 
                id="whatsappReport"
                readonly 
                rows="15"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono whitespace-pre-wrap"
            >{{ $whatsappReport }}</textarea>
            
            <button 
                onclick="copyReport()"
                class="absolute top-2 right-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Copiar
            </button>
        </div>
        
        <div id="copyFeedback" class="hidden mt-2 text-sm text-green-600">
            ✓ Relatório copiado para a área de transferência!
        </div>
    </div>
</div>

<script>
function copyReport() {
    const textarea = document.getElementById('whatsappReport');
    const feedback = document.getElementById('copyFeedback');
    
    textarea.select();
    document.execCommand('copy');
    
    feedback.classList.remove('hidden');
    setTimeout(() => feedback.classList.add('hidden'), 3000);
}
</script>
@endsection
