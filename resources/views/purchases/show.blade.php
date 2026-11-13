<x-app-layout>
    <x-slot name="title">Compra {{ $purchase->number }}</x-slot>
    @php
        $statusTone = [
            'pending' => 'bg-amber-500/15 text-amber-300',
            'partial' => 'bg-sky-500/15 text-sky-300',
            'received' => 'bg-emerald-500/15 text-emerald-300',
            'cancelled' => 'bg-surface-elevated text-dg-400',
        ];
        $items = $purchase->items;
        $pending = $items->where('status', 'pending');
        $received = $items->where('status', 'received');
        $cancelled = $items->where('status', 'cancelled');
        $inTransit = $pending->sum(fn ($i) => $i->totalCost());
    @endphp
    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))<div class="mb-4"><x-alert type="success">{{ session('success') }}</x-alert></div>@endif
            @if(session('error'))<div class="mb-4"><x-alert type="error">{{ session('error') }}</x-alert></div>@endif

            <a href="{{ route('purchases.index') }}" class="text-xs font-medium text-dg-400 underline-offset-4 hover:text-dg-100 hover:underline">← Compras</a>

            <div class="mt-2 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-dg-50">{{ $purchase->number }}</h1>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusTone[$purchase->status] ?? '' }}">{{ $purchase->statusLabel() }}</span>
                        @if($purchase->isLate())<span class="text-xs font-medium text-red-300">atrasada</span>@endif
                    </div>
                    <p class="mt-1 text-sm text-dg-400">
                        {{ $purchase->supplierLabel() }} · comprada em {{ $purchase->purchased_at->format('d/m/Y') }}
                        @if($purchase->expected_at) · previsão {{ $purchase->expected_at->format('d/m/Y') }} @endif
                        @if($purchase->user) · por {{ $purchase->user->name }} @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-dg-400">Total</p>
                    <p class="text-2xl font-bold tabular-nums text-dg-50">R$ {{ number_format((float) $purchase->total, 2, ',', '.') }}</p>
                    <p class="text-xs text-dg-400">
                        @if($purchase->financial_transaction_id)
                            Lançado no financeiro{{ $purchase->account ? ' · ' . $purchase->account->name : '' }}
                        @else
                            Não lançado no financeiro
                        @endif
                    </p>
                </div>
            </div>

            @if($purchase->notes)
                <p class="mt-3 rounded-lg bg-surface-raised px-3 py-2 text-sm text-dg-300">{{ $purchase->notes }}</p>
            @endif

            <dl class="mt-5 grid grid-cols-3 gap-3">
                <div class="rounded-xl border border-border bg-surface-raised p-3 sm:p-4">
                    <dt class="text-xs text-dg-400">A receber</dt>
                    <dd class="mt-0.5 text-lg font-bold text-amber-300">{{ $pending->sum('quantity') }} <span class="text-xs font-normal text-dg-400">· R$ {{ number_format($inTransit, 0, ',', '.') }}</span></dd>
                </div>
                <div class="rounded-xl border border-border bg-surface-raised p-3 sm:p-4">
                    <dt class="text-xs text-dg-400">No estoque</dt>
                    <dd class="mt-0.5 text-lg font-bold text-emerald-300">{{ $received->sum('quantity') }}</dd>
                </div>
                <div class="rounded-xl border border-border bg-surface-raised p-3 sm:p-4">
                    <dt class="text-xs text-dg-400">Não vieram</dt>
                    <dd class="mt-0.5 text-lg font-bold text-dg-300">{{ $cancelled->sum('quantity') }}</dd>
                </div>
            </dl>

            <section class="mt-5 rounded-xl border border-border bg-surface-raised" aria-labelledby="items-title">
                <h2 id="items-title" class="border-b border-border px-4 py-3 text-base font-semibold text-dg-100 sm:px-5">Itens</h2>
                <ul class="divide-y divide-border">
                    @foreach($items as $item)
                        <li class="flex flex-col gap-3 px-4 py-3.5 sm:flex-row sm:items-center sm:px-5 {{ $item->status === 'cancelled' ? 'opacity-60' : '' }}">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-dg-100">
                                    {{ $item->displayName() }}
                                    @if($item->quantity > 1)<span class="text-dg-400">× {{ $item->quantity }}</span>@endif
                                </p>
                                <p class="text-xs text-dg-400">
                                    {{ $item->categoryLabel() }} · {{ $item->conditionLabel() }} · R$ {{ number_format((float) $item->unit_cost, 2, ',', '.') }}{{ $item->quantity > 1 ? ' cada' : '' }}
                                </p>
                            </div>

                            <div class="flex flex-shrink-0 items-center gap-2">
                                @if($item->status === 'received')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-300">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Chegou {{ $item->received_at?->format('d/m') }}
                                    </span>
                                    @if($item->product)
                                        <a href="{{ route('products.show', $item->product) }}" class="text-xs font-medium text-dg-300 underline-offset-4 hover:text-dg-100 hover:underline">
                                            {{ $item->product->imei ? 'IMEI ' . $item->product->imei : 'Ver produto' }}
                                        </a>
                                    @endif
                                @elseif($item->status === 'cancelled')
                                    <span class="text-xs font-medium text-dg-400">Não veio · {{ $item->cancelled_at?->format('d/m') }}</span>
                                @else
                                    <a href="{{ route('products.create', ['purchase_item' => $item->id]) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-500 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-300">
                                        Dar entrada
                                    </a>
                                    <details class="relative">
                                        <summary class="cursor-pointer list-none rounded-lg px-2.5 py-1.5 text-xs font-medium text-dg-400 transition-colors hover:bg-surface-elevated hover:text-dg-100 [&::-webkit-details-marker]:hidden">Não veio</summary>
                                        <form method="POST" action="{{ route('purchases.items.cancel', $item) }}"
                                              class="absolute right-0 z-20 mt-1 w-72 rounded-lg border border-border-strong bg-surface-overlay p-3 text-left shadow-xl shadow-black/50">
                                            @csrf
                                            <p class="text-xs text-dg-200">Cancelar este item ({{ $item->displayName() }})?</p>
                                            @if($purchase->financial_transaction_id)
                                                <label class="mt-2 flex items-start gap-2 text-xs text-dg-300">
                                                    <input type="checkbox" name="refund" value="1" checked class="mt-0.5 h-3.5 w-3.5 rounded border-border-strong text-indigo-500 focus:ring-indigo-400">
                                                    <span>Estornar R$ {{ number_format($item->totalCost(), 2, ',', '.') }} no financeiro<span class="block text-dg-500">Desmarque se ficou como crédito com o fornecedor.</span></span>
                                                </label>
                                            @endif
                                            <button type="submit" class="mt-3 w-full rounded-md bg-red-500/15 px-3 py-1.5 text-xs font-semibold text-red-200 transition-colors hover:bg-red-500/25">Confirmar cancelamento</button>
                                        </form>
                                    </details>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </div>
</x-app-layout>
