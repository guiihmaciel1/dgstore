<x-app-layout>
    <x-slot name="title">CRM - Negócio</x-slot>
    @php
        $activeStages = \App\Domain\CRM\Models\PipelineStage::where('is_won', false)->where('is_lost', false)->orderBy('position')->get();
    @endphp
    <div class="deal-page" x-data="dealPage()">
        <div class="py-0 px-6">

            @if(session('success'))
                <div class="mb-4 py-3 px-4 bg-emerald-500/[0.08] border border-emerald-500/20 rounded-lg text-emerald-300 text-[0.85rem]">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Top Bar: Breadcrumb + Deal Title + Status --}}
            <div class="flex items-center justify-between mb-3 py-2 px-0">
                <div class="flex items-center gap-3">
                    <a class="inline-flex items-center justify-center w-[30px] h-[30px] rounded-md border border-border bg-surface-raised text-dg-300 no-underline transition-all hover:bg-surface-overlay hover:text-dg-100" href="{{ route('crm.board') }}" title="Voltar ao Pipeline">
                        <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <div class="text-[0.65rem] text-dg-500 mb-0.5">
                            <a class="text-blue-500 no-underline" href="{{ route('crm.board') }}">Pipeline</a>
                            <span class="my-0 mx-1">/</span>
                            <span>{{ $deal->stage->name }}</span>
                        </div>
                        <h1 class="text-[1.1rem] font-bold text-dg-100 m-0 leading-[1.2]">{{ $deal->title }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($deal->value)
                        <span class="text-base font-bold text-emerald-600 bg-[rgba(5,150,105,0.08)] py-1 px-3 rounded-md border border-[rgba(5,150,105,0.15)]">
                            R$ {{ number_format((float)$deal->value, 2, ',', '.') }}
                        </span>
                    @endif
                    {{-- Pipeline Progress Mini --}}
                    <div class="flex gap-[2px]">
                        @foreach($activeStages as $s)
                            @php $isCurrent = $deal->pipeline_stage_id === $s->id; $isPast = $s->position < $deal->stage->position; @endphp
                            <div class="w-[20px] h-[4px] rounded-[2px]" style="background: {{ $isCurrent ? $s->color : ($isPast ? $s->color . '80' : '#222') }};" title="{{ $s->name }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if(!$deal->isOpen())
                <div class="py-3 px-4 rounded-lg text-[0.85rem] font-semibold mb-3" style="{{ $deal->isWon() ? 'background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); color: #6ee7b7;' : 'background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5;' }}">
                    {{ $deal->isWon() ? 'Negócio GANHO em ' . $deal->won_at->format('d/m/Y H:i') : 'Negócio PERDIDO em ' . $deal->lost_at->format('d/m/Y H:i') }}
                    @if($deal->lost_reason) — Motivo: {{ $deal->lost_reason }} @endif
                    <form class="inline ml-4" method="POST" action="{{ route('crm.deals.reopen', $deal) }}">
                        @csrf
                        <input type="hidden" name="pipeline_stage_id" value="{{ $stages->where('is_default', true)->first()?->id ?? $stages->first()?->id }}">
                        <button class="text-xs" type="submit" style="color: {{ $deal->isWon() ? '#6ee7b7' : '#fca5a5' }}; text-decoration: underline; border: none; background: none; cursor: pointer;">Reabrir</button>
                    </form>
                </div>
            @endif

            {{-- Layout Principal: 2 colunas --}}
            <div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)] gap-3" style="height: calc(100vh - 160px);">

                {{-- COLUNA ESQUERDA: Detalhes --}}
                <div  class="custom-scroll flex flex-col gap-3 overflow-y-auto pr-1">

                    {{-- Card: Dados do Negócio --}}
                    <div class="bg-surface-raised border border-border rounded-xl p-4">
                        <div class="grid grid-cols-[1fr_1fr] gap-2.5 text-[0.8rem]">
                            {{-- Etapa --}}
                            <div>
                                <span class="text-dg-500 font-semibold text-[0.65rem] block uppercase tracking-wider">Etapa</span>
                                <span class="inline-flex items-center gap-1 mt-[2px]">
                                    <span class="w-[8px] h-[8px] rounded-full" style="background: {{ $deal->stage->color }};"></span>
                                    {{ $deal->stage->name }}
                                </span>
                            </div>
                            {{-- Vendedor --}}
                            <div>
                                <span class="text-dg-500 font-semibold text-[0.65rem] block uppercase tracking-wider">Vendedor</span>
                                <span class="mt-[2px]">{{ $deal->user->name }}</span>
                            </div>
                            {{-- Cliente --}}
                            @if($deal->customer)
                                <div style="grid-column: 1 / -1;">
                                    <span class="text-dg-500 font-semibold text-[0.65rem] block uppercase tracking-wider">Cliente</span>
                                    <div class="flex items-center gap-2 mt-[4px]">
                                        @if($deal->customer->wa_avatar_url)
                                            <img class="w-[28px] h-[28px] rounded-full object-cover border-2 border-[#25d366]" src="{{ $deal->customer->wa_avatar_url }}" alt="">
                                        @endif
                                        <div>
                                            <a class="text-blue-500 no-underline font-medium" href="{{ route('customers.show', $deal->customer) }}">{{ $deal->customer->name }}</a>
                                            @if($deal->customer->wa_is_business)
                                                <span class="text-[0.6rem] font-semibold py-[1px] px-[5px] rounded-[4px] bg-[rgba(37,211,102,0.12)] text-[#25d366] ml-1">
                                                    {{ $deal->customer->wa_business_name ?: 'Business' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($deal->customer->tags->isNotEmpty())
                                        <div class="flex gap-1 flex-wrap mt-1.5">
                                            @foreach($deal->customer->tags as $ctag)
                                                <span class="text-[0.6rem] font-semibold py-[1px] px-[6px] rounded-[4px]" style="background: {{ $ctag->color }}15; color: {{ $ctag->color }}; border: 1px solid {{ $ctag->color }}30;">
                                                    {{ $ctag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {{-- Última Interação --}}
                            <div>
                                <span class="text-dg-500 font-semibold text-[0.65rem] block uppercase tracking-wider">Última interação</span>
                                @php $waitColor = ['green' => '#059669', 'yellow' => '#ca8a04', 'orange' => '#ea580c', 'red' => '#dc2626'][$deal->waiting_urgency] ?? '#6b7280'; @endphp
                                <span class="mt-[2px]" style="color: {{ $waitColor }}; font-weight: 600;">
                                    {{ $deal->waiting_time_label === 'agora' ? 'Agora' : 'Há ' . $deal->waiting_time_label }}
                                </span>
                            </div>
                            {{-- Origem --}}
                            <div>
                                <span class="text-dg-500 font-semibold text-[0.65rem] block uppercase tracking-wider">Origem</span>
                                @if($deal->lead_source)
                                    <span class="inline-flex items-center gap-1 mt-[2px]" style="color: {{ $deal->lead_source->color() }}; font-weight: 600;">
                                        <svg class="w-[12px] h-[12px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $deal->lead_source->icon() }}"/>
                                        </svg>
                                        {{ $deal->lead_source->label() }}
                                    </span>
                                @else
                                    <span class="mt-[2px] text-[#444]">Não informada</span>
                                @endif
                            </div>
                            {{-- Temperatura --}}
                            <div>
                                @php
                                    $tempColors = ['hot' => '#ef4444', 'warm' => '#f59e0b', 'cold' => '#3b82f6'];
                                    $tempLabels = ['hot' => 'Quente', 'warm' => 'Morno', 'cold' => 'Frio'];
                                @endphp
                                <span class="text-dg-500 font-semibold text-[0.65rem] block uppercase tracking-wider">Temperatura</span>
                                <span class="inline-flex items-center gap-1 mt-[2px]">
                                    <span class="w-[8px] h-[8px] rounded-full" style="background: {{ $tempColors[$deal->temperature] ?? '#f59e0b' }};"></span>
                                    {{ $tempLabels[$deal->temperature] ?? 'Morno' }}
                                </span>
                            </div>
                            {{-- Criado em --}}
                            <div>
                                <span class="text-dg-500 font-semibold text-[0.65rem] block uppercase tracking-wider">Criado em</span>
                                <span class="mt-[2px]">{{ $deal->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        {{-- Próxima Ação (editável inline) --}}
                        <div class="mt-3" x-data="{
                            editingFollowup: false,
                            followupText: {{ json_encode($deal->next_action ?? '') }},
                            followupDate: '{{ $deal->next_action_at?->format('Y-m-d\TH:i') ?? '' }}',
                            saving: false,
                            async save() {
                                this.saving = true;
                                try {
                                    const res = await fetch('{{ route('crm.deals.followup', $deal) }}', {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({ next_action: this.followupText || null, next_action_at: this.followupDate || null }),
                                    });
                                    if (res.ok) { window.location.reload(); }
                                    else { alert('Erro ao salvar.'); }
                                } catch(e) { alert('Erro de conexão.'); }
                                finally { this.saving = false; }
                            }
                        }">
                            {{-- Modo visualização --}}
                            <div x-show="!editingFollowup">
                                @if($deal->next_action || $deal->next_action_at)
                                    <div class="py-2 px-2.5 rounded-lg" style="{{ $deal->is_followup_overdue ? 'background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);' : 'background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.15);' }}">
                                        <div class="flex items-center justify-between mb-0.5">
                                            <span class="text-[0.65rem] font-semibold" style="color: {{ $deal->is_followup_overdue ? '#dc2626' : '#3b82f6' }}; display: flex; align-items: center; gap: 0.25rem;">
                                                @if($deal->is_followup_overdue)
                                                    <svg class="w-[12px] h-[12px]" style="animation: pulse-followup 1.5s infinite;" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Follow-up atrasado!
                                                @else
                                                    Próximo Passo
                                                @endif
                                            </span>
                                            @if($deal->isOpen())
                                                <button class="bg-transparent border-0 cursor-pointer" @click="editingFollowup = true" type="button" title="Editar follow-up"
                                                        style="color: {{ $deal->is_followup_overdue ? '#dc2626' : '#3b82f6' }}; opacity: 0.6; transition: opacity 0.15s; padding: 0;"
                                                        onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                                                    <svg class="w-[12px] h-[12px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between">
                                            @if($deal->next_action)
                                                <span class="followup-text text-[0.8rem] text-dg-100 font-medium">{{ $deal->next_action }}</span>
                                            @endif
                                            @if($deal->next_action_at)
                                                <span class="text-[0.7rem] font-semibold" style="color: {{ $deal->is_followup_overdue ? '#dc2626' : '#666' }};">
                                                    {{ $deal->next_action_at->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    @if($deal->isOpen())
                                        <button class="w-full py-1.5 px-2.5 rounded-lg text-[0.7rem] text-dg-500 bg-transparent cursor-pointer transition-all flex items-center gap-1 justify-center hover:border-blue-500/30 hover:text-blue-300" @click="editingFollowup = true" type="button"
                                                style="border: 1px dashed rgba(255,255,255,0.08);">
                                            <svg class="w-[12px] h-[12px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Adicionar próximo passo / retorno
                                        </button>
                                    @endif
                                @endif
                            </div>

                            {{-- Modo edição --}}
                            <div class="py-2 px-2.5 rounded-lg bg-blue-500/[0.06] border border-blue-500/20" x-show="editingFollowup" x-transition>
                                <span class="text-[0.65rem] font-semibold text-blue-500 block mb-1.5">Editar Próximo Passo</span>
                                <textarea class="w-full p-1.5 border border-white/10 rounded text-[0.8rem] resize-y bg-surface text-dg-100 min-h-[40px]" x-model="followupText" rows="2" placeholder="Ex: Ligar para confirmar interesse, enviar orçamento..."
                                          @keydown.escape="editingFollowup = false"></textarea>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <div class="flex-1">
                                        <label class="text-[0.6rem] font-semibold text-dg-500 block mb-[2px]">Data do retorno</label>
                                        <input class="w-full py-[0.3rem] px-1.5 border border-white/10 rounded text-xs bg-surface text-dg-100" type="datetime-local" x-model="followupDate">
                                    </div>
                                    <div class="flex gap-1.5 self-end">
                                        <button class="py-[0.3rem] px-2 text-[0.7rem] border border-white/10 rounded bg-surface-raised text-dg-300 cursor-pointer" @click="editingFollowup = false" type="button">
                                            Cancelar
                                        </button>
                                        <button class="py-[0.3rem] px-2 text-[0.7rem] border-0 rounded bg-blue-500 text-white cursor-pointer font-semibold" @click="save()" :disabled="saving" type="button">
                                            <span x-show="!saving">Salvar</span>
                                            <span x-show="saving">Salvando...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($deal->description)
                            <div class="mt-3 pt-2.5 border-t border-border-subtle">
                                <span class="text-dg-500 font-semibold text-[0.65rem] block uppercase tracking-wider mb-1">Observações</span>
                                <p class="followup-text text-[0.8rem] text-dg-300 leading-normal m-0">{{ $deal->description }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Sinal / Reserva --}}
                    @if($deal->deposit_amount || $deal->stage->name === 'Reservado')
                        <div class="bg-surface-raised border border-amber-500/15 rounded-xl p-4"
                             x-data="{ editingDeposit: false, depositAmount: '{{ $deal->deposit_amount ?? '' }}', saving: false,
                                async save() {
                                    this.saving = true;
                                    try {
                                        const res = await fetch('{{ route('crm.deals.update', $deal) }}', {
                                            method: 'PUT',
                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                                            body: JSON.stringify({ title: '{{ addslashes($deal->title) }}', deposit_amount: this.depositAmount || null, deposit_paid_at: this.depositAmount ? new Date().toISOString() : null }),
                                        });
                                        if (res.ok) window.location.reload();
                                        else alert('Erro ao salvar.');
                                    } catch(e) { alert('Erro de conexão.'); }
                                    finally { this.saving = false; }
                                }
                             }">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-amber-500 font-semibold text-[0.65rem] uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-[12px] h-[12px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Sinal / Reserva
                                </span>
                                @if($deal->isOpen())
                                    <button class="text-[0.65rem] font-semibold text-amber-500 bg-transparent border-0 cursor-pointer py-[0.15rem] px-1.5 rounded hover:bg-amber-500/[0.08]" @click="editingDeposit = !editingDeposit" type="button">
                                        <span x-show="!editingDeposit">Editar</span>
                                        <span x-show="editingDeposit">Cancelar</span>
                                    </button>
                                @endif
                            </div>

                            <div x-show="!editingDeposit">
                                @if($deal->deposit_amount)
                                    <div class="flex items-center gap-2">
                                        <span class="text-base font-bold text-amber-500">
                                            R$ {{ number_format((float)$deal->deposit_amount, 2, ',', '.') }}
                                        </span>
                                        @if($deal->deposit_paid_at)
                                            <span class="text-[0.6rem] font-semibold py-[2px] px-[6px] rounded-[4px] bg-emerald-500/[0.12] text-emerald-300">
                                                Pago em {{ $deal->deposit_paid_at->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-dg-500">Nenhum sinal registrado</span>
                                @endif
                            </div>

                            <div class="mt-1" x-show="editingDeposit" x-transition>
                                <div class="flex items-end gap-1.5">
                                    <div class="flex-1">
                                        <label class="text-[0.6rem] font-semibold text-dg-500 block mb-[2px]">Valor do sinal (R$)</label>
                                        <input class="w-full p-1.5 border border-amber-500/20 rounded text-[0.8rem] bg-surface text-dg-100" type="number" x-model="depositAmount" step="0.01" placeholder="0,00">
                                    </div>
                                    <button class="py-1.5 px-2.5 text-[0.7rem] border-0 rounded bg-amber-500 text-dg-100 cursor-pointer font-semibold whitespace-nowrap" @click="save()" :disabled="saving" type="button">
                                        <span x-show="!saving">Salvar</span>
                                        <span x-show="saving">...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Interesses de Produto (CRUD) --}}
                    @include('crm.partials.product-interests', ['deal' => $deal])

                    {{-- Agendamentos vinculados --}}
                    @include('crm.partials.deal-appointments', ['deal' => $deal])

                    {{-- Ações Rápidas --}}
                    @if($deal->isOpen())
                        <div class="bg-surface-raised border border-border rounded-xl py-3 px-4">
                            <div class="flex gap-1.5 flex-wrap">
                                @if($deal->whatsapp_link)
                                    <a class="inline-flex items-center gap-[0.3rem] py-[0.3rem] px-2.5 bg-green-600 text-white text-[0.7rem] font-semibold rounded-md no-underline" href="{{ $deal->whatsapp_link }}" target="_blank">
                                        <svg class="w-[12px] h-[12px]" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        WhatsApp
                                    </a>
                                @endif
                                <form class="inline" method="POST" action="{{ route('crm.deals.win', $deal) }}">
                                    @csrf
                                    <button class="inline-flex items-center gap-[0.3rem] py-[0.3rem] px-2.5 bg-emerald-600 text-white text-[0.7rem] font-semibold rounded-md border-0 cursor-pointer" type="submit" onclick="return confirm('Marcar como ganho?')">
                                        ✓ Ganho
                                    </button>
                                </form>
                                <button class="inline-flex items-center gap-[0.3rem] py-[0.3rem] px-2.5 bg-red-600 text-white text-[0.7rem] font-semibold rounded-md border-0 cursor-pointer" @click="showLostForm = !showLostForm" type="button">
                                    ✕ Perdido
                                </button>
                                <button class="inline-flex items-center gap-[0.3rem] py-[0.3rem] px-2.5 bg-surface-overlay text-dg-300 text-[0.7rem] font-medium rounded-md border border-border cursor-pointer" @click="showEditForm = !showEditForm" type="button">
                                    ✏ Editar
                                </button>
                                <form class="inline" method="POST" action="{{ route('crm.deals.destroy', $deal) }}" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center gap-[0.3rem] py-[0.3rem] px-2.5 bg-red-500/[0.08] text-red-300 text-[0.7rem] font-medium rounded-md border border-red-500/15 cursor-pointer" type="submit">
                                        🗑 Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Form: Marcar como Perdido --}}
                    <div class="bg-red-500/[0.06] border border-red-500/15 rounded-xl p-4" x-show="showLostForm" x-transition>
                        <form method="POST" action="{{ route('crm.deals.lose', $deal) }}">
                            @csrf
                            <label class="text-[0.7rem] font-semibold text-red-300 block mb-1.5">Motivo da perda *</label>
                            <input class="w-full p-2" type="text" name="lost_reason" required minlength="3" placeholder="Ex: Preço alto, comprou na concorrência..."
                                   value="{{ old('lost_reason') }}"
                                   style="border: 1px solid {{ $errors->has('lost_reason') ? '#dc2626' : 'rgba(239,68,68,0.2)' }}; border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                            @error('lost_reason')
                                <p class="text-[0.7rem] text-red-300 mt-1 mx-0 mb-0">{{ $message }}</p>
                            @enderror
                            <div class="flex gap-2 justify-end mt-2">
                                <button class="py-[0.3rem] px-2.5 text-[0.7rem] border border-border rounded-md bg-surface-raised text-dg-300 cursor-pointer" @click.prevent="showLostForm = false" type="button">Cancelar</button>
                                <button class="py-[0.3rem] px-2.5 text-[0.7rem] bg-red-600 text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">Marcar como Perdido</button>
                            </div>
                        </form>
                    </div>

                    {{-- Form: Editar Deal --}}
                    <div class="bg-surface-raised border border-border rounded-xl p-4" x-show="showEditForm" x-transition>
                        <h3 class="text-[0.85rem] font-bold text-dg-100 mb-2">Editar Negócio</h3>
                        <form method="POST" action="{{ route('crm.deals.update', $deal) }}">
                            @csrf @method('PUT')
                            <div class="flex flex-col gap-2">
                                <div>
                                    <label class="text-[0.65rem] font-semibold text-dg-500">Título *</label>
                                    <input class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="text" name="title" required value="{{ $deal->title }}">
                                </div>
                                <div class="grid grid-cols-[1fr_1fr] gap-2">
                                    <div>
                                        <label class="text-[0.65rem] font-semibold text-dg-500">Produto</label>
                                        <input class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="text" name="product_interest" value="{{ $deal->product_interest }}">
                                    </div>
                                    <div>
                                        <label class="text-[0.65rem] font-semibold text-dg-500">Valor (R$)</label>
                                        <input class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="number" name="value" step="0.01" value="{{ $deal->value }}">
                                    </div>
                                </div>
                                <div class="grid grid-cols-[1fr_1fr] gap-2">
                                    <div>
                                        <label class="text-[0.65rem] font-semibold text-dg-500">Telefone</label>
                                        <input class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="text" name="phone" value="{{ $deal->phone }}">
                                    </div>
                                    <div>
                                        <label class="text-[0.65rem] font-semibold text-dg-500">Previsão</label>
                                        <input class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="date" name="expected_close_date" value="{{ $deal->expected_close_date?->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="grid grid-cols-[1fr_1fr] gap-2">
                                    <div>
                                        <label class="text-[0.65rem] font-semibold text-dg-500">Origem</label>
                                        <select class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" name="lead_source">
                                            <option value="">Não informada</option>
                                            @foreach(\App\Domain\CRM\Enums\LeadSource::cases() as $src)
                                                <option value="{{ $src->value }}" {{ $deal->lead_source === $src ? 'selected' : '' }}>{{ $src->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[0.65rem] font-semibold text-dg-500">Temperatura</label>
                                        <select class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" name="temperature">
                                            <option value="hot" {{ $deal->temperature === 'hot' ? 'selected' : '' }}>Quente</option>
                                            <option value="warm" {{ $deal->temperature === 'warm' ? 'selected' : '' }}>Morno</option>
                                            <option value="cold" {{ $deal->temperature === 'cold' ? 'selected' : '' }}>Frio</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-[1fr_1fr] gap-2">
                                    <div>
                                        <label class="text-[0.65rem] font-semibold text-dg-500">Próximo Passo</label>
                                        <input class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="text" name="next_action" value="{{ $deal->next_action }}" placeholder="Ex: Enviar orçamento">
                                    </div>
                                    <div>
                                        <label class="text-[0.65rem] font-semibold text-dg-500">Data Contato</label>
                                        <input class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="datetime-local" name="next_action_at" value="{{ $deal->next_action_at?->format('Y-m-d\TH:i') }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[0.65rem] font-semibold text-dg-500">Observações</label>
                                    <textarea class="w-full py-1.5 px-2 border border-border rounded-md text-[0.8rem] resize-y bg-surface text-dg-100" name="description" rows="2">{{ $deal->description }}</textarea>
                                </div>
                            </div>
                            <div class="flex gap-2 justify-end mt-2">
                                <button class="py-[0.3rem] px-2.5 text-[0.7rem] border border-border rounded-md bg-surface-raised text-dg-300 cursor-pointer" @click.prevent="showEditForm = false" type="button">Cancelar</button>
                                <button class="py-[0.3rem] px-2.5 text-[0.7rem] bg-blue-500 text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">Salvar</button>
                            </div>
                        </form>
                    </div>

                    {{-- IA + Atividades --}}
                    <div class="bg-surface-raised border border-border rounded-xl p-4">
                        <h3 class="text-[0.8rem] font-bold text-dg-100 mb-2 flex items-center gap-1.5">
                            <svg class="w-[16px] h-[16px] text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Assistente IA
                        </h3>
                        <div class="flex gap-1.5">
                            <button class="py-[0.3rem] px-2 bg-surface-overlay text-dg-300 text-[0.7rem] font-medium rounded-md border border-border cursor-pointer" @click="suggestMessage()" :disabled="aiLoading" type="button">
                                <span x-show="!aiLoading">💬 Sugerir Mensagem</span>
                                <span x-show="aiLoading">Gerando...</span>
                            </button>
                            <button class="py-[0.3rem] px-2 bg-surface-overlay text-dg-300 text-[0.7rem] font-medium rounded-md border border-border cursor-pointer" @click="analyzeDeal()" :disabled="aiLoading" type="button">
                                <span x-show="!aiLoading">📊 Analisar</span>
                                <span x-show="aiLoading">Analisando...</span>
                            </button>
                        </div>
                        <div class="mt-2 p-2.5 bg-violet-500/[0.06] border border-violet-500/15 rounded-md" x-show="aiResult" x-transition>
                            <p class="text-[0.8rem] text-violet-300 leading-normal whitespace-pre-line m-0" x-text="aiResult"></p>
                            <button class="mt-1.5 text-[0.65rem] text-violet-500 border-0 bg-transparent cursor-pointer underline" @click="copyAiResult()" type="button">Copiar</button>
                        </div>
                        <div class="mt-2 p-1.5 bg-red-500/[0.08] rounded-md text-[0.7rem] text-red-300" x-show="aiError" x-transition x-text="aiError"></div>
                    </div>

                    {{-- Registrar Atividade --}}
                    @if($deal->isOpen())
                        <div class="bg-surface-raised border border-border rounded-xl p-4">
                            <h3 class="text-[0.8rem] font-bold text-dg-100 mb-2">Registrar Atividade</h3>
                            <form method="POST" action="{{ route('crm.deals.activities.store', $deal) }}">
                                @csrf
                                <div class="flex gap-1.5 mb-1.5 flex-wrap">
                                    @foreach($activityTypes as $aType)
                                        <label class="inline-flex items-center gap-[0.2rem] py-1 px-2 border border-border rounded-md cursor-pointer text-[0.7rem] font-medium bg-surface">
                                            <input class="w-[12px] h-[12px]" type="radio" name="type" value="{{ $aType->value }}" {{ $loop->first ? 'checked' : '' }}>
                                            <svg class="w-[12px] h-[12px]" style="color: {{ $aType->color() }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $aType->icon() }}"/>
                                            </svg>
                                            {{ $aType->label() }}
                                        </label>
                                    @endforeach
                                </div>
                                <textarea class="w-full p-1.5 border border-border rounded-md text-[0.8rem] resize-y bg-surface text-dg-100 mb-1.5" name="description" required rows="2" placeholder="Descreva a interação..."></textarea>
                                <div class="text-right">
                                    <button class="py-[0.3rem] px-3 text-xs bg-blue-500 text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">Registrar</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- COLUNA DIREITA: Chat / Simulador / Timeline (full height) --}}
                <div class="flex flex-col min-h-0 bg-[#111] border border-border rounded-xl overflow-hidden" x-data="{ rightTab: 'chat' }">

                    {{-- Header com Avatar + Tabs --}}
                    <div class="shrink-0">
                        {{-- Customer Info Bar --}}
                        <div class="flex items-center gap-2 py-2.5 px-3 bg-surface-raised border-b border-border-subtle">
                            @if($deal->customer?->wa_avatar_url)
                                <img class="w-[32px] h-[32px] rounded-full object-cover border-2 border-[#25d366]" src="{{ $deal->customer->wa_avatar_url }}" alt="">
                            @else
                                <div class="w-[32px] h-[32px] rounded-full bg-surface-overlay flex items-center justify-center shrink-0">
                                    <svg class="w-[14px] h-[14px] text-[#444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-[0.85rem] text-dg-100 whitespace-nowrap overflow-hidden text-ellipsis">{{ $deal->customer?->name ?? $deal->title }}</div>
                                <div class="text-[0.65rem] text-dg-500">
                                    {{ $deal->phone }}
                                    @if($deal->customer?->wa_is_business)
                                        <span class="ml-1 font-semibold text-[#25d366]">● Business</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-[0.65rem] font-semibold py-[0.15rem] px-2 rounded" style="color: {{ $deal->stage->color }}; background: {{ $deal->stage->color }}12; border: 1px solid {{ $deal->stage->color }}30; white-space: nowrap;">
                                {{ $deal->stage->name }}
                            </span>
                        </div>

                        {{-- Tabs --}}
                        <div class="flex py-0 px-2 gap-1 bg-[#0f0f0f] border-b border-border">
                            <button class="py-2 px-3 text-xs font-semibold border-0 border-b-2 border-transparent cursor-pointer inline-flex items-center gap-1.5 transition-all leading-none" @click="rightTab = 'chat'"
                                    :style="rightTab === 'chat'
                                        ? 'color: #25D366; border-bottom: 2px solid #25D366; background: transparent;'
                                        : 'color: #555; border-bottom: 2px solid transparent; background: transparent;'"

                                    onmouseover="if(this.style.color==='rgb(85, 85, 85)')this.style.color='#999'" onmouseout="if(this.style.color==='rgb(153, 153, 153)')this.style.color='#555'">
                                <svg class="w-[14px] h-[14px] shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Chat
                                @if($deal->whatsappMessages->count() > 0)
                                    <span class="text-[0.55rem] min-w-[16px] text-center bg-[rgba(37,211,102,0.15)] text-[#25d366] py-[1px] px-[4px] rounded-[8px] font-bold leading-snug">{{ $deal->whatsappMessages->count() }}</span>
                                @endif
                            </button>
                            <button class="py-2 px-3 text-xs font-semibold border-0 border-b-2 border-transparent cursor-pointer inline-flex items-center gap-1.5 transition-all leading-none" @click="rightTab = 'simulator'"
                                    :style="rightTab === 'simulator'
                                        ? 'color: #8b5cf6; border-bottom: 2px solid #8b5cf6; background: transparent;'
                                        : 'color: #555; border-bottom: 2px solid transparent; background: transparent;'"

                                    onmouseover="if(this.style.color==='rgb(85, 85, 85)')this.style.color='#999'" onmouseout="if(this.style.color==='rgb(153, 153, 153)')this.style.color='#555'">
                                <svg class="w-[14px] h-[14px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Simulador
                                @if($allSnapshots->count() > 0)
                                    <span class="text-[0.55rem] min-w-[16px] text-center bg-violet-500/15 text-violet-500 py-[1px] px-[4px] rounded-[8px] font-bold leading-snug">{{ $allSnapshots->count() }}</span>
                                @endif
                            </button>
                            <button class="py-2 px-3 text-xs font-semibold border-0 border-b-2 border-transparent cursor-pointer inline-flex items-center gap-1.5 transition-all leading-none" @click="rightTab = 'timeline'"
                                    :style="rightTab === 'timeline'
                                        ? 'color: #3b82f6; border-bottom: 2px solid #3b82f6; background: transparent;'
                                        : 'color: #555; border-bottom: 2px solid transparent; background: transparent;'"

                                    onmouseover="if(this.style.color==='rgb(85, 85, 85)')this.style.color='#999'" onmouseout="if(this.style.color==='rgb(153, 153, 153)')this.style.color='#555'">
                                <svg class="w-[14px] h-[14px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Timeline
                                @if($deal->activities->count() > 0)
                                    <span class="text-[0.55rem] min-w-[16px] text-center bg-blue-500/15 text-blue-500 py-[1px] px-[4px] rounded-[8px] font-bold leading-snug">{{ $deal->activities->count() }}</span>
                                @endif
                            </button>
                        </div>
                    </div>

                    {{-- Tab Content (flex-grow fills remaining height) --}}
                    <div class="flex-1 min-h-0 flex flex-col">
                        {{-- Tab: Chat --}}
                        <div class="flex-1 flex flex-col min-h-0" x-show="rightTab === 'chat'">
                            @if($deal->phone)
                                @include('crm.partials.whatsapp-chat', ['deal' => $deal])
                            @else
                                <div class="flex-1 flex items-center justify-center py-12 px-4">
                                    <div class="text-center">
                                        <svg class="w-[40px] h-[40px] mt-0 mx-auto mb-2 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <p class="text-[0.8rem] text-[#444]">Sem telefone vinculado</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Tab: Simulador --}}
                        <div x-show="rightTab === 'simulator'" x-cloak class="custom-scroll flex-1 overflow-y-auto min-h-0">
                            @include('crm.partials.deal-simulator', ['deal' => $deal, 'allSnapshots' => $allSnapshots])
                        </div>

                        {{-- Tab: Timeline --}}
                        <div x-show="rightTab === 'timeline'" x-cloak class="custom-scroll flex-1 overflow-y-auto min-h-0">
                            <div class="p-3">
                                @forelse($deal->activities as $activity)
                                    <div class="flex gap-3 pb-2.5" style="{{ !$loop->last ? 'border-left: 2px solid #222; margin-left: 6px; padding-left: 1rem;' : 'margin-left: 6px; padding-left: 1rem;' }}; position: relative;">
                                        <div class="absolute left-[-5px] top-[2px] w-[10px] h-[10px] rounded-full" style="background: {{ $activity->type->color() }}; border: 2px solid #111;"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 mb-0.5">
                                                <span class="text-[0.7rem] font-semibold" style="color: {{ $activity->type->color() }};">{{ $activity->type->label() }}</span>
                                                <span class="text-[0.6rem] text-[#444]">{{ $activity->created_at->format('d/m H:i') }}</span>
                                            </div>
                                            @if($activity->description)
                                                <p class="text-[0.775rem] text-dg-300 leading-snug m-0">{{ $activity->description }}</p>
                                            @endif
                                            <span class="text-[0.6rem] text-[#444]">{{ $activity->user->name }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 px-4">
                                        <svg class="w-[32px] h-[32px] mt-0 mx-auto mb-2 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-[0.8rem] text-[#444]">Nenhuma atividade registrada</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .deal-page { padding: 0.75rem 0; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #555; }
        @keyframes pulse-followup { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    </style>

    <script>
    function dealPage() {
        return {
            showLostForm: {{ $errors->has('lost_reason') ? 'true' : 'false' }},
            showEditForm: false,
            aiLoading: false,
            aiResult: '',
            aiError: '',

            async suggestMessage() {
                this.aiLoading = true;
                this.aiResult = '';
                this.aiError = '';
                try {
                    const res = await fetch('{{ route("crm.deals.ai-message", $deal) }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    });
                    const data = await res.json();
                    this.aiResult = data.message || '';
                    this.aiError = data.message ? '' : (data.error || 'Erro ao gerar sugestão.');
                } catch (e) { this.aiError = 'Erro de conexão.'; }
                finally { this.aiLoading = false; }
            },

            async analyzeDeal() {
                this.aiLoading = true;
                this.aiResult = '';
                this.aiError = '';
                try {
                    const res = await fetch('{{ route("crm.deals.ai-analysis", $deal) }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    });
                    const data = await res.json();
                    this.aiResult = data.analysis || '';
                    this.aiError = data.analysis ? '' : (data.error || 'Erro ao analisar.');
                } catch (e) { this.aiError = 'Erro de conexão.'; }
                finally { this.aiLoading = false; }
            },

            copyAiResult() { navigator.clipboard.writeText(this.aiResult); },
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.followup-text').forEach(function(el) {
            el.innerHTML = el.textContent.replace(/~([^~]+)~/g, '<s style="opacity: 0.5;">$1</s>');
        });
    });
    </script>
</x-app-layout>
