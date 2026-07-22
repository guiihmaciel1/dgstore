<x-app-layout>
    <x-slot name="title">Editar Item Consignado</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            <div class="flex items-center mb-6">
                <a href="{{ route('stock.consignment.index') }}" class="mr-3 p-2 text-dg-500 rounded-lg hover:bg-surface-overlay transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Editar Item Consignado</h1>
                    <p class="text-sm text-dg-500">{{ $item->full_name }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('stock.consignment.update', $item) }}">
                @csrf
                @method('PUT')

                <div x-data="{ condition: '{{ old('condition', $item->condition?->value ?? 'new') }}' }" style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">

                    {{-- Fornecedor --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #a4a4a4; margin-bottom: 0.375rem;">
                            Fornecedor <span style="color: #fca5a5;">*</span>
                        </label>
                        <select name="supplier_id" required
                                style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                            <option value="">Selecione o fornecedor</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $item->supplier_id) === $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span style="color: #fca5a5; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nome do Produto --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #a4a4a4; margin-bottom: 0.375rem;">
                            Nome do Produto <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}" required placeholder="Ex: iPhone 17 Pro Max"
                               style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                        @error('name') <span style="color: #fca5a5; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Modelo / Storage / Cor --}}
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Modelo</label>
                            <input type="text" name="model" value="{{ old('model', $item->model) }}" placeholder="Ex: A3106"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Storage</label>
                            <input type="text" name="storage" value="{{ old('storage', $item->storage) }}" placeholder="Ex: 256GB"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Cor</label>
                            <input type="text" name="color" value="{{ old('color', $item->color) }}" placeholder="Ex: Silver"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                        </div>
                    </div>

                    {{-- Condição --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #a4a4a4; margin-bottom: 0.375rem;">
                            Condição <span style="color: #fca5a5;">*</span>
                        </label>
                        <div style="display: flex; gap: 0.75rem;">
                            <label :style="condition === 'new' ? 'display:flex;align-items:center;gap:0.5rem;padding:0.625rem 1rem;border:2px solid #111827;border-radius:0.5rem;cursor:pointer;font-size:0.875rem;font-weight:500;' : 'display:flex;align-items:center;gap:0.5rem;padding:0.625rem 1rem;border:2px solid #e5e7eb;border-radius:0.5rem;cursor:pointer;font-size:0.875rem;font-weight:500;'">
                                <input type="radio" name="condition" value="new" x-model="condition" style="accent-color: #e3e3e3;">
                                Novo
                            </label>
                            <label :style="condition === 'used' ? 'display:flex;align-items:center;gap:0.5rem;padding:0.625rem 1rem;border:2px solid #111827;border-radius:0.5rem;cursor:pointer;font-size:0.875rem;font-weight:500;' : 'display:flex;align-items:center;gap:0.5rem;padding:0.625rem 1rem;border:2px solid #e5e7eb;border-radius:0.5rem;cursor:pointer;font-size:0.875rem;font-weight:500;'">
                                <input type="radio" name="condition" value="used" x-model="condition" style="accent-color: #e3e3e3;">
                                Seminovo
                            </label>
                        </div>
                        @error('condition') <span style="color: #fca5a5; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Campos de Seminovo (bateria, caixa, cabo) --}}
                    <div x-show="condition === 'used'" x-cloak
                         style="background: #141414beb; border: 1px solid #fde68a; border-radius: 0.75rem; padding: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                        <div style="font-size: 0.8rem; font-weight: 600; color: #fbbf24; display: flex; align-items: center; gap: 0.375rem;">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Informações do Seminovo
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; align-items: end;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Bateria (%)</label>
                                <input type="number" name="battery_health" value="{{ old('battery_health', $item->battery_health) }}" min="0" max="100" placeholder="Ex: 87"
                                       style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 0;">
                                <input type="checkbox" name="has_box" value="1" {{ old('has_box', $item->has_box) ? 'checked' : '' }}
                                       id="has_box_edit" style="width: 1rem; height: 1rem; accent-color: #e3e3e3; cursor: pointer;">
                                <label for="has_box_edit" style="font-size: 0.875rem; font-weight: 500; color: #a4a4a4; cursor: pointer;">Com caixa</label>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 0;">
                                <input type="checkbox" name="has_cable" value="1" {{ old('has_cable', $item->has_cable) ? 'checked' : '' }}
                                       id="has_cable_edit" style="width: 1rem; height: 1rem; accent-color: #e3e3e3; cursor: pointer;">
                                <label for="has_cable_edit" style="font-size: 0.875rem; font-weight: 500; color: #a4a4a4; cursor: pointer;">Com cabo</label>
                            </div>
                        </div>
                    </div>

                    {{-- Identificadores do Dispositivo --}}
                    <div>
                        <p style="font-size: 0.75rem; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; padding-bottom: 0.375rem; border-bottom: 1px solid rgba(255,255,255,0.04);">Identificadores do Dispositivo</p>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">IMEI</label>
                                <input type="text" name="imei" value="{{ old('imei', $item->imei) }}" placeholder="IMEI principal"
                                       style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none; font-family: monospace;"
                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                                @error('imei') <span style="color: #fca5a5; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">IMEI 2</label>
                                <input type="text" name="imei2" value="{{ old('imei2', $item->imei2) }}" placeholder="Segundo IMEI"
                                       style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none; font-family: monospace;"
                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Nº de Série</label>
                                <input type="text" name="serial_number" value="{{ old('serial_number', $item->serial_number) }}" placeholder="Serial Number"
                                       style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none; font-family: monospace;"
                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Model No.</label>
                                <input type="text" name="model_number" value="{{ old('model_number', $item->model_number) }}" placeholder="Ex: A3257"
                                       style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none; font-family: monospace;"
                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Part Number</label>
                                <input type="text" name="part_number" value="{{ old('part_number', $item->part_number) }}" placeholder="Ex: MFXJ4LL/A"
                                       style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none; font-family: monospace;"
                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                            </div>
                        </div>
                    </div>

                    {{-- Custo / Preço Sugerido / Quantidade --}}
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #a4a4a4; margin-bottom: 0.375rem;">
                                Custo Fornecedor (R$) <span style="color: #fca5a5;">*</span>
                            </label>
                            <input type="number" name="supplier_cost" value="{{ old('supplier_cost', $item->supplier_cost) }}" required step="0.01" min="0" placeholder="0,00"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                            @error('supplier_cost') <span style="color: #fca5a5; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Preço Sugerido (R$)</label>
                            <input type="number" name="suggested_price" value="{{ old('suggested_price', $item->suggested_price) }}" step="0.01" min="0" placeholder="0,00"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #a4a4a4; margin-bottom: 0.375rem;">
                                Quantidade <span style="color: #fca5a5;">*</span>
                            </label>
                            <input type="number" name="quantity" value="{{ old('quantity', $item->quantity) }}" required min="1"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                        </div>
                    </div>

                    {{-- Info de estoque atual --}}
                    <div style="background: #1a1a1a; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; padding: 0.75rem; display: flex; gap: 1.5rem; font-size: 0.8rem; color: #818181;">
                        <span>Disponível: <strong style="color: #e3e3e3;">{{ $item->available_quantity }}</strong></span>
                        <span>Status: <strong style="color: #e3e3e3;">{{ $item->status->label() }}</strong></span>
                        @if($item->received_at)
                            <span>Entrada: <strong style="color: #e3e3e3;">{{ $item->received_at->format('d/m/Y') }}</strong></span>
                        @endif
                    </div>

                    {{-- Data Recebimento --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Data de Recebimento</label>
                        <input type="date" name="received_at" value="{{ old('received_at', $item->received_at?->toDateString()) }}"
                               style="width: 100%; max-width: 220px; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                    </div>

                    {{-- Observações --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #a4a4a4; margin-bottom: 0.375rem;">Observações</label>
                        <textarea name="notes" rows="2" placeholder="Observações (opcional)"
                                  style="width: 100%; padding: 0.625rem; border: 2px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.875rem; outline: none; resize: vertical;"
                                  onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">{{ old('notes', $item->notes) }}</textarea>
                    </div>

                    {{-- Informação do Lote --}}
                    @if($item->batch)
                        <div style="background: #1a1a1a; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; padding: 0.75rem; display: flex; gap: 1.5rem; font-size: 0.8rem; color: #818181;">
                            <span>Lote: <strong style="color: #e3e3e3; font-family: monospace;">{{ $item->batch->batch_code }}</strong></span>
                            <span>Custo Original do Lote: <strong style="color: #e3e3e3;">R$ {{ number_format($item->batch->supplier_cost, 2, ',', '.') }}</strong></span>
                            <span>Recebido: <strong style="color: #e3e3e3;">{{ $item->batch->received_at->format('d/m/Y') }}</strong></span>
                        </div>
                    @endif

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.5rem;">
                        <a href="{{ route('stock.consignment.index') }}"
                           style="padding: 0.625rem 1.5rem; color: #818181; font-size: 0.875rem; text-decoration: none; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem;">
                            Cancelar
                        </a>
                        <button type="submit"
                                style="padding: 0.625rem 1.5rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;"
                                onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>

            {{-- Histórico de Alterações de Preço --}}
            @if($priceHistory->isNotEmpty())
                <div style="margin-top: 2rem;">
                    <h2 style="font-size: 1rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.125rem; height: 1.125rem; color: #818181;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Histórico de Alterações de Preço
                    </h2>

                    <div style="position: relative; padding-left: 1.5rem;">
                        <div style="position: absolute; left: 0.5rem; top: 0; bottom: 0; width: 2px; background: #e5e7eb;"></div>

                        @foreach($priceHistory as $history)
                            <div style="position: relative; margin-bottom: 1.25rem;">
                                <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 0.75rem; height: 0.75rem; border-radius: 50%; background: {{ (float) $history->new_supplier_cost < (float) $history->old_supplier_cost ? '#16a34a' : '#dc2626' }}; border: 2px solid white; box-shadow: 0 0 0 2px #e5e7eb;"></div>

                                <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; padding: 0.875rem; margin-left: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                        <div style="font-size: 0.75rem; color: #818181;">
                                            {{ $history->created_at->format('d/m/Y H:i') }}
                                            @if($history->user)
                                                · por <strong>{{ $history->user->name }}</strong>
                                            @endif
                                        </div>
                                        @if($history->batch)
                                            <span style="font-size: 0.65rem; font-family: monospace; padding: 0.125rem 0.375rem; background: #222222; border-radius: 0.25rem; color: #818181;">
                                                {{ $history->batch->batch_code }}
                                            </span>
                                        @endif
                                    </div>

                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.875rem; font-weight: 600; color: #818181; text-decoration: line-through;">
                                            R$ {{ number_format((float) $history->old_supplier_cost, 2, ',', '.') }}
                                        </span>
                                        <svg style="width: 1rem; height: 1rem; color: #666666;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                        <span style="font-size: 0.875rem; font-weight: 700; color: {{ (float) $history->new_supplier_cost < (float) $history->old_supplier_cost ? '#16a34a' : '#dc2626' }};">
                                            R$ {{ number_format((float) $history->new_supplier_cost, 2, ',', '.') }}
                                        </span>
                                        @php
                                            $priceDiff = (float) $history->new_supplier_cost - (float) $history->old_supplier_cost;
                                        @endphp
                                        <span style="font-size: 0.7rem; font-weight: 600; padding: 0.125rem 0.375rem; border-radius: 0.25rem; {{ $priceDiff < 0 ? 'background: rgba(16,185,129,0.15); color: #16a34a;' : 'background: rgba(239,68,68,0.1); color: #fca5a5;' }}">
                                            {{ $priceDiff > 0 ? '+' : '' }}R$ {{ number_format(abs($priceDiff), 2, ',', '.') }}
                                        </span>
                                    </div>

                                    <div style="font-size: 0.8125rem; color: #a4a4a4; line-height: 1.4;">
                                        <strong>Motivo:</strong> {{ $history->reason }}
                                    </div>
                                    <div style="font-size: 0.7rem; color: #666666; margin-top: 0.25rem;">
                                        {{ $history->affected_items_count }} {{ $history->affected_items_count === 1 ? 'item afetado' : 'itens afetados' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
