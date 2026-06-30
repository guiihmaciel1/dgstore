<x-app-layout>
    <x-slot name="title">Painel do Estagiário</x-slot>
    <div class="py-6" x-data="{ simSearch: '', simResults: [], simSearching: false, async searchSimulations() { if (this.simSearch.length < 2) { this.simResults = []; return; } this.simSearching = true; try { const res = await fetch(`{{ route('simulations.search') }}?q=${encodeURIComponent(this.simSearch)}`); this.simResults = await res.json(); } catch(e) { this.simResults = []; } this.simSearching = false; } }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46; font-size: 0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b; font-size: 0.875rem;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- BUSCA RÁPIDA DE SIMULAÇÕES --}}
            <div style="margin-bottom: 1rem; position: relative;" @click.outside="simResults = []">
                <div style="position: relative;">
                    <div style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <svg style="width: 1.125rem; height: 1.125rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           x-model="simSearch"
                           @input.debounce.300ms="searchSimulations()"
                           @focus="if(simSearch.length >= 2) searchSimulations()"
                           placeholder="Buscar simulação salva (nome ou telefone do cliente)..."
                           style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-size: 0.875rem; outline: none; background: white; transition: border-color 0.15s;"
                           onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e5e7eb'">
                    <div x-show="simSearching" style="position: absolute; right: 0.875rem; top: 50%; transform: translateY(-50%);">
                        <svg style="width: 1.125rem; height: 1.125rem; color: #6366f1; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                        </svg>
                    </div>
                </div>

                {{-- Resultados da busca --}}
                <div x-show="simResults.length > 0" x-cloak x-transition
                     style="position: absolute; z-index: 50; margin-top: 0.5rem; width: 100%; background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); max-height: 20rem; overflow-y: auto;">
                    <template x-for="snap in simResults" :key="snap.id">
                        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.125rem;">
                                    <span style="font-weight: 600; font-size: 0.8125rem; color: #111827;" x-text="snap.customer?.name || 'Cliente'"></span>
                                    <span style="font-size: 0.6875rem; color: #6b7280; background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem;" x-text="snap.customer?.phone || ''"></span>
                                </div>
                                <div style="font-size: 0.75rem; color: #4b5563;">
                                    <span x-text="snap.product_description"></span>
                                    <span style="color: #9ca3af;"> · </span>
                                    <span style="font-weight: 600;" x-text="'R$ ' + Number(snap.product_price).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                    <span x-show="snap.trade_in_model" style="color: #9ca3af;"> · </span>
                                    <span x-show="snap.trade_in_model" style="color: #7c3aed;" x-text="'Troca: ' + (snap.trade_in_model || '')"></span>
                                </div>
                                <div style="font-size: 0.6875rem; color: #9ca3af; margin-top: 0.125rem;" x-text="new Date(snap.created_at).toLocaleDateString('pt-BR') + ' · Válida por 7 dias'"></div>
                            </div>
                            <div style="display: flex; gap: 0.375rem; flex-shrink: 0;">
                                <a :href="'{{ route('sales.create') }}?snapshot_id=' + snap.id"
                                   style="padding: 0.375rem 0.625rem; background: #111827; color: white; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 600; text-decoration: none; white-space: nowrap;"
                                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                    Abrir Venda
                                </a>
                                <a :href="'{{ route('tools.negotiation-simulator') }}?snap_product=' + encodeURIComponent(snap.product_description) + '&snap_price=' + snap.product_price + (snap.product_cost ? '&snap_cost=' + snap.product_cost : '') + (snap.trade_in_model ? '&snap_tradein_model=' + encodeURIComponent(snap.trade_in_model) + '&snap_tradein_value=' + (snap.trade_in_value || '') + '&snap_tradein_system_value=' + (snap.trade_in_system_value || '') + '&snap_tradein_storage=' + encodeURIComponent(snap.trade_in_storage || '') + '&snap_tradein_battery=' + (snap.trade_in_battery || '') : '')"
                                   style="padding: 0.375rem 0.625rem; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 600; text-decoration: none; white-space: nowrap;"
                                   onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                                    Reabrir Simulador
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Nenhum resultado --}}
                <div x-show="simSearch.length >= 2 && simResults.length === 0 && !simSearching" x-cloak
                     style="position: absolute; z-index: 50; margin-top: 0.5rem; width: 100%; background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); padding: 1.5rem; text-align: center;">
                    <p style="font-size: 0.8125rem; color: #6b7280;">Nenhuma simulação ativa encontrada.</p>
                </div>
            </div>

            {{-- BARRA DE PONTO (Destaque) --}}
            @if($nextPunchType)
                <div style="background: linear-gradient(135deg, {{ $punchButtonColors[$nextPunchType] }}dd 0%, {{ $punchButtonColors[$nextPunchType] }} 100%); border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; color: white; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -20%; right: -5%; width: 200px; height: 200px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                    <div style="position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0;">{{ $punchMessages[$nextPunchType][0] }}</h2>
                            <p style="font-size: 0.875rem; opacity: 0.9; margin-top: 0.25rem;">{{ $punchMessages[$nextPunchType][1] }}</p>
                            @if($timeClockEntries->count() > 0)
                                <div style="display: flex; gap: 0.375rem; margin-top: 0.75rem; flex-wrap: wrap;">
                                    @foreach($timeClockEntries as $entry)
                                        <span style="font-size: 0.65rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: rgba(255,255,255,0.2); font-weight: 500;">
                                            {{ \App\Domain\TimeClock\Models\TimeClockEntry::LABELS[$entry->type] }}: {{ $entry->punched_at->format('H:i') }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('intern.time-clock.punch') }}">
                            @csrf
                            <button type="submit" style="padding: 0.75rem 2rem; background: white; color: {{ $punchButtonColors[$nextPunchType] }}; border: none; border-radius: 0.75rem; font-size: 1rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.15); transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                {{ $punchButtonLabels[$nextPunchType] }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div style="background: linear-gradient(135deg, #059669dd 0%, #059669 100%); border-radius: 1rem; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; color: white;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <div>
                            <h2 style="font-size: 1rem; font-weight: 700; margin: 0;">Todos os pontos registrados hoje!</h2>
                            <div style="display: flex; gap: 0.375rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                @foreach($timeClockEntries as $entry)
                                    <span style="font-size: 0.65rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: rgba(255,255,255,0.2); font-weight: 500;">
                                        {{ \App\Domain\TimeClock\Models\TimeClockEntry::LABELS[$entry->type] }}: {{ $entry->punched_at->format('H:i') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ANIVERSARIANTES DO MÊS --}}
            @if($birthdayCustomers->count() > 0)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; font-size: 0.8rem;">
                <span style="font-size: 0.9rem;">&#127874;</span>
                <span style="color: #6b7280; font-weight: 500;">Próximos aniversariantes do mês:</span>
                @foreach($birthdayCustomers as $customer)
                    <a href="{{ route('customers.show', $customer) }}" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.5rem; border-radius: 9999px; text-decoration: none; font-size: 0.75rem; font-weight: 600; {{ $customer->birth_date->day === now()->day ? 'background: #fef3c7; color: #92400e;' : 'background: #f3f4f6; color: #374151;' }}" title="{{ $customer->phone }}">
                        <span style="color: #ec4899; font-weight: 800;">{{ $customer->birth_date->format('d') }}</span>
                        {{ $customer->name }}
                        @if($customer->birth_date->day === now()->day)
                            <span style="font-size: 0.625rem; font-weight: 800; color: #d97706;">HOJE!</span>
                        @endif
                    </a>
                @endforeach
            </div>
            @endif

            {{-- PRÓXIMO AGENDAMENTO --}}
            @if($nextAppointment)
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('schedule.index') }}" style="display: flex; align-items: center; gap: 0.875rem; padding: 0.75rem 1rem; background: white; border: 1px solid #c7d2fe; border-radius: 0.625rem; text-decoration: none; transition: all 0.15s; box-shadow: 0 1px 3px rgba(99,102,241,0.08);">
                    <div style="width: 2.5rem; height: 2.5rem; background: #eef2ff; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #4f46e5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #4f46e5;">Próximo agendamento</span>
                            <span style="font-size: 0.7rem; font-weight: 600; color: #111827; background: #e0e7ff; padding: 0.0625rem 0.5rem; border-radius: 9999px;">{{ substr($nextAppointment->start_time, 0, 5) }}</span>
                        </div>
                        <div style="font-size: 0.775rem; color: #374151; margin-top: 0.125rem;">
                            <span style="font-weight: 600;">{{ $nextAppointment->customer_name }}</span>
                            @if($nextAppointment->service_description)
                                <span style="color: #9ca3af;">·</span>
                                <span style="color: #6b7280;">{{ $nextAppointment->service_description }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endif

            {{-- CARDS ESTATÍSTICAS --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                {{-- Minhas Vendas --}}
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Minhas Vendas (Mês)</p>
                            <p style="font-size: 1.5rem; font-weight: 800; color: #111827;">{{ $mySalesCount }}
                                <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">vendas</span>
                            </p>
                        </div>
                        <div style="width: 2.5rem; height: 2.5rem; background: #111827; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Comissão do Mês --}}
                <div x-data="{ showInfo: false }" style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; position: relative;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Comissão do Mês</p>
                            <p style="font-size: 1.5rem; font-weight: 800; color: #059669;">R$ {{ number_format($monthCommissions, 2, ',', '.') }}</p>
                        </div>
                        <div style="width: 2.5rem; height: 2.5rem; background: #059669; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.5rem;">
                        <p style="font-size: 0.7rem; color: #374151; margin: 0;">
                            <strong style="color: #059669;">½ venda R$25</strong> · <strong style="color: #059669;">Completa R$50</strong>
                        </p>
                        <button @click="showInfo = !showInfo" style="width: 1.125rem; height: 1.125rem; border-radius: 9999px; background: #e5e7eb; border: none; cursor: pointer; font-size: 0.6rem; font-weight: 800; color: #6b7280; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">i</button>
                    </div>
                    <div x-show="showInfo" x-cloak @click.outside="showInfo = false"
                         style="position: absolute; bottom: calc(100% + 0.5rem); left: 0.5rem; right: 0.5rem; background: #111827; color: white; border-radius: 0.75rem; padding: 0.875rem; font-size: 0.7rem; z-index: 20; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                        <p style="font-weight: 700; margin: 0 0 0.5rem 0; font-size: 0.75rem;">💰 Como funciona a comissão?</p>
                        <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                            <div style="display: flex; align-items: flex-start; gap: 0.375rem;">
                                <span style="font-size: 0.8rem;">🤝</span>
                                <div>
                                    <strong style="color: #34d399;">Meia venda (R$25)</strong><br>
                                    Atender o cliente e fechar a venda
                                </div>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 0.375rem;">
                                <span style="font-size: 0.8rem;">⭐</span>
                                <div>
                                    <strong style="color: #fbbf24;">Venda completa (R$50)</strong><br>
                                    Atender, fechar, receber, transferir e cadastrar no sistema
                                </div>
                            </div>
                        </div>
                        <div style="position: absolute; bottom: -0.375rem; left: 1.5rem; width: 0.75rem; height: 0.75rem; background: #111827; transform: rotate(45deg);"></div>
                    </div>
                </div>

                {{-- Saldo de Comissão --}}
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Saldo Disponível</p>
                            <p style="font-size: 1.5rem; font-weight: 800; color: {{ $commissionBalance >= 0 ? '#059669' : '#dc2626' }};">
                                R$ {{ number_format($commissionBalance, 2, ',', '.') }}
                            </p>
                        </div>
                        <div style="width: 2.5rem; height: 2.5rem; background: #7c3aed; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">
                        Total acumulado: <strong>R$ {{ number_format($totalCommissions, 2, ',', '.') }}</strong>
                        · Sacado: <strong style="color: #dc2626;">R$ {{ number_format($totalWithdrawn, 2, ',', '.') }}</strong>
                    </p>
                </div>

                {{-- Agenda Hoje --}}
                <a href="{{ route('schedule.index') }}" style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; text-decoration: none;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Agenda Hoje</p>
                            <p style="font-size: 1.5rem; font-weight: 800; color: #111827;">{{ $todayAppointments->count() }}
                                <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">agend.</span>
                            </p>
                        </div>
                        <div style="width: 2.5rem; height: 2.5rem; background: #4f46e5; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </a>
            </div>

            {{-- MEUS DADOS --}}
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1.5rem;">
                <h3 style="font-size: 0.875rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Meus Dados</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; font-size: 0.8rem;">
                    <div>
                        <span style="color: #6b7280;">Nome:</span>
                        <span style="font-weight: 600; color: #111827; margin-left: 0.25rem;">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span style="color: #6b7280;">E-mail:</span>
                        <span style="font-weight: 600; color: #111827; margin-left: 0.25rem;">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span style="color: #6b7280;">Cargo:</span>
                        <span style="font-weight: 600; color: #0d9488; margin-left: 0.25rem;">{{ $user->role->label() }}</span>
                    </div>
                    <div>
                        <span style="color: #6b7280;">Comissão:</span>
                        <span style="font-weight: 600; color: #059669; margin-left: 0.25rem;">½ venda R$25</span>
                        <span style="color: #9ca3af; margin: 0 0.125rem;">·</span>
                        <span style="font-weight: 600; color: #059669;">Completa R$50</span>
                    </div>
                </div>
            </div>

            {{-- AGENDA DO DIA --}}
            @if($todayAppointments->count() > 0)
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #111827;">Agenda de Hoje</h3>
                    <a href="{{ route('schedule.index') }}" style="font-size: 0.75rem; color: #4f46e5; text-decoration: none; font-weight: 600;">Ver completa →</a>
                </div>
                <div style="padding: 0.75rem;">
                    @foreach($todayAppointments as $appt)
                        @php
                            $isPast = $appt->end_time <= now()->format('H:i:s');
                            $isNow = $appt->start_time <= now()->format('H:i:s') && $appt->end_time > now()->format('H:i:s');
                        @endphp
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem; margin-bottom: 0.25rem; {{ $isNow ? 'background: #eef2ff; border: 1px solid #c7d2fe;' : ($isPast ? 'opacity: 0.5;' : '') }}">
                            <div style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background: {{ $isNow ? '#4f46e5' : '#9ca3af' }}; flex-shrink: 0;"></div>
                            <span style="font-size: 0.8rem; font-weight: 700; color: {{ $isNow ? '#4f46e5' : '#111827' }}; min-width: 4rem; font-variant-numeric: tabular-nums;">
                                {{ substr($appt->start_time, 0, 5) }} - {{ substr($appt->end_time, 0, 5) }}
                            </span>
                            <span style="font-size: 0.8rem; font-weight: 600; color: #111827;">{{ $appt->customer_name }}</span>
                            @if($appt->service_description)
                                <span style="font-size: 0.7rem; color: #6b7280;">· {{ $appt->service_description }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- CHECKLIST DIÁRIO --}}
            @include('intern._daily-checklist')

        </div>

    </div>
</x-app-layout>
