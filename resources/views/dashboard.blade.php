<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <div class="py-6" x-data="{ stockModalOpen: false }">
        <div class="px-6 lg:px-8">
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

            <!-- PRÓXIMOS ANIVERSARIANTES DO MÊS -->
            @if($birthdayCustomers->count() > 0)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; font-size: 0.8rem;">
                <span style="font-size: 0.9rem;">&#127874;</span>
                <span style="color: #818181; font-weight: 500;">Próximos aniversariantes do mês:</span>
                @foreach($birthdayCustomers as $customer)
                    <a href="{{ route('customers.show', $customer) }}" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.5rem; border-radius: 9999px; text-decoration: none; font-size: 0.75rem; font-weight: 600; {{ $customer->birth_date->day === now()->day ? 'background: #fef3c7; color: #fbbf24;' : 'background: #222222; color: #a4a4a4;' }}" title="{{ $customer->phone }}">
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
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 0.875rem; background: rgba(239,68,68,0.1); border: 1px solid #fecaca; border-radius: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; font-size: 0.8rem;">
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

            <!-- PRÓXIMO AGENDAMENTO -->
            @if($nextAppointment)
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('schedule.index') }}" style="display: flex; align-items: center; gap: 0.875rem; padding: 0.75rem 1rem; background: #141414; border: 1px solid #c7d2fe; border-radius: 0.625rem; text-decoration: none; transition: all 0.15s; box-shadow: 0 1px 3px rgba(99,102,241,0.08);" onmouseover="this.style.borderColor='#818cf8'; this.style.boxShadow='0 4px 12px rgba(99,102,241,0.15)'" onmouseout="this.style.borderColor='#c7d2fe'; this.style.boxShadow='0 1px 3px rgba(99,102,241,0.08)'">
                    <div style="width: 2.5rem; height: 2.5rem; background: rgba(99,102,241,0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #4f46e5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.375rem; flex-wrap: wrap;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #4f46e5;">Próximo agendamento</span>
                            <span style="font-size: 0.7rem; font-weight: 600; color: #e3e3e3; background: rgba(99,102,241,0.15); padding: 0.0625rem 0.5rem; border-radius: 9999px;">{{ substr($nextAppointment->start_time, 0, 5) }}</span>
                        </div>
                        <div style="font-size: 0.775rem; color: #a4a4a4; margin-top: 0.125rem;">
                            <span style="font-weight: 600;">{{ $nextAppointment->customer_name }}</span>
                            <span style="color: #818181;">com</span>
                            <span style="font-weight: 600; color: {{ \App\Domain\Schedule\Models\Appointment::ATTENDANT_COLORS[$nextAppointment->attendant] ?? '#6b7280' }};">{{ $nextAppointment->attendant_name }}</span>
                            @if($nextAppointment->service_description)
                                <span style="color: #666666;">·</span>
                                <span style="color: #818181;">{{ $nextAppointment->service_description }}</span>
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

            <!-- ATALHOS RÁPIDOS -->
            <div class="dashboard-shortcuts">
                <a href="{{ route('sales.create') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; background: #111827; color: white; border-radius: 0.75rem; text-decoration: none; transition: all 0.15s; box-shadow: 0 1px 3px rgba(0,0,0,0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.2)'" onmouseout="this.style.transform=''; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                    <div style="width: 2.5rem; height: 2.5rem; background: #22c55e; border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <span style="font-size: 0.9375rem; font-weight: 700; display: block; line-height: 1.2;">Venda</span>
                        <span style="font-size: 0.6875rem; color: #666666;">Nova venda</span>
                    </div>
                </a>
                <a href="{{ route('tools.negotiation-simulator') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; background: #141414; border: 2px solid rgba(255,255,255,0.08); color: #e3e3e3; border-radius: 0.75rem; text-decoration: none; transition: all 0.15s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='#818181'; this.style.boxShadow='0 4px 12px rgba(99,102,241,0.15)'" onmouseout="this.style.transform=''; this.style.borderColor='rgba(255,255,255,0.06)'; this.style.boxShadow=''">
                    <div style="width: 2.5rem; height: 2.5rem; background: rgba(99,102,241,0.1); border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <span style="font-size: 0.9375rem; font-weight: 700; display: block; line-height: 1.2;">Simular</span>
                        <span style="font-size: 0.6875rem; color: #666666;">Negociação</span>
                    </div>
                </a>
                @if(config('crm.pipeline_enabled'))
                <a href="{{ route('crm.board') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; background: #141414; border: 2px solid rgba(255,255,255,0.08); color: #e3e3e3; border-radius: 0.75rem; text-decoration: none; transition: all 0.15s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 4px 12px rgba(245,158,11,0.15)'" onmouseout="this.style.transform=''; this.style.borderColor='rgba(255,255,255,0.06)'; this.style.boxShadow=''">
                    <div style="width: 2.5rem; height: 2.5rem; background: rgba(245,158,11,0.1); border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                    </div>
                    <div>
                        <span style="font-size: 0.9375rem; font-weight: 700; display: block; line-height: 1.2;">Pipeline</span>
                        <span style="font-size: 0.6875rem; color: #666666;">CRM / Leads</span>
                    </div>
                </a>
                @endif
                <a href="{{ route('marketing.index') }}?tab=used" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; background: #141414; border: 2px solid rgba(255,255,255,0.08); color: #e3e3e3; border-radius: 0.75rem; text-decoration: none; transition: all 0.15s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='#ec4899'; this.style.boxShadow='0 4px 12px rgba(236,72,153,0.15)'" onmouseout="this.style.transform=''; this.style.borderColor='rgba(255,255,255,0.06)'; this.style.boxShadow=''">
                    <div style="width: 2.5rem; height: 2.5rem; background: rgba(236,72,153,0.1); border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #ec4899;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <span style="font-size: 0.9375rem; font-weight: 700; display: block; line-height: 1.2;">Seminovos</span>
                        <span style="font-size: 0.6875rem; color: #666666;">Marketing</span>
                    </div>
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
                .dashboard-shortcuts {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0.75rem;
                    margin-bottom: 1rem;
                }
                @media (min-width: 640px) {
                    .dashboard-shortcuts {
                        grid-template-columns: repeat(4, 1fr);
                    }
                }
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
                .sn-card-danger { background: rgba(239,68,68,0.1); border-color: #fecaca; color: #fca5a5; }
                .sn-card-warning { background: #141414beb; border-color: #fde68a; color: #fbbf24; }
                .sn-card-info { background: #f0f9ff; border-color: #bae6fd; color: #0c4a6e; }
                .sn-card-success { background: rgba(16,185,129,0.1); border-color: #bbf7d0; color: #6ee7b7; }
                .sn-ok {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 8px 14px;
                    background: rgba(16,185,129,0.1);
                    border: 1px solid #bbf7d0;
                    border-radius: 10px;
                    color: #6ee7b7;
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
            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1rem; background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 0.625rem 1.25rem;">
                <a href="{{ route('dashboard', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                   style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #222222; text-decoration: none; color: #a4a4a4; transition: all 0.15s;"
                   onmouseover="this.style.background='#222222'" onmouseout="this.style.background='transparent'">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div style="text-align: center; min-width: 160px;">
                    <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3; text-transform: capitalize;">{{ $referenceDate->translatedFormat('F Y') }}</span>
                    @if(!$isCurrentMonth)
                        <a href="{{ route('dashboard') }}" style="display: block; font-size: 0.6875rem; color: #2563eb; text-decoration: none; font-weight: 500; margin-top: 0.125rem;">Voltar ao mês atual</a>
                    @endif
                </div>
                @if($canGoNext)
                    <a href="{{ route('dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                       style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #222222; text-decoration: none; color: #a4a4a4; transition: all 0.15s;"
                       onmouseover="this.style.background='#222222'" onmouseout="this.style.background='transparent'">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: #1a1a1a; display: flex; align-items: center; justify-content: center; color: #515151;">
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
                            title="Resumo Executivo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="hidden sm:inline">Resumo Executivo</span>
                    </button>
                    <button type="button" @click="showValues = !showValues"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg transition-colors"
                            :class="showValues ? 'text-dg-500 hover:text-dg-400 hover:bg-surface-overlay' : 'text-dg-500 bg-surface-overlay hover:bg-surface-elevated'"
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
                    <div class="bg-surface-raised rounded-xl p-4 sm:p-5 ring-1 ring-white/[0.03] border border-border">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-dg-500">Vendas Hoje</p>
                                <p class="text-xl sm:text-2xl font-bold text-dg-100 truncate">
                                    <span x-show="showValues">R$ {{ number_format($todayTotal, 2, ',', '.') }}</span>
                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-surface rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface-raised rounded-xl p-4 sm:p-5 ring-1 ring-white/[0.03] border border-border">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm text-dg-500">Pedidos Hoje</p>
                                <p class="text-xl sm:text-2xl font-bold text-dg-100">{{ $todayCount }}</p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-surface-elevated rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface-raised rounded-xl p-4 sm:p-5 ring-1 ring-white/[0.03] border border-border">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm text-dg-500">Vendas do Mês</p>
                                <p class="text-xl sm:text-2xl font-bold text-dg-100 truncate">
                                    <span x-show="showValues">R$ {{ number_format($monthTotal, 2, ',', '.') }}</span>
                                    <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                                </p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-surface0 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('schedule.index') }}" class="bg-surface-raised rounded-xl p-4 sm:p-5 ring-1 ring-white/[0.03] border border-border block text-decoration-none hover: transition-shadow" style="text-decoration: none;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm text-dg-500">Agenda Hoje</p>
                                <p class="text-xl sm:text-2xl font-bold text-dg-100">{{ $todayAppointments->count() }} <span class="text-base font-medium text-dg-500">agendamento{{ $todayAppointments->count() !== 1 ? 's' : '' }}</span></p>
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

                {{-- Cards de Lucro - Visão Financeira --}}
                @if(auth()->user()->canViewFinancials())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 mt-3 sm:mt-4">
                    {{-- Lucro Bruto do Mês --}}
                    <div class="group/card bg-surface-raised rounded-xl p-4 sm:p-5 ring-1 ring-white/[0.03] border border-border relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500 rounded-l-xl"></div>
                        <div class="pl-2">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-dg-500 uppercase tracking-wide">Lucro Bruto</p>
                                <span class="text-[0.65rem] px-1.5 py-0.5 rounded-full font-medium {{ $profit['month_margin'] >= 20 ? 'bg-emerald-50 text-emerald-700' : ($profit['month_margin'] >= 10 ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }}">
                                    <span x-show="showValues">{{ number_format($profit['month_margin'], 1, ',', '.') }}% margem</span>
                                    <span x-show="!showValues" x-cloak>&bull;&bull;&bull;</span>
                                </span>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold {{ $profit['month_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                <span x-show="showValues">R$ {{ number_format($profit['month_profit'], 2, ',', '.') }}</span>
                                <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                            </p>
                            <div class="mt-2.5 pt-2.5 border-t border-border">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Faturamento</span>
                                    <span class="font-semibold text-dg-300">
                                        <span x-show="showValues">R$ {{ number_format($profit['month_revenue'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs mt-1">
                                    <span class="text-dg-500">Hoje</span>
                                    <span class="font-semibold {{ $profit['today_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        <span x-show="showValues">R$ {{ number_format($profit['today_profit'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- Tooltip hover: composição do lucro bruto --}}
                        <div class="absolute inset-0 bg-surface-raised/95 backdrop-blur-sm rounded-xl opacity-0 group-hover/card:opacity-100 transition-opacity duration-200 flex items-center justify-center z-10 pointer-events-none group-hover/card:pointer-events-auto">
                            <div class="text-center px-4 w-full" x-show="showValues">
                                <p class="text-[0.6rem] uppercase tracking-wide text-dg-500 font-medium mb-2">Composição do Lucro Bruto</p>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Faturamento (vendas)</span>
                                        <span class="font-bold text-dg-200">R$ {{ number_format($profit['month_revenue'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Custo dos produtos</span>
                                        <span class="font-bold text-red-500">− R$ {{ number_format($profit['month_revenue'] - $profit['month_profit'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="border-t border-border pt-1.5 flex items-center justify-between text-xs">
                                        <span class="font-semibold text-dg-300">= Lucro Bruto</span>
                                        <span class="font-bold text-emerald-600">R$ {{ number_format($profit['month_profit'], 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div x-show="!showValues" x-cloak class="text-sm text-dg-500">Valores ocultos</div>
                        </div>
                    </div>

                    {{-- Lucro Líquido do Mês --}}
                    <div class="group/card bg-surface-raised rounded-xl p-4 sm:p-5 ring-1 ring-white/[0.03] border border-border relative">
                        <div class="absolute top-0 left-0 w-1 h-full {{ $profit['real_profit'] >= 0 ? 'bg-blue-500' : 'bg-red-500' }} rounded-l-xl"></div>
                        <div class="pl-2">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-dg-500 uppercase tracking-wide">Lucro Líquido</p>
                                @php
                                    $liquidMargin = $profit['month_revenue'] > 0 ? ($profit['real_profit'] / $profit['month_revenue']) * 100 : 0;
                                @endphp
                                <span class="text-[0.65rem] px-1.5 py-0.5 rounded-full font-medium {{ $liquidMargin >= 10 ? 'bg-blue-50 text-blue-700' : ($liquidMargin >= 5 ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }}">
                                    <span x-show="showValues">{{ number_format($liquidMargin, 1, ',', '.') }}% líquido</span>
                                    <span x-show="!showValues" x-cloak>&bull;&bull;&bull;</span>
                                </span>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold {{ $profit['real_profit'] >= 0 ? 'text-dg-100' : 'text-red-600' }}">
                                <span x-show="showValues">R$ {{ number_format($profit['real_profit'], 2, ',', '.') }}</span>
                                <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                            </p>
                            <div class="mt-2.5 pt-2.5 border-t border-border space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Lucro bruto</span>
                                    <span class="font-semibold text-emerald-600">
                                        <span x-show="showValues">R$ {{ number_format($profit['month_profit'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                @if($profit['salaries_paid'] > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Salários</span>
                                    <span class="font-semibold text-red-500">
                                        <span x-show="showValues">− R$ {{ number_format($profit['salaries_paid'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Despesas</span>
                                    <span class="font-semibold text-red-400">
                                        <span x-show="showValues">− R$ {{ number_format($profit['expenses_without_salaries'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                @else
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Despesas</span>
                                    <span class="font-semibold text-red-500">
                                        <span x-show="showValues">− R$ {{ number_format($profit['month_expenses_paid'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                @endif
                                @if(($profit['month_commissions'] ?? 0) > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Comissões</span>
                                    <span class="font-semibold text-purple-500">
                                        <span x-show="showValues">− R$ {{ number_format($profit['month_commissions'] ?? 0, 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        {{-- Tooltip hover: composição do lucro líquido --}}
                        <div class="absolute left-0 top-0 w-full min-h-full bg-surface-raised/98 backdrop-blur-sm rounded-xl opacity-0 group-hover/card:opacity-100 transition-opacity duration-200 z-10 pointer-events-none group-hover/card:pointer-events-auto p-5 pl-6">
                            <div x-show="showValues">
                                <p class="text-[0.6rem] uppercase tracking-wide text-dg-500 font-medium mb-3">Composição do Lucro Líquido</p>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Lucro bruto</span>
                                        <span class="font-bold text-emerald-600">R$ {{ number_format($profit['month_profit'], 2, ',', '.') }}</span>
                                    </div>
                                    @if($profit['salaries_paid'] > 0)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Salários ({{ count($profit['salary_details']) }})</span>
                                        <span class="font-bold text-red-500">− R$ {{ number_format($profit['salaries_paid'], 2, ',', '.') }}</span>
                                    </div>
                                    @foreach($profit['salary_details'] as $sd)
                                    <div class="flex items-center justify-between text-[0.65rem] pl-3">
                                        <span class="text-dg-500 truncate mr-2">{{ $sd['description'] }}</span>
                                        <span class="text-dg-500 whitespace-nowrap">R$ {{ number_format($sd['amount'], 2, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Outras despesas</span>
                                        <span class="font-bold text-red-400">− R$ {{ number_format($profit['expenses_without_salaries'], 2, ',', '.') }}</span>
                                    </div>
                                    @else
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Total despesas pagas</span>
                                        <span class="font-bold text-red-500">− R$ {{ number_format($profit['month_expenses_paid'], 2, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    @if(($profit['month_commissions'] ?? 0) > 0)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Comissões pagas</span>
                                        <span class="font-bold text-purple-500">− R$ {{ number_format($profit['month_commissions'] ?? 0, 2, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    <div class="border-t border-border pt-1.5 mt-1 flex items-center justify-between text-xs">
                                        <span class="font-semibold text-dg-300">= Lucro Líquido</span>
                                        <span class="font-bold {{ $profit['real_profit'] >= 0 ? 'text-dg-100' : 'text-red-600' }}">R$ {{ number_format($profit['real_profit'], 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div x-show="!showValues" x-cloak class="text-sm text-dg-500 pt-4">Valores ocultos</div>
                        </div>
                    </div>

                    {{-- Mês Anterior (Comparativo) --}}
                    <div class="group/card bg-surface rounded-xl p-4 sm:p-5 ring-1 ring-white/[0.03] border border-border relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-gray-400 rounded-l-xl"></div>
                        <div class="pl-2">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-dg-500 uppercase tracking-wide">Mês Anterior</p>
                                <span class="text-[0.65rem] px-1.5 py-0.5 rounded-full bg-surface-elevated text-dg-400 font-medium" style="text-transform: capitalize;">
                                    {{ $prevMonthProfit['month_label'] }}
                                </span>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold {{ $prevMonthProfit['real_profit'] >= 0 ? 'text-dg-300' : 'text-red-600' }}">
                                <span x-show="showValues">R$ {{ number_format($prevMonthProfit['real_profit'], 2, ',', '.') }}</span>
                                <span x-show="!showValues" x-cloak class="dg-hidden-value">R$ &bull;&bull;&bull;&bull;&bull;</span>
                            </p>
                            <p class="text-[0.65rem] text-dg-500 -mt-0.5 mb-1">lucro líquido acumulado</p>
                            <div class="mt-2 pt-2.5 border-t border-border space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Lucro bruto</span>
                                    <span class="font-semibold {{ $prevMonthProfit['month_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        <span x-show="showValues">R$ {{ number_format($prevMonthProfit['month_profit'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                @if($prevMonthProfit['salaries_paid'] > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Salários</span>
                                    <span class="font-semibold text-red-500">
                                        <span x-show="showValues">− R$ {{ number_format($prevMonthProfit['salaries_paid'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Despesas</span>
                                    <span class="font-semibold text-red-400">
                                        <span x-show="showValues">− R$ {{ number_format($prevMonthProfit['expenses_without_salaries'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                @else
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dg-500">Despesas</span>
                                    <span class="font-semibold text-red-500">
                                        <span x-show="showValues">− R$ {{ number_format($prevMonthProfit['month_expenses'], 2, ',', '.') }}</span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                @endif
                                @if($profit['real_profit'] != 0 && $prevMonthProfit['real_profit'] != 0)
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-border">
                                    <span class="text-dg-500">Variação</span>
                                    @php
                                        $variation = $prevMonthProfit['real_profit'] != 0
                                            ? (($profit['real_profit'] - $prevMonthProfit['real_profit']) / abs($prevMonthProfit['real_profit'])) * 100
                                            : 0;
                                    @endphp
                                    <span class="font-semibold {{ $variation >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        <span x-show="showValues">
                                            {{ $variation >= 0 ? '↑' : '↓' }} {{ number_format(abs($variation), 0, ',', '.') }}%
                                        </span>
                                        <span x-show="!showValues" x-cloak class="dg-hidden-value">&bull;&bull;&bull;</span>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        {{-- Tooltip hover: composição do mês anterior --}}
                        <div class="absolute inset-0 bg-surface/95 backdrop-blur-sm rounded-xl opacity-0 group-hover/card:opacity-100 transition-opacity duration-200 flex items-center justify-center z-10 pointer-events-none group-hover/card:pointer-events-auto">
                            <div class="text-center px-4 w-full" x-show="showValues">
                                <p class="text-[0.6rem] uppercase tracking-wide text-dg-500 font-medium mb-2" style="text-transform: capitalize;">Resumo {{ $prevMonthProfit['month_label'] }}</p>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Faturamento</span>
                                        <span class="font-bold text-dg-300">R$ {{ number_format($prevMonthProfit['month_revenue'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Lucro bruto</span>
                                        <span class="font-bold text-emerald-600">R$ {{ number_format($prevMonthProfit['month_profit'], 2, ',', '.') }}</span>
                                    </div>
                                    @if($prevMonthProfit['salaries_paid'] > 0)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Salários</span>
                                        <span class="font-bold text-red-500">− R$ {{ number_format($prevMonthProfit['salaries_paid'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Outras despesas</span>
                                        <span class="font-bold text-red-400">− R$ {{ number_format($prevMonthProfit['expenses_without_salaries'], 2, ',', '.') }}</span>
                                    </div>
                                    @else
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dg-500">Total despesas</span>
                                        <span class="font-bold text-red-500">− R$ {{ number_format($prevMonthProfit['month_expenses'], 2, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    <div class="border-t border-border-strong pt-1.5 flex items-center justify-between text-xs">
                                        <span class="font-semibold text-dg-400">= Lucro Líquido</span>
                                        <span class="font-bold {{ $prevMonthProfit['real_profit'] >= 0 ? 'text-dg-200' : 'text-red-600' }}">R$ {{ number_format($prevMonthProfit['real_profit'], 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div x-show="!showValues" x-cloak class="text-sm text-dg-500">Valores ocultos</div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Inteligência Atacado x Cliente Final --}}
                @include('partials._wholesale-intelligence', ['wholesaleData' => $wholesaleData, 'referenceDate' => $referenceDate])
            </div>

            <!-- Agenda do Dia -->
            @if($todayAppointments->count() > 0)
            <div class="bg-surface-raised rounded-xl ring-1 ring-white/[0.03] border border-border p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-dg-100">Agenda de Hoje</h3>
                    <a href="{{ route('schedule.index') }}" class="text-xs sm:text-sm text-dg-400 hover:text-dg-100">Ver agenda completa →</a>
                </div>
                <div class="space-y-2">
                    @foreach($todayAppointments as $appt)
                        @php
                            $isPast = $appt->end_time <= now()->format('H:i:s');
                            $isNow = $appt->start_time <= now()->format('H:i:s') && $appt->end_time > now()->format('H:i:s');
                            $statusColors = [
                                'scheduled' => ['border' => '#bfdbfe', 'bg' => '#eff6ff', 'dot' => '#3b82f6'],
                                'confirmed' => ['border' => '#bbf7d0', 'bg' => '#f0fdf4', 'dot' => '#16a34a'],
                                'completed' => ['border' => 'rgba(255,255,255,0.06)', 'bg' => '#1a1a1a', 'dot' => '#666666'],
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
                                <span style="font-size: 0.8rem; font-weight: 600; color: #e3e3e3;">{{ $appt->customer_name }}</span>
                                @if($appt->service_description)
                                    <span style="font-size: 0.7rem; color: #818181; margin-left: 0.375rem;">· {{ $appt->service_description }}</span>
                                @endif
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.375rem; flex-shrink: 0;">
                                <span style="font-size: 0.7rem; font-weight: 600; color: {{ \App\Domain\Schedule\Models\Appointment::ATTENDANT_COLORS[$appt->attendant] ?? '#6b7280' }}; background: {{ \App\Domain\Schedule\Models\Appointment::ATTENDANT_BG_COLORS[$appt->attendant] ?? '#f3f4f6' }}; padding: 0.125rem 0.5rem; border-radius: 9999px;">{{ $appt->attendant_name }}</span>
                                <span style="font-size: 0.65rem; font-weight: 500; padding: 0.125rem 0.375rem; border-radius: 9999px; background: {{ $sc['bg'] }}; color: {{ $sc['dot'] }}; border: 1px solid {{ $sc['border'] }};">{{ $appt->status->label() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

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
                            <h3 style="font-size: 0.9375rem; font-weight: 800; color: #e3e3e3; margin: 0;">Seminovos</h3>
                            <span style="font-size: 0.65rem; font-weight: 700; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #1414147ed; color: #ea580c;">{{ $stockItems['usedCount'] }} un.</span>
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
                            <div style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border-radius: 0.75rem; border: 1px solid #bfdbfe; padding: 0.875rem; position: relative;">
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
