<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showNewAccount: false, showTransfer: false }">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b;">
                    <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Carteiras</h1>
                    <p class="text-sm text-gray-500">Gerencie suas contas e veja os saldos</p>
                </div>
                <div class="flex gap-2">
                    <button @click="showTransfer = !showTransfer; showNewAccount = false"
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: white; color: #374151; font-weight: 600; border-radius: 0.75rem; border: 1px solid #d1d5db; cursor: pointer; font-size: 0.875rem;"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        Transferir
                    </button>
                    <button @click="showNewAccount = !showNewAccount; showTransfer = false"
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: #111827; color: white; font-weight: 600; border-radius: 0.75rem; border: none; cursor: pointer; font-size: 0.875rem;"
                            onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                        Nova Carteira
                    </button>
                </div>
            </div>

            <!-- Form Nova Carteira -->
            <div x-show="showNewAccount" x-cloak style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 600; color: #111827; margin-bottom: 1rem;">Nova Carteira</h3>
                <form method="POST" action="{{ route('finance.accounts.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    @csrf
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Nome *</label>
                        <input type="text" name="name" required placeholder="Ex: Nubank, PicPay" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Tipo *</label>
                        <select name="type" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white; margin-top: 0.25rem;">
                            <option value="cash">Dinheiro</option>
                            <option value="bank">Banco</option>
                            <option value="digital_wallet">Carteira Digital</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Saldo Inicial</label>
                        <input type="number" name="initial_balance" step="0.01" value="0" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Cor</label>
                        <input type="color" name="color" value="#111827" style="width: 100%; height: 2.375rem; border: 1px solid #d1d5db; border-radius: 0.5rem; margin-top: 0.25rem; cursor: pointer;">
                    </div>
                    <button type="submit" style="padding: 0.5rem 1rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">Criar Carteira</button>
                </form>
            </div>

            <!-- Form Transferência -->
            <div x-show="showTransfer" x-cloak style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 600; color: #111827; margin-bottom: 1rem;">Transferir entre Carteiras</h3>
                <form method="POST" action="{{ route('finance.transfers.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    @csrf
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">De *</label>
                        <select name="from_account_id" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white; margin-top: 0.25rem;">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->formatted_balance }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Para *</label>
                        <select name="to_account_id" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white; margin-top: 0.25rem;">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Valor *</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Descrição</label>
                        <input type="text" name="description" placeholder="Opcional" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                    </div>
                    <button type="submit" style="padding: 0.5rem 1rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">Transferir</button>
                </form>
            </div>

            <!-- Cards de Carteiras -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($accounts as $account)
                    <a href="{{ route('finance.accounts', ['account_id' => $account->id]) }}"
                       style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 2px solid {{ $selectedAccountId == $account->id ? $account->color : '#e5e7eb' }}; text-decoration: none; display: block; transition: all 0.15s;"
                       onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; background: {{ $account->color }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $account->type->icon() }}"/>
                                </svg>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">{{ $account->name }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">{{ $account->type->label() }}{{ $account->is_default ? ' — Padrão' : '' }}</div>
                            </div>
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: {{ (float)$account->current_balance >= 0 ? '#111827' : '#dc2626' }};">
                            {{ $account->formatted_balance }}
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Extrato da Conta Selecionada -->
            @if($selectedAccountId)
                @php $selectedAccount = $accounts->firstWhere('id', $selectedAccountId); @endphp
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                            <h2 style="font-size: 0.9375rem; font-weight: 600; color: #111827;">Extrato — {{ $selectedAccount?->name ?? 'Conta' }}</h2>
                            <form method="GET" action="{{ route('finance.accounts') }}" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">
                                <div>
                                    <label style="font-size: 0.625rem; font-weight: 500; color: #6b7280;">De</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" style="padding: 0.25rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.75rem;">
                                </div>
                                <div>
                                    <label style="font-size: 0.625rem; font-weight: 500; color: #6b7280;">Até</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" style="padding: 0.25rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.75rem;">
                                </div>
                                <button type="submit" style="padding: 0.25rem 0.625rem; background: #111827; color: white; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer; font-size: 0.75rem; margin-top: 0.875rem;">Filtrar</button>
                                @if(request('start_date') || request('end_date'))
                                    <a href="{{ route('finance.accounts', ['account_id' => $selectedAccountId]) }}" style="padding: 0.25rem 0.625rem; background: white; color: #374151; font-weight: 500; border-radius: 0.375rem; border: 1px solid #d1d5db; text-decoration: none; font-size: 0.75rem; margin-top: 0.875rem;">Limpar</a>
                                @endif
                            </form>
                        </div>
                    </div>
                @if($statement->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb;">
                                    <th style="padding: 0.625rem 1.25rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                    <th style="padding: 0.625rem 1rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Descrição</th>
                                    <th style="padding: 0.625rem 1rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Categoria</th>
                                    <th style="padding: 0.625rem 1.25rem; text-align: right; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statement as $entry)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 0.5rem 1.25rem; font-size: 0.8125rem; color: #6b7280;">{{ $entry->date?->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td style="padding: 0.5rem 1rem; font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $entry->description }}</td>
                                        <td style="padding: 0.5rem 1rem;">
                                            <span style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: {{ $entry->category_color }}20; color: {{ $entry->category_color }}; font-weight: 500;">
                                                {{ $entry->category_name }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.5rem 1.25rem; text-align: right; font-size: 0.875rem; font-weight: 700; color: {{ (float)$entry->amount >= 0 ? '#16a34a' : '#dc2626' }};">
                                            {{ (float)$entry->amount >= 0 ? '+' : '' }} R$ {{ number_format((float)$entry->amount, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="padding: 2rem; text-align: center; color: #9ca3af;">
                        Nenhuma movimentação nesta carteira{{ (request('start_date') || request('end_date')) ? ' no período selecionado' : '' }}.
                    </div>
                @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <style>[x-cloak] { display: none !important; }</style>
    @endpush
</x-app-layout>
