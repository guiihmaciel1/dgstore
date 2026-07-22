<x-app-layout>
    <x-slot name="title">Historico do Item</x-slot>

    <div class="py-6">
        <div class="px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center mb-6">
                <a href="{{ route('stock.consignment.index') }}" class="mr-3 p-2 text-dg-500 rounded-lg hover:bg-surface-overlay transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Historico do Item</h1>
                    <p class="text-sm text-dg-500">{{ $item->full_name }} | Lote {{ $item->batch?->batch_code }}</p>
                </div>
            </div>

            {{-- Item atual (estado de hoje) --}}
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; padding: 1.25rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; background: rgba(59,130,246,0.15); color: #93c5fd; border-radius: 9999px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Estado Atual</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; font-size: 0.875rem;">
                    <div>
                        <div style="font-size: 0.6875rem; font-weight: 600; color: #818181; text-transform: uppercase;">Produto</div>
                        <div style="color: #e3e3e3; font-weight: 600;">{{ $item->name }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.6875rem; font-weight: 600; color: #818181; text-transform: uppercase;">Storage / Cor</div>
                        <div style="color: #e3e3e3;">{{ $item->storage ?: '-' }} {{ $item->color ? '/ ' . $item->color : '' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.6875rem; font-weight: 600; color: #818181; text-transform: uppercase;">IMEI</div>
                        <div style="color: #e3e3e3; font-family: monospace; font-size: 0.8125rem;">{{ $item->imei ?: '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.6875rem; font-weight: 600; color: #818181; text-transform: uppercase;">Serial</div>
                        <div style="color: #e3e3e3; font-family: monospace; font-size: 0.8125rem;">{{ $item->serial_number ?: '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Trocas (se houver) --}}
            @if($item->exchanges->isNotEmpty())
                <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; padding: 1.25rem; margin-bottom: 1rem;">
                    <h2 style="font-size: 1rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🔁</span> Historico de Trocas ({{ $item->exchanges->count() }})
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($item->exchanges as $exchange)
                            <div style="border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem; background: #1a1a1a;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                    <div>
                                        <div style="font-size: 0.875rem; font-weight: 700; color: #e3e3e3;">
                                            Troca com {{ $exchange->partner_name }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: #818181;">
                                            {{ $exchange->exchanged_at->format('d/m/Y H:i') }}
                                            @if($exchange->user)
                                                | por {{ $exchange->user->name }}
                                            @endif
                                        </div>
                                    </div>
                                    @if((float) $exchange->cost_adjustment !== 0.0)
                                        @php
                                            $isIncome = (float) $exchange->cost_adjustment > 0;
                                        @endphp
                                        <span style="font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.5rem; border-radius: 0.375rem; background: {{ $isIncome ? '#ecfdf5' : '#fef2f2' }}; color: {{ $isIncome ? '#065f46' : '#991b1b' }};">
                                            {{ $exchange->formatted_cost_adjustment }}
                                        </span>
                                    @endif
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    {{-- Antes --}}
                                    <div style="background: #141414; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #fee2e2;">
                                        <div style="font-size: 0.6875rem; font-weight: 700; color: #fca5a5; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Antes</div>
                                        <div style="font-size: 0.8125rem; color: #e3e3e3; font-weight: 600;">{{ $exchange->old_full_name }}</div>
                                        @if($exchange->old_imei)
                                            <div style="font-size: 0.75rem; color: #818181; font-family: monospace; margin-top: 0.25rem;">IMEI: {{ $exchange->old_imei }}</div>
                                        @endif
                                        @if($exchange->old_serial_number)
                                            <div style="font-size: 0.75rem; color: #818181; font-family: monospace;">Serial: {{ $exchange->old_serial_number }}</div>
                                        @endif
                                    </div>

                                    {{-- Depois --}}
                                    <div style="background: #141414; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #a7f3d0;">
                                        <div style="font-size: 0.6875rem; font-weight: 700; color: #6ee7b7; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Depois</div>
                                        <div style="font-size: 0.8125rem; color: #e3e3e3; font-weight: 600;">{{ $exchange->new_full_name }}</div>
                                        @if($exchange->new_imei)
                                            <div style="font-size: 0.75rem; color: #818181; font-family: monospace; margin-top: 0.25rem;">IMEI: {{ $exchange->new_imei }}</div>
                                        @endif
                                        @if($exchange->new_serial_number)
                                            <div style="font-size: 0.75rem; color: #818181; font-family: monospace;">Serial: {{ $exchange->new_serial_number }}</div>
                                        @endif
                                    </div>
                                </div>

                                @if($exchange->reason)
                                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed #e5e7eb; font-size: 0.8125rem; color: #818181;">
                                        <strong>Motivo:</strong> {{ $exchange->reason }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Movimentacoes --}}
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; padding: 1.25rem;">
                <h2 style="font-size: 1rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📊</span> Movimentacoes ({{ $item->movements->count() }})
                </h2>

                @if($item->movements->isEmpty())
                    <p style="font-size: 0.875rem; color: #818181; text-align: center; padding: 1rem;">Nenhuma movimentacao registrada.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($item->movements as $movement)
                            @php
                                $typeColors = [
                                    'in' => ['#dcfce7', '#166534'],
                                    'out' => ['#dbeafe', '#1d4ed8'],
                                    'return' => ['#fef3c7', '#92400e'],
                                    'exchange' => ['#fce7f3', '#9d174d'],
                                ];
                                $colors = $typeColors[$movement->type->value] ?? ['#f3f4f6', '#374151'];
                            @endphp
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.875rem; border-radius: 0.5rem; border: 1px solid #f3f4f6; background: #1a1a1a;">
                                <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; background: {{ $colors[0] }}; color: {{ $colors[1] }}; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">{{ $movement->type->label() }}</span>
                                <div style="flex: 1; font-size: 0.8125rem; color: #a4a4a4;">{{ $movement->reason ?: '-' }}</div>
                                <div style="font-size: 0.75rem; color: #818181; white-space: nowrap;">
                                    {{ $movement->created_at?->format('d/m/Y H:i') }}
                                    @if($movement->user)
                                        <span style="display: block;">por {{ $movement->user->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
