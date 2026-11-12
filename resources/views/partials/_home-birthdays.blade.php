{{--
    Aniversariantes da home.
    Espera: $birthdayCustomers (DashboardController::getBirthdayCustomers) — itens com
    customer, date, days_until, age, can_send, greeting (só para os de hoje).
--}}
@php
    $birthdaysToday = $birthdayCustomers->where('days_until', 0)->values();
    $upcomingByDay = $birthdayCustomers->where('days_until', '>', 0)->groupBy(fn ($b) => $b['date']->toDateString());
    $upcomingCount = $birthdayCustomers->where('days_until', '>', 0)->count();
    $visibleDays = 4;@endphp

<section class="flex flex-col rounded-xl border border-border bg-surface-raised" aria-labelledby="birthdays-title"
         x-data="{ showAll: false }">
    <div class="flex items-baseline justify-between gap-3 px-4 pt-4 sm:px-5">
        <h2 id="birthdays-title" class="text-base font-semibold text-dg-100">Aniversariantes</h2>
        <span class="text-xs text-dg-400">próximos 14 dias</span>
    </div>

    @if($birthdayCustomers->isEmpty())
        <p class="px-4 pb-5 pt-3 text-sm text-dg-400 sm:px-5">Nenhum cliente faz aniversário nos próximos 14 dias.</p>
    @else
        {{-- Hoje --}}
        @if($birthdaysToday->isNotEmpty())
            <div class="mx-3 mt-3 rounded-lg bg-pink-500/10 p-1 sm:mx-4">
                <p class="px-2.5 pb-1 pt-1.5 text-xs font-semibold text-pink-300">
                    Hoje · {{ $birthdaysToday->count() }} {{ $birthdaysToday->count() === 1 ? 'aniversariante' : 'aniversariantes' }}
                </p>
                <ul>
                    @foreach($birthdaysToday as $b)
                        <li class="flex items-center gap-3 rounded-md px-2.5 py-2">
                            <a href="{{ route('customers.show', $b['customer']) }}" class="min-w-0 flex-1 rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-pink-300">
                                <span class="block truncate text-sm font-medium text-dg-50 hover:underline hover:underline-offset-4">{{ $b['customer']->name }}</span>
                                <span class="block text-[0.6875rem] text-pink-200/70">
                                    @if($b['age']) Faz {{ $b['age'] }} anos @endif
                                    @if($b['age'] && $b['customer']->phone) · @endif
                                    {{ $b['customer']->formatted_phone }}
                                </span>
                            </a>
                            @php $greeting = $b['greeting']; @endphp
                            @if($greeting?->isSent())
                                <span class="inline-flex flex-shrink-0 items-center gap-1.5 text-xs font-medium text-emerald-300"
                                      title="Enviado pelo WhatsApp{{ $greeting->sender ? ' por ' . $greeting->sender->name : '' }}">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Parabéns enviado {{ $greeting->sent_at?->format('H:i') }}
                                </span>
                            @elseif($b['can_send'])
                                <form method="POST" action="{{ route('customers.birthday-greeting', $b['customer']) }}"
                                      class="flex flex-shrink-0 flex-col items-end gap-0.5"
                                      x-data="{ busy: false }" @submit="busy = true">
                                    @csrf
                                    <button type="submit" :disabled="busy"
                                            class="inline-flex items-center gap-1.5 rounded-md bg-emerald-500/15 px-2.5 py-1.5 text-xs font-medium text-emerald-300 transition-colors hover:bg-emerald-500/25 hover:text-emerald-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-emerald-300 disabled:cursor-wait disabled:opacity-60">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span x-text="busy ? 'Enviando…' : '{{ $greeting ? 'Tentar de novo' : 'Parabenizar' }}'">{{ $greeting ? 'Tentar de novo' : 'Parabenizar' }}</span>
                                    </button>
                                    @if($greeting)
                                        <span class="text-[0.625rem] text-red-300" title="{{ $greeting->error }}">O envio falhou</span>
                                    @endif
                                </form>
                            @else
                                <span class="flex-shrink-0 text-[0.6875rem] text-dg-400">Sem celular válido</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Próximos dias --}}
        @if($upcomingByDay->isNotEmpty())
            <ol class="mt-2 flex-1 px-4 pb-2 sm:px-5">
                @foreach($upcomingByDay as $day => $people)
                    @php $date = $people->first()['date']; $daysUntil = $people->first()['days_until']; @endphp
                    <li class="flex gap-4 border-t border-border py-2.5 first:border-t-0"
                        @if($loop->index >= $visibleDays) x-show="showAll" x-cloak @endif>
                        <div class="w-14 flex-shrink-0 pt-0.5">
                            <span class="block text-sm font-semibold tabular-nums text-dg-200">{{ $date->format('d/m') }}</span>
                            <span class="block text-[0.6875rem] capitalize text-dg-400">
                                {{ $daysUntil === 1 ? 'amanhã' : $date->translatedFormat('D') }}
                            </span>
                        </div>
                        <ul class="min-w-0 flex-1 space-y-1">
                            @foreach($people as $b)
                                <li class="flex items-center justify-between gap-2">
                                    <a href="{{ route('customers.show', $b['customer']) }}" class="min-w-0 truncate text-sm text-dg-300 underline-offset-4 hover:text-dg-100 hover:underline">
                                        {{ $b['customer']->name }}
                                    </a>
                                    @if($b['age'])
                                        <span class="flex-shrink-0 text-[0.6875rem] tabular-nums text-dg-400">{{ $b['age'] }} anos</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ol>

            @if($upcomingByDay->count() > $visibleDays)
                <button type="button" @click="showAll = !showAll"
                        class="mx-4 mb-4 self-start rounded-md text-xs font-medium text-dg-400 underline-offset-4 hover:text-dg-100 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400 sm:mx-5"
                        x-text="showAll ? 'Mostrar menos' : 'Ver todos os {{ $upcomingCount }} próximos'"></button>
            @endif
        @endif
    @endif
</section>
