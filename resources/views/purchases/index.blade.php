<x-app-layout>
    <x-slot name="title">Compras</x-slot>
    @php
        $tabs = ['open' => 'A receber', 'received' => 'Recebidas', 'cancelled' => 'Canceladas', 'all' => 'Todas'];
        $statusTone = [
            'pending' => 'bg-amber-500/15 text-amber-300',
            'partial' => 'bg-sky-500/15 text-sky-300',
            'received' => 'bg-emerald-500/15 text-emerald-300',
            'cancelled' => 'bg-surface-elevated text-dg-400',
        ];
    @endphp
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            @if(session('success'))<div class="mb-4"><x-alert type="success">{{ session('success') }}</x-alert></div>@endif
            @if(session('error'))<div class="mb-4"><x-alert type="error">{{ session('error') }}</x-alert></div>@endif

            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-dg-50">Compras</h1>
                    <p class="mt-1 text-sm text-dg-400">Produtos pagos que ainda não chegaram. Quando chegar, dê entrada no estoque com o IMEI.</p>
                </div>
                <a href="{{ route('purchases.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Nova compra
                </a>
            </div>

            {{-- Resumo --}}
            <dl class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
                <div class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
                    <dt class="text-xs text-dg-400 sm:text-sm">A receber</dt>
                    <dd class="mt-1 text-2xl font-bold text-dg-50">{{ $summary['units'] }} <span class="text-sm font-medium text-dg-400">{{ $summary['units'] === 1 ? 'item' : 'itens' }} em {{ $summary['purchases'] }} {{ $summary['purchases'] === 1 ? 'compra' : 'compras' }}</span></dd>
                </div>
                <div class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
                    <dt class="text-xs text-dg-400 sm:text-sm">Em trânsito (custo)</dt>
                    <dd class="mt-1 text-2xl font-bold text-dg-50">R$ {{ number_format($summary['cost'], 2, ',', '.') }}</dd>
                </div>
                <div class="rounded-xl border p-4 sm:p-5 {{ $summary['late'] > 0 ? 'border-red-500/30 bg-red-500/10' : 'border-border bg-surface-raised' }}">
                    <dt class="text-xs sm:text-sm {{ $summary['late'] > 0 ? 'text-red-300' : 'text-dg-400' }}">Atrasadas</dt>
                    <dd class="mt-1 text-2xl font-bold {{ $summary['late'] > 0 ? 'text-red-200' : 'text-dg-50' }}">{{ $summary['late'] }} <span class="text-sm font-medium {{ $summary['late'] > 0 ? 'text-red-300' : 'text-dg-400' }}">passaram da previsão</span></dd>
                </div>
            </dl>

            {{-- Filtros --}}
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <nav class="flex gap-1 overflow-x-auto" aria-label="Situação">
                    @foreach($tabs as $key => $label)
                        <a href="{{ route('purchases.index', array_filter(['status' => $key, 'q' => $search])) }}"
                           class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $status === $key ? 'bg-surface-elevated text-dg-50' : 'text-dg-400 hover:bg-surface-raised hover:text-dg-100' }}"
                           @if($status === $key) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
                <form method="GET" class="flex gap-2 sm:w-80">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="search" name="q" value="{{ $search }}" placeholder="Número, fornecedor ou modelo"
                           class="w-full rounded-lg border border-border-strong px-3 py-2 text-sm text-dg-100 placeholder:text-dg-500 focus:border-indigo-400 focus:ring-0">
                </form>
            </div>

            {{-- Lista --}}
            <div class="overflow-hidden rounded-xl border border-border bg-surface-raised">
                @if($purchases->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm text-dg-200">{{ $status === 'open' ? 'Nenhuma compra aguardando chegada.' : 'Nenhuma compra encontrada.' }}</p>
                        <p class="mt-1 text-xs text-dg-400">Registre a compra assim que pagar: o valor sai do caixa e entra como "em trânsito" até o produto chegar.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[46rem] text-sm">
                            <thead>
                                <tr class="border-b border-border text-left text-[0.6875rem] font-medium uppercase tracking-wide text-dg-400">
                                    <th scope="col" class="px-4 py-3 font-medium sm:px-5">Compra</th>
                                    <th scope="col" class="px-3 py-3 font-medium">Fornecedor</th>
                                    <th scope="col" class="px-3 py-3 font-medium">Situação</th>
                                    <th scope="col" class="px-3 py-3 font-medium">Recebidos</th>
                                    <th scope="col" class="px-3 py-3 text-right font-medium">Total</th>
                                    <th scope="col" class="px-4 py-3 text-right font-medium sm:px-5">Previsão</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($purchases as $purchase)
                                    @php $progress = $purchase->units_total > 0 ? round($purchase->units_received / $purchase->units_total * 100) : 0; @endphp
                                    <tr class="cursor-pointer transition-colors hover:bg-surface-overlay" onclick="window.location='{{ route('purchases.show', $purchase) }}'">
                                        <td class="px-4 py-3 sm:px-5">
                                            <a href="{{ route('purchases.show', $purchase) }}" class="font-medium text-dg-50 underline-offset-4 hover:underline">{{ $purchase->number }}</a>
                                            <span class="block text-[0.6875rem] text-dg-400">{{ $purchase->purchased_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-dg-200">{{ $purchase->supplierLabel() }}</td>
                                        <td class="px-3 py-3">
                                            <span class="rounded-full px-2 py-0.5 text-[0.6875rem] font-medium {{ $statusTone[$purchase->status] ?? '' }}">{{ $purchase->statusLabel() }}</span>
                                            @if($purchase->isLate())<span class="ml-1 text-[0.6875rem] font-medium text-red-300">atrasada</span>@endif
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="text-xs tabular-nums text-dg-200">{{ $purchase->units_received }} de {{ $purchase->units_total }}</span>
                                            <span class="mt-1 block h-1 w-24 rounded-full bg-surface-elevated" aria-hidden="true"><span class="block h-full rounded-full bg-emerald-400" style="width: {{ $progress }}%"></span></span>
                                        </td>
                                        <td class="px-3 py-3 text-right font-semibold tabular-nums text-dg-100">R$ {{ number_format((float) $purchase->total, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right tabular-nums sm:px-5 {{ $purchase->isLate() ? 'text-red-300' : 'text-dg-300' }}">{{ $purchase->expected_at?->format('d/m/Y') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="mt-4">{{ $purchases->links() }}</div>
        </div>
    </div>
</x-app-layout>
