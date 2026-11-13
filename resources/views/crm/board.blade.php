<x-app-layout>
    <x-slot name="title">CRM - Pipeline</x-slot>
    <style>
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes pulse-badge { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
        @keyframes glow-pulse {
            0%, 100% { box-shadow: 0 0 4px rgba(37,211,102,0.3), inset 0 0 0 1px rgba(37,211,102,0.25); }
            50% { box-shadow: 0 0 12px rgba(37,211,102,0.5), inset 0 0 0 1px rgba(37,211,102,0.5); }
        }
        .deal-card-unread {
            border-color: rgba(37,211,102,0.4) !important;
            animation: glow-pulse 2s ease-in-out infinite;
        }
    </style>
    @php
        $activeStages = \App\Domain\CRM\Models\PipelineStage::where('is_won', false)->where('is_lost', false)->orderBy('position')->get();
    @endphp
    <div class="py-4" x-data="crmBoard()" x-init="init()">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 py-3 px-4 bg-emerald-500/10 border border-emerald-500/35 rounded-lg text-emerald-300 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header --}}
            <div class="flex items-start justify-between mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-dg-100">Pipeline de Vendas</h1>
                    <div class="flex gap-4 mt-1.5 flex-wrap">
                        <span class="text-xs text-dg-400">
                            <strong class="text-dg-100">{{ $metrics['total_open'] }}</strong> em aberto
                            &middot;
                            <strong class="text-emerald-600">R$ {{ number_format($metrics['total_value'], 2, ',', '.') }}</strong> no pipeline
                        </span>
                        <span class="text-xs text-dg-400">
                            Mês:
                            <strong class="text-emerald-600">{{ $metrics['won_month'] }} ganhos</strong> (R$ {{ number_format($metrics['won_value_month'], 2, ',', '.') }})
                            &middot;
                            <strong class="text-red-300">{{ $metrics['lost_month'] }} perdidos</strong>
                        </span>
                    </div>
                </div>
                <div class="flex gap-2 items-center flex-wrap">
                    @php
                        $crmSellers = $sellers->filter(fn($s) => in_array($s->name, ['Guilherme', 'Danilo', 'Sophia']));
                    @endphp
                    <select class="py-[0.4rem] px-3 border border-border rounded-lg text-[0.8rem] bg-surface-raised" onchange="window.location.href='{{ route('crm.board') }}?user_id=' + this.value">
                        <option value="">Todos os vendedores</option>
                        @foreach($crmSellers as $seller)
                            <option value="{{ $seller->id }}" {{ $filterUserId === $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                    <a class="py-2 px-4 text-[0.8rem] border border-border rounded-lg text-dg-300 no-underline bg-surface-raised font-medium" href="{{ route('crm.history') }}">
                        Histórico
                    </a>
                    <a class="inline-flex items-center gap-1.5 py-2 px-4 bg-[#111827] text-white text-[0.8rem] font-semibold rounded-lg no-underline cursor-pointer" href="{{ route('crm.deals.create') }}">
                        Novo Lead
                    </a>
                </div>
            </div>

            {{-- Alertas de atenção --}}
            @if($metrics['stale_4h'] > 0 || $metrics['overdue_followups'] > 0)
                <div class="flex gap-2 mb-3 flex-wrap">
                    @if($metrics['stale_24h'] > 0)
                        <div class="inline-flex items-center gap-1.5 py-1.5 px-3 bg-red-500/10 border border-red-500/35 rounded-lg text-xs font-semibold text-red-300">
                            <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            {{ $metrics['stale_24h'] }} lead(s) sem resposta +24h
                        </div>
                    @endif
                    @if($metrics['stale_4h'] > $metrics['stale_24h'])
                        <div class="inline-flex items-center gap-1.5 py-1.5 px-3 bg-orange-500/[0.12] border border-orange-500/35 rounded-lg text-xs font-semibold text-orange-600">
                            <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $metrics['stale_4h'] - $metrics['stale_24h'] }} lead(s) sem resposta +4h
                        </div>
                    @endif
                    @if($metrics['overdue_followups'] > 0)
                        <div class="inline-flex items-center gap-1.5 py-1.5 px-3 bg-red-500/10 border border-red-500/35 rounded-lg text-xs font-semibold text-red-300">
                            <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
            <div class="flex gap-3 overflow-x-auto pb-4 min-h-[65vh]">
                @foreach($activeStages as $stage)
                    <div class="min-w-[240px] max-w-[300px] flex-1 shrink-0 flex flex-col bg-surface-overlay rounded-lg border border-border">
                        {{-- Stage Header --}}
                        <div class="py-2 px-3" style="border-bottom: 2px solid {{ $stage->color }};">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-[8px] h-[8px] rounded-full" style="background: {{ $stage->color }};"></span>
                                    <span class="text-[0.7rem] font-bold text-dg-100">{{ $stage->name }}</span>
                                </div>
                                <span class="text-[0.6rem] font-semibold text-dg-400 bg-surface-elevated py-[1px] px-[6px] rounded-full">
                                    {{ ($dealsByStage[$stage->id] ?? collect())->count() }}
                                </span>
                            </div>
                            @php $stageValue = ($dealsByStage[$stage->id] ?? collect())->sum('value'); @endphp
                            @if($stageValue > 0)
                                <div class="text-[0.6rem] text-dg-500 mt-[0.15rem]">
                                    R$ {{ number_format($stageValue, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        {{-- Cards Container (sortable) --}}
                        <div class="deal-column flex-1 p-1.5 flex flex-col gap-[0.35rem] overflow-y-auto min-h-[100px]" data-stage-id="{{ $stage->id }}">
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
                                    $borderColor = $urgencyColors[$urgency] ?? 'rgba(255,255,255,0.08)';
                                    $tempColors = ['hot' => '#ef4444', 'warm' => '#f59e0b', 'cold' => '#3b82f6'];
                                    $tempLabels = ['hot' => 'Quente', 'warm' => 'Morno', 'cold' => 'Frio'];
                                    $waitingColors = [
                                        'green' => '#059669',
                                        'yellow' => '#ca8a04',
                                        'orange' => '#ea580c',
                                        'red' => '#dc2626',
                                    ];
                                @endphp
                                <div class="deal-card {{ $deal->unread_wa_count > 0 ? 'deal-card-unread' : '' }}" data-deal-id="{{ $deal->id }}"
                                     x-data="{ editingFollowup: false, followupText: {{ json_encode($deal->next_action ?? '') }}, followupDate: '{{ $deal->next_action_at?->format('Y-m-d\TH:i') ?? '' }}', saving: false }"
                                     style="background: {{ $deal->unread_wa_count > 0 ? '#0d1f13' : '#141414' }}; border: 1px solid {{ $deal->unread_wa_count > 0 ? 'rgba(37,211,102,0.3)' : 'rgba(255,255,255,0.06)' }}; border-left: 3px solid {{ $deal->unread_wa_count > 0 ? '#25D366' : $borderColor }}; border-radius: 0.375rem; cursor: grab; transition: box-shadow 0.15s; position: relative;"
                                     onmouseover="this.style.boxShadow='0 2px 8px {{ $deal->unread_wa_count > 0 ? 'rgba(37,211,102,0.15)' : 'rgba(0,0,0,0.08)' }}'" onmouseout="this.style.boxShadow='none'">

                                    <a class="block py-2 px-2.5 no-underline" href="{{ route('crm.show', $deal) }}"

                                       @click="if (editingFollowup) $event.preventDefault()">

                                        {{-- Badges: origem + temperatura + condição --}}
                                        <div class="flex gap-[0.2rem] flex-wrap mb-1 items-center">
                                            <span class="w-[6px] h-[6px] rounded-full" style="background: {{ $tempColors[$deal->temperature] ?? '#f59e0b' }}; flex-shrink: 0;"
                                                  title="{{ $tempLabels[$deal->temperature] ?? 'Morno' }}"></span>
                                            @if($deal->lead_source)
                                                <span class="text-[0.55rem] font-semibold py-0 px-[4px] rounded-[3px] text-white" style="background: {{ $deal->lead_source->color() }};">
                                                    {{ $deal->lead_source->label() }}
                                                </span>
                                            @endif
                                            @if($deal->product_interest)
                                                <span class="text-[0.55rem] font-semibold text-[#888] bg-[#1e1e1e] py-0 px-[4px] rounded-[3px] overflow-hidden text-ellipsis whitespace-nowrap max-w-[110px]">
                                                    {{ $deal->product_interest }}
                                                </span>
                                            @endif
                                            @if($interest && $interest->condition)
                                                <span class="text-[0.5rem] font-semibold py-0 px-[4px] rounded-[3px]" style="{{ $interest->condition === 'novo' ? 'background: rgba(59,130,246,0.15); color: #93c5fd;' : 'background: rgba(245,158,11,0.15); color: #fbbf24;' }}">
                                                    {{ $interest->condition === 'novo' ? 'N' : 'U' }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-xs font-semibold text-dg-100 leading-[1.2] flex items-center gap-1">
                                            <span class="flex-1 min-w-0 overflow-hidden text-ellipsis whitespace-nowrap">{{ $deal->title }}</span>
                                            @if($deal->phone)
                                                <span class="text-[0.55rem] font-medium text-dg-500 shrink-0">{{ substr(preg_replace('/\D/', '', $deal->phone), -4) }}</span>
                                            @endif
                                            @if($deal->unread_wa_count > 0)
                                                <span class="inline-flex items-center justify-center min-w-[16px] h-[16px] py-0 px-[3px] rounded-[8px] bg-[#25d366] text-dg-100 text-[0.6rem] font-bold shrink-0" style="animation: pulse-badge 2s infinite;" title="{{ $deal->unread_wa_count }} mensagens não lidas">
                                                    {{ $deal->unread_wa_count }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($deal->customer)
                                            <div class="text-[0.65rem] text-[#777] mt-[0.15rem] flex items-center gap-1 overflow-hidden">
                                                @if($deal->customer->wa_avatar_url)
                                                    <img class="w-[14px] h-[14px] rounded-full object-cover shrink-0 border border-[#333]" src="{{ $deal->customer->wa_avatar_url }}" alt="">
                                                @endif
                                                <span class="overflow-hidden text-ellipsis whitespace-nowrap">{{ $deal->customer->name }}</span>
                                                @if($deal->customer->wa_is_business)
                                                    <span class="text-[0.45rem] font-bold py-0 px-[2px] rounded-[2px] bg-[rgba(37,211,102,0.15)] text-[#25d366] shrink-0">BIZ</span>
                                                @endif
                                            </div>
                                            @if($deal->customer->tags->isNotEmpty())
                                                <div class="flex gap-[0.15rem] flex-wrap mt-[0.15rem]">
                                                    @foreach($deal->customer->tags->take(3) as $ctag)
                                                        <span class="text-[0.5rem] font-semibold py-0 px-[4px] rounded-[3px]" style="background: {{ $ctag->color }}20; color: {{ $ctag->color }};">
                                                            {{ $ctag->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($deal->customer->tags->count() > 3)
                                                        <span class="text-[0.45rem] text-dg-500">+{{ $deal->customer->tags->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Rodapé: valor + tempo + vendedor --}}
                                        <div class="mt-[0.3rem] flex items-center justify-between gap-1">
                                            <div class="flex items-center gap-[0.3rem]">
                                                <span class="text-[0.55rem] font-semibold" style="color: {{ $waitingColors[$urgency] ?? '#6b7280' }};">
                                                    ⏱ {{ $deal->waiting_time_label }}
                                                </span>
                                                @if($deal->whatsapp_link)
                                                    <svg class="w-[10px] h-[10px] shrink-0" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-[0.3rem]">
                                                @if($deal->value)
                                                    <span class="text-[0.6rem] font-bold text-emerald-600">
                                                        R$ {{ number_format((float)$deal->value, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                                <span class="text-[0.5rem] text-dg-500">{{ $deal->user?->name }}</span>
                                            </div>
                                        </div>
                                    </a>

                                    {{-- Tag section --}}
                                    @if($deal->customer)
                                    <div class="pt-0 px-2.5 pb-1" x-data="{ showTagMenu: false }">
                                        <button class="inline-flex items-center gap-[0.2rem] py-[0.1rem] px-[0.3rem] rounded-[0.2rem] text-[0.5rem] text-dg-500 bg-transparent cursor-pointer transition-all hover:border-blue-500/30 hover:text-blue-300" @click.stop="showTagMenu = !showTagMenu" type="button"
                                                style="border: 1px dashed rgba(255,255,255,0.06);">
                                            <svg class="w-[8px] h-[8px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            Tags
                                        </button>
                                        <div class="absolute z-[50] mt-1 bg-surface-overlay border border-white/10 rounded-md p-1.5 min-w-[160px] shadow-[0_4px_12px_rgba(0,0,0,0.3)]" x-show="showTagMenu" x-transition @click.away="showTagMenu = false" @click.stop>
                                            @foreach($allTags as $atag)
                                                @php $hasTag = $deal->customer->tags->contains('id', $atag->id); @endphp
                                                <button class="flex items-center gap-1.5 w-full py-[0.3rem] px-2 rounded text-[0.7rem]" @click.stop="toggleTag('{{ $deal->customer->id }}', '{{ $atag->id }}', {{ $hasTag ? 'true' : 'false' }})"
                                                        type="button"
                                                        style="color: {{ $hasTag ? $atag->color : '#a4a4a4' }}; background: {{ $hasTag ? $atag->color . '15' : 'transparent' }}; border: none; cursor: pointer; text-align: left; transition: background 0.15s;"
                                                        onmouseover="this.style.background='{{ $atag->color }}20'" onmouseout="this.style.background='{{ $hasTag ? $atag->color . '15' : 'transparent' }}'">
                                                    <span class="w-[8px] h-[8px] rounded-full" style="background: {{ $atag->color }}; flex-shrink: 0;"></span>
                                                    {{ $atag->name }}
                                                    @if($hasTag)
                                                        <svg class="w-[12px] h-[12px] ml-auto" style="color: {{ $atag->color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Follow-up section --}}
                                    <div class="pt-0 px-2.5 pb-2" x-show="!editingFollowup">
                                        @if($deal->next_action)
                                            <div class="py-1 px-1.5 rounded text-[0.6rem] leading-[1.3] relative" style="{{ $deal->is_followup_overdue ? 'background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5;' : 'background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); color: #93c5fd;' }}">
                                                <div class="flex items-start gap-1">
                                                    <svg class="w-[10px] h-[10px] shrink-0 mt-[2px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                    <span class="followup-text flex-1" style="word-break: break-word;">{{ $deal->next_action }}</span>
                                                    <button class="shrink-0 bg-transparent border-0 cursor-pointer p-0 opacity-[0.5]" @click.stop="editingFollowup = true" type="button" title="Editar follow-up"
                                                            style="color: inherit; transition: opacity 0.15s;"
                                                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">
                                                        <svg class="w-[10px] h-[10px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                @if($deal->next_action_at)
                                                    <div class="text-[0.6rem] font-semibold mt-0.5 opacity-[0.8]">
                                                        {{ $deal->next_action_at->format('d/m H:i') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <button class="w-full p-[0.15rem] rounded text-[0.55rem] text-dg-500 bg-transparent cursor-pointer transition-all hover:border-blue-500/30 hover:text-blue-300" @click.stop="editingFollowup = true" type="button"
                                                    style="border: 1px dashed rgba(255,255,255,0.06);">
                                                + Adicionar follow-up
                                            </button>
                                        @endif

                                        @if($deal->description)
                                            <div class="followup-text mt-[0.15rem] text-[0.55rem] text-dg-500 leading-[1.2] italic pl-[0.2rem] border-l-2 border-border-subtle overflow-hidden text-ellipsis whitespace-nowrap" style="word-break: break-word;">
                                                {{ Str::limit($deal->description, 60) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Inline follow-up edit form --}}
                                    <div class="pt-0 px-3 pb-3" x-show="editingFollowup" x-transition @click.stop>
                                        <div class="bg-surface-overlay border border-blue-500/30 rounded-md p-2">
                                            <textarea class="w-full p-1.5 border border-white/10 rounded text-xs resize-y bg-surface-raised text-dg-100 min-h-[40px]" x-model="followupText" rows="2" placeholder="Ex: esperando resposta sobre parcelamento..."
                                                      x-ref="followupInput"
                                                      @keydown.escape="editingFollowup = false"
                                                      @keydown.ctrl.enter="$dispatch('save-followup')"></textarea>
                                            <div class="flex items-center gap-1.5 mt-1.5">
                                                <input class="flex-1 py-1 px-1.5 border border-white/10 rounded text-[0.65rem] bg-surface-raised text-dg-100" type="datetime-local" x-model="followupDate">
                                                <button class="py-1 px-2 text-[0.65rem] border border-white/10 rounded bg-surface-raised text-dg-300 cursor-pointer" @click="editingFollowup = false" type="button">
                                                    Cancelar
                                                </button>
                                                <button class="py-1 px-2 text-[0.65rem] border-0 rounded bg-blue-500 text-white cursor-pointer font-semibold" @click="saveFollowup('{{ $deal->id }}', $el)" :disabled="saving" type="button">
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
