<x-app-layout>
    <div class="py-6" x-data="dealPage()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #166534; font-size: 0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Breadcrumb --}}
            <div style="margin-bottom: 1rem; font-size: 0.8rem; color: #6b7280;">
                <a href="{{ route('crm.board') }}" style="color: #3b82f6; text-decoration: none;">Pipeline</a>
                <span style="margin: 0 0.375rem;">/</span>
                <span>{{ $deal->title }}</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; {{ !$deal->isOpen() ? '' : '' }}">
                @if(!$deal->isOpen())
                    <div style="padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; {{ $deal->isWon() ? 'background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;' : 'background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;' }}">
                        {{ $deal->isWon() ? 'Negócio GANHO em ' . $deal->won_at->format('d/m/Y H:i') : 'Negócio PERDIDO em ' . $deal->lost_at->format('d/m/Y H:i') }}
                        @if($deal->lost_reason)
                            — Motivo: {{ $deal->lost_reason }}
                        @endif
                        <form method="POST" action="{{ route('crm.deals.reopen', $deal) }}" style="display: inline; margin-left: 1rem;">
                            @csrf
                            <input type="hidden" name="pipeline_stage_id" value="{{ $stages->where('is_default', true)->first()?->id ?? $stages->first()?->id }}">
                            <button type="submit" style="font-size: 0.75rem; color: {{ $deal->isWon() ? '#166534' : '#991b1b' }}; text-decoration: underline; border: none; background: none; cursor: pointer;">
                                Reabrir
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Layout em 2 colunas --}}
                <div style="display: grid; grid-template-columns: 1fr 380px; gap: 1rem;">

                    {{-- Coluna Esquerda: Info + Ações --}}
                    <div style="display: flex; flex-direction: column; gap: 1rem;">

                        {{-- Card: Dados do Negócio --}}
                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                                <div>
                                    @if($deal->product_interest)
                                        <span style="font-size: 0.7rem; font-weight: 600; color: #374151; background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">
                                            {{ $deal->product_interest }}
                                        </span>
                                    @endif
                                    <h1 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $deal->title }}</h1>
                                </div>
                                @if($deal->value)
                                    <span style="font-size: 1.125rem; font-weight: 700; color: #059669;">
                                        R$ {{ number_format((float)$deal->value, 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; font-size: 0.8rem;">
                                <div>
                                    <span style="color: #6b7280; font-weight: 600; font-size: 0.7rem; display: block;">Etapa</span>
                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; margin-top: 2px;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $deal->stage->color }};"></span>
                                        {{ $deal->stage->name }}
                                    </span>
                                </div>
                                <div>
                                    <span style="color: #6b7280; font-weight: 600; font-size: 0.7rem; display: block;">Vendedor</span>
                                    <span style="margin-top: 2px;">{{ $deal->user->name }}</span>
                                </div>
                                @if($deal->customer)
                                    <div>
                                        <span style="color: #6b7280; font-weight: 600; font-size: 0.7rem; display: block;">Cliente</span>
                                        <a href="{{ route('customers.show', $deal->customer) }}" style="color: #3b82f6; text-decoration: none; margin-top: 2px; display: block;">
                                            {{ $deal->customer->name }}
                                        </a>
                                    </div>
                                @endif
                                @if($deal->expected_close_date)
                                    <div>
                                        <span style="color: #6b7280; font-weight: 600; font-size: 0.7rem; display: block;">Previsão</span>
                                        <span style="margin-top: 2px; {{ $deal->isOverdue() ? 'color: #dc2626; font-weight: 600;' : '' }}">
                                            {{ $deal->expected_close_date->format('d/m/Y') }}
                                            @if($deal->isOverdue())
                                                (atrasado)
                                            @endif
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <span style="color: #6b7280; font-weight: 600; font-size: 0.7rem; display: block;">Criado em</span>
                                    <span style="margin-top: 2px;">{{ $deal->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span style="color: #6b7280; font-weight: 600; font-size: 0.7rem; display: block;">Última atividade</span>
                                    <span style="margin-top: 2px;">{{ $deal->days_since_last_activity === 0 ? 'Hoje' : $deal->days_since_last_activity . ' dia(s) atrás' }}</span>
                                </div>
                            </div>

                            @if($deal->description)
                                <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #f3f4f6;">
                                    <span style="color: #6b7280; font-weight: 600; font-size: 0.7rem; display: block; margin-bottom: 0.25rem;">Observações</span>
                                    <p style="font-size: 0.8rem; color: #374151; line-height: 1.5;">{{ $deal->description }}</p>
                                </div>
                            @endif

                            {{-- Ações rápidas --}}
                            @if($deal->isOpen())
                                <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #f3f4f6; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    @if($deal->whatsapp_link)
                                        <a href="{{ $deal->whatsapp_link }}" target="_blank"
                                           style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #16a34a; color: white; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; text-decoration: none;">
                                            <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            WhatsApp
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ route('crm.deals.win', $deal) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Marcar como ganho?')"
                                                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #059669; color: white; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; border: none; cursor: pointer;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Ganho
                                        </button>
                                    </form>

                                    <button @click="showLostForm = !showLostForm" type="button"
                                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #dc2626; color: white; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; border: none; cursor: pointer;">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Perdido
                                    </button>

                                    <button @click="showEditForm = !showEditForm" type="button"
                                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid #e5e7eb; cursor: pointer;">
                                        Editar
                                    </button>

                                    <form method="POST" action="{{ route('crm.deals.destroy', $deal) }}" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #fef2f2; color: #dc2626; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid #fecaca; cursor: pointer;">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        {{-- Form: Marcar como perdido --}}
                        <div x-show="showLostForm" x-transition style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem;">
                            <form method="POST" action="{{ route('crm.deals.lose', $deal) }}">
                                @csrf
                                <label style="font-size: 0.75rem; font-weight: 600; color: #991b1b; display: block; margin-bottom: 0.375rem;">Motivo da perda</label>
                                <input type="text" name="lost_reason" placeholder="Ex: Preço alto, comprou na concorrência, desistiu..."
                                       style="width: 100%; padding: 0.5rem; border: 1px solid #fecaca; border-radius: 0.375rem; font-size: 0.8rem; margin-bottom: 0.5rem;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button @click.prevent="showLostForm = false" type="button" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; cursor: pointer;">Cancelar</button>
                                    <button type="submit" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; background: #dc2626; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">Marcar como Perdido</button>
                                </div>
                            </form>
                        </div>

                        {{-- Form: Editar Deal --}}
                        <div x-show="showEditForm" x-transition style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Editar Negócio</h3>
                            <form method="POST" action="{{ route('crm.deals.update', $deal) }}">
                                @csrf
                                @method('PUT')
                                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    <div>
                                        <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280;">Título *</label>
                                        <input type="text" name="title" required value="{{ $deal->title }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                        <div>
                                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280;">Produto</label>
                                            <input type="text" name="product_interest" value="{{ $deal->product_interest }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                        </div>
                                        <div>
                                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280;">Valor (R$)</label>
                                            <input type="number" name="value" step="0.01" value="{{ $deal->value }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                        </div>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                        <div>
                                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280;">Telefone</label>
                                            <input type="text" name="phone" value="{{ $deal->phone }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                        </div>
                                        <div>
                                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280;">Previsão</label>
                                            <input type="date" name="expected_close_date" value="{{ $deal->expected_close_date?->format('Y-m-d') }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                        </div>
                                    </div>
                                    <div>
                                        <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280;">Observações</label>
                                        <textarea name="description" rows="2" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; resize: vertical;">{{ $deal->description }}</textarea>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.75rem;">
                                    <button @click.prevent="showEditForm = false" type="button" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; cursor: pointer;">Cancelar</button>
                                    <button type="submit" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; background: #111827; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">Salvar</button>
                                </div>
                            </form>
                        </div>

                        {{-- IA Section --}}
                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.375rem;">
                                <svg style="width: 18px; height: 18px; color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                Assistente IA
                            </h3>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <button @click="suggestMessage()" :disabled="aiLoading" type="button"
                                        style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid #e5e7eb; cursor: pointer;">
                                    <span x-show="!aiLoading">Sugerir Mensagem WhatsApp</span>
                                    <span x-show="aiLoading">Gerando...</span>
                                </button>
                                <button @click="analyzeDeal()" :disabled="aiLoading" type="button"
                                        style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid #e5e7eb; cursor: pointer;">
                                    <span x-show="!aiLoading">Analisar Negócio</span>
                                    <span x-show="aiLoading">Analisando...</span>
                                </button>
                            </div>
                            <div x-show="aiResult" x-transition style="margin-top: 0.75rem; padding: 0.75rem; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 0.375rem;">
                                <p style="font-size: 0.8rem; color: #374151; line-height: 1.5; white-space: pre-line;" x-text="aiResult"></p>
                                <button @click="copyAiResult()" type="button" style="margin-top: 0.5rem; font-size: 0.7rem; color: #8b5cf6; border: none; background: none; cursor: pointer; text-decoration: underline;">
                                    Copiar texto
                                </button>
                            </div>
                            <div x-show="aiError" x-transition style="margin-top: 0.75rem; padding: 0.5rem; background: #fef2f2; border-radius: 0.375rem; font-size: 0.75rem; color: #dc2626;" x-text="aiError"></div>
                        </div>

                        {{-- Registrar Atividade --}}
                        @if($deal->isOpen())
                            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem;">
                                <h3 style="font-size: 0.9rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Registrar Atividade</h3>
                                <form method="POST" action="{{ route('crm.deals.activities.store', $deal) }}">
                                    @csrf
                                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        @foreach($activityTypes as $aType)
                                            <label style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; cursor: pointer; font-size: 0.75rem; font-weight: 500;">
                                                <input type="radio" name="type" value="{{ $aType->value }}" {{ $loop->first ? 'checked' : '' }}
                                                       style="width: 14px; height: 14px;">
                                                <svg style="width: 14px; height: 14px; color: {{ $aType->color() }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $aType->icon() }}"/>
                                                </svg>
                                                {{ $aType->label() }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <textarea name="description" required rows="2" placeholder="Descreva a interação..."
                                              style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; resize: vertical; margin-bottom: 0.5rem;"></textarea>
                                    <div style="text-align: right;">
                                        <button type="submit" style="padding: 0.375rem 1rem; font-size: 0.8rem; background: #111827; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                                            Registrar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>

                    {{-- Coluna Direita: Timeline --}}
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        {{-- Progress do Pipeline --}}
                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem;">
                            <h3 style="font-size: 0.8rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">PIPELINE</h3>
                            <div style="display: flex; gap: 2px;">
                                @foreach($stages->where('is_won', false)->where('is_lost', false) as $s)
                                    @php
                                        $isCurrent = $deal->pipeline_stage_id === $s->id;
                                        $isPast = $s->position < $deal->stage->position;
                                    @endphp
                                    <div style="flex: 1; height: 6px; border-radius: 3px; background: {{ $isCurrent ? $s->color : ($isPast ? $s->color . '80' : '#e5e7eb') }};"
                                         title="{{ $s->name }}"></div>
                                @endforeach
                            </div>
                            <div style="font-size: 0.7rem; color: #6b7280; margin-top: 0.375rem;">
                                Etapa atual: <strong style="color: {{ $deal->stage->color }};">{{ $deal->stage->name }}</strong>
                            </div>
                        </div>

                        {{-- Timeline de Atividades --}}
                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem;">
                            <h3 style="font-size: 0.8rem; font-weight: 700; color: #6b7280; margin-bottom: 0.75rem;">TIMELINE</h3>
                            <div style="display: flex; flex-direction: column;">
                                @forelse($deal->activities as $activity)
                                    <div style="display: flex; gap: 0.75rem; padding-bottom: 0.75rem; {{ !$loop->last ? 'border-left: 2px solid #e5e7eb; margin-left: 9px; padding-left: 1.25rem;' : 'margin-left: 9px; padding-left: 1.25rem;' }}; position: relative;">
                                        <div style="position: absolute; left: -7px; top: 2px; width: 12px; height: 12px; border-radius: 50%; background: {{ $activity->type->color() }}; border: 2px solid white;"></div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.125rem;">
                                                <span style="font-size: 0.7rem; font-weight: 600; color: {{ $activity->type->color() }};">{{ $activity->type->label() }}</span>
                                                <span style="font-size: 0.65rem; color: #9ca3af;">{{ $activity->created_at->format('d/m H:i') }}</span>
                                            </div>
                                            @if($activity->description)
                                                <p style="font-size: 0.775rem; color: #374151; line-height: 1.4; margin: 0;">{{ $activity->description }}</p>
                                            @endif
                                            <span style="font-size: 0.625rem; color: #9ca3af;">{{ $activity->user->name }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p style="font-size: 0.8rem; color: #9ca3af; text-align: center; padding: 1rem 0;">Nenhuma atividade registrada.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function dealPage() {
        return {
            showLostForm: false,
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
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    if (data.message) {
                        this.aiResult = data.message;
                    } else {
                        this.aiError = data.error || 'Erro ao gerar sugestão.';
                    }
                } catch (e) {
                    this.aiError = 'Erro de conexão.';
                } finally {
                    this.aiLoading = false;
                }
            },

            async analyzeDeal() {
                this.aiLoading = true;
                this.aiResult = '';
                this.aiError = '';

                try {
                    const res = await fetch('{{ route("crm.deals.ai-analysis", $deal) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    if (data.analysis) {
                        this.aiResult = data.analysis;
                    } else {
                        this.aiError = data.error || 'Erro ao analisar.';
                    }
                } catch (e) {
                    this.aiError = 'Erro de conexão.';
                } finally {
                    this.aiLoading = false;
                }
            },

            copyAiResult() {
                navigator.clipboard.writeText(this.aiResult);
            },
        };
    }
    </script>
</x-app-layout>
