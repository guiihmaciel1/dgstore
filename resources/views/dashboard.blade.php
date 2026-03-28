<x-app-layout>
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

            <!-- ANIVERSARIANTES DO MÊS -->
            @if($birthdayCustomers->count() > 0)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; font-size: 0.8rem;">
                <span style="font-size: 0.9rem;">&#127874;</span>
                <span style="color: #6b7280; font-weight: 500;">Aniversariantes:</span>
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
                            <a href="{{ $notif['route'] }}" class="sn-card sn-card-{{ $notif['type'] }}">
                                <span class="sn-card-count">{{ $notif['count'] }}</span>
                                <span class="sn-card-label">{{ $notif['label'] }}</span>
                            </a>
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

            <!-- Cards de Estatísticas -->
            <div x-data="{ showValues: localStorage.getItem('dg_show_values') !== 'false' }"
                 x-init="$watch('showValues', v => localStorage.setItem('dg_show_values', v))"
                 class="mb-6 sm:mb-8">
                {{-- Botão olho --}}
                <div class="flex justify-end mb-2">
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
        </div>
    </div>

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
    </script>
    @endpush
</x-app-layout>
