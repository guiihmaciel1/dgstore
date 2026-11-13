<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    @php
        $prevMonth = $referenceDate->copy()->subMonth();
        $nextMonth = $referenceDate->copy()->addMonth();
    @endphp
    <div class="py-6"
         x-data="{ stockModalOpen: false, showValues: localStorage.getItem('dg_show_values') !== 'false' }"
         x-init="$watch('showValues', v => localStorage.setItem('dg_show_values', v))">
        <div class="px-4 sm:px-6 lg:px-8">
            {{-- Barra da página: mês de referência + ações --}}
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <nav class="flex items-center gap-1" aria-label="Mês de referência">
                    <a href="{{ route('dashboard', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                       class="flex h-8 w-8 items-center justify-center rounded-lg text-dg-400 transition-colors hover:bg-surface-elevated hover:text-dg-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400"
                       aria-label="Mês anterior">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <h1 class="min-w-[9.5rem] text-center text-base font-semibold capitalize text-dg-100">{{ $referenceDate->translatedFormat('F Y') }}</h1>
                    @if(!$isCurrentMonth)
                        <a href="{{ route('dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                           class="flex h-8 w-8 items-center justify-center rounded-lg text-dg-400 transition-colors hover:bg-surface-elevated hover:text-dg-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400"
                           aria-label="Próximo mês">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('dashboard') }}" class="ml-2 text-xs font-medium text-indigo-300 underline-offset-4 hover:underline">Voltar ao mês atual</a>
                    @else
                        <span class="flex h-8 w-8 items-center justify-center text-dg-700" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    @endif
                </nav>

                <div class="flex items-center gap-2">
                    <button type="button" @click="$dispatch('open-month-summary')"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-500/15 px-3 py-2 text-xs font-semibold text-indigo-200 transition-colors hover:bg-indigo-500/25 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Resumo Executivo
                    </button>
                    <button type="button" @click="showValues = !showValues"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium text-dg-400 transition-colors hover:bg-surface-elevated hover:text-dg-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400"
                            :aria-pressed="(!showValues).toString()">
                        <svg x-show="showValues" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="!showValues" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                        <span x-text="showValues ? 'Ocultar valores' : 'Mostrar valores'"></span>
                    </button>
                </div>
            </div>

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
                    background: #141414;
                    color: #fca5a5;
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
                    background: #141414;
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

            @include('partials._dashboard-open-repairs', ['openRepairs' => $openRepairs])

            <!-- ALERTA: PREÇOS ABAIXO DO CUSTO DO FORNECEDOR -->
            @if(!empty($priceBelowCostAlerts))
            <div style="background: #141414; border: 2px solid #ef4444; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1rem;">
                <div style="padding: 0.625rem 0.875rem; background: rgba(239,68,68,0.08); border-bottom: 1px solid rgba(239,68,68,0.15); display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1rem;">⚠️</span>
                    <div>
                        <span style="font-size: 0.8rem; font-weight: 800; color: #fca5a5;">Preços abaixo do custo do fornecedor</span>
                        <span style="font-size: 0.65rem; color: #ef4444; margin-left: 0.375rem;">({{ count($priceBelowCostAlerts) }} {{ count($priceBelowCostAlerts) === 1 ? 'item' : 'itens' }})</span>
                    </div>
                    <a href="{{ route('marketing.index', ['tab' => 'prices']) }}" style="margin-left: auto; font-size: 0.65rem; font-weight: 700; color: #ef4444; text-decoration: none; background: rgba(239,68,68,0.1); padding: 0.25rem 0.625rem; border-radius: 9999px; white-space: nowrap;">
                        Corrigir preços →
                    </a>
                </div>
                <div style="padding: 0.375rem 0.5rem;">
                    @foreach($priceBelowCostAlerts as $alert)
                    <div style="display: flex; align-items: center; gap: 0.625rem; padding: 0.5rem 0.5rem; {{ !$loop->last ? 'border-bottom: 1px solid rgba(255,255,255,0.04);' : '' }}">
                        <div style="flex: 1; min-width: 0; display: flex; align-items: center; gap: 0.5rem;">
                            @if(!empty($alert['color_hex']))
                                <span style="width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0; border: 2px solid rgba(255,255,255,0.15); background: {{ $alert['color_hex'] }};"></span>
                            @endif
                            <div>
                                <span style="font-size: 0.8rem; font-weight: 700; color: #e3e3e3;">{{ $alert['name'] }}</span>
                                @if($alert['color'])
                                    <span style="font-size: 0.7rem; font-weight: 600; color: #a4a4a4; margin-left: 0.25rem;">{{ $alert['color'] }}</span>
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
                            <div style="text-align: right;">
                                <div style="font-size: 0.6rem; color: #818181; font-weight: 500;">Nosso preço</div>
                                <div style="font-size: 0.8rem; font-weight: 800; color: #fca5a5; font-variant-numeric: tabular-nums;">
                                    {{ number_format($alert['sale_price'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.6rem; color: #818181; font-weight: 500;">Custo forn.</div>
                                <div style="font-size: 0.8rem; font-weight: 800; color: #e3e3e3; font-variant-numeric: tabular-nums;">
                                    {{ number_format($alert['supplier_cost'], 0, ',', '.') }}
                                </div>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 800; color: #ef4444; background: rgba(239,68,68,0.1); padding: 0.125rem 0.5rem; border-radius: 9999px; font-variant-numeric: tabular-nums; white-space: nowrap;">
                                {{ number_format($alert['diff'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- COTAÇÃO FORNECEDOR (WhatsApp) — somente admin -->
            @if(!empty($supplierQuoteInfo) && auth()->user()->isAdmin())
            <div style="margin-bottom: 1rem;">
                @foreach($supplierQuoteInfo as $quote)
                <div style="background: #141414; border: 1px solid rgba(37,211,102,0.15); border-radius: 0.75rem; overflow: hidden;">
                    {{-- Header com toggle --}}
                    <div style="display: flex; align-items: center; gap: 0.625rem; padding: 0.625rem 0.875rem; border-bottom: 1px solid rgba(255,255,255,0.06); cursor: pointer;"
                         onclick="toggleQuoteAccordion(this)"
                         onmouseover="this.style.background='rgba(37,211,102,0.04)'" onmouseout="this.style.background='transparent'">
                        <a href="{{ route('quotations.index', ['supplier_id' => $quote['supplier_id']]) }}"
                           onclick="event.stopPropagation()"
                           style="width: 1.75rem; height: 1.75rem; background: rgba(37,211,102,0.12); border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; text-decoration: none;"
                           title="Ver todas as cotações">
                            <svg style="width: 0.875rem; height: 0.875rem; color: #25D366;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </a>
                        <div style="flex: 1; min-width: 0;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #25D366;">Cotação {{ $quote['supplier_name'] }}</span>
                            <span style="font-size: 0.65rem; color: #555; margin-left: 0.5rem;">
                                {{ $quote['last_update']->setTimezone('America/Sao_Paulo')->format('d/m H:i') }}
                                · {{ $quote['items_count'] }} itens
                            </span>
                        </div>
                        <svg class="quote-chevron" style="width: 0.75rem; height: 0.75rem; color: #444; transition: transform 0.2s ease;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    {{-- Cards de modelos (colapsável) --}}
                    @if(!empty($quote['tracked_models']))
                    <div class="quote-accordion-body" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1px; background: rgba(255,255,255,0.04); transition: max-height 0.3s ease; overflow: hidden;">
                        @php $currentGroup = null; @endphp
                        @foreach($quote['tracked_models'] as $model)
                        @if(($model['group'] ?? null) !== $currentGroup)
                            @php $currentGroup = $model['group'] ?? null; @endphp
                            @if($currentGroup)
                            <div style="grid-column: 1 / -1; padding: 0.375rem 0.875rem; background: rgba(255,255,255,0.02); border-top: 1px solid rgba(255,255,255,0.08);">
                                <span style="font-size: 0.65rem; font-weight: 700; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 0.08em;">{{ $currentGroup }}</span>
                            </div>
                            @endif
                        @endif
                        <div style="background: #141414; padding: 0.75rem 0.875rem;">
                            {{-- Cabeçalho do modelo --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 0.8rem; font-weight: 800; color: #e3e3e3;">{{ $model['label'] }}</span>
                                    @if($model['we_have'] ?? false)
                                        <span style="font-size: 0.55rem; font-weight: 700; color: #22c55e; background: rgba(34,197,94,0.12); padding: 0.125rem 0.375rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.03em;">✓ Temos</span>
                                    @else
                                        <span style="font-size: 0.55rem; font-weight: 700; color: #ef4444; background: rgba(239,68,68,0.1); padding: 0.125rem 0.375rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.03em;">✗ Sem</span>
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.375rem;">
                                    @if($model['diff'] !== null && $model['diff'] != 0)
                                        @if($model['diff'] < 0)
                                            <span style="display: inline-flex; align-items: center; font-size: 0.6rem; font-weight: 700; color: #22c55e; background: rgba(34,197,94,0.1); padding: 0.125rem 0.375rem; border-radius: 9999px;">▼{{ number_format(abs($model['diff']), 0, ',', '.') }}</span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; font-size: 0.6rem; font-weight: 700; color: #ef4444; background: rgba(239,68,68,0.1); padding: 0.125rem 0.375rem; border-radius: 9999px;">▲{{ number_format($model['diff'], 0, ',', '.') }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Legenda de colunas --}}
                            <div style="display: flex; align-items: center; padding: 0 0.5rem; margin-bottom: 0.125rem;">
                                <span style="flex: 1; font-size: 0.55rem; color: #555; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;"></span>
                                <span style="font-size: 0.55rem; color: #555; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; width: 55px; text-align: right;">Custo</span>
                                <span style="font-size: 0.55rem; color: #555; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; width: 55px; text-align: right;">Venda</span>
                                <span style="font-size: 0.55rem; color: #555; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; width: 55px; text-align: right;">Margem</span>
                            </div>

                            {{-- Cores: custo forn → nosso preço → margem --}}
                            @if(!empty($model['colors']))
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                @foreach($model['colors'] as $color)
                                @php
                                    $hasOur = ($color['our_price'] ?? null) !== null;
                                    $margin = $color['margin'] ?? null;
                                    $marginColor = $margin !== null ? ($margin >= 0 ? '#22c55e' : '#ef4444') : '#555';
                                    $colorDiff = $color['diff'] ?? null;
                                    $changed = $colorDiff !== null && $colorDiff != 0;
                                    $wentUp = $changed && $colorDiff > 0;
                                    $wentDown = $changed && $colorDiff < 0;

                                    if ($margin !== null && $margin < 0) {
                                        $rowBg = 'rgba(239,68,68,0.06)';
                                        $rowBorder = 'border: 1px solid rgba(239,68,68,0.15);';
                                    } elseif ($wentUp) {
                                        $rowBg = 'rgba(239,68,68,0.04)';
                                        $rowBorder = 'border-left: 3px solid #ef4444;';
                                    } elseif ($wentDown) {
                                        $rowBg = 'rgba(34,197,94,0.04)';
                                        $rowBorder = 'border-left: 3px solid #22c55e;';
                                    } else {
                                        $rowBg = '#1a1a1a';
                                        $rowBorder = '';
                                    }
                                @endphp
                                <div style="display: flex; align-items: center; gap: 0.375rem; padding: 0.3rem 0.5rem; background: {{ $rowBg }}; border-radius: 0.375rem; {{ $rowBorder }}">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; border: 2px solid rgba(255,255,255,0.15); background: {{ $color['hex'] }};" title="{{ $color['name'] }}"></span>
                                    @if(($color['diff'] ?? null) !== null && $color['diff'] != 0)
                                        @if($color['diff'] < 0)
                                            <span style="font-size: 0.55rem; color: #22c55e; font-weight: 700; flex: 1;">▼{{ number_format(abs($color['diff']), 0, ',', '.') }}</span>
                                        @else
                                            <span style="font-size: 0.55rem; color: #ef4444; font-weight: 700; flex: 1;">▲{{ number_format($color['diff'], 0, ',', '.') }}</span>
                                        @endif
                                    @else
                                        <span style="flex: 1;"></span>
                                    @endif
                                    <span style="font-size: 0.75rem; font-weight: 700; color: #e3e3e3; font-variant-numeric: tabular-nums; width: 55px; text-align: right;">
                                        {{ number_format($color['price'], 0, ',', '.') }}
                                    </span>
                                    @if($hasOur)
                                        <span style="font-size: 0.75rem; font-weight: 700; color: #818181; font-variant-numeric: tabular-nums; width: 55px; text-align: right;">
                                            {{ number_format($color['our_price'], 0, ',', '.') }}
                                        </span>
                                        <span style="font-size: 0.7rem; font-weight: 800; color: {{ $marginColor }}; font-variant-numeric: tabular-nums; width: 55px; text-align: right;">
                                            {{ $margin >= 0 ? '+' : '' }}{{ number_format($margin, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span style="font-size: 0.65rem; color: #444; width: 55px; text-align: right;">—</span>
                                        <span style="font-size: 0.65rem; color: #444; width: 55px; text-align: right;">—</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div style="padding: 0.25rem 0.5rem; background: #1a1a1a; border-radius: 0.375rem;">
                                <span style="font-size: 0.65rem; color: #555;">Sem detalhamento de cores</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <!-- CONTAS A PAGAR HOJE -->
            @if($todayPayables->count() > 0)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 0.875rem; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.35); border-radius: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; font-size: 0.8rem;">
                <svg style="width: 1rem; height: 1rem; color: #fca5a5; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span style="color: #fca5a5; font-weight: 600;">Contas a pagar hoje:</span>
                @foreach($todayPayables as $payable)
                    <a href="{{ route('finance.payables') }}" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.5rem; border-radius: 9999px; text-decoration: none; font-size: 0.75rem; font-weight: 600; background: rgba(239,68,68,0.15); color: #fca5a5;" title="{{ $payable->description }}">
                        <span style="font-weight: 800;">R$ {{ number_format($payable->amount, 2, ',', '.') }}</span>
                        {{ $payable->description }}
                        @if($payable->category)
                            <span style="font-size: 0.625rem; color: #fca5a5; opacity: 0.7;">({{ $payable->category->name }})</span>
                        @endif
                    </a>
                @endforeach
                <a href="{{ route('finance.payables') }}" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.5rem; font-size: 0.7rem; font-weight: 700; color: #fca5a5; text-decoration: none;">
                    Ver todas →
                </a>
            </div>
            @endif

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
                    color: #515151;
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
                .sn-card-danger { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.35); color: #fca5a5; }
                .sn-card-warning { background: rgba(245,158,11,0.08); border-color: rgba(253,230,138,0.7); color: #fbbf24; }
                .sn-card-info { background: rgba(56,189,248,0.08); border-color: rgba(125,211,252,0.35); color: #7dd3fc; }
                .sn-card-success { background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.35); color: #6ee7b7; }
                .sn-ok {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 8px 14px;
                    background: rgba(16,185,129,0.1);
                    border: 1px solid rgba(16,185,129,0.35);
                    border-radius: 10px;
                    color: #6ee7b7;
                    font-size: 0.8rem;
                    font-weight: 600;
                }
                .sn-ok svg { width: 1rem; height: 1rem; color: #16a34a; }
            </style>

            {{-- Números do mês --}}
            @include("partials._home-numbers")

            {{-- Equipe: vendas, lucro, ponto e comissões das vendedoras (admin geral) --}}
            @if($teamPanel)
                @include("partials._home-team")
            @endif

            <div class="mb-6 grid grid-cols-1 gap-4 sm:mb-8 sm:gap-6 lg:grid-cols-2">
                @include("partials._home-birthdays")
                @include("partials._home-agenda")
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Gráfico de Vendas -->
                <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-dg-100 mb-4">Vendas dos Últimos 7 Dias</h3>
                    <div class="h-48 sm:h-auto">
                        <canvas id="salesChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Produtos Mais Vendidos -->
                <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-dg-100 mb-4">Produtos Mais Vendidos</h3>
                    @if(count($topProducts) > 0)
                        <div class="space-y-3">
                            @foreach($topProducts as $index => $item)
                                <div class="flex items-center justify-between p-3 rounded-lg {{ $index === 0 ? 'bg-surface-overlay' : 'hover:bg-surface' }}">
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 bg-surface text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-medium text-dg-100">{{ $item['name'] ?? $item['product']?->name ?? 'Produto removido' }}</p>
                                            <p class="text-xs text-dg-500">{{ $item['sku'] ?? $item['product']?->sku ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-surface text-white text-sm rounded-full">{{ $item['total_sold'] }} un</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-dg-500 text-center py-8">Nenhuma venda registrada ainda.</p>
                    @endif
                </div>
            </div>

            {{-- Notícias Apple --}}
            <div class="mt-4 sm:mt-6">
                <x-apple-news-widget :news="$appleNews" />
            </div>
        </div>

        {{-- MODAL CATÁLOGO DE ESTOQUE --}}
        <div x-show="stockModalOpen" x-cloak
             x-data="stockCatalogModal()"
             style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">
            <div @click="stockModalOpen = false" style="position: absolute; inset: 0; background: rgba(0,0,0,0.5);"></div>
            <div @click.stop style="position: relative; background: #141414; border-radius: 1rem; width: 100%; max-width: 1000px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.25);">
                <div class="p-4 sm:px-6 sm:py-5 border-b border-border rounded-t-2xl shrink-0">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h2 class="text-xl font-extrabold text-dg-100 m-0">📱 Nosso Estoque</h2>
                            <p class="text-xs text-dg-500 mt-1 mb-0">Consulte o que temos disponível para venda</p>
                        </div>
                        <button @click="stockModalOpen = false" class="w-8 h-8 rounded-lg bg-surface-overlay border-none cursor-pointer flex items-center justify-center text-base text-dg-500 shrink-0 hover:bg-surface-elevated transition-colors">✕</button>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        @if(count($stockItems['used']) > 0)
                        <button @click="copyUsed()" class="inline-flex items-center justify-center gap-1.5 py-2 px-3 bg-orange-500/10 text-orange-600 text-[0.7rem] font-bold rounded-lg border border-orange-500/30 cursor-pointer transition-colors whitespace-nowrap hover:bg-orange-500/15">
                            <span x-text="copiedUsed ? '✓ Copiado!' : '📋 Copiar Seminovos'"></span>
                        </button>
                        @endif
                        @if(count($stockItems['new']) > 0)
                        <button @click="copyNew()" class="inline-flex items-center justify-center gap-1.5 py-2 px-3 bg-blue-500/10 text-blue-600 text-[0.7rem] font-bold rounded-lg border border-blue-500/30 cursor-pointer transition-colors whitespace-nowrap hover:bg-blue-500/15">
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
                            <h3 style="font-size: 0.9375rem; font-weight: 800; color: #e3e3e3; margin: 0;">Seminovos</h3>
                            <span style="font-size: 0.65rem; font-weight: 700; padding: 0.125rem 0.5rem; border-radius: 9999px; background: rgba(249,115,22,0.12); color: #ea580c;">{{ $stockItems['usedCount'] }} un.</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.625rem;">
                            @foreach($stockItems['used'] as $idx => $item)
                            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl border border-orange-500/30 p-3.5 relative flex flex-col">
                                <div class="flex items-start justify-between gap-1">
                                    <span class="text-[0.55rem] font-extrabold px-1.5 py-0.5 rounded-full bg-orange-600 text-white uppercase tracking-wide">SEMINOVO</span>
                                    @if($item['qty'] > 1)
                                    <span class="text-[0.7rem] font-extrabold text-orange-600">{{ $item['qty'] }}x</span>
                                    @endif
                                </div>
                                <p class="text-[0.8rem] font-bold text-dg-100 mt-2 mb-1 leading-tight">{{ $item['name'] }}</p>
                                <div class="flex flex-wrap gap-1 mb-1.5">
                                    @if($item['storage'])
                                    <span class="text-[0.625rem] font-semibold px-1.5 py-px rounded-full bg-surface-raised text-dg-300 border border-border">💾 {{ $item['storage'] }}</span>
                                    @endif
                                    @if($item['color'])
                                    <span class="text-[0.625rem] font-semibold px-1.5 py-px rounded-full bg-surface-raised text-dg-300 border border-border">🎨 {{ $item['color'] }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1 mb-1.5">
                                    @if($item['battery'])
                                    <span class="text-[0.6rem] font-semibold px-1.5 py-px rounded-full {{ $item['battery'] >= 80 ? 'bg-emerald-500/10 text-emerald-600' : 'bg-red-500/10 text-red-600' }}">🔋 {{ $item['battery'] }}%</span>
                                    @endif
                                    @if($item['has_box'])
                                    <span class="text-[0.6rem] font-semibold px-1.5 py-px rounded-full bg-green-500/10 text-green-600">📦 Caixa</span>
                                    @endif
                                    @if($item['has_cable'])
                                    <span class="text-[0.6rem] font-semibold px-1.5 py-px rounded-full bg-green-500/10 text-green-600">🔌 Cabo</span>
                                    @endif
                                </div>
                                @if(!empty($item['notes']))
                                <p class="text-[0.65rem] text-dg-500 italic mb-1.5 leading-snug">📝 {{ $item['notes'] }}</p>
                                @endif
                                @if($item['price'] > 0)
                                <div class="mt-auto">
                                    <span class="text-[0.7rem] text-dg-500 line-through">De R$ {{ number_format($item['price'] + 200, 2, ',', '.') }}</span>
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
                            <h3 style="font-size: 0.9375rem; font-weight: 800; color: #e3e3e3; margin: 0;">Estoque Novo</h3>
                            <span style="font-size: 0.65rem; font-weight: 700; padding: 0.125rem 0.5rem; border-radius: 9999px; background: rgba(59,130,246,0.1); color: #2563eb;">{{ $stockItems['newCount'] }} mod.</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.625rem;">
                            @foreach($stockItems['new'] as $item)
                            <div style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(16,185,129,0.08) 100%); border-radius: 0.75rem; border: 1px solid #bfdbfe; padding: 0.875rem; position: relative;">
                                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.25rem;">
                                    <span style="font-size: 0.55rem; font-weight: 800; padding: 0.1rem 0.375rem; border-radius: 9999px; background: #2563eb; color: white; text-transform: uppercase; letter-spacing: 0.04em;">NOVO</span>
                                    @if($item['qty'] > 1)
                                    <span style="font-size: 0.7rem; font-weight: 800; color: #2563eb;">{{ $item['qty'] }}x</span>
                                    @endif
                                </div>
                                <p style="font-size: 0.8rem; font-weight: 700; color: #e3e3e3; margin: 0.5rem 0 0.25rem 0; line-height: 1.2;">{{ $item['name'] }}</p>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; margin-bottom: 0.375rem;">
                                    @if($item['storage'])
                                    <span style="font-size: 0.625rem; font-weight: 600; padding: 0.0625rem 0.375rem; border-radius: 9999px; background: #141414; color: #a4a4a4; border: 1px solid rgba(255,255,255,0.06);">💾 {{ $item['storage'] }}</span>
                                    @endif
                                    @if($item['color'])
                                    <span style="font-size: 0.625rem; font-weight: 600; padding: 0.0625rem 0.375rem; border-radius: 9999px; background: #141414; color: #a4a4a4; border: 1px solid rgba(255,255,255,0.06);">🎨 {{ $item['color'] }}</span>
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
                    <div style="text-align: center; padding: 2rem; color: #818181;">
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

    {{-- Resumo Executivo (Vendedoras + Gerencial + Detalhado) --}}
    @include('partials._intern-dashboard-modal')

    {{-- Conferência de caixa (banco + espécie) a cada N horas em horário comercial --}}
    @if($cashStatus)
        @include('partials._home-cash-modal')
    @endif


    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleQuoteAccordion(header) {
            const container = header.closest('div[style*="border-radius: 0.75rem"]');
            const body = container.querySelector('.quote-accordion-body');
            const chevron = header.querySelector('.quote-chevron');
            if (!body) return;

            const isHidden = body.style.display === 'none';
            body.style.display = isHidden ? 'grid' : 'none';
            chevron.style.transform = isHidden ? '' : 'rotate(-90deg)';

            localStorage.setItem('dg_quote_accordion', isHidden ? 'open' : 'closed');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const saved = localStorage.getItem('dg_quote_accordion');
            if (saved === 'closed') {
                document.querySelectorAll('.quote-accordion-body').forEach(function(body) {
                    body.style.display = 'none';
                    const chevron = body.closest('div[style*="border-radius: 0.75rem"]').querySelector('.quote-chevron');
                    if (chevron) chevron.style.transform = 'rotate(-90deg)';
                });
            }
        });

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
                window.dispatchEvent(new CustomEvent('dg-show-values-changed', { detail: show }));
            }
        };

        // Alpine countUp component
        document.addEventListener('alpine:init', () => {
            Alpine.data('countUp', (target) => ({
                display: 0,
                init() {
                    const duration = 800;
                    const steps = 30;
                    const increment = target / steps;
                    let current = 0;
                    const interval = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            this.display = target;
                            clearInterval(interval);
                        } else {
                            this.display = Math.floor(current);
                        }
                    }, duration / steps);
                }
            }));
        });

    </script>
    @endpush
</x-app-layout>
