<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" x-data="followupPage()">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #166534; font-size: 0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Header -->
            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Agenda de Follow-ups</h1>
                    <div style="display: flex; gap: 0.75rem; margin-top: 0.375rem;">
                        @if($counts['overdue'] > 0)
                            <span style="font-size: 0.75rem; font-weight: 600; color: #dc2626;">{{ $counts['overdue'] }} atrasado(s)</span>
                        @endif
                        @if($counts['today'] > 0)
                            <span style="font-size: 0.75rem; font-weight: 600; color: #d97706;">{{ $counts['today'] }} para hoje</span>
                        @endif
                        <span style="font-size: 0.75rem; color: #6b7280;">{{ $counts['pending'] }} pendente(s) total</span>
                    </div>
                </div>
                <button @click="showForm = !showForm" type="button"
                        style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.8rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Follow-up
                </button>
            </div>

            <!-- Formulario inline -->
            <div x-show="showForm" x-transition style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1rem;">
                <form method="POST" action="{{ route('followups.store') }}">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Tipo *</label>
                            <select name="type" required style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="grid-column: span 2 / span 2;">
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Titulo *</label>
                            <input type="text" name="title" required placeholder="Ex: Ligar para cliente sobre iPhone 17"
                                   style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Data *</label>
                            <input type="date" name="due_date" required value="{{ date('Y-m-d') }}"
                                   style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Telefone</label>
                            <input type="text" name="phone" placeholder="5511999999999"
                                   style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Descricao</label>
                            <input type="text" name="description" placeholder="Detalhes adicionais (opcional)"
                                   style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                        </div>
                    </div>
                    @if($errors->any())
                        <div style="margin-bottom: 0.75rem; color: #dc2626; font-size: 0.75rem;">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <button @click.prevent="showForm = false" type="button"
                                style="padding: 0.4rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; color: #6b7280; cursor: pointer;">
                            Cancelar
                        </button>
                        <button type="submit"
                                style="padding: 0.4rem 1rem; font-size: 0.8rem; background: #111827; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Filtros -->
            <div style="display: flex; gap: 0.375rem; margin-bottom: 1rem; overflow-x: auto; flex-wrap: wrap;">
                <a href="{{ route('followups.index', ['status' => 'pending']) }}"
                   style="padding: 0.375rem 0.875rem; font-size: 0.8rem; font-weight: 500; border-radius: 9999px; border: 1px solid {{ $currentStatus === 'pending' ? 'transparent' : '#e5e7eb' }}; background: {{ $currentStatus === 'pending' ? '#111827' : 'white' }}; color: {{ $currentStatus === 'pending' ? 'white' : '#6b7280' }}; text-decoration: none; white-space: nowrap;">
                    Pendentes ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('followups.index', ['status' => 'done']) }}"
                   style="padding: 0.375rem 0.875rem; font-size: 0.8rem; font-weight: 500; border-radius: 9999px; border: 1px solid {{ $currentStatus === 'done' ? 'transparent' : '#e5e7eb' }}; background: {{ $currentStatus === 'done' ? '#111827' : 'white' }}; color: {{ $currentStatus === 'done' ? 'white' : '#6b7280' }}; text-decoration: none; white-space: nowrap;">
                    Concluidos
                </a>
                <a href="{{ route('followups.index', ['status' => 'all']) }}"
                   style="padding: 0.375rem 0.875rem; font-size: 0.8rem; font-weight: 500; border-radius: 9999px; border: 1px solid {{ $currentStatus === 'all' ? 'transparent' : '#e5e7eb' }}; background: {{ $currentStatus === 'all' ? '#111827' : 'white' }}; color: {{ $currentStatus === 'all' ? 'white' : '#6b7280' }}; text-decoration: none; white-space: nowrap;">
                    Todos
                </a>
            </div>

            <!-- Lista -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @forelse($followups as $f)
                    @php
                        $isOverdue = $f->isPending() && $f->due_date->lt(today());
                        $isToday = $f->isPending() && $f->due_date->isToday();
                        $borderColor = $isOverdue ? '#fecaca' : ($isToday ? '#fde68a' : '#e5e7eb');
                        $bgColor = $isOverdue ? '#fef2f2' : ($isToday ? '#fffbeb' : 'white');
                    @endphp
                    <div style="background: {{ $bgColor }}; border: 1px solid {{ $borderColor }}; border-radius: 0.75rem; padding: 0.875rem 1.25rem;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                            <!-- Info -->
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.65rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: #f3f4f6; color: #374151;">{{ $f->type->label() }}</span>
                                    @if($isOverdue)
                                        <span style="font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 9999px; background: #dc2626; color: white;">ATRASADO</span>
                                    @elseif($isToday)
                                        <span style="font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 9999px; background: #d97706; color: white;">HOJE</span>
                                    @endif
                                    @if($f->status === \App\Domain\Followup\Enums\FollowupStatus::Done)
                                        <span style="font-size: 0.65rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: #dcfce7; color: #166534;">CONCLUIDO</span>
                                    @elseif($f->status === \App\Domain\Followup\Enums\FollowupStatus::Cancelled)
                                        <span style="font-size: 0.65rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: #f3f4f6; color: #6b7280;">CANCELADO</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-top: 0.25rem;">{{ $f->title }}</div>
                                @if($f->description)
                                    <div style="font-size: 0.8rem; color: #6b7280; margin-top: 0.125rem;">{{ $f->description }}</div>
                                @endif
                                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.375rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                    <span>{{ $f->due_date->format('d/m/Y') }}</span>
                                    @if($f->customer)
                                        <span>{{ $f->customer->name }}</span>
                                    @endif
                                    @if($f->completed_at)
                                        <span>Concluido em {{ $f->completed_at->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Acoes -->
                            @if($f->isPending())
                                <div style="display: flex; gap: 0.375rem; flex-shrink: 0; flex-wrap: wrap;">
                                    @if($f->phone)
                                        <a href="{{ $f->whatsapp_link }}" target="_blank"
                                           style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 0.375rem; background: #16a34a; color: white; text-decoration: none;"
                                           title="WhatsApp">
                                            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('followups.complete', $f) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" title="Concluir"
                                                style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 0.375rem; background: #059669; color: white; border: none; cursor: pointer;">
                                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('followups.cancel', $f) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" title="Cancelar"
                                                style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 0.375rem; background: #e5e7eb; color: #6b7280; border: none; cursor: pointer;">
                                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('followups.destroy', $f) }}" style="display: inline;" onsubmit="return confirm('Remover este follow-up?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Excluir"
                                                style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 0.375rem; background: #fef2f2; color: #dc2626; border: none; cursor: pointer;">
                                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2.5rem; color: #9ca3af; font-size: 0.875rem;">
                        <svg style="width: 40px; height: 40px; margin: 0 auto 0.75rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Nenhum follow-up {{ $currentStatus === 'pending' ? 'pendente' : ($currentStatus === 'done' ? 'concluido' : '') }}
                    </div>
                @endforelse
            </div>

            <!-- Paginacao -->
            @if($followups->hasPages())
                <div style="margin-top: 1rem;">
                    {{ $followups->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
    function followupPage() {
        return {
            showForm: {{ $errors->any() ? 'true' : 'false' }},
        };
    }
    </script>
</x-app-layout>
