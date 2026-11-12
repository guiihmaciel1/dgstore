{{--
    Agenda de hoje na home.
    Espera: $todayAppointments (Collection<Appointment>).
--}}
@php
    $nowTime = now()->format('H:i:s');
    $statusTone = [
        'scheduled' => 'text-blue-300 bg-blue-500/10',
        'confirmed' => 'text-emerald-300 bg-emerald-500/10',
        'completed' => 'text-dg-400 bg-surface-elevated',
        'cancelled' => 'text-red-300 bg-red-500/10',
        'no_show' => 'text-amber-300 bg-amber-500/10',
    ];
@endphp

<section class="flex flex-col rounded-xl border border-border bg-surface-raised" aria-labelledby="agenda-title">
    <div class="flex items-baseline justify-between gap-3 px-4 pt-4 sm:px-5">
        <h2 id="agenda-title" class="text-base font-semibold text-dg-100">Agenda de hoje</h2>
        <a href="{{ route('schedule.index') }}" class="text-xs font-medium text-dg-400 underline-offset-4 hover:text-dg-100 hover:underline">Ver agenda</a>
    </div>

    @if($todayAppointments->isEmpty())
        <div class="px-4 pb-5 pt-3 sm:px-5">
            <p class="text-sm text-dg-400">Nenhum atendimento marcado para hoje.</p>
            <a href="{{ route('schedule.index') }}" class="mt-2 inline-block text-xs font-medium text-indigo-300 underline-offset-4 hover:underline">Agendar atendimento</a>
        </div>
    @else
        <ol class="mt-2 px-4 pb-3 sm:px-5">
            @foreach($todayAppointments as $appt)
                @php
                    $isNow = $appt->start_time <= $nowTime && $appt->end_time > $nowTime;
                    $isPast = $appt->end_time <= $nowTime && $appt->status->value !== 'completed';
                    $tone = $statusTone[$appt->status->value] ?? $statusTone['scheduled'];
                    $attendantTone = ['danilo' => 'text-blue-300', 'guilherme' => 'text-violet-300', 'sophia' => 'text-pink-300'][$appt->attendant] ?? 'text-dg-300';
                @endphp
                <li class="flex items-center gap-4 border-t border-border py-2.5 first:border-t-0 {{ $isPast ? 'opacity-55' : '' }}">
                    <div class="w-14 flex-shrink-0">
                        <span class="block text-sm font-semibold tabular-nums {{ $isNow ? 'text-indigo-300' : 'text-dg-200' }}">{{ substr($appt->start_time, 0, 5) }}</span>
                        <span class="block text-[0.6875rem] tabular-nums text-dg-400">até {{ substr($appt->end_time, 0, 5) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="flex items-center gap-2">
                            @if($isNow)
                                <span class="h-2 w-2 flex-shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                <span class="sr-only">Agora:</span>
                            @endif
                            <span class="truncate text-sm font-medium text-dg-100">{{ $appt->customer_name }}</span>
                        </span>
                        <span class="block truncate text-[0.6875rem] text-dg-400">
                            <span class="{{ $attendantTone }}">{{ $appt->attendant_name }}</span>
                            @if($appt->service_description) · {{ $appt->service_description }} @endif
                        </span>
                    </div>
                    <span class="flex-shrink-0 rounded-full px-2 py-0.5 text-[0.6875rem] font-medium {{ $tone }}">{{ $appt->status->label() }}</span>
                </li>
            @endforeach
        </ol>
    @endif
</section>
