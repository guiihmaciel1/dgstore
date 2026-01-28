<x-app-layout>
    <div class="py-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('reservations.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $reservation->reservation_number }}</h1>
                        <p style="font-size: 0.875rem; color: #6b7280;">Criada em {{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    @if($reservation->canConvert())
                        <a href="{{ route('reservations.convert', $reservation) }}" 
                           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #16a34a; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.5rem; text-decoration: none;"
                           onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Converter em Venda
                        </a>
                    @endif
                </div>
            </div>

            <!-- Status Banner -->
            @php
                $statusColors = [
                    'active' => ['bg' => '#f0fdf4', 'border' => '#86efac', 'color' => '#16a34a', 'icon' => 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z'],
                    'converted' => ['bg' => '#eff6ff', 'border' => '#93c5fd', 'color' => '#2563eb', 'icon' => 'M5 13l4 4L19 7'],
                    'cancelled' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'color' => '#dc2626', 'icon' => 'M6 18L18 6M6 6l12 12'],
                    'expired' => ['bg' => '#f3f4f6', 'border' => '#d1d5db', 'color' => '#6b7280', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                $sc = $statusColors[$reservation->status->value] ?? $statusColors['active'];
            @endphp
            <div style="background: {{ $sc['bg'] }}; border: 1px solid {{ $sc['border'] }}; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <svg style="width: 1.5rem; height: 1.5rem; color: {{ $sc['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sc['icon'] }}"/>
                </svg>
                <div style="flex: 1;">
                    <span style="font-weight: 600; color: {{ $sc['color'] }};">{{ $reservation->status->label() }}</span>
                    @if($reservation->status->isActive())
                        @if($reservation->is_overdue)
                            <span style="margin-left: 0.5rem; font-size: 0.875rem; color: #dc2626;">- Vencida!</span>
                        @elseif($reservation->is_expiring_soon)
                            <span style="margin-left: 0.5rem; font-size: 0.875rem; color: #d97706;">- Vence em {{ $reservation->days_until_expiration }} dias</span>
                        @else
                            <span style="margin-left: 0.5rem; font-size: 0.875rem; color: #6b7280;">- Vence em {{ $reservation->expires_at->format('d/m/Y') }}</span>
                        @endif
                    @endif
                </div>
                @if($reservation->convertedSale)
                    <a href="{{ route('sales.show', $reservation->convertedSale) }}" style="font-size: 0.875rem; color: #2563eb; text-decoration: none;">
                        Ver Venda #{{ $reservation->convertedSale->sale_number }}
                    </a>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <!-- Coluna Esquerda -->
                <div>
                    <!-- Cliente -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                        <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <h3 style="font-weight: 600; color: #111827;">Cliente</h3>
                        </div>
                        <div style="padding: 1.25rem;">
                            <div style="font-size: 1.125rem; font-weight: 600; color: #111827;">{{ $reservation->customer?->name ?? 'Não informado' }}</div>
                            @if($reservation->customer)
                                <div style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                    @if($reservation->customer->phone)
                                        <div>{{ $reservation->customer->formatted_phone }}</div>
                                    @endif
                                    @if($reservation->customer->email)
                                        <div>{{ $reservation->customer->email }}</div>
                                    @endif
                                </div>
                                <a href="{{ route('customers.show', $reservation->customer) }}" style="display: inline-block; margin-top: 0.75rem; font-size: 0.875rem; color: #2563eb; text-decoration: none;">
                                    Ver perfil do cliente
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Produto -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                        <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <h3 style="font-weight: 600; color: #111827;">Produto Reservado</h3>
                        </div>
                        <div style="padding: 1.25rem;">
                            <div style="font-size: 1.125rem; font-weight: 600; color: #111827;">{{ $reservation->product?->full_name ?? 'Não informado' }}</div>
                            @if($reservation->product)
                                <div style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                    <div>SKU: {{ $reservation->product->sku }}</div>
                                    @if($reservation->product->imei)
                                        <div>IMEI: {{ $reservation->product->imei }}</div>
                                    @endif
                                </div>
                                <div style="margin-top: 0.75rem; font-size: 1.25rem; font-weight: 700; color: #16a34a;">
                                    {{ $reservation->formatted_product_price }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pagamentos -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-weight: 600; color: #111827;">Pagamentos do Sinal</h3>
                        </div>
                        <div>
                            @if($reservation->payments->isEmpty())
                                <div style="padding: 2rem; text-align: center; color: #6b7280;">
                                    Nenhum pagamento registrado.
                                </div>
                            @else
                                <div style="overflow-x: auto;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                                <th style="padding: 0.5rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280;">Data</th>
                                                <th style="padding: 0.5rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280;">Forma</th>
                                                <th style="padding: 0.5rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280;">Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reservation->payments as $payment)
                                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                                    <td style="padding: 0.5rem 1rem; font-size: 0.875rem;">{{ $payment->paid_at->format('d/m/Y H:i') }}</td>
                                                    <td style="padding: 0.5rem 1rem; font-size: 0.875rem;">{{ $payment->payment_method->label() }}</td>
                                                    <td style="padding: 0.5rem 1rem; font-size: 0.875rem; text-align: right; font-weight: 600; color: #16a34a;">{{ $payment->formatted_amount }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if($reservation->canReceivePayment())
                                <div style="padding: 1.25rem; border-top: 1px solid #e5e7eb;">
                                    <form method="POST" action="{{ route('reservations.payments.store', $reservation) }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
                                        @csrf
                                        <div style="flex: 1; min-width: 100px;">
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Valor</label>
                                            <input type="number" name="amount" required min="0.01" step="0.01" value="{{ $reservation->deposit_pending }}"
                                                   style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                        </div>
                                        <div style="flex: 1; min-width: 120px;">
                                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Forma</label>
                                            <select name="payment_method" required style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                                @foreach($paymentMethods as $method)
                                                    <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" style="padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer;">
                                            Registrar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita -->
                <div>
                    <!-- Resumo Financeiro -->
                    <div style="background: #111827; border-radius: 0.75rem; padding: 1.25rem; color: white; margin-bottom: 1.5rem;">
                        <h3 style="font-weight: 600; margin-bottom: 1rem;">Resumo Financeiro</h3>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem;">
                            <span style="opacity: 0.8;">Valor do Produto:</span>
                            <span style="font-weight: 500;">{{ $reservation->formatted_product_price }}</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem;">
                            <span style="opacity: 0.8;">Sinal Combinado:</span>
                            <span style="font-weight: 500;">{{ $reservation->formatted_deposit_amount }}</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem;">
                            <span style="opacity: 0.8;">Sinal Pago:</span>
                            <span style="font-weight: 600; color: #86efac;">{{ $reservation->formatted_deposit_paid }}</span>
                        </div>

                        <!-- Barra de progresso -->
                        <div style="margin: 1rem 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.75rem; opacity: 0.8;">
                                <span>Progresso do sinal</span>
                                <span>{{ number_format($reservation->deposit_percent_paid, 0) }}%</span>
                            </div>
                            <div style="height: 8px; background: rgba(255,255,255,0.2); border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; background: #86efac; width: {{ $reservation->deposit_percent_paid }}%; transition: width 0.3s ease;"></div>
                            </div>
                        </div>

                        @if($reservation->deposit_pending > 0)
                            <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.2); font-size: 0.875rem; color: #fcd34d;">
                                <span>Sinal Pendente:</span>
                                <span style="font-weight: 600;">R$ {{ number_format($reservation->deposit_pending, 2, ',', '.') }}</span>
                            </div>
                        @endif

                        <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; margin-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.2); font-size: 1.125rem;">
                            <span style="font-weight: 600;">Restante para Venda:</span>
                            <span style="font-weight: 700;">{{ $reservation->formatted_remaining_amount }}</span>
                        </div>
                    </div>

                    <!-- Informações -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                        <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <h3 style="font-weight: 600; color: #111827;">Informações</h3>
                        </div>
                        <div style="padding: 1rem;">
                            <dl style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Vendedor:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $reservation->user?->name }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Data da Reserva:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $reservation->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Data Limite:</dt>
                                    <dd style="font-weight: 500; color: {{ $reservation->is_overdue ? '#dc2626' : ($reservation->is_expiring_soon ? '#d97706' : '#111827') }};">
                                        {{ $reservation->expires_at->format('d/m/Y') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($reservation->notes)
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Observações</h3>
                            </div>
                            <div style="padding: 1rem;">
                                <p style="font-size: 0.875rem; color: #374151; white-space: pre-line;">{{ $reservation->notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($reservation->canCancel())
                        <form method="POST" action="{{ route('reservations.cancel', $reservation) }}"
                              onsubmit="return confirm('Tem certeza que deseja cancelar esta reserva? O produto será liberado.');">
                            @csrf
                            <button type="submit" style="width: 100%; padding: 0.5rem; color: #dc2626; font-size: 0.875rem; background: none; border: 1px solid #fecaca; border-radius: 0.5rem; cursor: pointer;"
                                    onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                Cancelar Reserva
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
