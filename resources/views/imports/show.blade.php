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
                    <a href="{{ route('imports.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $order->order_number }}</h1>
                        <p style="font-size: 0.875rem; color: #6b7280;">Pedido de {{ $order->ordered_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    @if($order->isActive())
                        <a href="{{ route('imports.receive', $order) }}" 
                           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #16a34a; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.5rem; text-decoration: none;"
                           onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Receber
                        </a>
                    @endif
                </div>
            </div>

            <!-- Timeline de Status -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-weight: 600; color: #111827;">Status do Pedido</h3>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; position: relative;">
                        @php
                            $currentStep = $order->status->step();
                            $allSteps = [
                                ['value' => 'ordered', 'label' => 'Pedido', 'step' => 1],
                                ['value' => 'shipped', 'label' => 'Enviado', 'step' => 2],
                                ['value' => 'in_transit', 'label' => 'Em Trânsito', 'step' => 3],
                                ['value' => 'customs', 'label' => 'Alfândega', 'step' => 4],
                                ['value' => 'received', 'label' => 'Recebido', 'step' => 5],
                            ];
                        @endphp

                        <!-- Linha de progresso -->
                        <div style="position: absolute; top: 1rem; left: 2.5rem; right: 2.5rem; height: 2px; background: #e5e7eb; z-index: 0;">
                            <div style="height: 100%; background: #16a34a; transition: width 0.3s ease;" 
                                 @if($order->status === \App\Domain\Import\Enums\ImportOrderStatus::Cancelled)
                                     style="width: 0%;"
                                 @else
                                     style="width: {{ (($currentStep - 1) / 4) * 100 }}%;"
                                 @endif
                            ></div>
                        </div>

                        @foreach($allSteps as $step)
                            @php
                                $isCompleted = $currentStep >= $step['step'] && $order->status !== \App\Domain\Import\Enums\ImportOrderStatus::Cancelled;
                                $isCurrent = $currentStep === $step['step'] && $order->status !== \App\Domain\Import\Enums\ImportOrderStatus::Cancelled;
                            @endphp
                            <div style="display: flex; flex-direction: column; align-items: center; z-index: 1; flex: 1;">
                                <div style="width: 2rem; height: 2rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; {{ $isCompleted ? 'background: #16a34a; color: white;' : 'background: #e5e7eb; color: #9ca3af;' }} {{ $isCurrent ? 'box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.2);' : '' }}">
                                    @if($isCompleted)
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        <span style="font-size: 0.75rem; font-weight: 600;">{{ $step['step'] }}</span>
                                    @endif
                                </div>
                                <span style="margin-top: 0.5rem; font-size: 0.75rem; font-weight: {{ $isCurrent ? '600' : '500' }}; color: {{ $isCompleted ? '#111827' : '#9ca3af' }};">{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if($order->status === \App\Domain\Import\Enums\ImportOrderStatus::Cancelled)
                        <div style="margin-top: 1.5rem; padding: 1rem; background: #fef2f2; border-radius: 0.5rem; text-align: center;">
                            <span style="color: #dc2626; font-weight: 600;">Pedido Cancelado</span>
                        </div>
                    @endif

                    <!-- Atualizar Status -->
                    @if($order->isActive())
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                            <form method="POST" action="{{ route('imports.status', $order) }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
                                @csrf
                                @method('PATCH')
                                <div style="flex: 1; min-width: 150px;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Atualizar Status</label>
                                    <select name="status" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                        @foreach($statuses as $status)
                                            @if($order->canAdvanceTo($status))
                                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div style="flex: 1; min-width: 150px;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Rastreio (opcional)</label>
                                    <input type="text" name="tracking_code" value="{{ $order->tracking_code }}" placeholder="Código de rastreio"
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <button type="submit" style="padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;">
                                    Atualizar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <!-- Itens do Pedido -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <h3 style="font-weight: 600; color: #111827;">Itens do Pedido ({{ $order->total_items }})</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280;">Descrição</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280;">Qtd</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280;">Custo Unit.</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280;">Total</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280;">Recebido</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 0.75rem 1rem;">
                                            <span style="font-weight: 500; color: #111827;">{{ $item->description }}</span>
                                            @if($item->product)
                                                <div style="font-size: 0.75rem; color: #6b7280;">SKU: {{ $item->product->sku }}</div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">{{ $item->quantity }}</td>
                                        <td style="padding: 0.75rem 1rem; text-align: right;">{{ $item->formatted_unit_cost }}</td>
                                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 500;">{{ $item->formatted_total_cost }}</td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @if($item->is_fully_received)
                                                <span style="display: inline-block; padding: 0.125rem 0.5rem; background: #dcfce7; color: #16a34a; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                                    {{ $item->received_quantity }}/{{ $item->quantity }}
                                                </span>
                                            @elseif($item->is_partially_received)
                                                <span style="display: inline-block; padding: 0.125rem 0.5rem; background: #fef3c7; color: #d97706; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                                    {{ $item->received_quantity }}/{{ $item->quantity }}
                                                </span>
                                            @else
                                                <span style="color: #9ca3af;">0/{{ $item->quantity }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Informações e Custos -->
                <div>
                    <!-- Informações -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1rem;">
                        <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <h3 style="font-weight: 600; color: #111827;">Informações</h3>
                        </div>
                        <div style="padding: 1rem;">
                            <dl style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Fornecedor:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->supplier?->name ?? '-' }}</dd>
                                </div>
                                @if($order->tracking_code)
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Rastreio:</dt>
                                    <dd style="font-weight: 500; color: #111827; font-family: monospace;">{{ $order->tracking_code }}</dd>
                                </div>
                                @endif
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Data do Pedido:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->ordered_at->format('d/m/Y') }}</dd>
                                </div>
                                @if($order->shipped_at)
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Data Envio:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->shipped_at->format('d/m/Y') }}</dd>
                                </div>
                                @endif
                                @if($order->estimated_arrival)
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Previsão:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->estimated_arrival->format('d/m/Y') }}</dd>
                                </div>
                                @endif
                                @if($order->received_at)
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Data Recebimento:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->received_at->format('d/m/Y') }}</dd>
                                </div>
                                @endif
                                @if($order->days_in_transit)
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Dias em trânsito:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->days_in_transit }} dias</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Custos -->
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <h3 style="font-weight: 600; color: #111827;">Custos</h3>
                        </div>
                        <div style="padding: 1rem;">
                            <dl style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Subtotal (USD):</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->formatted_estimated_cost }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Cotação USD:</dt>
                                    <dd style="font-weight: 500; color: #111827;">R$ {{ number_format($order->exchange_rate, 4, ',', '.') }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Frete:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->formatted_shipping_cost }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <dt style="color: #6b7280;">Impostos:</dt>
                                    <dd style="font-weight: 500; color: #111827;">{{ $order->formatted_taxes }}</dd>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                                    <dt style="font-weight: 600; color: #111827;">Total Estimado:</dt>
                                    <dd style="font-weight: 700; color: #111827;">{{ $order->formatted_estimated_total_brl }}</dd>
                                </div>
                                @if($order->actual_cost !== null)
                                    <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                                        <dt style="font-weight: 600; color: #111827;">Total Real:</dt>
                                        <dd style="font-weight: 700; color: #111827;">{{ $order->formatted_actual_total_brl }}</dd>
                                    </div>
                                    @if($order->cost_difference !== null)
                                        @php
                                            $diff = $order->cost_difference;
                                            $isPositive = $diff > 0;
                                        @endphp
                                        <div style="display: flex; justify-content: space-between;">
                                            <dt style="color: #6b7280;">Diferença:</dt>
                                            <dd style="font-weight: 600; color: {{ $isPositive ? '#dc2626' : '#16a34a' }};">
                                                {{ $isPositive ? '+' : '' }}R$ {{ number_format($diff, 2, ',', '.') }}
                                                ({{ number_format($order->cost_difference_percent, 1, ',', '.') }}%)
                                            </dd>
                                        </div>
                                    @endif
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($order->notes)
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-top: 1rem;">
                            <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <h3 style="font-weight: 600; color: #111827;">Observações</h3>
                            </div>
                            <div style="padding: 1rem;">
                                <p style="font-size: 0.875rem; color: #374151; white-space: pre-line;">{{ $order->notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->isActive())
                        <form method="POST" action="{{ route('imports.cancel', $order) }}" style="margin-top: 1rem;"
                              onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?');">
                            @csrf
                            <button type="submit" style="width: 100%; padding: 0.5rem; color: #dc2626; font-size: 0.875rem; background: none; border: 1px solid #fecaca; border-radius: 0.5rem; cursor: pointer;"
                                    onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                Cancelar Pedido
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: 2fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
