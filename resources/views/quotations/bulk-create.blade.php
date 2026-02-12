<x-app-layout>
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('quotations.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Cadastro Rápido de Cotações</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Adicione múltiplas cotações de uma vez</p>
                </div>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <form method="POST" action="{{ route('quotations.bulk-store') }}" x-data="bulkQuotationForm()">
                    @csrf
                    
                    <!-- Cabeçalho do Form: Fornecedor e Data -->
                    <div class="p-4 sm:p-6 border-b border-gray-200 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Fornecedor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fornecedor <span class="text-red-600">*</span>
                                </label>
                                <select name="supplier_id" required
                                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg text-sm bg-white focus:border-gray-900 focus:outline-none">
                                    <option value="">Selecione um fornecedor</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Data da Cotação -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Data da Cotação <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="quoted_at" value="{{ date('Y-m-d') }}" required
                                       class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                                @error('quoted_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Produtos/Cotações -->
                    <div style="padding: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h3 style="font-size: 1rem; font-weight: 600; color: #111827;">Produtos</h3>
                            <button type="button" @click="addRow()"
                                    style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Adicionar Linha
                            </button>
                        </div>

                        <!-- Tabela de Cotações -->
                        <div style="overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 0.75rem;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 8%;">Tipo</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 32%;">Produto *</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 14%;">Preço Unit. *</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 10%;">Qtd</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 8%;">Un</th>
                                        <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 20%;">Obs</th>
                                        <th style="padding: 0.75rem 0.5rem; text-align: center; width: 8%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in rows" :key="index">
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <!-- Tipo: Existente ou Livre -->
                                            <td style="padding: 0.5rem;">
                                                <select x-model="row.type" @change="onTypeChange(index)"
                                                        style="width: 100%; padding: 0.5rem 0.25rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.75rem; background: white;">
                                                    <option value="free">Livre</option>
                                                    <option value="existing">Cadastrado</option>
                                                </select>
                                            </td>
                                            <!-- Produto -->
                                            <td style="padding: 0.5rem;">
                                                <!-- Produto Livre -->
                                                <div x-show="row.type === 'free'">
                                                    <input type="text" 
                                                           :name="`quotations[${index}][product_name]`"
                                                           x-model="row.product_name"
                                                           :required="row.type === 'free'"
                                                           placeholder="Digite o nome do produto"
                                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                                </div>
                                                <!-- Produto Existente -->
                                                <div x-show="row.type === 'existing'">
                                                    <select x-model="row.product_id" @change="onProductSelect(index)"
                                                            style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                                        <option value="">Selecione um produto</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->sale_price }}">
                                                                {{ $product->name }} ({{ $product->sku }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" :name="`quotations[${index}][product_id]`" :value="row.product_id">
                                                    <input type="hidden" :name="`quotations[${index}][product_name]`" :value="row.product_name">
                                                </div>
                                            </td>
                                            <!-- Preço Unitário -->
                                            <td style="padding: 0.5rem;">
                                                <div style="position: relative;">
                                                    <span style="position: absolute; left: 0.5rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem;">R$</span>
                                                    <input type="number" 
                                                           :name="`quotations[${index}][unit_price]`"
                                                           x-model="row.unit_price"
                                                           required
                                                           step="0.01" min="0.01"
                                                           placeholder="0,00"
                                                           style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                                </div>
                                            </td>
                                            <!-- Quantidade -->
                                            <td style="padding: 0.5rem;">
                                                <input type="number" 
                                                       :name="`quotations[${index}][quantity]`"
                                                       x-model="row.quantity"
                                                       step="0.01" min="0.01"
                                                       placeholder="1"
                                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                            </td>
                                            <!-- Unidade -->
                                            <td style="padding: 0.5rem;">
                                                <select :name="`quotations[${index}][unit]`"
                                                        x-model="row.unit"
                                                        style="width: 100%; padding: 0.5rem 0.25rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                                    <option value="un">un</option>
                                                    <option value="cx">cx</option>
                                                    <option value="kg">kg</option>
                                                    <option value="pç">pç</option>
                                                    <option value="par">par</option>
                                                </select>
                                            </td>
                                            <!-- Observações -->
                                            <td style="padding: 0.5rem;">
                                                <input type="text" 
                                                       :name="`quotations[${index}][notes]`"
                                                       x-model="row.notes"
                                                       placeholder="Observação"
                                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                            </td>
                                            <!-- Ações -->
                                            <td style="padding: 0.5rem; text-align: center;">
                                                <button type="button" @click="removeRow(index)" 
                                                        x-show="rows.length > 1"
                                                        style="padding: 0.375rem; color: #dc2626; background: transparent; border: none; cursor: pointer; border-radius: 0.375rem;"
                                                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        @error('quotations')
                            <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>
                        @enderror

                        <!-- Resumo -->
                        <div style="margin-top: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.875rem; color: #6b7280;">
                                Total de itens: <strong style="color: #111827;" x-text="rows.length"></strong>
                            </span>
                            <span style="font-size: 0.875rem; color: #6b7280;">
                                Valor total estimado: 
                                <strong style="color: #16a34a; font-size: 1rem;" x-text="formatCurrency(totalValue())"></strong>
                            </span>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 1rem 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <a href="{{ route('quotations.index') }}" 
                           style="padding: 0.625rem 1.5rem; background: white; color: #374151; font-weight: 500; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb;">
                            Cancelar
                        </a>
                        <button type="submit" 
                                style="padding: 0.625rem 1.5rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            Salvar Todas as Cotações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Dica -->
            <div style="margin-top: 1rem; padding: 1rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #2563eb; flex-shrink: 0; margin-top: 0.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div style="font-size: 0.875rem; color: #1e40af;">
                        <strong>Dica:</strong> Use "Cadastrado" para produtos já existentes no sistema (vincula a cotação ao produto).
                        Use "Livre" para cotar produtos novos ou que ainda não foram cadastrados.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bulkQuotationForm() {
            const products = @json($productsJson);
            
            return {
                rows: [
                    { type: 'free', product_id: '', product_name: '', unit_price: '', quantity: 1, unit: 'un', notes: '' }
                ],

                addRow() {
                    this.rows.push({ type: 'free', product_id: '', product_name: '', unit_price: '', quantity: 1, unit: 'un', notes: '' });
                },

                removeRow(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index, 1);
                    }
                },

                onTypeChange(index) {
                    // Limpa os campos ao mudar o tipo
                    this.rows[index].product_id = '';
                    this.rows[index].product_name = '';
                    this.rows[index].unit_price = '';
                },

                onProductSelect(index) {
                    const productId = this.rows[index].product_id;
                    if (productId) {
                        const product = products.find(p => p.id === productId);
                        if (product) {
                            this.rows[index].product_name = product.name;
                            // Sugere o preço de venda como referência
                            if (!this.rows[index].unit_price) {
                                this.rows[index].unit_price = product.price;
                            }
                        }
                    }
                },

                totalValue() {
                    return this.rows.reduce((sum, row) => {
                        const price = parseFloat(row.unit_price) || 0;
                        const qty = parseFloat(row.quantity) || 1;
                        return sum + (price * qty);
                    }, 0);
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }).format(value);
                }
            }
        }
    </script>

</x-app-layout>
