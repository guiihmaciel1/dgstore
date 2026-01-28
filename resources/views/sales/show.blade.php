<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('sales.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Venda #{{ $sale->sale_number }}</h1>
                        <p style="font-size: 0.875rem; color: #6b7280;">{{ $sale->sold_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('sales.receipt', $sale) }}" target="_blank"
                       style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.5rem; background: #374151; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none;"
                       onmouseover="this.style.background='#4b5563'" onmouseout="this.style.background='#374151'">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimir
                    </a>
                    @if($sale->canBeCancelled())
                        <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('Tem certeza que deseja cancelar esta venda? O estoque será devolvido.')">
                            @csrf
                            <button type="submit" 
                                    style="padding: 0.625rem 1.5rem; background: #dc2626; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                                Cancelar Venda
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #16a34a;">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #dc2626;">
                    {{ session('error') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <!-- Coluna Principal -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Itens da Venda -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Itens da Venda</h3>
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Qtd</th>
                                        <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Preço Unit.</th>
                                        <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $item)
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 1rem 1.5rem;">
                                                <div style="font-weight: 500; color: #111827;">{{ $item->product_name }}</div>
                                                <div style="font-size: 0.75rem; color: #9ca3af;">SKU: {{ $item->product_sku }}</div>
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: center; color: #6b7280;">
                                                {{ $item->quantity }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: right; color: #6b7280;">
                                                {{ $item->formatted_unit_price }}
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; text-align: right; font-weight: 600; color: #111827;">
                                                {{ $item->formatted_subtotal }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Totais -->
                        <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #f9fafb;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                <span style="color: #6b7280;">Subtotal:</span>
                                <span style="color: #111827;">{{ $sale->formatted_subtotal }}</span>
                            </div>
                            @if($sale->discount > 0)
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                <span style="color: #6b7280;">Desconto:</span>
                                <span style="color: #dc2626;">-{{ $sale->formatted_discount }}</span>
                            </div>
                            @endif
                            <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                                <span style="color: #111827;">Total:</span>
                                <span style="color: #16a34a;">{{ $sale->formatted_total }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($sale->notes)
                    <!-- Observações -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Observações</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <p style="font-size: 0.875rem; color: #374151; white-space: pre-line;">{{ $sale->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Coluna Lateral -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Status -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Status da Venda</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            @php
                                $statusColors = [
                                    'paid' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
                                    'pending' => ['bg' => '#fefce8', 'color' => '#ca8a04', 'border' => '#fde68a'],
                                    'partial' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#bfdbfe'],
                                    'cancelled' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
                                ];
                                $sc = $statusColors[$sale->payment_status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280', 'border' => '#e5e7eb'];
                            @endphp
                            <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
                                <span style="display: inline-block; padding: 0.5rem 1.5rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.875rem; font-weight: 600; border-radius: 9999px; border: 1px solid {{ $sc['border'] }};">
                                    {{ $sale->payment_status->label() }}
                                </span>
                            </div>
                            
                            @if(!$sale->isCancelled())
                            <form method="POST" action="{{ route('sales.update-status', $sale) }}">
                                @csrf
                                @method('PATCH')
                                <div style="display: flex; gap: 0.5rem;">
                                    <select name="payment_status" 
                                            style="flex: 1; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                        <option value="pending" {{ $sale->payment_status->value === 'pending' ? 'selected' : '' }}>Pendente</option>
                                        <option value="paid" {{ $sale->payment_status->value === 'paid' ? 'selected' : '' }}>Pago</option>
                                        <option value="partial" {{ $sale->payment_status->value === 'partial' ? 'selected' : '' }}>Parcial</option>
                                    </select>
                                    <button type="submit" 
                                            style="padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                            onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                        Atualizar
                                    </button>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Informações -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Informações</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <dl style="display: flex; flex-direction: column; gap: 1rem;">
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Data da Venda</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $sale->sold_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Vendedor</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $sale->user?->name }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Forma de Pagamento</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; font-weight: 500; color: #111827;">
                                        {{ $sale->payment_method->label() }}
                                        @if($sale->installments > 1)
                                            <span style="color: #6b7280;">({{ $sale->installments }}x de {{ $sale->formatted_installment_value }})</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <!-- Cliente -->
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <h3 style="font-weight: 600; color: #111827;">Cliente</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            @if($sale->customer)
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                                    <div style="width: 2.5rem; height: 2.5rem; background: #111827; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: white; font-size: 1rem; font-weight: 600;">{{ strtoupper(substr($sale->customer->name, 0, 1)) }}</span>
                                    </div>
                                    <a href="{{ route('customers.show', $sale->customer) }}" 
                                       style="font-weight: 600; color: #111827; text-decoration: none;"
                                       onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        {{ $sale->customer->name }}
                                    </a>
                                </div>
                                <dl style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div>
                                        <dt style="font-size: 0.75rem; color: #6b7280;">Telefone</dt>
                                        <dd style="font-size: 0.875rem; color: #111827;">{{ $sale->customer->formatted_phone }}</dd>
                                    </div>
                                    @if($sale->customer->email)
                                    <div>
                                        <dt style="font-size: 0.75rem; color: #6b7280;">E-mail</dt>
                                        <dd style="font-size: 0.875rem; color: #111827;">{{ $sale->customer->email }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            @else
                                <p style="font-size: 0.875rem; color: #6b7280; text-align: center;">Cliente não informado</p>
                            @endif
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
