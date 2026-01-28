<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('customers.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3.5rem; height: 3.5rem; background: #111827; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.5rem; font-weight: 600;">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $customer->name }}</h1>
                            <p style="font-size: 0.875rem; color: #6b7280;">Cliente desde {{ $customer->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('sales.create') }}?customer_id={{ $customer->id }}" 
                       style="padding: 0.625rem 1.5rem; background: #16a34a; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none;"
                       onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        Nova Venda
                    </a>
                    <a href="{{ route('customers.edit', $customer) }}" 
                       style="padding: 0.625rem 1.5rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none;"
                       onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                        Editar
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #16a34a;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <!-- Coluna Principal -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Dados do Cliente -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Dados do Cliente</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Telefone</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827; font-weight: 500;">{{ $customer->formatted_phone }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">E-mail</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $customer->email ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">CPF</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $customer->formatted_cpf ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Cliente desde</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $customer->created_at->format('d/m/Y') }}</dd>
                                </div>
                            </div>
                            @if($customer->address)
                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Endereço</dt>
                                <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $customer->address }}</dd>
                            </div>
                            @endif
                            @if($customer->notes)
                            <div style="margin-top: 1rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Observações</dt>
                                <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #111827;">{{ $customer->notes }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Histórico de Compras -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Histórico de Compras</h3>
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Venda</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Itens</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Total</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                        <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->sales as $sale)
                                        @php
                                            $statusColors = [
                                                'paid' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'pending' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                                'partial' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                'cancelled' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                            ];
                                            $sc = $statusColors[$sale->payment_status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                        @endphp
                                        <tr style="border-bottom: 1px solid #f3f4f6;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                            <td style="padding: 0.75rem 1.5rem; font-weight: 600; color: #111827;">
                                                {{ $sale->sale_number }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #6b7280;">
                                                {{ $sale->sold_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.875rem; color: #6b7280;">
                                                {{ $sale->items->count() }} {{ $sale->items->count() === 1 ? 'item' : 'itens' }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #111827;">
                                                {{ $sale->formatted_total }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: center;">
                                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                                    {{ $sale->payment_status->label() }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                                <a href="{{ route('sales.show', $sale) }}" 
                                                   style="font-size: 0.875rem; color: #111827; text-decoration: none; font-weight: 500;"
                                                   onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                    Ver →
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="padding: 3rem; text-align: center; color: #6b7280;">
                                                <svg style="margin: 0 auto 1rem; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                </svg>
                                                Nenhuma compra registrada.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div>
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Resumo de Compras</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="margin-bottom: 1.5rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Total de Compras</dt>
                                <dd style="margin-top: 0.25rem; font-size: 2rem; font-weight: 700; color: #111827;">{{ $customer->purchases_count }}</dd>
                            </div>
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Valor Total</dt>
                                <dd style="margin-top: 0.25rem; font-size: 1.75rem; font-weight: 700; color: #16a34a;">R$ {{ number_format($customer->total_purchases, 2, ',', '.') }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: 2fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
