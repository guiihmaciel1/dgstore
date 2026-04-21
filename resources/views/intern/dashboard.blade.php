<x-app-layout>
    <x-slot name="title">Painel do Estagiário</x-slot>
    <div class="py-6" x-data="{ stockModalOpen: false }">
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

            {{-- AÇÕES RÁPIDAS --}}
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                <a href="{{ route('sales.create') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: linear-gradient(to right, #10b981, #16a34a); color: white; font-size: 0.75rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; box-shadow: 0 2px 6px rgba(16,185,129,0.3);">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Nova Venda
                </a>
                <button @click="stockModalOpen = true" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: linear-gradient(to right, #f59e0b, #ea580c); color: white; font-size: 0.75rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(245,158,11,0.3);">
                    📱 Seminovos
                </button>
                <a href="{{ route('schedule.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: linear-gradient(to right, #6366f1, #4f46e5); color: white; font-size: 0.75rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; box-shadow: 0 2px 6px rgba(99,102,241,0.3);">
                    📅 Agenda
                </a>
                <a href="{{ route('marketing.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb;">
                    📣 Marketing
                </a>
                <a href="{{ route('customers.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb;">
                    👥 Clientes
                </a>
                <a href="{{ route('tools.stone-calculator') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb;">
                    💳 Calculadora Stone
                </a>
            </div>

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

        </div>

        {{-- MODAL CATÁLOGO DE ESTOQUE --}}
        <div x-show="stockModalOpen" x-cloak
             x-data="stockCatalogModal()"
             style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">
            <div @click="stockModalOpen = false" style="position: absolute; inset: 0; background: rgba(0,0,0,0.5);"></div>
            <div @click.stop style="position: relative; background: white; border-radius: 1rem; width: 100%; max-width: 1000px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.25);">
                <div class="p-4 sm:px-6 sm:py-5 border-b border-gray-200 rounded-t-2xl shrink-0">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-900 m-0">📱 Nosso Estoque</h2>
                            <p class="text-xs text-gray-500 mt-1 mb-0">Consulte o que temos disponível para venda</p>
                        </div>
                        <button @click="stockModalOpen = false" class="w-8 h-8 rounded-lg bg-gray-100 border-none cursor-pointer flex items-center justify-center text-base text-gray-500 shrink-0 hover:bg-gray-200 transition-colors">✕</button>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        @if(count($stockItems['used']) > 0)
                        <button @click="copyUsed()" class="inline-flex items-center justify-center gap-1.5 py-2 px-3 bg-orange-50 text-orange-600 text-[0.7rem] font-bold rounded-lg border border-orange-200 cursor-pointer transition-colors whitespace-nowrap hover:bg-orange-100">
                            <span x-text="copiedUsed ? '✓ Copiado!' : '📋 Copiar Seminovos'"></span>
                        </button>
                        @endif
                        @if(count($stockItems['new']) > 0)
                        <button @click="copyNew()" class="inline-flex items-center justify-center gap-1.5 py-2 px-3 bg-blue-50 text-blue-600 text-[0.7rem] font-bold rounded-lg border border-blue-200 cursor-pointer transition-colors whitespace-nowrap hover:bg-blue-100">
                            <span x-text="copiedNew ? '✓ Copiado!' : '📋 Copiar Novos'"></span>
                        </button>
                        @endif
                    </div>
                </div>
                <div style="padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1;">
                    @if(count($stockItems['used']) > 0)
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <span style="font-size: 1.125rem;">🔄</span>
                            <h3 style="font-size: 0.9375rem; font-weight: 800; color: #111827; margin: 0;">Seminovos</h3>
                            <span style="font-size: 0.65rem; font-weight: 700; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #fff7ed; color: #ea580c;">{{ $stockItems['usedCount'] }} un.</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.625rem;">
                            @foreach($stockItems['used'] as $idx => $item)
                            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl border border-orange-200 p-3.5 relative flex flex-col">
                                <div class="flex items-start justify-between gap-1">
                                    <span class="text-[0.55rem] font-extrabold px-1.5 py-0.5 rounded-full bg-orange-600 text-white uppercase tracking-wide">SEMINOVO</span>
                                    @if($item['qty'] > 1)
                                    <span class="text-[0.7rem] font-extrabold text-orange-600">{{ $item['qty'] }}x</span>
                                    @endif
                                </div>
                                <p class="text-[0.8rem] font-bold text-gray-900 mt-2 mb-1 leading-tight">{{ $item['name'] }}</p>
                                <div class="flex flex-wrap gap-1 mb-1.5">
                                    @if($item['storage'])
                                    <span class="text-[0.625rem] font-semibold px-1.5 py-px rounded-full bg-white text-gray-700 border border-gray-200">💾 {{ $item['storage'] }}</span>
                                    @endif
                                    @if($item['color'])
                                    <span class="text-[0.625rem] font-semibold px-1.5 py-px rounded-full bg-white text-gray-700 border border-gray-200">🎨 {{ $item['color'] }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1 mb-1.5">
                                    @if($item['battery'])
                                    <span class="text-[0.6rem] font-semibold px-1.5 py-px rounded-full {{ $item['battery'] >= 80 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">🔋 {{ $item['battery'] }}%</span>
                                    @endif
                                    @if($item['has_box'])
                                    <span class="text-[0.6rem] font-semibold px-1.5 py-px rounded-full bg-green-50 text-green-600">📦 Caixa</span>
                                    @endif
                                    @if($item['has_cable'])
                                    <span class="text-[0.6rem] font-semibold px-1.5 py-px rounded-full bg-green-50 text-green-600">🔌 Cabo</span>
                                    @endif
                                </div>
                                @if(!empty($item['notes']))
                                <p class="text-[0.65rem] text-gray-500 italic mb-1.5 leading-snug">📝 {{ $item['notes'] }}</p>
                                @endif
                                @if($item['price'] > 0)
                                <div class="mt-auto">
                                    <span class="text-[0.7rem] text-gray-400 line-through">De R$ {{ number_format($item['price'] + 200, 2, ',', '.') }}</span>
                                    <p class="text-[0.875rem] font-extrabold text-emerald-600 m-0">Por R$ {{ number_format($item['price'], 2, ',', '.') }}</p>
                                </div>
                                @endif
                                <button type="button" @click="copyItem({{ $idx }})"
                                        class="mt-2 w-full py-1.5 rounded-lg text-[0.7rem] font-bold border-none cursor-pointer flex items-center justify-center gap-1 transition-colors bg-orange-600 text-white hover:bg-orange-700"
                                        :class="copiedIdx === {{ $idx }} ? '!bg-emerald-600' : ''">
                                    <span x-text="copiedIdx === {{ $idx }} ? '✓ Copiado!' : '📋 Enviar p/ cliente'"></span>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if(count($stockItems['new']) > 0)
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <span style="font-size: 1.125rem;">✨</span>
                            <h3 style="font-size: 0.9375rem; font-weight: 800; color: #111827; margin: 0;">Estoque Novo</h3>
                            <span style="font-size: 0.65rem; font-weight: 700; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #eff6ff; color: #2563eb;">{{ $stockItems['newCount'] }} mod.</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.625rem;">
                            @foreach($stockItems['new'] as $item)
                            <div style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border-radius: 0.75rem; border: 1px solid #bfdbfe; padding: 0.875rem; position: relative;">
                                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.25rem;">
                                    <span style="font-size: 0.55rem; font-weight: 800; padding: 0.1rem 0.375rem; border-radius: 9999px; background: #2563eb; color: white; text-transform: uppercase; letter-spacing: 0.04em;">NOVO</span>
                                    @if($item['qty'] > 1)
                                    <span style="font-size: 0.7rem; font-weight: 800; color: #2563eb;">{{ $item['qty'] }}x</span>
                                    @endif
                                </div>
                                <p style="font-size: 0.8rem; font-weight: 700; color: #111827; margin: 0.5rem 0 0.25rem 0; line-height: 1.2;">{{ $item['name'] }}</p>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; margin-bottom: 0.375rem;">
                                    @if($item['storage'])
                                    <span style="font-size: 0.625rem; font-weight: 600; padding: 0.0625rem 0.375rem; border-radius: 9999px; background: white; color: #374151; border: 1px solid #e5e7eb;">💾 {{ $item['storage'] }}</span>
                                    @endif
                                    @if($item['color'])
                                    <span style="font-size: 0.625rem; font-weight: 600; padding: 0.0625rem 0.375rem; border-radius: 9999px; background: white; color: #374151; border: 1px solid #e5e7eb;">🎨 {{ $item['color'] }}</span>
                                    @endif
                                </div>
                                @if($item['price'] > 0)
                                <p style="font-size: 0.875rem; font-weight: 800; color: #2563eb; margin: 0;">R$ {{ number_format($item['price'], 2, ',', '.') }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if(count($stockItems['used']) === 0 && count($stockItems['new']) === 0)
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <p style="font-size: 2rem; margin-bottom: 0.5rem;">📭</p>
                        <p style="font-size: 0.875rem;">Nenhum produto no estoque no momento.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <script>
            function stockCatalogModal() {
                const usedItems = @json($stockItems['used']);
                const newItems = @json($stockItems['new']);
                return {
                    copiedUsed: false,
                    copiedNew: false,
                    copiedIdx: null,
                    _formatPrice(v) {
                        return 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    },
                    _buildUsedMsg() {
                        let msg = '📱 *SEMINOVOS DISPONÍVEIS - DG Store* 📱\n\n';
                        usedItems.forEach(i => {
                            let line = '• ' + i.name;
                            if (i.storage) line += ' ' + i.storage;
                            if (i.color) line += ' ' + i.color;
                            if (i.battery) line += ' 🔋' + i.battery + '%';
                            if (i.price > 0) line += ' - *' + this._formatPrice(i.price) + '*';
                            if (i.qty > 1) line += ' (' + i.qty + 'x)';
                            msg += line + '\n';
                        });
                        msg += '\n_Sujeito à disponibilidade_';
                        return msg;
                    },
                    _buildNewMsg() {
                        let msg = '✨ *ESTOQUE NOVO - DG Store* ✨\n\n';
                        newItems.forEach(i => {
                            let line = '• ' + i.name;
                            if (i.storage) line += ' ' + i.storage;
                            if (i.color) line += ' ' + i.color;
                            if (i.price > 0) line += ' - *' + this._formatPrice(i.price) + '*';
                            if (i.qty > 1) line += ' (' + i.qty + 'x)';
                            msg += line + '\n';
                        });
                        msg += '\n_Sujeito à disponibilidade_';
                        return msg;
                    },
                    _buildItemMsg(item) {
                        let lines = [];
                        let title = '📱 *' + item.name;
                        if (item.storage) title += ' ' + item.storage;
                        if (item.color) title += ' ' + item.color;
                        title += '*';
                        lines.push(title);
                        lines.push('');
                        let details = [];
                        if (item.battery) details.push('🔋 Bateria: *' + item.battery + '%*');
                        if (item.has_box) details.push('📦 Com caixa');
                        if (item.has_cable) details.push('🔌 Com cabo');
                        if (!item.has_box) details.push('📦 Sem caixa');
                        if (!item.has_cable) details.push('🔌 Sem cabo');
                        if (details.length) lines.push(details.join(' | '));
                        if (item.notes) {
                            lines.push('📝 _' + item.notes + '_');
                        }
                        if (item.price > 0) {
                            lines.push('');
                            lines.push('~De ' + this._formatPrice(item.price + 200) + '~');
                            lines.push('✅ *Por ' + this._formatPrice(item.price) + '*');
                        }
                        lines.push('');
                        lines.push('_DG Store - Sujeito à disponibilidade_');
                        return lines.join('\n');
                    },
                    copyItem(idx) {
                        const item = usedItems[idx];
                        if (!item) return;
                        navigator.clipboard.writeText(this._buildItemMsg(item));
                        this.copiedIdx = idx;
                        setTimeout(() => this.copiedIdx = null, 2000);
                    },
                    copyUsed() {
                        navigator.clipboard.writeText(this._buildUsedMsg());
                        this.copiedUsed = true;
                        setTimeout(() => this.copiedUsed = false, 2000);
                    },
                    copyNew() {
                        navigator.clipboard.writeText(this._buildNewMsg());
                        this.copiedNew = true;
                        setTimeout(() => this.copiedNew = false, 2000);
                    },
                };
            }
        </script>
    </div>
</x-app-layout>
