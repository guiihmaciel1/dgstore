<x-app-layout>
    <x-slot name="title">Detalhes do Produto</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div class="flex items-center">
                    <a href="{{ route('products.index') }}" class="mr-3 sm:mr-4 p-2 text-dg-500 rounded-lg hover:bg-surface-overlay transition-colors">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-2xl font-bold text-dg-100 truncate">{{ $product->name }}</h1>
                        <p class="text-sm text-dg-500">SKU: {{ $product->sku }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('products.label', $product) }}" target="_blank"
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-surface-raised border border-border-strong text-dg-300 font-medium rounded-lg hover:bg-surface transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Gerar Etiqueta
                    </a>
                    <a href="{{ route('products.edit', $product) }}" 
                       class="inline-flex items-center justify-center px-4 sm:px-6 py-2.5 bg-surface text-white font-medium rounded-lg hover:bg-surface-elevated transition-colors">
                        Editar Produto
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #16a34a;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-4 lg:gap-6">
                <!-- Coluna Principal -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Informações do Produto -->
                    <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a;">
                            <h3 style="font-weight: 600; color: #e3e3e3;">Informações do Produto</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Categoria</dt>
                                    <dd style="margin-top: 0.25rem;">
                                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #222222; color: #a4a4a4; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                            {{ $product->category->label() }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Condição</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3;">{{ $product->condition->label() }}</dd>
                                </div>
                                @if(in_array($product->condition->value, ['used', 'refurbished']))
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Acessórios</dt>
                                    <dd style="margin-top: 0.25rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 500; border-radius: 9999px; {{ $product->has_box ? 'background: rgba(16,185,129,0.1); color: #16a34a;' : 'background: rgba(239,68,68,0.1); color: #fca5a5;' }}">
                                            {{ $product->has_box ? '✓' : '✗' }} Caixa
                                        </span>
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 500; border-radius: 9999px; {{ $product->has_cable ? 'background: rgba(16,185,129,0.1); color: #16a34a;' : 'background: rgba(239,68,68,0.1); color: #fca5a5;' }}">
                                            {{ $product->has_cable ? '✓' : '✗' }} Cabo
                                        </span>
                                        @if($product->battery_health !== null)
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 500; border-radius: 9999px; background: rgba(59,130,246,0.1); color: #2563eb;">
                                            🔋 {{ $product->battery_health }}%
                                        </span>
                                        @endif
                                    </dd>
                                </div>
                                @endif
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Status</dt>
                                    <dd style="margin-top: 0.25rem;">
                                        @if($product->active)
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(16,185,129,0.1); color: #16a34a; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">Ativo</span>
                                        @else
                                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #222222; color: #818181; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">Inativo</span>
                                        @endif
                                    </dd>
                                </div>
                                @if($product->model)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Modelo</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3;">{{ $product->model }}</dd>
                                </div>
                                @endif
                                @if($product->storage)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Armazenamento</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3;">{{ $product->storage }}</dd>
                                </div>
                                @endif
                                @if($product->color)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Cor</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3;">{{ $product->color }}</dd>
                                </div>
                                @endif
                                @if($product->imei)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">IMEI</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3; font-family: monospace;">{{ $product->imei }}</dd>
                                </div>
                                @endif
                                @if($product->imei2)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">IMEI 2</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3; font-family: monospace;">{{ $product->imei2 }}</dd>
                                </div>
                                @endif
                                @if($product->serial_number)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Nº de Série</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3; font-family: monospace;">{{ $product->serial_number }}</dd>
                                </div>
                                @endif
                                @if($product->model_number)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Model No.</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3; font-family: monospace;">{{ $product->model_number }}</dd>
                                </div>
                                @endif
                                @if($product->part_number)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Part Number</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3; font-family: monospace;">{{ $product->part_number }}</dd>
                                </div>
                                @endif
                                @if($product->supplier)
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Fornecedor</dt>
                                    <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3;">{{ $product->supplier }}</dd>
                                </div>
                                @endif
                            </div>
                            @if($product->notes)
                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.06);">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Observações</dt>
                                <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3;">{{ $product->notes }}</dd>
                            </div>
                            @endif

                            @if($product->device_details)
                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.06);">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Detalhes do Dispositivo</dt>
                                    <button type="button" onclick="document.getElementById('device-details-full').style.display = document.getElementById('device-details-full').style.display === 'none' ? 'block' : 'none'"
                                            style="font-size: 0.75rem; color: #2563eb; background: none; border: none; cursor: pointer; text-decoration: underline;">
                                        Ver todos
                                    </button>
                                </div>
                                @php
                                    $details = $product->device_details;
                                    $summaryKeys = [
                                        'SerialNumber' => 'Número de Série',
                                        'InternationalMobileEquipmentIdentity' => 'IMEI',
                                        'InternationalMobileEquipmentIdentity2' => 'IMEI 2',
                                        'ProductType' => 'Tipo do Dispositivo',
                                        'ModelNumber' => 'Nº Modelo',
                                        'ProductVersion' => 'Versão iOS',
                                        'RegionInfo' => 'Região',
                                        'DeviceName' => 'Nome do Dispositivo',
                                        'ActivationState' => 'Ativação',
                                        'BluetoothAddress' => 'Bluetooth MAC',
                                        'WiFiAddress' => 'WiFi MAC',
                                        'PhoneNumber' => 'Telefone',
                                    ];
                                @endphp
                                <dd>
                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.25rem 1.5rem;">
                                        @foreach($summaryKeys as $key => $label)
                                            @if(!empty($details[$key]))
                                                <div style="display: flex; justify-content: space-between; padding: 0.375rem 0; border-bottom: 1px solid rgba(255,255,255,0.04);">
                                                    <span style="font-size: 0.75rem; font-weight: 500; color: #818181;">{{ $label }}</span>
                                                    <span style="font-size: 0.75rem; color: #e3e3e3; font-family: monospace;">{{ $details[$key] }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div id="device-details-full" style="display: none; margin-top: 0.75rem; padding: 0.75rem; background: #1a1a1a; border-radius: 0.5rem; max-height: 20rem; overflow-y: auto;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            @foreach($details as $key => $value)
                                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                                    <td style="padding: 0.25rem 0.5rem 0.25rem 0; font-size: 0.6875rem; font-weight: 500; color: #818181; white-space: nowrap;">{{ $key }}</td>
                                                    <td style="padding: 0.25rem 0; font-size: 0.6875rem; color: #e3e3e3; font-family: monospace; word-break: break-all;">{{ $value }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>

                                    <p style="font-size: 0.6875rem; color: #666666; margin-top: 0.5rem; font-style: italic;">{{ count($details) }} propriedades importadas</p>
                                </dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Movimentações Recentes -->
                    <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: #1a1a1a; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-weight: 600; color: #e3e3e3;">Movimentações Recentes</h3>
                            <a href="{{ route('stock.product-history', $product) }}" style="font-size: 0.875rem; color: #e3e3e3; text-decoration: none;"
                               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                Ver histórico completo →
                            </a>
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                        <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Data</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Tipo</th>
                                        <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Qtd</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Motivo</th>
                                        <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #818181; text-transform: uppercase;">Usuário</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->stockMovements->take(5) as $movement)
                                        @php
                                            $typeColors = [
                                                'in' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'out' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                                'adjustment' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                'return' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                            ];
                                            $tc = $typeColors[$movement->type->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                        @endphp
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 0.75rem 1.5rem; font-size: 0.875rem; color: #818181;">
                                                {{ $movement->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: {{ $tc['bg'] }}; color: {{ $tc['color'] }}; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;">
                                                    {{ $movement->type->label() }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: {{ $movement->isAddition() ? '#16a34a' : '#dc2626' }};">
                                                {{ $movement->isAddition() ? '+' : '-' }}{{ $movement->quantity }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #818181;">
                                                {{ $movement->reason ?? '-' }}
                                            </td>
                                            <td style="padding: 0.75rem 1.5rem; font-size: 0.875rem; color: #818181;">
                                                {{ $movement->user?->name ?? 'Sistema' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="padding: 3rem; text-align: center; color: #818181;">
                                                Nenhuma movimentação registrada.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    {{-- Rastreio / Origem do Produto --}}
                    @php
                        $tradeIn = $product->tradeIn;
                        $soldItems = $product->saleItems->filter(fn($item) => $item->sale && $item->sale->status !== 'cancelled');
                    @endphp
                    @if($tradeIn || $product->cost_price || $product->sale_price || $product->resale_price)
                    <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 2px solid #bfdbfe; overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(59,130,246,0.1); display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <h3 style="font-weight: 600; color: #93c5fd;">Rastreio do Produto</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            @php $canViewFinancials = auth()->user()->canViewFinancials(); @endphp
                            @if($canViewFinancials && ($product->cost_price || $product->sale_price || $product->resale_price))
                            <div style="display: grid; grid-template-columns: repeat({{ ($product->cost_price ? 1 : 0) + ($product->sale_price ? 1 : 0) + ($product->resale_price ? 1 : 0) }}, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
                                @if($product->cost_price)
                                <div style="padding: 0.75rem; background: rgba(16,185,129,0.1); border-radius: 0.5rem; border: 1px solid #bbf7d0;">
                                    <p style="font-size: 0.65rem; font-weight: 500; color: #818181; text-transform: uppercase;">Custo</p>
                                    <p style="font-size: 1.125rem; font-weight: 700; color: #16a34a;">{{ $product->formatted_cost_price }}</p>
                                </div>
                                @endif
                                @if($product->sale_price)
                                <div style="padding: 0.75rem; background: rgba(59,130,246,0.1); border-radius: 0.5rem; border: 1px solid #bfdbfe;">
                                    <p style="font-size: 0.65rem; font-weight: 500; color: #818181; text-transform: uppercase;">Final</p>
                                    <p style="font-size: 1.125rem; font-weight: 700; color: #2563eb;">R$ {{ number_format((float) $product->sale_price, 2, ',', '.') }}</p>
                                </div>
                                @endif
                                @if($product->resale_price)
                                <div style="padding: 0.75rem; background: rgba(245,158,11,0.1); border-radius: 0.5rem; border: 1px solid #fde68a;">
                                    <p style="font-size: 0.65rem; font-weight: 500; color: #818181; text-transform: uppercase;">Repasse</p>
                                    <p style="font-size: 1.125rem; font-weight: 700; color: #ca8a04;">R$ {{ number_format((float) $product->resale_price, 2, ',', '.') }}</p>
                                </div>
                                @endif
                            </div>
                            @elseif(!$canViewFinancials && $product->sale_price)
                            <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                                <div style="padding: 0.75rem; background: rgba(59,130,246,0.1); border-radius: 0.5rem; border: 1px solid #bfdbfe;">
                                    <p style="font-size: 0.65rem; font-weight: 500; color: #818181; text-transform: uppercase;">Preço de Venda</p>
                                    <p style="font-size: 1.125rem; font-weight: 700; color: #2563eb;">R$ {{ number_format((float) $product->sale_price, 2, ',', '.') }}</p>
                                </div>
                            </div>
                            @endif

                            {{-- Origem: Trade-in --}}
                            @if($tradeIn)
                            <div style="margin-bottom: 1rem;">
                                <p style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase; margin-bottom: 0.5rem;">Origem</p>
                                <div style="padding: 0.75rem; background: #1a1a1a; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.06);">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.625rem; padding: 0.125rem 0.5rem; background: rgba(59,130,246,0.15); color: #93c5fd; border-radius: 9999px; font-weight: 600; text-transform: uppercase;">Trade-in</span>
                                        <span style="font-size: 0.75rem; padding: 0.125rem 0.5rem; background: {{ $tradeIn->condition->value === 'excellent' ? '#f0fdf4' : ($tradeIn->condition->value === 'good' ? '#eff6ff' : '#fefce8') }}; color: {{ $tradeIn->condition->value === 'excellent' ? '#16a34a' : ($tradeIn->condition->value === 'good' ? '#2563eb' : '#ca8a04') }}; border-radius: 9999px; font-weight: 500;">
                                            {{ ucfirst($tradeIn->condition->value) }}
                                        </span>
                                    </div>
                                    <p style="font-size: 0.875rem; font-weight: 500; color: #e3e3e3;">{{ $tradeIn->device_name }}</p>
                                    @if($tradeIn->imei)
                                    <p style="font-size: 0.75rem; color: #818181; font-family: monospace;">IMEI: {{ $tradeIn->imei }}</p>
                                    @endif
                                    @if($tradeIn->notes)
                                    <p style="font-size: 0.75rem; color: #818181; margin-top: 0.25rem;">{{ $tradeIn->notes }}</p>
                                    @endif
                                    @if($tradeIn->sale)
                                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.06);">
                                        <p style="font-size: 0.75rem; color: #818181;">Recebido na venda:</p>
                                        <a href="{{ route('sales.show', $tradeIn->sale) }}" style="font-size: 0.875rem; font-weight: 600; color: #93c5fd; text-decoration: none;"
                                           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                            #{{ $tradeIn->sale->sale_number }}
                                        </a>
                                        @if($tradeIn->sale->customer)
                                        <p style="font-size: 0.75rem; color: #818181;">Cliente: {{ $tradeIn->sale->customer->name }}</p>
                                        @endif
                                        <p style="font-size: 0.75rem; color: #818181;">{{ $tradeIn->sale->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Vendas deste produto --}}
                            @if($soldItems->isNotEmpty())
                            <div>
                                <p style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase; margin-bottom: 0.5rem;">Vendido em</p>
                                @foreach($soldItems as $saleItem)
                                <div style="padding: 0.75rem; background: #1a1a1a; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.06); {{ !$loop->last ? 'margin-bottom: 0.5rem;' : '' }}">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <a href="{{ route('sales.show', $saleItem->sale) }}" style="font-size: 0.875rem; font-weight: 600; color: #93c5fd; text-decoration: none;"
                                           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                            #{{ $saleItem->sale->sale_number }}
                                        </a>
                                        <span style="font-size: 0.75rem; color: #818181;">{{ $saleItem->sale->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    @if($saleItem->sale->customer)
                                    <p style="font-size: 0.75rem; color: #818181;">{{ $saleItem->sale->customer->name }}</p>
                                    @endif
                                    <div style="display: flex; gap: 1rem; margin-top: 0.5rem; font-size: 0.75rem;">
                                        <span style="color: #818181;">Venda: <span style="font-weight: 600; color: #e3e3e3;">R$ {{ number_format((float) $saleItem->unit_price, 2, ',', '.') }}</span></span>
                                        @if(auth()->user()->canViewFinancials())
                                        @if($saleItem->cost_price)
                                        <span style="color: #818181;">Custo: <span style="font-weight: 600; color: #e3e3e3;">R$ {{ number_format((float) $saleItem->cost_price, 2, ',', '.') }}</span></span>
                                        @endif
                                        <span style="color: {{ $saleItem->item_profit >= 0 ? '#16a34a' : '#dc2626' }}; font-weight: 600;">
                                            Lucro: R$ {{ number_format($saleItem->item_profit, 2, ',', '.') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Estoque -->
                    <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden; {{ $product->isLowStock() ? ($product->isOutOfStock() ? 'border-color: #fecaca;' : 'border-color: #fde68a;') : '' }}">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); background: {{ $product->isLowStock() ? ($product->isOutOfStock() ? '#fef2f2' : '#fefce8') : '#f9fafb' }};">
                            <h3 style="font-weight: 600; color: #e3e3e3;">Estoque</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="margin-bottom: 1rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Quantidade Atual</dt>
                                <dd style="margin-top: 0.25rem;">
                                    <span style="font-size: 2rem; font-weight: 700; color: {{ $product->isLowStock() ? ($product->isOutOfStock() ? '#dc2626' : '#ca8a04') : '#111827' }};">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    <span style="color: #818181;"> unidades</span>
                                </dd>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #818181; text-transform: uppercase;">Alerta Mínimo</dt>
                                <dd style="margin-top: 0.25rem; font-size: 0.875rem; color: #e3e3e3;">{{ $product->min_stock_alert }} unidades</dd>
                            </div>
                            @if($product->isLowStock())
                            <div style="margin-bottom: 1rem; padding: 0.75rem; background: {{ $product->isOutOfStock() ? '#fef2f2' : '#fefce8' }}; border-radius: 0.5rem; border: 1px solid {{ $product->isOutOfStock() ? '#fecaca' : '#fde68a' }};">
                                <p style="font-size: 0.875rem; font-weight: 500; color: {{ $product->isOutOfStock() ? '#dc2626' : '#ca8a04' }};">
                                    {{ $product->isOutOfStock() ? '⚠️ Produto sem estoque!' : '⚠️ Estoque baixo! Considere reabastecer.' }}
                                </p>
                            </div>
                            @endif
                            <a href="{{ route('stock.create') }}?product_id={{ $product->id }}" 
                               style="display: block; width: 100%; padding: 0.75rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none; text-align: center;"
                               onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                + Registrar Entrada
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
