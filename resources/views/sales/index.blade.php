<x-app-layout>
    <x-slot name="title">Vendas</x-slot>
    @php
        $field = 'w-full rounded-lg border border-border bg-surface-overlay px-3 py-2 text-sm text-dg-100 placeholder-dg-500 focus:border-dg-400 focus:ring-2 focus:ring-dg-400/20';
        $fieldLabel = 'mb-1 block text-xs font-medium text-dg-400';
        $money = fn ($v) => 'R$ ' . number_format((float) $v, 2, ',', '.');
        $canSeeFinancials = auth()->user()->canViewFinancials();
        $activeFilters = collect($filters)->filter()->count();
        $statusTone = [
            'paid' => 'bg-emerald-500/15 text-emerald-400',
            'pending' => 'bg-amber-500/15 text-amber-300',
            'partial' => 'bg-sky-500/15 text-sky-300',
            'cancelled' => 'bg-red-500/15 text-red-400',
        ];
    @endphp
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            <x-flash class="mb-4" />

            <x-page-header title="Vendas" subtitle="Histórico de vendas realizadas">
                <x-slot name="actions">
                    <a href="{{ route('sales.create') }}" class="dg-btn-primary font-semibold">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Nova venda
                    </a>
                </x-slot>
            </x-page-header>

            <div class="dg-card overflow-hidden">
                <!-- Filtros -->
                <form method="GET" action="{{ route('sales.index') }}" class="border-b border-border p-4">
                    <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
                        <div>
                            <label for="s-search" class="{{ $fieldLabel }}">Buscar</label>
                            <input id="s-search" type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nº da venda, cliente..." class="{{ $field }}">
                        </div>
                        <div>
                            <label for="s-status" class="{{ $fieldLabel }}">Status</label>
                            <select id="s-status" name="payment_status" class="{{ $field }}">
                                <option value="">Todos</option>
                                @foreach($paymentStatuses as $status)
                                    <option value="{{ $status->value }}" {{ ($filters['payment_status'] ?? '') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="s-method" class="{{ $fieldLabel }}">Pagamento</label>
                            <select id="s-method" name="payment_method" class="{{ $field }}">
                                <option value="">Todos</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->value }}" {{ ($filters['payment_method'] ?? '') === $method->value ? 'selected' : '' }}>{{ $method->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="s-type" class="{{ $fieldLabel }}">Tipo</label>
                            <select id="s-type" name="sale_type" class="{{ $field }}">
                                <option value="">Todos os tipos</option>
                                <option value="cliente_final" {{ ($filters['sale_type'] ?? '') === 'cliente_final' ? 'selected' : '' }}>Cliente Final</option>
                                <option value="repasse" {{ ($filters['sale_type'] ?? '') === 'repasse' ? 'selected' : '' }}>Repasse</option>
                            </select>
                        </div>
                        <div>
                            <label for="s-from" class="{{ $fieldLabel }}">Data início</label>
                            <input id="s-from" type="date" name="date_from" value="{{ $filters['date_from'] }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="s-to" class="{{ $fieldLabel }}">Data fim</label>
                            <input id="s-to" type="date" name="date_to" value="{{ $filters['date_to'] }}" class="{{ $field }}">
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2 text-sm font-semibold text-dg-950 transition-colors hover:bg-dg-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Filtrar
                        </button>
                        <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium text-dg-300 transition-colors hover:text-dg-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Limpar
                        </a>
                        @if($activeFilters)
                            <span class="text-xs text-dg-500">{{ $activeFilters }} {{ $activeFilters === 1 ? 'filtro ativo' : 'filtros ativos' }}</span>
                        @endif
                    </div>
                </form>

                <!-- Tabela -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="dg-table-header">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left">Venda</th>
                                <th scope="col" class="px-4 py-3 text-left">Cliente</th>
                                <th scope="col" class="px-4 py-3 text-left">Tipo</th>
                                <th scope="col" class="px-4 py-3 text-right">Total</th>
                                @if($canSeeFinancials)
                                    <th scope="col" class="px-3 py-3 text-right">Lucro bruto</th>
                                    <th scope="col" class="px-3 py-3 text-right">Comissão</th>
                                    <th scope="col" class="px-3 py-3 text-right">Lucro líq.</th>
                                @endif
                                <th scope="col" class="px-4 py-3 text-center">Status</th>
                                <th scope="col" class="px-4 py-3 text-left">Data</th>
                                <th scope="col" class="px-5 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @forelse($sales as $sale)
                                @php $itemsLabel = $sale->items->map(fn($i) => ($i->quantity > 1 ? $i->quantity.'x ' : '') . $i->product_name)->implode(', '); @endphp
                                <tr class="transition-colors hover:bg-surface-overlay/60">
                                    <td class="whitespace-nowrap px-5 py-3">
                                        <p class="text-xs font-semibold text-dg-300">{{ $sale->sale_number }}</p>
                                        <p class="text-[0.6875rem] text-dg-500">{{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-dg-100">{{ $sale->customer?->name ?? 'Cliente não informado' }}</p>
                                        <p class="mt-0.5 max-w-[18rem] truncate text-[0.6875rem] text-dg-500" title="{{ $itemsLabel }}">{{ $itemsLabel }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($sale->sale_type)
                                            <span class="inline-block whitespace-nowrap rounded-md px-2.5 py-1 text-xs font-semibold {{ $sale->sale_type->value === 'repasse' ? 'bg-violet-500/10 text-violet-300' : 'bg-sky-500/10 text-sky-300' }}">
                                                {{ $sale->sale_type->label() }}
                                            </span>
                                        @else
                                            <span class="text-xs text-dg-600">N/A</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-base font-semibold tabular-nums text-dg-50">{{ $sale->formatted_total }}</td>
                                    @if($canSeeFinancials)
                                        @php
                                            $commissionTotal = $sale->commissions->sum('commission_amount');
                                            $netProfit = $sale->profit - $commissionTotal;
                                        @endphp
                                        <td class="whitespace-nowrap px-3 py-3 text-right text-[0.8125rem] font-semibold tabular-nums {{ $sale->profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $money($sale->profit) }}</td>
                                        <td class="whitespace-nowrap px-3 py-3 text-right text-[0.8125rem] font-semibold tabular-nums {{ $commissionTotal > 0 ? 'text-violet-300' : 'text-dg-600' }}">{{ $commissionTotal > 0 ? $money($commissionTotal) : '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-3 text-right text-[0.8125rem] font-bold tabular-nums {{ $netProfit >= 0 ? 'text-emerald-300' : 'text-red-400' }}">{{ $money($netProfit) }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $statusTone[$sale->payment_status->value] ?? 'bg-white/5 text-dg-400' }}">
                                            {{ $sale->payment_status->label() }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm tabular-nums text-dg-400">{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @include('sales.partials.receipt-button')
                                            <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center whitespace-nowrap rounded-md border border-border bg-surface-overlay px-2.5 py-1 text-[0.7rem] font-medium text-dg-300 transition-colors hover:bg-surface-elevated hover:text-dg-100">
                                                Ver detalhes
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $canSeeFinancials ? 10 : 7 }}" class="px-5 py-12 text-center text-dg-400">Nenhuma venda encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($sales->count() > 0)
                            @php $net = $totals['total_net_profit'] ?? $totals['total_profit']; @endphp
                            <tfoot class="border-t-2 border-border-strong bg-surface-overlay">
                                <tr>
                                    <td colspan="3" class="px-5 py-3.5 text-[0.8rem] font-bold uppercase tracking-wide text-dg-300">
                                        Total ({{ $totals['count'] }} {{ $totals['count'] === 1 ? 'venda' : 'vendas' }})
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-right text-[0.9375rem] font-bold tabular-nums text-dg-50">{{ $money($totals['total_revenue']) }}</td>
                                    @if($canSeeFinancials)
                                        <td class="whitespace-nowrap px-3 py-3.5 text-right text-[0.8125rem] font-bold tabular-nums {{ $totals['total_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $money($totals['total_profit']) }}</td>
                                        <td class="whitespace-nowrap px-3 py-3.5 text-right text-[0.8125rem] font-bold tabular-nums {{ ($totals['total_commissions'] ?? 0) > 0 ? 'text-violet-300' : 'text-dg-600' }}">{{ ($totals['total_commissions'] ?? 0) > 0 ? $money($totals['total_commissions']) : '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-3.5 text-right text-[0.8125rem] font-extrabold tabular-nums {{ ($totals['total_net_profit'] ?? 0) >= 0 ? 'text-emerald-300' : 'text-red-400' }}">{{ $money($totals['total_net_profit'] ?? 0) }}</td>
                                    @endif
                                    <td class="px-4 py-3.5 text-center">
                                        @if($totals['total_revenue'] > 0)
                                            <span class="inline-block rounded-full px-2.5 py-1 text-[0.7rem] font-semibold tabular-nums {{ $net >= 0 ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}" title="Margem líquida">
                                                {{ number_format(($net / $totals['total_revenue']) * 100, 1, ',', '.') }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td colspan="2" class="px-4 py-3.5"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                @if($sales->hasPages())
                    <div class="border-t border-border bg-surface-overlay px-5 py-4">{{ $sales->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <script>
    function markReceiptSent(btn, title) {
        btn.disabled = true;
        btn.onclick = null;
        btn.classList.add('receipt-btn--sent');
        btn.title = title;
        btn.innerHTML = '<svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Recibo enviado';
    }

    async function sendReceiptWA(saleId, btn) {
        if (btn.disabled) return;

        const originalHTML = btn.innerHTML;
        const restore = () => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        };
        btn.disabled = true;
        btn.innerHTML = '<span style="display: inline-block; width: 12px; height: 12px; border: 2px solid #25D366; border-top-color: transparent; border-radius: 50%; animation: spin-receipt 0.6s linear infinite;"></span> Enviando...';

        try {
            const res = await fetch(`/sales/${saleId}/send-receipt-wa`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();

            if (data.success) {
                markReceiptSent(btn, `Recibo enviado em ${data.sent_at}` + (data.sent_by ? ` por ${data.sent_by}` : ''));
            } else if (res.status === 409) {
                markReceiptSent(btn, data.error);
            } else {
                alert(data.error || 'Erro ao enviar recibo');
                restore();
            }
        } catch (e) {
            alert('Erro de conexão ao enviar recibo');
            restore();
        }
    }
    </script>
    <style>
    @keyframes spin-receipt { to { transform: rotate(360deg); } }
    .receipt-btn {
        display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.3rem 0.5rem;
        background: rgba(37,211,102,0.1); color: #25D366; font-size: 0.7rem; font-weight: 600;
        border-radius: 0.375rem; border: 1px solid rgba(37,211,102,0.2); cursor: pointer;
        white-space: nowrap; transition: all 0.15s;
    }
    .receipt-btn:hover:not(:disabled) { background: rgba(37,211,102,0.2); }
    .receipt-btn:disabled { cursor: default; }
    .receipt-btn--sent {
        background: rgba(255,255,255,0.04); color: #818181; border-color: rgba(255,255,255,0.08);
    }
    </style>
</x-app-layout>
