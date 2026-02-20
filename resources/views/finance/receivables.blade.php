<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="receivablesPage()">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
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
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Contas a Receber</h1>
                    <p class="text-sm text-gray-500">Acompanhe receitas pendentes e recebidas</p>
                </div>
                <button @click="showForm = !showForm"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: #16a34a; color: white; font-weight: 600; border-radius: 0.75rem; border: none; cursor: pointer; font-size: 0.875rem;"
                        onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Receita
                </button>
            </div>

            <!-- Cards de Resumo -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
                    <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 600; color: #d97706;">A Receber (Filtrado)</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #d97706; margin-top: 0.25rem;">R$ {{ number_format($summary['pending'], 2, ',', '.') }}</div>
                    <div style="font-size: 0.625rem; color: #9ca3af; margin-top: 0.25rem;">Baseado nos filtros aplicados</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid {{ $summary['overdue'] > 0 ? '#fecaca' : '#e5e7eb' }};">
                    <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 600; color: #dc2626;">Atrasado (Total)</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #dc2626; margin-top: 0.25rem;">R$ {{ number_format($summary['overdue'], 2, ',', '.') }}</div>
                    <div style="font-size: 0.625rem; color: #9ca3af; margin-top: 0.25rem;">Todas as receitas atrasadas</div>
                </div>
                <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
                    <div style="font-size: 0.6875rem; text-transform: uppercase; font-weight: 600; color: #16a34a;">Recebido no Período</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #16a34a; margin-top: 0.25rem;">R$ {{ number_format($summary['receivedInPeriod'], 2, ',', '.') }}</div>
                    <div style="font-size: 0.625rem; color: #9ca3af; margin-top: 0.25rem;">Baseado nos filtros aplicados</div>
                </div>
            </div>

            <!-- Formulário Nova Receita -->
            <div x-show="showForm" x-cloak style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 600; color: #111827; margin-bottom: 1rem;">Nova Receita</h3>
                <form method="POST" action="{{ route('finance.transactions.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="income">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Descrição *</label>
                            <input type="text" name="description" required placeholder="Ex: Parcela cliente João" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Valor Total *</label>
                            <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0,00" x-model.number="totalAmount" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Categoria *</label>
                            <select name="category_id" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white; margin-top: 0.25rem;">
                                <option value="">Selecione</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">1o Vencimento *</label>
                            <input type="date" name="due_date" required value="{{ now()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-4">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Parcelas</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                                <input type="number" name="installments" min="1" max="120" value="1" x-model.number="installments" style="width: 5rem; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                <span x-show="installments > 1" style="font-size: 0.6875rem; color: #16a34a; font-weight: 600;" x-text="installmentAmount"></span>
                            </div>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Já recebido?</label>
                            <select name="is_paid" x-model="isPaid" :disabled="installments > 1" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white; margin-top: 0.25rem;">
                                <option value="0">Não — Pendente</option>
                                <option value="1" x-bind:disabled="installments > 1">Sim — Já recebido</option>
                            </select>
                            <template x-if="installments > 1">
                                <span style="font-size: 0.625rem; color: #9ca3af;">Parcelado = receber individualmente</span>
                            </template>
                        </div>
                        <div x-show="isPaid === '1' && installments <= 1">
                            <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Carteira</label>
                            <select name="account_id" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white; margin-top: 0.25rem;">
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ $account->is_default ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Observações</label>
                            <input type="text" name="notes" placeholder="Opcional" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                        </div>
                        <div style="display: flex; align-items: flex-end;">
                            <button type="submit" style="width: 100%; padding: 0.5rem 1rem; background: #16a34a; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">
                                <span x-show="installments <= 1">Registrar Receita</span>
                                <span x-show="installments > 1" x-text="'Registrar ' + installments + 'x'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Filtros -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('finance.receivables') }}" x-data x-ref="filterForm" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                    <div>
                        <label style="font-size: 0.6875rem; font-weight: 500; color: #6b7280;">Buscar</label>
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Descrição..." style="width: 100%; padding: 0.375rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem;"
                               x-on:input.debounce.400ms="$refs.filterForm.submit()">
                    </div>
                    <div>
                        <label style="font-size: 0.6875rem; font-weight: 500; color: #6b7280;">Categoria</label>
                        <select name="category_id" style="width: 100%; padding: 0.375rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem; background: white;"
                                x-on:change="$refs.filterForm.submit()">
                            <option value="">Todas</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $filters['category_id'] == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.6875rem; font-weight: 500; color: #6b7280;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.375rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem; background: white;"
                                x-on:change="$refs.filterForm.submit()">
                            <option value="">Todos</option>
                            <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="overdue" {{ $filters['status'] === 'overdue' ? 'selected' : '' }}>Atrasado</option>
                            <option value="paid" {{ $filters['status'] === 'paid' ? 'selected' : '' }}>Recebido</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.6875rem; font-weight: 500; color: #6b7280;">De</label>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] }}" style="width: 100%; padding: 0.375rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem;"
                               x-on:change="$refs.filterForm.submit()">
                    </div>
                    <div>
                        <label style="font-size: 0.6875rem; font-weight: 500; color: #6b7280;">Até</label>
                        <input type="date" name="end_date" value="{{ $filters['end_date'] }}" style="width: 100%; padding: 0.375rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.8125rem;"
                               x-on:change="$refs.filterForm.submit()">
                    </div>
                    @if(array_filter($filters))
                        <div>
                            <a href="{{ route('finance.receivables') }}" style="display: inline-block; padding: 0.375rem 0.75rem; background: white; color: #374151; font-weight: 500; border-radius: 0.375rem; border: 1px solid #d1d5db; text-decoration: none; font-size: 0.8125rem;">Limpar</a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Tabela -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb;">
                                <th style="padding: 0.625rem 1.25rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Previsão</th>
                                <th style="padding: 0.625rem 1rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Descrição</th>
                                <th style="padding: 0.625rem 1rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Categoria</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                <th style="padding: 0.625rem 1rem; text-align: right; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Valor</th>
                                <th style="padding: 0.625rem 1.25rem; text-align: right; font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 0.625rem 1.25rem;">
                                        <div style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $tx->due_date->format('d/m/Y') }}</div>
                                        @if($tx->paid_at)
                                            <div style="font-size: 0.6875rem; color: #16a34a;">Recebido {{ $tx->paid_at->format('d/m') }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.625rem 1rem;">
                                        <div style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ Str::limit($tx->description, 45) }}</div>
                                        @if($tx->notes)
                                            <div style="font-size: 0.6875rem; color: #9ca3af;">{{ Str::limit($tx->notes, 40) }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.625rem 1rem;">
                                        <span style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: {{ $tx->category->color ?? '#6b7280' }}20; color: {{ $tx->category->color ?? '#6b7280' }}; font-weight: 500;">
                                            {{ $tx->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.625rem 1rem; text-align: center;">
                                        <span style="font-size: 0.625rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 600; background: {{ $tx->status->bgColor() }}; color: {{ $tx->status->color() }};">
                                            {{ $tx->status->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.625rem 1rem; text-align: right; font-size: 0.875rem; font-weight: 700; color: #16a34a;">
                                        R$ {{ number_format((float)$tx->amount, 2, ',', '.') }}
                                    </td>
                                    <td style="padding: 0.625rem 1.25rem; text-align: right;">
                                        @if($tx->status->value !== 'paid' && $tx->status->value !== 'cancelled')
                                            <form method="POST" action="{{ route('finance.transactions.pay', $tx) }}" style="display: inline;">
                                                @csrf
                                                <select name="account_id" required style="font-size: 0.6875rem; padding: 0.125rem 0.25rem; border: 1px solid #d1d5db; border-radius: 0.25rem; background: white;">
                                                    @foreach($accounts as $acc)
                                                        <option value="{{ $acc->id }}" {{ $acc->is_default ? 'selected' : '' }}>{{ $acc->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; background: #16a34a; color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-weight: 600; margin-left: 0.25rem;">Receber</button>
                                            </form>
                                            <form method="POST" action="{{ route('finance.transactions.cancel', $tx) }}" style="display: inline; margin-left: 0.25rem;" onsubmit="return confirm('Cancelar esta receita?')">
                                                @csrf
                                                <button type="submit" style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; background: none; color: #9ca3af; border: none; cursor: pointer;">Cancelar</button>
                                            </form>
                                        @elseif($tx->status->value === 'paid')
                                            <span style="font-size: 0.6875rem; color: #9ca3af;">{{ $tx->account->name ?? '-' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 2rem; text-align: center; color: #9ca3af;">Nenhuma conta a receber encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div style="padding: 1rem 1.25rem; border-top: 1px solid #e5e7eb;">{{ $transactions->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <style>[x-cloak] { display: none !important; }</style>
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
