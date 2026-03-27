<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center mb-6">
                <a href="{{ route('stock.consignment.index') }}" class="mr-3 p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Nova Entrada Consignada</h1>
                    <p class="text-sm text-gray-500">Registre um produto recebido de fornecedor em consignação</p>
                </div>
            </div>

            <form method="POST" action="{{ route('stock.consignment.store') }}">
                @csrf

                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">

                    {{-- Fornecedor --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Fornecedor <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="supplier_id" required
                                style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            <option value="">Selecione o fornecedor</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') === $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nome do Produto --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Nome do Produto <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: iPhone 17 Pro Max"
                               style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        @error('name') <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Modelo / Storage / Cor --}}
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Modelo</label>
                            <input type="text" name="model" value="{{ old('model') }}" placeholder="Ex: A3106"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Storage</label>
                            <input type="text" name="storage" value="{{ old('storage') }}" placeholder="Ex: 256GB"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Cor</label>
                            <input type="text" name="color" value="{{ old('color') }}" placeholder="Ex: Silver"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                    </div>

                    {{-- IMEI --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">IMEI</label>
                        <input type="text" name="imei" value="{{ old('imei') }}" placeholder="Número IMEI (opcional)"
                               style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none; font-family: monospace;"
                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        @error('imei') <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Custo / Preço Sugerido / Quantidade --}}
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Custo Fornecedor (R$) <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="number" name="supplier_cost" value="{{ old('supplier_cost') }}" required step="0.01" min="0" placeholder="0,00"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            @error('supplier_cost') <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Preço Sugerido (R$)</label>
                            <input type="number" name="suggested_price" value="{{ old('suggested_price') }}" step="0.01" min="0" placeholder="0,00"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Quantidade <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" required min="1"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                    </div>

                    {{-- Data Recebimento --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Data de Recebimento</label>
                        <input type="date" name="received_at" value="{{ old('received_at', now()->toDateString()) }}"
                               style="width: 100%; max-width: 220px; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>

                    {{-- Observações --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Observações</label>
                        <textarea name="notes" rows="2" placeholder="Observações (opcional)"
                                  style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none; resize: vertical;"
                                  onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">{{ old('notes') }}</textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.5rem;">
                        <a href="{{ route('stock.consignment.index') }}"
                           style="padding: 0.625rem 1.5rem; color: #6b7280; font-size: 0.875rem; text-decoration: none; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                            Cancelar
                        </a>
                        <button type="submit"
                                style="padding: 0.625rem 1.5rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;"
                                onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                            Registrar Entrada
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
