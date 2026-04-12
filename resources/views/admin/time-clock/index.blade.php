<x-app-layout>
    <x-slot name="title">Registro de Ponto</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Registro de Ponto</h1>
                    <p class="text-sm text-gray-500">Acompanhe o ponto dos estagiários</p>
                </div>
            </div>

            {{-- Status Hoje --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($todayStatus as $status)
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 2rem; height: 2rem; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <svg style="width: 1rem; height: 1rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span style="font-weight: 700; color: #111827;">{{ $status['user']->name }}</span>
                            </div>
                            <span style="font-size: 0.7rem; font-weight: 700; padding: 0.25rem 0.625rem; border-radius: 9999px; color: white; background: {{ $status['status_color'] }};">
                                {{ $status['status_label'] }}
                            </span>
                        </div>

                        @if($status['clock_in_time'])
                            <p style="font-size: 0.8rem; color: #6b7280;">
                                Chegou às <strong style="color: #111827;">{{ $status['clock_in_time'] }}</strong>
                            </p>
                        @endif

                        @if($status['entries']->count() > 0)
                            <div style="display: flex; gap: 0.375rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                @foreach($status['entries'] as $entry)
                                    <span style="font-size: 0.65rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #f3f4f6; color: #374151; font-weight: 500;">
                                        {{ \App\Domain\TimeClock\Models\TimeClockEntry::LABELS[$entry->type] }}:
                                        <strong>{{ $entry->punched_at->format('H:i') }}</strong>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Filtros --}}
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('admin.time-clock.index') }}" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <label style="font-size: 0.75rem; color: #6b7280; display: block; margin-bottom: 0.25rem;">Estagiário</label>
                        <select name="user_id" style="padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; min-width: 180px;">
                            <option value="">Todos</option>
                            @foreach($interns as $intern)
                                <option value="{{ $intern->id }}" {{ $selectedUserId === $intern->id ? 'selected' : '' }}>{{ $intern->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" style="padding: 0.5rem 1rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; margin-top: 1.1rem;">
                        Filtrar
                    </button>
                </form>
            </div>

            {{-- Histórico por Dia --}}
            @forelse($groupedByDate as $dateStr => $dayEntries)
                @php $dateObj = \Carbon\Carbon::parse($dateStr); @endphp
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1rem;">
                    <div style="padding: 0.625rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 0.875rem; font-weight: 700; color: #111827;">
                            {{ $dateObj->translatedFormat('l, d/m/Y') }}
                            @if($dateObj->isToday())
                                <span style="font-size: 0.7rem; font-weight: 600; color: #2563eb; margin-left: 0.25rem;">HOJE</span>
                            @endif
                        </h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.5rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Estagiário</th>
                                    <th style="padding: 0.5rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Tipo</th>
                                    <th style="padding: 0.5rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Horário</th>
                                    <th style="padding: 0.5rem 1rem; text-align: left; font-weight: 600; color: #6b7280;">Obs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dayEntries->sortBy('punched_at') as $entry)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 0.5rem 1rem; font-weight: 600; color: #111827;">{{ $entry->user->name }}</td>
                                        <td style="padding: 0.5rem 1rem;">
                                            @php
                                                $typeColors = [
                                                    'clock_in' => '#059669',
                                                    'lunch_out' => '#d97706',
                                                    'lunch_in' => '#2563eb',
                                                    'clock_out' => '#7c3aed',
                                                ];
                                            @endphp
                                            <span style="font-size: 0.7rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px; color: white; background: {{ $typeColors[$entry->type] ?? '#6b7280' }};">
                                                {{ $entry->getTypeLabel() }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.5rem 1rem; font-weight: 700; color: #111827; font-variant-numeric: tabular-nums;">{{ $entry->punched_at->format('H:i:s') }}</td>
                                        <td style="padding: 0.5rem 1rem; color: #6b7280;">{{ $entry->notes ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 3rem; text-align: center; color: #6b7280; font-size: 0.875rem;">
                    Nenhum registro de ponto encontrado.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
