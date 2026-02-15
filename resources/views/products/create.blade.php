<x-app-layout>
    <div class="py-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho compacto -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('products.index') }}" style="margin-right: 0.75rem; padding: 0.375rem; color: #6b7280; border-radius: 0.375rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Novo Produto</h1>
                </div>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf
                    
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.875rem;">
                            <!-- Nome -->
                            <div style="grid-column: span 3;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Nome do Produto <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827';this.style.boxShadow='0 0 0 1px #111827'" onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            </div>

                            <!-- SKU -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    SKU <span style="color: #dc2626;">*</span>
                                </label>
                                <div style="display: flex; gap: 0.25rem;">
                                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                           style="flex: 1; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;"
                                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#d1d5db'">
                                    <button type="button" onclick="generateSku()"
                                            style="padding: 0.5rem; background: #f3f4f6; color: #374151; border-radius: 0.375rem; border: 1px solid #d1d5db; cursor: pointer; font-size: 0.75rem;"
                                            onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                        Gerar
                                    </button>
                                </div>
                            </div>

                            <!-- Categoria -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Categoria <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="category" id="category" required
                                        style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                    <option value="">Selecione...</option>
                                    @foreach(\App\Domain\Product\Enums\ProductCategory::grouped() as $group => $items)
                                        <optgroup label="{{ $group }}">
                                            @foreach($items as $category)
                                                <option value="{{ $category->value }}" {{ old('category') == $category->value ? 'selected' : '' }}>
                                                    {{ $category->label() }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Condição -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Condição <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="condition" required
                                        style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                    @foreach($conditions as $condition)
                                        <option value="{{ $condition->value }}" {{ old('condition') == $condition->value ? 'selected' : '' }}>
                                            {{ $condition->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Modelo -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Modelo</label>
                                <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="Ex: 15 Pro Max"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Armazenamento -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Armazenamento</label>
                                <input type="text" name="storage" value="{{ old('storage') }}" placeholder="Ex: 256GB"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Cor -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Cor</label>
                                <input type="text" name="color" value="{{ old('color') }}" placeholder="Ex: Preto"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- IMEI/Serial -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">IMEI/Serial</label>
                                <input type="text" name="imei" value="{{ old('imei') }}" placeholder="Para eletrônicos"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Qtd Estoque -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Estoque <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Alerta Mínimo -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Alerta Mín. <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', 1) }}" min="0" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Fornecedor -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Fornecedor</label>
                                <input type="text" name="supplier" value="{{ old('supplier') }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Observações -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Observações</label>
                                <input type="text" name="notes" value="{{ old('notes') }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 0.75rem 1.25rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="active" value="1" checked
                                   style="width: 1rem; height: 1rem; border-radius: 0.25rem; margin-right: 0.375rem;">
                            <span style="font-size: 0.875rem; color: #374151;">Produto ativo</span>
                        </label>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('products.index') }}" 
                               style="padding: 0.5rem 1rem; background: white; color: #374151; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; text-decoration: none; border: 1px solid #d1d5db;">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                Cadastrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function generateSku() {
            const category = document.getElementById('category').value || 'smartphone';
            const model = document.getElementById('model').value || '';
            fetch(`{{ route('products.generate-sku') }}?category=${category}&model=${encodeURIComponent(model)}`)
                .then(response => response.json())
                .then(data => { document.getElementById('sku').value = data.sku; });
        }
    </script>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: repeat(2, 1fr) !important; }
            div[style*="grid-column: span 3"] { grid-column: span 2 !important; }
        }
    </style>
</x-app-layout>
