<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('stock.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Trade-ins</h1>
                        <p style="font-size: 0.875rem; color: #6b7280;">Aparelhos recebidos como entrada em vendas</p>
                    </div>
                </div>
            </div>

            <!-- Cards de Estatísticas -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <a href="{{ route('stock.trade-ins', ['status' => 'pending']) }}" 
                   style="display: block; padding: 1.5rem; background: {{ $currentStatus === 'pending' ? '#fefce8' : 'white' }}; border-radius: 1rem; border: 2px solid {{ $currentStatus === 'pending' ? '#fde68a' : '#e5e7eb' }}; text-decoration: none; transition: all 0.2s;"
                   onmouseover="this.style.borderColor='#fde68a'" onmouseout="this.style.borderColor='{{ $currentStatus === 'pending' ? '#fde68a' : '#e5e7eb' }}'">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.75rem; background: #fefce8; border-radius: 0.75rem;">
                            <svg style="width: 1.5rem; height: 1.5rem; color: #ca8a04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 2rem; font-weight: 700; color: #ca8a04;">{{ $stats['pending'] }}</p>
                            <p style="font-size: 0.875rem; color: #6b7280;">Pendentes</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('stock.trade-ins', ['status' => 'processed']) }}" 
                   style="display: block; padding: 1.5rem; background: {{ $currentStatus === 'processed' ? '#f0fdf4' : 'white' }}; border-radius: 1rem; border: 2px solid {{ $currentStatus === 'processed' ? '#bbf7d0' : '#e5e7eb' }}; text-decoration: none; transition: all 0.2s;"
                   onmouseover="this.style.borderColor='#bbf7d0'" onmouseout="this.style.borderColor='{{ $currentStatus === 'processed' ? '#bbf7d0' : '#e5e7eb' }}'">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.75rem; background: #f0fdf4; border-radius: 0.75rem;">
                            <svg style="width: 1.5rem; height: 1.5rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 2rem; font-weight: 700; color: #16a34a;">{{ $stats['processed'] }}</p>
                            <p style="font-size: 0.875rem; color: #6b7280;">Processados</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('stock.trade-ins', ['status' => 'rejected']) }}" 
                   style="display: block; padding: 1.5rem; background: {{ $currentStatus === 'rejected' ? '#fef2f2' : 'white' }}; border-radius: 1rem; border: 2px solid {{ $currentStatus === 'rejected' ? '#fecaca' : '#e5e7eb' }}; text-decoration: none; transition: all 0.2s;"
                   onmouseover="this.style.borderColor='#fecaca'" onmouseout="this.style.borderColor='{{ $currentStatus === 'rejected' ? '#fecaca' : '#e5e7eb' }}'">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.75rem; background: #fef2f2; border-radius: 0.75rem;">
                            <svg style="width: 1.5rem; height: 1.5rem; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size: 2rem; font-weight: 700; color: #dc2626;">{{ $stats['rejected'] }}</p>
                            <p style="font-size: 0.875rem; color: #6b7280;">Rejeitados</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Lista de Trade-ins -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: between; align-items: center;">
                    <h3 style="font-weight: 600; color: #111827;">
                        @if($currentStatus === 'pending')
                            Trade-ins Pendentes
                        @elseif($currentStatus === 'processed')
                            Trade-ins Processados
                        @elseif($currentStatus === 'rejected')
                            Trade-ins Rejeitados
                        @else
                            Todos os Trade-ins
                        @endif
                    </h3>
                    <a href="{{ route('stock.trade-ins', ['status' => 'all']) }}" 
                       style="margin-left: auto; font-size: 0.875rem; color: #6b7280; text-decoration: none;"
                       onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                        Ver todos
                    </a>
                </div>

                @if($tradeIns->isEmpty())
                    <div style="padding: 3rem; text-align: center;">
                        <svg style="margin: 0 auto; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <p style="margin-top: 1rem; color: #6b7280;">Nenhum trade-in encontrado</p>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Aparelho</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Venda</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Condição</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Valor</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                    <th style="padding: 0.75rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tradeIns as $tradeIn)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 1rem 1.5rem;">
                                            <div style="font-weight: 600; color: #111827;">{{ $tradeIn->full_name }}</div>
                                            @if($tradeIn->imei)
                                                <div style="font-size: 0.75rem; color: #9ca3af; font-family: monospace;">IMEI: {{ $tradeIn->imei }}</div>
                                            @endif
                                            @if($tradeIn->notes)
                                                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">{{ Str::limit($tradeIn->notes, 50) }}</div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem;">
                                            <a href="{{ route('sales.show', $tradeIn->sale) }}" 
                                               style="font-weight: 500; color: #2563eb; text-decoration: none;"
                                               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                #{{ $tradeIn->sale->sale_number }}
                                            </a>
                                            <div style="font-size: 0.75rem; color: #6b7280;">{{ $tradeIn->created_at->format('d/m/Y') }}</div>
                                            @if($tradeIn->sale->customer)
                                                <div style="font-size: 0.75rem; color: #9ca3af;">{{ $tradeIn->sale->customer->name }}</div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @php
                                                $conditionColors = [
                                                    'excellent' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                    'good' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                    'fair' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                                    'poor' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                                ];
                                                $cc = $conditionColors[$tradeIn->condition->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                            @endphp
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $cc['bg'] }}; color: {{ $cc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                                {{ $tradeIn->condition->label() }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right;">
                                            <div style="font-weight: 600; color: #7c3aed;">{{ $tradeIn->formatted_value }}</div>
                                            <div style="font-size: 0.6875rem; color: #ca8a04;">Venda min: {{ $tradeIn->formatted_value }}</div>
                                            @if($tradeIn->isProcessed() && $tradeIn->product)
                                                @php $tradeInSalePrice = (float) $tradeIn->product->sale_price; @endphp
                                                @if($tradeInSalePrice > 0 && $tradeInSalePrice < (float) $tradeIn->estimated_value)
                                                    <div style="font-size: 0.6875rem; color: #dc2626; font-weight: 700;">
                                                        PRECO VENDA ABAIXO DO CUSTO!
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @php
                                                $statusColors = [
                                                    'pending' => ['bg' => '#fefce8', 'color' => '#ca8a04', 'border' => '#fde68a'],
                                                    'processed' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
                                                    'rejected' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
                                                ];
                                                $sc = $statusColors[$tradeIn->status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280', 'border' => '#e5e7eb'];
                                            @endphp
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; border: 1px solid {{ $sc['border'] }};">
                                                {{ $tradeIn->status->label() }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem 1.5rem; text-align: right;">
                                            @if($tradeIn->isPending())
                                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                                    <form method="POST" action="{{ route('stock.trade-ins.process', $tradeIn) }}">
                                                        @csrf
                                                        <input type="hidden" name="action" value="create">
                                                        <button type="submit" 
                                                                style="padding: 0.375rem 0.75rem; background: #16a34a; color: white; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer;"
                                                                onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                                                            Cadastrar
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('stock.trade-ins.process', $tradeIn) }}" 
                                                          onsubmit="return confirm('Tem certeza que deseja rejeitar este trade-in?')">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" 
                                                                style="padding: 0.375rem 0.75rem; background: #f3f4f6; color: #6b7280; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer;"
                                                                onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                                            Rejeitar
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($tradeIn->isProcessed() && $tradeIn->product)
                                                <a href="{{ route('products.show', $tradeIn->product) }}" 
                                                   style="font-size: 0.75rem; color: #2563eb; text-decoration: none;"
                                                   onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                    Ver Produto
                                                </a>
                                            @else
                                                <span style="font-size: 0.75rem; color: #9ca3af;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($tradeIns->hasPages())
                        <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb;">
                            {{ $tradeIns->links() }}
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
