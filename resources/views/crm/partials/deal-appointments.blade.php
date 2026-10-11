{{-- Seção: Agendamentos vinculados ao Deal --}}
@php
    $statusColors = [
        'scheduled' => ['bg' => 'rgba(59,130,246,0.08)', 'border' => 'rgba(59,130,246,0.2)', 'text' => '#93c5fd'],
        'confirmed' => ['bg' => 'rgba(16,185,129,0.08)', 'border' => 'rgba(16,185,129,0.2)', 'text' => '#6ee7b7'],
        'completed' => ['bg' => 'rgba(107,114,128,0.08)', 'border' => 'rgba(107,114,128,0.2)', 'text' => '#9ca3af'],
        'cancelled' => ['bg' => 'rgba(239,68,68,0.08)', 'border' => 'rgba(239,68,68,0.2)', 'text' => '#fca5a5'],
        'no_show'   => ['bg' => 'rgba(234,179,8,0.08)', 'border' => 'rgba(234,179,8,0.2)', 'text' => '#fde047'],
    ];
@endphp
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;"
     x-data="{ showAddAppointment: false }">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: #555; font-weight: 600; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.25rem;">
            <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Agendamentos
        </span>
        @if($deal->isOpen())
            <button @click="showAddAppointment = !showAddAppointment" type="button"
                    style="font-size: 0.65rem; font-weight: 600; color: #8b5cf6; background: none; border: none; cursor: pointer; padding: 0.15rem 0.375rem; border-radius: 0.25rem; transition: background 0.15s;"
                    onmouseover="this.style.background='rgba(139,92,246,0.08)'" onmouseout="this.style.background='none'">
                <span x-show="!showAddAppointment">+ Agendar</span>
                <span x-show="showAddAppointment">Cancelar</span>
            </button>
        @endif
    </div>

    {{-- Lista de agendamentos vinculados --}}
    @forelse($deal->appointments as $appt)
        @php
            $sc = $statusColors[$appt->status->value] ?? $statusColors['scheduled'];
            $isPast = $appt->date->lt(today());
        @endphp
        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: {{ $sc['bg'] }}; border: 1px solid {{ $sc['border'] }}; border-radius: 0.375rem; margin-bottom: 0.375rem; {{ $isPast && $appt->status->value === 'scheduled' ? 'opacity: 0.7;' : '' }}">
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <span style="font-size: 0.8rem; font-weight: 600; color: #e3e3e3;">
                        {{ $appt->date->format('d/m') }}
                    </span>
                    <span style="font-size: 0.75rem; color: #a4a4a4;">
                        {{ $appt->formatted_start_time }} - {{ $appt->formatted_end_time }}
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.125rem; flex-wrap: wrap;">
                    <span style="font-size: 0.65rem; font-weight: 600; color: {{ \App\Domain\Schedule\Models\Appointment::ATTENDANT_COLORS[$appt->attendant] ?? '#666' }};">
                        {{ $appt->attendant_name }}
                    </span>
                    @if($appt->service_description)
                        <span style="font-size: 0.6rem; color: #555; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 120px;">
                            {{ $appt->service_description }}
                        </span>
                    @endif
                </div>
            </div>
            <span style="font-size: 0.6rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap; background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border: 1px solid {{ $sc['border'] }};">
                {{ $appt->status->label() }}
            </span>
        </div>
    @empty
        <div x-show="!showAddAppointment" style="text-align: center; padding: 0.75rem; color: #444; font-size: 0.75rem;">
            Nenhum agendamento vinculado
        </div>
    @endforelse

    {{-- Formulario para criar agendamento --}}
    <div x-show="showAddAppointment" x-transition style="margin-top: 0.5rem; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(139,92,246,0.15); border-radius: 0.5rem;">
        <form method="POST" action="{{ route('crm.deals.appointments.store', $deal) }}">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <div>
                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Data <span style="color: #fca5a5;">*</span></label>
                    <input type="date" name="date" required value="{{ now()->format('Y-m-d') }}"
                           style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                </div>
                <div>
                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Atendente <span style="color: #fca5a5;">*</span></label>
                    <select name="attendant" required style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                        @foreach(\App\Domain\Schedule\Models\Appointment::ATTENDANTS as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Horario <span style="color: #fca5a5;">*</span></label>
                    <input type="time" name="start_time" required value="10:00"
                           style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                </div>
                <div>
                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Duracao <span style="color: #fca5a5;">*</span></label>
                    <select name="duration_minutes" required style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                        @foreach(\App\Domain\Schedule\Models\Appointment::DURATION_OPTIONS as $minutes => $label)
                            <option value="{{ $minutes }}" {{ $minutes === 60 ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-top: 0.5rem;">
                <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Descricao do servico</label>
                <input type="text" name="service_description" placeholder="Ex: Avaliacao de iPhone para troca"
                       style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.5rem;">
                <button @click.prevent="showAddAppointment = false" type="button"
                        style="padding: 0.3rem 0.625rem; font-size: 0.7rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; background: #141414; color: #a4a4a4; cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="padding: 0.3rem 0.625rem; font-size: 0.7rem; background: #8b5cf6; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                    Agendar
                </button>
            </div>
        </form>
    </div>
</div>
