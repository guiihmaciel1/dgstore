<x-app-layout>
    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho compacto -->
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <a href="{{ route('quotations.index') }}" style="margin-right: 0.75rem; padding: 0.375rem; color: #6b7280; border-radius: 0.375rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Nova Cotação</h1>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <form method="POST" action="{{ route('quotations.store') }}" x-data="quotationForm()">
                    @csrf
                    
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.875rem;">
                            <!-- Fornecedor -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Fornecedor <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="supplier_id" required
                                        style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                    <option value="">Selecione um fornecedor</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ ($selectedSupplierId ?? old('supplier_id')) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Data da Cotação -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Data da Cotação <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="date" name="quoted_at" value="{{ old('quoted_at', date('Y-m-d')) }}" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Produto -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Produto <span style="color: #dc2626;">*</span>
                                </label>
                                <div style="display: flex; gap: 1rem; margin-bottom: 0.5rem;">
                                    <label style="display: flex; align-items: center; cursor: pointer; font-size: 0.875rem; color: #374151;">
                                        <input type="radio" name="product_type" value="free" x-model="productType" style="margin-right: 0.25rem;">
                                        Nome livre
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer; font-size: 0.875rem; color: #374151;">
                                        <input type="radio" name="product_type" value="existing" x-model="productType" style="margin-right: 0.25rem;">
                                        Produto existente
                                    </label>
                                </div>

                                <!-- Nome livre -->
                                <div x-show="productType === 'free'">
                                    <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}"
                                           placeholder="Ex: iPhone 15 Pro Max 256GB"
                                           :required="productType === 'free'"
                                           style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                </div>

                                <!-- Produto existente -->
                                <div x-show="productType === 'existing'" style="position: relative;">
                                    <input type="hidden" name="product_id" x-model="selectedProductId">
                                    <input type="text" x-model="productSearch" @input.debounce.300ms="searchProducts" @focus="showResults = true"
                                           placeholder="Buscar produto cadastrado..."
                                           style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                    
                                    <div x-show="showResults && searchResults.length > 0" @click.away="showResults = false"
                                         style="position: absolute; z-index: 10; width: 100%; margin-top: 0.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.375rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-height: 12rem; overflow: auto;">
                                        <template x-for="product in searchResults" :key="product.id">
                                            <div @click="selectProduct(product)" style="padding: 0.5rem 0.75rem; cursor: pointer; font-size: 0.875rem;"
                                                 onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                                <div style="font-weight: 500; color: #111827;" x-text="product.name"></div>
                                                <div style="font-size: 0.75rem; color: #6b7280;" x-text="'SKU: ' + product.sku"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Preço Unitário -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Preço Unitário <span style="color: #dc2626;">*</span>
                                </label>
                                <div style="position: relative;">
                                    <span style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 0.75rem;">R$</span>
                                    <input type="number" name="unit_price" value="{{ old('unit_price') }}" step="0.01" min="0.01" required
                                           style="width: 100%; padding: 0.5rem 0.625rem 0.5rem 2rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                </div>
                            </div>
                            
                            <!-- Quantidade -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Quantidade</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" step="0.01" min="0.01"
                                           style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                    <select name="unit"
                                            style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                        <option value="un">un</option>
                                        <option value="cx">cx</option>
                                        <option value="kg">kg</option>
                                        <option value="pç">pç</option>
                                        <option value="par">par</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Observações -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Observações</label>
                                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Informações adicionais"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 0.75rem 1.25rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.5rem;">
                        <a href="{{ route('quotations.index') }}" 
                           style="padding: 0.5rem 1rem; background: white; color: #374151; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; text-decoration: none; border: 1px solid #d1d5db;">
                            Cancelar
                        </a>
                        <button type="submit" 
                                style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            Salvar Cotação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function quotationForm() {
            return {
                productType: 'free',
                productSearch: '',
                selectedProductId: null,
                searchResults: [],
                showResults: false,
                async searchProducts() {
                    if (this.productSearch.length < 2) { this.searchResults = []; return; }
                    try {
                        const response = await fetch(`{{ route('quotations.products.search') }}?q=${encodeURIComponent(this.productSearch)}`);
                        this.searchResults = await response.json();
                        this.showResults = true;
                    } catch (error) { console.error('Erro ao buscar produtos:', error); }
                },
                selectProduct(product) {
                    this.selectedProductId = product.id;
                    this.productSearch = product.name;
                    document.getElementById('product_name').value = product.name;
                    this.showResults = false;
                    this.searchResults = [];
                }
            }
        }
    </script>

    <style>
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(2"] { grid-template-columns: 1fr !important; }
            div[style*="grid-column: span 2"] { grid-column: span 1 !important; }
        }
    </style>
</x-app-layout>
