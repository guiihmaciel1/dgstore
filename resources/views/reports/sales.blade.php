<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('reports.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Relatório de Vendas
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('reports.sales') }}" class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Inicial</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Final</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('reports.sales.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        Exportar PDF
                    </a>
                </form>
            </x-card>

            <!-- Resumo -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <x-stat-card title="Total de Vendas" :value="$report['summary']['total_sales']" color="blue" />
                <x-stat-card title="Faturamento" :value="'R$ ' . number_format($report['summary']['total_revenue'], 2, ',', '.')" color="green" />
                <x-stat-card title="Descontos" :value="'R$ ' . number_format($report['summary']['total_discount'], 2, ',', '.')" color="red" />
                <x-stat-card title="Ticket Médio" :value="'R$ ' . number_format($report['summary']['average_ticket'], 2, ',', '.')" color="indigo" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Por Forma de Pagamento -->
                <x-card title="Por Forma de Pagamento">
                    @if($report['by_payment_method']->count() > 0)
                        <div class="space-y-3">
                            @foreach($report['by_payment_method'] as $method => $data)
                                @php $methodEnum = \App\Domain\Sale\Enums\PaymentMethod::from($method); @endphp
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $methodEnum->label() }}</span>
                                    <div class="text-right">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($data['total'], 2, ',', '.') }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ $data['count'] }} vendas)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma venda no período.</p>
                    @endif
                </x-card>

                <!-- Por Vendedor -->
                <x-card title="Por Vendedor">
                    @if($report['by_seller']->count() > 0)
                        <div class="space-y-3">
                            @foreach($report['by_seller'] as $seller)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $seller['seller_name'] }}</span>
                                    <div class="text-right">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($seller['total'], 2, ',', '.') }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ $seller['count'] }} vendas)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma venda no período.</p>
                    @endif
                </x-card>
            </div>

            <!-- Lista de Vendas -->
            <x-card title="Vendas do Período" :padding="false">
                <x-data-table :headers="['Venda', 'Cliente', 'Vendedor', 'Total', 'Status', 'Data']">
                    @forelse($report['sales'] as $sale)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('sales.show', $sale) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ $sale->sale_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $sale->customer?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $sale->user?->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $sale->formatted_total }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :color="$sale->payment_status->color()">{{ $sale->payment_status->label() }}</x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $sale->sold_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Nenhuma venda no período.
                            </td>
                        </tr>
                    @endforelse
                </x-data-table>
            </x-card>
        </div>
    </div>
</x-app-layout>
