<x-app-layout>
    <x-slot name="title">CRM - Pipeline</x-slot>
    <style>
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes pulse-badge { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
    </style>
    @php
        $activeStages = \App\Domain\CRM\Models\PipelineStage::where('is_won', false)->where('is_lost', false)->orderBy('position')->get();
    @endphp
    <div class="py-4" x-data="crmBoard()" x-init="init()">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(16,185,129,0.1); border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #6ee7b7; font-size: 0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header --}}
            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;">Pipeline de Vendas</h1>
                    <div style="display: flex; gap: 1rem; margin-top: 0.375rem; flex-wrap: wrap;">
                        <span style="font-size: 0.75rem; color: #818181;">
                            <strong style="color: #e3e3e3;">{{ $metrics['total_open'] }}</strong> em aberto
                            &middot;
                            <strong style="color: #059669;">R$ {{ number_format($metrics['total_value'], 2, ',', '.') }}</strong> no pipeline
                        </span>
                        <span style="font-size: 0.75rem; color: #818181;">
                            Mês:
                            <strong style="color: #059669;">{{ $metrics['won_month'] }} ganhos</strong> (R$ {{ number_format($metrics['won_value_month'], 2, ',', '.') }})
                            &middot;
                            <strong style="color: #fca5a5;">{{ $metrics['lost_month'] }} perdidos</strong>
                        </span>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                    @php
                        $crmSellers = $sellers->filter(fn($s) => in_array($s->name, ['Guilherme', 'Danilo', 'Sophia']));
                    @endphp
                    <select onchange="window.location.href='{{ route('crm.board') }}?user_id=' + this.value"
                            style="padding: 0.4rem 0.75rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.8rem; background: #141414;">
                        <option value="">Todos os vendedores</option>
                        @foreach($crmSellers as $seller)
                            <option value="{{ $seller->id }}" {{ $filterUserId === $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('crm.history') }}" style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; color: #a4a4a4; text-decoration: none; background: #141414; font-weight: 500;">
                        Histórico
                    </a>
                    <a href="{{ route('crm.deals.create') }}"
                       style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.8rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; cursor: pointer;">
                        Novo Lead
                    </a>
                </div>
            </div>

            {{-- Alertas de atenção --}}
            @if($metrics['stale_4h'] > 0 || $metrics['overdue_followups'] > 0)
                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                    @if($metrics['stale_24h'] > 0)
                        <div style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: rgba(239,68,68,0.1); border: 1px solid #fecaca; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; color: #fca5a5;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            {{ $metrics['stale_24h'] }} lead(s) sem resposta +24h
                        </div>
                    @endif
                    @if($metrics['stale_4h'] > $metrics['stale_24h'])
                        <div style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #1414147ed; border: 1px solid #fed7aa; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; color: #ea580c;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $metrics['stale_4h'] - $metrics['stale_24h'] }} lead(s) sem resposta +4h
                        </div>
                    @endif
                    @if($metrics['overdue_followups'] > 0)
                        <div style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: rgba(239,68,68,0.1); border: 1px solid #fecaca; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; color: #fca5a5;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ $metrics['overdue_followups'] }} follow-up(s) atrasado(s)
                        </div>
                    @endif
                </div>
            @endif

            @include('crm.partials.modal-new-customer')
            @include('crm.partials.modal-new-schedule')

            {{-- Kanban Board --}}
            <div style="display: flex; gap: 0.75rem; overflow-x: auto; padding-bottom: 1rem; min-height: 65vh;">
                @foreach($activeStages as $stage)
                    <div style="min-width: 240px; max-width: 300px; flex: 1; flex-shrink: 0; display: flex; flex-direction: column; background: #1a1a1a; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.06);">
                        {{-- Stage Header --}}
                        <div style="padding: 0.5rem 0.75rem; border-bottom: 2px solid {{ $stage->color }};">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.375rem;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $stage->color }};"></span>
                                    <span style="font-size: 0.7rem; font-weight: 700; color: #e3e3e3;">{{ $stage->name }}</span>
                                </div>
                                <span style="font-size: 0.6rem; font-weight: 600; color: #818181; background: #222; padding: 1px 6px; border-radius: 9999px;">
                                    {{ ($dealsByStage[$stage->id] ?? collect())->count() }}
                                </span>
                            </div>
                            @php $stageValue = ($dealsByStage[$stage->id] ?? collect())->sum('value'); @endphp
                            @if($stageValue > 0)
                                <div style="font-size: 0.6rem; color: #666; margin-top: 0.15rem;">
                                    R$ {{ number_format($stageValue, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        {{-- Cards Container (sortable) --}}
                        <div class="deal-column" data-stage-id="{{ $stage->id }}"
                             style="flex: 1; padding: 0.375rem; display: flex; flex-direction: column; gap: 0.35rem; overflow-y: auto; min-height: 100px;">
                            @foreach(($dealsByStage[$stage->id] ?? collect()) as $deal)
                                @php
                                    $interest = $deal->productInterests->first();
                                    $urgency = $deal->waiting_urgency;
                                    $urgencyColors = [
                                        'green' => '#22c55e',
                                        'yellow' => '#eab308',
                                        'orange' => '#f97316',
                                        'red' => '#ef4444',
                                    ];
                                    $borderColor = $urgencyColors[$urgency] ?? '#e5e7eb';
                                    $tempColors = ['hot' => '#ef4444', 'warm' => '#f59e0b', 'cold' => '#3b82f6'];
                                    $tempLabels = ['hot' => 'Quente', 'warm' => 'Morno', 'cold' => 'Frio'];
                                    $waitingColors = [
                                        'green' => '#059669',
                                        'yellow' => '#ca8a04',
                                        'orange' => '#ea580c',
                                        'red' => '#dc2626',
                                    ];
                                @endphp
                                <div class="deal-card" data-deal-id="{{ $deal->id }}"
                                     x-data="{ editingFollowup: false, followupText: {{ json_encode($deal->next_action ?? '') }}, followupDate: '{{ $deal->next_action_at?->format('Y-m-d\TH:i') ?? '' }}', saving: false }"
                                     style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-left: 3px solid {{ $borderColor }}; border-radius: 0.375rem; cursor: grab; transition: box-shadow 0.15s; position: relative;"
                                     onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">

                                    <a href="{{ route('crm.show', $deal) }}"
                                       style="display: block; padding: 0.5rem 0.625rem; text-decoration: none;"
                                       @click="if (editingFollowup) $event.preventDefault()">

                                        {{-- Badges: origem + temperatura + condição --}}
                                        <div style="display: flex; gap: 0.2rem; flex-wrap: wrap; margin-bottom: 0.25rem; align-items: center;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $tempColors[$deal->temperature] ?? '#f59e0b' }}; flex-shrink: 0;"
                                                  title="{{ $tempLabels[$deal->temperature] ?? 'Morno' }}"></span>
                                            @if($deal->lead_source)
                                                <span style="font-size: 0.55rem; font-weight: 600; padding: 0px 4px; border-radius: 3px; color: white; background: {{ $deal->lead_source->color() }};">
                                                    {{ $deal->lead_source->label() }}
                                                </span>
                                            @endif
                                            @if($deal->product_interest)
                                                <span style="font-size: 0.55rem; font-weight: 600; color: #888; background: #1e1e1e; padding: 0px 4px; border-radius: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 110px;">
                                                    {{ $deal->product_interest }}
                                                </span>
                                            @endif
                                            @if($interest && $interest->condition)
                                                <span style="font-size: 0.5rem; font-weight: 600; padding: 0px 4px; border-radius: 3px; {{ $interest->condition === 'novo' ? 'background: rgba(59,130,246,0.15); color: #93c5fd;' : 'background: #fef3c7; color: #fbbf24;' }}">
                                                    {{ $interest->condition === 'novo' ? 'N' : 'U' }}
                                                </span>
                                            @endif
                                        </div>

                                        <div style="font-size: 0.75rem; font-weight: 600; color: #e3e3e3; line-height: 1.2; display: flex; align-items: center; gap: 0.25rem;">
                                            <span style="flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $deal->title }}</span>
                                            @if($deal->phone)
                                                <span style="font-size: 0.55rem; font-weight: 500; color: #555; flex-shrink: 0;">{{ substr(preg_replace('/\D/', '', $deal->phone), -4) }}</span>
                                            @endif
                                            @if($deal->unread_wa_count > 0)
                                                <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 16px; height: 16px; padding: 0 3px; border-radius: 8px; background: #25D366; color: #000; font-size: 0.6rem; font-weight: 700; flex-shrink: 0; animation: pulse-badge 2s infinite;" title="{{ $deal->unread_wa_count }} mensagens não lidas">
                                                    {{ $deal->unread_wa_count }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($deal->customer)
                                            <div style="font-size: 0.65rem; color: #777; margin-top: 0.15rem; display: flex; align-items: center; gap: 0.25rem; overflow: hidden;">
                                                @if($deal->customer->wa_avatar_url)
                                                    <img src="{{ $deal->customer->wa_avatar_url }}" alt="" style="width: 14px; height: 14px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1px solid #333;">
                                                @endif
                                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $deal->customer->name }}</span>
                                                @if($deal->customer->wa_is_business)
                                                    <span style="font-size: 0.45rem; font-weight: 700; padding: 0px 2px; border-radius: 2px; background: rgba(37,211,102,0.15); color: #25D366; flex-shrink: 0;">BIZ</span>
                                                @endif
                                            </div>
                                            @if($deal->customer->tags->isNotEmpty())
                                                <div style="display: flex; gap: 0.15rem; flex-wrap: wrap; margin-top: 0.15rem;">
                                                    @foreach($deal->customer->tags->take(3) as $ctag)
                                                        <span style="font-size: 0.5rem; font-weight: 600; padding: 0px 4px; border-radius: 3px; background: {{ $ctag->color }}20; color: {{ $ctag->color }};">
                                                            {{ $ctag->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($deal->customer->tags->count() > 3)
                                                        <span style="font-size: 0.45rem; color: #555;">+{{ $deal->customer->tags->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Rodapé: valor + tempo + vendedor --}}
                                        <div style="margin-top: 0.3rem; display: flex; align-items: center; justify-content: space-between; gap: 0.25rem;">
                                            <div style="display: flex; align-items: center; gap: 0.3rem;">
                                                <span style="font-size: 0.55rem; font-weight: 600; color: {{ $waitingColors[$urgency] ?? '#6b7280' }};">
                                                    ⏱ {{ $deal->waiting_time_label }}
                                                </span>
                                                @if($deal->whatsapp_link)
                                                    <svg viewBox="0 0 24 24" fill="#25D366" style="width: 10px; height: 10px; flex-shrink: 0;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                @endif
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 0.3rem;">
                                                @if($deal->value)
                                                    <span style="font-size: 0.6rem; font-weight: 700; color: #059669;">
                                                        R$ {{ number_format((float)$deal->value, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                                <span style="font-size: 0.5rem; color: #555;">{{ $deal->user?->name }}</span>
                                            </div>
                                        </div>
                                    </a>

                                    {{-- Tag section --}}
                                    @if($deal->customer)
                                    <div style="padding: 0 0.625rem 0.25rem 0.625rem;" x-data="{ showTagMenu: false }">
                                        <button @click.stop="showTagMenu = !showTagMenu" type="button"
                                                style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.1rem 0.3rem; border-radius: 0.2rem; font-size: 0.5rem; color: #555; background: none; border: 1px dashed rgba(255,255,255,0.06); cursor: pointer; transition: all 0.15s;"
                                                onmouseover="this.style.borderColor='rgba(59,130,246,0.3)'; this.style.color='#93c5fd'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'; this.style.color='#555'">
                                            <svg style="width: 8px; height: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            Tags
                                        </button>
                                        <div x-show="showTagMenu" x-transition @click.away="showTagMenu = false" @click.stop
                                             style="position: absolute; z-index: 50; margin-top: 0.25rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.375rem; padding: 0.375rem; min-width: 160px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                                            @foreach($allTags as $atag)
                                                @php $hasTag = $deal->customer->tags->contains('id', $atag->id); @endphp
                                                <button @click.stop="toggleTag('{{ $deal->customer->id }}', '{{ $atag->id }}', {{ $hasTag ? 'true' : 'false' }})"
                                                        type="button"
                                                        style="display: flex; align-items: center; gap: 0.375rem; width: 100%; padding: 0.3rem 0.5rem; border-radius: 0.25rem; font-size: 0.7rem; color: {{ $hasTag ? $atag->color : '#a4a4a4' }}; background: {{ $hasTag ? $atag->color . '15' : 'transparent' }}; border: none; cursor: pointer; text-align: left; transition: background 0.15s;"
                                                        onmouseover="this.style.background='{{ $atag->color }}20'" onmouseout="this.style.background='{{ $hasTag ? $atag->color . '15' : 'transparent' }}'">
                                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $atag->color }}; flex-shrink: 0;"></span>
                                                    {{ $atag->name }}
                                                    @if($hasTag)
                                                        <svg style="width: 12px; height: 12px; margin-left: auto; color: {{ $atag->color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Follow-up section --}}
                                    <div style="padding: 0 0.625rem 0.5rem 0.625rem;" x-show="!editingFollowup">
                                        @if($deal->next_action)
                                            <div style="padding: 0.25rem 0.375rem; border-radius: 0.25rem; font-size: 0.6rem; line-height: 1.3; position: relative; {{ $deal->is_followup_overdue ? 'background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5;' : 'background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); color: #93c5fd;' }}">
                                                <div style="display: flex; align-items: flex-start; gap: 0.25rem;">
                                                    <svg style="width: 10px; height: 10px; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                    <span class="followup-text" style="word-break: break-word; flex: 1;">{{ $deal->next_action }}</span>
                                                    <button @click.stop="editingFollowup = true" type="button" title="Editar follow-up"
                                                            style="flex-shrink: 0; background: none; border: none; cursor: pointer; padding: 0; color: inherit; opacity: 0.5; transition: opacity 0.15s;"
                                                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">
                                                        <svg style="width: 10px; height: 10px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                @if($deal->next_action_at)
                                                    <div style="font-size: 0.6rem; font-weight: 600; margin-top: 0.125rem; opacity: 0.8;">
                                                        {{ $deal->next_action_at->format('d/m H:i') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <button @click.stop="editingFollowup = true" type="button"
                                                    style="width: 100%; padding: 0.15rem; border-radius: 0.25rem; font-size: 0.55rem; color: #555; background: none; border: 1px dashed rgba(255,255,255,0.06); cursor: pointer; transition: all 0.15s;"
                                                    onmouseover="this.style.borderColor='rgba(59,130,246,0.3)'; this.style.color='#93c5fd'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'; this.style.color='#555'">
                                                + Adicionar follow-up
                                            </button>
                                        @endif

                                        @if($deal->description)
                                            <div class="followup-text" style="margin-top: 0.15rem; font-size: 0.55rem; color: #555; line-height: 1.2; word-break: break-word; font-style: italic; padding-left: 0.2rem; border-left: 2px solid rgba(255,255,255,0.04); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ Str::limit($deal->description, 60) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Inline follow-up edit form --}}
                                    <div x-show="editingFollowup" x-transition @click.stop style="padding: 0 0.75rem 0.75rem 0.75rem;">
                                        <div style="background: #1a1a1a; border: 1px solid rgba(59,130,246,0.3); border-radius: 0.375rem; padding: 0.5rem;">
                                            <textarea x-model="followupText" rows="2" placeholder="Ex: esperando resposta sobre parcelamento..."
                                                      x-ref="followupInput"
                                                      @keydown.escape="editingFollowup = false"
                                                      @keydown.ctrl.enter="$dispatch('save-followup')"
                                                      style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.25rem; font-size: 0.75rem; resize: vertical; background: #141414; color: #e3e3e3; min-height: 40px;"></textarea>
                                            <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem;">
                                                <input type="datetime-local" x-model="followupDate"
                                                       style="flex: 1; padding: 0.25rem 0.375rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.25rem; font-size: 0.65rem; background: #141414; color: #e3e3e3;">
                                                <button @click="editingFollowup = false" type="button"
                                                        style="padding: 0.25rem 0.5rem; font-size: 0.65rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.25rem; background: #141414; color: #a4a4a4; cursor: pointer;">
                                                    Cancelar
                                                </button>
                                                <button @click="saveFollowup('{{ $deal->id }}', $el)" :disabled="saving" type="button"
                                                        style="padding: 0.25rem 0.5rem; font-size: 0.65rem; border: none; border-radius: 0.25rem; background: #3b82f6; color: white; cursor: pointer; font-weight: 600;">
                                                    <span x-show="!saving">Salvar</span>
                                                    <span x-show="saving">...</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
    function crmBoard() {
        return {
            showCustomerForm: false,
            showScheduleForm: false,
            customerSearch: '',
            customerResults: [],
            selectedCustomerId: '',
            selectedCustomerName: '',
            scheduleCustomerSearch: '',
            scheduleCustomerResults: [],

            init() {
                this.initSortable();
            },

            initSortable() {
                document.querySelectorAll('.deal-column').forEach(column => {
                    new Sortable(column, {
                        group: 'deals',
                        animation: 150,
                        ghostClass: 'opacity-30',
                        dragClass: 'shadow-xl',
                        handle: '.deal-card',
                        draggable: '.deal-card',
                        onEnd: (evt) => {
                            const dealId = evt.item.dataset.dealId;
                            const newStageId = evt.to.dataset.stageId;
                            const newIndex = evt.newIndex;

                            evt.item.addEventListener('click', function preventClick(e) {
                                e.preventDefault();
                                evt.item.removeEventListener('click', preventClick);
                            }, { once: true });

                            fetch(`/crm/deals/${dealId}/move`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    pipeline_stage_id: newStageId,
                                    position: newIndex,
                                }),
                            }).catch(err => {
                                console.error('Erro ao mover deal:', err);
                                window.location.reload();
                            });
                        },
                    });
                });
            },

            async searchCustomers() {
                if (this.customerSearch.length < 2) {
                    this.customerResults = [];
                    return;
                }
                try {
                    const res = await fetch(`/api/customers/search?q=${encodeURIComponent(this.customerSearch)}`);
                    const data = await res.json();
                    this.customerResults = data.slice(0, 5);
                } catch (e) {
                    this.customerResults = [];
                }
            },

            selectCustomer(customer) {
                this.selectedCustomerId = customer.id;
                this.selectedCustomerName = customer.name;
                this.customerSearch = '';
                this.customerResults = [];
            },

            clearCustomer() {
                this.selectedCustomerId = '';
                this.selectedCustomerName = '';
                this.customerSearch = '';
            },

            async searchScheduleCustomers() {
                if (this.scheduleCustomerSearch.length < 2) {
                    this.scheduleCustomerResults = [];
                    return;
                }
                try {
                    const res = await fetch(`/api/customers/search?q=${encodeURIComponent(this.scheduleCustomerSearch)}`);
                    const data = await res.json();
                    this.scheduleCustomerResults = data.slice(0, 5);
                } catch (e) {
                    this.scheduleCustomerResults = [];
                }
            },

            selectScheduleCustomer(customer) {
                document.getElementById('schedule_customer_id').value = customer.id;
                document.getElementById('schedule_customer_name').value = customer.name;
                document.getElementById('schedule_customer_phone').value = customer.phone || '';
                this.scheduleCustomerSearch = '';
                this.scheduleCustomerResults = [];
            },
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.followup-text').forEach(function(el) {
            el.innerHTML = el.textContent.replace(/~([^~]+)~/g, '<s style="opacity: 0.5;">$1</s>');
        });

        const notifSound = new Audio('/sounds/notification.mp3');
        let lastCheck = new Date().toISOString();
        let notifContainer = document.getElementById('wa-notifications');

        function canReload() {
            const hasModal = document.querySelector('[x-data]')?.__x?.$data?.showCustomerForm
                || document.querySelector('[x-data]')?.__x?.$data?.showScheduleForm;
            const hasEditing = document.querySelector('[x-show="editingFollowup"]:not([style*="display: none"])');
            return !hasModal && !hasEditing && !document.querySelector('.sortable-ghost');
        }

        async function pollNewDeals() {
            try {
                const res = await fetch(`/api/crm/new-deals-check?since=${encodeURIComponent(lastCheck)}`);
                const data = await res.json();
                lastCheck = data.server_time;

                if (data.count > 0) {
                    data.deals.forEach(d => {
                        showNotification(d);
                    });
                    try { notifSound.play(); } catch(e) {}
                    if (canReload()) {
                        setTimeout(() => window.location.reload(), 2000);
                    }
                }
            } catch(e) {}
        }

        function showNotification(deal) {
            const toast = document.createElement('div');
            toast.innerHTML = `
                <a href="/crm/deals/${deal.id}" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: inherit;">
                    <svg style="width: 20px; height: 20px; color: #25D366; flex-shrink: 0;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <div>
                        <div style="font-weight: 600; font-size: 0.8rem;">Novo lead via WhatsApp</div>
                        <div style="font-size: 0.75rem; color: #a4a4a4;">${deal.customer_name || deal.title}</div>
                    </div>
                </a>
            `;
            toast.style.cssText = 'position: fixed; top: 1rem; right: 1rem; z-index: 9999; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid #25D366; border-radius: 0.75rem; box-shadow: 0 4px 20px rgba(37,211,102,0.2); animation: slideIn 0.3s ease-out; min-width: 250px;';
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.5s'; setTimeout(() => toast.remove(), 500); }, 8000);
        }

        setInterval(pollNewDeals, 5000);

        setInterval(function() {
            if (canReload()) window.location.reload();
        }, 30000);
    });

    window.toggleTag = async function(customerId, tagId, hasTag) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        if (hasTag) {
            await fetch(`/api/customers/${customerId}/tags/${tagId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            });
        } else {
            await fetch(`/api/customers/${customerId}/tags/add`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ tag_id: tagId }),
            });
        }

        window.location.reload();
    }

    window.saveFollowup = async function(dealId, el) {
        const card = el.closest('[x-data]');
        const component = Alpine.$data(card);
        component.saving = true;

        try {
            const res = await fetch(`/crm/deals/${dealId}/followup`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    next_action: component.followupText || null,
                    next_action_at: component.followupDate || null,
                }),
            });

            if (res.ok) {
                window.location.reload();
            } else {
                alert('Erro ao salvar follow-up.');
            }
        } catch (e) {
            alert('Erro de conexão.');
        } finally {
            component.saving = false;
        }
    }
    </script>
</x-app-layout>
