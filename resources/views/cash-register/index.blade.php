<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4"><x-alert type="success">{{ session('success') }}</x-alert></div>
            @endif
            @if(session('error'))
                <div class="mb-4"><x-alert type="error">{{ session('error') }}</x-alert></div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Fluxo de Caixa</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Controle diario de entradas e saidas</p>
                </div>
            </div>

            @if($openRegister)
                {{-- ═══ CAIXA ABERTO ═══ --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Coluna Esquerda -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                        <!-- Status do Caixa -->
                        <div style="background: linear-gradient(135deg, #111827, #1f2937); border-radius: 0.75rem; padding: 1.5rem; color: white;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 0.5rem; height: 0.5rem; background: #4ade80; border-radius: 50%; animation: pulse 2s infinite;"></div>
                                    <span style="font-weight: 600;">Caixa Aberto</span>
                                </div>
                                <span style="font-size: 0.75rem; color: #9ca3af;">{{ $openRegister->opened_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div style="font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">Aberto por {{ $openRegister->openedByUser?->name }}</div>

                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem;">
                                <div style="text-align: center;">
                                    <p style="font-size: 0.625rem; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em;">Abertura</p>
                                    <p style="font-size: 1.125rem; font-weight: 700;">R$ {{ number_format($openRegister->opening_balance, 2, ',', '.') }}</p>
                                </div>
                                <div style="text-align: center;">
                                    <p style="font-size: 0.625rem; text-transform: uppercase; color: #4ade80; letter-spacing: 0.05em;">Entradas</p>
                                    <p style="font-size: 1.125rem; font-weight: 700; color: #4ade80;">+ R$ {{ number_format($summary['total_inflow'], 2, ',', '.') }}</p>
                                </div>
                                <div style="text-align: center;">
                                    <p style="font-size: 0.625rem; text-transform: uppercase; color: #f87171; letter-spacing: 0.05em;">Saidas</p>
                                    <p style="font-size: 1.125rem; font-weight: 700; color: #f87171;">- R$ {{ number_format($summary['total_outflow'], 2, ',', '.') }}</p>
                                </div>
                            </div>

                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.15); text-align: center;">
                                <p style="font-size: 0.625rem; text-transform: uppercase; color: #9ca3af;">Saldo Esperado</p>
                                <p style="font-size: 1.5rem; font-weight: 800;">R$ {{ number_format($summary['expected_balance'], 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Resumo por tipo -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Resumo por Tipo</h3>
                            </div>
                            <div style="padding: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                @foreach($summary['by_type'] as $type => $data)
                                    @if($data['count'] > 0)
                                        @php
                                            $typeColors = [
                                                'green' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'blue' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                'red' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                                'purple' => ['bg' => '#f5f3ff', 'color' => '#7c3aed'],
                                                'orange' => ['bg' => '#fff7ed', 'color' => '#ea580c'],
                                            ];
                                            $tc = $typeColors[$data['color']] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                        @endphp
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0.75rem; background: {{ $tc['bg'] }}; border-radius: 0.375rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="font-size: 0.875rem; color: {{ $tc['color'] }}; font-weight: 500;">{{ $data['label'] }}</span>
                                                <span style="font-size: 0.625rem; background: {{ $tc['color'] }}; color: white; padding: 0.125rem 0.375rem; border-radius: 9999px;">{{ $data['count'] }}</span>
                                            </div>
                                            <span style="font-size: 0.875rem; font-weight: 700; color: {{ $tc['color'] }};">
                                                {{ $data['is_inflow'] ? '+' : '-' }} R$ {{ number_format($data['total'], 2, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Registrar Movimentacao -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Registrar Movimentacao</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <form method="POST" action="{{ route('cash-register.entry', $openRegister) }}">
                                    @csrf
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Tipo</label>
                                            <select name="type" required style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                                <option value="supply">Suprimento (entrada)</option>
                                                <option value="withdrawal">Sangria (saida)</option>
                                                <option value="expense">Despesa (saida)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Valor</label>
                                            <input type="number" name="amount" required min="0.01" step="0.01" placeholder="0,00"
                                                   style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Descricao</label>
                                        <input type="text" name="description" required placeholder="Ex: Sangria para banco, troco, etc."
                                               style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                    </div>
                                    <button type="submit" style="width: 100%; padding: 0.625rem; background: #111827; color: white; font-size: 0.875rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;"
                                            onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                        Registrar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Direita -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <!-- Movimentacoes do dia -->
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Movimentacoes de Hoje</h3>
                            </div>
                            @if($openRegister->entries->isEmpty())
                                <div style="padding: 2rem; text-align: center; color: #6b7280;">
                                    Nenhuma movimentacao registrada.
                                </div>
                            @else
                                <div style="max-height: 400px; overflow-y: auto;">
                                    @foreach($openRegister->entries->sortByDesc('created_at') as $entry)
                                        @php
                                            $entryColors = [
                                                'sale' => '#16a34a', 'supply' => '#2563eb',
                                                'withdrawal' => '#dc2626', 'trade_in' => '#7c3aed', 'expense' => '#ea580c',
                                            ];
                                            $ec = $entryColors[$entry->type->value] ?? '#6b7280';
                                        @endphp
                                        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <span style="font-size: 0.625rem; padding: 0.125rem 0.5rem; background: {{ $ec }}15; color: {{ $ec }}; border-radius: 9999px; font-weight: 600;">
                                                        {{ $entry->type->label() }}
                                                    </span>
                                                    <span style="font-size: 0.75rem; color: #9ca3af;">{{ $entry->created_at->format('H:i') }}</span>
                                                </div>
                                                <p style="font-size: 0.875rem; color: #374151; margin-top: 0.25rem;">{{ $entry->description }}</p>
                                                <p style="font-size: 0.625rem; color: #9ca3af;">por {{ $entry->user?->name }}</p>
                                            </div>
                                            <span style="font-size: 0.875rem; font-weight: 700; color: {{ $ec }}; white-space: nowrap;">
                                                {{ $entry->formatted_amount }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Fechar Caixa -->
                        <div style="background: white; border-radius: 0.75rem; border: 2px solid #fecaca; overflow: hidden;">
                            <div style="padding: 1rem; background: #fef2f2; border-bottom: 1px solid #fecaca;">
                                <h3 style="font-weight: 600; color: #991b1b;">Fechar Caixa</h3>
                            </div>
                            <div style="padding: 1.25rem;">
                                <form method="POST" action="{{ route('cash-register.close', $openRegister) }}">
                                    @csrf
                                    <div style="margin-bottom: 0.75rem;">
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                            Valor Contado no Caixa (R$)
                                        </label>
                                        <input type="number" name="closing_balance" required min="0" step="0.01"
                                               placeholder="Conte o dinheiro e informe o valor"
                                               style="width: 100%; padding: 0.625rem; border: 2px solid #fecaca; border-radius: 0.375rem; font-size: 1rem; font-weight: 600;">
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Observacoes (opcional)</label>
                                        <textarea name="closing_notes" rows="2" placeholder="Alguma observacao sobre o fechamento..."
                                                  style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; resize: vertical;"></textarea>
                                    </div>
                                    <button type="submit" style="width: 100%; padding: 0.625rem; background: #dc2626; color: white; font-size: 0.875rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;"
                                            onclick="return confirm('Tem certeza que deseja fechar o caixa?')"
                                            onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                                        Fechar Caixa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- ═══ CAIXA FECHADO ═══ --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Abrir Caixa -->
                    <div style="background: white; border-radius: 0.75rem; border: 2px solid #bbf7d0; overflow: hidden;">
                        <div style="padding: 1.25rem; background: #f0fdf4; border-bottom: 1px solid #bbf7d0; text-align: center;">
                            <svg style="width: 3rem; height: 3rem; color: #16a34a; margin: 0 auto 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #166534;">Abrir Caixa</h3>
                            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Nenhum caixa aberto no momento</p>
                        </div>
                        <div style="padding: 1.5rem;">
                            <form method="POST" action="{{ route('cash-register.open') }}">
                                @csrf
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Valor Inicial (R$)</label>
                                    <input type="number" name="opening_balance" required min="0" step="0.01" value="0"
                                           style="width: 100%; padding: 0.75rem; border: 2px solid #d1d5db; border-radius: 0.5rem; font-size: 1.125rem; font-weight: 600; text-align: center;">
                                </div>
                                <button type="submit" style="width: 100%; padding: 0.75rem; background: #16a34a; color: white; font-size: 1rem; font-weight: 700; border-radius: 0.5rem; border: none; cursor: pointer;"
                                        onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                                    Abrir Caixa
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Historico -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <h3 style="font-weight: 600; color: #111827;">Historico de Caixas</h3>
                        </div>
                        @if($history->isEmpty())
                            <div style="padding: 2rem; text-align: center; color: #6b7280;">Nenhum historico.</div>
                        @else
                            <div style="max-height: 400px; overflow-y: auto;">
                                @foreach($history as $register)
                                    <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <span style="font-size: 0.875rem; font-weight: 600; color: #111827;">{{ $register->opened_at->format('d/m/Y') }}</span>
                                                <span style="font-size: 0.75rem; color: #6b7280;">{{ $register->opened_at->format('H:i') }} - {{ $register->closed_at?->format('H:i') ?? '...' }}</span>
                                            </div>
                                            @if($register->status->value === 'closed')
                                                @php $diff = (float) $register->difference; @endphp
                                                <span style="font-size: 0.75rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px;
                                                    {{ $diff == 0 ? 'background: #f0fdf4; color: #16a34a;' : ($diff > 0 ? 'background: #eff6ff; color: #2563eb;' : 'background: #fef2f2; color: #dc2626;') }}">
                                                    {{ $diff == 0 ? 'OK' : ($diff > 0 ? '+R$' . number_format($diff, 2, ',', '.') : '-R$' . number_format(abs($diff), 2, ',', '.')) }}
                                                </span>
                                            @else
                                                <span style="font-size: 0.75rem; font-weight: 600; padding: 0.125rem 0.5rem; background: #f0fdf4; color: #16a34a; border-radius: 9999px;">Aberto</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                                            Abertura: R$ {{ number_format($register->opening_balance, 2, ',', '.') }}
                                            @if($register->closing_balance !== null)
                                                | Fechamento: R$ {{ number_format($register->closing_balance, 2, ',', '.') }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        @media (max-width: 768px) {
            div[style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
