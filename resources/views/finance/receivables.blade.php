<x-app-layout>
    <x-slot name="title">Contas a Receber</x-slot>
    @php
        $smallInput = 'w-full rounded-lg border border-border bg-surface-overlay px-2.5 py-1.5 text-[0.8125rem] text-dg-100 placeholder-dg-500 focus:border-dg-400 focus:ring-2 focus:ring-dg-400/20';
    @endphp
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8" x-data="receivablesPage()">
            <x-flash class="mb-4" />

            <x-page-header title="Contas a receber" subtitle="Acompanhe receitas pendentes e recebidas">
                <x-slot name="actions">
                    <button type="button" @click="showForm = !showForm" :aria-expanded="showForm"
                            class="dg-btn bg-emerald-600 font-semibold text-white hover:bg-emerald-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Nova receita
                    </button>
                </x-slot>
            </x-page-header>

            <!-- Cards de Resumo -->
            <dl class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="dg-card-flat p-5">
                    <dt class="text-[0.6875rem] font-semibold uppercase tracking-wide text-amber-400">A receber (filtrado)</dt>
                    <dd class="mt-1 text-2xl font-extrabold tabular-nums text-amber-300">R$ {{ number_format($summary['pending'], 2, ',', '.') }}</dd>
                    <dd class="mt-1 text-[0.6875rem] text-dg-500">Baseado nos filtros aplicados</dd>
                </div>
                <div class="rounded-xl border bg-surface-raised p-5 {{ $summary['overdue'] > 0 ? 'border-red-500/35' : 'border-border' }}">
                    <dt class="text-[0.6875rem] font-semibold uppercase tracking-wide text-red-400">Atrasado (total)</dt>
                    <dd class="mt-1 text-2xl font-extrabold tabular-nums text-red-300">R$ {{ number_format($summary['overdue'], 2, ',', '.') }}</dd>
                    <dd class="mt-1 text-[0.6875rem] text-dg-500">Todas as receitas atrasadas</dd>
                </div>
                <div class="dg-card-flat p-5">
                    <dt class="text-[0.6875rem] font-semibold uppercase tracking-wide text-emerald-400">Recebido no período</dt>
                    <dd class="mt-1 text-2xl font-extrabold tabular-nums text-emerald-300">R$ {{ number_format($summary['receivedInPeriod'], 2, ',', '.') }}</dd>
                    <dd class="mt-1 text-[0.6875rem] text-dg-500">Baseado nos filtros aplicados</dd>
                </div>
            </dl>

            <!-- Formulário Nova Receita -->
            <div x-show="showForm" x-cloak class="dg-card-flat mb-6 p-5">
                <h3 class="mb-4 font-semibold text-dg-100">Nova receita</h3>
                <form method="POST" action="{{ route('finance.transactions.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="income">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="inc-desc" class="dg-label">Descrição *</label>
                            <input id="inc-desc" type="text" name="description" required placeholder="Ex: Parcela cliente João" class="dg-input py-2.5">
                        </div>
                        <div>
                            <label for="inc-amount" class="dg-label">Valor total *</label>
                            <input id="inc-amount" type="number" name="amount" step="0.01" min="0.01" required placeholder="0,00" x-model.number="totalAmount" class="dg-input py-2.5 text-right tabular-nums">
                        </div>
                        <div>
                            <label for="inc-cat" class="dg-label">Categoria *</label>
                            <select id="inc-cat" name="category_id" required class="dg-select py-2.5">
                                <option value="">Selecione</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="inc-due" class="dg-label">1º vencimento *</label>
                            <input id="inc-due" type="date" name="due_date" required value="{{ now()->format('Y-m-d') }}" class="dg-input py-2.5">
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <label for="inc-inst" class="dg-label">Parcelas</label>
                            <div class="flex items-center gap-2">
                                <input id="inc-inst" type="number" name="installments" min="1" max="120" value="1" x-model.number="installments" class="dg-input w-20 py-2.5 text-center">
                                <span x-show="installments > 1" class="text-[0.6875rem] font-semibold text-emerald-400" x-text="installmentAmount"></span>
                            </div>
                        </div>
                        <div>
                            <label for="inc-paid" class="dg-label">Já recebido?</label>
                            <select id="inc-paid" name="is_paid" x-model="isPaid" :disabled="installments > 1" class="dg-select py-2.5 disabled:opacity-50">
                                <option value="0">Não, pendente</option>
                                <option value="1" x-bind:disabled="installments > 1">Sim, já recebido</option>
                            </select>
                            <template x-if="installments > 1">
                                <span class="mt-1 block text-[0.6875rem] text-dg-500">Parcelado: receba cada parcela separadamente</span>
                            </template>
                        </div>
                        <div x-show="isPaid === '1' && installments <= 1">
                            <label for="inc-acc" class="dg-label">Carteira</label>
                            <select id="inc-acc" name="account_id" class="dg-select py-2.5">
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ $account->is_default ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="inc-notes" class="dg-label">Observações</label>
                            <input id="inc-notes" type="text" name="notes" placeholder="Opcional" class="dg-input py-2.5">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="dg-btn w-full bg-emerald-600 py-2.5 font-semibold text-white hover:bg-emerald-500">
                                <span x-show="installments <= 1">Registrar receita</span>
                                <span x-show="installments > 1" x-text="'Registrar ' + installments + 'x'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Filtros -->
            <div class="dg-card-flat mb-6 p-4">
                <form method="GET" action="{{ route('finance.receivables') }}">
                    <div class="grid grid-cols-2 items-end gap-3 sm:grid-cols-3 lg:grid-cols-6">
                        <div>
                            <label for="f-search" class="mb-1 block text-[0.6875rem] font-medium text-dg-400">Buscar</label>
                            <input id="f-search" type="text" name="search" value="{{ $filters['search'] }}" placeholder="Descrição..." class="{{ $smallInput }}">
                        </div>
                        <div>
                            <label for="f-cat" class="mb-1 block text-[0.6875rem] font-medium text-dg-400">Categoria</label>
                            <select id="f-cat" name="category_id" class="{{ $smallInput }}">
                                <option value="">Todas</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $filters['category_id'] == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="f-status" class="mb-1 block text-[0.6875rem] font-medium text-dg-400">Status</label>
                            <select id="f-status" name="status" class="{{ $smallInput }}">
                                <option value="">Todos</option>
                                <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pendente</option>
                                <option value="overdue" {{ $filters['status'] === 'overdue' ? 'selected' : '' }}>Atrasado</option>
                                <option value="paid" {{ $filters['status'] === 'paid' ? 'selected' : '' }}>Recebido</option>
                            </select>
                        </div>
                        <div>
                            <label for="f-start" class="mb-1 block text-[0.6875rem] font-medium text-dg-400">De</label>
                            <input id="f-start" type="date" name="start_date" value="{{ $filters['start_date'] }}" class="{{ $smallInput }}">
                        </div>
                        <div>
                            <label for="f-end" class="mb-1 block text-[0.6875rem] font-medium text-dg-400">Até</label>
                            <input id="f-end" type="date" name="end_date" value="{{ $filters['end_date'] }}" class="{{ $smallInput }}">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 rounded-lg bg-white px-3 py-1.5 text-[0.8125rem] font-semibold text-dg-950 transition-colors hover:bg-dg-100">Filtrar</button>
                            @if(array_filter($filters))
                                <a href="{{ route('finance.receivables') }}" class="flex items-center rounded-lg border border-border px-3 py-1.5 text-[0.8125rem] font-medium text-dg-300 hover:text-dg-100">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabela -->
            <div class="dg-card-flat overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="dg-table-header">
                            <tr>
                                <th scope="col" class="px-5 py-2.5 text-left">Previsão</th>
                                <th scope="col" class="px-4 py-2.5 text-left">Descrição</th>
                                <th scope="col" class="px-4 py-2.5 text-left">Categoria</th>
                                <th scope="col" class="px-4 py-2.5 text-center">Status</th>
                                <th scope="col" class="px-4 py-2.5 text-right">Valor</th>
                                <th scope="col" class="px-5 py-2.5 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @forelse($transactions as $tx)
                                <tr class="transition-colors hover:bg-surface-overlay/60">
                                    <td class="whitespace-nowrap px-5 py-2.5">
                                        <p class="text-[0.8125rem] font-medium tabular-nums text-dg-100">{{ $tx->due_date->format('d/m/Y') }}</p>
                                        @if($tx->paid_at)
                                            <p class="text-[0.6875rem] text-emerald-400">Recebido {{ $tx->paid_at->format('d/m') }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <p class="text-[0.8125rem] font-medium text-dg-100">{{ Str::limit($tx->description, 45) }}</p>
                                        @if($tx->notes)
                                            <p class="text-[0.6875rem] text-dg-500">{{ Str::limit($tx->notes, 40) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[0.6875rem] font-medium" style="background: {{ $tx->category->color ?? '#6b7280' }}26; color: {{ $tx->category->color ?? '#a4a4a4' }};">
                                            {{ $tx->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[0.6875rem] font-semibold" style="background: {{ $tx->status->bgColor() }}; color: {{ $tx->status->color() }};">
                                            {{ $tx->status->label() }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2.5 text-right font-bold tabular-nums text-emerald-400">
                                        R$ {{ number_format((float) $tx->amount, 2, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-2.5 text-right">
                                        @if($tx->status->value !== 'paid' && $tx->status->value !== 'cancelled')
                                            <form method="POST" action="{{ route('finance.transactions.pay', $tx) }}" class="inline-flex items-center gap-1">
                                                @csrf
                                                <select name="account_id" required aria-label="Carteira do recebimento" class="rounded-md border border-border bg-surface-overlay py-1 pl-2 pr-7 text-[0.6875rem] text-dg-200">
                                                    @foreach($accounts as $acc)
                                                        <option value="{{ $acc->id }}" {{ $acc->is_default ? 'selected' : '' }}>{{ $acc->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="rounded-md bg-emerald-600 px-2.5 py-1 text-[0.6875rem] font-semibold text-white transition-colors hover:bg-emerald-500">Receber</button>
                                            </form>
                                            <form method="POST" action="{{ route('finance.transactions.cancel', $tx) }}" class="ml-1 inline" onsubmit="return confirm('Cancelar esta receita?')">
                                                @csrf
                                                <button type="submit" class="rounded-md px-2 py-1 text-[0.6875rem] font-medium text-dg-500 transition-colors hover:text-red-300">Cancelar</button>
                                            </form>
                                        @elseif($tx->status->value === 'paid')
                                            <span class="text-[0.6875rem] text-dg-500">{{ $tx->account->name ?? '-' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-dg-500">Nenhuma conta a receber encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="border-t border-border px-5 py-4">{{ $transactions->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function receivablesPage() {
            return {
                showForm: false,
                isPaid: '0',
                installments: 1,
                totalAmount: 0,
                get installmentAmount() {
                    if (this.installments <= 1 || this.totalAmount <= 0) return '';
                    return 'Parcela: R$ ' + (this.totalAmount / this.installments).toFixed(2).replace('.', ',');
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
