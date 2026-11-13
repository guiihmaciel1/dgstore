<x-app-layout>
    <x-slot name="title">Contas Bancárias</x-slot>
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8" x-data="{ showNewAccount: false, showTransfer: false }">
            <x-flash class="mb-4" />

            <x-page-header title="Carteiras" subtitle="Gerencie suas contas e veja os saldos">
                <x-slot name="actions">
                    <button type="button" @click="showTransfer = !showTransfer; showNewAccount = false" class="dg-btn-secondary" :aria-expanded="showTransfer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                        Transferir
                    </button>
                    <button type="button" @click="showNewAccount = !showNewAccount; showTransfer = false" class="dg-btn-primary" :aria-expanded="showNewAccount">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Nova carteira
                    </button>
                </x-slot>
            </x-page-header>

            <!-- Form Nova Carteira -->
            <div x-show="showNewAccount" x-cloak class="dg-card-flat mb-6 p-5">
                <h3 class="mb-4 font-semibold text-dg-100">Nova carteira</h3>
                <form method="POST" action="{{ route('finance.accounts.store') }}" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @csrf
                    <div>
                        <label for="acc-name" class="dg-label">Nome *</label>
                        <input id="acc-name" type="text" name="name" required placeholder="Ex: Nubank, PicPay" class="dg-input py-2.5">
                    </div>
                    <div>
                        <label for="acc-type" class="dg-label">Tipo *</label>
                        <select id="acc-type" name="type" required class="dg-select py-2.5">
                            <option value="cash">Dinheiro</option>
                            <option value="bank">Banco</option>
                            <option value="digital_wallet">Carteira Digital</option>
                        </select>
                    </div>
                    <div>
                        <label for="acc-initial" class="dg-label">Saldo inicial</label>
                        <input id="acc-initial" type="number" name="initial_balance" step="0.01" value="0" class="dg-input py-2.5 text-right tabular-nums">
                    </div>
                    <div>
                        <label for="acc-color" class="dg-label">Cor</label>
                        <input id="acc-color" type="color" name="color" value="#4f46e5" class="h-[2.625rem] w-full cursor-pointer rounded-xl border border-border bg-surface-overlay p-1">
                    </div>
                    <button type="submit" class="dg-btn-primary py-2.5">Criar carteira</button>
                </form>
            </div>

            <!-- Form Transferência -->
            <div x-show="showTransfer" x-cloak class="dg-card-flat mb-6 p-5">
                <h3 class="mb-4 font-semibold text-dg-100">Transferir entre carteiras</h3>
                <form method="POST" action="{{ route('finance.transfers.store') }}" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @csrf
                    <div>
                        <label for="tr-from" class="dg-label">De *</label>
                        <select id="tr-from" name="from_account_id" required class="dg-select py-2.5">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->formatted_balance }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tr-to" class="dg-label">Para *</label>
                        <select id="tr-to" name="to_account_id" required class="dg-select py-2.5">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tr-amount" class="dg-label">Valor *</label>
                        <input id="tr-amount" type="number" name="amount" step="0.01" min="0.01" required class="dg-input py-2.5 text-right tabular-nums">
                    </div>
                    <div>
                        <label for="tr-desc" class="dg-label">Descrição</label>
                        <input id="tr-desc" type="text" name="description" placeholder="Opcional" class="dg-input py-2.5">
                    </div>
                    <button type="submit" class="dg-btn-primary py-2.5">Transferir</button>
                </form>
            </div>

            <!-- Cards de Carteiras -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($accounts as $account)
                    @php $selected = $selectedAccountId == $account->id; @endphp
                    <div class="relative">
                        <a href="{{ route('finance.accounts', ['account_id' => $account->id]) }}"
                           @if($selected) aria-current="true" @endif
                           class="block h-full rounded-xl border bg-surface-raised p-5 transition-colors hover:bg-surface-overlay focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-dg-300 {{ $selected ? 'border-dg-300' : 'border-border hover:border-border-strong' }}">
                            <div class="mb-4 flex items-center gap-3 pr-20">
                                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg ring-1 ring-inset ring-white/10" style="background: {{ $account->color }};">
                                    <svg class="h-5 w-5 {{ $account->isLightColor() ? 'text-dg-950' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $account->type->icon() }}"/>
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-dg-100">{{ $account->name }}</p>
                                    <p class="text-xs text-dg-400">{{ $account->type->label() }}{{ $account->is_default ? ' · Padrão' : '' }}</p>
                                </div>
                            </div>
                            <p class="text-2xl font-bold tabular-nums {{ (float) $account->current_balance >= 0 ? 'text-dg-50' : 'text-red-400' }}">
                                {{ $account->formatted_balance }}
                            </p>
                        </a>
                        @unless($account->is_default)
                            <form method="POST" action="{{ route('finance.accounts.deactivate', $account) }}"
                                  onsubmit="return confirm('Desativar a carteira {{ addslashes($account->name) }}? Ela some das listas, mas o histórico fica guardado.')"
                                  class="absolute right-4 top-4">
                                @csrf
                                <button type="submit" title="Desativar carteira"
                                        class="rounded-md border border-border px-2.5 py-1 text-xs font-medium text-dg-400 transition-colors hover:border-red-500/40 hover:text-red-300">
                                    Desativar
                                </button>
                            </form>
                        @endunless
                    </div>
                @endforeach
            </div>

            <!-- Extrato da Conta Selecionada -->
            @if($selectedAccountId)
                @php $selectedAccount = $accounts->firstWhere('id', $selectedAccountId); @endphp
                <section class="dg-card-flat overflow-hidden" aria-labelledby="statement-title">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border px-5 py-4">
                        <h2 id="statement-title" class="text-[0.9375rem] font-semibold text-dg-100">Extrato · {{ $selectedAccount?->name ?? 'Conta' }}</h2>
                        <form method="GET" action="{{ route('finance.accounts') }}" x-data x-ref="filterForm" class="flex flex-wrap items-end gap-2">
                            <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                            <div>
                                <label for="st-start" class="block text-[0.6875rem] font-medium text-dg-400">De</label>
                                <input id="st-start" type="date" name="start_date" value="{{ request('start_date') }}" x-on:change="$refs.filterForm.submit()"
                                       class="rounded-lg border border-border bg-surface-overlay px-2 py-1.5 text-xs text-dg-100">
                            </div>
                            <div>
                                <label for="st-end" class="block text-[0.6875rem] font-medium text-dg-400">Até</label>
                                <input id="st-end" type="date" name="end_date" value="{{ request('end_date') }}" x-on:change="$refs.filterForm.submit()"
                                       class="rounded-lg border border-border bg-surface-overlay px-2 py-1.5 text-xs text-dg-100">
                            </div>
                            @if(request('start_date') || request('end_date'))
                                <a href="{{ route('finance.accounts', ['account_id' => $selectedAccountId]) }}" class="rounded-lg border border-border px-2.5 py-1.5 text-xs font-medium text-dg-300 hover:text-dg-100">Limpar</a>
                            @endif
                        </form>
                    </div>
                    @if($statement->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="dg-table-header">
                                    <tr>
                                        <th scope="col" class="px-5 py-2.5 text-left">Data</th>
                                        <th scope="col" class="px-4 py-2.5 text-left">Descrição</th>
                                        <th scope="col" class="px-4 py-2.5 text-left">Categoria</th>
                                        <th scope="col" class="px-5 py-2.5 text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border-subtle">
                                    @foreach($statement as $entry)
                                        <tr class="transition-colors hover:bg-surface-overlay/60">
                                            <td class="whitespace-nowrap px-5 py-2.5 text-[0.8125rem] text-dg-400">{{ $entry->date?->format('d/m/Y H:i') ?? '-' }}</td>
                                            <td class="px-4 py-2.5 text-[0.8125rem] font-medium text-dg-100">{{ $entry->description }}</td>
                                            <td class="px-4 py-2.5">
                                                <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[0.6875rem] font-medium" style="background: {{ $entry->category_color }}26; color: {{ $entry->category_color }};">
                                                    {{ $entry->category_name }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-2.5 text-right font-bold tabular-nums {{ (float) $entry->amount >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                                {{ (float) $entry->amount >= 0 ? '+' : '' }} R$ {{ number_format((float) $entry->amount, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="px-5 py-8 text-center text-sm text-dg-500">
                            Nenhuma movimentação nesta carteira{{ (request('start_date') || request('end_date')) ? ' no período selecionado' : '' }}.
                        </p>
                    @endif
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
