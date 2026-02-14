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
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Garantias</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Controle de garantias dos produtos vendidos</p>
                </div>
            </div>

            <!-- Cards de Estatísticas -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="padding: 1.25rem; background: {{ $stats['expiring_soon'] > 0 ? '#fefce8' : 'white' }}; border-radius: 0.75rem; border: 1px solid {{ $stats['expiring_soon'] > 0 ? '#fde68a' : '#e5e7eb' }};">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.625rem; background: {{ $stats['expiring_soon'] > 0 ? '#fef3c7' : '#f3f4f6' }}; border-radius: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: {{ $stats['expiring_soon'] > 0 ? '#d97706' : '#6b7280' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 1.5rem; font-weight: 700; color: {{ $stats['expiring_soon'] > 0 ? '#d97706' : '#111827' }};">{{ $stats['expiring_soon'] }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280;">Vencendo em 30 dias</p>
                        </div>
                    </div>
                </div>

                <div style="padding: 1.25rem; background: {{ $stats['open_claims'] > 0 ? '#fef2f2' : 'white' }}; border-radius: 0.75rem; border: 1px solid {{ $stats['open_claims'] > 0 ? '#fecaca' : '#e5e7eb' }};">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.625rem; background: {{ $stats['open_claims'] > 0 ? '#fee2e2' : '#f3f4f6' }}; border-radius: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: {{ $stats['open_claims'] > 0 ? '#dc2626' : '#6b7280' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 1.5rem; font-weight: 700; color: {{ $stats['open_claims'] > 0 ? '#dc2626' : '#111827' }};">{{ $stats['open_claims'] }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280;">Acionamentos abertos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem; margin-bottom: 1rem;">
                <form method="GET" x-data x-ref="filterForm" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Buscar</label>
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="IMEI, venda, cliente ou produto..."
                               style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;"
                               x-on:input.debounce.400ms="$refs.filterForm.submit()">
                    </div>
                    <div style="min-width: 180px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Filtrar por</label>
                        <select name="status" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                                x-on:change="$refs.filterForm.submit()">
                            <option value="">Todas</option>
                            <option value="expiring" {{ $filters['status'] === 'expiring' ? 'selected' : '' }}>Vencendo em 30 dias</option>
                            <option value="supplier_active" {{ $filters['status'] === 'supplier_active' ? 'selected' : '' }}>Garantia fornecedor ativa</option>
                            <option value="customer_active" {{ $filters['status'] === 'customer_active' ? 'selected' : '' }}>Garantia cliente ativa</option>
                            <option value="with_claims" {{ $filters['status'] === 'with_claims' ? 'selected' : '' }}>Com acionamentos abertos</option>
                        </select>
                    </div>
                    @if($filters['search'] || $filters['status'])
                        <a href="{{ route('warranties.index') }}" style="padding: 0.5rem 1rem; color: #6b7280; font-size: 0.875rem; text-decoration: none;">Limpar</a>
                    @endif
                </form>
            </div>

            <!-- Lista -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                @if($warranties->isEmpty())
                    <div style="padding: 3rem; text-align: center;">
                        <svg style="margin: 0 auto; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p style="margin-top: 1rem; color: #6b7280;">Nenhuma garantia encontrada</p>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Cliente</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Gar. Fornecedor</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Gar. Cliente</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Acionamentos</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warranties as $warranty)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 0.75rem 1rem;">
                                            <div style="font-weight: 500; color: #111827;">{{ $warranty->product_name }}</div>
                                            @if($warranty->imei)
                                                <div style="font-size: 0.75rem; color: #9ca3af; font-family: monospace;">IMEI: {{ $warranty->imei }}</div>
                                            @endif
                                            <div style="font-size: 0.75rem; color: #6b7280;">Venda: {{ $warranty->sale_number }}</div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem;">
                                            <span style="color: #374151;">{{ $warranty->customer_name ?? 'Não informado' }}</span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @if($warranty->supplier_warranty_until)
                                                @php
                                                    $daysRemaining = $warranty->supplier_days_remaining;
                                                    $isExpiring = $daysRemaining !== null && $daysRemaining <= 30 && $daysRemaining > 0;
                                                    $isExpired = $daysRemaining !== null && $daysRemaining <= 0;
                                                @endphp
                                                <div style="font-size: 0.875rem; font-weight: 500; color: {{ $isExpired ? '#dc2626' : ($isExpiring ? '#d97706' : '#16a34a') }};">
                                                    {{ $warranty->supplier_warranty_until->format('d/m/Y') }}
                                                </div>
                                                <div style="font-size: 0.75rem; color: {{ $isExpired ? '#dc2626' : ($isExpiring ? '#d97706' : '#6b7280') }};">
                                                    @if($isExpired)
                                                        Expirada
                                                    @else
                                                        {{ $daysRemaining }} dias
                                                    @endif
                                                </div>
                                            @else
                                                <span style="color: #9ca3af;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @if($warranty->customer_warranty_until)
                                                @php
                                                    $daysRemaining = $warranty->customer_days_remaining;
                                                    $isExpiring = $daysRemaining !== null && $daysRemaining <= 30 && $daysRemaining > 0;
                                                    $isExpired = $daysRemaining !== null && $daysRemaining <= 0;
                                                @endphp
                                                <div style="font-size: 0.875rem; font-weight: 500; color: {{ $isExpired ? '#dc2626' : ($isExpiring ? '#d97706' : '#16a34a') }};">
                                                    {{ $warranty->customer_warranty_until->format('d/m/Y') }}
                                                </div>
                                                <div style="font-size: 0.75rem; color: {{ $isExpired ? '#dc2626' : ($isExpiring ? '#d97706' : '#6b7280') }};">
                                                    @if($isExpired)
                                                        Expirada
                                                    @else
                                                        {{ $daysRemaining }} dias
                                                    @endif
                                                </div>
                                            @else
                                                <span style="color: #9ca3af;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @if($warranty->open_claims_count > 0)
                                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #fef2f2; color: #dc2626; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                                    {{ $warranty->open_claims_count }} aberto(s)
                                                </span>
                                            @else
                                                <span style="color: #9ca3af;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right;">
                                            <a href="{{ route('warranties.show', $warranty) }}" 
                                               style="padding: 0.375rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;"
                                               onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                                Ver detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($warranties->hasPages())
                        <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                            {{ $warranties->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
