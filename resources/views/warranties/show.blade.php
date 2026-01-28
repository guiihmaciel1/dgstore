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
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('warranties.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Detalhes da Garantia</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">{{ $warranty->product_name }}</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <!-- Informações do Produto -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <h3 style="font-weight: 600; color: #111827;">Informações do Produto</h3>
                    </div>
                    <div style="padding: 1.25rem;">
                        <dl style="display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Produto</dt>
                                <dd style="margin-top: 0.25rem; font-weight: 500; color: #111827;">{{ $warranty->saleItem?->product?->full_name ?? $warranty->product_name }}</dd>
                            </div>
                            @if($warranty->imei)
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">IMEI/Serial</dt>
                                <dd style="margin-top: 0.25rem; font-family: monospace; color: #111827;">{{ $warranty->imei }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Venda</dt>
                                <dd style="margin-top: 0.25rem;">
                                    <a href="{{ route('sales.show', $warranty->saleItem?->sale_id) }}" style="color: #2563eb; text-decoration: none;"
                                       onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        #{{ $warranty->sale_number }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Data da Venda</dt>
                                <dd style="margin-top: 0.25rem; color: #111827;">{{ $warranty->saleItem?->sale?->sold_at?->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Cliente</dt>
                                <dd style="margin-top: 0.25rem; color: #111827;">{{ $warranty->customer_name ?? 'Não informado' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Status das Garantias -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <h3 style="font-weight: 600; color: #111827;">Status das Garantias</h3>
                    </div>
                    <div style="padding: 1.25rem;">
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <!-- Garantia Fornecedor -->
                            <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <div>
                                        <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Garantia do Fornecedor</p>
                                        <p style="font-size: 0.875rem; color: #374151;">{{ $warranty->supplier_warranty_months }} meses</p>
                                    </div>
                                    @if($warranty->supplier_warranty_until)
                                        @php
                                            $isActive = $warranty->is_supplier_warranty_active;
                                            $daysRemaining = $warranty->supplier_days_remaining;
                                        @endphp
                                        <span style="padding: 0.25rem 0.75rem; background: {{ $isActive ? '#f0fdf4' : '#fef2f2' }}; color: {{ $isActive ? '#16a34a' : '#dc2626' }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $isActive ? 'Ativa' : 'Expirada' }}
                                        </span>
                                    @endif
                                </div>
                                @if($warranty->supplier_warranty_until)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                                        <span style="color: #6b7280;">Válida até:</span>
                                        <span style="font-weight: 500; color: #111827;">{{ $warranty->supplier_warranty_until->format('d/m/Y') }}</span>
                                    </div>
                                    @if($warranty->is_supplier_warranty_active)
                                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-top: 0.25rem;">
                                            <span style="color: #6b7280;">Dias restantes:</span>
                                            <span style="font-weight: 500; color: {{ $daysRemaining <= 30 ? '#d97706' : '#16a34a' }};">{{ $daysRemaining }} dias</span>
                                        </div>
                                    @endif
                                @else
                                    <p style="font-size: 0.875rem; color: #9ca3af;">Não definida</p>
                                @endif
                            </div>

                            <!-- Garantia Cliente -->
                            <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <div>
                                        <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Garantia para o Cliente</p>
                                        <p style="font-size: 0.875rem; color: #374151;">{{ $warranty->customer_warranty_months }} meses</p>
                                    </div>
                                    @if($warranty->customer_warranty_until)
                                        @php
                                            $isActive = $warranty->is_customer_warranty_active;
                                            $daysRemaining = $warranty->customer_days_remaining;
                                        @endphp
                                        <span style="padding: 0.25rem 0.75rem; background: {{ $isActive ? '#f0fdf4' : '#fef2f2' }}; color: {{ $isActive ? '#16a34a' : '#dc2626' }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $isActive ? 'Ativa' : 'Expirada' }}
                                        </span>
                                    @endif
                                </div>
                                @if($warranty->customer_warranty_until)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                                        <span style="color: #6b7280;">Válida até:</span>
                                        <span style="font-weight: 500; color: #111827;">{{ $warranty->customer_warranty_until->format('d/m/Y') }}</span>
                                    </div>
                                    @if($warranty->is_customer_warranty_active)
                                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-top: 0.25rem;">
                                            <span style="color: #6b7280;">Dias restantes:</span>
                                            <span style="font-weight: 500; color: {{ $daysRemaining <= 30 ? '#d97706' : '#16a34a' }};">{{ $daysRemaining }} dias</span>
                                        </div>
                                    @endif
                                @else
                                    <p style="font-size: 0.875rem; color: #9ca3af;">Não definida</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Novo Acionamento -->
            @if($warranty->canClaimSupplierWarranty() || $warranty->canClaimCustomerWarranty())
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-top: 1.5rem;">
                <div style="padding: 1rem; background: #fef3c7; border-bottom: 1px solid #fde68a;">
                    <h3 style="font-weight: 600; color: #92400e;">Registrar Acionamento de Garantia</h3>
                </div>
                <div style="padding: 1.25rem;">
                    <form method="POST" action="{{ route('warranties.claims.store', $warranty) }}">
                        @csrf
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; align-items: start;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Tipo de Acionamento</label>
                                <select name="type" required style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                    @if($warranty->canClaimCustomerWarranty())
                                        <option value="customer">Cliente acionou a loja</option>
                                    @endif
                                    @if($warranty->canClaimSupplierWarranty())
                                        <option value="supplier">Loja aciona o fornecedor</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Motivo do Acionamento</label>
                                <textarea name="reason" required rows="2" placeholder="Descreva o problema relatado..."
                                          style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"></textarea>
                            </div>
                        </div>
                        <div style="margin-top: 1rem;">
                            <button type="submit" style="padding: 0.625rem 1.5rem; background: #d97706; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">
                                Registrar Acionamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Histórico de Acionamentos -->
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-top: 1.5rem;">
                <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-weight: 600; color: #111827;">Histórico de Acionamentos</h3>
                </div>
                @if($warranty->claims->isEmpty())
                    <div style="padding: 2rem; text-align: center; color: #6b7280;">
                        Nenhum acionamento registrado.
                    </div>
                @else
                    <div style="divide-y divide-gray-100;">
                        @foreach($warranty->claims->sortByDesc('opened_at') as $claim)
                            <div style="padding: 1.25rem; border-bottom: 1px solid #f3f4f6;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        @php
                                            $statusColors = [
                                                'opened' => ['bg' => '#fefce8', 'color' => '#ca8a04'],
                                                'in_progress' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                                'resolved' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'denied' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                            ];
                                            $sc = $statusColors[$claim->status->value] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                                        @endphp
                                        <span style="padding: 0.25rem 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $claim->status->label() }}
                                        </span>
                                        <span style="padding: 0.25rem 0.75rem; background: {{ $claim->type->value === 'supplier' ? '#eff6ff' : '#f5f3ff' }}; color: {{ $claim->type->value === 'supplier' ? '#2563eb' : '#7c3aed' }}; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                                            {{ $claim->type->shortLabel() }}
                                        </span>
                                    </div>
                                    <span style="font-size: 0.75rem; color: #6b7280;">{{ $claim->opened_at->format('d/m/Y H:i') }}</span>
                                </div>
                                
                                <div style="margin-bottom: 0.75rem;">
                                    <p style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Motivo:</p>
                                    <p style="font-size: 0.875rem; color: #374151;">{{ $claim->reason }}</p>
                                </div>

                                @if($claim->resolution)
                                    <div style="margin-bottom: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 0.375rem;">
                                        <p style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Resolução:</p>
                                        <p style="font-size: 0.875rem; color: #374151;">{{ $claim->resolution }}</p>
                                    </div>
                                @endif

                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #6b7280;">
                                    <span>Registrado por: {{ $claim->user?->name }}</span>
                                    @if($claim->resolved_at)
                                        <span>Resolvido em: {{ $claim->resolved_at->format('d/m/Y') }} ({{ $claim->duration_in_days }} dias)</span>
                                    @endif
                                </div>

                                @if($claim->isOpen())
                                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                        <form method="POST" action="{{ route('warranties.claims.update', $claim) }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
                                            @csrf
                                            @method('PATCH')
                                            <div style="min-width: 150px;">
                                                <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Status</label>
                                                <select name="status" style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.75rem; background: white;">
                                                    <option value="in_progress" {{ $claim->status->value === 'in_progress' ? 'selected' : '' }}>Em Andamento</option>
                                                    <option value="resolved">Resolvido</option>
                                                    <option value="denied">Negado</option>
                                                </select>
                                            </div>
                                            <div style="flex: 1; min-width: 200px;">
                                                <label style="display: block; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Resolução</label>
                                                <input type="text" name="resolution" placeholder="Descreva a resolução..."
                                                       style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.75rem;">
                                            </div>
                                            <button type="submit" style="padding: 0.375rem 0.75rem; background: #111827; color: white; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer;">
                                                Atualizar
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($warranty->notes)
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden; margin-top: 1.5rem;">
                <div style="padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-weight: 600; color: #111827;">Observações</h3>
                </div>
                <div style="padding: 1.25rem;">
                    <p style="font-size: 0.875rem; color: #374151; white-space: pre-line;">{{ $warranty->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: 1fr 2fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
