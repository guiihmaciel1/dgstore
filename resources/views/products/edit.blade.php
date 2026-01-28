<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('products.show', $product) }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Editar Produto</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">{{ $product->name }}</p>
                </div>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf
                    @method('PUT')
                    
                    <div style="padding: 1.5rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                            <!-- Nome -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Nome do Produto <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                @error('name')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- SKU -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    SKU <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                @error('sku')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Categoria -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Categoria <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="category" required
                                        style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                                        onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    @foreach(\App\Domain\Product\Enums\ProductCategory::grouped() as $group => $items)
                                        <optgroup label="{{ $group }}">
                                            @foreach($items as $category)
                                                <option value="{{ $category->value }}" {{ old('category', $product->category->value) == $category->value ? 'selected' : '' }}>
                                                    {{ $category->label() }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('category')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Condição -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Condição <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="condition" required
                                        style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                                        onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    @foreach($conditions as $condition)
                                        <option value="{{ $condition->value }}" {{ old('condition', $product->condition->value) == $condition->value ? 'selected' : '' }}>
                                            {{ $condition->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('condition')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Modelo -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Modelo</label>
                                <input type="text" name="model" value="{{ old('model', $product->model) }}"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- Armazenamento -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Armazenamento</label>
                                <input type="text" name="storage" value="{{ old('storage', $product->storage) }}"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- Cor -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Cor</label>
                                <input type="text" name="color" value="{{ old('color', $product->color) }}"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- IMEI/Serial -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">IMEI/Serial</label>
                                <input type="text" name="imei" value="{{ old('imei', $product->imei) }}"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- Preço de Custo -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Preço de Custo <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0" required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- Preço de Venda -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Preço de Venda <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0" required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- Quantidade em Estoque -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Quantidade em Estoque <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- Alerta de Estoque Mínimo -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Alerta de Estoque Mínimo <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" min="0" required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- Fornecedor -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Fornecedor</label>
                                <input type="text" name="supplier" value="{{ old('supplier', $product->supplier) }}"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            <!-- Observações -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Observações</label>
                                <textarea name="notes" rows="3"
                                          style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                                          onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">{{ old('notes', $product->notes) }}</textarea>
                            </div>

                            <!-- Ativo -->
                            <div style="grid-column: span 2;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                                           style="width: 1.25rem; height: 1.25rem; border: 2px solid #e5e7eb; border-radius: 0.25rem; margin-right: 0.5rem;">
                                    <span style="font-size: 0.875rem; color: #374151;">Produto ativo</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 1rem 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" onclick="if(confirm('Tem certeza que deseja excluir este produto?')) document.getElementById('delete-form').submit();"
                                style="padding: 0.625rem 1rem; background: #dc2626; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                            Excluir
                        </button>
                        <div style="display: flex; gap: 0.75rem;">
                            <a href="{{ route('products.show', $product) }}" 
                               style="padding: 0.625rem 1.5rem; background: white; color: #374151; font-weight: 500; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb;">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="padding: 0.625rem 1.5rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Form de exclusão oculto -->
            <form id="delete-form" method="POST" action="{{ route('products.destroy', $product) }}" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <style>
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(2"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-column: span 2"] {
                grid-column: span 1 !important;
            }
        }
    </style>
</x-app-layout>
