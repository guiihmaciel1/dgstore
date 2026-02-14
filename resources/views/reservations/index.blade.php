<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Reservas de Produtos</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Gerencie reservas e sinais de clientes</p>
                </div>
                <a href="{{ route('reservations.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova Reserva
                </a>
            </div>

            <!-- Cards de Estatísticas -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="padding: 1.25rem; background: {{ $stats['active'] > 0 ? '#f0fdf4' : 'white' }}; border-radius: 0.75rem; border: 1px solid {{ $stats['active'] > 0 ? '#bbf7d0' : '#e5e7eb' }};">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.625rem; background: {{ $stats['active'] > 0 ? '#dcfce7' : '#f3f4f6' }}; border-radius: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: {{ $stats['active'] > 0 ? '#16a34a' : '#6b7280' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 1.5rem; font-weight: 700; color: {{ $stats['active'] > 0 ? '#16a34a' : '#111827' }};">{{ $stats['active'] }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280;">Reservas ativas</p>
                        </div>
                    </div>
                </div>

                <div style="padding: 1.25rem; background: {{ $stats['expiring_soon'] > 0 ? '#fefce8' : 'white' }}; border-radius: 0.75rem; border: 1px solid {{ $stats['expiring_soon'] > 0 ? '#fde68a' : '#e5e7eb' }};">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.625rem; background: {{ $stats['expiring_soon'] > 0 ? '#fef3c7' : '#f3f4f6' }}; border-radius: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: {{ $stats['expiring_soon'] > 0 ? '#d97706' : '#6b7280' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 1.5rem; font-weight: 700; color: {{ $stats['expiring_soon'] > 0 ? '#d97706' : '#111827' }};">{{ $stats['expiring_soon'] }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280;">Vencendo em 3 dias</p>
                        </div>
                    </div>
                </div>

                <div style="padding: 1.25rem; background: {{ $stats['overdue'] > 0 ? '#fef2f2' : 'white' }}; border-radius: 0.75rem; border: 1px solid {{ $stats['overdue'] > 0 ? '#fecaca' : '#e5e7eb' }};">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.625rem; background: {{ $stats['overdue'] > 0 ? '#fee2e2' : '#f3f4f6' }}; border-radius: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: {{ $stats['overdue'] > 0 ? '#dc2626' : '#6b7280' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 1.5rem; font-weight: 700; color: {{ $stats['overdue'] > 0 ? '#dc2626' : '#111827' }};">{{ $stats['overdue'] }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280;">Vencidas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem; margin-bottom: 1rem;">
                <form method="GET" x-data x-ref="filterForm" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Buscar</label>
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Número, cliente ou produto..."
                               style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;"
                               x-on:input.debounce.400ms="$refs.filterForm.submit()">
                    </div>
                    <div style="min-width: 150px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                                x-on:change="$refs.filterForm.submit()">
                            <option value="">Todos</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" {{ $filters['status'] === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($filters['search'] || $filters['status'])
                        <a href="{{ route('reservations.index') }}" style="padding: 0.5rem 1rem; color: #6b7280; font-size: 0.875rem; text-decoration: none;">Limpar</a>
                    @endif
                </form>
            </div>

            <!-- Lista -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                @if($reservations->isEmpty())
                    <div style="padding: 3rem; text-align: center;">
                        <svg style="margin: 0 auto; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                        <p style="margin-top: 1rem; color: #6b7280;">Nenhuma reserva encontrada</p>
                        <a href="{{ route('reservations.create') }}" style="display: inline-block; margin-top: 1rem; color: #2563eb; text-decoration: none;">Criar primeira reserva</a>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Reserva</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Cliente</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Sinal</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Vencimento</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservations as $reservation)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 0.75rem 1rem;">
                                            <div style="font-weight: 600; color: #111827;">{{ $reservation->reservation_number }}</div>
                                            <div style="font-size: 0.75rem; color: #6b7280;">{{ $reservation->created_at->format('d/m/Y') }}</div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem;">
                                            <span style="color: #374151;">{{ $reservation->customer?->name ?? 'Não informado' }}</span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem;">
                                            <span style="font-weight: 500; color: #111827;">{{ $reservation->product_name }}</span>
                                            <div style="font-size: 0.75rem; color: #6b7280;">
                                                {{ $reservation->formatted_product_price }}
                                                @php $resSource = $reservation->source ?? 'stock'; @endphp
                                                @if($resSource !== 'stock')
                                                    <span style="margin-left: 0.25rem; font-size: 0.625rem; padding: 0.0625rem 0.375rem; border-radius: 1rem; font-weight: 500; {{ $resSource === 'quotation' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;' }}">
                                                        {{ $resSource === 'quotation' ? 'Cotação' : 'Manual' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @php
                                                $statusColors = [
                                                    'active' => ['bg' => '#dcfce7', 'color' => '#16a34a'],
                                                    'converted' => ['bg' => '#dbeafe', 'color' => '#2563eb'],
                                                    'cancelled' => ['bg' => '#fee2e2', 'color' => '#dc2626'],
                                                    'expired' => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
                                                ];
                                                $sc = $statusColors[$reservation->status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                            @endphp
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                                {{ $reservation->status->label() }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right;">
                                            <div style="font-weight: 600; color: #111827;">{{ $reservation->formatted_deposit_paid }}</div>
                                            <div style="font-size: 0.75rem; color: #6b7280;">de {{ $reservation->formatted_deposit_amount }}</div>
                                            @if($reservation->deposit_amount > 0)
                                                <div style="margin-top: 0.25rem; height: 4px; background: #e5e7eb; border-radius: 2px; overflow: hidden;">
                                                    <div style="height: 100%; background: #16a34a; width: {{ $reservation->deposit_percent_paid }}%;"></div>
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @if($reservation->status->isActive())
                                                @if($reservation->is_overdue)
                                                    <span style="font-weight: 600; color: #dc2626;">Vencida</span>
                                                @elseif($reservation->is_expiring_soon)
                                                    <span style="font-weight: 600; color: #d97706;">{{ $reservation->expires_at->format('d/m/Y') }}</span>
                                                    <div style="font-size: 0.75rem; color: #d97706;">{{ $reservation->days_until_expiration }} dias</div>
                                                @else
                                                    <span style="color: #374151;">{{ $reservation->expires_at->format('d/m/Y') }}</span>
                                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $reservation->days_until_expiration }} dias</div>
                                                @endif
                                            @else
                                                <span style="color: #9ca3af;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right;">
                                            <div style="display: flex; gap: 0.375rem; justify-content: flex-end;">
                                                @if($reservation->status->isActive())
                                                    <a href="{{ route('reservations.edit', $reservation) }}" title="Editar reserva"
                                                       style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.625rem; background: #eff6ff; color: #2563eb; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none; border: 1px solid #bfdbfe;"
                                                       onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                                        <svg style="width: 0.8rem; height: 0.8rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Editar
                                                    </a>
                                                @endif
                                                <a href="{{ route('reservations.show', $reservation) }}" title="Ver detalhes"
                                                   style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.625rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none; border: 1px solid #e5e7eb;"
                                                   onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                                    <svg style="width: 0.8rem; height: 0.8rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Detalhes
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($reservations->hasPages())
                        <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                            {{ $reservations->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(3, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
