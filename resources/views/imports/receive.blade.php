<x-app-layout>
    <x-slot name="title">Receber Importação</x-slot>
    <div class="py-4">
        <div class="px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('imports.show', $order) }}" style="margin-right: 1rem; padding: 0.5rem; color: #818181; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#222222'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;">Receber Pedido</h1>
                    <p style="font-size: 0.875rem; color: #818181;">{{ $order->order_number }} - {{ $order->supplier?->name ?? 'Sem fornecedor' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('imports.confirm-receive', $order) }}" x-data="receiveForm()">
                @csrf

                <!-- Itens -->
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden; margin-bottom: 1.5rem;">
                    <div style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-weight: 600; color: #e3e3e3;">Confirmar Quantidades Recebidas</h3>
                        <button type="button" @click="markAllReceived()" style="padding: 0.375rem 0.75rem; background: #222222; color: #a4a4a4; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer;">
                            Receber Todos
                        </button>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181;">Item</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181;">Pedido</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181;">Já Recebido</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181;">Recebendo Agora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td style="padding: 0.75rem 1rem;">
                                            <span style="font-weight: 500; color: #e3e3e3;">{{ $item->description }}</span>
                                            <div style="font-size: 0.75rem; color: #818181;">{{ $item->formatted_unit_cost }}/un</div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 500;">{{ $item->quantity }}</td>
                                        <td style="padding: 0.75rem 1rem; text-align: center; color: #818181;">{{ $item->received_quantity }}</td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            <input type="number" 
                                                   name="items[{{ $item->id }}]" 
                                                   x-model.number="items['{{ $item->id }}']"
                                                   min="0" 
                                                   max="{{ $item->quantity }}"
                                                   style="width: 5rem; padding: 0.375rem 0.5rem; text-align: center; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Custo Real -->
                <div style="background: #141414; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06); overflow: hidden; margin-bottom: 1.5rem;">
                    <div style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
                        <h3 style="font-weight: 600; color: #e3e3e3;">Custo Real (Opcional)</h3>
                    </div>
                    <div style="padding: 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.5rem;">Custo Estimado (USD)</label>
                                <div style="padding: 0.625rem 0.75rem; background: #222222; border-radius: 0.5rem; font-size: 0.875rem; color: #818181;">
                                    {{ $order->formatted_estimated_cost }}
                                </div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.5rem;">Custo Real (USD)</label>
                                <input type="number" name="actual_cost" step="0.01" min="0" value="{{ old('actual_cost', $order->estimated_cost) }}"
                                       style="width: 100%; padding: 0.625rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem;">
                                <p style="font-size: 0.75rem; color: #818181; margin-top: 0.25rem;">Deixe em branco para usar o custo estimado</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <a href="{{ route('imports.show', $order) }}" 
                       style="padding: 0.75rem 1.5rem; color: #818181; font-weight: 500; border-radius: 0.5rem; text-decoration: none;"
                       onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                        Cancelar
                    </a>
                    <button type="submit" style="padding: 0.75rem 1.5rem; background: #16a34a; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;"
                            onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        Confirmar Recebimento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function receiveForm() {
            return {
                items: {
                    @foreach($order->items as $item)
                        '{{ $item->id }}': {{ $item->quantity }},
                    @endforeach
                },

                markAllReceived() {
                    @foreach($order->items as $item)
                        this.items['{{ $item->id }}'] = {{ $item->quantity }};
                    @endforeach
                }
            };
        }
    </script>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
