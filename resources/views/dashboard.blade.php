<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <!-- DESTAQUE: NOVOS LEADS AGUARDANDO INTERAÇÃO -->
            @if($newLeadsWaiting->count() > 0)
            <div class="mb-4 sm:mb-6 new-leads-banner">
                <a href="{{ route('crm.board') }}" class="new-leads-card">
                    <div class="new-leads-pulse"></div>
                    <div class="new-leads-content">
                        <div class="new-leads-left">
                            <div class="new-leads-icon-wrap">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span class="new-leads-badge">{{ $newLeadsWaiting->count() }}</span>
                            </div>
                            <div>
                                <h3 class="new-leads-title">
                                    {{ $newLeadsWaiting->count() }} {{ $newLeadsWaiting->count() === 1 ? 'novo lead aguardando' : 'novos leads aguardando' }} interação!
                                </h3>
                                <div class="new-leads-list">
                                    @foreach($newLeadsWaiting->take(3) as $lead)
                                        <div class="new-leads-item">
                                            <span class="new-leads-dot"></span>
                                            <span class="new-leads-name">{{ $lead->title }}</span>
                                            @if($lead->source)
                                                <span class="new-leads-source">via {{ ucfirst($lead->source) }}</span>
                                            @endif
                                            <span class="new-leads-time">{{ $lead->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endforeach
                                    @if($newLeadsWaiting->count() > 3)
                                        <span class="new-leads-more">+{{ $newLeadsWaiting->count() - 3 }} mais...</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="new-leads-action">
                            <span>Atender agora</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <style>
                @keyframes newLeadsPulse {
                    0%, 100% { opacity: 0.6; transform: scale(1); }
                    50% { opacity: 0; transform: scale(1.03); }
                }
                @keyframes newLeadsBell {
                    0%, 100% { transform: rotate(0deg); }
                    10% { transform: rotate(14deg); }
                    20% { transform: rotate(-14deg); }
                    30% { transform: rotate(10deg); }
                    40% { transform: rotate(-8deg); }
                    50% { transform: rotate(4deg); }
                    60% { transform: rotate(0deg); }
                }
                @keyframes newLeadsDot {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.3; }
                }
                .new-leads-banner {
                    position: relative;
                }
                .new-leads-card {
                    display: block;
                    position: relative;
                    background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #dc2626 100%);
                    border-radius: 0.875rem;
                    padding: 1.125rem 1.25rem;
                    text-decoration: none;
                    color: white;
                    overflow: hidden;
                    transition: all 0.2s ease;
                    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.35), 0 0 0 1px rgba(249, 115, 22, 0.1);
                }
                .new-leads-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(249, 115, 22, 0.45), 0 0 0 1px rgba(249, 115, 22, 0.2);
                }
                .new-leads-pulse {
                    position: absolute;
                    inset: 0;
                    border: 2px solid rgba(255, 255, 255, 0.4);
                    border-radius: 0.875rem;
                    animation: newLeadsPulse 2s ease-in-out infinite;
                    pointer-events: none;
                }
                .new-leads-content {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                    position: relative;
                    z-index: 1;
                }
                .new-leads-left {
                    display: flex;
                    align-items: flex-start;
                    gap: 0.875rem;
                    min-width: 0;
                    flex: 1;
                }
                .new-leads-icon-wrap {
                    position: relative;
                    width: 2.75rem;
                    height: 2.75rem;
                    background: rgba(255, 255, 255, 0.2);
                    backdrop-filter: blur(4px);
                    border-radius: 0.75rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                }
                .new-leads-icon-wrap svg {
                    width: 1.5rem;
                    height: 1.5rem;
                    animation: newLeadsBell 2s ease-in-out infinite;
                }
                .new-leads-badge {
                    position: absolute;
                    top: -0.375rem;
                    right: -0.375rem;
                    min-width: 1.25rem;
                    height: 1.25rem;
                    padding: 0 0.25rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: white;
                    color: #dc2626;
                    font-size: 0.7rem;
                    font-weight: 800;
                    border-radius: 9999px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                }
                .new-leads-title {
                    font-size: 1rem;
                    font-weight: 700;
                    line-height: 1.3;
                    margin: 0;
                    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                }
                .new-leads-list {
                    display: flex;
                    flex-direction: column;
                    gap: 0.25rem;
                    margin-top: 0.5rem;
                }
                .new-leads-item {
                    display: flex;
                    align-items: center;
                    gap: 0.375rem;
                    font-size: 0.775rem;
                    opacity: 0.95;
                }
                .new-leads-dot {
                    width: 0.375rem;
                    height: 0.375rem;
                    background: white;
                    border-radius: 9999px;
                    flex-shrink: 0;
                    animation: newLeadsDot 1.5s ease-in-out infinite;
                }
                .new-leads-name {
                    font-weight: 600;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 180px;
                }
                .new-leads-source {
                    font-size: 0.7rem;
                    background: rgba(255, 255, 255, 0.2);
                    padding: 0.0625rem 0.375rem;
                    border-radius: 0.25rem;
                    white-space: nowrap;
                }
                .new-leads-time {
                    font-size: 0.7rem;
                    opacity: 0.75;
                    white-space: nowrap;
                }
                .new-leads-more {
                    font-size: 0.7rem;
                    opacity: 0.8;
                    font-weight: 500;
                    margin-top: 0.125rem;
                }
                .new-leads-action {
                    display: flex;
                    align-items: center;
                    gap: 0.375rem;
                    background: rgba(255, 255, 255, 0.2);
                    backdrop-filter: blur(4px);
                    padding: 0.5rem 1rem;
                    border-radius: 0.5rem;
                    font-size: 0.8rem;
                    font-weight: 700;
                    white-space: nowrap;
                    flex-shrink: 0;
                    transition: background 0.15s ease;
                }
                .new-leads-card:hover .new-leads-action {
                    background: rgba(255, 255, 255, 0.3);
                }
                .new-leads-action svg {
                    width: 1rem;
                    height: 1rem;
                }
                @media (max-width: 640px) {
                    .new-leads-content {
                        flex-direction: column;
                        align-items: stretch;
                    }
                    .new-leads-action {
                        justify-content: center;
                    }
                    .new-leads-name {
                        max-width: 140px;
                    }
                }
            </style>
            @endif

            <!-- PRÓXIMOS ANIVERSARIANTES DO MÊS -->
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

            <!-- CONTAS A PAGAR HOJE -->
            @if($todayPayables->count() > 0)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 0.875rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; font-size: 0.8rem;">
                <svg style="width: 1rem; height: 1rem; color: #dc2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span style="color: #991b1b; font-weight: 600;">Contas a pagar hoje:</span>
                @foreach($todayPayables as $payable)
                    <a href="{{ route('finance.payables') }}" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.5rem; border-radius: 9999px; text-decoration: none; font-size: 0.75rem; font-weight: 600; background: #fee2e2; color: #991b1b;" title="{{ $payable->description }}">
                        <span style="font-weight: 800;">R$ {{ number_format($payable->amount, 2, ',', '.') }}</span>
                        {{ $payable->description }}
                        @if($payable->category)
                            <span style="font-size: 0.625rem; color: #b91c1c; opacity: 0.7;">({{ $payable->category->name }})</span>
                        @endif
                    </a>
                @endforeach
                <a href="{{ route('finance.payables') }}" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.5rem; font-size: 0.7rem; font-weight: 700; color: #dc2626; text-decoration: none;">
                    Ver todas →
                </a>
            </div>
            @endif

            <!-- PRÓXIMO AGENDAMENTO -->
            @if($nextAppointment)
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('schedule.index') }}" style="display: flex; align-items: center; gap: 0.875rem; padding: 0.75rem 1rem; background: white; border: 1px solid #c7d2fe; border-radius: 0.625rem; text-decoration: none; transition: all 0.15s; box-shadow: 0 1px 3px rgba(99,102,241,0.08);" onmouseover="this.style.borderColor='#818cf8'; this.style.boxShadow='0 4px 12px rgba(99,102,241,0.15)'" onmouseout="this.style.borderColor='#c7d2fe'; this.style.boxShadow='0 1px 3px rgba(99,102,241,0.08)'">
                    <div style="width: 2.5rem; height: 2.5rem; background: #eef2ff; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #4f46e5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.375rem; flex-wrap: wrap;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #4f46e5;">Próximo agendamento</span>
                            <span style="font-size: 0.7rem; font-weight: 600; color: #111827; background: #e0e7ff; padding: 0.0625rem 0.5rem; border-radius: 9999px;">{{ substr($nextAppointment->start_time, 0, 5) }}</span>
                        </div>
                        <div style="font-size: 0.775rem; color: #374151; margin-top: 0.125rem;">
                            <span style="font-weight: 600;">{{ $nextAppointment->customer_name }}</span>
                            <span style="color: #6b7280;">com</span>
                            <span style="font-weight: 600; color: {{ $nextAppointment->attendant === 'danilo' ? '#2563eb' : '#7c3aed' }};">{{ $nextAppointment->attendant_name }}</span>
                            @if($nextAppointment->service_description)
                                <span style="color: #9ca3af;">·</span>
                                <span style="color: #6b7280;">{{ $nextAppointment->service_description }}</span>
                            @endif
                        </div>
                    </div>
                    <div style="flex-shrink: 0; display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #4f46e5;">
                        <span>Ver agenda</span>
                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            </div>
            @endif

            <!-- BANNER DG STORE -->
            <div class="mb-4 sm:mb-6">
                <div id="banner-container" style="width: 100%; aspect-ratio: 1200/280; background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #374151 100%); border-radius: 0.75rem; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('images/bannerdg.png') }}" alt="DG Store" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>

            <!-- AÇÕES RÁPIDAS -->
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                <a href="{{ route('sales.create') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: linear-gradient(to right, #10b981, #16a34a); color: white; font-size: 0.75rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; border: 1px solid #10b981; box-shadow: 0 2px 6px rgba(16,185,129,0.3); transition: all 0.15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(16,185,129,0.45)'" onmouseout="this.style.boxShadow='0 2px 6px rgba(16,185,129,0.3)'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Nova Venda
                </a>
                <a href="{{ route('schedule.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: linear-gradient(to right, #6366f1, #4f46e5); color: white; font-size: 0.75rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; border: 1px solid #6366f1; box-shadow: 0 2px 6px rgba(99,102,241,0.3); transition: all 0.15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(99,102,241,0.45)'" onmouseout="this.style.boxShadow='0 2px 6px rgba(99,102,241,0.3)'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Agenda
                </a>
                <a href="{{ route('sales.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: #111827; color: white; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #111827; transition: all 0.15s;" onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Vendas
                </a>
                <a href="{{ route('marketing.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb; transition: all 0.15s;" onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Marketing
                </a>
                <a href="{{ route('valuations.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb; transition: all 0.15s;" onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Avaliador
                </a>
                <a href="{{ route('tools.stone-calculator') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb; transition: all 0.15s;" onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Calculadora Stone
                </a>
                <a href="{{ route('stock.consignment.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb; transition: all 0.15s;" onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Fornecedor Interno
                </a>
            </div>

            <!-- Mensagens do Sistema -->
            <div class="mb-6 sm:mb-8">
                @if(count($systemNotifications) > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 8px;">
                        @foreach($systemNotifications as $notif)
                            @if(($notif['icon'] ?? '') === 'followup')
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-followup-modal'))" class="sn-card sn-card-{{ $notif['type'] }}" style="cursor: pointer; text-align: left;">
                                    <span class="sn-card-count">{{ $notif['count'] }}</span>
                                    <span class="sn-card-label">{{ $notif['label'] }}</span>
                                </button>
                            @else
                                <a href="{{ $notif['route'] }}" class="sn-card sn-card-{{ $notif['type'] }}">
                                    <span class="sn-card-count">{{ $notif['count'] }}</span>
                                    <span class="sn-card-label">{{ $notif['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="sn-ok">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Tudo em dia!</span>
                    </div>
                @endif
            </div>

            <style>
                .dg-hidden-value {
                    letter-spacing: 0.1em;
                    color: #d1d5db;
                    user-select: none;
                }
                .sn-card {
                    display: flex;
                    flex-direction: column;
                    padding: 10px 14px;
                    border-radius: 10px;
                    text-decoration: none;
                    border: 1px solid;
                    transition: all 0.15s ease;
                }
                .sn-card:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                }
                .sn-card-count {
                    font-size: 1.25rem;
                    font-weight: 800;
                    line-height: 1;
                }
                .sn-card-label {
                    font-size: 0.7rem;
                    font-weight: 500;
                    margin-top: 2px;
                    opacity: 0.8;
                }
                .sn-card-danger { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
                .sn-card-warning { background: #fffbeb; border-color: #fde68a; color: #92400e; }
                .sn-card-info { background: #f0f9ff; border-color: #bae6fd; color: #0c4a6e; }
                .sn-card-success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
                .sn-ok {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 8px 14px;
                    background: #f0fdf4;
                    border: 1px solid #bbf7d0;
                    border-radius: 10px;
                    color: #166534;
                    font-size: 0.8rem;
                    font-weight: 600;
                }
                .sn-ok svg { width: 1rem; height: 1rem; color: #16a34a; }
            </style>

            <!-- Navegação de Mês -->
            @php
                $prevMonth = $referenceDate->copy()->subMonth();
                $nextMonth = $referenceDate->copy()->addMonth();
                $canGoNext = !$isCurrentMonth;
            @endphp
            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1rem; background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 0.625rem 1.25rem;">
                <a href="{{ route('dashboard', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                   style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #f3f4f6; text-decoration: none; color: #374151; transition: all 0.15s;"
                   onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div style="text-align: center; min-width: 160px;">
                    <span style="font-size: 1rem; font-weight: 700; color: #111827; text-transform: capitalize;">{{ $referenceDate->translatedFormat('F Y') }}</span>
                    @if(!$isCurrentMonth)
                        <a href="{{ route('dashboard') }}" style="display: block; font-size: 0.6875rem; color: #2563eb; text-decoration: none; font-weight: 500; margin-top: 0.125rem;">Voltar ao mês atual</a>
                    @endif
                </div>
                @if($canGoNext)
                    <a href="{{ route('dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                       style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #f3f4f6; text-decoration: none; color: #374151; transition: all 0.15s;"
                       onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: #f9fafb; display: flex; align-items: center; justify-content: center; color: #d1d5db;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                @endif
            </div>

            <!-- Cards de Estatísticas -->
            <div x-data="{ showValues: localStorage.getItem('dg_show_values') !== 'false' }"
                 x-init="$watch('showValues', v => localStorage.setItem('dg_show_values', v))"
                 class="mb-6 sm:mb-8">
                {{-- Botões de ação --}}
                <div class="flex items-center justify-end gap-2 mb-2">
                    <button type="button" @click="$dispatch('open-month-summary')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg transition-colors text-blue-600 hover:text-blue-800 hover:bg-blue-50"
                            title="Resumo do Mês">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="hidden sm:inline">Resumo do Mês</span>
                    </button>
                    <button type="button" @click="showValues = !showValues"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg transition-colors"
                            :class="showValues ? 'text-gray-400 hover:text-gray-600 hover:bg-gray-100' : 'text-gray-500 bg-gray-100 hover:bg-gray-200'"
                            :title="showValues ? 'Ocultar valores' : 'Mostrar valores'">
                        {{-- Olho aberto --}}
                        <svg x-show="showValues" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{-- Olho fechado --}}
                        <svg x-show="!showValues" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                        <span x-text="showValues ? 'Ocultar' : 'Mostrar'" class="hidden sm:inline"></span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-500">Vendas Hoje</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                                    <span x-show="showValues">R$ {{ number_format($todayTotal, 2, ',', '.') }}</span>
                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500">Pedidos Hoje</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $todayCount }}</p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-500">Vendas do Mês</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                                    <span x-show="showValues">R$ {{ number_format($monthTotal, 2, ',', '.') }}</span>
                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-500 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('schedule.index') }}" class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 block text-decoration-none hover:shadow-md transition-shadow" style="text-decoration: none;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500">Agenda Hoje</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $todayAppointments->count() }} <span class="text-base font-medium text-gray-500">agendamento{{ $todayAppointments->count() !== 1 ? 's' : '' }}</span></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        @if($nextAppointment)
                            <p class="text-xs text-indigo-600 font-medium mt-2">Próximo: {{ substr($nextAppointment->start_time, 0, 5) }} - {{ $nextAppointment->attendant_name }}</p>
                        @endif
                    </a>
                </div>

                {{-- Cards de Lucro --}}
                @if(auth()->user()->canViewFinancials())
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mt-3 sm:mt-4">
                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-500">Lucro Hoje</p>
                                <p class="text-xl sm:text-2xl font-bold truncate {{ $profit['today_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    <span x-show="showValues">R$ {{ number_format($profit['today_profit'], 2, ',', '.') }}</span>
                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-500">Lucro do Mês</p>
                                <p class="text-xl sm:text-2xl font-bold truncate {{ $profit['month_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    <span x-show="showValues">R$ {{ number_format($profit['month_profit'], 2, ',', '.') }}</span>
                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-700 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-gray-500">Lucro Real do Mês</p>
                                <p class="text-xl sm:text-2xl font-bold truncate {{ $profit['real_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    <span x-show="showValues">R$ {{ number_format($profit['real_profit'], 2, ',', '.') }}</span>
                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;</span>
                                </p>
                                <div class="flex items-center gap-2 mt-1.5 text-xs">
                                    <span class="text-gray-400">Lucro</span>
                                    <span class="font-semibold {{ $profit['month_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        <span x-show="showValues">R$ {{ number_format($profit['month_profit'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                    <span class="text-gray-300">−</span>
                                    <span class="text-gray-400">Contas</span>
                                    <span class="font-semibold text-red-500">
                                        <span x-show="showValues">R$ {{ number_format($profit['month_expenses_paid'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 {{ $profit['real_profit'] >= 0 ? 'bg-emerald-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Inteligência de Lucro --}}
                @if(auth()->user()->canViewFinancials() && ($profit['top_products']->count() > 0 || $profit['category_ranking']->count() > 0))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                    {{-- Top Produtos por Lucro --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Produtos Mais Lucrativos</h3>
                            <span class="text-xs text-gray-400 font-medium" style="text-transform: capitalize;">{{ $referenceDate->translatedFormat('M/Y') }}</span>
                        </div>
                        @if($profit['top_products']->count() > 0)
                            @php $maxProfit = $profit['top_products']->max('profit'); @endphp
                            <div class="space-y-3">
                                @foreach($profit['top_products'] as $index => $item)
                                    <div class="relative p-3 rounded-lg {{ $index === 0 ? 'bg-emerald-50 border border-emerald-100' : 'hover:bg-gray-50' }}">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center min-w-0 flex-1">
                                                <span class="w-6 h-6 {{ $index === 0 ? 'bg-emerald-600' : 'bg-gray-900' }} text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 flex-shrink-0">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-medium text-gray-900 text-sm truncate">{{ $item['product']?->name ?? 'Produto removido' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $item['quantity'] }} un. vendidas</p>
                                                </div>
                                            </div>
                                            <div class="text-right flex-shrink-0 ml-3">
                                                <p class="font-bold text-sm {{ $item['profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                    <span x-show="showValues">R$ {{ number_format($item['profit'], 2, ',', '.') }}</span>
                                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;</span>
                                                </p>
                                                <p class="text-xs {{ $item['margin'] >= 0 ? 'text-emerald-500' : 'text-red-500' }} font-medium">
                                                    <span x-show="showValues">{{ number_format($item['margin'], 1, ',', '.') }}% margem</span>
                                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                                                </p>
                                            </div>
                                        </div>
                                        @if($maxProfit > 0)
                                        <div class="ml-9 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $item['profit'] >= 0 ? 'bg-emerald-400' : 'bg-red-400' }}" style="width: {{ $maxProfit > 0 ? min(100, max(2, ($item['profit'] / $maxProfit) * 100)) : 0 }}%"></div>
                                        </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8 text-sm">Nenhuma venda com dados de custo este mês.</p>
                        @endif
                    </div>

                    {{-- Lucro por Categoria --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Lucro por Categoria</h3>
                            <span class="text-xs text-gray-400 font-medium" style="text-transform: capitalize;">{{ $referenceDate->translatedFormat('M/Y') }}</span>
                        </div>
                        @if($profit['category_ranking']->count() > 0)
                            @php $maxCatRevenue = $profit['category_ranking']->max('revenue'); @endphp
                            <div class="space-y-3">
                                @foreach($profit['category_ranking'] as $cat)
                                    <div class="p-3 rounded-lg hover:bg-gray-50">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-sm text-gray-900">{{ $cat['label'] }}</span>
                                                <span class="text-xs text-gray-400">{{ $cat['quantity'] }} un.</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm font-bold {{ $cat['profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                    <span x-show="showValues">R$ {{ number_format($cat['profit'], 2, ',', '.') }}</span>
                                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;</span>
                                                </span>
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $cat['margin'] >= 20 ? 'bg-emerald-100 text-emerald-700' : ($cat['margin'] >= 0 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                                    <span x-show="showValues">{{ number_format($cat['margin'], 1, ',', '.') }}%</span>
                                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;%</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                                @php
                                                    $revenueWidth = $maxCatRevenue > 0 ? min(100, max(2, ($cat['revenue'] / $maxCatRevenue) * 100)) : 0;
                                                    $profitWidth = $cat['revenue'] > 0 ? max(0, min(100, ($cat['profit'] / $cat['revenue']) * 100)) : 0;
                                                @endphp
                                                <div class="h-full rounded-full flex overflow-hidden" style="width: {{ $revenueWidth }}%">
                                                    <div class="h-full {{ $cat['profit'] >= 0 ? 'bg-emerald-400' : 'bg-red-400' }}" style="width: {{ $profitWidth }}%"></div>
                                                    <div class="h-full bg-gray-300 flex-1"></div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400 whitespace-nowrap w-20 text-right">
                                                <span x-show="showValues">R$ {{ number_format($cat['revenue'], 0, ',', '.') }}</span>
                                                <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-2 bg-emerald-400 rounded-sm"></div>
                                    <span class="text-xs text-gray-400">Lucro</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-2 bg-gray-300 rounded-sm"></div>
                                    <span class="text-xs text-gray-400">Custo</span>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8 text-sm">Nenhuma venda registrada este mês.</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Agenda do Dia -->
            @if($todayAppointments->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Agenda de Hoje</h3>
                    <a href="{{ route('schedule.index') }}" class="text-xs sm:text-sm text-gray-600 hover:text-gray-900">Ver agenda completa →</a>
                </div>
                <div class="space-y-2">
                    @foreach($todayAppointments as $appt)
                        @php
                            $isPast = $appt->end_time <= now()->format('H:i:s');
                            $isNow = $appt->start_time <= now()->format('H:i:s') && $appt->end_time > now()->format('H:i:s');
                            $statusColors = [
                                'scheduled' => ['border' => '#bfdbfe', 'bg' => '#eff6ff', 'dot' => '#3b82f6'],
                                'confirmed' => ['border' => '#bbf7d0', 'bg' => '#f0fdf4', 'dot' => '#16a34a'],
                                'completed' => ['border' => '#e5e7eb', 'bg' => '#f9fafb', 'dot' => '#9ca3af'],
                                'cancelled' => ['border' => '#fecaca', 'bg' => '#fef2f2', 'dot' => '#dc2626'],
                                'no_show'   => ['border' => '#fde68a', 'bg' => '#fefce8', 'dot' => '#d97706'],
                            ];
                            $sc = $statusColors[$appt->status->value] ?? $statusColors['scheduled'];
                        @endphp
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.875rem; border-radius: 0.5rem; border: 1px solid {{ $sc['border'] }}; background: {{ $isNow ? '#eef2ff' : $sc['bg'] }}; {{ $isPast && $appt->status->value !== 'completed' ? 'opacity: 0.6;' : '' }}">
                            <div style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: {{ $isNow ? '#4f46e5' : $sc['dot'] }}; flex-shrink: 0; {{ $isNow ? 'box-shadow: 0 0 0 3px rgba(79,70,229,0.2);' : '' }}"></div>
                            <div style="min-width: 4.5rem; font-size: 0.8rem; font-weight: 700; color: {{ $isNow ? '#4f46e5' : '#111827' }}; font-variant-numeric: tabular-nums;">
                                {{ substr($appt->start_time, 0, 5) }} - {{ substr($appt->end_time, 0, 5) }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: #111827;">{{ $appt->customer_name }}</span>
                                @if($appt->service_description)
                                    <span style="font-size: 0.7rem; color: #6b7280; margin-left: 0.375rem;">· {{ $appt->service_description }}</span>
                                @endif
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.375rem; flex-shrink: 0;">
                                <span style="font-size: 0.7rem; font-weight: 600; color: {{ $appt->attendant === 'danilo' ? '#2563eb' : '#7c3aed' }}; background: {{ $appt->attendant === 'danilo' ? '#eff6ff' : '#f5f3ff' }}; padding: 0.125rem 0.5rem; border-radius: 9999px;">{{ $appt->attendant_name }}</span>
                                <span style="font-size: 0.65rem; font-weight: 500; padding: 0.125rem 0.375rem; border-radius: 9999px; background: {{ $sc['bg'] }}; color: {{ $sc['dot'] }}; border: 1px solid {{ $sc['border'] }};">{{ $appt->status->label() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Gráfico de Vendas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Vendas dos Últimos 7 Dias</h3>
                    <div class="h-48 sm:h-auto">
                        <canvas id="salesChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Produtos Mais Vendidos -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Produtos Mais Vendidos</h3>
                    @if(count($topProducts) > 0)
                        <div class="space-y-3">
                            @foreach($topProducts as $index => $item)
                                <div class="flex items-center justify-between p-3 rounded-lg {{ $index === 0 ? 'bg-gray-100' : 'hover:bg-gray-50' }}">
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 bg-gray-900 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item['product']->name ?? 'Produto removido' }}</p>
                                            <p class="text-xs text-gray-500">{{ $item['product']->sku ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-gray-900 text-white text-sm rounded-full">{{ $item['total_sold'] }} un</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Nenhuma venda registrada ainda.</p>
                    @endif
                </div>
            </div>

            {{-- Notícias Apple --}}
            <div class="mt-4 sm:mt-6">
                <x-apple-news-widget :news="$appleNews" />
            </div>
        </div>
    </div>

    {{-- Modal Resumo do Mês (Fullscreen) --}}
    <div x-data="{ open: false, hideValues: false }"
         x-on:open-month-summary.window="open = true"
         x-on:keydown.escape.window="open = false"
         x-show="open" x-cloak
         style="position: fixed; inset: 0; z-index: 9999;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div id="month-summary-card"
             style="position: absolute; inset: 0; background: linear-gradient(170deg, #050505 0%, #0a0f1a 30%, #0c1220 60%, #080d15 100%); display: flex; flex-direction: column; align-items: center; justify-content: space-between; height: 100vh; overflow: hidden; padding: 0;">

            {{-- Ambient glows --}}
            <div style="position: absolute; top: -15%; right: -10%; width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 65%); pointer-events: none;"></div>
            <div style="position: absolute; bottom: -10%; left: -10%; width: 40vw; height: 40vw; background: radial-gradient(circle, rgba(16,185,129,0.06) 0%, transparent 65%); pointer-events: none;"></div>

            {{-- Top bar: eye + close --}}
            <div style="position: absolute; top: 0; right: 0; display: flex; align-items: center; gap: 0.5rem; padding: 1rem 1.25rem; z-index: 10;">
                <button @click="hideValues = !hideValues" style="width: 2.25rem; height: 2.25rem; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); cursor: pointer; color: rgba(255,255,255,0.4); transition: all 0.15s;" :style="hideValues ? 'background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.7)' : ''" onmouseover="this.style.color='rgba(255,255,255,0.8)'" onmouseout="">
                    <svg x-show="!hideValues" style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="hideValues" x-cloak style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </button>
                <button @click="open = false" style="width: 2.25rem; height: 2.25rem; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); cursor: pointer; color: rgba(255,255,255,0.4); transition: all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.12)';this.style.color='white'" onmouseout="this.style.background='rgba(255,255,255,0.06)';this.style.color='rgba(255,255,255,0.4)'">
                    <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Content wrapper: flex column, justify-between, full height --}}
            <div style="position: relative; z-index: 1; width: 100%; max-width: 480px; height: 100vh; display: flex; flex-direction: column; justify-content: space-between; padding: 2.5vh 1.5rem;">

                {{-- TOP: Logo --}}
                <div style="text-align: center; padding-top: 1rem;">
                    <img src="{{ asset('images/logodg.png') }}" alt="DG Store" style="height: 3rem; margin: 0 auto; filter: brightness(1.15) drop-shadow(0 0 20px rgba(255,255,255,0.08));">
                </div>

                {{-- MIDDLE: All content --}}
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 1.5vh;">

                    {{-- Month Title --}}
                    <div style="text-align: center;">
                        <p style="font-size: 0.6rem; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; color: rgba(255,255,255,0.3); margin-bottom: 0.375rem;">Resumo</p>
                        <h2 style="font-size: 2rem; font-weight: 800; color: white; line-height: 1; letter-spacing: -0.03em;">{{ ucfirst($monthSummary['month_label']) }}</h2>
                        <div style="width: 2.5rem; height: 2px; background: linear-gradient(to right, #6366f1, #10b981); margin: 0.625rem auto 0; border-radius: 1px;"></div>
                    </div>

                    {{-- Faturamento --}}
                    <div style="text-align: center; padding: 1.25rem 1rem; background: linear-gradient(135deg, rgba(255,255,255,0.035) 0%, rgba(255,255,255,0.01) 100%); border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem;">
                        <p style="font-size: 0.55rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 0.375rem;">Faturamento</p>
                        <p style="font-size: 2.125rem; font-weight: 800; color: white; line-height: 1; letter-spacing: -0.02em;">
                            <span x-show="!hideValues">R$ {{ number_format($monthSummary['total_revenue'], 2, ',', '.') }}</span>
                            <span x-show="hideValues" x-cloak style="letter-spacing: 0.1em; color: rgba(255,255,255,0.25);">R$ &bull;&bull;&bull;&bull;&bull;&bull;</span>
                        </p>
                        <p style="font-size: 0.725rem; color: rgba(255,255,255,0.3); margin-top: 0.375rem;">Ticket médio
                            <span x-show="!hideValues" style="color: rgba(255,255,255,0.6); font-weight: 700;">R$ {{ number_format($monthSummary['average_ticket'], 2, ',', '.') }}</span>
                            <span x-show="hideValues" x-cloak style="color: rgba(255,255,255,0.25); font-weight: 700; letter-spacing: 0.05em;">&bull;&bull;&bull;&bull;</span>
                        </p>
                    </div>

                    {{-- Pedidos + Total itens vendidos --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem;">
                        <div style="padding: 1rem; background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.875rem; text-align: center;">
                            <p style="font-size: 1.875rem; font-weight: 800; color: white; line-height: 1;">{{ $monthSummary['total_sales'] }}</p>
                            <p style="font-size: 0.55rem; font-weight: 600; color: rgba(255,255,255,0.35); margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.12em;">Pedidos</p>
                        </div>
                        <div style="padding: 1rem; background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.875rem; text-align: center;">
                            <p style="font-size: 1.875rem; font-weight: 800; color: white; line-height: 1;">{{ $monthSummary['total_items'] }}</p>
                            <p style="font-size: 0.55rem; font-weight: 600; color: rgba(255,255,255,0.35); margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.12em;">Itens vendidos</p>
                        </div>
                    </div>

                    {{-- Categorias --}}
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.875rem; overflow: hidden;">
                        <div style="padding: 0.625rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between;">
                            <span style="font-size: 0.55rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255,255,255,0.25);">Categoria</span>
                            <span style="font-size: 0.55rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255,255,255,0.25);">Qtd</span>
                        </div>

                        <div style="padding: 0.625rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: white;">iPhones vendidos</span>
                            <span style="font-size: 1rem; font-weight: 800; color: white;">{{ $monthSummary['iphone_total'] }}</span>
                        </div>

                        <div style="padding: 0.4375rem 1rem 0.4375rem 1.875rem; border-bottom: 1px solid rgba(255,255,255,0.03); display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <div style="width: 0.3125rem; height: 0.3125rem; border-radius: 50%; background: #34d399;"></div>
                                <span style="font-size: 0.725rem; color: rgba(255,255,255,0.55);">Novos</span>
                            </div>
                            <span style="font-size: 0.85rem; font-weight: 700; color: #34d399;">{{ $monthSummary['iphone_new'] }}</span>
                        </div>

                        <div style="padding: 0.4375rem 1rem 0.4375rem 1.875rem; border-bottom: 1px solid rgba(255,255,255,0.04); display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <div style="width: 0.3125rem; height: 0.3125rem; border-radius: 50%; background: #fbbf24;"></div>
                                <span style="font-size: 0.725rem; color: rgba(255,255,255,0.55);">Seminovos (used)</span>
                            </div>
                            <span style="font-size: 0.85rem; font-weight: 700; color: #fbbf24;">{{ $monthSummary['iphone_used'] }}</span>
                        </div>

                        @if($monthSummary['accessories'] > 0)
                        <div style="padding: 0.625rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.75);">Acessórios <span style="font-size: 0.625rem; color: rgba(255,255,255,0.25);">(capinhas, fontes)</span></span>
                            <span style="font-size: 1rem; font-weight: 800; color: white;">{{ $monthSummary['accessories'] }}</span>
                        </div>
                        @endif

                        @if($monthSummary['other_apple'] > 0)
                        <div style="padding: 0.625rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.75);">Outros Apple <span style="font-size: 0.625rem; color: rgba(255,255,255,0.25);">(MacBooks, iPad, AirPods, Watch)</span></span>
                            <span style="font-size: 1rem; font-weight: 800; color: white;">{{ $monthSummary['other_apple'] }}</span>
                        </div>
                        @endif

                        <div style="padding: 0.75rem 1rem; background: rgba(255,255,255,0.025); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8rem; font-weight: 800; color: white;">Total de itens</span>
                            <span style="font-size: 1.125rem; font-weight: 800; color: white;">{{ $monthSummary['total_items'] }}</span>
                        </div>
                    </div>

                    {{-- Trade-ins --}}
                    @if($monthSummary['trade_ins_received'] > 0)
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.625rem; padding: 0.75rem; background: rgba(139,92,246,0.06); border: 1px solid rgba(139,92,246,0.1); border-radius: 0.875rem;">
                        <svg style="width: 1rem; height: 1rem; color: #a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <p style="font-size: 0.85rem; font-weight: 800; color: #a78bfa; line-height: 1;">{{ $monthSummary['trade_ins_received'] }} <span style="font-size: 0.675rem; font-weight: 600; color: rgba(139,92,246,0.55);">trade-ins recebidos</span></p>
                    </div>
                    @endif
                </div>

                {{-- BOTTOM: Footer --}}
                <div style="text-align: center; padding-bottom: 1rem;">
                    <p style="font-size: 0.55rem; color: rgba(255,255,255,0.15); letter-spacing: 0.15em; text-transform: uppercase; font-weight: 600;">DG Store · Sistema de Gestão</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Follow-Up Pós-Venda --}}
    <div x-data="followupModal()" x-on:open-followup-modal.window="open = true" x-on:keydown.escape.window="open = false"
         x-show="open" x-cloak
         style="position: fixed; inset: 0; z-index: 9998;">

        {{-- Overlay --}}
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); transition: opacity 0.2s;" @click="open = false"></div>

        {{-- Container centralizado --}}
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div style="position: relative; background: white; border-radius: 1rem; width: 100%; max-width: 580px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.25); pointer-events: auto;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">

                {{-- Header fixo --}}
                <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div style="min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.625rem;">
                            <div style="width: 2.25rem; height: 2.25rem; background: #eff6ff; border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.125rem; height: 1.125rem; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <h2 style="font-size: 1rem; font-weight: 700; color: #111827; line-height: 1.3; margin: 0;">Follow-Up Pós-Venda</h2>
                                <p style="font-size: 0.6875rem; color: #6b7280; margin: 0;">
                                    <span x-text="pendingCount"></span> pendente<span x-show="pendingCount !== 1">s</span>
                                    · clientes há 7+ dias sem contato
                                </p>
                            </div>
                        </div>
                    </div>
                    <button @click="open = false" style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; background: #f3f4f6; border: none; cursor: pointer; color: #6b7280; flex-shrink: 0; transition: all 0.15s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Lista scrollável --}}
                <div style="flex: 1; overflow-y: auto; min-height: 0;">
                    <template x-if="sales.length === 0">
                        <div style="text-align: center; padding: 3rem 1.5rem; color: #6b7280;">
                            <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.75rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <p style="font-weight: 600; font-size: 0.875rem;">Todos os follow-ups estão em dia!</p>
                        </div>
                    </template>

                    <div style="padding: 0.75rem;">
                        <template x-for="(sale, idx) in sales" :key="sale.id">
                            <div style="padding: 0.875rem 1rem; border-radius: 0.75rem; transition: all 0.2s; margin-bottom: 0.5rem;"
                                 :style="sale.done
                                    ? 'opacity: 0.35; background: #f9fafb;'
                                    : 'background: white; border: 1px solid #e5e7eb;'">

                                {{-- Info do cliente --}}
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem; flex-wrap: wrap;">
                                    <span style="font-weight: 700; font-size: 0.8125rem; color: #111827;" x-text="sale.customer_name"></span>
                                    <span style="font-size: 0.5625rem; font-weight: 700; padding: 0.125rem 0.4375rem; border-radius: 9999px; background: #dbeafe; color: #1e40af; white-space: nowrap;" x-text="'há ' + sale.days_since + ' dias'"></span>
                                    <template x-if="sale.done">
                                        <span style="font-size: 0.5625rem; font-weight: 700; padding: 0.125rem 0.4375rem; border-radius: 9999px; background: #dcfce7; color: #166534;">Concluído</span>
                                    </template>
                                </div>

                                {{-- Produtos --}}
                                <p style="font-size: 0.75rem; color: #4b5563; margin: 0 0 0.25rem 0; line-height: 1.4;" x-text="sale.product_names"></p>

                                {{-- Venda + data --}}
                                <p style="font-size: 0.6875rem; color: #9ca3af; margin: 0 0 0.625rem 0;">
                                    <span x-text="sale.sale_number"></span> · <span x-text="sale.sold_at_formatted"></span>
                                    <template x-if="sale.customer_phone">
                                        <span> · <span x-text="sale.customer_phone"></span></span>
                                    </template>
                                </p>

                                {{-- Ações --}}
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;" x-show="!sale.done">
                                    <a x-show="sale.has_phone" :href="sale.whatsapp_url" target="_blank"
                                       style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.4375rem 0.875rem; background: #16a34a; color: white; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background 0.15s;"
                                       onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        WhatsApp
                                    </a>
                                    <button @click="markDone(sale)"
                                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.4375rem 0.875rem; background: white; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; border: 1px solid #d1d5db; cursor: pointer; transition: all 0.15s;"
                                            onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#9ca3af'" onmouseout="this.style.background='white';this.style.borderColor='#d1d5db'"
                                            :disabled="sale.loading">
                                        <template x-if="!sale.loading">
                                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </template>
                                        <template x-if="sale.loading">
                                            <svg style="width: 0.875rem; height: 0.875rem; animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </template>
                                        <span x-text="sale.loading ? 'Salvando...' : 'Marcar como feito'"></span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="padding: 0.75rem 1.5rem; border-top: 1px solid #f3f4f6; flex-shrink: 0; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.6875rem; color: #9ca3af;" x-text="doneCount + ' de ' + sales.length + ' concluído' + (doneCount !== 1 ? 's' : '')"></span>
                    <button @click="open = false" style="padding: 0.375rem 1rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Atalho F2 para Nova Venda
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                window.location.href = '{{ route('sales.create') }}';
            }
        });

        const realData = @json($salesChart['data']);
        const hiddenData = realData.map(() => 0);

        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($salesChart['labels']),
                datasets: [{
                    label: 'Vendas (R$)',
                    data: localStorage.getItem('dg_show_values') !== 'false' ? realData : hiddenData,
                    borderColor: '#1f2937',
                    backgroundColor: 'rgba(31, 41, 55, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (localStorage.getItem('dg_show_values') === 'false') return 'R$ -----';
                                return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (localStorage.getItem('dg_show_values') === 'false') return '';
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });

        // Sincronizar gráfico com toggle de valores
        window.addEventListener('storage', function() {
            const show = localStorage.getItem('dg_show_values') !== 'false';
            salesChart.data.datasets[0].data = show ? realData : hiddenData;
            salesChart.update();
        });

        // Observer para mudanças no Alpine (mesmo aba)
        const origSetItem = localStorage.setItem;
        localStorage.setItem = function(key, value) {
            origSetItem.apply(this, arguments);
            if (key === 'dg_show_values') {
                const show = value !== 'false';
                salesChart.data.datasets[0].data = show ? realData : hiddenData;
                salesChart.update();
            }
        };

        function followupModal() {
            const rawSales = @json($followupSales);

            return {
                open: false,
                sales: rawSales.map(s => ({ ...s, done: false, loading: false })),

                get pendingCount() {
                    return this.sales.filter(s => !s.done).length;
                },

                get doneCount() {
                    return this.sales.filter(s => s.done).length;
                },

                async markDone(sale) {
                    sale.loading = true;
                    try {
                        const res = await fetch(`{{ url('/sales') }}/${sale.id}/followup`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ method: 'whatsapp', notes: '' }),
                        });
                        if (res.ok) {
                            sale.done = true;
                        }
                    } catch (e) {
                        console.error('Erro ao registrar follow-up:', e);
                    } finally {
                        sale.loading = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
