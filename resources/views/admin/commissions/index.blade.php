<x-app-layout>
    <x-slot name="title">Comissões</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46; font-size: 0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Comissões</h1>
                    <p class="text-sm text-gray-500">Gerenciar comissões e saques de estagiários</p>
                </div>
            </div>

            {{-- Seletor de Estagiário --}}
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('admin.commissions.index') }}" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: #374151;">Estagiário:</label>
                    <select name="user_id" onchange="this.form.submit()" style="padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; min-width: 200px;">
                        @foreach($interns as $intern)
                            <option value="{{ $intern->id }}" {{ $selectedUser?->id === $intern->id ? 'selected' : '' }}>
                                {{ $intern->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                </form>
            </div>

            @if($selectedUser)
                {{-- Cards Resumo --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Saldo Disponível</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: {{ $balance >= 0 ? '#059669' : '#dc2626' }};">
                            R$ {{ number_format($balance, 2, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Comissões do Mês</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: #111827;">
                            R$ {{ number_format($monthEarned, 2, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Total Acumulado</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: #111827;">
                            R$ {{ number_format($totalEarned, 2, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Total Sacado</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: #dc2626;">
                            R$ {{ number_format($totalWithdrawn, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Configuração de Taxa + Botão de Saque --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    {{-- Configurar Taxa --}}
                    <div x-data="{ type: '{{ $selectedUser->commission_type ?? 'percentage' }}' }" style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827;">Taxa de Comissão</h3>
                        </div>
                        <form method="POST" action="{{ route('admin.commissions.update-rate', $selectedUser) }}" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
                            @csrf
                            @method('PUT')
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="button" @click="type = 'percentage'"
                                        :style="type === 'percentage'
                                            ? 'padding: 0.375rem 0.75rem; font-size: 0.8125rem; font-weight: 600; border-radius: 0.375rem; cursor: pointer; border: 1px solid #111827; background: #111827; color: white;'
                                            : 'padding: 0.375rem 0.75rem; font-size: 0.8125rem; font-weight: 500; border-radius: 0.375rem; cursor: pointer; border: 1px solid #d1d5db; background: white; color: #374151;'">
                                    % Percentual
                                </button>
                                <button type="button" @click="type = 'fixed'"
                                        :style="type === 'fixed'
                                            ? 'padding: 0.375rem 0.75rem; font-size: 0.8125rem; font-weight: 600; border-radius: 0.375rem; cursor: pointer; border: 1px solid #111827; background: #111827; color: white;'
                                            : 'padding: 0.375rem 0.75rem; font-size: 0.8125rem; font-weight: 500; border-radius: 0.375rem; cursor: pointer; border: 1px solid #d1d5db; background: white; color: #374151;'">
                                    R$ Valor fixo
                                </button>
                            </div>
                            <input type="hidden" name="commission_type" :value="type">
                            <div style="display: flex; align-items: flex-end; gap: 0.75rem;">
                                <div style="flex: 1;">
                                    <label style="font-size: 0.75rem; color: #6b7280; display: block; margin-bottom: 0.25rem;">
                                        <span x-show="type === 'percentage'">Percentual (%)</span>
                                        <span x-show="type === 'fixed'" x-cloak>Valor por venda (R$)</span>
                                    </label>
                                    <input type="number" name="commission_rate" value="{{ $selectedUser->commission_rate ?? 0 }}" step="0.01" min="0"
                                           :max="type === 'percentage' ? 100 : 99999"
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <button type="submit" style="padding: 0.5rem 1rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                    Salvar
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Registrar Saque --}}
                    <div x-data="{ open: false }" style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827;">Registrar Saque</h3>
                            <button @click="open = !open" style="font-size: 0.75rem; color: #2563eb; font-weight: 600; background: none; border: none; cursor: pointer;">
                                <span x-text="open ? 'Cancelar' : 'Novo Saque'"></span>
                            </button>
                        </div>
                        <div x-show="open" x-cloak style="padding: 1.25rem;">
                            <form method="POST" action="{{ route('admin.commissions.withdrawals.store') }}" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label style="font-size: 0.75rem; color: #6b7280; display: block; margin-bottom: 0.25rem;">Valor (R$)</label>
                                        <input type="number" name="amount" step="0.01" min="0.01" max="{{ $balance }}" required
                                               style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.75rem; color: #6b7280; display: block; margin-bottom: 0.25rem;">Data</label>
                                        <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required
                                               style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                    </div>
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; color: #6b7280; display: block; margin-bottom: 0.25rem;">Motivo</label>
                                    <input type="text" name="reason" required placeholder="Ex: Saque mensal, adiantamento..."
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <button type="submit" style="padding: 0.5rem 1rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; align-self: flex-end;">
                                    Confirmar Saque
                                </button>
                            </form>
                        </div>
                        <div x-show="!open" style="padding: 1.25rem; text-align: center;">
                            <p style="font-size: 0.8rem; color: #6b7280;">Saldo disponível: <strong style="color: #059669;">R$ {{ number_format($balance, 2, ',', '.') }}</strong></p>
                        </div>
                    </div>
                </div>

                {{-- Navegação de Mês --}}
                @php
                    $refDate = \Carbon\Carbon::createFromDate($year, $month, 1);
                    $prevMonth = $refDate->copy()->subMonth();
                    $nextMonth = $refDate->copy()->addMonth();
                @endphp
                <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1rem; background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 0.5rem 1rem;">
                    <a href="{{ route('admin.commissions.index', ['user_id' => $selectedUser->id, 'month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                       style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #f3f4f6; text-decoration: none; color: #374151;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <span style="font-size: 0.875rem; font-weight: 700; color: #111827; text-transform: capitalize; min-width: 120px; text-align: center;">
                        {{ $refDate->translatedFormat('F Y') }}
                    </span>
                    <a href="{{ route('admin.commissions.index', ['user_id' => $selectedUser->id, 'month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                       style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #f3f4f6; text-decoration: none; color: #374151;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- Tabela de Comissões do Mês --}}
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827;">Comissões do Mês</h3>
                    </div>
                    @if($commissions->count() > 0)
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Data</th>
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Venda</th>
                                        <th style="padding: 0.625rem 1rem; text-align: right; font-weight: 600; color: #6b7280;">Total Venda</th>
                                        <th style="padding: 0.625rem 1rem; text-align: right; font-weight: 600; color: #6b7280;">Taxa</th>
                                        <th style="padding: 0.625rem 1rem; text-align: right; font-weight: 600; color: #6b7280;">Comissão</th>
                                        <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #6b7280;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commissions as $commission)
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 0.625rem 1rem; color: #374151;">{{ $commission->created_at->format('d/m/Y H:i') }}</td>
                                            <td style="padding: 0.625rem 1rem;">
                                                <a href="{{ route('sales.show', $commission->sale_id) }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                                                    {{ $commission->sale_number }}
                                                </a>
                                            </td>
                                            <td style="padding: 0.625rem 1rem; text-align: right; color: #374151;">R$ {{ number_format($commission->sale_total, 2, ',', '.') }}</td>
                                            <td style="padding: 0.625rem 1rem; text-align: right; color: #374151;">
                                                @if($commission->commission_type === 'fixed')
                                                    R$ {{ number_format($commission->commission_rate, 2, ',', '.') }}/venda
                                                @else
                                                    {{ number_format($commission->commission_rate, 2, ',', '.') }}%
                                                @endif
                                            </td>
                                            <td style="padding: 0.625rem 1rem; text-align: right; font-weight: 700; color: #059669;">R$ {{ number_format($commission->commission_amount, 2, ',', '.') }}</td>
                                            <td style="padding: 0.625rem 1rem; text-align: center;">
                                                <span style="font-size: 0.7rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px;
                                                    {{ $commission->status === 'approved' ? 'background: #ecfdf5; color: #065f46;' : ($commission->status === 'paid' ? 'background: #eff6ff; color: #1e40af;' : 'background: #fffbeb; color: #92400e;') }}">
                                                    {{ $commission->status === 'approved' ? 'Aprovada' : ($commission->status === 'paid' ? 'Paga' : 'Pendente') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center; color: #6b7280; font-size: 0.875rem;">
                            Nenhuma comissão neste mês.
                        </div>
                    @endif
                </div>

                {{-- Tabela de Saques --}}
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827;">Histórico de Saques</h3>
                    </div>
                    @if($withdrawals->count() > 0)
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Data</th>
                                        <th style="padding: 0.625rem 1rem; text-align: right; font-weight: 600; color: #6b7280;">Valor</th>
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Motivo</th>
                                        <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #6b7280;">Status</th>
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Aprovado por</th>
                                        <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #6b7280;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawals as $withdrawal)
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 0.625rem 1rem; color: #374151;">{{ $withdrawal->date->format('d/m/Y') }}</td>
                                            <td style="padding: 0.625rem 1rem; text-align: right; font-weight: 700; color: #dc2626;">R$ {{ number_format($withdrawal->amount, 2, ',', '.') }}</td>
                                            <td style="padding: 0.625rem 1rem; color: #374151;">{{ $withdrawal->reason }}</td>
                                            <td style="padding: 0.625rem 1rem; text-align: center;">
                                                <span style="font-size: 0.7rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px;
                                                    {{ $withdrawal->status === 'approved' ? 'background: #ecfdf5; color: #065f46;' : ($withdrawal->status === 'rejected' ? 'background: #fef2f2; color: #991b1b;' : 'background: #fffbeb; color: #92400e;') }}">
                                                    {{ $withdrawal->status === 'approved' ? 'Aprovado' : ($withdrawal->status === 'rejected' ? 'Rejeitado' : 'Pendente') }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.625rem 1rem; color: #6b7280;">{{ $withdrawal->approver?->name ?? '-' }}</td>
                                            <td style="padding: 0.625rem 1rem; text-align: center;">
                                                @if($withdrawal->status === 'pending')
                                                    <div style="display: flex; gap: 0.375rem; justify-content: center;">
                                                        <form method="POST" action="{{ route('admin.commissions.withdrawals.approve', $withdrawal) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" style="font-size: 0.7rem; padding: 0.25rem 0.5rem; background: #059669; color: white; border: none; border-radius: 0.375rem; cursor: pointer; font-weight: 600;">Aprovar</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.commissions.withdrawals.reject', $withdrawal) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" style="font-size: 0.7rem; padding: 0.25rem 0.5rem; background: #dc2626; color: white; border: none; border-radius: 0.375rem; cursor: pointer; font-weight: 600;">Rejeitar</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span style="font-size: 0.75rem; color: #9ca3af;">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center; color: #6b7280; font-size: 0.875rem;">
                            Nenhum saque registrado.
                        </div>
                    @endif
                </div>
            @else
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 3rem; text-align: center; color: #6b7280;">
                    Nenhum estagiário cadastrado no sistema.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
