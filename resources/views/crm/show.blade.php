<x-app-layout>
    <x-slot name="title">CRM - Negócio</x-slot>
    @php
        $activeStages = \App\Domain\CRM\Models\PipelineStage::where('is_won', false)->where('is_lost', false)->orderBy('position')->get();
    @endphp
    <div class="deal-page" x-data="dealPage()">
        <div style="padding: 0 1.5rem;">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); border-radius: 0.5rem; color: #6ee7b7; font-size: 0.85rem;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Top Bar: Breadcrumb + Deal Title + Status --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; padding: 0.5rem 0;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <a href="{{ route('crm.board') }}" style="display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.06); background: #141414; color: #a4a4a4; text-decoration: none; transition: all 0.15s;" onmouseover="this.style.background='#1a1a1a';this.style.color='#e3e3e3'" onmouseout="this.style.background='#141414';this.style.color='#a4a4a4'" title="Voltar ao Pipeline">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <div style="font-size: 0.65rem; color: #555; margin-bottom: 0.125rem;">
                            <a href="{{ route('crm.board') }}" style="color: #3b82f6; text-decoration: none;">Pipeline</a>
                            <span style="margin: 0 0.25rem;">/</span>
                            <span>{{ $deal->stage->name }}</span>
                        </div>
                        <h1 style="font-size: 1.1rem; font-weight: 700; color: #e3e3e3; margin: 0; line-height: 1.2;">{{ $deal->title }}</h1>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    @if($deal->value)
                        <span style="font-size: 1rem; font-weight: 700; color: #059669; background: rgba(5,150,105,0.08); padding: 0.25rem 0.75rem; border-radius: 0.375rem; border: 1px solid rgba(5,150,105,0.15);">
                            R$ {{ number_format((float)$deal->value, 2, ',', '.') }}
                        </span>
                    @endif
                    {{-- Pipeline Progress Mini --}}
                    <div style="display: flex; gap: 2px;">
                        @foreach($activeStages as $s)
                            @php $isCurrent = $deal->pipeline_stage_id === $s->id; $isPast = $s->position < $deal->stage->position; @endphp
                            <div style="width: 20px; height: 4px; border-radius: 2px; background: {{ $isCurrent ? $s->color : ($isPast ? $s->color . '80' : '#222') }};" title="{{ $s->name }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if(!$deal->isOpen())
                <div style="padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.75rem; {{ $deal->isWon() ? 'background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); color: #6ee7b7;' : 'background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5;' }}">
                    {{ $deal->isWon() ? 'Negócio GANHO em ' . $deal->won_at->format('d/m/Y H:i') : 'Negócio PERDIDO em ' . $deal->lost_at->format('d/m/Y H:i') }}
                    @if($deal->lost_reason) — Motivo: {{ $deal->lost_reason }} @endif
                    <form method="POST" action="{{ route('crm.deals.reopen', $deal) }}" style="display: inline; margin-left: 1rem;">
                        @csrf
                        <input type="hidden" name="pipeline_stage_id" value="{{ $stages->where('is_default', true)->first()?->id ?? $stages->first()?->id }}">
                        <button type="submit" style="font-size: 0.75rem; color: {{ $deal->isWon() ? '#6ee7b7' : '#fca5a5' }}; text-decoration: underline; border: none; background: none; cursor: pointer;">Reabrir</button>
                    </form>
                </div>
            @endif

            {{-- Layout Principal: 2 colunas --}}
            <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr); gap: 0.75rem; height: calc(100vh - 160px);">

                {{-- COLUNA ESQUERDA: Detalhes --}}
                <div style="display: flex; flex-direction: column; gap: 0.75rem; overflow-y: auto; padding-right: 0.25rem;" class="custom-scroll">

                    {{-- Card: Dados do Negócio --}}
                    <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem; font-size: 0.8rem;">
                            {{-- Etapa --}}
                            <div>
                                <span style="color: #555; font-weight: 600; font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.05em;">Etapa</span>
                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; margin-top: 2px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $deal->stage->color }};"></span>
                                    {{ $deal->stage->name }}
                                </span>
                            </div>
                            {{-- Vendedor --}}
                            <div>
                                <span style="color: #555; font-weight: 600; font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.05em;">Vendedor</span>
                                <span style="margin-top: 2px;">{{ $deal->user->name }}</span>
                            </div>
                            {{-- Cliente --}}
                            @if($deal->customer)
                                <div style="grid-column: 1 / -1;">
                                    <span style="color: #555; font-weight: 600; font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.05em;">Cliente</span>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 4px;">
                                        @if($deal->customer->wa_avatar_url)
                                            <img src="{{ $deal->customer->wa_avatar_url }}" alt="" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 2px solid #25D366;">
                                        @endif
                                        <div>
                                            <a href="{{ route('customers.show', $deal->customer) }}" style="color: #3b82f6; text-decoration: none; font-weight: 500;">{{ $deal->customer->name }}</a>
                                            @if($deal->customer->wa_is_business)
                                                <span style="font-size: 0.6rem; font-weight: 600; padding: 1px 5px; border-radius: 4px; background: rgba(37,211,102,0.12); color: #25D366; margin-left: 0.25rem;">
                                                    {{ $deal->customer->wa_business_name ?: 'Business' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($deal->customer->tags->isNotEmpty())
                                        <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-top: 0.375rem;">
                                            @foreach($deal->customer->tags as $ctag)
                                                <span style="font-size: 0.6rem; font-weight: 600; padding: 1px 6px; border-radius: 4px; background: {{ $ctag->color }}15; color: {{ $ctag->color }}; border: 1px solid {{ $ctag->color }}30;">
                                                    {{ $ctag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {{-- Última Interação --}}
                            <div>
                                <span style="color: #555; font-weight: 600; font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.05em;">Última interação</span>
                                @php $waitColor = ['green' => '#059669', 'yellow' => '#ca8a04', 'orange' => '#ea580c', 'red' => '#dc2626'][$deal->waiting_urgency] ?? '#6b7280'; @endphp
                                <span style="margin-top: 2px; color: {{ $waitColor }}; font-weight: 600;">
                                    {{ $deal->waiting_time_label === 'agora' ? 'Agora' : 'Há ' . $deal->waiting_time_label }}
                                </span>
                            </div>
                            {{-- Origem --}}
                            <div>
                                <span style="color: #555; font-weight: 600; font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.05em;">Origem</span>
                                @if($deal->lead_source)
                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; margin-top: 2px; color: {{ $deal->lead_source->color() }}; font-weight: 600;">
                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $deal->lead_source->icon() }}"/>
                                        </svg>
                                        {{ $deal->lead_source->label() }}
                                    </span>
                                @else
                                    <span style="margin-top: 2px; color: #444;">Não informada</span>
                                @endif
                            </div>
                            {{-- Temperatura --}}
                            <div>
                                @php
                                    $tempColors = ['hot' => '#ef4444', 'warm' => '#f59e0b', 'cold' => '#3b82f6'];
                                    $tempLabels = ['hot' => 'Quente', 'warm' => 'Morno', 'cold' => 'Frio'];
                                @endphp
                                <span style="color: #555; font-weight: 600; font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.05em;">Temperatura</span>
                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; margin-top: 2px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $tempColors[$deal->temperature] ?? '#f59e0b' }};"></span>
                                    {{ $tempLabels[$deal->temperature] ?? 'Morno' }}
                                </span>
                            </div>
                            {{-- Criado em --}}
                            <div>
                                <span style="color: #555; font-weight: 600; font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.05em;">Criado em</span>
                                <span style="margin-top: 2px;">{{ $deal->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        {{-- Próxima Ação (editável inline) --}}
                        <div x-data="{
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
                        }" style="margin-top: 0.75rem;">
                            {{-- Modo visualização --}}
                            <div x-show="!editingFollowup">
                                @if($deal->next_action || $deal->next_action_at)
                                    <div style="padding: 0.5rem 0.625rem; border-radius: 0.5rem; {{ $deal->is_followup_overdue ? 'background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);' : 'background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.15);' }}">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.125rem;">
                                            <span style="font-size: 0.65rem; font-weight: 600; color: {{ $deal->is_followup_overdue ? '#dc2626' : '#3b82f6' }}; display: flex; align-items: center; gap: 0.25rem;">
                                                @if($deal->is_followup_overdue)
                                                    <svg style="width: 12px; height: 12px; animation: pulse-followup 1.5s infinite;" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Follow-up atrasado!
                                                @else
                                                    Próximo Passo
                                                @endif
                                            </span>
                                            @if($deal->isOpen())
                                                <button @click="editingFollowup = true" type="button" title="Editar follow-up"
                                                        style="background: none; border: none; cursor: pointer; color: {{ $deal->is_followup_overdue ? '#dc2626' : '#3b82f6' }}; opacity: 0.6; transition: opacity 0.15s; padding: 0;"
                                                        onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                                                    <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            @if($deal->next_action)
                                                <span class="followup-text" style="font-size: 0.8rem; color: #e3e3e3; font-weight: 500;">{{ $deal->next_action }}</span>
                                            @endif
                                            @if($deal->next_action_at)
                                                <span style="font-size: 0.7rem; font-weight: 600; color: {{ $deal->is_followup_overdue ? '#dc2626' : '#666' }};">
                                                    {{ $deal->next_action_at->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    @if($deal->isOpen())
                                        <button @click="editingFollowup = true" type="button"
                                                style="width: 100%; padding: 0.375rem 0.625rem; border-radius: 0.5rem; font-size: 0.7rem; color: #555; background: none; border: 1px dashed rgba(255,255,255,0.08); cursor: pointer; transition: all 0.15s; display: flex; align-items: center; gap: 0.25rem; justify-content: center;"
                                                onmouseover="this.style.borderColor='rgba(59,130,246,0.3)'; this.style.color='#93c5fd'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'; this.style.color='#555'">
                                            <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Adicionar próximo passo / retorno
                                        </button>
                                    @endif
                                @endif
                            </div>

                            {{-- Modo edição --}}
                            <div x-show="editingFollowup" x-transition style="padding: 0.5rem 0.625rem; border-radius: 0.5rem; background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.2);">
                                <span style="font-size: 0.65rem; font-weight: 600; color: #3b82f6; display: block; margin-bottom: 0.375rem;">Editar Próximo Passo</span>
                                <textarea x-model="followupText" rows="2" placeholder="Ex: Ligar para confirmar interesse, enviar orçamento..."
                                          @keydown.escape="editingFollowup = false"
                                          style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.25rem; font-size: 0.8rem; resize: vertical; background: #0d0d0d; color: #e3e3e3; min-height: 40px;"></textarea>
                                <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem;">
                                    <div style="flex: 1;">
                                        <label style="font-size: 0.6rem; font-weight: 600; color: #555; display: block; margin-bottom: 2px;">Data do retorno</label>
                                        <input type="datetime-local" x-model="followupDate"
                                               style="width: 100%; padding: 0.3rem 0.375rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.25rem; font-size: 0.75rem; background: #0d0d0d; color: #e3e3e3;">
                                    </div>
                                    <div style="display: flex; gap: 0.375rem; align-self: flex-end;">
                                        <button @click="editingFollowup = false" type="button"
                                                style="padding: 0.3rem 0.5rem; font-size: 0.7rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.25rem; background: #141414; color: #a4a4a4; cursor: pointer;">
                                            Cancelar
                                        </button>
                                        <button @click="save()" :disabled="saving" type="button"
                                                style="padding: 0.3rem 0.5rem; font-size: 0.7rem; border: none; border-radius: 0.25rem; background: #3b82f6; color: white; cursor: pointer; font-weight: 600;">
                                            <span x-show="!saving">Salvar</span>
                                            <span x-show="saving">Salvando...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($deal->description)
                            <div style="margin-top: 0.75rem; padding-top: 0.625rem; border-top: 1px solid rgba(255,255,255,0.04);">
                                <span style="color: #555; font-weight: 600; font-size: 0.65rem; display: block; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Observações</span>
                                <p class="followup-text" style="font-size: 0.8rem; color: #a4a4a4; line-height: 1.5; margin: 0;">{{ $deal->description }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Sinal / Reserva --}}
                    @if($deal->deposit_amount || $deal->stage->name === 'Reservado')
                        <div style="background: #141414; border: 1px solid rgba(245,158,11,0.15); border-radius: 0.75rem; padding: 1rem;"
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
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.375rem;">
                                <span style="color: #f59e0b; font-weight: 600; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.25rem;">
                                    <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Sinal / Reserva
                                </span>
                                @if($deal->isOpen())
                                    <button @click="editingDeposit = !editingDeposit" type="button"
                                            style="font-size: 0.65rem; font-weight: 600; color: #f59e0b; background: none; border: none; cursor: pointer; padding: 0.15rem 0.375rem; border-radius: 0.25rem;"
                                            onmouseover="this.style.background='rgba(245,158,11,0.08)'" onmouseout="this.style.background='none'">
                                        <span x-show="!editingDeposit">Editar</span>
                                        <span x-show="editingDeposit">Cancelar</span>
                                    </button>
                                @endif
                            </div>

                            <div x-show="!editingDeposit">
                                @if($deal->deposit_amount)
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-size: 1rem; font-weight: 700; color: #f59e0b;">
                                            R$ {{ number_format((float)$deal->deposit_amount, 2, ',', '.') }}
                                        </span>
                                        @if($deal->deposit_paid_at)
                                            <span style="font-size: 0.6rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; background: rgba(16,185,129,0.12); color: #6ee7b7;">
                                                Pago em {{ $deal->deposit_paid_at->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span style="font-size: 0.75rem; color: #555;">Nenhum sinal registrado</span>
                                @endif
                            </div>

                            <div x-show="editingDeposit" x-transition style="margin-top: 0.25rem;">
                                <div style="display: flex; align-items: flex-end; gap: 0.375rem;">
                                    <div style="flex: 1;">
                                        <label style="font-size: 0.6rem; font-weight: 600; color: #555; display: block; margin-bottom: 2px;">Valor do sinal (R$)</label>
                                        <input type="number" x-model="depositAmount" step="0.01" placeholder="0,00"
                                               style="width: 100%; padding: 0.375rem; border: 1px solid rgba(245,158,11,0.2); border-radius: 0.25rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                    </div>
                                    <button @click="save()" :disabled="saving" type="button"
                                            style="padding: 0.375rem 0.625rem; font-size: 0.7rem; border: none; border-radius: 0.25rem; background: #f59e0b; color: #000; cursor: pointer; font-weight: 600; white-space: nowrap;">
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
                        <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.75rem 1rem;">
                            <div style="display: flex; gap: 0.375rem; flex-wrap: wrap;">
                                @if($deal->whatsapp_link)
                                    <a href="{{ $deal->whatsapp_link }}" target="_blank"
                                       style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.625rem; background: #16a34a; color: white; font-size: 0.7rem; font-weight: 600; border-radius: 0.375rem; text-decoration: none;">
                                        <svg style="width: 12px; height: 12px;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        WhatsApp
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('crm.deals.win', $deal) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Marcar como ganho?')"
                                            style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.625rem; background: #059669; color: white; font-size: 0.7rem; font-weight: 600; border-radius: 0.375rem; border: none; cursor: pointer;">
                                        ✓ Ganho
                                    </button>
                                </form>
                                <button @click="showLostForm = !showLostForm" type="button"
                                        style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.625rem; background: #dc2626; color: white; font-size: 0.7rem; font-weight: 600; border-radius: 0.375rem; border: none; cursor: pointer;">
                                    ✕ Perdido
                                </button>
                                <button @click="showEditForm = !showEditForm" type="button"
                                        style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.625rem; background: #1a1a1a; color: #a4a4a4; font-size: 0.7rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;">
                                    ✏ Editar
                                </button>
                                <form method="POST" action="{{ route('crm.deals.destroy', $deal) }}" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.625rem; background: rgba(239,68,68,0.08); color: #fca5a5; font-size: 0.7rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid rgba(239,68,68,0.15); cursor: pointer;">
                                        🗑 Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Form: Marcar como Perdido --}}
                    <div x-show="showLostForm" x-transition style="background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.15); border-radius: 0.75rem; padding: 1rem;">
                        <form method="POST" action="{{ route('crm.deals.lose', $deal) }}">
                            @csrf
                            <label style="font-size: 0.7rem; font-weight: 600; color: #fca5a5; display: block; margin-bottom: 0.375rem;">Motivo da perda *</label>
                            <input type="text" name="lost_reason" required minlength="3" placeholder="Ex: Preço alto, comprou na concorrência..."
                                   value="{{ old('lost_reason') }}"
                                   style="width: 100%; padding: 0.5rem; border: 1px solid {{ $errors->has('lost_reason') ? '#dc2626' : 'rgba(239,68,68,0.2)' }}; border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                            @error('lost_reason')
                                <p style="font-size: 0.7rem; color: #fca5a5; margin: 0.25rem 0 0;">{{ $message }}</p>
                            @enderror
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.5rem;">
                                <button @click.prevent="showLostForm = false" type="button" style="padding: 0.3rem 0.625rem; font-size: 0.7rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; background: #141414; color: #a4a4a4; cursor: pointer;">Cancelar</button>
                                <button type="submit" style="padding: 0.3rem 0.625rem; font-size: 0.7rem; background: #dc2626; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">Marcar como Perdido</button>
                            </div>
                        </form>
                    </div>

                    {{-- Form: Editar Deal --}}
                    <div x-show="showEditForm" x-transition style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                        <h3 style="font-size: 0.85rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.5rem;">Editar Negócio</h3>
                        <form method="POST" action="{{ route('crm.deals.update', $deal) }}">
                            @csrf @method('PUT')
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div>
                                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Título *</label>
                                    <input type="text" name="title" required value="{{ $deal->title }}" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Produto</label>
                                        <input type="text" name="product_interest" value="{{ $deal->product_interest }}" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Valor (R$)</label>
                                        <input type="number" name="value" step="0.01" value="{{ $deal->value }}" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Telefone</label>
                                        <input type="text" name="phone" value="{{ $deal->phone }}" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Previsão</label>
                                        <input type="date" name="expected_close_date" value="{{ $deal->expected_close_date?->format('Y-m-d') }}" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Origem</label>
                                        <select name="lead_source" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                            <option value="">Não informada</option>
                                            @foreach(\App\Domain\CRM\Enums\LeadSource::cases() as $src)
                                                <option value="{{ $src->value }}" {{ $deal->lead_source === $src ? 'selected' : '' }}>{{ $src->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Temperatura</label>
                                        <select name="temperature" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                            <option value="hot" {{ $deal->temperature === 'hot' ? 'selected' : '' }}>Quente</option>
                                            <option value="warm" {{ $deal->temperature === 'warm' ? 'selected' : '' }}>Morno</option>
                                            <option value="cold" {{ $deal->temperature === 'cold' ? 'selected' : '' }}>Frio</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Próximo Passo</label>
                                        <input type="text" name="next_action" value="{{ $deal->next_action }}" placeholder="Ex: Enviar orçamento" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Data Contato</label>
                                        <input type="datetime-local" name="next_action_at" value="{{ $deal->next_action_at?->format('Y-m-d\TH:i') }}" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                                    </div>
                                </div>
                                <div>
                                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Observações</label>
                                    <textarea name="description" rows="2" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; resize: vertical; background: #0d0d0d; color: #e3e3e3;">{{ $deal->description }}</textarea>
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.5rem;">
                                <button @click.prevent="showEditForm = false" type="button" style="padding: 0.3rem 0.625rem; font-size: 0.7rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; background: #141414; color: #a4a4a4; cursor: pointer;">Cancelar</button>
                                <button type="submit" style="padding: 0.3rem 0.625rem; font-size: 0.7rem; background: #3b82f6; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">Salvar</button>
                            </div>
                        </form>
                    </div>

                    {{-- IA + Atividades --}}
                    <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                        <h3 style="font-size: 0.8rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.375rem;">
                            <svg style="width: 16px; height: 16px; color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Assistente IA
                        </h3>
                        <div style="display: flex; gap: 0.375rem;">
                            <button @click="suggestMessage()" :disabled="aiLoading" type="button"
                                    style="padding: 0.3rem 0.5rem; background: #1a1a1a; color: #a4a4a4; font-size: 0.7rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;">
                                <span x-show="!aiLoading">💬 Sugerir Mensagem</span>
                                <span x-show="aiLoading">Gerando...</span>
                            </button>
                            <button @click="analyzeDeal()" :disabled="aiLoading" type="button"
                                    style="padding: 0.3rem 0.5rem; background: #1a1a1a; color: #a4a4a4; font-size: 0.7rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;">
                                <span x-show="!aiLoading">📊 Analisar</span>
                                <span x-show="aiLoading">Analisando...</span>
                            </button>
                        </div>
                        <div x-show="aiResult" x-transition style="margin-top: 0.5rem; padding: 0.625rem; background: rgba(139,92,246,0.06); border: 1px solid rgba(139,92,246,0.15); border-radius: 0.375rem;">
                            <p style="font-size: 0.8rem; color: #c4b5fd; line-height: 1.5; white-space: pre-line; margin: 0;" x-text="aiResult"></p>
                            <button @click="copyAiResult()" type="button" style="margin-top: 0.375rem; font-size: 0.65rem; color: #8b5cf6; border: none; background: none; cursor: pointer; text-decoration: underline;">Copiar</button>
                        </div>
                        <div x-show="aiError" x-transition style="margin-top: 0.5rem; padding: 0.375rem; background: rgba(239,68,68,0.08); border-radius: 0.375rem; font-size: 0.7rem; color: #fca5a5;" x-text="aiError"></div>
                    </div>

                    {{-- Registrar Atividade --}}
                    @if($deal->isOpen())
                        <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;">
                            <h3 style="font-size: 0.8rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.5rem;">Registrar Atividade</h3>
                            <form method="POST" action="{{ route('crm.deals.activities.store', $deal) }}">
                                @csrf
                                <div style="display: flex; gap: 0.375rem; margin-bottom: 0.375rem; flex-wrap: wrap;">
                                    @foreach($activityTypes as $aType)
                                        <label style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.25rem 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; cursor: pointer; font-size: 0.7rem; font-weight: 500; background: #0d0d0d;">
                                            <input type="radio" name="type" value="{{ $aType->value }}" {{ $loop->first ? 'checked' : '' }} style="width: 12px; height: 12px;">
                                            <svg style="width: 12px; height: 12px; color: {{ $aType->color() }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $aType->icon() }}"/>
                                            </svg>
                                            {{ $aType->label() }}
                                        </label>
                                    @endforeach
                                </div>
                                <textarea name="description" required rows="2" placeholder="Descreva a interação..."
                                          style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; resize: vertical; background: #0d0d0d; color: #e3e3e3; margin-bottom: 0.375rem;"></textarea>
                                <div style="text-align: right;">
                                    <button type="submit" style="padding: 0.3rem 0.75rem; font-size: 0.75rem; background: #3b82f6; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">Registrar</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- COLUNA DIREITA: Chat / Simulador / Timeline (full height) --}}
                <div x-data="{ rightTab: 'chat' }" style="display: flex; flex-direction: column; min-height: 0; background: #111; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">

                    {{-- Header com Avatar + Tabs --}}
                    <div style="flex-shrink: 0;">
                        {{-- Customer Info Bar --}}
                        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 0.75rem; background: #141414; border-bottom: 1px solid rgba(255,255,255,0.04);">
                            @if($deal->customer?->wa_avatar_url)
                                <img src="{{ $deal->customer->wa_avatar_url }}" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #25D366;">
                            @else
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #1a1a1a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg style="width: 14px; height: 14px; color: #444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            @endif
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.85rem; color: #e3e3e3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $deal->customer?->name ?? $deal->title }}</div>
                                <div style="font-size: 0.65rem; color: #555;">
                                    {{ $deal->phone }}
                                    @if($deal->customer?->wa_is_business)
                                        <span style="margin-left: 0.25rem; font-weight: 600; color: #25D366;">● Business</span>
                                    @endif
                                </div>
                            </div>
                            <span style="font-size: 0.65rem; font-weight: 600; padding: 0.15rem 0.5rem; border-radius: 0.25rem; color: {{ $deal->stage->color }}; background: {{ $deal->stage->color }}12; border: 1px solid {{ $deal->stage->color }}30; white-space: nowrap;">
                                {{ $deal->stage->name }}
                            </span>
                        </div>

                        {{-- Tabs --}}
                        <div style="display: flex; background: #111; border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <button @click="rightTab = 'chat'" :style="rightTab === 'chat' ? 'color: #25D366; border-bottom-color: #25D366; background: #0d0d0d;' : 'color: #666; border-bottom-color: transparent;'"
                                    style="flex: 1; padding: 0.625rem 0.5rem; font-size: 0.75rem; font-weight: 600; border: none; border-bottom: 2px solid transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.375rem; transition: all 0.15s; background: #111; line-height: 1;">
                                <svg style="width: 14px; height: 14px; flex-shrink: 0;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                <span>Chat</span>
                                @if($deal->whatsappMessages->count() > 0)
                                    <span style="font-size: 0.6rem; background: rgba(37,211,102,0.15); color: #25D366; padding: 1px 5px; border-radius: 6px; font-weight: 700; line-height: 1.3;">{{ $deal->whatsappMessages->count() }}</span>
                                @endif
                            </button>
                            <button @click="rightTab = 'simulator'" :style="rightTab === 'simulator' ? 'color: #8b5cf6; border-bottom-color: #8b5cf6; background: #0d0d0d;' : 'color: #666; border-bottom-color: transparent;'"
                                    style="flex: 1; padding: 0.625rem 0.5rem; font-size: 0.75rem; font-weight: 600; border: none; border-bottom: 2px solid transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.375rem; transition: all 0.15s; background: #111; line-height: 1;">
                                <svg style="width: 14px; height: 14px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span>Simulador</span>
                                @if($allSnapshots->count() > 0)
                                    <span style="font-size: 0.6rem; background: rgba(139,92,246,0.15); color: #8b5cf6; padding: 1px 5px; border-radius: 6px; font-weight: 700; line-height: 1.3;">{{ $allSnapshots->count() }}</span>
                                @endif
                            </button>
                            <button @click="rightTab = 'timeline'" :style="rightTab === 'timeline' ? 'color: #3b82f6; border-bottom-color: #3b82f6; background: #0d0d0d;' : 'color: #666; border-bottom-color: transparent;'"
                                    style="flex: 1; padding: 0.625rem 0.5rem; font-size: 0.75rem; font-weight: 600; border: none; border-bottom: 2px solid transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.375rem; transition: all 0.15s; background: #111; line-height: 1;">
                                <svg style="width: 14px; height: 14px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Timeline</span>
                                @if($deal->activities->count() > 0)
                                    <span style="font-size: 0.6rem; background: rgba(59,130,246,0.15); color: #3b82f6; padding: 1px 5px; border-radius: 6px; font-weight: 700; line-height: 1.3;">{{ $deal->activities->count() }}</span>
                                @endif
                            </button>
                        </div>
                    </div>

                    {{-- Tab Content (flex-grow fills remaining height) --}}
                    <div style="flex: 1; min-height: 0; overflow-y: auto;" class="custom-scroll">
                        {{-- Tab: Chat --}}
                        <div x-show="rightTab === 'chat'" x-cloak style="display: flex; flex-direction: column; height: 100%;">
                            @if($deal->phone)
                                @include('crm.partials.whatsapp-chat', ['deal' => $deal])
                            @else
                                <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 3rem 1rem;">
                                    <div style="text-align: center;">
                                        <svg style="width: 40px; height: 40px; margin: 0 auto 0.5rem; color: #222;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <p style="font-size: 0.8rem; color: #444;">Sem telefone vinculado</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Tab: Simulador --}}
                        <div x-show="rightTab === 'simulator'" x-cloak style="height: 100%;">
                            @include('crm.partials.deal-simulator', ['deal' => $deal, 'allSnapshots' => $allSnapshots])
                        </div>

                        {{-- Tab: Timeline --}}
                        <div x-show="rightTab === 'timeline'" x-cloak style="height: 100%;">
                            <div style="padding: 0.75rem;">
                                @forelse($deal->activities as $activity)
                                    <div style="display: flex; gap: 0.75rem; padding-bottom: 0.625rem; {{ !$loop->last ? 'border-left: 2px solid #222; margin-left: 6px; padding-left: 1rem;' : 'margin-left: 6px; padding-left: 1rem;' }}; position: relative;">
                                        <div style="position: absolute; left: -5px; top: 2px; width: 10px; height: 10px; border-radius: 50%; background: {{ $activity->type->color() }}; border: 2px solid #111;"></div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.125rem;">
                                                <span style="font-size: 0.7rem; font-weight: 600; color: {{ $activity->type->color() }};">{{ $activity->type->label() }}</span>
                                                <span style="font-size: 0.6rem; color: #444;">{{ $activity->created_at->format('d/m H:i') }}</span>
                                            </div>
                                            @if($activity->description)
                                                <p style="font-size: 0.775rem; color: #a4a4a4; line-height: 1.4; margin: 0;">{{ $activity->description }}</p>
                                            @endif
                                            <span style="font-size: 0.6rem; color: #444;">{{ $activity->user->name }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div style="text-align: center; padding: 2rem 1rem;">
                                        <svg style="width: 32px; height: 32px; margin: 0 auto 0.5rem; color: #222;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p style="font-size: 0.8rem; color: #444;">Nenhuma atividade registrada</p>
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
