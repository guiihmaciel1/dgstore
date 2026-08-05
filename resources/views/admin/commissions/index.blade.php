<x-app-layout>
    <x-slot name="title">Comissões</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(16,185,129,0.1); border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #6ee7b7; font-size: 0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Comissões</h1>
                    <p class="text-sm text-dg-500">Gerenciar comissões e saques dos vendedores</p>
                </div>
            </div>

            {{-- Seletor de Vendedora --}}
            <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('admin.commissions.index') }}" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: #a4a4a4;">Vendedor:</label>
                    <select name="user_id" onchange="this.form.submit()" style="padding: 0.5rem 0.75rem; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; min-width: 200px;">
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
                    <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                        <p style="font-size: 0.75rem; color: #818181; font-weight: 500;">Saldo Disponível</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: {{ $balance >= 0 ? '#059669' : '#dc2626' }};">
                            R$ {{ number_format($balance, 2, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                        <p style="font-size: 0.75rem; color: #818181; font-weight: 500;">Comissões do Mês</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: #e3e3e3;">
                            R$ {{ number_format($monthEarned, 2, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                        <p style="font-size: 0.75rem; color: #818181; font-weight: 500;">Total Acumulado</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: #e3e3e3;">
                            R$ {{ number_format($totalEarned, 2, ',', '.') }}
                        </p>
                    </div>
                    <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                        <p style="font-size: 0.75rem; color: #818181; font-weight: 500;">Total Sacado</p>
                        <p style="font-size: 1.5rem; font-weight: 800; color: #fca5a5;">
                            R$ {{ number_format($totalWithdrawn, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Lançamento Manual de Comissão --}}
                <div x-data="{ open: false }" style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden; margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #e3e3e3;">Lançar Comissão Manual</h3>
                        <button @click="open = !open" style="font-size: 0.75rem; color: #c4b5fd; font-weight: 600; background: none; border: none; cursor: pointer;">
                            <span x-text="open ? 'Cancelar' : '+ Novo Lançamento'"></span>
                        </button>
                    </div>
                    <div x-show="open" x-cloak style="padding: 1.25rem;">
                        <form method="POST" action="{{ route('admin.commissions.manual.store') }}" style="display: flex; flex-direction: column; gap: 0.75rem;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label style="font-size: 0.75rem; color: #818181; display: block; margin-bottom: 0.25rem;">Valor (R$)</label>
                                    <input type="number" name="commission_amount" step="0.01" min="0.01" required
                                           style="width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; color: #818181; display: block; margin-bottom: 0.25rem;">Data</label>
                                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required
                                           style="width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; color: #818181; display: block; margin-bottom: 0.25rem;">Descrição</label>
                                    <input type="text" name="description" required placeholder="Ex: Bônus, ajuste, premiação..."
                                           style="width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                            </div>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" style="padding: 0.5rem 1rem; background: #7c3aed; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                    Lançar Comissão
                                </button>
                            </div>
                        </form>
                    </div>
                    <div x-show="!open" style="padding: 0.75rem 1.25rem;">
                        <p style="font-size: 0.8rem; color: #818181;">Lance comissões extras como bônus, ajustes ou premiações sem vínculo com uma venda.</p>
                    </div>
                </div>

                {{-- Metodologia + Saque --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    {{-- Nova Metodologia Info --}}
                    <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                        <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                            <h3 style="font-size: 0.875rem; font-weight: 600; color: #e3e3e3;">Metodologia de Comissão</h3>
                        </div>
                        <div style="padding: 1.25rem; font-size: 0.8rem; color: #a4a4a4;">
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669; flex-shrink: 0;"></span>
                                    <span><strong>10%</strong> sobre o lucro do aparelho</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #2563eb; flex-shrink: 0;"></span>
                                    <span><strong>2%</strong> da economia na avaliação trade-in <span style="color: #818181;">(máx. 20% desconto)</span></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #7c3aed; flex-shrink: 0;"></span>
                                    <span>Acessórios: <strong>50%</strong> capinha / <strong>20%</strong> carregador <span style="color: #818181;">(acima do preço base)</span></span>
                                </div>
                            </div>
                            <p style="margin-top: 0.75rem; font-size: 0.7rem; color: #666666;">Apenas vendas Cliente Final. Cálculo automático ao registrar a venda.</p>
                        </div>
                    </div>

                    {{-- Registrar Saque --}}
                    <div x-data="{ open: false }" style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                        <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-size: 0.875rem; font-weight: 600; color: #e3e3e3;">Registrar Saque</h3>
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
                                        <label style="font-size: 0.75rem; color: #818181; display: block; margin-bottom: 0.25rem;">Valor (R$)</label>
                                        <input type="number" name="amount" step="0.01" min="0.01" max="{{ $balance }}" required
                                               style="width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.75rem; color: #818181; display: block; margin-bottom: 0.25rem;">Data</label>
                                        <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required
                                               style="width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                    </div>
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; color: #818181; display: block; margin-bottom: 0.25rem;">Motivo</label>
                                    <input type="text" name="reason" required placeholder="Ex: Saque mensal, adiantamento..."
                                           style="width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <button type="submit" style="padding: 0.5rem 1rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; align-self: flex-end;">
                                    Confirmar Saque
                                </button>
                            </form>
                        </div>
                        <div x-show="!open" style="padding: 1.25rem; text-align: center;">
                            <p style="font-size: 0.8rem; color: #818181;">Saldo disponível: <strong style="color: #059669;">R$ {{ number_format($balance, 2, ',', '.') }}</strong></p>
                        </div>
                    </div>
                </div>

                {{-- Navegação de Mês --}}
                @php
                    $refDate = \Carbon\Carbon::createFromDate($year, $month, 1);
                    $prevMonth = $refDate->copy()->subMonth();
                    $nextMonth = $refDate->copy()->addMonth();
                @endphp
                <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1rem; background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 0.5rem 1rem;">
                    <a href="{{ route('admin.commissions.index', ['user_id' => $selectedUser->id, 'month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                       style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #222222; text-decoration: none; color: #a4a4a4;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <span style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3; text-transform: capitalize; min-width: 120px; text-align: center;">
                        {{ $refDate->translatedFormat('F Y') }}
                    </span>
                    <a href="{{ route('admin.commissions.index', ['user_id' => $selectedUser->id, 'month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                       style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #222222; text-decoration: none; color: #a4a4a4;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- Tabela de Comissões do Mês --}}
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a; border-radius: 0.75rem 0.75rem 0 0;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #e3e3e3;">Comissões do Mês</h3>
                    </div>
                    @if($commissions->count() > 0)
                        <div style="overflow: visible; position: relative;">
                            <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #818181;">Data</th>
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #818181;">Origem / Descrição</th>
                                        <th style="padding: 0.625rem 1rem; text-align: right; font-weight: 600; color: #818181;">Total Venda</th>
                                        <th style="padding: 0.625rem 1rem; text-align: right; font-weight: 600; color: #818181;">Comissão</th>
                                        <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commissions as $commission)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); position: relative;" class="commission-row group" x-data="{ showTip: false }" @mouseenter="showTip = true" @mouseleave="showTip = false">
                                            <td style="padding: 0.625rem 1rem; color: #a4a4a4;">{{ $commission->created_at->format('d/m/Y H:i') }}</td>
                                            <td style="padding: 0.625rem 1rem;">
                                                @if($commission->is_manual)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                                        <span style="font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 9999px; background: #f3e8ff; color: #c4b5fd; text-transform: uppercase; letter-spacing: 0.03em;">Manual</span>
                                                        <span style="color: #a4a4a4;">{{ $commission->description }}</span>
                                                    </span>
                                                @else
                                                    <a href="{{ route('sales.show', $commission->sale_id) }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                                                        {{ $commission->sale_number }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td style="padding: 0.625rem 1rem; text-align: right; color: #a4a4a4;">
                                                @if($commission->sale_total !== null)
                                                    R$ {{ number_format($commission->sale_total, 2, ',', '.') }}
                                                @else
                                                    <span style="color: #666666;">—</span>
                                                @endif
                                            </td>
                                            <td style="padding: 0.625rem 1rem; text-align: right; font-weight: 700; color: #059669; position: relative;">
                                                R$ {{ number_format($commission->commission_amount, 2, ',', '.') }}
                                                {{-- Tooltip com breakdown --}}
                                                @if(!$commission->is_manual && ($commission->customer_name || $commission->product_summary))
                                                <div x-show="showTip" x-cloak x-transition
                                                     style="position: absolute; bottom: calc(100% + 8px); right: 0; z-index: 9999; background: #1f2937; color: white; border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.75rem; min-width: 220px; max-width: 320px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); pointer-events: none; white-space: nowrap;">
                                                    @if($commission->customer_name)
                                                        <div style="margin-bottom: 0.375rem;">
                                                            <span style="color: #666666;">Cliente:</span>
                                                            <span style="font-weight: 600;">{{ $commission->customer_name }}</span>
                                                        </div>
                                                    @endif
                                                    @if($commission->product_summary)
                                                        <div style="margin-bottom: 0.375rem; white-space: normal;">
                                                            <span style="color: #666666;">Produto:</span>
                                                            <span style="font-weight: 600;">{{ $commission->product_summary }}</span>
                                                        </div>
                                                    @endif
                                                    @if($commission->profit_commission > 0 || $commission->tradein_commission > 0 || $commission->accessory_commission > 0)
                                                        <div style="border-top: 1px solid #374151; margin-top: 0.375rem; padding-top: 0.375rem;">
                                                            @if($commission->profit_commission > 0)
                                                                <div style="display: flex; justify-content: space-between; gap: 1rem;">
                                                                    <span style="color: #666666;">Aparelho:</span>
                                                                    <span style="color: #34d399;">R$ {{ number_format($commission->profit_commission, 2, ',', '.') }}</span>
                                                                </div>
                                                            @endif
                                                            @if($commission->tradein_commission > 0)
                                                                <div style="display: flex; justify-content: space-between; gap: 1rem;">
                                                                    <span style="color: #666666;">Trade-in:</span>
                                                                    <span style="color: #34d399;">R$ {{ number_format($commission->tradein_commission, 2, ',', '.') }}</span>
                                                                </div>
                                                            @endif
                                                            @if($commission->accessory_commission > 0)
                                                                <div style="display: flex; justify-content: space-between; gap: 1rem;">
                                                                    <span style="color: #666666;">Acessórios:</span>
                                                                    <span style="color: #34d399;">R$ {{ number_format($commission->accessory_commission, 2, ',', '.') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    <div style="position: absolute; bottom: -4px; right: 1rem; width: 8px; height: 8px; background: #1f2937; transform: rotate(45deg);"></div>
                                                </div>
                                                @endif
                                            </td>
                                            <td style="padding: 0.625rem 1rem; text-align: center;">
                                                <span style="font-size: 0.7rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px;
                                                    {{ $commission->status === 'approved' ? 'background: rgba(16,185,129,0.1); color: #6ee7b7;' : ($commission->status === 'paid' ? 'background: rgba(59,130,246,0.1); color: #93c5fd;' : 'background: #141414beb; color: #fbbf24;') }}">
                                                    {{ $commission->status === 'approved' ? 'Aprovada' : ($commission->status === 'paid' ? 'Paga' : 'Pendente') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center; color: #818181; font-size: 0.875rem;">
                            Nenhuma comissão neste mês.
                        </div>
                    @endif
                </div>

                {{-- Tabela de Saques --}}
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                    <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #e3e3e3;">Histórico de Saques</h3>
                    </div>
                    @if($withdrawals->count() > 0)
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #818181;">Data</th>
                                        <th style="padding: 0.625rem 1rem; text-align: right; font-weight: 600; color: #818181;">Valor</th>
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #818181;">Motivo</th>
                                        <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Status</th>
                                        <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #818181;">Aprovado por</th>
                                        <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawals as $withdrawal)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                            <td style="padding: 0.625rem 1rem; color: #a4a4a4;">{{ $withdrawal->date->format('d/m/Y') }}</td>
                                            <td style="padding: 0.625rem 1rem; text-align: right; font-weight: 700; color: #fca5a5;">R$ {{ number_format($withdrawal->amount, 2, ',', '.') }}</td>
                                            <td style="padding: 0.625rem 1rem; color: #a4a4a4;">{{ $withdrawal->reason }}</td>
                                            <td style="padding: 0.625rem 1rem; text-align: center;">
                                                <span style="font-size: 0.7rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px;
                                                    {{ $withdrawal->status === 'approved' ? 'background: rgba(16,185,129,0.1); color: #6ee7b7;' : ($withdrawal->status === 'rejected' ? 'background: rgba(239,68,68,0.1); color: #fca5a5;' : 'background: #141414beb; color: #fbbf24;') }}">
                                                    {{ $withdrawal->status === 'approved' ? 'Aprovado' : ($withdrawal->status === 'rejected' ? 'Rejeitado' : 'Pendente') }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.625rem 1rem; color: #818181;">{{ $withdrawal->approver?->name ?? '-' }}</td>
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
                                                    <span style="font-size: 0.75rem; color: #666666;">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center; color: #818181; font-size: 0.875rem;">
                            Nenhum saque registrado.
                        </div>
                    @endif
                </div>
            @else
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 3rem; text-align: center; color: #818181;">
                    Nenhum vendedor cadastrado no sistema.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
