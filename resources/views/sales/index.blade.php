<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Vendas</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Histórico de vendas realizadas</p>
                </div>
                <a href="{{ route('sales.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; text-decoration: none; transition: background 0.2s;"
                   onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Venda
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                
                <!-- Filtros -->
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <form method="GET" action="{{ route('sales.index') }}" class="sales-filter-form">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Buscar</label>
                            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nº da venda, cliente..." 
                                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#d1d5db'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Status</label>
                            <select name="payment_status" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                <option value="">Todos</option>
                                @foreach($paymentStatuses as $status)
                                    <option value="{{ $status->value }}" {{ ($filters['payment_status'] ?? '') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Pagamento</label>
                            <select name="payment_method" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                <option value="">Todos</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->value }}" {{ ($filters['payment_method'] ?? '') === $method->value ? 'selected' : '' }}>
                                        {{ $method->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Data Início</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" 
                                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Data Fim</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" 
                                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                            <button type="submit" 
                                    style="flex: 1; padding: 0.5rem 1rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">
                                Filtrar
                            </button>
                            <a href="{{ route('sales.index') }}" 
                               style="padding: 0.5rem 1rem; background: white; color: #374151; font-weight: 500; border-radius: 0.5rem; border: 1px solid #d1d5db; text-decoration: none; font-size: 0.875rem;">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabela -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Venda</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Cliente</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Total</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Pagamento</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Data</th>
                                <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="font-weight: 600; color: #111827;">{{ $sale->sale_number }}</div>
                                        <div style="font-size: 0.75rem; color: #9ca3af;">{{ $sale->user?->name }}</div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151;">
                                        {{ $sale->customer?->name ?? 'Cliente não informado' }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; font-size: 1rem; color: #111827;">
                                        {{ $sale->formatted_total }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $sale->payment_method->label() }}
                                        @if($sale->installments > 1)
                                            <span style="font-size: 0.75rem; color: #9ca3af;">({{ $sale->installments }}x)</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @php
                                            $statusColors = [
                                                'paid' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'pending' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                                'partial' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                'cancelled' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                            ];
                                            $sc = $statusColors[$sale->payment_status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                        @endphp
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $sale->payment_status->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                        {{ $sale->sold_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;"
                                           onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                            Ver detalhes
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 3rem; text-align: center; color: #6b7280;">
                                        Nenhuma venda encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($sales->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #f9fafb;">
                        {{ $sales->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .sales-filter-form {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            align-items: end;
        }
        @media (max-width: 1024px) {
            .sales-filter-form {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 640px) {
            .sales-filter-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
