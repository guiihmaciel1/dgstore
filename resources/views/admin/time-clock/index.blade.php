<x-app-layout>
    <x-slot name="title">Registro de Ponto</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Registro de Ponto</h1>
                    <p class="text-sm text-dg-500">Acompanhe o ponto das vendedoras</p>
                </div>
            </div>

            {{-- Status Hoje --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($todayStatus as $status)
                    <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 2rem; height: 2rem; background: #222222; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <svg style="width: 1rem; height: 1rem; color: #818181;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span style="font-weight: 700; color: #e3e3e3;">{{ $status['user']->name }}</span>
                            </div>
                            <span style="font-size: 0.7rem; font-weight: 700; padding: 0.25rem 0.625rem; border-radius: 9999px; color: white; background: {{ $status['status_color'] }};">
                                {{ $status['status_label'] }}
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            @if($status['clock_in_time'])
                                <p style="font-size: 0.8rem; color: #818181;">
                                    Chegou às <strong style="color: #e3e3e3;">{{ $status['clock_in_time'] }}</strong>
                                </p>
                            @else
                                <p style="font-size: 0.8rem; color: #818181;">—</p>
                            @endif

                            @if($status['worked_minutes'] > 0)
                                @php
                                    $h = intdiv($status['worked_minutes'], 60);
                                    $m = $status['worked_minutes'] % 60;
                                    $isOngoing = $status['next_type'] !== null && $status['entries']->isNotEmpty();
                                @endphp
                                <span style="font-size: 0.8rem; font-weight: 700; color: {{ $status['worked_minutes'] >= 480 ? '#059669' : '#e3e3e3' }}; font-variant-numeric: tabular-nums;">
                                    {{ $h }}h{{ str_pad((string)$m, 2, '0', STR_PAD_LEFT) }}
                                    @if($isOngoing)
                                        <span style="font-size: 0.6rem; color: #2563eb; font-weight: 600;">ao vivo</span>
                                    @endif
                                </span>
                            @endif
                        </div>

                        @if($status['entries']->count() > 0)
                            <div style="display: flex; gap: 0.375rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                @foreach($status['entries'] as $entry)
                                    <span style="font-size: 0.65rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #222222; color: #a4a4a4; font-weight: 500;">
                                        {{ \App\Domain\TimeClock\Models\TimeClockEntry::LABELS[$entry->type] }}:
                                        <strong>{{ $entry->punched_at->format('H:i') }}</strong>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Resumo Semanal --}}
            @if(count($weeklySummary) > 0)
            <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; color: #818181;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;">Resumo da Semana</h3>
                    <span style="font-size: 0.7rem; color: #666; margin-left: auto;">
                        {{ now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('d/m') }} — {{ now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d/m') }}
                    </span>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #818181;">Vendedora</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Dias</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Trabalhado</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Esperado</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Saldo</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Média/dia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weeklySummary as $ws)
                                @php
                                    $wH = intdiv($ws['total_worked_minutes'], 60);
                                    $wM = $ws['total_worked_minutes'] % 60;
                                    $eH = intdiv($ws['expected_minutes'], 60);
                                    $eM = $ws['expected_minutes'] % 60;
                                    $bAbs = abs($ws['balance_minutes']);
                                    $bH = intdiv($bAbs, 60);
                                    $bM = $bAbs % 60;
                                    $bSign = $ws['balance_minutes'] >= 0 ? '+' : '-';
                                    $bColor = $ws['balance_minutes'] >= 0 ? '#059669' : '#ef4444';
                                    $aH = intdiv($ws['avg_minutes_per_day'], 60);
                                    $aM = $ws['avg_minutes_per_day'] % 60;
                                    $avgColor = $ws['avg_minutes_per_day'] >= 480 ? '#059669' : ($ws['avg_minutes_per_day'] >= 420 ? '#d97706' : '#ef4444');
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                    <td style="padding: 0.625rem 1rem; font-weight: 700; color: #e3e3e3;">{{ $ws['user']->name }}</td>
                                    <td style="padding: 0.625rem 1rem; text-align: center; color: #a4a4a4; font-variant-numeric: tabular-nums;">{{ $ws['days_worked'] }}</td>
                                    <td style="padding: 0.625rem 1rem; text-align: center; font-weight: 700; color: #e3e3e3; font-variant-numeric: tabular-nums;">{{ $wH }}h{{ str_pad((string)$wM, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td style="padding: 0.625rem 1rem; text-align: center; color: #818181; font-variant-numeric: tabular-nums;">{{ $eH }}h{{ str_pad((string)$eM, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td style="padding: 0.625rem 1rem; text-align: center; font-weight: 700; color: {{ $bColor }}; font-variant-numeric: tabular-nums;">
                                        {{ $bSign }}{{ $bH }}h{{ str_pad((string)$bM, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td style="padding: 0.625rem 1rem; text-align: center; font-variant-numeric: tabular-nums;">
                                        <span style="font-weight: 700; color: {{ $avgColor }};">{{ $aH }}h{{ str_pad((string)$aM, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span style="font-size: 0.65rem; color: #666;">/dia</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Inatividade --}}
            @if(count($idleSummary) > 0 && collect($idleSummary)->sum('week_idle_count') > 0)
            <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;">⏸ Tempo Inativo</h3>
                    <span style="font-size: 0.7rem; color: #666; margin-left: auto;">
                        {{ now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('d/m') }} — {{ now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d/m') }}
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #818181;">Vendedora</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Hoje</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Semana</th>
                                <th style="padding: 0.625rem 1rem; text-align: center; font-weight: 600; color: #818181;">Ocorrências</th>
                                <th style="padding: 0.625rem 1rem; text-align: left; font-weight: 600; color: #818181;">Registros Hoje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($idleSummary as $is)
                                @php
                                    $todayColor = $is['today_idle_minutes'] >= 60 ? '#ef4444' : ($is['today_idle_minutes'] >= 30 ? '#d97706' : '#059669');
                                    $weekColor = $is['week_idle_minutes'] >= 120 ? '#ef4444' : ($is['week_idle_minutes'] >= 60 ? '#d97706' : '#059669');
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                    <td style="padding: 0.625rem 1rem; font-weight: 700; color: #e3e3e3;">{{ $is['user']->name }}</td>
                                    <td style="padding: 0.625rem 1rem; text-align: center;">
                                        <span style="font-weight: 700; color: {{ $todayColor }}; font-variant-numeric: tabular-nums;">
                                            {{ $is['today_idle_minutes'] }}min
                                        </span>
                                    </td>
                                    <td style="padding: 0.625rem 1rem; text-align: center;">
                                        <span style="font-weight: 700; color: {{ $weekColor }}; font-variant-numeric: tabular-nums;">
                                            @php
                                                $wH = intdiv($is['week_idle_minutes'], 60);
                                                $wM = $is['week_idle_minutes'] % 60;
                                            @endphp
                                            @if($wH > 0)
                                                {{ $wH }}h{{ str_pad((string)$wM, 2, '0', STR_PAD_LEFT) }}
                                            @else
                                                {{ $wM }}min
                                            @endif
                                        </span>
                                    </td>
                                    <td style="padding: 0.625rem 1rem; text-align: center; color: #a4a4a4; font-variant-numeric: tabular-nums;">
                                        {{ $is['week_idle_count'] }}×
                                    </td>
                                    <td style="padding: 0.625rem 1rem;">
                                        @if($is['today_logs']->count() > 0)
                                            <div style="display: flex; gap: 0.375rem; flex-wrap: wrap;">
                                                @foreach($is['today_logs'] as $log)
                                                    <span style="
                                                        font-size: 0.65rem;
                                                        padding: 0.125rem 0.5rem;
                                                        border-radius: 9999px;
                                                        background: {{ $log->idle_minutes >= 15 ? 'rgba(239,68,68,0.15)' : '#222222' }};
                                                        color: {{ $log->idle_minutes >= 15 ? '#ef4444' : '#a4a4a4' }};
                                                        font-weight: 500;
                                                    ">
                                                        {{ $log->started_at->format('H:i') }}→{{ $log->ended_at?->format('H:i') ?? '...' }}
                                                        ({{ $log->idle_minutes }}min)
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="font-size: 0.75rem; color: #666;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Filtros --}}
            <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('admin.time-clock.index') }}" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <label style="font-size: 0.75rem; color: #818181; display: block; margin-bottom: 0.25rem;">Vendedora</label>
                        <select name="user_id" style="padding: 0.5rem 0.75rem; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; min-width: 180px;">
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
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden; margin-bottom: 1rem;">
                    <div style="padding: 0.625rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a; display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;">
                            {{ $dateObj->translatedFormat('l, d/m/Y') }}
                            @if($dateObj->isToday())
                                <span style="font-size: 0.7rem; font-weight: 600; color: #2563eb; margin-left: 0.25rem;">HOJE</span>
                            @endif
                        </h3>
                        {{-- Resumo do dia --}}
                        @if(isset($dailyHours[$dateStr]))
                            <div style="display: flex; gap: 0.75rem;">
                                @foreach($dailyHours[$dateStr] as $userId => $dh)
                                    @php
                                        $userName = $dayEntries->firstWhere('user_id', $userId)?->user?->name ?? '?';
                                        $dhH = intdiv($dh['worked_minutes'], 60);
                                        $dhM = $dh['worked_minutes'] % 60;
                                        $dhBal = $dh['balance_minutes'];
                                        $dhBAbs = abs($dhBal);
                                        $dhBH = intdiv($dhBAbs, 60);
                                        $dhBM = $dhBAbs % 60;
                                        $dhSign = $dhBal >= 0 ? '+' : '-';
                                        $dhColor = $dhBal >= 0 ? '#059669' : '#ef4444';
                                    @endphp
                                    <span style="font-size: 0.7rem; color: #a4a4a4; font-variant-numeric: tabular-nums;">
                                        {{ $userName }}:
                                        <strong style="color: #e3e3e3;">{{ $dhH }}h{{ str_pad((string)$dhM, 2, '0', STR_PAD_LEFT) }}</strong>
                                        <span style="color: {{ $dhColor }}; font-weight: 700;">({{ $dhSign }}{{ $dhBH }}h{{ str_pad((string)$dhBM, 2, '0', STR_PAD_LEFT) }})</span>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th style="padding: 0.5rem 1rem; text-align: left; font-weight: 600; color: #818181;">Vendedora</th>
                                    <th style="padding: 0.5rem 1rem; text-align: left; font-weight: 600; color: #818181;">Tipo</th>
                                    <th style="padding: 0.5rem 1rem; text-align: left; font-weight: 600; color: #818181;">Horário</th>
                                    <th style="padding: 0.5rem 1rem; text-align: left; font-weight: 600; color: #818181;">Obs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dayEntries->sortBy('punched_at') as $entry)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td style="padding: 0.5rem 1rem; font-weight: 600; color: #e3e3e3;">{{ $entry->user->name }}</td>
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
                                        <td style="padding: 0.5rem 1rem; font-weight: 700; color: #e3e3e3; font-variant-numeric: tabular-nums;">{{ $entry->punched_at->format('H:i:s') }}</td>
                                        <td style="padding: 0.5rem 1rem; color: #818181;">{{ $entry->notes ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); padding: 3rem; text-align: center; color: #818181; font-size: 0.875rem;">
                    Nenhum registro de ponto encontrado.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
