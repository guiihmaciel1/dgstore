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
                    @if($reservation->isActive())
                        <a href="{{ route('reservations.edit', $reservation) }}"
                           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #eff6ff; color: #2563eb; font-weight: 500; font-size: 0.875rem; border-radius: 0.5rem; text-decoration: none; border: 1px solid #bfdbfe;"
                           onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </a>
                    @endif
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
                        <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-weight: 600; color: #111827;">Produto Reservado</h3>
                            @php
                                $sourceColors = [
                                    'stock' => ['bg' => '#dcfce7', 'color' => '#16a34a', 'label' => 'Estoque'],
                                    'quotation' => ['bg' => '#dbeafe', 'color' => '#2563eb', 'label' => 'Cotação'],
                                    'manual' => ['bg' => '#fef3c7', 'color' => '#d97706', 'label' => 'Manual'],
                                ];
                                $src = $sourceColors[$reservation->source ?? 'stock'] ?? $sourceColors['stock'];
                            @endphp
                            <span style="font-size: 0.625rem; padding: 0.125rem 0.5rem; border-radius: 1rem; font-weight: 500; background: {{ $src['bg'] }}; color: {{ $src['color'] }};">
                                {{ $src['label'] }}
                            </span>
                        </div>
                        <div style="padding: 1.25rem;">
                            <div style="font-size: 1.125rem; font-weight: 600; color: #111827;">{{ $reservation->product_name }}</div>
                            @if($reservation->product)
                                <div style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                    <div>SKU: {{ $reservation->product->sku }}</div>
                                    @if($reservation->product->imei)
                                        <div>IMEI: {{ $reservation->product->imei }}</div>
                                    @endif
                                </div>
                            @endif
                            <div style="margin-top: 0.75rem; font-size: 1.25rem; font-weight: 700; color: #16a34a;">
                                {{ $reservation->formatted_product_price }}
                            </div>
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
                                                @if($reservation->isActive())
                                                    <th style="padding: 0.5rem 0.5rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; width: 70px;"></th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reservation->payments as $payment)
                                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                                    <td style="padding: 0.5rem 1rem; font-size: 0.875rem;">{{ $payment->paid_at->format('d/m/Y H:i') }}</td>
                                                    <td style="padding: 0.5rem 1rem; font-size: 0.875rem;">{{ $payment->payment_method->label() }}</td>
                                                    <td style="padding: 0.5rem 1rem; font-size: 0.875rem; text-align: right; font-weight: 600; color: #16a34a;">{{ $payment->formatted_amount }}</td>
                                                    @if($reservation->isActive())
                                                        <td style="padding: 0.5rem 0.5rem; text-align: center;">
                                                            <form method="POST" action="{{ route('reservations.payments.destroy', [$reservation, $payment]) }}"
                                                                  onsubmit="return confirm('Tem certeza que deseja estornar este pagamento de {{ $payment->formatted_amount }}?');"
                                                                  style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" title="Estornar pagamento"
                                                                        style="padding: 0.25rem 0.5rem; background: none; color: #dc2626; font-size: 0.7rem; font-weight: 500; border: 1px solid #fecaca; border-radius: 0.25rem; cursor: pointer;"
                                                                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                                                    Estornar
                                                                </button>
                                                            </form>
                                                        </td>
                                                    @endif
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

                        @if($reservation->cost_price > 0)
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8rem; opacity: 0.7;">
                                <span>Custo:</span>
                                <span>{{ $reservation->formatted_cost_price }}</span>
                            </div>
                        @endif
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem;">
                            <span style="opacity: 0.8;">Valor de Venda:</span>
                            <span style="font-weight: 500;">{{ $reservation->formatted_product_price }}</span>
                        </div>

                        @if($reservation->cost_price > 0)
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.8rem; color: {{ $reservation->profit >= 0 ? '#86efac' : '#fca5a5' }};">
                                <span>Lucro:</span>
                                <span style="font-weight: 600;">{{ $reservation->formatted_profit }}</span>
                            </div>
                        @endif
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.75rem;">
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

                    <!-- WhatsApp do Cliente -->
                    @if($reservation->customer?->phone)
                        @php
                            $waPhone = preg_replace('/\D/', '', $reservation->customer->phone);
                            if (strlen($waPhone) <= 11) $waPhone = '55' . $waPhone;
                            $waText = urlencode(
                                "Ola {$reservation->customer->name}! Sua reserva na DG Store:\n\n"
                                . "Reserva: #{$reservation->reservation_number}\n"
                                . "Produto: {$reservation->product_name}\n"
                                . "Valor: {$reservation->formatted_product_price}\n"
                                . "Sinal: {$reservation->formatted_deposit_amount}\n"
                                . "Pago: {$reservation->formatted_deposit_paid}\n"
                                . "Validade: {$reservation->expires_at->format('d/m/Y')}\n\n"
                                . "DG Store"
                            );
                        @endphp
                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank"
                           style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.625rem; background: #16a34a; color: white; font-size: 0.875rem; font-weight: 600; border-radius: 0.5rem; text-decoration: none; margin-bottom: 0.75rem;"
                           onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            <svg style="width: 1rem; height: 1rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp do Cliente
                        </a>
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
