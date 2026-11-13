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
<div class="bg-surface-raised border border-border rounded-xl p-4"
     x-data="{ showAddAppointment: false }">
    <div class="flex items-center justify-between mb-2">
        <span class="text-dg-500 font-semibold text-[0.65rem] uppercase tracking-wider flex items-center gap-1">
            <svg class="w-[12px] h-[12px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Agendamentos
        </span>
        @if($deal->isOpen())
            <button class="text-[0.65rem] font-semibold text-violet-500 bg-transparent border-0 cursor-pointer py-[0.15rem] px-1.5 rounded transition-colors hover:bg-violet-500/[0.08]" @click="showAddAppointment = !showAddAppointment" type="button">
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
        <div class="flex items-center gap-2 p-2" style="background: {{ $sc['bg'] }}; border: 1px solid {{ $sc['border'] }}; border-radius: 0.375rem; margin-bottom: 0.375rem; {{ $isPast && $appt->status->value === 'scheduled' ? 'opacity: 0.7;' : '' }}">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="text-[0.8rem] font-semibold text-dg-100">
                        {{ $appt->date->format('d/m') }}
                    </span>
                    <span class="text-xs text-dg-300">
                        {{ $appt->formatted_start_time }} - {{ $appt->formatted_end_time }}
                    </span>
                </div>
                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                    <span class="text-[0.65rem] font-semibold" style="color: {{ \App\Domain\Schedule\Models\Appointment::ATTENDANT_COLORS[$appt->attendant] ?? '#666' }};">
                        {{ $appt->attendant_name }}
                    </span>
                    @if($appt->service_description)
                        <span class="text-[0.6rem] text-dg-500 overflow-hidden text-ellipsis whitespace-nowrap max-w-[120px]">
                            {{ $appt->service_description }}
                        </span>
                    @endif
                </div>
            </div>
            <span class="text-[0.6rem] font-bold py-[2px] px-[6px] rounded-[4px] whitespace-nowrap" style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border: 1px solid {{ $sc['border'] }};">
                {{ $appt->status->label() }}
            </span>
        </div>
    @empty
        <div class="text-center p-3 text-[#444] text-xs" x-show="!showAddAppointment">
            Nenhum agendamento vinculado
        </div>
    @endforelse

    {{-- Formulario para criar agendamento --}}
    <div class="mt-2 p-3 bg-surface-overlay border border-violet-500/15 rounded-lg" x-show="showAddAppointment" x-transition>
        <form method="POST" action="{{ route('crm.deals.appointments.store', $deal) }}">
            @csrf
            <div class="grid grid-cols-[1fr_1fr] gap-2">
                <div>
                    <label class="text-[0.65rem] font-semibold text-dg-500">Data <span class="text-red-300">*</span></label>
                    <input class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="date" name="date" required value="{{ now()->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="text-[0.65rem] font-semibold text-dg-500">Atendente <span class="text-red-300">*</span></label>
                    <select class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" name="attendant" required>
                        @foreach(\App\Domain\Schedule\Models\Appointment::ATTENDANTS as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[0.65rem] font-semibold text-dg-500">Horario <span class="text-red-300">*</span></label>
                    <input class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="time" name="start_time" required value="10:00">
                </div>
                <div>
                    <label class="text-[0.65rem] font-semibold text-dg-500">Duracao <span class="text-red-300">*</span></label>
                    <select class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" name="duration_minutes" required>
                        @foreach(\App\Domain\Schedule\Models\Appointment::DURATION_OPTIONS as $minutes => $label)
                            <option value="{{ $minutes }}" {{ $minutes === 60 ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <label class="text-[0.65rem] font-semibold text-dg-500">Descricao do servico</label>
                <input class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="text" name="service_description" placeholder="Ex: Avaliacao de iPhone para troca">
            </div>
            <div class="flex gap-2 justify-end mt-2">
                <button class="py-[0.3rem] px-2.5 text-[0.7rem] border border-border rounded-md bg-surface-raised text-dg-300 cursor-pointer" @click.prevent="showAddAppointment = false" type="button">
                    Cancelar
                </button>
                <button class="py-[0.3rem] px-2.5 text-[0.7rem] bg-violet-500 text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">
                    Agendar
                </button>
            </div>
        </form>
    </div>
</div>
